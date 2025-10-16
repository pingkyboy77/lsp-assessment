<?php

namespace App\Http\Controllers\Asesor;

use App\Models\FrAk07;
use Illuminate\Http\Request;
use App\Models\FormKerahasiaan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\DelegasiPersonilAsesmen;

class AsesorFormKerahasiaanController extends Controller
{
    /**
     * Create Form Kerahasiaan
     */
    public function create($delegasiId)
    {
        $delegasi = DelegasiPersonilAsesmen::with(['asesi', 'asesor', 'certificationScheme', 'mapa.ak07', 'tukRequest'])
        ->findOrFail($delegasiId);

        // Check if AK07 exists and has continue recommendation
        if (!$delegasi->mapa || !$delegasi->mapa->ak07) {
            return redirect()->back()->with('error', 'AK.07 belum tersedia');
        }

        if ($delegasi->mapa->ak07->final_recommendation !== 'continue') {
            return redirect()->back()->with('error', 'Final Recommendation harus Continue untuk membuat Form Kerahasiaan');
        }

        // Check if already exists
        if ($delegasi->formKerahasiaan) {
            return redirect()->route('asesor.form-kerahasiaan.edit', $delegasi->formKerahasiaan->id)
                ->with('info', 'Form Kerahasiaan sudah ada, silakan edit');
        }

        // Get jam_mulai from delegasi or tuk_request
        $jamMulai = $delegasi->waktu_mulai ?? $delegasi->tukRequest?->jam_mulai ?? '08:00';

        return view('asesor.form-kerahasiaan.create', compact('delegasi', 'jamMulai'));
    }

    /**
     * Store Form Kerahasiaan
     */
    public function store(Request $request, $delegasiId)
    {
        $request->validate([
            'tanggal_asesmen' => 'required|date',
            'jam_mulai' => 'required',
            'ttd_asesor' => 'required|string',
        ]);

        $delegasi = DelegasiPersonilAsesmen::with(['asesi', 'asesor', 'certificationScheme', 'mapa.ak07'])
            ->findOrFail($delegasiId);

        try {
            DB::beginTransaction();

            $formKerahasiaan = FormKerahasiaan::create([
                'delegasi_personil_asesmen_id' => $delegasi->id,
                'ak07_id' => $delegasi->mapa->ak07->id,
                'nama_asesi' => $delegasi->asesi->name,
                'nama_asesor' => $delegasi->asesor->name,
                'skema_sertifikasi' => $delegasi->certificationScheme->nama,
                'tanggal_asesmen' => $request->tanggal_asesmen,
                'jam_mulai' => $request->jam_mulai,
                'ttd_asesor' => $request->ttd_asesor,
                'tanggal_ttd_asesor' => now(),
                'status' => 'waiting_asesi',
            ]);

            // Sync jam_mulai to delegasi and tuk_request
            $formKerahasiaan->syncJamMulai();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form Kerahasiaan berhasil dibuat dan menunggu tanda tangan Asesi',
                'redirect' => route('asesor.mapa.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Form Kerahasiaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View Form Kerahasiaan
     */
    public function view($id)
    {
        $formKerahasiaan = FormKerahasiaan::with(['delegasi.asesi', 'delegasi.asesor', 'ak07'])
            ->findOrFail($id);

        return view('asesor.form-kerahasiaan.view', compact('formKerahasiaan'));
    }

    /**
     * Show Form Banding (Embed Document Only)
     */
    public function showFormBanding($delegasiId)
    {
        $delegasi = DelegasiPersonilAsesmen::with(['asesi', 'certificationScheme'])
            ->findOrFail($delegasiId);

        // URL ke dokumen banding (bisa dari storage atau external)
        $bandingDocumentUrl = asset('Banding/AK-04-BANDING.pdf');

        return view('asesor.form-banding.show', compact('delegasi', 'bandingDocumentUrl'));
    }
}
