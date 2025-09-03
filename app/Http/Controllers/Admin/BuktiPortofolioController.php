<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuktiPortofolio;
use App\Models\CertificationScheme;
use App\Models\KelompokKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BuktiPortofolioController extends Controller
{
    /**
     * Display a listing of bukti portofolio for a specific kelompok kerja
     */
    public function index(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        try {
            $buktiPortofolios = $kelompokKerja
                ->buktiPortofolios()
                ->orderBy('sort_order')
                ->get()
                ->map(function ($bukti) {
                    return [
                        'id' => $bukti->id,
                        'bukti_portofolio' => $bukti->bukti_portofolio,
                        'is_active' => $bukti->is_active,
                        'sort_order' => $bukti->sort_order,
                        'dependency_type' => $bukti->dependency_type,
                        'dependency_rules' => $bukti->dependency_rules,
                        'group_identifier' => $bukti->group_identifier,
                        'status_text' => $bukti->status_text,
                        'created_at' => $bukti->created_at,
                        'updated_at' => $bukti->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $buktiPortofolios,
                'message' => 'Bukti portofolio berhasil dimuat.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading bukti portofolio: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memuat bukti portofolio.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Store multiple new bukti portofolio with dependency support
     */
    public function store(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'bukti_portofolios' => 'required|array|min:1',
                'bukti_portofolios.*.bukti_portofolio' => 'required|string|min:10|max:2000',
                'bukti_portofolios.*.is_active' => 'boolean',
                'bukti_portofolios.*.sort_order' => 'nullable|integer|min:1',
                'bukti_portofolios.*.dependency_type' => 'in:standalone,required_with,optional_with,exclusive',
                'bukti_portofolios.*.dependency_rules' => 'nullable|array',
                'bukti_portofolios.*.group_identifier' => 'nullable|string|max:100',
            ],
            [
                'bukti_portofolios.required' => 'Data bukti portofolio harus diisi.',
                'bukti_portofolios.array' => 'Format data bukti portofolio tidak valid.',
                'bukti_portofolios.min' => 'Minimal harus ada 1 bukti portofolio.',
                'bukti_portofolios.*.bukti_portofolio.required' => 'Deskripsi bukti portofolio harus diisi.',
                'bukti_portofolios.*.bukti_portofolio.min' => 'Deskripsi bukti portofolio minimal 10 karakter.',
                'bukti_portofolios.*.bukti_portofolio.max' => 'Deskripsi bukti portofolio maksimal 2000 karakter.',
                'bukti_portofolios.*.dependency_type.in' => 'Tipe dependency tidak valid.',
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            $buktiPortofolios = $request->input('bukti_portofolios');
            $createdCount = 0;
            $createdBukti = [];

            foreach ($buktiPortofolios as $index => $buktiData) {
                // Check for duplicate content
                $existing = $kelompokKerja
                    ->buktiPortofolios()
                    ->where('bukti_portofolio', trim($buktiData['bukti_portofolio']))
                    ->first();

                if ($existing) {
                    continue; // Skip duplicate
                }

                $buktiPortofolio = new BuktiPortofolio();
                $buktiPortofolio->kelompok_kerja_id = $kelompokKerja->id;
                $buktiPortofolio->bukti_portofolio = trim($buktiData['bukti_portofolio']);
                $buktiPortofolio->is_active = $buktiData['is_active'] ?? true;
                $buktiPortofolio->sort_order = $buktiData['sort_order'] ?? $index + 1;
                $buktiPortofolio->dependency_type = $buktiData['dependency_type'] ?? 'standalone';
                $buktiPortofolio->dependency_rules = $buktiData['dependency_rules'] ?? null;
                $buktiPortofolio->group_identifier = $buktiData['group_identifier'] ?? null;

                // Set default sort_order if not provided
                if (!$buktiPortofolio->sort_order) {
                    $maxSortOrder = $kelompokKerja->buktiPortofolios()->max('sort_order') ?? 0;
                    $buktiPortofolio->sort_order = $maxSortOrder + 1;
                }

                $buktiPortofolio->save();
                $createdBukti[] = $buktiPortofolio;
                $createdCount++;
            }

            // Validate dependencies after all bukti are created
            $this->validateBuktiDependencies($createdBukti);

            DB::commit();

            $message = $createdCount > 0 ? "Berhasil menambahkan {$createdCount} bukti portofolio." : 'Tidak ada bukti portofolio baru yang ditambahkan (mungkin sudah ada yang sama).';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'created_count' => $createdCount,
                    'total_count' => $kelompokKerja->buktiPortofolios()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing bukti portofolio: ' . $e->getMessage(), [
                'scheme_id' => $scheme->id,
                'kelompok_kerja_id' => $kelompokKerja->id,
                'request_data' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan bukti portofolio.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Display the specified bukti portofolio
     */
    public function show(CertificationScheme $scheme, KelompokKerja $kelompokKerja, BuktiPortofolio $buktiPortofolio)
    {
        // Ensure the bukti portofolio belongs to the kelompok kerja
        if ($buktiPortofolio->kelompok_kerja_id !== $kelompokKerja->id) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Bukti portofolio tidak ditemukan dalam kelompok kerja ini.',
                ],
                404,
            );
        }

        return response()->json([
            'success' => true,
            'data' => $buktiPortofolio,
            'message' => 'Bukti portofolio berhasil dimuat.',
        ]);
    }

    /**
     * Update the specified bukti portofolio
     */
    public function update(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja, BuktiPortofolio $buktiPortofolio)
    {
        // Ensure the bukti portofolio belongs to the kelompok kerja
        if ($buktiPortofolio->kelompok_kerja_id !== $kelompokKerja->id) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Bukti portofolio tidak ditemukan dalam kelompok kerja ini.',
                ],
                404,
            );
        }

        $validator = Validator::make(
            $request->all(),
            [
                'bukti_portofolio' => 'required|string|min:10|max:2000',
                'is_active' => 'boolean',
                'sort_order' => 'nullable|integer|min:1',
                'dependency_type' => 'in:standalone,required_with,optional_with,exclusive',
                'dependency_rules' => 'nullable|array',
                'group_identifier' => 'nullable|string|max:100',
            ],
            [
                'bukti_portofolio.required' => 'Deskripsi bukti portofolio harus diisi.',
                'bukti_portofolio.min' => 'Deskripsi bukti portofolio minimal 10 karakter.',
                'bukti_portofolio.max' => 'Deskripsi bukti portofolio maksimal 2000 karakter.',
                'dependency_type.in' => 'Tipe dependency tidak valid.',
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            // Check for duplicate content (excluding current record)
            $duplicate = $kelompokKerja
                ->buktiPortofolios()
                ->where('bukti_portofolio', trim($request->input('bukti_portofolio')))
                ->where('id', '!=', $buktiPortofolio->id)
                ->first();

            if ($duplicate) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Bukti portofolio dengan deskripsi yang sama sudah ada.',
                    ],
                    422,
                );
            }

            $buktiPortofolio->bukti_portofolio = trim($request->input('bukti_portofolio'));
            $buktiPortofolio->is_active = $request->input('is_active', $buktiPortofolio->is_active);
            $buktiPortofolio->dependency_type = $request->input('dependency_type', $buktiPortofolio->dependency_type);
            $buktiPortofolio->dependency_rules = $request->input('dependency_rules', $buktiPortofolio->dependency_rules);
            $buktiPortofolio->group_identifier = $request->input('group_identifier', $buktiPortofolio->group_identifier);

            if ($request->has('sort_order')) {
                $buktiPortofolio->sort_order = $request->input('sort_order');
            }

            $buktiPortofolio->save();

            return response()->json([
                'success' => true,
                'message' => 'Bukti portofolio berhasil diperbarui.',
                'data' => $buktiPortofolio,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating bukti portofolio: ' . $e->getMessage(), [
                'bukti_id' => $buktiPortofolio->id,
                'request_data' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui bukti portofolio.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified bukti portofolio from storage
     */
    public function destroy(CertificationScheme $scheme, KelompokKerja $kelompokKerja, BuktiPortofolio $buktiPortofolio)
    {
        // Ensure the bukti portofolio belongs to the kelompok kerja
        if ($buktiPortofolio->kelompok_kerja_id !== $kelompokKerja->id) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Bukti portofolio tidak ditemukan dalam kelompok kerja ini.',
                ],
                404,
            );
        }

        try {
            DB::beginTransaction();

            $buktiTitle = $buktiPortofolio->bukti_portofolio;
            $sortOrder = $buktiPortofolio->sort_order;

            // Check if this bukti is required by others
            $dependentBukti = $kelompokKerja->buktiPortofolios()->where('dependency_type', 'required_with')->whereJsonContains('dependency_rules->required_bukti_ids', $buktiPortofolio->id)->get();

            if ($dependentBukti->count() > 0) {
                $dependentTitles = $dependentBukti->pluck('bukti_portofolio')->toArray();
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Bukti portofolio ini diperlukan oleh bukti lain: ' . implode(', ', array_map(fn($title) => Str::limit($title, 30), $dependentTitles)),
                    ],
                    422,
                );
            }

            // Delete the bukti portofolio
            $buktiPortofolio->delete();

            // Reorder remaining bukti portofolios
            $this->reorderBuktiPortofolios($kelompokKerja, $sortOrder);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bukti portofolio berhasil dihapus.',
                'data' => [
                    'deleted_bukti' => Str::limit($buktiTitle, 50),
                    'remaining_count' => $kelompokKerja->buktiPortofolios()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting bukti portofolio: ' . $e->getMessage(), [
                'bukti_id' => $buktiPortofolio->id,
                'kelompok_kerja_id' => $kelompokKerja->id,
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus bukti portofolio.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Get dependency options for a kelompok kerja
     */
    public function getDependencyOptions(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        try {
            $buktiPortofolios = $kelompokKerja
                ->buktiPortofolios()
                ->orderBy('sort_order')
                ->get(['id', 'bukti_portofolio', 'sort_order']);

            $dependencyTypes = [
                'standalone' => 'Mandiri (tidak bergantung)',
                'required_with' => 'Wajib dengan bukti lain',
                'optional_with' => 'Opsional dengan bukti lain',
                'exclusive' => 'Eksklusif (tidak boleh dengan bukti tertentu)',
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'dependency_types' => $dependencyTypes,
                    'available_bukti' => $buktiPortofolios->map(function ($bukti) {
                        return [
                            'id' => $bukti->id,
                            'label' => "#{$bukti->sort_order}. " . Str::limit($bukti->bukti_portofolio, 60),
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting dependency options: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memuat opsi dependency.',
                ],
                500,
            );
        }
    }

    /**
     * Validate bukti portfolio dependencies
     */
    public function validateDependencies(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'selected_bukti_ids' => 'required|array',
            'selected_bukti_ids.*' => 'integer|exists:bukti_portofolios,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            $selectedIds = $request->input('selected_bukti_ids');
            $selectedBukti = $kelompokKerja->buktiPortofolios()->whereIn('id', $selectedIds)->get();

            $validationResults = [];
            $hasErrors = false;

            foreach ($selectedBukti as $bukti) {
                $isValid = $bukti->validateDependencies($selectedIds);

                if (!$isValid) {
                    $hasErrors = true;
                }

                $validationResults[] = [
                    'id' => $bukti->id,
                    'bukti_portofolio' => $bukti->bukti_portofolio,
                    'is_valid' => $isValid,
                    'dependency_type' => $bukti->dependency_type,
                    'error_message' => $isValid ? null : $this->getDependencyErrorMessage($bukti, $selectedIds),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'is_valid' => !$hasErrors,
                    'validation_results' => $validationResults,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error validating dependencies: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat validasi dependency.',
                ],
                500,
            );
        }
    }

    /**
     * Validate bukti dependencies
     */
    private function validateBuktiDependencies($buktiPortofolios)
    {
        // Implementation for dependency validation
        // This would be called during bulk operations to ensure consistency
    }

    /**
     * Get dependency error message
     */
    private function getDependencyErrorMessage(BuktiPortofolio $bukti, array $selectedIds)
    {
        $rules = $bukti->dependency_rules ?? [];

        switch ($bukti->dependency_type) {
            case 'required_with':
                $requiredIds = $rules['required_bukti_ids'] ?? [];
                $missing = array_diff($requiredIds, $selectedIds);
                if (!empty($missing)) {
                    return 'Memerlukan bukti dengan ID: ' . implode(', ', $missing);
                }
                break;

            case 'exclusive':
                $exclusiveIds = $rules['exclusive_bukti_ids'] ?? [];
                $conflicts = array_intersect($exclusiveIds, $selectedIds);
                if (!empty($conflicts)) {
                    return 'Tidak boleh bersamaan dengan bukti ID: ' . implode(', ', $conflicts);
                }
                break;
        }

        return 'Dependency tidak valid';
    }

    /**
     * Toggle the status of bukti portofolio
     */
    public function toggleStatus(CertificationScheme $scheme, KelompokKerja $kelompokKerja, BuktiPortofolio $buktiPortofolio)
    {
        // Ensure the bukti portofolio belongs to the kelompok kerja
        if ($buktiPortofolio->kelompok_kerja_id !== $kelompokKerja->id) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Bukti portofolio tidak ditemukan dalam kelompok kerja ini.',
                ],
                404,
            );
        }

        try {
            $buktiPortofolio->is_active = !$buktiPortofolio->is_active;
            $buktiPortofolio->save();

            $status = $buktiPortofolio->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => "Bukti portofolio berhasil {$status}.",
                'data' => [
                    'is_active' => $buktiPortofolio->is_active,
                    'status_text' => $buktiPortofolio->is_active ? 'Aktif' : 'Nonaktif',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling bukti portofolio status: ' . $e->getMessage(), [
                'bukti_id' => $buktiPortofolio->id,
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengubah status bukti portofolio.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Bulk operations for bukti portofolio with dependency validation
     */
    public function bulkAction(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete,reorder',
            'bukti_ids' => 'required_unless:action,reorder|array|min:1',
            'bukti_ids.*' => 'integer|exists:bukti_portofolios,id',
            'order_data' => 'required_if:action,reorder|array',
            'order_data.*.id' => 'required_with:order_data|integer|exists:bukti_portofolios,id',
            'order_data.*.sort_order' => 'required_with:order_data|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            $action = $request->input('action');
            $affectedCount = 0;

            switch ($action) {
                case 'activate':
                case 'deactivate':
                    $isActive = $action === 'activate';
                    $buktiIds = $request->input('bukti_ids');

                    $affectedCount = $kelompokKerja
                        ->buktiPortofolios()
                        ->whereIn('id', $buktiIds)
                        ->update(['is_active' => $isActive]);

                    $actionText = $isActive ? 'diaktifkan' : 'dinonaktifkan';
                    $message = "{$affectedCount} bukti portofolio berhasil {$actionText}.";
                    break;

                case 'delete':
                    $buktiIds = $request->input('bukti_ids');

                    // Check dependencies before deletion
                    $dependencyCheck = $this->checkDependenciesBeforeDelete($kelompokKerja, $buktiIds);
                    if (!$dependencyCheck['can_delete']) {
                        return response()->json(
                            [
                                'success' => false,
                                'message' => $dependencyCheck['message'],
                            ],
                            422,
                        );
                    }

                    $affectedCount = $kelompokKerja->buktiPortofolios()->whereIn('id', $buktiIds)->count();

                    $kelompokKerja->buktiPortofolios()->whereIn('id', $buktiIds)->delete();

                    // Reorder remaining items
                    $this->reorderAllBuktiPortofolios($kelompokKerja);

                    $message = "{$affectedCount} bukti portofolio berhasil dihapus.";
                    break;

                case 'reorder':
                    $orderData = $request->input('order_data');

                    foreach ($orderData as $item) {
                        $kelompokKerja
                            ->buktiPortofolios()
                            ->where('id', $item['id'])
                            ->update(['sort_order' => $item['sort_order']]);
                        $affectedCount++;
                    }

                    $message = "Urutan {$affectedCount} bukti portofolio berhasil diperbarui.";
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'affected_count' => $affectedCount,
                    'total_count' => $kelompokKerja->buktiPortofolios()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk action for bukti portofolio: ' . $e->getMessage(), [
                'action' => $request->input('action'),
                'kelompok_kerja_id' => $kelompokKerja->id,
                'request_data' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat melakukan operasi bulk.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Check dependencies before deletion
     */
    private function checkDependenciesBeforeDelete(KelompokKerja $kelompokKerja, array $buktiIds)
    {
        $dependentBukti = $kelompokKerja
            ->buktiPortofolios()
            ->where('dependency_type', 'required_with')
            ->get()
            ->filter(function ($bukti) use ($buktiIds) {
                $requiredIds = $bukti->dependency_rules['required_bukti_ids'] ?? [];
                return !empty(array_intersect($requiredIds, $buktiIds));
            });

        if ($dependentBukti->count() > 0) {
            $dependentTitles = $dependentBukti->pluck('bukti_portofolio')->take(3)->toArray();
            $message = 'Tidak dapat menghapus karena bukti ini diperlukan oleh: ' . implode(', ', array_map(fn($title) => Str::limit($title, 30), $dependentTitles));

            if ($dependentBukti->count() > 3) {
                $message .= ' dan ' . ($dependentBukti->count() - 3) . ' bukti lainnya';
            }

            return [
                'can_delete' => false,
                'message' => $message,
            ];
        }

        return [
            'can_delete' => true,
            'message' => '',
        ];
    }

    /**
     * Reorder bukti portofolios after deletion
     */
    private function reorderBuktiPortofolios(KelompokKerja $kelompokKerja, int $deletedSortOrder)
    {
        $kelompokKerja->buktiPortofolios()->where('sort_order', '>', $deletedSortOrder)->decrement('sort_order');
    }

    /**
     * Reorder all bukti portofolios sequentially
     */
    private function reorderAllBuktiPortofolios(KelompokKerja $kelompokKerja)
    {
        $buktiPortofolios = $kelompokKerja->buktiPortofolios()->orderBy('sort_order')->get();

        foreach ($buktiPortofolios as $index => $bukti) {
            $bukti->update(['sort_order' => $index + 1]);
        }
    }

    public function storeBatch(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'bukti_items' => 'required|array|min:1',
            'bukti_items.*.bukti_portofolio' => 'required|string|min:10|max:2000',
            'bukti_items.*.is_active' => 'boolean',
            'bukti_items.*.sort_order' => 'nullable|integer|min:1',
            'bukti_items.*.dependency_type' => 'in:standalone,required_with,optional_with,exclusive',
            'bukti_items.*.dependency_rules' => 'nullable|array',
            'bukti_items.*.group_identifier' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            $buktiItems = $request->input('bukti_items');
            $created = [];
            $duplicates = 0;

            foreach ($buktiItems as $index => $item) {
                // Check for duplicates
                $existing = $kelompokKerja
                    ->buktiPortofolios()
                    ->where('bukti_portofolio', trim($item['bukti_portofolio']))
                    ->first();

                if ($existing) {
                    $duplicates++;
                    continue;
                }

                $bukti = new BuktiPortofolio();
                $bukti->kelompok_kerja_id = $kelompokKerja->id;
                $bukti->bukti_portofolio = trim($item['bukti_portofolio']);
                $bukti->is_active = $item['is_active'] ?? true;
                $bukti->dependency_type = $item['dependency_type'] ?? 'standalone';
                $bukti->dependency_rules = $item['dependency_rules'] ?? null;
                $bukti->group_identifier = $item['group_identifier'] ?? null;

                // Set sort order
                if (isset($item['sort_order'])) {
                    $bukti->sort_order = $item['sort_order'];
                } else {
                    $maxSort = $kelompokKerja->buktiPortofolios()->max('sort_order') ?? 0;
                    $bukti->sort_order = $maxSort + 1;
                }

                $bukti->save();
                $created[] = $bukti;
            }

            DB::commit();

            $createdCount = count($created);
            $message = "Berhasil menambahkan {$createdCount} bukti portofolio.";

            if ($duplicates > 0) {
                $message .= " {$duplicates} bukti diabaikan karena sudah ada.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'created_count' => $createdCount,
                    'duplicate_count' => $duplicates,
                    'total_count' => $kelompokKerja->buktiPortofolios()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in batch store bukti portofolio: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan bukti portofolio.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Reorder bukti portofolio items
     */
    public function reorder(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:bukti_portofolios,id',
            'items.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            $items = $request->input('items');

            foreach ($items as $item) {
                $bukti = $kelompokKerja->buktiPortofolios()->where('id', $item['id'])->first();

                if ($bukti) {
                    $bukti->sort_order = $item['sort_order'];
                    $bukti->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan bukti portofolio berhasil diperbarui.',
                'data' => [
                    'updated_count' => count($items),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reordering bukti portofolio: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengubah urutan.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Create or update dependency groups
     */
    public function manageGroups(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:100',
            'dependency_type' => 'required|in:required_with,optional_with,exclusive',
            'bukti_ids' => 'required|array|min:1',
            'bukti_ids.*' => 'integer|exists:bukti_portofolios,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            $groupName = $request->input('group_name');
            $dependencyType = $request->input('dependency_type');
            $buktiIds = $request->input('bukti_ids');

            // Validate that all bukti belong to this kelompok kerja
            $validBukti = $kelompokKerja->buktiPortofolios()->whereIn('id', $buktiIds)->get();

            if ($validBukti->count() !== count($buktiIds)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Beberapa bukti portofolio tidak ditemukan dalam kelompok kerja ini.',
                    ],
                    422,
                );
            }

            // Update each bukti with group information
            foreach ($validBukti as $bukti) {
                $bukti->group_identifier = $groupName;
                $bukti->dependency_type = $dependencyType;

                // Set dependency rules based on type
                $otherIds = $buktiIds;
                $otherIds = array_values(array_filter($otherIds, fn($id) => $id != $bukti->id));

                switch ($dependencyType) {
                    case 'required_with':
                        $bukti->dependency_rules = ['required_bukti_ids' => $otherIds];
                        break;
                    case 'optional_with':
                        $bukti->dependency_rules = ['optional_bukti_ids' => $otherIds];
                        break;
                    case 'exclusive':
                        $bukti->dependency_rules = ['exclusive_bukti_ids' => $otherIds];
                        break;
                }

                $bukti->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Grup '{$groupName}' berhasil dibuat dengan {$validBukti->count()} bukti portofolio.",
                'data' => [
                    'group_name' => $groupName,
                    'dependency_type' => $dependencyType,
                    'bukti_count' => $validBukti->count(),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error managing groups: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengelola grup.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Remove bukti from group
     */
    public function removeFromGroup(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
    {
        $validator = Validator::make($request->all(), [
            'bukti_ids' => 'required|array|min:1',
            'bukti_ids.*' => 'integer|exists:bukti_portofolios,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            $buktiIds = $request->input('bukti_ids');

            $updatedCount = $kelompokKerja
                ->buktiPortofolios()
                ->whereIn('id', $buktiIds)
                ->update([
                    'group_identifier' => null,
                    'dependency_type' => 'standalone',
                    'dependency_rules' => null,
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} bukti portofolio berhasil dikeluarkan dari grup.",
                'data' => [
                    'updated_count' => $updatedCount,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing from group: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengeluarkan dari grup.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                ],
                500,
            );
        }
    }

    /**
     * Get available bukti for grouping
     */
    public function getAvailableBukti(CertificationScheme $scheme, KelompokKerja $kelompokKerja)
{
    try {
        // Cek apakah kelompok kerja benar-benar milik scheme ini
        if ($kelompokKerja->certification_scheme_id !== $scheme->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kelompok kerja tidak termasuk dalam skema ini.',
                'debug' => [
                    'scheme_id_from_route' => $scheme->id,
                    'scheme_id_from_kelompok' => $kelompokKerja->certification_scheme_id,
                ]
            ], 404);
        }

        // Ambil semua bukti portofolio untuk kelompok kerja dan yang aktif
        $allBukti = BuktiPortofolio::where('kelompok_kerja_id', $kelompokKerja->id)
            ->where('is_active', true)
            ->get();

        if ($allBukti->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'standalone' => [],
                    'grouped' => [],
                ],
                'message' => 'Tidak ada bukti portofolio ditemukan.',
                'debug' => [
                    'scheme_id' => $scheme->id,
                    'kelompok_kerja_id' => $kelompokKerja->id,
                    'total_bukti_in_db' => BuktiPortofolio::count(),
                    'bukti_for_this_kelompok' => $allBukti->count(),
                ],
            ]);
        }

        // Pisahkan standalone dan grouped
        $standalone = $allBukti
            ->filter(fn($item) => empty($item->group_identifier))
            ->map(fn($item) => [
                'id' => $item->id,
                'bukti_portofolio' => $item->bukti_portofolio,
                'sort_order' => $item->sort_order,
                'dependency_type' => $item->dependency_type,
                'group_identifier' => $item->group_identifier,
            ])
            ->values()
            ->toArray();

        $grouped = $allBukti
            ->filter(fn($item) => !empty($item->group_identifier))
            ->groupBy('group_identifier')
            ->map(fn($groupItems, $groupId) => [
                'group_identifier' => $groupId,
                'items' => $groupItems->map(fn($item) => [
                    'id' => $item->id,
                    'bukti_portofolio' => $item->bukti_portofolio,
                    'sort_order' => $item->sort_order,
                    'dependency_type' => $item->dependency_type,
                ])->values(),
            ])
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'standalone' => $standalone,
                'grouped' => $grouped,
            ],
            'debug' => [
                'scheme_id' => $scheme->id,
                'kelompok_kerja_id' => $kelompokKerja->id,
                'total_bukti' => $allBukti->count(),
                'standalone_bukti' => count($standalone),
                'grouped_bukti' => count($grouped),
            ],
            'message' => 'Data berhasil dimuat.',
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in getAvailableBukti: ' . $e->getMessage(), [
            'scheme_id' => $scheme->id ?? 'NULL',
            'kelompok_kerja_id' => $kelompokKerja->id ?? 'NULL',
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat memuat data bukti portofolio.',
            'error' => $e->getMessage(),
            'debug_info' => [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ],
        ], 500);
    }
}

public function removeGroup(Request $request, CertificationScheme $scheme, KelompokKerja $kelompokKerja)
{
    $validator = Validator::make($request->all(), [
        'group_name' => 'required|string|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        DB::beginTransaction();

        $groupName = $request->input('group_name');

        // Ambil semua bukti yang tergabung di grup ini
        $buktiInGroup = $kelompokKerja->buktiPortofolios()
            ->where('group_identifier', $groupName)
            ->get();

        if ($buktiInGroup->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Grup '{$groupName}' tidak ditemukan atau sudah kosong.",
            ], 404);
        }

        // Reset semua bukti: keluar dari grup
        foreach ($buktiInGroup as $bukti) {
            $bukti->group_identifier = null;
            $bukti->dependency_type = 'standalone';
            $bukti->dependency_rules = null;
            $bukti->save();
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Grup '{$groupName}' berhasil dihapus (semua bukti dikeluarkan).",
            'data' => [
                'removed_count' => $buktiInGroup->count(),
            ],
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error removing group: ' . $e->getMessage(), [
            'group_name' => $request->input('group_name'),
            'kelompok_kerja_id' => $kelompokKerja->id,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghapus grup.',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
        ], 500);
    }
}



}
