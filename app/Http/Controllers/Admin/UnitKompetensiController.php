<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\UnitKompetensi;
use App\Models\KelompokKerja;
use App\Models\ElemenKompetensi;
use App\Models\KriteriaKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitKompetensiController extends Controller
{
    public function index(CertificationScheme $scheme)
    {
        $units = $scheme
            ->unitKompetensis()
            ->with(['elemenKompetensis.kriteriaKerjas', 'activeKelompokKerjas'])
            ->ordered()
            ->paginate(20);

        return view('admin.unit-kompetensi.index', compact('scheme', 'units'));
    }

    public function create(CertificationScheme $scheme)
    {
        $kelompokKerjas = $scheme->kelompokKerjas()->active()->ordered()->get();

        return view('admin.unit-kompetensi.create', compact('scheme', 'kelompokKerjas'));
    }

    public function store(Request $request, CertificationScheme $scheme)
    {
        $validator = Validator::make($request->all(), [
            'kode_unit' => ['required', 'string', 'max:50', 'unique:unit_kompetensis,kode_unit,NULL,id,certification_scheme_id,' . $scheme->id],
            'judul_unit' => 'required|string|max:500',
            'standar_kompetensi_kerja' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'kelompok_kerja_ids' => 'nullable|array',
            'kelompok_kerja_ids.*' => 'exists:kelompok_kerjas,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Get next sort order
            $maxSort = $scheme->unitKompetensis()->max('sort_order') ?? 0;

            $unit = $scheme->unitKompetensis()->create([
                'kode_unit' => $request->kode_unit,
                'judul_unit' => $request->judul_unit,
                'standar_kompetensi_kerja' => $request->standar_kompetensi_kerja,
                'sort_order' => $maxSort + 1,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Sync kelompok kerja jika ada
            if ($request->has('kelompok_kerja_ids')) {
                $unit->syncKelompokKerjas($request->kelompok_kerja_ids);
            }

            DB::commit();

            return redirect()
                ->route('admin.schemes.unit-kompetensi.show', [$scheme, $unit])
                ->with('success', 'Unit kompetensi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat unit kompetensi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        $unit = $unitKompetensi->load([
            'elemenKompetensis.kriteriaKerjas' => function ($query) {
                $query->ordered();
            },
            'activeKelompokKerjas',
        ]);

        return view('admin.unit-kompetensi.show', compact('scheme', 'unit'));
    }

    public function edit(CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        $kelompokKerjas = $scheme->kelompokKerjas()->active()->ordered()->get();
        $selectedKelompokKerjas = $unitKompetensi->kelompokKerjas()->pluck('kelompok_kerjas.id')->toArray();

        return view('admin.unit-kompetensi.edit', compact('scheme', 'unitKompetensi', 'kelompokKerjas', 'selectedKelompokKerjas'));
    }

    public function update(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        $validator = Validator::make($request->all(), [
            'kode_unit' => ['required', 'string', 'max:50', 'unique:unit_kompetensis,kode_unit,' . $unitKompetensi->id . ',id,certification_scheme_id,' . $scheme->id],
            'judul_unit' => 'required|string|max:500',
            'standar_kompetensi_kerja' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'kelompok_kerja_ids' => 'nullable|array',
            'kelompok_kerja_ids.*' => 'exists:kelompok_kerjas,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $unitKompetensi->update([
                'kode_unit' => $request->kode_unit,
                'judul_unit' => $request->judul_unit,
                'standar_kompetensi_kerja' => $request->standar_kompetensi_kerja,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Sync kelompok kerja
            if ($request->has('kelompok_kerja_ids')) {
                $unitKompetensi->syncKelompokKerjas($request->kelompok_kerja_ids);
            } else {
                $unitKompetensi->kelompokKerjas()->detach();
            }

            DB::commit();

            return redirect()
                ->route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi])
                ->with('success', 'Unit kompetensi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui unit kompetensi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        DB::beginTransaction();
        try {
            // Detach dari semua kelompok kerja terlebih dahulu
            $unitKompetensi->kelompokKerjas()->detach();

            $unitKompetensi->delete();
            DB::commit();

            return redirect()->route('admin.schemes.unit-kompetensi.index', $scheme)->with('success', 'Unit kompetensi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus unit kompetensi: ' . $e->getMessage());
        }
    }

    public function duplicateForm(CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        $schemes = CertificationScheme::active()->get();
        return view('admin.unit-kompetensi.duplicate', compact('scheme', 'unitKompetensi', 'schemes'));
    }

    public function duplicateStore(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        $validator = Validator::make($request->all(), [
            'kode_unit' => ['required', 'string', 'max:50', 'unique:unit_kompetensis,kode_unit,NULL,id,certification_scheme_id,' . $request->scheme_id],
            'scheme_id' => 'required|exists:certification_schemes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $targetScheme = CertificationScheme::findOrFail($request->scheme_id);

            // Ambil urutan terakhir di scheme tujuan
            $maxSort = $targetScheme->unitKompetensis()->max('sort_order') ?? 0;

            // Buat Unit baru dengan data lama + kode baru
            $newUnit = $targetScheme->unitKompetensis()->create([
                'kode_unit' => $request->kode_unit,
                'judul_unit' => $unitKompetensi->judul_unit,
                'standar_kompetensi_kerja' => $unitKompetensi->standar_kompetensi_kerja,
                'sort_order' => $maxSort + 1,
                'is_active' => true,
            ]);

            // Duplikasi Kelompok Kerja (pivot)
            $kelompokIds = $unitKompetensi->kelompokKerjas()->pluck('kelompok_kerjas.id');
            if ($kelompokIds->isNotEmpty()) {
                $newUnit->kelompokKerjas()->sync($kelompokIds);
            }

            foreach ($unitKompetensi->elemenKompetensis as $elemen) {
                $newElemen = $elemen->replicate();
                $newElemen->unit_kompetensi_id = $newUnit->id;
                $newElemen->created_at = now();
                $newElemen->updated_at = now();
                $newElemen->save();

                // Kalau ada relasi turunan (misal kriteria kerja)
                foreach ($elemen->kriteriaKerjas as $kriteria) {
                    $newKriteria = $kriteria->replicate();
                    $newKriteria->elemen_kompetensi_id = $newElemen->id;
                    $newKriteria->created_at = now();
                    $newKriteria->updated_at = now();
                    $newKriteria->save();
                }
            }

            DB::commit();

            return redirect()->route('admin.schemes.unit-kompetensi.index', $targetScheme->id)->with('success', 'Unit Kompetensi berhasil diduplikasi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal duplikasi: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, CertificationScheme $scheme, UnitKompetensi $unitKompetensi)
    {
        try {
            $unitKompetensi->is_active = !$unitKompetensi->is_active;
            $unitKompetensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Status Unit Kompetensi berhasil diperbarui.',
                'is_active' => $unitKompetensi->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengubah status unitKompetensi.',
                ],
                500,
            );
        }
    }

    public function reorder(Request $request, CertificationScheme $scheme)
    {
        $request->validate([
            'unit_ids' => 'required|array',
            'unit_ids.*' => 'exists:unit_kompetensis,id',
        ]);

        try {
            DB::transaction(function () use ($request, $scheme) {
                foreach ($request->unit_ids as $index => $id) {
                    UnitKompetensi::where('id', $id)
                        ->where('certification_scheme_id', $scheme->id) // pastikan FK bener
                        ->update([
                            'sort_order' => $index + 1,
                        ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Urutan unit berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Reorder gagal: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
