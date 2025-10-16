<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_kompetensi_id',
        'document_name',
        'document_description',
        'sort_order',
        'is_required',
        'is_active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    /**
     * Get the unit kompetensi that owns the portfolio file
     */
    public function unitKompetensi()
    {
        return $this->belongsTo(UnitKompetensi::class);
    }

    /**
     * Get the certification scheme through unit kompetensi
     */
    public function certificationScheme()
    {
        return $this->hasOneThrough(
            CertificationScheme::class,
            UnitKompetensi::class,
            'id',
            'id',
            'unit_kompetensi_id',
            'certification_scheme_id'
        );
    }

    /* ===================== SCOPES ===================== */

    /**
     * Scope to only get active portfolio files
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only get required portfolio files
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope to only get optional portfolio files
     */
    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Scope by unit kompetensi
     */
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_kompetensi_id', $unitId);
    }

    /**
     * Scope by certification scheme
     */
    public function scopeByCertificationScheme($query, $schemeId)
    {
        return $query->whereHas('unitKompetensi', function ($q) use ($schemeId) {
            $q->where('certification_scheme_id', $schemeId);
        });
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('document_name', 'like', "%{$search}%")
                ->orWhere('document_description', 'like', "%{$search}%");
        });
    }

    /* ===================== ACCESSORS ===================== */

    /**
     * Get the status color for display
     */
    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * Get the status text for display
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Get the requirement color for display
     */
    public function getRequirementColorAttribute()
    {
        return $this->is_required ? 'danger' : 'warning';
    }

    /**
     * Get the requirement text for display
     */
    public function getRequirementTextAttribute()
    {
        return $this->is_required ? 'Wajib' : 'Opsional';
    }

    /**
     * Get formatted document name with numbering
     */
    public function getFormattedDocumentNameAttribute()
    {
        return $this->sort_order . '. ' . $this->document_name;
    }

    /**
     * Get badge class for requirement status
     */
    public function getRequirementBadgeAttribute()
    {
        return $this->is_required ? 'badge-danger' : 'badge-warning';
    }

    /**
     * Get icon for requirement status
     */
    public function getRequirementIconAttribute()
    {
        return $this->is_required ? 'fa-exclamation-circle' : 'fa-info-circle';
    }

    /* ===================== HELPER METHODS ===================== */

    /**
     * Toggle active status
     */
    public function toggleStatus()
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Toggle requirement status
     */
    public function toggleRequirement()
    {
        return $this->update(['is_required' => !$this->is_required]);
    }

    /**
     * Update document information
     */
    public function updateDocument($documentName, $description = null)
    {
        return $this->update([
            'document_name' => $documentName,
            'document_description' => $description
        ]);
    }

    /**
     * Duplicate portfolio file to another unit
     */
    public function duplicateToUnit($newUnitId)
    {
        // Get the max sort order for the new unit
        $maxSort = static::where('unit_kompetensi_id', $newUnitId)->max('sort_order') ?? 0;

        $newPortfolioFile = $this->replicate();
        $newPortfolioFile->unit_kompetensi_id = $newUnitId;
        $newPortfolioFile->sort_order = $maxSort + 1;
        $newPortfolioFile->save();

        return $newPortfolioFile;
    }

    /**
     * Get preview data for JSON responses
     */
    public function getPreviewData()
    {
        return [
            'id' => $this->id,
            'document_name' => $this->document_name,
            'document_description' => $this->document_description,
            'formatted_document_name' => $this->formatted_document_name,
            'is_required' => $this->is_required,
            'is_active' => $this->is_active,
            'requirement_text' => $this->requirement_text,
            'requirement_color' => $this->requirement_color,
            'requirement_badge' => $this->requirement_badge,
            'requirement_icon' => $this->requirement_icon,
            'status_text' => $this->status_text,
            'status_color' => $this->status_color,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }

    /* ===================== STATIC METHODS ===================== */

    /**
     * Get all portfolio files for a certification scheme
     */
    public static function getByCertificationScheme($schemeId, $activeOnly = false)
    {
        $query = static::whereHas('unitKompetensi', function ($q) use ($schemeId) {
            $q->where('certification_scheme_id', $schemeId);
        })->with(['unitKompetensi:id,kode_unit,judul_unit']);

        if ($activeOnly) {
            $query->active();
        }

        return $query->ordered()->get();
    }

    /**
     * Reorder portfolio files for a unit
     */
    public static function reorderForUnit($unitId, $orderedIds)
    {
        try {
            foreach ($orderedIds as $index => $portfolioFileId) {
                static::where('id', $portfolioFileId)
                    ->where('unit_kompetensi_id', $unitId)
                    ->update(['sort_order' => $index + 1]);
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Error reordering portfolio files: ' . $e->getMessage());
        }
    }

    /**
     * Create portfolio file for unit
     */
    public static function createForUnit($unitId, $documentName, $description = null, $isRequired = true)
    {
        // Get max sort order for the unit
        $maxSort = static::where('unit_kompetensi_id', $unitId)->max('sort_order') ?? 0;

        return static::create([
            'unit_kompetensi_id' => $unitId,
            'document_name' => $documentName,
            'document_description' => $description,
            'sort_order' => $maxSort + 1,
            'is_required' => $isRequired,
            'is_active' => true
        ]);
    }

    /**
     * Bulk create portfolio files for unit
     */
    public static function bulkCreateForUnit($unitId, $documents)
    {
        $created = [];
        $errors = [];

        // Get starting sort order
        $maxSort = static::where('unit_kompetensi_id', $unitId)->max('sort_order') ?? 0;

        foreach ($documents as $index => $document) {
            try {
                $created[] = static::create([
                    'unit_kompetensi_id' => $unitId,
                    'document_name' => $document['name'],
                    'document_description' => $document['description'] ?? null,
                    'sort_order' => $maxSort + $index + 1,
                    'is_required' => $document['is_required'] ?? true,
                    'is_active' => $document['is_active'] ?? true
                ]);
            } catch (\Exception $e) {
                $errors[] = [
                    'document' => $document['name'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
            'success_count' => count($created),
            'error_count' => count($errors)
        ];
    }

    /**
     * Get statistics for a unit
     */
    public static function getStatsForUnit($unitId)
    {
        $total = static::where('unit_kompetensi_id', $unitId)->count();
        $active = static::where('unit_kompetensi_id', $unitId)->active()->count();
        $required = static::where('unit_kompetensi_id', $unitId)->required()->count();
        $optional = static::where('unit_kompetensi_id', $unitId)->optional()->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'required' => $required,
            'optional' => $optional,
            'required_active' => static::where('unit_kompetensi_id', $unitId)->active()->required()->count(),
            'optional_active' => static::where('unit_kompetensi_id', $unitId)->active()->optional()->count(),
        ];
    }

    /**
     * Get statistics for certification scheme
     */
    public static function getStatsForScheme($schemeId)
    {
        $query = static::whereHas('unitKompetensi', function ($q) use ($schemeId) {
            $q->where('certification_scheme_id', $schemeId);
        });

        $total = $query->count();
        $active = $query->where('is_active', true)->count();
        $required = $query->where('is_required', true)->count();

        // Count by unit
        $unitCounts = static::whereHas('unitKompetensi', function ($q) use ($schemeId) {
            $q->where('certification_scheme_id', $schemeId);
        })
            ->join('unit_kompetensis', 'portfolio_files.unit_kompetensi_id', '=', 'unit_kompetensis.id')
            ->groupBy('unit_kompetensi_id', 'unit_kompetensis.judul_unit')
            ->selectRaw('unit_kompetensi_id, unit_kompetensis.judul_unit, COUNT(*) as count')
            ->get();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'required' => $required,
            'optional' => $total - $required,
            'units_with_portfolio' => $unitCounts->count(),
            'unit_breakdown' => $unitCounts
        ];
    }

    /**
     * Copy portfolio files from one unit to another
     */
    public static function copyFromUnit($sourceUnitId, $targetUnitId, $includeInactive = false)
    {
        $query = static::where('unit_kompetensi_id', $sourceUnitId);

        if (!$includeInactive) {
            $query->active();
        }

        $sourceFiles = $query->ordered()->get();
        $copiedFiles = [];

        // Get max sort order for target unit
        $maxSort = static::where('unit_kompetensi_id', $targetUnitId)->max('sort_order') ?? 0;

        foreach ($sourceFiles as $index => $sourceFile) {
            $copiedFiles[] = static::create([
                'unit_kompetensi_id' => $targetUnitId,
                'document_name' => $sourceFile->document_name,
                'document_description' => $sourceFile->document_description,
                'sort_order' => $maxSort + $index + 1,
                'is_required' => $sourceFile->is_required,
                'is_active' => $sourceFile->is_active
            ]);
        }

        return $copiedFiles;
    }

    /**
     * Get template portfolio files (commonly used documents)
     */
    public static function getTemplateDocuments()
    {
        return [
            [
                'name' => 'Sertifikat Kompetensi',
                'description' => 'Sertifikat yang menunjukkan pencapaian kompetensi',
                'is_required' => true
            ],
            [
                'name' => 'Portofolio Bukti Kerja',
                'description' => 'Kumpulan dokumen yang menunjukkan hasil kerja',
                'is_required' => true
            ],
            [
                'name' => 'Logbook/Jurnal Kerja',
                'description' => 'Catatan harian aktivitas kerja',
                'is_required' => true
            ],
            [
                'name' => 'Testimoni/Referensi Kerja',
                'description' => 'Surat keterangan dari atasan atau rekan kerja',
                'is_required' => false
            ],
            [
                'name' => 'Foto/Video Dokumentasi',
                'description' => 'Dokumentasi visual dari aktivitas kerja',
                'is_required' => false
            ],
            [
                'name' => 'Surat Tugas/Penugasan',
                'description' => 'Dokumen penugasan resmi',
                'is_required' => false
            ],
            [
                'name' => 'Hasil Evaluasi/Penilaian',
                'description' => 'Dokumen hasil evaluasi kinerja',
                'is_required' => false
            ]
        ];
    }

    /**
     * Import template documents to unit
     */
    public static function importTemplateToUnit($unitId, $selectedTemplates = [])
    {
        $templates = static::getTemplateDocuments();
        $imported = [];

        // If no specific templates selected, import all
        if (empty($selectedTemplates)) {
            $selectedTemplates = array_keys($templates);
        }

        // Get max sort order for the unit
        $maxSort = static::where('unit_kompetensi_id', $unitId)->max('sort_order') ?? 0;

        foreach ($selectedTemplates as $index => $templateIndex) {
            if (isset($templates[$templateIndex])) {
                $template = $templates[$templateIndex];

                $imported[] = static::create([
                    'unit_kompetensi_id' => $unitId,
                    'document_name' => $template['name'],
                    'document_description' => $template['description'],
                    'sort_order' => $maxSort + $index + 1,
                    'is_required' => $template['is_required'],
                    'is_active' => true
                ]);
            }
        }

        return $imported;
    }

    /* ===================== VALIDATION METHODS ===================== */

    /**
     * Validate document name uniqueness within unit
     */
    public static function validateUniqueInUnit($unitId, $documentName, $excludeId = null)
    {
        $query = static::where('unit_kompetensi_id', $unitId)
            ->where('document_name', $documentName);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->doesntExist();
    }

    /**
     * Validate bulk documents before creation
     */
    public static function validateBulkDocuments($unitId, $documents)
    {
        $errors = [];
        $documentNames = [];

        foreach ($documents as $index => $document) {
            $documentName = $document['name'] ?? '';

            // Check required fields
            if (empty($documentName)) {
                $errors[] = [
                    'index' => $index,
                    'error' => 'Document name is required'
                ];
                continue;
            }

            // Check for duplicates within the batch
            if (in_array($documentName, $documentNames)) {
                $errors[] = [
                    'index' => $index,
                    'document_name' => $documentName,
                    'error' => 'Duplicate document name in batch'
                ];
            } else {
                $documentNames[] = $documentName;
            }

            // Check for existing documents in unit
            if (!static::validateUniqueInUnit($unitId, $documentName)) {
                $errors[] = [
                    'index' => $index,
                    'document_name' => $documentName,
                    'error' => 'Document name already exists in this unit'
                ];
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
