<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Ak07CeklisePenyesuaian;
use App\Models\Ak07History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AsesiAk07Controller extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $ak07List = Ak07CeklisePenyesuaian::whereHas('mapa.delegasi', function ($query) use ($user) {
            $query->where('asesi_id', $user->id);
        })
            ->with([
                'mapa.delegasi.asesi',
                'mapa.certificationScheme',
                'asesor'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('asesi.ak07.index', compact('ak07List'));
    }

    public function sign($id)
    {
        $ak07 = Ak07CeklisePenyesuaian::with([
            'mapa.delegasi.asesi',
            'mapa.certificationScheme',
            'mapa.delegasi.certificationScheme',
            'asesor'
        ])->findOrFail($id);

        // Validasi: hanya asesi yang bersangkutan yang bisa akses
        if ($ak07->mapa->delegasi->asesi_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Validasi: hanya bisa TTD jika status waiting_asesi
        if ($ak07->status !== 'waiting_asesi') {
            return redirect()->route('asesi.ak07.index')
                ->with('error', 'Dokumen ini tidak memerlukan tanda tangan Anda saat ini.');
        }

        return view('asesi.ak07.sign', compact('ak07'));
    }

    public function submitSignature(Request $request, $id)
    {
        $request->validate([
            'asesi_signature' => 'required|string',
            'asesi_tanggal_tanda_tangan' => 'required|date'
        ]);

        $ak07 = Ak07CeklisePenyesuaian::findOrFail($id);

        // Validasi asesi
        if ($ak07->mapa->delegasi->asesi_id !== auth()->id()) {
            abort(403);
        }

        // Validasi status
        if ($ak07->status !== 'waiting_asesi') {
            return back()->with('error', 'Dokumen ini tidak memerlukan tanda tangan Anda.');
        }

        DB::beginTransaction();
        try {
            // Simpan signature
            $signaturePath = $this->saveSignature($request->asesi_signature, 'asesi', $ak07->id);

            // Update AK07 - menggunakan kolom asesi_signed_at
            $ak07->update([
                'asesi_signature' => $signaturePath,
                'asesi_signed_at' => now(), // Otomatis gunakan waktu sekarang
                'status' => 'completed'
            ]);

            DB::commit();

            return redirect()->route('asesi.ak07.index')
                ->with('success', 'Tanda tangan berhasil disimpan. FR.AK.07 telah lengkap.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan tanda tangan: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $ak07 = Ak07CeklisePenyesuaian::with([
            'mapa.delegasi.asesi',
            'mapa.certificationScheme',
            'asesor',
        ])->findOrFail($id);

        // Validasi akses
        if ($ak07->mapa->delegasi->asesi_id !== auth()->id()) {
            abort(403);
        }

        return view('asesi.ak07.view', compact('ak07'));
    }

    private function saveSignature($base64Data, $type, $ak07Id)
    {
        // Remove data:image/png;base64, prefix
        $image = str_replace('data:image/png;base64,', '', $base64Data);
        $image = str_replace(' ', '+', $image);
        $imageName = $type . '_signature_' . $ak07Id . '_' . time() . '.png';

        $path = 'signatures/ak07/' . $imageName;
        Storage::disk('public')->put($path, base64_decode($image));

        return $path;
    }
}
