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

class KriteriaKerjaController extends Controller
{
    public function store(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi, ElemenKompetensi $elemen)
    {
        $validator = Validator::make($request->all(), [
            'kode_kriteria' => 'required|string|max:50',
            'uraian_kriteria' => 'required|string|max:2000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data tidak valid.',
                        'errors' => $validator->errors(),
                    ],
                    422,
                );
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $maxSort = $elemen->kriteriaKerjas()->max('sort_order') ?? 0;

            $kriteria = KriteriaKerja::create([
                'elemen_kompetensi_id' => $elemen->id,
                'kode_kriteria' => $request->kode_kriteria,
                'uraian_kriteria' => $request->uraian_kriteria,
                'sort_order' => $maxSort + 1,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kriteria kerja berhasil ditambahkan.',
                    'kriteria' => $kriteria
                ]);
            }

            return redirect()
                ->route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi])
                ->with('success', 'Kriteria kerja berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Gagal menambahkan kriteria kerja: ' . $e->getMessage(),
                    ],
                    500,
                );
            }
            
            return back()->with('error', 'Gagal menambahkan kriteria kerja: ' . $e->getMessage());
        }
    }

    public function update(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi, ElemenKompetensi $elemen, KriteriaKerja $kriteria)
    {
        $validator = Validator::make($request->all(), [
            'kode_kriteria' => 'required|string|max:50',
            'uraian_kriteria' => 'required|string|max:2000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data tidak valid.',
                        'errors' => $validator->errors(),
                    ],
                    422,
                );
            }
            return back()->withErrors($validator)->withInput();
        }

        // Add method spoofing for PUT requests
        $request->merge(['_method' => 'PUT']);

        DB::beginTransaction();
        try {
            $kriteria->update([
                'kode_kriteria' => $request->kode_kriteria,
                'uraian_kriteria' => $request->uraian_kriteria,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kriteria kerja berhasil diperbarui.',
                    'kriteria' => $kriteria->fresh()
                ]);
            }

            return redirect()
                ->route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi])
                ->with('success', 'Kriteria kerja berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Gagal memperbarui kriteria kerja: ' . $e->getMessage(),
                    ],
                    500,
                );
            }
            
            return back()->with('error', 'Gagal memperbarui kriteria kerja: ' . $e->getMessage());
        }
    }

    public function destroy(CertificationScheme $scheme, UnitKompetensi $unitKompetensi, ElemenKompetensi $elemen, KriteriaKerja $kriteria)
    {
        DB::beginTransaction();
        try {
            $kriteria->delete();
            DB::commit();

            return redirect()
                ->route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi])
                ->with('success', 'Kriteria kerja berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus kriteria kerja: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi, ElemenKompetensi $elemen)
    {
        $validator = Validator::make($request->all(), [
            'kriteria_ids' => 'required|array',
            'kriteria_ids.*' => 'exists:kriteria_kerjas,id',
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
            $elemen->reorderKriteria($request->kriteria_ids);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan kriteria kerja berhasil diperbarui.',
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