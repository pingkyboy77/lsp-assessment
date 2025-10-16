<?php

namespace App\Http\Controllers\Admin;

use App\Models\Apl02;
use Illuminate\Http\Request;
use App\Models\Apl01Pendaftaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DelegasiPersonilAsesmen;

class AplReviewController extends Controller
{

    /**
     * Review dan Approve APL 01
     */
    public function approveApl01(Request $request, Apl01Pendaftaran $apl): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $previousStatus = $apl->status;

            // Update status APL01
            $apl->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'completed_at' => now(),
                'notes' => $request->notes,
            ]);

            // Log activity if using spatie/laravel-activitylog
            if (function_exists('activity')) {
                activity('apl01')
                    ->performedOn($apl)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'notes' => $request->notes,
                        'previous_status' => $previousStatus,
                        'new_status' => 'approved',
                        'action' => 'approved_by_admin',
                    ])
                    ->log('APL 01 approved and completed by admin');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'APL 01 berhasil disetujui dan completed.',
                'data' => [
                    'status' => $apl->status,
                    'completed_at' => $apl->completed_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
     * Reject APL 01 dan buka kembali untuk editing
     */
    public function rejectApl01(Request $request, Apl01Pendaftaran $apl): JsonResponse
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $previousStatus = $apl->status;

            // Set status ke 'open' dan clear completed_at
            $apl->update([
                'status' => 'open',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'completed_at' => null,
                'notes' => $request->notes,
            ]);

            // Log activity
            if (function_exists('activity')) {
                activity('apl01')
                    ->performedOn($apl)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'notes' => $request->notes,
                        'previous_status' => $previousStatus,
                        'new_status' => 'open',
                        'action' => 'rejected_reopened',
                    ])
                    ->log('APL 01 rejected and reopened for editing');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'APL 01 ditolak dan dibuka kembali untuk perbaikan.',
                'data' => [
                    'status' => $apl->status,
                    'notes' => $apl->notes,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
     * Review dan Approve APL 02
     */
    public function approveApl02(Request $request, Apl02 $apl02): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $previousStatus = $apl02->status;

            // Update status APL02
            $apl02->update([
                'status' => 'approved',
                'reviewer_id' => Auth::id(),
                'reviewed_at' => now(),
                'completed_at' => now(),
                'reviewer_notes' => $request->notes,
            ]);

            // Log activity
            if (function_exists('activity')) {
                activity('apl02')
                    ->performedOn($apl02)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'notes' => $request->notes,
                        'previous_status' => $previousStatus,
                        'new_status' => 'approved',
                        'action' => 'approved_by_admin',
                    ])
                    ->log('APL 02 approved and completed by admin');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'APL 02 berhasil disetujui dan completed.',
                'data' => [
                    'status' => $apl02->status,
                    'completed_at' => $apl02->completed_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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

    /**
     * Reject APL 02 dan buka kembali
     */
    public function rejectApl02(Request $request, Apl02 $apl02): JsonResponse
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $previousStatus = $apl02->status;

            // Set status ke 'open' dan clear completed_at
            $apl02->update([
                'status' => 'open',
                'reviewer_id' => Auth::id(),
                'reviewed_at' => now(),
                'completed_at' => null,
                'reviewer_notes' => $request->notes,
            ]);

            // Log activity
            if (function_exists('activity')) {
                activity('apl02')
                    ->performedOn($apl02)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'notes' => $request->notes,
                        'previous_status' => $previousStatus,
                        'new_status' => 'open',
                        'action' => 'rejected_reopened',
                    ])
                    ->log('APL 02 rejected and reopened for editing');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'APL 02 ditolak dan dibuka kembali untuk perbaikan.',
                'data' => [
                    'status' => $apl02->status,
                    'notes' => $apl02->reviewer_notes,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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

    
}
