<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminMapaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mapa::with([
            'delegasi.asesi',
            'certificationScheme',
            'asesor',
            'reviewedBy',
            'ak07'
        ]);

        // Filter by status - Show all status by default
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by Asesi name (server-side, case-insensitive with PostgreSQL ILIKE)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('delegasi.asesi', function ($sq) use ($search) {
                    $sq->whereRaw('name ILIKE ?', ["%{$search}%"]);
                })
                    ->orWhereHas('certificationScheme', function ($sq) use ($search) {
                        $sq->whereRaw('nama ILIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereRaw('nomor_mapa ILIKE ?', ["%{$search}%"]);
            });
        }

        // Filter by date range (Tanggal Submit) - Server-side
        if ($request->filled('date_from')) {
            $dateFrom = \Carbon\Carbon::createFromFormat('Y-m-d', $request->date_from)->startOfDay();
            $query->where('submitted_at', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = \Carbon\Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay();
            $query->where('submitted_at', '<=', $dateTo);
        }

        // Filter by Skema (Certification Scheme) - Server-side
        if ($request->filled('skema')) {
            $query->where('certification_scheme_id', $request->skema);
        }

        // Sorting
        $query->orderBy('submitted_at', 'desc');

        // Stats (updated untuk accuracy)
        $stats = [
            'total' => Mapa::count(),
            'submitted' => Mapa::where('status', 'submitted')->count(),
            'approved' => Mapa::where('status', 'approved')->count(),
            'validated' => Mapa::where('status', 'validated')->count(),
        ];

        // Pagination
        $perPage = $request->get('per_page', 15);
        $mapaList = $query->paginate($perPage)->withQueryString();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $mapaList->items(),
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $mapaList->currentPage(),
                    'last_page' => $mapaList->lastPage(),
                    'per_page' => $mapaList->perPage(),
                    'total' => $mapaList->total(),
                ],
                'html' => view('admin.mapa.partials.table-rows', compact('mapaList'))->render(),
                'pagination_html' => $mapaList->appends(request()->query())->links()->render(),
            ]);
        }

        // Return view for normal page load
        return view('admin.mapa.index', compact('mapaList', 'stats'));
    }

    /**
     * Get MAPA info for modal (AJAX)
     */
    public function getInfo($id)
    {
        try {
            $mapa = Mapa::with(['delegasi.asesi', 'certificationScheme', 'asesor'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'mapa' => [
                    'id' => $mapa->id,
                    'nomor_mapa' => $mapa->nomor_mapa,
                    'mapa_code' => $mapa->mapa_code,
                    'asesi_name' => $mapa->delegasi->asesi->name ?? 'N/A',
                    'asesi_email' => $mapa->delegasi->asesi->email ?? 'N/A',
                    'skema_name' => $mapa->certificationScheme->nama ?? 'N/A',
                    'asesor_name' => $mapa->asesor->name ?? 'N/A',
                    'submitted_at' => $mapa->submitted_at ? $mapa->submitted_at->format('d/m/Y H:i') : '-',
                    'status' => $mapa->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting MAPA info: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'error' => 'MAPA tidak ditemukan',
                ],
                404,
            );
        }
    }

    /**
     * Show MAPA detail for review
     */
    public function show($id)
    {
        $mapa = Mapa::with(['delegasi.asesi', 'delegasi.formKerahasiaan', 'certificationScheme.kelompokKerjas.unitKompetensis', 'asesor', 'reviewedBy', 'signedBy', 'apl01', 'apl02', 'ak07'])->findOrFail($id);

        $kelompokDetails = $mapa->getKelompokMetodeDetails();

        return view('admin.mapa.show', compact('mapa', 'kelompokDetails'));
    }

    /**
     * Approve MAPA
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $mapa = Mapa::findOrFail($id);

            if (!$mapa->canBeReviewed()) {
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'MAPA tidak dapat direview (status: ' . $mapa->status . ')',
                    ],
                    400,
                );
            }

            $mapa->approve(auth()->id(), $request->review_notes);

            DB::commit();

            // Log activity
            activity()
                ->performedOn($mapa)
                ->causedBy(auth()->user())
                ->withProperties([
                    'nomor_mapa' => $mapa->nomor_mapa,
                    'notes' => $request->review_notes,
                ])
                ->log('Admin approved MAPA');

            return response()->json([
                'success' => true,
                'message' => 'MAPA berhasil diapprove. Asesor dapat melakukan validasi.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving MAPA: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal approve MAPA: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Reject MAPA
     */
    public function reject(Request $request, $id)
    {
        $request->validate(
            [
                'review_notes' => 'required|string|max:1000',
            ],
            [
                'review_notes.required' => 'Catatan review wajib diisi untuk reject MAPA',
            ],
        );

        try {
            DB::beginTransaction();

            $mapa = Mapa::findOrFail($id);

            if (!$mapa->canBeReviewed()) {
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'MAPA tidak dapat direview (status: ' . $mapa->status . ')',
                    ],
                    400,
                );
            }

            $mapa->reject(auth()->id(), $request->review_notes);

            DB::commit();

            // Log activity
            activity()
                ->performedOn($mapa)
                ->causedBy(auth()->user())
                ->withProperties([
                    'nomor_mapa' => $mapa->nomor_mapa,
                    'notes' => $request->review_notes,
                ])
                ->log('Admin rejected MAPA');

            return response()->json([
                'success' => true,
                'message' => 'MAPA ditolak. Asesor dapat mengedit kembali.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting MAPA: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal reject MAPA: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Bulk Approve MAPA
     */
    public function bulkApprove(Request $request)
    {
        $request->validate(
            [
                'mapa_ids' => 'required|array|min:1',
                'mapa_ids.*' => 'exists:mapa,id',
                'review_notes' => 'nullable|string|max:1000',
            ],
            [
                'mapa_ids.required' => 'Pilih minimal 1 MAPA untuk di-approve',
                'mapa_ids.*.exists' => 'MAPA tidak ditemukan',
            ],
        );

        try {
            DB::beginTransaction();

            $successCount = 0;
            $failedCount = 0;
            $errors = [];
            $processedMapas = [];

            foreach ($request->mapa_ids as $mapaId) {
                try {
                    $mapa = Mapa::with(['delegasi.asesi', 'certificationScheme'])->findOrFail($mapaId);

                    if ($mapa->canBeReviewed()) {
                        $mapa->approve(auth()->id(), $request->review_notes);
                        $successCount++;
                        $processedMapas[] = $mapa->nomor_mapa;
                    } else {
                        $failedCount++;
                        $errors[] = "MAPA {$mapa->nomor_mapa} tidak dapat direview (status: {$mapa->status_text})";
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Error pada MAPA ID {$mapaId}: " . $e->getMessage();
                    Log::error("Bulk approve error for MAPA {$mapaId}: " . $e->getMessage());
                }
            }

            DB::commit();

            // Log bulk activity
            if ($successCount > 0) {
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'success_count' => $successCount,
                        'failed_count' => $failedCount,
                        'mapa_numbers' => $processedMapas,
                        'notes' => $request->review_notes,
                    ])
                    ->log("Bulk approved {$successCount} MAPA");
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil approve {$successCount} MAPA" . ($failedCount > 0 ? ", {$failedCount} gagal" : ''),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk approving MAPA: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal melakukan bulk approve: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Bulk Reject MAPA
     */
    public function bulkReject(Request $request)
    {
        $request->validate(
            [
                'mapa_ids' => 'required|array|min:1',
                'mapa_ids.*' => 'exists:mapa,id',
                'review_notes' => 'required|string|max:1000',
            ],
            [
                'mapa_ids.required' => 'Pilih minimal 1 MAPA untuk di-reject',
                'mapa_ids.*.exists' => 'MAPA tidak ditemukan',
                'review_notes.required' => 'Catatan review wajib diisi untuk bulk reject',
            ],
        );

        try {
            DB::beginTransaction();

            $successCount = 0;
            $failedCount = 0;
            $errors = [];
            $processedMapas = [];

            foreach ($request->mapa_ids as $mapaId) {
                try {
                    $mapa = Mapa::with(['delegasi.asesi', 'certificationScheme'])->findOrFail($mapaId);

                    if ($mapa->canBeReviewed()) {
                        $mapa->reject(auth()->id(), $request->review_notes);
                        $successCount++;
                        $processedMapas[] = $mapa->nomor_mapa;
                    } else {
                        $failedCount++;
                        $errors[] = "MAPA {$mapa->nomor_mapa} tidak dapat direview (status: {$mapa->status_text})";
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Error pada MAPA ID {$mapaId}: " . $e->getMessage();
                    Log::error("Bulk reject error for MAPA {$mapaId}: " . $e->getMessage());
                }
            }

            DB::commit();

            // Log bulk activity
            if ($successCount > 0) {
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'success_count' => $successCount,
                        'failed_count' => $failedCount,
                        'mapa_numbers' => $processedMapas,
                        'notes' => $request->review_notes,
                    ])
                    ->log("Bulk rejected {$successCount} MAPA");
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil reject {$successCount} MAPA" . ($failedCount > 0 ? ", {$failedCount} gagal" : ''),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk rejecting MAPA: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal melakukan bulk reject: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Unlock AK07 for re-editing
     */
    public function unlockAk07($mapaId)
    {
        try {
            DB::beginTransaction();

            $mapa = Mapa::with(['ak07', 'delegasi.formKerahasiaan'])->findOrFail($mapaId);

            if (!$mapa->ak07) {
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'AK07 tidak ditemukan untuk MAPA ini',
                    ],
                    404,
                );
            }

            $ak07 = $mapa->ak07;

            // Delete asesi signature if exists
            if ($ak07->asesi_signature && Storage::disk('public')->exists($ak07->asesi_signature)) {
                Storage::disk('public')->delete($ak07->asesi_signature);
            }

            // Reset AK07 status and signatures
            $ak07->update([
                'status' => 'draft',
                'asesi_signature' => null,
                'asesi_signed_at' => null,
                'asesi_ip' => null,
                'final_recommendation' => null,
                'recommendation_notes' => null,
                'final_signature_path' => null,
                'final_signed_at' => null,
                'final_signed_by' => null,
            ]);

            // Delete Form Kerahasiaan if exists
            if ($mapa->delegasi && $mapa->delegasi->formKerahasiaan) {
                $formKerahasiaan = $mapa->delegasi->formKerahasiaan;

                // Delete signatures
                if ($formKerahasiaan->ttd_asesor && Storage::disk('public')->exists($formKerahasiaan->ttd_asesor)) {
                    Storage::disk('public')->delete($formKerahasiaan->ttd_asesor);
                }
                if ($formKerahasiaan->ttd_asesi && Storage::disk('public')->exists($formKerahasiaan->ttd_asesi)) {
                    Storage::disk('public')->delete($formKerahasiaan->ttd_asesi);
                }

                $formKerahasiaan->delete();
            }

            // Reset MAPA validation if needed
            if ($mapa->status === 'validated') {
                $mapa->update([
                    'status' => 'approved',
                    'validation_signature' => null,
                    'validated_by' => null,
                    'validated_at' => null,
                    'validation_ip' => null,
                    'final_recommendation_status' => null,
                ]);
            }

            DB::commit();

            // Log activity
            activity()
                ->performedOn($ak07)
                ->causedBy(auth()->user())
                ->withProperties([
                    'mapa_id' => $mapa->id,
                    'ak07_id' => $ak07->id,
                    'nomor_ak07' => $ak07->nomor_ak07,
                ])
                ->log('Admin unlocked AK07 for re-editing');

            return response()->json([
                'success' => true,
                'message' => 'AK07 berhasil di-unlock. Asesor dapat mengisi ulang AK07 dan Form Kerahasiaan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error unlocking AK07: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal unlock AK07: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * View Form Kerahasiaan
     */
    public function viewFormKerahasiaan($mapaId)
    {
        $mapa = Mapa::with(['delegasi.formKerahasiaan', 'delegasi.asesi', 'asesor'])->findOrFail($mapaId);

        if (!$mapa->delegasi || !$mapa->delegasi->formKerahasiaan) {
            return redirect()->back()->with('error', 'Form Kerahasiaan tidak ditemukan');
        }

        $formKerahasiaan = $mapa->delegasi->formKerahasiaan;

        return view('admin.mapa.form-kerahasiaan-view', compact('formKerahasiaan', 'mapa'));
    }

    public function viewAk07($mapaId)
    {
        $mapa = Mapa::with(['delegasi.asesi', 'certificationScheme', 'asesor', 'ak07.asesor'])->findOrFail($mapaId);

        if (!$mapa->ak07) {
            return redirect()->back()->with('error', 'AK07 tidak ditemukan untuk MAPA ini');
        }

        $ak07 = $mapa->ak07;

        return view('admin.mapa.ak07-view', compact('ak07', 'mapa'));
    }
}
