<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\UnitKompetensi;
use App\Models\ElemenKompetensi;
use App\Models\KriteriaKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ElemenKompetensiController extends Controller
{
    public function store(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        $request->validate([
            'kode_elemen' => 'required|string|max:50',
            'judul_elemen' => 'required|string|max:1000',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $maxSort = $unitKompetensi->elemenKompetensis()->max('sort_order') ?? 0;
            $elemen = ElemenKompetensi::create([
                'unit_kompetensi_id' => $unitKompetensi->id,
                'kode_elemen' => $request->kode_elemen,
                'judul_elemen' => $request->judul_elemen,
                'sort_order' => $maxSort + 1,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            // Return JSON for AJAX requests or redirect for regular form submissions
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Elemen kompetensi berhasil ditambahkan.',
                    'elemen' => $elemen,
                ]);
            }

            return redirect()
                ->route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi])
                ->with('success', 'Elemen kompetensi berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Gagal menambahkan elemen: ' . $e->getMessage(),
                    ],
                    500,
                );
            }

            return back()->with('error', 'Gagal menambahkan elemen: ' . $e->getMessage());
        }
    }

    public function update(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi, ElemenKompetensi $elemen)
    {
        $request->validate([
            'kode_elemen' => 'required|string|max:50',
            'judul_elemen' => 'required|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Add method spoofing for PUT requests
        $request->merge(['_method' => 'PUT']);

        DB::beginTransaction();
        try {
            $elemen->update([
                'kode_elemen' => $request->kode_elemen,
                'judul_elemen' => $request->judul_elemen,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Elemen kompetensi berhasil diperbarui.',
                    'elemen' => $elemen->fresh(),
                ]);
            }

            return redirect()
                ->route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi])
                ->with('success', 'Elemen kompetensi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Gagal memperbarui elemen: ' . $e->getMessage(),
                    ],
                    500,
                );
            }

            return back()->with('error', 'Gagal memperbarui elemen: ' . $e->getMessage());
        }
    }

    public function destroy(CertificationScheme $scheme, UnitKompetensi $unitKompetensi, ElemenKompetensi $elemen)
    {
        DB::beginTransaction();
        try {
            $elemen->delete();
            DB::commit();

            return redirect()
                ->route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi])
                ->with('success', 'Elemen kompetensi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus elemen: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        $validator = Validator::make($request->all(), [
            'elemen_ids' => 'required|array',
            'elemen_ids.*' => 'exists:elemen_kompetensis,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data tidak valid.',
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            $unitKompetensi->reorderElemen($request->elemen_ids);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan elemen kompetensi berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memperbarui urutan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
