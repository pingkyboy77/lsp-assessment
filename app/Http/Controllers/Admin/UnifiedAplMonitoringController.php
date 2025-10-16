<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apl01Pendaftaran;
use App\Models\Apl02;
use App\Models\Apl02EvidenceSubmission; // Model yang benar
use App\Models\UserDocument;
use App\Models\RequirementItem;
use App\Services\AplDataService;
use App\Services\AplReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UnifiedAplMonitoringController extends Controller
{
    protected AplDataService $aplDataService;
    protected AplReviewService $aplReviewService;

    public function __construct(AplDataService $aplDataService, AplReviewService $aplReviewService)
    {
        $this->aplDataService = $aplDataService;
        $this->aplReviewService = $aplReviewService;
    }

    public function index()
    {
        return view('admin.monitoring.unified-apl');
    }

    // METHOD UNTUK PREVIEW EVIDENCE
    public function previewEvidence($evidenceId)
    {
        try {
            // Cari evidence berdasarkan ID dengan model yang benar
            $evidence = Apl02EvidenceSubmission::findOrFail($evidenceId);

            // Cek apakah file exists menggunakan method dari model
            if (!$evidence->fileExists()) {
                abort(404, 'File tidak ditemukan');
            }

            // Get file info dari model
            $filePath = $evidence->file_path;
            $fileName = $evidence->file_name;
            $mimeType = $evidence->mime_type ?? 'application/octet-stream';

            // Cek apakah bisa dipreview di browser
            if ($evidence->canPreview()) {
                // Untuk PDF dan gambar, tampilkan di browser
                return response($evidence->getFileContents(), 200, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
            } else {
                // Selain PDF/gambar, langsung download
                return response($evidence->getFileContents(), 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Evidence Preview Error: ' . $e->getMessage(), [
                'evidence_id' => $evidenceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(404, 'File tidak dapat dipreview: ' . $e->getMessage());
        }
    }

    // TAMBAHKAN METHOD INI UNTUK DOWNLOAD EVIDENCE (optional)
    // METHOD UNTUK DOWNLOAD EVIDENCE (optional)
    public function downloadEvidence($evidenceId)
    {
        try {
            $evidence = Apl02EvidenceSubmission::findOrFail($evidenceId);

            if (!$evidence->fileExists()) {
                abort(404, 'File tidak ditemukan');
            }

            return response($evidence->getFileContents(), 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $evidence->file_name . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Evidence Download Error: ' . $e->getMessage(), [
                'evidence_id' => $evidenceId,
                'error' => $e->getMessage(),
            ]);
            abort(404, 'File tidak dapat didownload');
        }
    }

    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $aplType = $request->input('type', 'apl01');
            $stats = $this->aplDataService->getStatistics($aplType);

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Unified APL Statistics Error: ' . $e->getMessage());
            return response()->json($this->getEmptyStats(), 500);
        }
    }

    public function getData(Request $request): JsonResponse
    {
        try {
            $aplType = $request->input('apl_type', 'apl01');
            $data = $this->aplDataService->getDataTable($aplType, $request);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Unified APL Data Error: ' . $e->getMessage());
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

    // APL 01 Methods
    public function getApl01ReviewData(Apl01Pendaftaran $apl): JsonResponse
    {
        try {
            $data = $this->aplReviewService->getApl01ReviewData($apl);
            // dd($data);
            return response()->json(['success' => true, 'data' => $data]);
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

    public function approveApl01(Request $request, Apl01Pendaftaran $apl): JsonResponse
    {
        // $request->validate(['notes' => 'nullable|string|max:1000']);

        try {
            $previousStatus = $apl->status;

            $this->aplReviewService->approveApl01($apl, $request->notes, Auth::id());

            activity('apl01')
                ->performedOn($apl)
                ->causedBy(auth()->user() ?? null)
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl->status,
                ])
                ->log('APL 01 approved');

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil disetujui.',
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

    public function rejectApl01(Request $request, Apl01Pendaftaran $apl): JsonResponse
    {
        $request->validate(['notes' => 'required|string|max:1000']);

        try {
            $previousStatus = $apl->status;

            $this->aplReviewService->rejectApl01($apl, $request->notes, Auth::id());

            activity('apl01')
                ->performedOn($apl)
                ->causedBy(auth()->user() ?? null)
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl->status,
                ])
                ->log('APL 01 rejected');

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil ditolak.',
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

    public function reopenApl01(Request $request, Apl01Pendaftaran $apl): JsonResponse
    {
        $request->validate(['notes' => 'nullable|string|max:1000']);

        try {
            // Simpan status lama sebelum diubah
            $previousStatus = $apl->status;

            $this->aplReviewService->reopenApl01($apl, $request->notes, Auth::id());

            activity('apl01')
                ->performedOn($apl)
                ->causedBy(auth()->user() ?? null)
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl->status, // tambahkan biar jelas status barunya
                ])
                ->log('APL 01 reopened for editing');

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil dibuka kembali.',
            ]);
        } catch (\Exception $e) {
            Log::error('APL01 Reopen Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal membuka kembali APL 01: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // APL 02 Methods
    public function getApl02ReviewData($apl02Id): JsonResponse
    {
        try {
            $apl02 = Apl02::findOrFail($apl02Id);
            $data = $this->aplReviewService->getApl02ReviewData($apl02);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('APL02 Review Data Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memuat data APL 02: ' . $e->getMessage(),
                ],
                404,
            );
        }
    }

    public function approveApl02(Request $request, Apl02 $apl02): JsonResponse
    {
        $request->validate(['notes' => 'nullable|string|max:1000']);

        try {
            $previousStatus = $apl02->status;

            $this->aplReviewService->approveApl02($apl02, $request->notes, Auth::id());

            activity('apl02')
                ->performedOn($apl02)
                ->causedBy(auth()->user() ?? null)
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl02->status,
                ])
                ->log('APL 02 approved');

            return response()->json(['success' => true, 'message' => 'APL 02 berhasil disetujui.']);
        } catch (\Exception $e) {
            Log::error('APL02 Approve Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menyetujui APL 02: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function rejectApl02(Request $request, Apl02 $apl02): JsonResponse
    {
        $request->validate(['notes' => 'required|string|max:1000']);

        try {
            $previousStatus = $apl02->status;

            $this->aplReviewService->rejectApl02($apl02, $request->notes, Auth::id());

            activity('apl02')
                ->performedOn($apl02)
                ->causedBy(auth()->user() ?? null)
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl02->status,
                ])
                ->log('APL 02 rejected');

            return response()->json(['success' => true, 'message' => 'APL 02 berhasil ditolak.']);
        } catch (\Exception $e) {
            Log::error('APL02 Reject Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menolak APL 02: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function reopenApl02(Request $request, Apl02 $apl02): JsonResponse
    {
        $request->validate(['notes' => 'nullable|string|max:1000']);

        try {
            // Simpan status lama sebelum diubah
            $previousStatus = $apl02->status;

            $this->aplReviewService->reopenApl02($apl02, $request->notes, Auth::id());

            activity('apl02')
                ->performedOn($apl02)
                ->causedBy(auth()->user() ?? null)
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl02->status,
                ])
                ->log('APL 02 reopened for editing');

            return response()->json([
                'success' => true,
                'message' => 'APL 02 berhasil dibuka kembali.',
            ]);
        } catch (\Exception $e) {
            Log::error('APL02 Reopen Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal membuka kembali APL 02: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Bulk Actions
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'action' => 'required|in:approve,reject,reopen',
            'notes' => 'nullable|string|max:1000',
            'apl_type' => 'required|in:apl01,apl02',
        ]);

        if ($request->action === 'reject' && !$request->filled('notes')) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Catatan wajib diisi untuk aksi reject.',
                ],
                422,
            );
        }

        try {
            $result = $this->aplReviewService->processBulkAction($request->apl_type, $request->ids, $request->action, $request->notes, Auth::id());

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Bulk Action Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memproses bulk action: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    private function getEmptyStats(): array
    {
        return [
            'total' => 0,
            'draft' => 0,
            'submitted' => 0,
            'approved' => 0,
            'rejected' => 0,
            'open' => 0,
            'returned' => 0,
        ];
    }
}
