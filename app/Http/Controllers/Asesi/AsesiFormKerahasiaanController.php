<?php

namespace App\Http\Controllers\Asesi;

use Illuminate\Http\Request;
use App\Models\FormKerahasiaan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AsesiFormKerahasiaanController extends Controller
{

    /**
     * Show Form Kerahasiaan for Signing
     */
    public function sign($id)
    {
        $formKerahasiaan = FormKerahasiaan::with(['delegasi.asesi', 'delegasi.asesor'])
            ->findOrFail($id);

        // Check if belongs to current asesi
        if ($formKerahasiaan->delegasi->asesi_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if can be signed
        if (!$formKerahasiaan->canBeSignedByAsesi()) {
            return redirect()->back()->with('error', 'Form belum dapat ditandatangani');
        }

        return view('asesi.form-kerahasiaan.sign', compact('formKerahasiaan'));
    }

    /**
     * Store Asesi Signature
     */
    public function storeSignature(Request $request, $id)
    {
        $request->validate([
            'ttd_asesi' => 'required|string',
        ]);

        $formKerahasiaan = FormKerahasiaan::findOrFail($id);

        // Check ownership
        if ($formKerahasiaan->delegasi->asesi_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $formKerahasiaan->signByAsesi($request->ttd_asesi);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form Kerahasiaan berhasil ditandatangani. Konsultasi selesai!',
                'redirect' => route('asesi.ak07.index', $id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View Form Kerahasiaan
     */
    public function view($id)
    {
        $formKerahasiaan = FormKerahasiaan::with(['delegasi.asesi', 'delegasi.asesor'])
            ->findOrFail($id);

        // Check ownership
        if ($formKerahasiaan->delegasi->asesi_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        return view('asesi.form-kerahasiaan.view', compact('formKerahasiaan'));
    }

    /**
     * Show Form Banding (Embed Document Only)
     */
    public function showFormBanding($delegasiId)
    {
        $delegasi = \App\Models\DelegasiPersonilAsesmen::with(['asesi', 'certificationScheme'])
            ->findOrFail($delegasiId);

        // Check ownership
        if ($delegasi->asesi_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // URL ke dokumen banding
        $bandingDocumentUrl = asset('documents/FR_AK_04_BANDING_ASESMEN.pdf');

        return view('asesi.form-banding.show', compact('delegasi', 'bandingDocumentUrl'));
    }
}