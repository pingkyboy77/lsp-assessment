<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminMapaController extends Controller
{
    /**
     * Display MAPA list for review
     */
    public function index(Request $request)
    {
        $query = Mapa::with([
            'delegasi.asesi',
            'certificationScheme',
            'asesor',
            'reviewedBy'
        ]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show submitted and approved only
            $query->whereIn('status', ['submitted', 'approved']);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('delegasi.asesi', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('certificationScheme', function ($sq) use ($search) {
                        $sq->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhere('nomor_mapa', 'like', "%{$search}%");
            });
        }

        $query->orderBy('submitted_at', 'desc');

        // Stats
        $stats = [
            'total' => Mapa::whereIn('status', ['submitted', 'approved', 'validated'])->count(),
            'submitted' => Mapa::submitted()->count(),
            'approved' => Mapa::approved()->count(),
            'validated' => Mapa::validated()->count(),
        ];

        $perPage = $request->get('per_page', 15);
        $mapaList = $query->paginate($perPage)->withQueryString();

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

        return view('admin.mapa.index', compact('mapaList', 'stats'));
    }

    /**
     * Get MAPA info for modal (AJAX)
     */
    public function getInfo($id)
    {
        try {
            $mapa = Mapa::with([
                'delegasi.asesi',
                'certificationScheme',
                'asesor'
            ])->findOrFail($id);

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
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting MAPA info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'MAPA tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Show MAPA detail for review
     */
    public function show($id)
    {
        $mapa = Mapa::with([
            'delegasi.asesi',
            'certificationScheme.kelompokKerjas.unitKompetensis',
            'asesor',
            'reviewedBy',
            'signedBy',
            'apl01',
            'apl02'
        ])->findOrFail($id);

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
                return response()->json([
                    'success' => false,
                    'error' => 'MAPA tidak dapat direview (status: ' . $mapa->status . ')',
                ], 400);
            }

            $mapa->approve(auth()->id(), $request->review_notes);

            DB::commit();

            // Log activity
            activity()
                ->performedOn($mapa)
                ->causedBy(auth()->user())
                ->withProperties([
                    'nomor_mapa' => $mapa->nomor_mapa,
                    'notes' => $request->review_notes
                ])
                ->log('Admin approved MAPA');

            return response()->json([
                'success' => true,
                'message' => 'MAPA berhasil diapprove. Asesor dapat melakukan validasi.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving MAPA: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Gagal approve MAPA: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject MAPA
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'review_notes' => 'required|string|max:1000',
        ], [
            'review_notes.required' => 'Catatan review wajib diisi untuk reject MAPA'
        ]);

        try {
            DB::beginTransaction();

            $mapa = Mapa::findOrFail($id);

            if (!$mapa->canBeReviewed()) {
                return response()->json([
                    'success' => false,
                    'error' => 'MAPA tidak dapat direview (status: ' . $mapa->status . ')',
                ], 400);
            }

            $mapa->reject(auth()->id(), $request->review_notes);

            DB::commit();

            // Log activity
            activity()
                ->performedOn($mapa)
                ->causedBy(auth()->user())
                ->withProperties([
                    'nomor_mapa' => $mapa->nomor_mapa,
                    'notes' => $request->review_notes
                ])
                ->log('Admin rejected MAPA');

            return response()->json([
                'success' => true,
                'message' => 'MAPA ditolak. Asesor dapat mengedit kembali.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting MAPA: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Gagal reject MAPA: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk Approve MAPA
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'mapa_ids' => 'required|array|min:1',
            'mapa_ids.*' => 'exists:mapa,id',
            'review_notes' => 'nullable|string|max:1000',
        ], [
            'mapa_ids.required' => 'Pilih minimal 1 MAPA untuk di-approve',
            'mapa_ids.*.exists' => 'MAPA tidak ditemukan'
        ]);

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
                        'notes' => $request->review_notes
                    ])
                    ->log("Bulk approved {$successCount} MAPA");
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil approve {$successCount} MAPA" .
                    ($failedCount > 0 ? ", {$failedCount} gagal" : ""),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk approving MAPA: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Gagal melakukan bulk approve: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk Reject MAPA
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'mapa_ids' => 'required|array|min:1',
            'mapa_ids.*' => 'exists:mapa,id',
            'review_notes' => 'required|string|max:1000',
        ], [
            'mapa_ids.required' => 'Pilih minimal 1 MAPA untuk di-reject',
            'mapa_ids.*.exists' => 'MAPA tidak ditemukan',
            'review_notes.required' => 'Catatan review wajib diisi untuk bulk reject'
        ]);

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
                        'notes' => $request->review_notes
                    ])
                    ->log("Bulk rejected {$successCount} MAPA");
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil reject {$successCount} MAPA" .
                    ($failedCount > 0 ? ", {$failedCount} gagal" : ""),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk rejecting MAPA: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Gagal melakukan bulk reject: ' . $e->getMessage(),
            ], 500);
        }
    }
}
