<?php

namespace App\Http\Controllers\LembagaPelatihan;

use App\Http\Controllers\Controller;
use App\Models\Apl01Pendaftaran;
use App\Models\Apl02;
use App\Models\Apl02EvidenceSubmission;
use App\Services\LembagaAplDataService;
use App\Services\AplReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class LembagaAplMonitoringController extends Controller
{
    protected LembagaAplDataService $aplDataService;
    protected AplReviewService $aplReviewService;

    public function __construct(LembagaAplDataService $aplDataService, AplReviewService $aplReviewService)
    {
        $this->aplDataService = $aplDataService;
        $this->aplReviewService = $aplReviewService;
    }

    public function index()
    {
        return view('lembaga-pelatihan.monitoring.unified-apl');
    }

    public function previewEvidence($evidenceId)
    {
        try {
            $evidence = Apl02EvidenceSubmission::findOrFail($evidenceId);

            $lembagaId = Auth::user()->company;
            $apl02 = $evidence->apl02()->with('apl01')->first();

            if (!$apl02 || $apl02->apl01->training_provider != $lembagaId) {
                abort(403, 'Unauthorized access');
            }

            if (!$evidence->fileExists()) {
                abort(404, 'File tidak ditemukan');
            }

            $filePath = $evidence->file_path;
            $fileName = $evidence->file_name;
            $mimeType = $evidence->mime_type ?? 'application/octet-stream';

            if ($evidence->canPreview()) {
                return response($evidence->getFileContents(), 200, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
            } else {
                return response($evidence->getFileContents(), 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Evidence Preview Error: ' . $e->getMessage(), [
                'evidence_id' => $evidenceId,
                'user_id' => Auth::id(),
            ]);
            abort(404, 'File tidak dapat dipreview');
        }
    }

    public function downloadEvidence($evidenceId)
    {
        try {
            $evidence = Apl02EvidenceSubmission::findOrFail($evidenceId);

            // Validasi bahwa evidence ini milik peserta dari lembaga ini
            $lembagaId = Auth::user()->company;
            $apl02 = $evidence->apl02()->with('apl01.user')->first();

            if (!$apl02 || $apl02->apl01->user->company != $lembagaId) {
                abort(403, 'Unauthorized access');
            }

            if (!$evidence->fileExists()) {
                abort(404, 'File tidak ditemukan');
            }

            return response($evidence->getFileContents(), 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $evidence->file_name . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Evidence Download Error: ' . $e->getMessage());
            abort(404, 'File tidak dapat didownload');
        }
    }

    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $lembagaId = Auth::user()->company;
            $aplType = $request->input('type', 'apl01');
            $stats = $this->aplDataService->getStatistics($aplType, $lembagaId);

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Lembaga APL Statistics Error: ' . $e->getMessage());
            return response()->json($this->getEmptyStats(), 500);
        }
    }

    public function getData(Request $request): JsonResponse
    {
        try {
            $lembagaId = Auth::user()->company;
            $aplType = $request->input('apl_type', 'apl01');
            $data = $this->aplDataService->getDataTable($aplType, $lembagaId, $request);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Lembaga APL Data Error: ' . $e->getMessage());
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

    public function getApl01ReviewData(Apl01Pendaftaran $apl): JsonResponse
    {
        try {
            $lembagaId = Auth::user()->company;

            // Konversi ke string untuk perbandingan
            if (strval($apl->training_provider) != strval($lembagaId)) {
                \Log::warning('Unauthorized APL01 Access Attempt', [
                    'user_id' => Auth::id(),
                    'user_company' => $lembagaId,
                    'user_company_type' => gettype($lembagaId),
                    'apl_training_provider' => $apl->training_provider,
                    'apl_training_provider_type' => gettype($apl->training_provider),
                    'apl_id' => $apl->id,
                ]);

                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Unauthorized access',
                    ],
                    403,
                );
            }

            $data = $this->aplReviewService->getApl01ReviewData($apl);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            \Log::error('APL01 Review Data Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memuat data review: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getApl02ReviewData($apl02Id): JsonResponse
    {
        try {
            $apl02 = Apl02::findOrFail($apl02Id);
            $lembagaId = Auth::user()->company;

            // Log untuk debug
            \Log::info('APL02 Access Check', [
                'apl02_id' => $apl02->id,
                'training_provider' => $apl02->apl01->training_provider,
                'user_company' => $lembagaId,
                'match' => $apl02->apl01->training_provider == $lembagaId,
            ]);

            if (strval($apl02->apl01->training_provider) != strval($lembagaId)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Unauthorized access',
                    ],
                    403,
                );
            }

            $data = $this->aplReviewService->getApl02ReviewData($apl02);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('APL02 Review Data Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memuat data APL 02: ' . $e->getMessage(),
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


    public function approveApl01(Request $request, Apl01Pendaftaran $apl): JsonResponse
    {
        try {
            $lembagaId = Auth::user()->company;

            if (strval($apl->training_provider) != strval($lembagaId)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $previousStatus = $apl->status;
            $this->aplReviewService->approveApl01($apl, $request->notes, Auth::id());

            activity('apl01')
                ->performedOn($apl)
                ->causedBy(auth()->user())
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl->status,
                    'approved_by_lembaga' => $lembagaId
                ])
                ->log('APL 01 approved by Lembaga Pelatihan');

            return response()->json(['success' => true, 'message' => 'APL 01 berhasil disetujui.']);
        } catch (\Exception $e) {
            Log::error('Lembaga APL01 Approve Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui: ' . $e->getMessage()], 500);
        }
    }

    public function rejectApl01(Request $request, Apl01Pendaftaran $apl): JsonResponse
    {
        $request->validate(['notes' => 'required|string|max:1000']);

        try {
            $lembagaId = Auth::user()->company;

            if (strval($apl->training_provider) != strval($lembagaId)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $previousStatus = $apl->status;
            $this->aplReviewService->rejectApl01($apl, $request->notes, Auth::id());

            activity('apl01')
                ->performedOn($apl)
                ->causedBy(auth()->user())
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl->status,
                    'rejected_by_lembaga' => $lembagaId
                ])
                ->log('APL 01 rejected by Lembaga Pelatihan');

            return response()->json(['success' => true, 'message' => 'APL 01 berhasil ditolak.']);
        } catch (\Exception $e) {
            Log::error('Lembaga APL01 Reject Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menolak: ' . $e->getMessage()], 500);
        }
    }

    public function reopenApl01(Request $request, Apl01Pendaftaran $apl): JsonResponse
    {
        $request->validate(['notes' => 'nullable|string|max:1000']);

        try {
            $lembagaId = Auth::user()->company;

            if (strval($apl->training_provider) != strval($lembagaId)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $previousStatus = $apl->status;
            $this->aplReviewService->reopenApl01($apl, $request->notes, Auth::id());

            activity('apl01')
                ->performedOn($apl)
                ->causedBy(auth()->user())
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl->status,
                    'reopened_by_lembaga' => $lembagaId
                ])
                ->log('APL 01 reopened by Lembaga Pelatihan');

            return response()->json(['success' => true, 'message' => 'APL 01 berhasil dibuka kembali.']);
        } catch (\Exception $e) {
            Log::error('Lembaga APL01 Reopen Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal reopen: ' . $e->getMessage()], 500);
        }
    }

    // APL02 METHODS
    public function approveApl02(Request $request, Apl02 $apl02): JsonResponse
    {
        try {
            $lembagaId = Auth::user()->company;

            if (strval($apl02->apl01->training_provider) != strval($lembagaId)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $previousStatus = $apl02->status;
            $this->aplReviewService->approveApl02($apl02, $request->notes, Auth::id());

            activity('apl02')
                ->performedOn($apl02)
                ->causedBy(auth()->user())
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl02->status,
                    'approved_by_lembaga' => $lembagaId
                ])
                ->log('APL 02 approved by Lembaga Pelatihan');

            return response()->json(['success' => true, 'message' => 'APL 02 berhasil disetujui.']);
        } catch (\Exception $e) {
            Log::error('Lembaga APL02 Approve Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui: ' . $e->getMessage()], 500);
        }
    }

    public function rejectApl02(Request $request, Apl02 $apl02): JsonResponse
    {
        $request->validate(['notes' => 'required|string|max:1000']);

        try {
            $lembagaId = Auth::user()->company;

            if (strval($apl02->apl01->training_provider) != strval($lembagaId)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $previousStatus = $apl02->status;
            $this->aplReviewService->rejectApl02($apl02, $request->notes, Auth::id());

            activity('apl02')
                ->performedOn($apl02)
                ->causedBy(auth()->user())
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl02->status,
                    'rejected_by_lembaga' => $lembagaId
                ])
                ->log('APL 02 rejected by Lembaga Pelatihan');

            return response()->json(['success' => true, 'message' => 'APL 02 berhasil ditolak.']);
        } catch (\Exception $e) {
            Log::error('Lembaga APL02 Reject Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menolak: ' . $e->getMessage()], 500);
        }
    }

    public function reopenApl02(Request $request, Apl02 $apl02): JsonResponse
    {
        $request->validate(['notes' => 'nullable|string|max:1000']);

        try {
            $lembagaId = Auth::user()->company;

            if (strval($apl02->apl01->training_provider) != strval($lembagaId)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $previousStatus = $apl02->status;
            $this->aplReviewService->reopenApl02($apl02, $request->notes, Auth::id());

            activity('apl02')
                ->performedOn($apl02)
                ->causedBy(auth()->user())
                ->withProperties([
                    'notes' => $request->notes,
                    'previous_status' => $previousStatus,
                    'new_status' => $apl02->status,
                    'reopened_by_lembaga' => $lembagaId
                ])
                ->log('APL 02 reopened by Lembaga Pelatihan');

            return response()->json(['success' => true, 'message' => 'APL 02 berhasil dibuka kembali.']);
        } catch (\Exception $e) {
            Log::error('Lembaga APL02 Reopen Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal reopen: ' . $e->getMessage()], 500);
        }
    }
}
