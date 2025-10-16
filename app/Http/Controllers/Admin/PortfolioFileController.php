<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\UnitKompetensi;
use App\Models\PortfolioFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PortfolioFileController extends Controller
{
    /**
     * Display a listing of portfolio files for a unit
     */
    public function index(CertificationScheme $scheme, UnitKompetensi $unit)
    {
        try {
            // Pastikan unit belongs to scheme
            if ($unit->certification_scheme_id !== $scheme->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan dalam skema ini'
                ], 404);
            }

            $portfolioFiles = PortfolioFile::where('unit_kompetensi_id', $unit->id)
                ->ordered()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $portfolioFiles->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'document_name' => $file->document_name,
                        'document_description' => $file->document_description,
                        'is_required' => $file->is_required,
                        'is_active' => $file->is_active,
                        'sort_order' => $file->sort_order,
                        'created_at' => $file->created_at->format('d M Y H:i')
                    ];
                }),
                'message' => 'Portfolio files retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving portfolio files: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat dokumen portofolio'
            ], 500);
        }
    }

    /**
     * Store a newly created portfolio file
     */
    public function store(Request $request, CertificationScheme $scheme, UnitKompetensi $unit)
    {
        // Pastikan unit belongs to scheme
        if ($unit->certification_scheme_id !== $scheme->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan dalam skema ini'
            ], 404);
        }
        // dd($request->all());
        $request->validate([
            'document_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('portfolio_files')
                    ->where('unit_kompetensi_id', $unit->id)
            ],
            'document_description' => 'nullable|string|max:500',
            'is_required' => 'required|boolean',
            'is_active' => 'boolean'
        ], [
            'document_name.required' => 'Nama dokumen harus diisi',
            'document_name.unique' => 'Nama dokumen sudah ada untuk unit ini',
            'document_name.max' => 'Nama dokumen tidak boleh lebih dari 255 karakter',
            'document_description.max' => 'Deskripsi tidak boleh lebih dari 500 karakter',
            'is_required.required' => 'Status dokumen harus dipilih'
        ]);

        try {
            DB::beginTransaction();

            // Get max sort order for this unit specifically
            $maxSort = PortfolioFile::where('unit_kompetensi_id', $unit->id)
                ->max('sort_order') ?? 0;

            $portfolioFile = PortfolioFile::create([
                'unit_kompetensi_id' => $unit->id, // Pastikan menggunakan unit ID yang benar
                'document_name' => $request->document_name,
                'document_description' => $request->document_description,
                'is_required' => $request->boolean('is_required'),
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $maxSort + 1
            ]);

            DB::commit();

            Log::info("Portfolio file created for Unit ID: {$unit->id}, Document: {$portfolioFile->document_name}");

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $portfolioFile->id,
                    'document_name' => $portfolioFile->document_name,
                    'document_description' => $portfolioFile->document_description,
                    'is_required' => $portfolioFile->is_required,
                    'is_active' => $portfolioFile->is_active,
                    'sort_order' => $portfolioFile->sort_order,
                    'created_at' => $portfolioFile->created_at->format('d M Y H:i')
                ],
                'message' => 'Dokumen portofolio berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating portfolio file: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan dokumen portofolio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified portfolio file
     */
    public function update(Request $request, CertificationScheme $scheme, UnitKompetensi $unit, PortfolioFile $portfolioFile)
    {
        // Pastikan unit belongs to scheme
        if ($unit->certification_scheme_id !== $scheme->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan dalam skema ini'
            ], 404);
        }

        // Ensure portfolio file belongs to the unit
        if ($portfolioFile->unit_kompetensi_id !== $unit->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan untuk unit ini'
            ], 404);
        }

        $request->validate([
            'document_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('portfolio_files')
                    ->where('unit_kompetensi_id', $unit->id)
                    ->ignore($portfolioFile->id)
            ],
            'document_description' => 'nullable|string|max:500',
            'is_required' => 'required|boolean',
            'is_active' => 'boolean'
        ], [
            'document_name.required' => 'Nama dokumen harus diisi',
            'document_name.unique' => 'Nama dokumen sudah ada untuk unit ini',
            'document_name.max' => 'Nama dokumen tidak boleh lebih dari 255 karakter',
            'document_description.max' => 'Deskripsi tidak boleh lebih dari 500 karakter',
            'is_required.required' => 'Status dokumen harus dipilih'
        ]);

        try {
            DB::beginTransaction();

            $portfolioFile->update([
                'document_name' => $request->document_name,
                'document_description' => $request->document_description,
                'is_required' => $request->boolean('is_required'),
                'is_active' => $request->boolean('is_active', true)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $portfolioFile->id,
                    'document_name' => $portfolioFile->document_name,
                    'document_description' => $portfolioFile->document_description,
                    'is_required' => $portfolioFile->is_required,
                    'is_active' => $portfolioFile->is_active,
                    'sort_order' => $portfolioFile->sort_order,
                    'updated_at' => $portfolioFile->updated_at->format('d M Y H:i')
                ],
                'message' => 'Dokumen portofolio berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating portfolio file: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui dokumen portofolio'
            ], 500);
        }
    }

    /**
     * Remove the specified portfolio file
     */
    public function destroy(CertificationScheme $scheme, UnitKompetensi $unit, PortfolioFile $portfolioFile)
    {
        // Pastikan unit belongs to scheme
        if ($unit->certification_scheme_id !== $scheme->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan dalam skema ini'
            ], 404);
        }

        // Ensure portfolio file belongs to the unit
        if ($portfolioFile->unit_kompetensi_id !== $unit->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan untuk unit ini'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $documentName = $portfolioFile->document_name;
            $portfolioFile->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Dokumen '{$documentName}' berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting portfolio file: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen portofolio'
            ], 500);
        }
    }

    /**
     * Toggle active status of portfolio file
     */
    public function toggleStatus(CertificationScheme $scheme, UnitKompetensi $unit, PortfolioFile $portfolioFile)
    {
        // Pastikan unit belongs to scheme
        if ($unit->certification_scheme_id !== $scheme->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan dalam skema ini'
            ], 404);
        }

        // Ensure portfolio file belongs to the unit
        if ($portfolioFile->unit_kompetensi_id !== $unit->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan untuk unit ini'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $newStatus = !$portfolioFile->is_active;
            $portfolioFile->is_active = $newStatus;
            $portfolioFile->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $portfolioFile->id,
                    'is_active' => $portfolioFile->is_active
                ],
                'message' => 'Status dokumen berhasil diubah menjadi ' . ($newStatus ? 'aktif' : 'nonaktif')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling portfolio file status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status dokumen'
            ], 500);
        }
    }

    /**
     * Duplicate portfolio files from another unit
     */
    public function duplicate(Request $request, CertificationScheme $scheme, UnitKompetensi $unit)
    {
        // Pastikan unit belongs to scheme
        if ($unit->certification_scheme_id !== $scheme->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan dalam skema ini'
            ], 404);
        }

        $request->validate([
            'source_unit_id' => [
                'required',
                'integer',
                'exists:unit_kompetensis,id',
                'not_in:' . $unit->id
            ],
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'integer|exists:portfolio_files,id'
        ], [
            'source_unit_id.required' => 'Unit sumber harus dipilih',
            'source_unit_id.exists' => 'Unit sumber tidak ditemukan',
            'source_unit_id.not_in' => 'Tidak bisa menduplikasi dari unit yang sama',
            'document_ids.required' => 'Pilih minimal satu dokumen',
            'document_ids.min' => 'Pilih minimal satu dokumen',
            'document_ids.*.exists' => 'Dokumen tidak ditemukan'
        ]);

        try {
            DB::beginTransaction();

            $sourceUnit = UnitKompetensi::findOrFail($request->source_unit_id);

            // Ensure source unit is in the same scheme
            if ($sourceUnit->certification_scheme_id !== $scheme->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit sumber harus dalam skema yang sama'
                ], 400);
            }

            // Get source documents - pastikan hanya ambil yang milik source unit
            $sourceDocuments = PortfolioFile::whereIn('id', $request->document_ids)
                ->where('unit_kompetensi_id', $request->source_unit_id) // Penting!
                ->get();

            if ($sourceDocuments->count() !== count($request->document_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa dokumen tidak ditemukan atau tidak milik unit sumber'
                ], 400);
            }

            $duplicated = [];
            $errors = [];
            $skipped = [];

            // Get max sort order for target unit
            $maxSort = PortfolioFile::where('unit_kompetensi_id', $unit->id)
                ->max('sort_order') ?? 0;

            foreach ($sourceDocuments as $index => $sourceDoc) {
                // Check if document name already exists in target unit
                $existingDoc = PortfolioFile::where('unit_kompetensi_id', $unit->id)
                    ->where('document_name', $sourceDoc->document_name)
                    ->first();

                if ($existingDoc) {
                    $skipped[] = $sourceDoc->document_name;
                    continue;
                }

                try {
                    $newDoc = PortfolioFile::create([
                        'unit_kompetensi_id' => $unit->id, // Target unit
                        'document_name' => $sourceDoc->document_name,
                        'document_description' => $sourceDoc->document_description,
                        'is_required' => $sourceDoc->is_required,
                        'is_active' => $sourceDoc->is_active,
                        'sort_order' => $maxSort + $index + 1
                    ]);

                    $duplicated[] = $newDoc->document_name;

                    Log::info("Duplicated document: {$sourceDoc->document_name} from Unit {$sourceUnit->id} to Unit {$unit->id}");
                } catch (\Exception $e) {
                    $errors[] = [
                        'document' => $sourceDoc->document_name,
                        'error' => $e->getMessage()
                    ];
                    Log::error("Error duplicating document {$sourceDoc->document_name}: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = count($duplicated) . ' dokumen berhasil diduplikasi';

            if (count($skipped) > 0) {
                $message .= ', ' . count($skipped) . ' dokumen dilewati (sudah ada)';
            }

            if (count($errors) > 0) {
                $message .= ', ' . count($errors) . ' dokumen gagal diduplikasi';
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'duplicated' => count($duplicated),
                    'skipped' => count($skipped),
                    'errors' => count($errors),
                    'skipped_documents' => $skipped,
                    'error_details' => $errors,
                    'duplicated_documents' => $duplicated
                ],
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating portfolio files: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menduplikasi dokumen portofolio: ' . $e->getMessage()
            ], 500);
        }
    }
}
