<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Apl01Pendaftaran;
use App\Models\Apl02;
use App\Models\Apl02ElementAssessment;
use App\Models\Apl02EvidenceSubmission;
use App\Models\PortfolioFile;

class Apl02Controller extends Controller
{
    public function index()
    {
        $apl02s = Apl02::byUser(auth()->id())
            ->with(['certificationScheme:id,nama,code_1,jenjang', 'apl01:id,nomor_apl_01,nama_lengkap'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('asesi.apl02.index', compact('apl02s'));
    }

    public function create()
    {
        $availableApl01s = Apl01Pendaftaran::where('user_id', auth()->id())
            ->where('status', 'approved')
            ->whereDoesntHave('apl02')
            ->with('certificationScheme:id,nama,code_1,jenjang')
            ->get();

        if ($availableApl01s->isEmpty()) {
            return redirect()->route('asesi.apl02.index')->with('error', 'Tidak ada APL 01 yang disetujui untuk membuat APL 02 baru.');
        }

        return view('asesi.apl02.create', compact('availableApl01s'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'apl_01_id' => 'required|exists:apl_01_pendaftarans,id',
        ]);

        $apl01 = Apl01Pendaftaran::where('id', $request->apl_01_id)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->firstOrFail();

        if ($apl01->apl02()->exists()) {
            return redirect()->route('asesi.apl02.index')->with('error', 'APL 02 untuk APL 01 ini sudah ada.');
        }

        DB::beginTransaction();
        try {
            $apl02 = Apl02::create([
                'user_id' => auth()->id(),
                'apl_01_id' => $apl01->id,
                'certification_scheme_id' => $apl01->certification_scheme_id,
                'nomor_apl_02' => $apl01->nomor_apl_01,
                'status' => 'draft',
            ]);

            $this->initializeElementAssessments($apl02);

            DB::commit();

            return redirect()->route('asesi.apl02.edit', $apl02)->with('success', 'APL 02 berhasil dibuat. Silakan lakukan self assessment.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat APL 02: ' . $e->getMessage());
        }
    }

    public function show(Apl02 $apl02)
    {
        $apl02->loadCompleteData();

        // Get all evidence submissions grouped by document name for easy lookup
        $evidenceByDocumentName = $apl02
            ->evidenceSubmissions()
            ->with('portfolioFile')
            ->get()
            ->groupBy(function ($evidence) {
                return $evidence->portfolioFile ? $evidence->portfolioFile->document_name : null;
            })
            ->filter(function ($group, $documentName) {
                return $documentName !== null; // Remove entries with null document names
            })
            ->map(function ($evidenceGroup) {
                // Take the first evidence file for this document name
                return $evidenceGroup->first();
            });

        return view('asesi.apl02.show', compact('apl02', 'evidenceByDocumentName'));
    }
    public function edit(Apl02 $apl02)
    {
        $apl02->load(['user', 'apl01']);

        if (!in_array($apl02->status, ['draft', 'returned', 'open'])) {
            return redirect()->route('asesi.apl02.show', $apl02)->with('error', 'APL 02 tidak dapat diedit karena sudah disubmit.');
        }

        $assessmentData = $this->getAssessmentDataWithPortfolio($apl02);
        $existingEvidence = $this->getExistingEvidence($apl02);

        return view('asesi.apl02.edit', compact('apl02', 'assessmentData', 'existingEvidence'));
    }

    public function update(Request $request, Apl02 $apl02)
    {
        Log::info('APL02 update started', [
            'apl02_id' => $apl02->id,
            'user_id' => auth()->id(),
        ]);

        $apl02->load(['user', 'apl01']);

        if (!in_array($apl02->status, ['draft', 'returned', 'open'])) {
            return redirect()->back()->with('error', 'APL 02 tidak dapat diupdate');
        }

        try {
            $validator = Validator::make($request->all(), [
                'assessments' => 'required|string',
                'signature' => 'nullable|string',
                'evidence_files' => 'nullable|array',
                'evidence_files.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', ['errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Parse assessments BEFORE starting transaction
            $assessments = json_decode($request->assessments, true);
            if (empty($assessments) || json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode failed', [
                    'json_error' => json_last_error_msg(),
                    'assessments_raw' => $request->assessments,
                ]);

                return redirect()
                    ->back()
                    ->with('error', 'Assessment data tidak valid atau format JSON salah: ' . json_last_error_msg())
                    ->withInput();
            }

            // Validate signature BEFORE starting transaction
            $newSignaturePath = null;
            if ($request->has('signature') && !empty($request->signature)) {
                if (!$this->isValidSignature($request->signature)) {
                    return redirect()->back()->with('error', 'Format tanda tangan tidak valid')->withInput();
                }

                // Save signature file BEFORE database transaction
                try {
                    $newSignaturePath = $this->saveSignature($request->signature, $apl02);
                    Log::info('Signature file saved successfully', ['path' => $newSignaturePath]);
                } catch (\Exception $e) {
                    Log::error('Signature save error: ' . $e->getMessage());
                    return redirect()
                        ->back()
                        ->with('error', 'Gagal menyimpan tanda tangan: ' . $e->getMessage())
                        ->withInput();
                }
            } elseif (empty($apl02->tanda_tangan_asesi)) {
                return redirect()->back()->with('error', 'Tanda tangan digital diperlukan untuk submit assessment')->withInput();
            }

            // NOW start the database transaction
            DB::beginTransaction();

            try {
                // UPDATED: Save assessments with element documents in notes field
                foreach ($assessments as $assessment) {
                    if (!isset($assessment['elemen_id']) || !isset($assessment['result'])) {
                        continue;
                    }

                    // Validate notes JSON if present
                    $notesData = null;
                    if (isset($assessment['notes']) && !empty($assessment['notes'])) {
                        $notesData = $assessment['notes'];

                        // Validate if it's valid JSON
                        if (is_string($notesData)) {
                            $decoded = json_decode($notesData, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                Log::warning('Invalid notes JSON for element', [
                                    'element_id' => $assessment['elemen_id'],
                                    'notes' => $notesData,
                                    'json_error' => json_last_error_msg(),
                                ]);
                                $notesData = null;
                            }
                        }
                    }

                    Apl02ElementAssessment::updateOrCreate(
                        [
                            'apl_02_id' => $apl02->id,
                            'elemen_kompetensi_id' => $assessment['elemen_id'],
                        ],
                        [
                            'unit_kompetensi_id' => $assessment['unit_id'] ?? null,
                            'assessment_result' => $assessment['result'],
                            'notes' => $notesData, // Store JSON of selected documents
                        ],
                    );

                    Log::info('Assessment saved with documents', [
                        'element_id' => $assessment['elemen_id'],
                        'result' => $assessment['result'],
                        'notes_length' => $notesData ? strlen($notesData) : 0,
                    ]);
                }

                // Handle batch file uploads with PROPER PATH STRUCTURE
                if ($request->has('evidence_files') && is_array($request->evidence_files)) {
                    $this->handleBatchEvidenceFiles($request->evidence_files, $apl02);
                }

                // Update signature if new one was uploaded
                if ($newSignaturePath) {
                    $apl02->update([
                        'tanda_tangan_asesi' => $newSignaturePath,
                        'tanggal_tanda_tangan_asesi' => now(),
                        'ip_tanda_tangan_asesi' => request()->ip(),
                    ]);

                    Log::info('Signature path updated in database', [
                        'path' => $newSignaturePath,
                    ]);
                }

                // Calculate competency stats
                $apl02->calculateCompetencyStats();

                // Submit the APL02
                $apl02->submit();

                DB::commit();

                // ✅ PASTI MUNCUL NOTIFNYA!
                return redirect()->route('asesi.inbox.index', $apl02->id)->with('success', 'Assessment berhasil disimpan dan disubmit!');
            } catch (\Exception $e) {
                DB::rollBack();

                // Clean up signature file if database transaction failed
                if ($newSignaturePath) {
                    try {
                        Storage::disk('public')->delete($newSignaturePath);
                        Log::info('Cleaned up signature file due to database error: ' . $newSignaturePath);
                    } catch (\Exception $cleanupError) {
                        Log::warning('Failed to clean up signature file: ' . $cleanupError->getMessage());
                    }
                }

                throw $e;
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('APL02 Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['signature']),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /* ===================== FILE UPLOAD METHODS ===================== */

    /**
     * Handle batch evidence file uploads with PROPER PATH STRUCTURE
     */
    private function handleBatchEvidenceFiles(array $evidenceFiles, Apl02 $apl02)
    {
        $apl02->ensureFolderPath();
        $folderPath = $apl02->file_folder_path;

        Log::info('Using folder path for APL02', [
            'apl02_id' => $apl02->id,
            'folder_path' => $folderPath,
        ]);

        foreach ($evidenceFiles as $portfolioId => $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            try {
                // Delete existing evidence for this portfolio
                $existingEvidence = Apl02EvidenceSubmission::where('apl_02_id', $apl02->id)->where('portfolio_file_id', $portfolioId)->first();

                if ($existingEvidence) {
                    $existingEvidence->deleteFile();
                    Log::info('Deleted existing evidence file', [
                        'portfolio_id' => $portfolioId,
                        'old_file' => $existingEvidence->file_name,
                    ]);
                }

                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . $portfolioId . '_' . Str::random(10) . '.' . $extension;

                // Store file in the APL02's specific folder
                $filePath = $file->storeAs($folderPath, $fileName, 'public');

                // Save to database
                Apl02EvidenceSubmission::create([
                    'apl_02_id' => $apl02->id,
                    'portfolio_file_id' => $portfolioId,
                    'file_name' => $originalName,
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'file_type' => $extension,
                    'mime_type' => $file->getMimeType(),
                    'is_submitted' => true,
                ]);

                Log::info('Evidence file uploaded successfully', [
                    'apl_02_id' => $apl02->id,
                    'portfolio_id' => $portfolioId,
                    'file_name' => $originalName,
                    'file_path' => $filePath,
                    'folder_structure' => $folderPath,
                ]);
            } catch (\Exception $e) {
                Log::error('Evidence file upload error: ' . $e->getMessage(), [
                    'portfolio_id' => $portfolioId,
                    'file_name' => $originalName ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }
    }

    /**
     * Validate signature data
     */
    private function isValidSignature($signature)
    {
        if (!is_string($signature)) {
            Log::warning('Signature is not a string');
            return false;
        }

        if (!str_starts_with($signature, 'data:image/')) {
            Log::warning('Signature does not start with data:image/');
            return false;
        }

        if (!str_contains($signature, 'base64,')) {
            Log::warning('Signature does not contain base64,');
            return false;
        }

        if (strlen($signature) < 100) {
            Log::warning('Signature data too short: ' . strlen($signature));
            return false;
        }

        return true;
    }

    /**
     * Save signature to storage using APL02 folder structure
     */
    private function saveSignature($signatureData, Apl02 $apl02)
    {
        try {
            // Extract base64 data
            $base64Pos = strpos($signatureData, 'base64,');
            if ($base64Pos === false) {
                throw new \Exception('Invalid signature format');
            }

            $data = substr($signatureData, $base64Pos + 7);
            $data = base64_decode($data);

            if ($data === false || strlen($data) < 100) {
                throw new \Exception('Invalid signature data');
            }

            // Use APL02's folder structure
            $apl02->ensureFolderPath();
            $folderPath = $apl02->file_folder_path . '/signatures';

            // Ensure signatures subfolder exists
            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }

            // Delete old signature if exists
            if (!empty($apl02->tanda_tangan_asesi)) {
                try {
                    $oldPath = $apl02->tanda_tangan_asesi;
                    if (!str_starts_with($oldPath, 'http')) {
                        Storage::disk('public')->delete($oldPath);
                        Log::info('Old signature deleted: ' . $oldPath);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to delete old signature: ' . $e->getMessage());
                }
            }

            // Generate filename
            $fileName = 'signature_apl02_' . $apl02->id . '_' . time() . '.png';
            $filePath = $folderPath . '/' . $fileName;

            // Save file
            $saved = Storage::disk('public')->put($filePath, $data);

            if (!$saved) {
                throw new \Exception('Failed to write signature file to storage');
            }

            Log::info('Signature saved successfully:', [
                'file_path' => $filePath,
                'file_size' => strlen($data),
            ]);

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Signature save error: ' . $e->getMessage());
            throw $e;
        }
    }

    /* ===================== HELPER METHODS ===================== */

    public function uploadEvidence(Request $request, Apl02 $apl02)
    {
        if (!in_array($apl02->status, ['draft', 'returned', 'open'])) {
            return response()->json(['success' => false, 'error' => 'APL 02 tidak dapat diupdate'], 403);
        }

        $request->validate([
            'portfolio_file_id' => 'required|exists:portfolio_files,id',
            'evidence_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing evidence if any
            $existingEvidence = Apl02EvidenceSubmission::where('apl_02_id', $apl02->id)->where('portfolio_file_id', $request->portfolio_file_id)->first();

            if ($existingEvidence) {
                $existingEvidence->deleteFile();
            }

            // Upload new file using proper folder structure
            $apl02->ensureFolderPath();
            $folderPath = $apl02->file_folder_path;

            $file = $request->file('evidence_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs($folderPath, $fileName, 'public');

            $evidence = Apl02EvidenceSubmission::create([
                'apl_02_id' => $apl02->id,
                'portfolio_file_id' => $request->portfolio_file_id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'is_submitted' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bukti berhasil diupload',
                'evidence' => [
                    'id' => $evidence->id,
                    'file_name' => $evidence->file_name,
                    'file_size_formatted' => $evidence->file_size_formatted,
                    'file_type' => $evidence->file_type,
                    'download_url' => route('asesi.apl02.download-evidence', [$apl02->id, $evidence->id]),
                    'preview_url' => route('asesi.apl02.preview-evidence', [$apl02->id, $evidence->id]),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => 'Gagal upload: ' . $e->getMessage()], 500);
        }
    }

    private function initializeElementAssessments(Apl02 $apl02)
    {
        $elements = $apl02->certificationScheme
            ->elemenKompetensis()
            ->whereHas('unitKompetensi', function ($q) {
                $q->where('unit_kompetensis.is_active', true);
            })
            ->where('elemen_kompetensis.is_active', true)
            ->with('unitKompetensi:id')
            ->get();

        foreach ($elements as $element) {
            Apl02ElementAssessment::create([
                'apl_02_id' => $apl02->id,
                'unit_kompetensi_id' => $element->unit_kompetensi_id,
                'elemen_kompetensi_id' => $element->id,
                'assessment_result' => null,
                'notes' => null, // Initialize empty notes field
            ]);
        }
    }

    private function getAssessmentDataWithPortfolio(Apl02 $apl02)
    {
        return $apl02->certificationScheme
            ->activeUnits()
            ->with([
                'activeElemenKompetensis.activeKriteriaKerjas',
                'activeElemenKompetensis.assessments' => function ($q) use ($apl02) {
                    $q->where('apl_02_id', $apl02->id);
                },
                'portfolioFiles' => function ($q) {
                    $q->where('portfolio_files.is_active', true)->orderBy('portfolio_files.sort_order')->orderBy('portfolio_files.document_name');
                },
            ])
            ->ordered()
            ->get()
            ->map(function ($unit) {
                return [
                    'unit' => $unit,
                    'elements' => $unit->activeElemenKompetensis->map(function ($element) {
                        return [
                            'element' => $element,
                            'criterias' => $element->activeKriteriaKerjas,
                            'assessment' => $element->assessments->first(),
                        ];
                    }),
                    'portfolio_files' => $unit->portfolioFiles,
                ];
            });
    }

    private function getExistingEvidence(Apl02 $apl02)
    {
        return $apl02
            ->evidenceSubmissions()
            ->with('portfolioFile')
            ->get()
            ->groupBy(function ($evidence) {
                // Group by document name instead of portfolio_file_id
                return $evidence->portfolioFile ? $evidence->portfolioFile->document_name : 'Unknown Document';
            })
            ->map(function ($evidenceGroup, $documentName) {
                // Take the first (or most recent) evidence for this document name
                $evidence = $evidenceGroup->first();

                return [
                    'id' => $evidence->id,
                    'file_name' => $evidence->file_name,
                    'file_size_formatted' => $evidence->file_size_formatted,
                    'file_type' => $evidence->file_type,
                    'download_url' => route('asesi.apl02.download-evidence', [$evidence->apl_02_id, $evidence->id]),
                    'preview_url' => route('asesi.apl02.preview-evidence', [$evidence->apl_02_id, $evidence->id]),
                    'portfolio_file_id' => $evidence->portfolio_file_id,
                    'document_name' => $documentName,
                    // Add all portfolio IDs that have this document name
                    'portfolio_ids' => $evidenceGroup->pluck('portfolio_file_id')->toArray(),
                ];
            });
    }

    public function deleteEvidence(Apl02 $apl02, $evidenceId)
    {
        if (!in_array($apl02->status, ['draft', 'returned', 'open'])) {
            return response()->json(['success' => false, 'error' => 'APL 02 tidak dapat diupdate'], 403);
        }

        try {
            $evidence = $apl02->evidenceSubmissions()->findOrFail($evidenceId);
            $evidence->deleteFile();

            return response()->json([
                'success' => true,
                'message' => 'Bukti berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }

    public function downloadEvidence(Apl02 $apl02, $evidenceId)
    {
        $evidence = $apl02->evidenceSubmissions()->findOrFail($evidenceId);

        if (!Storage::disk('public')->exists($evidence->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($evidence->file_path, $evidence->file_name);
    }

    public function previewEvidence(Apl02 $apl02, $evidenceId)
    {
        $evidence = $apl02->evidenceSubmissions()->findOrFail($evidenceId);

        if (!Storage::disk('public')->exists($evidence->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        $fileType = strtolower($evidence->file_type);
        $filePath = storage_path('app/public/' . $evidence->file_path);
        $mimeType = $evidence->mime_type;

        switch ($fileType) {
            case 'pdf':
                return response()->file($filePath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $evidence->file_name . '"',
                ]);

            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $evidence->file_name . '"',
                ]);

            default:
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'Tipe file tidak dapat dipreview',
                        'download_url' => route('asesi.apl02.download-evidence', [$apl02->id, $evidenceId]),
                    ],
                    400,
                );
        }
    }

    public function preview(Apl02 $apl02)
    {
        // Add preview method if needed
        return view('asesi.apl02.preview', compact('apl02'));
    }

    public function exportPdf(Apl02 $apl02)
    {
        // Add PDF export method if needed
        return redirect()->back()->with('info', 'PDF export functionality not implemented yet');
    }
}
