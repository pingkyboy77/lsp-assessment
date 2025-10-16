<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Apl01Pendaftaran;
use App\Models\TukRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TukRequestController extends Controller
{
    /**
     * Show TUK form for specific APL01 - CREATE OR SHOW
     */
    public function show(Apl01Pendaftaran $apl01)
    {
        // Check if user owns this APL01
        if ($apl01->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to APL01');
        }

        // Check if APL01 status is approved (prerequisite for TUK request)
        if ($apl01->status !== 'approved') {
            return redirect()->back()->with('error', 'APL01 harus disetujui terlebih dahulu sebelum dapat mengajukan TUK.');
        }

        // Check if APL01 requires TUK sewaktu (optional check based on your business logic)
        // Uncomment if you have this method in your APL01 model
        // if (!$apl01->isTukSewaktu()) {
        //     return redirect()->back()->with('error', 'APL01 ini tidak memerlukan form TUK Sewaktu.');
        // }

        // Get existing TUK request or prepare for new one
        $tukRequest = TukRequest::where('apl01_id', $apl01->id)->first();

        // Determine if this is create or edit mode
        $isEdit = $tukRequest !== null;
        $pageTitle = $isEdit ? 'Edit Permohonan Verifikasi Tempat Uji Kompetensi (TUK) SEWAKTU Jarah Jauh' : 'Permohonan Verifikasi Tempat Uji Kompetensi (TUK) SEWAKTU Jarah Jauh';

        return view('asesi.tuk-request.form', compact('apl01', 'tukRequest', 'isEdit', 'pageTitle'));
    }

    /**
     * Store or update TUK request
     */
    public function store(Request $request, Apl01Pendaftaran $apl01)
    {
        // Validate ownership
        if ($apl01->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to APL01');
        }

        // Check if APL01 status is approved
        if ($apl01->status !== 'approved') {
            return response()->json(['success' => false, 'error' => 'APL01 harus disetujui terlebih dahulu.'], 400);
        }

        // Validate request data
        $validated = $request->validate([
            'tanggal_assessment' => 'required|date|after:today',
            'lokasi_assessment' => 'required|string|max:1000',
            'tanda_tangan_peserta' => 'required|string', // Base64 signature
        ]);

        try {
            DB::beginTransaction();

            // Check if TUK request already exists
            $tukRequest = TukRequest::where('apl01_id', $apl01->id)->first();

            if ($tukRequest) {
                // Update existing request
                $tukRequest->update([
                    'tanggal_assessment' => $validated['tanggal_assessment'],
                    'lokasi_assessment' => $validated['lokasi_assessment'],
                ]);
                $isNew = false;
            } else {
                // Create new TUK request
                $tukRequest = TukRequest::create([
                    'apl01_id' => $apl01->id,
                    'user_id' => Auth::id(),
                    'tanggal_assessment' => $validated['tanggal_assessment'],
                    'lokasi_assessment' => $validated['lokasi_assessment'],
                ]);
                $isNew = true;
            }

            // Store signature
            if ($validated['tanda_tangan_peserta']) {
                $tukRequest->signByPeserta($validated['tanda_tangan_peserta']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isNew ? 'Permohonan TUK berhasil dibuat!' : 'Permohonan TUK berhasil diperbarui!',
                'data' => [
                    'kode_tuk' => $tukRequest->kode_tuk,
                    'is_complete' => $tukRequest->isComplete(),
                    'is_new' => $isNew,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving TUK request: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal menyimpan permohonan TUK: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Show TUK Mandiri PDF
     */
    public function showMandiriPdf(Apl01Pendaftaran $apl01)
    {
        // Check ownership
        if ($apl01->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to APL01');
        }

        // Check if APL01 is TUK Mandiri (optional based on your business logic)
        // if (!$apl01->isTukMandiri()) {
        //     return redirect()->back()->with('error', 'APL01 ini bukan TUK Mandiri.');
        // }

        return view('asesi.tuk-request.mandiri-pdf', compact('apl01'));
    }

    /**
     * Check TUK form status (AJAX)
     */
    public function checkStatus(Apl01Pendaftaran $apl01)
    {
        if ($apl01->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tukRequest = TukRequest::where('apl01_id', $apl01->id)->first();

        return response()->json([
            'has_tuk_request' => $tukRequest !== null,
            'form_completed' => $tukRequest ? $tukRequest->isComplete() : false,
            'has_recommendation' => $tukRequest ? $tukRequest->hasRecommendation() : false,
            'kode_tuk' => $tukRequest?->kode_tuk,
            'status' => $tukRequest ? 'exists' : 'not_created',
        ]);
    }
}
