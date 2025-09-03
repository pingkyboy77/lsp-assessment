<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\KelompokKerja;
use App\Models\BuktiPortofolio;
use App\Models\UnitKompetensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelompokKerjaController extends Controller
{
    public function index(CertificationScheme $scheme)
    {
        $kelompoks = $scheme
            ->kelompokKerjas()
            ->with(['buktiPortofolios'])
            ->ordered()
            ->paginate(20);

        return view('admin.kelompok-kerja.index', compact('scheme', 'kelompoks'));
    }

    public function create(CertificationScheme $scheme)
    {
        return view('admin.kelompok-kerja.create', compact('scheme'));
    }

    public function store(Request $request, CertificationScheme $scheme)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelompok' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $maxSort = $scheme->kelompokKerjas()->max('sort_order') ?? 0;

            $kelompok = $scheme->kelompokKerjas()->create([
                'nama_kelompok' => $request->nama_kelompok,
                'deskripsi' => $request->deskripsi,
                'sort_order' => $maxSort + 1,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompok])
                ->with('success', 'Kelompok kerja berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat kelompok kerja: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $kelompokKerja = $kelompokKerja->load([
            'buktiPortofolios' => function ($query) {
                $query->ordered();
            },
        ]);

        return view('admin.kelompok-kerja.show', compact('scheme', 'kelompokKerja'));
    }

    public function edit(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        return view('admin.kelompok-kerja.edit', compact('scheme', 'kelompokKerja'));
    }

    public function update(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelompok' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $kelompokKerja->update([
                'nama_kelompok' => $request->nama_kelompok,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompokKerja])
                ->with('success', 'Kelompok kerja berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui kelompok kerja: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        DB::beginTransaction();
        try {
            $kelompokKerja->delete();
            DB::commit();

            return redirect()->route('admin.schemes.kelompok-kerja.index', $scheme)->with('success', 'Kelompok kerja berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus kelompok kerja: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request, CertificationScheme $scheme)
    {
        $validator = Validator::make($request->all(), [
            'kelompok_ids' => 'required|array',
            'kelompok_ids.*' => 'exists:kelompok_kerjas,id',
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
            $scheme->reorderKelompokKerja($request->kelompok_ids);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan kelompok kerja berhasil diperbarui.',
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

    public function toggleStatus(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        DB::beginTransaction();
        try {
            $kelompokKerja->update([
                'is_active' => !$kelompokKerja->is_active,
            ]);

            DB::commit();

            $status = $kelompokKerja->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => "Kelompok kerja berhasil {$status}.",
                'is_active' => $kelompokKerja->is_active,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengubah status: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Show the manage unit kompetensi page
     */
    public function manageUnitKompetensi(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        // Pastikan kelompok kerja belongs to scheme
        if ($kelompokKerja->certification_scheme_id !== $scheme->id) {
            abort(404, 'Kelompok kerja tidak ditemukan dalam skema ini.');
        }

        // Get units already assigned to this kelompok kerja dengan pivot data
        $assignedUnits = $kelompokKerja
            ->unitKompetensis()
            ->withPivot(['sort_order', 'is_active', 'created_at', 'updated_at'])
            ->with(['elemenKompetensis.kriteriaKerjas'])
            ->get();

        // Get all available units for this scheme that are not assigned to this kelompok kerja
        $assignedUnitIds = $assignedUnits->pluck('id')->toArray();

        $availableUnits = $scheme
            ->unitKompetensis()
            ->whereNotIn('id', $assignedUnitIds)
            ->with(['elemenKompetensis.kriteriaKerjas'])
            ->orderBy('kode_unit')
            ->get();

        return view('admin.kelompok-kerja.manage-unit-kompetensi', compact('scheme', 'kelompokKerja', 'assignedUnits', 'availableUnits'));
    }

    /**
     * Update unit kompetensi assignments for a kelompok kerja
     */
    public function updateUnitKompetensi(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        // Pastikan kelompok kerja belongs to scheme
        if ($kelompokKerja->certification_scheme_id !== $scheme->id) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Kelompok kerja tidak ditemukan dalam skema ini.',
                ],
                404,
            );
        }

        $validator = Validator::make($request->all(), [
            'unit_kompetensi_ids' => 'nullable|array', // <--- ubah dari required
            'unit_kompetensi_ids.*' => 'exists:unit_kompetensis,id',
            'changes' => 'array',
            'changes.*.action' => 'required|string|in:add,remove,update_sort,toggle_status',
            'changes.*.unit_id' => 'required|exists:unit_kompetensis,id',
            'changes.*.sort_order' => 'sometimes|integer|min:1',
            'changes.*.is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data tidak valid.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            // Validate that all units belong to the scheme
            $validUnitIds = $scheme->unitKompetensis()->pluck('id')->toArray();
            $invalidUnits = array_diff($request->unit_kompetensi_ids, $validUnitIds);

            if (!empty($invalidUnits)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Beberapa unit kompetensi tidak valid untuk skema ini.',
                    ],
                    422,
                );
            }

            // Get current assignments
            $currentAssignments = $kelompokKerja->unitKompetensis()->pluck('unit_kompetensis.id')->toArray();

            // Detach units that are no longer in the list
            $unitsToRemove = array_diff($currentAssignments, $request->unit_kompetensi_ids);
            if (!empty($unitsToRemove)) {
                $kelompokKerja->unitKompetensis()->detach($unitsToRemove);
            }

            // Process each unit in the new order
            foreach ($request->unit_kompetensi_ids as $index => $unitId) {
                $sortOrder = $index + 1;

                // Check if unit is already attached
                if (in_array($unitId, $currentAssignments)) {
                    // Update existing assignment
                    $kelompokKerja->unitKompetensis()->updateExistingPivot($unitId, [
                        'sort_order' => $sortOrder,
                        'updated_at' => now(),
                    ]);
                } else {
                    // Attach new unit
                    $kelompokKerja->unitKompetensis()->attach($unitId, [
                        'sort_order' => $sortOrder,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Process individual changes if provided
            if ($request->has('changes')) {
                foreach ($request->changes as $change) {
                    $unitId = $change['unit_id'];

                    switch ($change['action']) {
                        case 'toggle_status':
                            if (isset($change['is_active'])) {
                                $kelompokKerja->unitKompetensis()->updateExistingPivot($unitId, [
                                    'is_active' => $change['is_active'],
                                    'updated_at' => now(),
                                ]);
                            }
                            break;

                        case 'update_sort':
                            if (isset($change['sort_order'])) {
                                $kelompokKerja->unitKompetensis()->updateExistingPivot($unitId, [
                                    'sort_order' => $change['sort_order'],
                                    'updated_at' => now(),
                                ]);
                            }
                            break;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit kompetensi berhasil diperbarui.',
                'data' => [
                    'assigned_count' => count($request->unit_kompetensi_ids),
                    'removed_count' => count($unitsToRemove),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating unit kompetensi: ' . $e->getMessage(), [
                'scheme_id' => $scheme->id,
                'kelompok_kerja_id' => $kelompokKerja->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memperbarui unit kompetensi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Add a single unit kompetensi to kelompok kerja
     */
    public function addUnitKompetensi(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'unit_kompetensi_id' => 'required|exists:unit_kompetensis,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Unit kompetensi tidak valid.',
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            $unitId = $request->unit_kompetensi_id;

            // Validate unit belongs to the scheme
            if (!$scheme->unitKompetensis()->where('id', $unitId)->exists()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Unit kompetensi tidak termasuk dalam skema ini.',
                    ],
                    422,
                );
            }

            // Check if already assigned
            if ($kelompokKerja->unitKompetensis()->where('unit_kompetensi_id', $unitId)->exists()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Unit kompetensi sudah terdaftar dalam kelompok kerja ini.',
                    ],
                    422,
                );
            }

            // Get next sort order
            $maxSort = $kelompokKerja->unitKompetensis()->max('kelompok_kerja_unit_kompetensi.sort_order') ?? 0;

            // Attach unit
            $kelompokKerja->unitKompetensis()->attach($unitId, [
                'sort_order' => $maxSort + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit kompetensi berhasil ditambahkan ke kelompok kerja.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menambahkan unit kompetensi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove a single unit kompetensi from kelompok kerja
     */
    public function removeUnitKompetensi(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'unit_kompetensi_id' => 'required|exists:unit_kompetensis,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Unit kompetensi tidak valid.',
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            $unitId = $request->unit_kompetensi_id;

            // Check if unit is assigned
            if (!$kelompokKerja->unitKompetensis()->where('unit_kompetensi_id', $unitId)->exists()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Unit kompetensi tidak terdaftar dalam kelompok kerja ini.',
                    ],
                    422,
                );
            }

            // Detach unit
            $kelompokKerja->unitKompetensis()->detach($unitId);

            // Reorder remaining units
            $remainingUnits = $kelompokKerja->unitKompetensis()->orderBy('kelompok_kerja_unit_kompetensi.sort_order')->get();

            foreach ($remainingUnits as $index => $unit) {
                $kelompokKerja->unitKompetensis()->updateExistingPivot($unit->id, [
                    'sort_order' => $index + 1,
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit kompetensi berhasil dihapus dari kelompok kerja.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menghapus unit kompetensi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Toggle status of unit kompetensi in kelompok kerja
     */
    public function toggleUnitStatus(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'unit_kompetensi_id' => 'required|exists:unit_kompetensis,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Unit kompetensi tidak valid.',
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            $unitId = $request->unit_kompetensi_id;

            // Check if unit is assigned
            $pivot = $kelompokKerja->unitKompetensis()->where('unit_kompetensi_id', $unitId)->first();

            if (!$pivot) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Unit kompetensi tidak terdaftar dalam kelompok kerja ini.',
                    ],
                    422,
                );
            }

            // Toggle status
            $newStatus = !$pivot->pivot->is_active;
            $kelompokKerja->unitKompetensis()->updateExistingPivot($unitId, [
                'is_active' => $newStatus,
                'updated_at' => now(),
            ]);

            DB::commit();

            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => "Unit kompetensi berhasil {$statusText}.",
                'is_active' => $newStatus,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengubah status unit kompetensi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Reorder unit kompetensi within kelompok kerja
     */
    public function reorderUnitKompetensi(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'unit_kompetensi_ids' => 'required|array',
            'unit_kompetensi_ids.*' => 'exists:unit_kompetensis,id',
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
            $unitIds = $request->unit_kompetensi_ids;

            // Validate all units are assigned to this kelompok kerja
            $assignedUnits = $kelompokKerja->unitKompetensis()->pluck('unit_kompetensis.id')->toArray();
            $invalidUnits = array_diff($unitIds, $assignedUnits);

            if (!empty($invalidUnits)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Beberapa unit kompetensi tidak terdaftar dalam kelompok kerja ini.',
                    ],
                    422,
                );
            }

            // Update sort order for each unit
            foreach ($unitIds as $index => $unitId) {
                $kelompokKerja->unitKompetensis()->updateExistingPivot($unitId, [
                    'sort_order' => $index + 1,
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan unit kompetensi berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memperbarui urutan unit kompetensi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Bulk toggle status for all units in kelompok kerja
     */
    public function bulkToggleUnitStatus(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
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
            $isActive = $request->boolean('is_active');

            // Update all assigned units
            $kelompokKerja->unitKompetensis()->update([
                'kelompok_kerja_unit_kompetensi.is_active' => $isActive,
                'kelompok_kerja_unit_kompetensi.updated_at' => now(),
            ]);

            DB::commit();

            $statusText = $isActive ? 'diaktifkan' : 'dinonaktifkan';
            $count = $kelompokKerja->unitKompetensis()->count();

            return response()->json([
                'success' => true,
                'message' => "{$count} unit kompetensi berhasil {$statusText}.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengubah status unit kompetensi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }


//     public function removeUnitKompetensi(Request $request, KelompokKerja $kelompokKerja)
// {
//     $request->validate([
//         'unit_id' => 'required|exists:unit_kompetensis,id',
//     ]);

//     $unitId = $request->input('unit_id');

//     try {
//         // Hapus pivot (detach)
//         $kelompokKerja->unitKompetensis()->detach($unitId);

//         if ($request->ajax()) {
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Unit kompetensi berhasil dilepas dari kelompok kerja.'
//             ]);
//         }

//         return redirect()->back()->with('success', 'Unit kompetensi berhasil dilepas dari kelompok kerja.');
//     } catch (\Exception $e) {
//         if ($request->ajax()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Terjadi kesalahan: ' . $e->getMessage()
//             ]);
//         }

//         return redirect()->back()->with('error', 'Terjadi kesalahan saat melepas unit kompetensi.');
//     }
// }
}
