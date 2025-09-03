<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apl01Pendaftaran;
use App\Models\UserDocument;
use App\Models\RequirementItem;
use App\Models\RegionProv;
use App\Models\RegionKab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminApl01Controller extends Controller
{
    /**
     * Display a listing of APL 01 applications
     */
    public function index(Request $request)
    {
        return view('admin.apl01.index');
    }

    /**
     * Get data for DataTables server-side processing
     */
    public function data(Request $request)
    {
        try {
            $query = Apl01Pendaftaran::with(['user', 'certificationScheme:id,nama,jenjang', 'reviewer:id,name', 'lembagaPelatihan:id,name', 'kotaRumah:id,name', 'provinsiRumah:id,name', 'kotaKantor:id,name', 'provinsiKantor:id,name']);

            // Apply date filters (default to current month if not provided)
            $dateFrom = $request->date_from ?: date('Y-m-01');
            $dateTo = $request->date_to ?: date('Y-m-d');

            if ($dateFrom && $dateTo) {
                $query->whereBetween('submitted_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply scheme filter
            if ($request->filled('scheme_id')) {
                $query->where('certification_scheme_id', $request->scheme_id);
            }

            // Apply search filter
            if ($request->filled('search_input')) {
                $search = $request->search_input;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'ILIKE', "%{$search}%")
                        ->orWhere('nomor_apl_01', 'ILIKE', "%{$search}%")
                        ->orWhere('nik', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            }

            // DataTables parameters
            $start = $request->input('start', 0);
            $length = $request->input('length', 25);
            $orderColumn = $request->input('order.0.column', 4);
            $orderDir = $request->input('order.0.dir', 'desc');

            // Define sortable columns
            $columns = [
                0 => 'id',
                1 => 'nama_lengkap',
                2 => 'certification_scheme_id',
                3 => 'training_provider',
                4 => 'submitted_at',
                5 => 'status',
                6 => 'reviewed_at',
                7 => 'id',
            ];

            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }

            // Get counts
            $totalRecords = Apl01Pendaftaran::count();
            $filteredRecords = (clone $query)->count();

            // Apply pagination
            $data = $query->skip($start)->take($length)->get();

            // Format data for DataTables
            $formattedData = $data->map(function ($apl) {
                return [
                    'id' => $apl->id,
                    'nomor_apl_01' => $apl->nomor_apl_01 ?: 'DRAFT',
                    'nama_lengkap' => $apl->nama_lengkap,
                    'email' => $apl->email,
                    'nik' => $apl->nik,
                    'status' => $apl->status,
                    'submitted_at' => $apl->submitted_at?->toISOString(),
                    'reviewed_at' => $apl->reviewed_at?->toISOString(),
                    'certification_scheme_nama' => $apl->certificationScheme->nama ?? null,
                    'certification_scheme_jenjang' => $apl->certificationScheme->jenjang ?? null,
                    'units_count' => $apl->certificationScheme->activeUnitKompetensis->count() ?? 0,
                    'reviewer_name' => $apl->reviewer->name ?? null,
                    'lembaga_pelatihan_nama' => $apl->lembagaPelatihan->name ?? null,
                    'kota_rumah_nama' => $apl->kotaRumah->name ?? null,
                    'provinsi_rumah_nama' => $apl->provinsiRumah->name ?? null,
                    'kota_kantor_nama' => $apl->kotaKantor->name ?? null,
                    'provinsi_kantor_nama' => $apl->provinsiKantor->name ?? null,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 DataTables Error: ' . $e->getMessage());
            return response()->json(
                [
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Failed to load data: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get statistics for dashboard - RENAMED from getStatistics to avoid confusion
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Apl01Pendaftaran::count(),
                'draft' => Apl01Pendaftaran::where('status', 'draft')->count(),
                'submitted' => Apl01Pendaftaran::where('status', 'submitted')->count(),
                'review' => Apl01Pendaftaran::whereIn('status', ['review', 'reviewed'])->count(),
                'approved' => Apl01Pendaftaran::where('status', 'approved')->count(),
                'rejected' => Apl01Pendaftaran::where('status', 'rejected')->count(),
                'returned' => Apl01Pendaftaran::where('status', 'returned')->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('APL01 Statistics Error: ' . $e->getMessage());
            return response()->json(
                [
                    'total' => 0,
                    'draft' => 0,
                    'submitted' => 0,
                    'review' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                    'returned' => 0,
                ],
                500,
            );
        }
    }

    /**
     * Display the specified APL 01 application
     */
    public function show(Apl01Pendaftaran $apl)
    {
        try {
            $apl->load(['user', 'certificationScheme.activeUnitKompetensis', 'reviewer', 'kotaRumah', 'provinsiRumah', 'kotaKantor', 'provinsiKantor', 'lembagaPelatihan']);

            return view('admin.apl01.show', compact('apl'));
        } catch (\Exception $e) {
            Log::error('APL01 Show Error: ' . $e->getMessage());
            return redirect()->route('admin.apl01.index')->with('error', 'APL 01 tidak ditemukan atau terjadi kesalahan.');
        }
    }

    /**
     * Show the form for editing the specified APL 01 application
     */
    public function edit(Apl01Pendaftaran $apl)
    {
        try {
            $apl->load(['certificationScheme.activeUnitKompetensis']);
            $provinces = RegionProv::orderBy('name')->get();
            return view('admin.apl01.edit', compact('apl', 'provinces'));
        } catch (\Exception $e) {
            Log::error('APL01 Edit Error: ' . $e->getMessage());
            return redirect()->route('admin.apl01.show', $apl)->with('error', 'Gagal memuat form edit.');
        }
    }

    /**
     * Update the specified APL 01 application
     */
    public function update(Request $request, Apl01Pendaftaran $apl)
    {
        try {
            $validatedData = $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'nik' => 'required|string|max:20',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'kebangsaan' => 'required|string|max:100',
                'alamat_rumah' => 'required|string',
                'kota_rumah' => 'required|exists:region_kabs,id',
                'provinsi_rumah' => 'required|exists:region_provs,id',
                'kode_pos' => 'nullable|string|max:10',
                'no_telp_rumah' => 'nullable|string|max:20',
                'no_hp' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'pendidikan_terakhir' => 'required|string|max:100',
                'nama_sekolah_terakhir' => 'required|string|max:255',
                'jabatan' => 'required|string|max:255',
                'nama_tempat_kerja' => 'required|string|max:255',
                'kategori_pekerjaan' => 'required|string|max:255',
                'nama_jalan_kantor' => 'nullable|string',
                'kota_kantor' => 'nullable|exists:region_kabs,id',
                'provinsi_kantor' => 'nullable|exists:region_provs,id',
                'kode_pos_kantor' => 'nullable|string|max:10',
                'no_telp_kantor' => 'nullable|string|max:20',
                'tujuan_asesmen' => 'required|string|max:255',
                'tuk' => 'nullable|string|max:255',
                'kategori_peserta' => 'required|in:individu,training_provider',
                'training_provider' => 'nullable|exists:lembaga_pelatihans,id',
                'pernah_asesmen_lsp' => 'nullable|string|max:255',
                'bisa_share_screen' => 'nullable|string|max:255',
                'bisa_gunakan_browser' => 'nullable|string|max:255',
                'aplikasi_yang_digunakan' => 'nullable|array',
                'nama_lengkap_ktp' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            DB::transaction(function () use ($apl, $validatedData) {
                $apl->update($validatedData);
            });

            return redirect()->route('admin.apl01.show', $apl)->with('success', 'APL 01 berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('APL01 Update Error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui APL 01: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(Apl01Pendaftaran $apl)
    {
        try {
            if (!$apl->canDelete) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'APL 01 tidak dapat dihapus karena sudah disubmit atau sedang dalam proses review.',
                    ],
                    403,
                );
            }

            DB::transaction(function () use ($apl) {
                $apl->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 Delete Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menghapus APL 01: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get review modal data
     */
    public function getReviewData(Apl01Pendaftaran $apl)
    {
        try {
            $apl->load(['user', 'certificationScheme.activeUnitKompetensis', 'kotaRumah', 'provinsiRumah', 'kotaKantor', 'provinsiKantor', 'lembagaPelatihan']);

            // Get user documents
            $userDocuments = UserDocument::where('user_id', $apl->user_id)->get();

            // Get requirement documents
            $requirementDocuments = [];
            if ($apl->requirement_answers && is_array($apl->requirement_answers)) {
                foreach ($apl->requirement_answers as $itemId => $filePath) {
                    if ($filePath && Storage::disk('public')->exists($filePath)) {
                        $requirementItem = RequirementItem::find($itemId);
                        $requirementDocuments[] = [
                            'item_id' => $itemId,
                            'item_name' => $requirementItem ? $requirementItem->document_name : "Dokumen {$itemId}",
                            'file_path' => $filePath,
                            'file_url' => Storage::url($filePath),
                            'file_size' => Storage::disk('public')->size($filePath),
                            'file_extension' => pathinfo($filePath, PATHINFO_EXTENSION),
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'apl' => [
                        'id' => $apl->id,
                        'nomor_apl_01' => $apl->nomor_apl_01,
                        'nama_lengkap' => $apl->nama_lengkap,
                        'email' => $apl->email,
                        'no_hp' => $apl->no_hp,
                        'status' => $apl->status,
                        'status_text' => $apl->statusText,
                        'submitted_at' => $apl->submitted_at?->format('d F Y H:i'),
                        'certification_scheme' => $apl->certificationScheme->nama ?? null,
                        'unit_count' => $apl->certificationScheme->activeUnitKompetensis->count() ?? 0,
                        'kota_rumah' => $apl->kotaRumah->name ?? null,
                        'provinsi_rumah' => $apl->provinsiRumah->name ?? null,
                        'kota_kantor' => $apl->kotaKantor->name ?? null,
                        'provinsi_kantor' => $apl->provinsiKantor->name ?? null,
                        'lembaga_pelatihan' => $apl->lembagaPelatihan->name ?? null,
                    ],
                    'user_documents' => $userDocuments->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'jenis_dokumen' => $doc->jenis_dokumen,
                            'file_url' => $doc->file_url,
                            'file_exists' => $doc->file_exists,
                            'file_size_kb' => $doc->file_size_kb,
                            'file_extension' => pathinfo($doc->file_path, PATHINFO_EXTENSION),
                        ];
                    }),
                    'requirement_documents' => $requirementDocuments,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 Review Data Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memuat data review: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Approve APL 01 application
     */
    public function approve(Request $request, Apl01Pendaftaran $apl)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string|max:1000',
            ]);

            DB::transaction(function () use ($apl, $request) {
                $apl->approve(Auth::id(), $request->notes);
            });

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil disetujui.',
                'status' => $apl->fresh()->statusText,
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 Approve Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menyetujui APL 01: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Reject APL 01 application
     */
    public function reject(Request $request, Apl01Pendaftaran $apl)
    {
        try {
            $request->validate([
                'notes' => 'required|string|max:1000',
            ]);

            DB::transaction(function () use ($apl, $request) {
                $apl->reject(Auth::id(), $request->notes);
            });

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil ditolak.',
                'status' => $apl->fresh()->statusText,
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 Reject Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menolak APL 01: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Set APL 01 application under review
     */
    public function setUnderReview(Request $request, Apl01Pendaftaran $apl)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string|max:1000',
            ]);

            DB::transaction(function () use ($apl, $request) {
                $apl->setUnderReview(Auth::id(), $request->notes);
            });

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil diset dalam review.',
                'status' => $apl->fresh()->statusText,
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 Set Review Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengatur status review: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Return APL 01 application for revision
     */
    public function returnForRevision(Request $request, Apl01Pendaftaran $apl)
    {
        try {
            $request->validate([
                'notes' => 'required|string|max:1000',
            ]);

            DB::transaction(function () use ($apl, $request) {
                $apl->returnForRevision(Auth::id(), $request->notes);
            });

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil dikembalikan untuk revisi.',
                'status' => $apl->fresh()->statusText,
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 Return Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengembalikan APL 01: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Export APL 01 applications to Excel
     */
    public function export(Request $request)
    {
        try {
            $apls = Apl01Pendaftaran::with(['user', 'certificationScheme', 'reviewer', 'lembagaPelatihan', 'kotaRumah', 'provinsiRumah'])->get();

            $data = $apls->map(function ($apl) {
                return [
                    'No. APL' => $apl->nomor_apl_01 ?: 'DRAFT',
                    'Nama Lengkap' => $apl->nama_lengkap,
                    'NIK' => $apl->nik,
                    'Email' => $apl->email,
                    'No. HP' => $apl->no_hp,
                    'Status' => $apl->statusText ?? $apl->status,
                    'Skema Sertifikasi' => $apl->certificationScheme->nama ?? '',
                    'Jenjang' => $apl->certificationScheme->jenjang ?? '',
                    'Lembaga Pelatihan' => $apl->lembagaPelatihan->nama ?? 'Individu',
                    'Kota Rumah' => $apl->kotaRumah->name ?? '',
                    'Provinsi Rumah' => $apl->provinsiRumah->name ?? '',
                    'Submitted At' => $apl->submitted_at?->format('d/m/Y H:i') ?? '',
                    'Reviewed At' => $apl->reviewed_at?->format('d/m/Y H:i') ?? '',
                    'Reviewer' => $apl->reviewer->name ?? '',
                    'Notes' => $apl->notes ?? '',
                ];
            });

            // Generate CSV content
            $csvContent = '';
            $headers = ['No. APL', 'Nama Lengkap', 'NIK', 'Email', 'No. HP', 'Status', 'Skema Sertifikasi', 'Jenjang', 'Lembaga Pelatihan', 'Kota Rumah', 'Provinsi Rumah', 'Submitted At', 'Reviewed At', 'Reviewer', 'Notes'];

            $csvContent .= implode(',', $headers) . "\n";

            foreach ($data as $row) {
                $csvContent .= '"' . implode('","', array_values($row)) . '"' . "\n";
            }

            $filename = 'apl01_export_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Log::error('APL01 Export Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Export gagal: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Bulk actions for multiple APL 01 applications
     */
    public function bulkAction(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:apl_01_pendaftarans,id',
                'action' => 'required|in:approve,reject,set_review,delete',
                'notes' => 'nullable|string|max:1000',
            ]);

            $apls = Apl01Pendaftaran::whereIn('id', $request->ids)->get();
            $successCount = 0;
            $errors = [];

            DB::transaction(function () use ($apls, $request, &$successCount, &$errors) {
                foreach ($apls as $apl) {
                    try {
                        switch ($request->action) {
                            case 'approve':
                                $apl->approve(Auth::id(), $request->notes);
                                break;
                            case 'reject':
                                $apl->reject(Auth::id(), $request->notes);
                                break;
                            case 'set_review':
                                $apl->setUnderReview(Auth::id(), $request->notes);
                                break;
                            case 'delete':
                                if (property_exists($apl, 'canDelete') && $apl->canDelete) {
                                    $apl->delete();
                                } else {
                                    throw new \Exception("APL {$apl->nomor_apl_01} tidak dapat dihapus.");
                                }
                                break;
                        }
                        $successCount++;
                    } catch (\Exception $e) {
                        $errors[] = "APL {$apl->nomor_apl_01}: " . $e->getMessage();
                        Log::error("Bulk action failed for APL {$apl->id}: " . $e->getMessage());
                    }
                }
            });

            $message = "{$successCount} APL 01 berhasil diproses.";
            if (!empty($errors)) {
                $message .= ' Gagal: ' . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= ' dan ' . (count($errors) - 3) . ' lainnya.';
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'processed' => $successCount,
                'total' => count($apls),
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 Bulk Action Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Bulk action gagal: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function reopen(Request $request, Apl01Pendaftaran $apl)
    {
        // 1. Validasi input
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // 2. Ambil status lama sebelum update
            $previousStatus = $apl->status;

            // 3. Update APL 01
            $updateData = [
                'status' => 'open',
                'reopened_at' => now(),
                'reopened_by' => auth()->id() ?? null,
                'reopen_notes' => $request->notes,
            ];

            // Optional: pastikan kolom ada sebelum update
            $fillableColumns = $apl->getFillable();
            $updateData = array_filter($updateData, fn($key) => in_array($key, $fillableColumns), ARRAY_FILTER_USE_KEY);

            $apl->update($updateData);

            // 4. Logging activity
            activity('apl01')
                ->performedOn($apl)
                ->causedBy(auth()->user() ?? null)
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                ])
                ->log('APL 01 reopened for editing');

            // 5. Response sukses
            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil dibuka kembali untuk diedit oleh asesi.',
            ]);
        } catch (\Exception $e) {
            // 6. Response error
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal membuka kembali APL 01: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
