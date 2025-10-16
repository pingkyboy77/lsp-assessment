<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\KelompokKerja;
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
            ->withCount(['unitKompetensis', 'activeUnitKompetensis'])
            ->ordered()
            ->paginate(20);

        return view('admin.kelompok-kerja.index', compact('scheme', 'kelompoks'));
    }

    public function create(CertificationScheme $scheme)
    {
        $potensiAsesiOptions = KelompokKerja::POTENSI_ASESI_OPTIONS;
        $usedPLevels = $scheme->kelompokKerjas()->whereNotNull('p_level')->pluck('p_level')->toArray();

        return view('admin.kelompok-kerja.create', compact('scheme', 'potensiAsesiOptions', 'usedPLevels'));
    }

    public function store(Request $request, CertificationScheme $scheme)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelompok' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'p_level' => 'nullable|integer|min:1|max:10',
            'potensi_asesi' => 'nullable|array',
            'potensi_asesi.*' => 'in:p1,p2,p3,p4,p5',
            'is_active' => 'boolean',
        ]);

        // Validasi custom: P Level harus unik per scheme
        $validator->after(function ($validator) use ($request, $scheme) {
            if ($request->p_level) {
                $exists = $scheme->kelompokKerjas()
                    ->where('p_level', $request->p_level)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('p_level', 'P Level ' . $request->p_level . ' sudah digunakan di kelompok kerja lain.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $maxSort = $scheme->kelompokKerjas()->max('sort_order') ?? 0;

            $kelompok = $scheme->kelompokKerjas()->create([
                'nama_kelompok' => $request->nama_kelompok,
                'deskripsi' => $request->deskripsi,
                'p_level' => $request->p_level,
                'potensi_asesi' => $request->potensi_asesi ?? [],
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
            'unitKompetensis' => function ($query) {
                $query->withPivot(['sort_order', 'is_active'])
                    ->with(['elemenKompetensis', 'portfolioFiles' => function ($query) {
                        $query->where('is_active', true);
                    }])
                    ->orderByPivot('sort_order');
            },
        ]);

        return view('admin.kelompok-kerja.show', compact('scheme', 'kelompokKerja'));
    }

    public function edit(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $potensiAsesiOptions = KelompokKerja::POTENSI_ASESI_OPTIONS;
        $usedPLevels = $scheme->kelompokKerjas()
            ->whereNotNull('p_level')
            ->where('id', '!=', $kelompokKerja->id)
            ->pluck('p_level')
            ->toArray();

        return view('admin.kelompok-kerja.edit', compact('scheme', 'kelompokKerja', 'potensiAsesiOptions', 'usedPLevels'));
    }

    public function update(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelompok' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'p_level' => 'nullable|integer|min:1|max:10',
            'potensi_asesi' => 'nullable|array',
            'potensi_asesi.*' => 'in:p1,p2,p3,p4,p5',
            'is_active' => 'boolean',
        ]);

        // Validasi custom: P Level harus unik per scheme (kecuali untuk kelompok ini sendiri)
        $validator->after(function ($validator) use ($request, $scheme, $kelompokKerja) {
            if ($request->p_level) {
                $exists = $scheme->kelompokKerjas()
                    ->where('p_level', $request->p_level)
                    ->where('id', '!=', $kelompokKerja->id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('p_level', 'P Level ' . $request->p_level . ' sudah digunakan di kelompok kerja lain.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $kelompokKerja->update([
                'nama_kelompok' => $request->nama_kelompok,
                'deskripsi' => $request->deskripsi,
                'p_level' => $request->p_level,
                'potensi_asesi' => $request->potensi_asesi ?? [],
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
            // Detach all unit kompetensi relationships
            $kelompokKerja->unitKompetensis()->detach();

            // Delete the kelompok kerja
            $kelompokKerja->delete();

            DB::commit();

            return redirect()->route('admin.schemes.kelompok-kerja.index', $scheme)
                ->with('success', 'Kelompok kerja berhasil dihapus.');
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
            foreach ($request->kelompok_ids as $index => $kelompokId) {
                $scheme->kelompokKerjas()
                    ->where('id', $kelompokId)
                    ->update([
                        'sort_order' => $index + 1,
                        'updated_at' => now(),
                    ]);
            }

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

    /* ===================== UNIT KOMPETENSI MANAGEMENT ===================== */

    /**
     * Show the manage unit kompetensi page
     */
    public function manageUnitKompetensi(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        // Ensure kelompok kerja belongs to scheme
        if ($kelompokKerja->certification_scheme_id !== $scheme->id) {
            abort(404, 'Kelompok kerja tidak ditemukan dalam skema ini.');
        }

        // Get units already assigned to this kelompok kerja with pivot data
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
        // Ensure kelompok kerja belongs to scheme
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
            'unit_kompetensi_ids' => 'nullable|array',
            'unit_kompetensi_ids.*' => 'exists:unit_kompetensis,id',
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
            $unitIds = $request->unit_kompetensi_ids ?? [];

            // Validate that all units belong to the scheme
            if (!empty($unitIds)) {
                $validUnitIds = $scheme->unitKompetensis()->pluck('id')->toArray();
                $invalidUnits = array_diff($unitIds, $validUnitIds);

                if (!empty($invalidUnits)) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Beberapa unit kompetensi tidak valid untuk skema ini.',
                        ],
                        422,
                    );
                }
            }

            // Get current assignments
            $currentAssignments = $kelompokKerja->unitKompetensis()->pluck('unit_kompetensis.id')->toArray();

            // Detach units that are no longer in the list
            $unitsToRemove = array_diff($currentAssignments, $unitIds);
            if (!empty($unitsToRemove)) {
                $kelompokKerja->unitKompetensis()->detach($unitsToRemove);
            }

            // Process each unit in the new order
            foreach ($unitIds as $index => $unitId) {
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit kompetensi berhasil diperbarui.',
                'data' => [
                    'assigned_count' => count($unitIds),
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

    /**
     * Duplicate kelompok kerja with its unit assignments
     */
    public function duplicate(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelompok' => 'required|string|max:200',
            'copy_units' => 'boolean',
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
            $copyUnits = $request->boolean('copy_units', true);
            $duplicate = $kelompokKerja->duplicate($request->nama_kelompok, $copyUnits);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kelompok kerja berhasil diduplikasi.',
                'data' => [
                    'id' => $duplicate->id,
                    'nama_kelompok' => $duplicate->nama_kelompok,
                    'units_copied' => $copyUnits ? $kelompokKerja->unitKompetensis->count() : 0,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menduplikasi kelompok kerja: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get units for API/AJAX requests
     */
    public function getUnits(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        try {
            $units = $kelompokKerja->unitKompetensis()
                ->withPivot(['sort_order', 'is_active'])
                ->withCount(['portfolioFiles' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderByPivot('sort_order')
                ->get()
                ->map(function ($unit) {
                    return [
                        'id' => $unit->id,
                        'kode_unit' => $unit->kode_unit,
                        'judul_unit' => $unit->judul_unit,
                        'is_active' => $unit->pivot->is_active,
                        'sort_order' => $unit->pivot->sort_order,
                        'portfolio_file_count' => $unit->portfolio_files_count ?? 0,
                        'elements_count' => $unit->elemenKompetensis ? $unit->elemenKompetensis->count() : 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $units,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengambil data unit kompetensi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Export kelompok kerja data
     */
    public function export(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        try {
            $kelompokKerja->loadWithUnits();

            $data = [
                'kelompok_info' => [
                    'id' => $kelompokKerja->id,
                    'nama_kelompok' => $kelompokKerja->nama_kelompok,
                    'deskripsi' => $kelompokKerja->deskripsi,
                    'is_active' => $kelompokKerja->is_active,
                    'sort_order' => $kelompokKerja->sort_order,
                    'created_at' => $kelompokKerja->created_at->format('d/m/Y H:i'),
                    'updated_at' => $kelompokKerja->updated_at->format('d/m/Y H:i'),
                ],
                'scheme_info' => [
                    'id' => $scheme->id,
                    'nama' => $scheme->nama,
                    'code_1' => $scheme->code_1,
                    'jenjang' => $scheme->jenjang,
                ],
                'units' => $kelompokKerja->unitKompetensis->map(function ($unit) {
                    return [
                        'id' => $unit->id,
                        'kode_unit' => $unit->kode_unit,
                        'judul_unit' => $unit->judul_unit,
                        'pivot_data' => [
                            'sort_order' => $unit->pivot->sort_order,
                            'is_active' => $unit->pivot->is_active,
                            'assigned_at' => $unit->pivot->created_at->format('d/m/Y H:i'),
                        ],
                        'elements_count' => $unit->elemenKompetensis ? $unit->elemenKompetensis->count() : 0,
                        'portfolio_files_count' => $unit->portfolioFiles ? $unit->portfolioFiles->where('is_active', true)->count() : 0,
                    ];
                }),
                'statistics' => [
                    'total_units' => $kelompokKerja->unitKompetensis->count(),
                    'active_units' => $kelompokKerja->unitKompetensis->where('pivot.is_active', true)->count(),
                    'total_elements' => $kelompokKerja->unitKompetensis->sum(function ($unit) {
                        return $unit->elemenKompetensis ? $unit->elemenKompetensis->count() : 0;
                    }),
                    'total_portfolio_files' => $kelompokKerja->unitKompetensis->sum(function ($unit) {
                        return $unit->portfolioFiles ? $unit->portfolioFiles->where('is_active', true)->count() : 0;
                    }),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengexport data: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
