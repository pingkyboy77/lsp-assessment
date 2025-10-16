<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SPTSignature;
use App\Models\DelegasiPersonilAsesmen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SPTSignatureController extends Controller
{
    /**
     * Display SPT Signatures index page
     */
    public function index()
    {
        return view('admin.spt-signatures.index');
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = DelegasiPersonilAsesmen::with([
            'asesi',
            'certificationScheme',
            'verifikatorTuk',
            'observer',
            'asesor',
            'sptSignature.signedBy',
            'apl01'
        ]);

        // Filter by SPT status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'pending') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('sptSignature')->orWhereHas('sptSignature', function ($subQ) {
                        $subQ->where('status', 'pending');
                    });
                });
            } elseif ($request->status === 'signed') {
                $query->whereHas('sptSignature', function ($q) {
                    $q->where('status', 'signed');
                });
            }
        } else {
            $query->where(function ($q) {
                $q->whereDoesntHave('sptSignature')->orWhereHas('sptSignature', function ($subQ) {
                    $subQ->where('status', 'pending');
                });
            });
        }

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('asesi', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('id_number', 'like', "%{$search}%");
                })->orWhereHas('certificationScheme', function ($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%");
                });
            });
        }

        $totalRecords = $query->count();
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        $data = $query->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data->map(function ($delegasi) {
                $spt = $delegasi->sptSignature;

                return [
                    'id' => $delegasi->id,
                    'asesi_info' => view('admin.spt-signatures.partials.asesi-info', compact('delegasi'))->render(),
                    'scheme_name' => '<div class="scheme-name">' . e($delegasi->certificationScheme->nama ?? '-') . '</div>',
                    'personil_info' => view('admin.spt-signatures.partials.personil-info', compact('delegasi'))->render(),
                    'status_badge' => view('admin.spt-signatures.partials.status-badge', compact('delegasi', 'spt'))->render(),
                    'created_at' => $delegasi->created_at->format('d/m/Y H:i'),
                    'actions' => view('admin.spt-signatures.partials.actions', compact('delegasi', 'spt'))->render(),
                ];
            }),
        ]);
    }

    /**
     * Get summary for modal
     */
    public function getSummary($id)
    {
        $delegasi = DelegasiPersonilAsesmen::with([
            'asesi',
            'certificationScheme',
            'sptSignature',
            'apl01'
        ])->findOrFail($id);

        $isMandiri = $this->isTukMandiri($delegasi);

        return response()->json([
            'id' => $delegasi->id,
            'asesi_name' => $delegasi->asesi->name,
            'scheme_name' => $delegasi->certificationScheme->nama ?? '-',
            'formatted_date' => $delegasi->created_at->format('d/m/Y'),
            'has_spt' => $delegasi->sptSignature ? true : false,
            'spt_status' => $delegasi->sptSignature ? $delegasi->sptSignature->status : 'not_generated',
            'is_mandiri' => $isMandiri,
            'tuk_type' => $isMandiri ? 'Mandiri' : 'Sewaktu',
        ]);
    }

    /**
     * Sign bulk delegasi - Generate SPT dengan nomor otomatis
     */
    public function signBulk(Request $request)
    {
        $request->validate([
            'delegasi_ids' => 'required|array',
            'delegasi_ids.*' => 'exists:delegasi_personil_asesmen,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $signedCount = 0;
            $signaturePath = public_path('assets/signatures/direktur_signature.png');

            if (!file_exists($signaturePath)) {
                throw new \Exception('File tanda tangan direktur tidak ditemukan di: ' . $signaturePath);
            }

            foreach ($request->delegasi_ids as $delegasiId) {
                $delegasi = DelegasiPersonilAsesmen::with([
                    'asesi',
                    'certificationScheme',
                    'verifikatorTuk',
                    'observer',
                    'asesor',
                    'tukRequest',
                    'apl01'
                ])->findOrFail($delegasiId);

                // Skip jika sudah signed
                if ($delegasi->sptSignature && $delegasi->sptSignature->is_signed) {
                    continue;
                }

                // Cek jenis TUK
                $isMandiri = $this->isTukMandiri($delegasi);

                // Create SPT record terlebih dahulu (auto-generate nomor via boot)
                $spt = $delegasi->sptSignature;
                if (!$spt) {
                    $spt = new SPTSignature();
                    $spt->delegasi_personil_id = $delegasi->id;
                    $spt->save(); // Trigger boot() untuk generate nomor
                }

                // Generate SPT sesuai jenis TUK
                if (!$isMandiri) {
                    // TUK Sewaktu: Generate SPT Verifikator
                    $sptVerifikator = $this->generateSPTVerifikator($delegasi, $spt, $signaturePath);
                    $spt->spt_verifikator_file = $sptVerifikator;
                } else {
                    // TUK Mandiri: Tidak ada Verifikator, set null
                    $spt->spt_verifikator_file = null;
                    $spt->spt_verifikator_number = null;
                }

                // Observer dan Asesor tetap dibuat untuk kedua jenis TUK
                $sptObserver = $this->generateSPTObserver($delegasi, $spt, $signaturePath);
                $sptAsesor = $this->generateSPTAsesor($delegasi, $spt, $signaturePath);

                // Update file paths dan status
                $spt->spt_observer_file = $sptObserver;
                $spt->spt_asesor_file = $sptAsesor;
                $spt->status = 'signed';
                $spt->signed_by = auth()->id();
                $spt->signed_at = now();
                $spt->signature_image = 'assets/signatures/direktur_signature.png';
                $spt->notes = $request->notes;
                $spt->save();

                $signedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$signedCount} SPT berhasil digenerate dan ditandatangani",
                'signed_count' => $signedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk Sign Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sign single delegasi
     */
    public function sign(Request $request, $delegasiId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $delegasi = DelegasiPersonilAsesmen::with([
                'asesi',
                'certificationScheme',
                'verifikatorTuk',
                'observer',
                'asesor',
                'tukRequest',
                'apl01'
            ])->findOrFail($delegasiId);

            if ($delegasi->sptSignature && $delegasi->sptSignature->is_signed) {
                return response()->json([
                    'success' => false,
                    'error' => 'SPT sudah ditandatangani sebelumnya',
                ], 400);
            }

            $signaturePath = public_path('assets/signatures/direktur_signature.png');

            if (!file_exists($signaturePath)) {
                throw new \Exception('File tanda tangan direktur tidak ditemukan');
            }

            // Cek jenis TUK
            $isMandiri = $this->isTukMandiri($delegasi);

            // Create SPT record (auto-generate nomor)
            $spt = $delegasi->sptSignature;
            if (!$spt) {
                $spt = new SPTSignature();
                $spt->delegasi_personil_id = $delegasi->id;
                $spt->save();
            }

            // Generate PDFs sesuai jenis TUK
            if (!$isMandiri) {
                // TUK Sewaktu: Generate SPT Verifikator
                $sptVerifikator = $this->generateSPTVerifikator($delegasi, $spt, $signaturePath);
                $spt->spt_verifikator_file = $sptVerifikator;
            } else {
                // TUK Mandiri: Tidak ada Verifikator
                $spt->spt_verifikator_file = null;
                $spt->spt_verifikator_number = null;
            }

            // Observer dan Asesor tetap dibuat
            $sptObserver = $this->generateSPTObserver($delegasi, $spt, $signaturePath);
            $sptAsesor = $this->generateSPTAsesor($delegasi, $spt, $signaturePath);

            // Update SPT
            $spt->spt_observer_file = $sptObserver;
            $spt->spt_asesor_file = $sptAsesor;
            $spt->status = 'signed';
            $spt->signed_by = auth()->id();
            $spt->signed_at = now();
            $spt->signature_image = 'assets/signatures/direktur_signature.png';
            $spt->notes = $request->notes;
            $spt->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SPT berhasil digenerate dan ditandatangani',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sign SPT Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download SPT file
     */
    public function downloadSPT($delegasiId, $type)
    {
        $delegasi = DelegasiPersonilAsesmen::with(['sptSignature', 'apl01'])->findOrFail($delegasiId);
        $spt = $delegasi->sptSignature;

        if (!$spt) {
            abort(404, 'SPT belum digenerate');
        }

        // Validasi untuk TUK Mandiri tidak bisa download SPT Verifikator
        $isMandiri = $this->isTukMandiri($delegasi);
        if ($isMandiri && $type === 'verifikator') {
            abort(404, 'SPT Verifikator tidak tersedia untuk TUK Mandiri');
        }

        $filePath = match ($type) {
            'verifikator' => $spt->spt_verifikator_file,
            'observer' => $spt->spt_observer_file,
            'asesor' => $spt->spt_asesor_file,
            default => null,
        };

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'File SPT tidak ditemukan');
        }

        $filename = basename($filePath);
        return Storage::download($filePath, $filename);
    }

    /**
     * Show detail SPT
     */
    public function show($id)
    {
        $delegasi = DelegasiPersonilAsesmen::with([
            'asesi',
            'certificationScheme',
            'tukRequest',
            'apl01',
            'sptSignature.signedBy',
            'verifikatorTuk',
            'observer',
            'asesor'
        ])->findOrFail($id);

        $spt = $delegasi->sptSignature;

        return view('admin.spt-signatures.show', compact('delegasi', 'spt'));
    }

    /**
     * Preview SPT file
     */
    public function preview($id, $type)
    {
        $delegasi = DelegasiPersonilAsesmen::with(['sptSignature', 'apl01'])->findOrFail($id);
        $spt = $delegasi->sptSignature;

        if (!$spt) {
            abort(404, 'SPT belum digenerate');
        }

        // Validasi untuk TUK Mandiri tidak bisa preview SPT Verifikator
        $isMandiri = $this->isTukMandiri($delegasi);
        if ($isMandiri && $type === 'verifikator') {
            abort(404, 'SPT Verifikator tidak tersedia untuk TUK Mandiri');
        }

        $filePath = match ($type) {
            'verifikator' => storage_path("app/private/{$spt->spt_verifikator_file}"),
            'observer' => storage_path("app/private/{$spt->spt_observer_file}"),
            'asesor' => storage_path("app/private/{$spt->spt_asesor_file}"),
            default => null,
        };

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Cek apakah TUK adalah Mandiri
     * Database value: "Mandiri" dan "Sewaktu" (dengan huruf kapital di awal)
     */
    private function isTukMandiri($delegasi)
    {
        if (!$delegasi->apl01) {
            return false;
        }

        // Case-insensitive comparison untuk keamanan
        return strtolower($delegasi->apl01->tuk) === 'mandiri';
    }

    /**
     * Cek apakah TUK adalah Sewaktu
     */
    private function isTukSewaktu($delegasi)
    {
        if (!$delegasi->apl01) {
            return false;
        }

        // Case-insensitive comparison untuk keamanan
        return strtolower($delegasi->apl01->tuk) === 'sewaktu';
    }

    /**
     * Generate SPT Verifikator (Hanya untuk TUK Sewaktu)
     */
    private function generateSPTVerifikator($delegasi, $spt, $signaturePath)
    {
        $data = [
            'spt_number' => $spt->spt_verifikator_number,
            'verifikator_name' => $delegasi->verifikatorTuk->name,
            'verifikator_nik' => $delegasi->verifikatorTuk->id_number ?? '-',
            'assessment_date' => $delegasi->tanggal_pelaksanaan_asesmen->format('d/m/Y'),
            'director_name' => 'Haryajid Ramelan',
            'director_signature' => $signaturePath,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('pdfs.spt-verifikator', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'spt/verifikator/SPT-TUK-' . $delegasi->id . '-' . time() . '.pdf';
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate SPT Observer (Untuk semua jenis TUK)
     */
    private function generateSPTObserver($delegasi, $spt, $signaturePath)
    {
        $data = [
            'spt_number' => $spt->spt_observer_number,
            'observer_name' => $delegasi->observer->name,
            'observer_nik' => $delegasi->observer->id_number ?? '-',
            'assessment_date' => $delegasi->tanggal_pelaksanaan_asesmen->format('d/m/Y'),
            'director_name' => 'Haryajid Ramelan',
            'director_signature' => $signaturePath,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('pdfs.spt-observer', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'spt/observer/SPT-OBS-' . $delegasi->id . '-' . time() . '.pdf';
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate SPT Asesor (Untuk semua jenis TUK)
     * Database values: "Mandiri" dan "Sewaktu" (dengan huruf kapital di awal)
     * - TUK Mandiri: location = "Mandiri", alamat = "" (kosong)
     * - TUK Sewaktu: location = "Sewaktu, Asesmen Jarak Jauh", alamat = lokasi assessment
     */
    private function generateSPTAsesor($delegasi, $spt, $signaturePath)
    {
        $isMandiri = $this->isTukMandiri($delegasi);

        // Tentukan lokasi dan alamat berdasarkan jenis TUK
        if ($isMandiri) {
            // TUK Mandiri
            $tuk = 'Mandiri';
            $alamat = ''; // Kosongkan alamat untuk TUK Mandiri
        } else {
            // TUK Sewaktu (default)
            $tuk = 'Sewaktu, Asesmen Jarak Jauh';
            $alamat = $delegasi->tukRequest->lokasi_assessment ?? '';
        }

        $data = [
            'spt_number' => $spt->spt_asesor_number,
            'asesor_name' => $delegasi->asesor->name,
            'asesor_met' => $delegasi->asesor_met ?? '-',
            'asesi_name' => $delegasi->asesi->name,
            'scheme_name' => $delegasi->certificationScheme->nama,
            'location' => $tuk,
            'alamat' => $alamat,
            'assessment_date' => $delegasi->tanggal_pelaksanaan_asesmen->format('d/m/Y'),
            'director_name' => 'Haryajid Ramelan',
            'director_signature' => $signaturePath,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('pdfs.spt-asesor', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'spt/asesor/SPT-AK-' . $delegasi->id . '-' . time() . '.pdf';
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Get location info (Legacy method - not used anymore)
     * Database values: "Mandiri" dan "Sewaktu" (dengan huruf kapital di awal)
     * Kept for backward compatibility
     */
    private function getLocationInfo($delegasi)
    {
        if ($delegasi->apl01) {
            $jenisAsesmen = strtolower($delegasi->apl01->tuk);

            if ($jenisAsesmen === 'sewaktu') {
                return 'Sewaktu, Asesmen Jarak Jauh';
            } elseif ($jenisAsesmen === 'mandiri') {
                return 'Mandiri';
            }
        }

        // Fallback
        if ($delegasi->tukRequest) {
            return $delegasi->tukRequest->lokasi_assessment ?? 'Sewaktu, Asesmen Jarak Jauh';
        }

        return 'Sewaktu, Asesmen Jarak Jauh';
    }
}
