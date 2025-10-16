<?php

namespace App\Http\Controllers\Admin;

use App\Models\TukRequest;
use Illuminate\Http\Request;
use App\Models\Apl01Pendaftaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\DelegasiPersonilAsesmen;

class DelegasiPersonilAsesmenController extends Controller
{
    /**
     * Store new delegation
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asesi_id' => 'required|exists:users,id',
                'certification_scheme_id' => 'required|exists:certification_schemes,id',
                'apl01_id' => 'nullable|exists:apl_01_pendaftarans,id',
                'tuk_request_id' => 'nullable|exists:tuk_requests,id',
                'jenis_ujian' => 'required|in:online,offline',
                'verifikator_tuk_id' => 'required|exists:users,id',
                'verifikator_nik' => 'required|string',
                'verifikator_spt_date' => 'required|date',
                'observer_id' => 'required|exists:users,id',
                'observer_nik' => 'required|string',
                'observer_spt_date' => 'required|date',
                'asesor_id' => 'required|exists:users,id',
                'asesor_met' => 'required|string',
                'asesor_spt_date' => 'required|date',
                'tanggal_pelaksanaan_asesmen' => 'required|date',
                'waktu_mulai' => 'required',
                'notes' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Pastikan nullable fields tetap masuk ke data yang akan disimpan
            $dataToSave = array_merge($validated, [
                'apl01_id' => $request->input('apl01_id'), // Bisa null atau ada nilai
                'tuk_request_id' => $request->input('tuk_request_id'), // Bisa null atau ada nilai
                'delegated_by' => auth()->id(),
                'delegated_at' => now(),
            ]);

            // Create delegation
            $delegasi = DelegasiPersonilAsesmen::create($dataToSave);

            // Update status based on type
            if ($dataToSave['tuk_request_id']) {
                // TUK Sewaktu
                $tukRequest = TukRequest::findOrFail($dataToSave['tuk_request_id']);
                $tukRequest->update([
                    'status' => 'delegated',
                    'delegated_at' => now()
                ]);
            } elseif ($dataToSave['apl01_id']) {
                // TUK Mandiri
                $apl01 = Apl01Pendaftaran::findOrFail($dataToSave['apl01_id']);
                $apl01->update([
                    'status_delegasi' => 'delegated',
                    'delegated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delegasi personil berhasil disimpan!',
                'data' => $delegasi
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing delegation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal menyimpan delegasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show delegation detail
     */
    public function show($id)
    {
        try {
            $delegasi = DelegasiPersonilAsesmen::with([
                'asesi',
                'certificationScheme',
                'verifikatorTuk',
                'observer',
                'asesor',
                'apl01',
                'tukRequest'
            ])->findOrFail($id);

            $html = view('admin.tuk-requests.delegasi-detail', compact('delegasi'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => $delegasi
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing delegation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat detail delegasi'
            ], 500);
        }
    }

    /**
     * Update delegation
     */
    public function update(Request $request, $id)
    {
        try {
            $delegasi = DelegasiPersonilAsesmen::findOrFail($id);

            $validated = $request->validate([
                'jenis_ujian' => 'required|in:online,offline',
                'verifikator_tuk_id' => 'required|exists:users,id',
                'verifikator_nik' => 'required|string',
                'verifikator_spt_date' => 'required|date',
                'observer_id' => 'required|exists:users,id',
                'observer_nik' => 'required|string',
                'observer_spt_date' => 'required|date',
                'asesor_id' => 'required|exists:users,id',
                'asesor_met' => 'required|string',
                'asesor_spt_date' => 'required|date',
                'tanggal_pelaksanaan_asesmen' => 'required|date',
                'waktu_mulai' => 'required',
                'notes' => 'nullable|string'
            ]);

            DB::beginTransaction();

            $delegasi->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delegasi personil berhasil diupdate!',
                'data' => $delegasi
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating delegation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengupdate delegasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete delegation
     */
    public function destroy($id)
    {
        try {
            $delegasi = DelegasiPersonilAsesmen::findOrFail($id);

            DB::beginTransaction();

            // Reset status
            if ($delegasi->tuk_request_id) {
                $tukRequest = TukRequest::find($delegasi->tuk_request_id);
                if ($tukRequest) {
                    $tukRequest->update([
                        'status' => 'recommended',
                        'delegated_at' => null
                    ]);
                }
            } elseif ($delegasi->apl01_id) {
                $apl01 = Apl01Pendaftaran::find($delegasi->apl01_id);
                if ($apl01) {
                    $apl01->update([
                        'status_delegasi' => null,
                        'delegated_at' => null
                    ]);
                }
            }

            $delegasi->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delegasi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting delegation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus delegasi'
            ], 500);
        }
    }

    public function showView($id)
    {
        try {
            $delegasi = DelegasiPersonilAsesmen::with([
                'asesi',
                'certificationScheme',
                'verifikatorTuk',
                'observer',
                'asesor',
                'apl01',
                'tukRequest',
                'delegatedBy',
                'sptSignature'
            ])->findOrFail($id);

            return view('admin.tuk-requests.delegasi-form', compact('delegasi'));
        } catch (\Exception $e) {
            Log::error('Error showing delegation view: ' . $e->getMessage());
            return response()->make(
                '<div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Error:</strong> Gagal memuat detail delegasi: ' . $e->getMessage() . '
            </div>',
                500
            );
        }
    }
}
