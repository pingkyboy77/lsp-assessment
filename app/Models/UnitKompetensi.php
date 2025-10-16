<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitKompetensi extends Model
{
    use HasFactory;

    protected $fillable = ['certification_scheme_id', 'kode_unit', 'judul_unit', 'standar_kompetensi_kerja', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /* ===================== MAIN RELATIONSHIPS ===================== */

    public function certificationScheme()
    {
        return $this->belongsTo(CertificationScheme::class);
    }

    public function elemenKompetensis()
    {
        return $this->hasMany(ElemenKompetensi::class)->orderBy('sort_order');
    }

    public function activeElemenKompetensis()
    {
        return $this->hasMany(ElemenKompetensi::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Portfolio files (document requirements) yang dimiliki oleh unit ini
     * Ini adalah daftar nama dokumen yang wajib/opsional untuk unit kompetensi
     */
    public function portfolioFiles()
    {
        return $this->hasMany(PortfolioFile::class)->orderBy('sort_order');
    }

    /**
     * Portfolio files yang aktif
     */
    public function activePortfolioFiles()
    {
        return $this->hasMany(PortfolioFile::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Portfolio files yang wajib
     */
    public function requiredPortfolioFiles()
    {
        return $this->hasMany(PortfolioFile::class)->where('is_required', true)->orderBy('sort_order');
    }

    /**
     * Portfolio files yang opsional
     */
    public function optionalPortfolioFiles()
    {
        return $this->hasMany(PortfolioFile::class)->where('is_required', false)->orderBy('sort_order');
    }

    /* ===================== KELOMPOK KERJA RELATIONSHIPS ===================== */

    public function kelompokKerjas()
    {
        return $this->belongsToMany(KelompokKerja::class, 'kelompok_kerja_unit_kompetensi')
            ->withPivot(['sort_order', 'is_active'])
            ->withTimestamps();
    }

    public function activeKelompokKerjas()
    {
        return $this->kelompokKerjas()->wherePivot('is_active', true);
    }

    /* ===================== THROUGH RELATIONSHIPS ===================== */

    public function kriteriaKerjas()
    {
        return $this->hasManyThrough(KriteriaKerja::class, ElemenKompetensi::class);
    }

    public function activeKriteriaKerjas()
    {
        return $this->hasManyThrough(KriteriaKerja::class, ElemenKompetensi::class)
            ->where('kriteria_kerjas.is_active', true)
            ->orderBy('elemen_kompetensis.sort_order')
            ->orderBy('kriteria_kerjas.sort_order');
    }

    /* ===================== SCOPES ===================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByScheme($query, $schemeId)
    {
        return $query->where('certification_scheme_id', $schemeId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('kode_unit');
    }

    public function scopeByKelompokKerja($query, $kelompokKerjaId, $activeOnly = false)
    {
        $query->whereHas('kelompokKerjas', function ($q) use ($kelompokKerjaId, $activeOnly) {
            $q->where('kelompok_kerjas.id', $kelompokKerjaId);

            if ($activeOnly) {
                $q->wherePivot('is_active', true);
            }
        });

        return $query;
    }

    public function scopeWithoutKelompokKerja($query)
    {
        return $query->doesntHave('kelompokKerjas');
    }

    /* ===================== ACCESSORS ===================== */

    public function getFullKodeAttribute()
    {
        return $this->kode_unit . ' - ' . $this->judul_unit;
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function getElemenCountAttribute()
    {
        return $this->elemenKompetensis()->count();
    }

    public function getActiveElemenCountAttribute()
    {
        return $this->activeElemenKompetensis()->count();
    }

    public function getKriteriaCountAttribute()
    {
        return $this->kriteriaKerjas()->count();
    }

    public function getActiveKriteriaCountAttribute()
    {
        return $this->activeKriteriaKerjas()->count();
    }

    /**
     * Accessor untuk jumlah portfolio files (document requirements)
     */
    public function getPortfolioFileCountAttribute()
    {
        return $this->portfolioFiles()->count();
    }

    public function getActivePortfolioFileCountAttribute()
    {
        return $this->activePortfolioFiles()->count();
    }

    public function getRequiredPortfolioFileCountAttribute()
    {
        return $this->requiredPortfolioFiles()->count();
    }

    public function getOptionalPortfolioFileCountAttribute()
    {
        return $this->optionalPortfolioFiles()->count();
    }

    public function getActiveKelompokKerjasAttribute()
    {
        return $this->kelompokKerjas()
            ->withPivot(['sort_order', 'is_active', 'created_at', 'updated_at'])
            ->wherePivot('is_active', true)
            ->orderBy('kelompok_kerja_unit_kompetensi.sort_order')
            ->get();
    }

    /* ===================== PORTFOLIO FILE MANAGEMENT ===================== */

    /**
     * Add document requirement to this unit
     */
    public function addPortfolioFile($documentName, $description = null, $isRequired = true)
    {
        return PortfolioFile::createForUnit($this->id, $documentName, $description, $isRequired);
    }

    /**
     * Batch add document requirements
     */
    public function batchAddPortfolioFiles($documents)
    {
        return PortfolioFile::bulkCreateForUnit($this->id, $documents);
    }

    /**
     * Import template document requirements
     */
    public function importTemplatePortfolioFiles($templateIndexes = [])
    {
        return PortfolioFile::importTemplateToUnit($this->id, $templateIndexes);
    }

    /**
     * Copy document requirements from another unit
     */
    public function copyPortfolioFilesFrom($sourceUnitId, $includeInactive = false)
    {
        return PortfolioFile::copyFromUnit($sourceUnitId, $this->id, $includeInactive);
    }

    /**
     * Reorder document requirements
     */
    public function reorderPortfolioFiles($fileIds)
    {
        return PortfolioFile::reorderForUnit($this->id, $fileIds);
    }

    /**
     * Toggle portfolio file status
     */
    public function togglePortfolioFileStatus($fileId)
    {
        $file = $this->portfolioFiles()->find($fileId);

        if (!$file) {
            throw new \Exception('Portfolio file tidak ditemukan dalam unit kompetensi ini.');
        }

        return $file->toggleStatus();
    }

    /**
     * Toggle portfolio file requirement status
     */
    public function togglePortfolioFileRequirement($fileId)
    {
        $file = $this->portfolioFiles()->find($fileId);

        if (!$file) {
            throw new \Exception('Portfolio file tidak ditemukan dalam unit kompetensi ini.');
        }

        return $file->toggleRequirement();
    }

    /**
     * Update portfolio file document info
     */
    public function updatePortfolioFileDocument($fileId, $documentName, $description = null)
    {
        $file = $this->portfolioFiles()->find($fileId);

        if (!$file) {
            throw new \Exception('Portfolio file tidak ditemukan dalam unit kompetensi ini.');
        }

        return $file->updateDocument($documentName, $description);
    }

    /**
     * Remove portfolio file (document requirement)
     */
    public function removePortfolioFile($fileId)
    {
        $file = $this->portfolioFiles()->find($fileId);

        if (!$file) {
            throw new \Exception('Portfolio file tidak ditemukan dalam unit kompetensi ini.');
        }

        return $file->delete();
    }

    /**
     * Get portfolio file statistics
     */
    public function getPortfolioFileStats()
    {
        return PortfolioFile::getStatsForUnit($this->id);
    }

    /**
     * Get portfolio files by requirement type
     */
    public function getPortfolioFilesByRequirement($required = null)
    {
        $query = $this->portfolioFiles();

        if ($required !== null) {
            $query->where('is_required', $required);
        }

        return $query->ordered()->get();
    }

    /**
     * Validate portfolio file names uniqueness
     */
    public function validatePortfolioFileName($documentName, $excludeId = null)
    {
        return PortfolioFile::validateUniqueInUnit($this->id, $documentName, $excludeId);
    }

    /* ===================== KELOMPOK KERJA MANAGEMENT ===================== */
    // (Keep the existing kelompok kerja management methods unchanged)

    public function addToKelompokKerja($kelompokKerjaId, $sortOrder = null, $isActive = true)
    {
        if (!$this->kelompokKerjas()->where('kelompok_kerja_id', $kelompokKerjaId)->exists()) {
            if ($sortOrder === null) {
                $maxSort = $this->kelompokKerjas()->where('kelompok_kerja_id', $kelompokKerjaId)->max('pivot_sort_order') ?? 0;
                $sortOrder = $maxSort + 1;
            }

            $this->kelompokKerjas()->attach($kelompokKerjaId, [
                'sort_order' => $sortOrder,
                'is_active' => $isActive,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function removeFromKelompokKerja($kelompokKerjaId)
    {
        $this->kelompokKerjas()->detach($kelompokKerjaId);
    }

    public function syncKelompokKerjas(array $kelompokKerjaIds, array $pivotData = [])
    {
        $syncData = [];

        foreach ($kelompokKerjaIds as $index => $kelompokId) {
            $syncData[$kelompokId] = [
                'sort_order' => $pivotData[$kelompokId]['sort_order'] ?? $index + 1,
                'is_active' => $pivotData[$kelompokId]['is_active'] ?? true,
                'created_at' => $pivotData[$kelompokId]['created_at'] ?? now(),
                'updated_at' => now(),
            ];
        }

        return $this->kelompokKerjas()->sync($syncData);
    }

    public function attachKelompokKerja($kelompokKerjaId, array $pivotData = [])
    {
        $maxSort = $this->kelompokKerjas()->max('kelompok_kerja_unit_kompetensi.sort_order') ?? 0;

        $defaultPivotData = [
            'sort_order' => $maxSort + 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $pivotData = array_merge($defaultPivotData, $pivotData);

        return $this->kelompokKerjas()->attach($kelompokKerjaId, $pivotData);
    }

    public function updateKelompokKerjaPivot($kelompokKerjaId, array $pivotData)
    {
        $pivotData['updated_at'] = now();

        return $this->kelompokKerjas()->updateExistingPivot($kelompokKerjaId, $pivotData);
    }

    public function toggleKelompokKerjaStatus($kelompokKerjaId)
    {
        $currentRecord = $this->kelompokKerjas()
            ->withPivot(['is_active'])
            ->where('kelompok_kerjas.id', $kelompokKerjaId)
            ->first();

        if (!$currentRecord) {
            throw new \Exception('Kelompok kerja tidak ditemukan dalam unit kompetensi ini.');
        }

        $newStatus = !$currentRecord->pivot->is_active;

        return $this->updateKelompokKerjaPivot($kelompokKerjaId, [
            'is_active' => $newStatus,
        ]);
    }

    public function reorderKelompokKerjas(array $kelompokKerjaIds)
    {
        DB::beginTransaction();
        try {
            foreach ($kelompokKerjaIds as $index => $kelompokKerjaId) {
                $this->updateKelompokKerjaPivot($kelompokKerjaId, [
                    'sort_order' => $index + 1,
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function activeKelompokKerjasOrdered()
    {
        return $this->kelompokKerjas()
            ->withPivot(['sort_order', 'is_active', 'created_at'])
            ->wherePivot('is_active', true)
            ->orderBy('kelompok_kerja_unit_kompetensi.sort_order');
    }

    public function getKelompokKerjaStats()
    {
        $total = $this->kelompokKerjas()->count();
        $active = $this->kelompokKerjas()->wherePivot('is_active', true)->count();
        $inactive = $total - $active;

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }

    public function hasKelompokKerja($kelompokKerjaId, $activeOnly = false)
    {
        $query = $this->kelompokKerjas()->where('kelompok_kerjas.id', $kelompokKerjaId);

        if ($activeOnly) {
            $query->wherePivot('is_active', true);
        }

        return $query->exists();
    }

    /* ===================== HELPER METHODS ===================== */

    public function reorderElemen($elemenIds)
    {
        foreach ($elemenIds as $index => $elemenId) {
            $this->elemenKompetensis()
                ->where('id', $elemenId)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function duplicate($newSchemeId = null)
    {
        DB::beginTransaction();
        try {
            $newUnit = $this->replicate();
            if ($newSchemeId) {
                $newUnit->certification_scheme_id = $newSchemeId;
            }
            $newUnit->save();

            // Duplicate elemen kompetensi
            foreach ($this->elemenKompetensis as $elemen) {
                $elemen->duplicateToUnit($newUnit->id);
            }

            // Duplicate portfolio files (document requirements)
            foreach ($this->portfolioFiles as $file) {
                $file->duplicateToUnit($newUnit->id);
            }

            DB::commit();
            return $newUnit;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function duplicateWithKelompokKerjas()
    {
        DB::beginTransaction();
        try {
            // Get original kelompok kerja data
            $originalKelompokKerjas = $this->kelompokKerjas()
                ->withPivot(['sort_order', 'is_active'])
                ->get();

            // Duplicate the unit first
            $newUnit = $this->duplicate();

            // Sync kelompok kerja with same pivot data
            if ($originalKelompokKerjas->isNotEmpty()) {
                $syncData = [];
                foreach ($originalKelompokKerjas as $kelompok) {
                    $syncData[$kelompok->id] = [
                        'sort_order' => $kelompok->pivot->sort_order,
                        'is_active' => $kelompok->pivot->is_active,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $newUnit->kelompokKerjas()->sync($syncData);
            }

            DB::commit();
            return $newUnit;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /* ===================== COMPREHENSIVE STATISTICS ===================== */

    /**
     * Get comprehensive statistics for this unit
     */
    public function getComprehensiveStats()
    {
        $elemenStats = [
            'total' => $this->elemen_count,
            'active' => $this->active_elemen_count,
        ];

        $kriteriaStats = [
            'total' => $this->kriteria_count,
            'active' => $this->active_kriteria_count,
        ];

        $portfolioStats = $this->getPortfolioFileStats();
        $kelompokStats = $this->getKelompokKerjaStats();

        return [
            'unit_info' => [
                'id' => $this->id,
                'kode_unit' => $this->kode_unit,
                'judul_unit' => $this->judul_unit,
                'is_active' => $this->is_active,
            ],
            'elemen' => $elemenStats,
            'kriteria' => $kriteriaStats,
            'portfolio_files' => $portfolioStats,
            'kelompok_kerja' => $kelompokStats,
            'completion_percentage' => $this->getCompletionPercentage(),
        ];
    }

    /**
     * Calculate completion percentage based on various criteria
     */
    public function getCompletionPercentage()
    {
        $criteria = [
            'has_kode_unit' => !empty($this->kode_unit),
            'has_judul_unit' => !empty($this->judul_unit),
            'has_elemen' => $this->elemen_count > 0,
            'has_kriteria' => $this->kriteria_count > 0,
            'has_portfolio_files' => $this->portfolio_file_count > 0,
            'has_kelompok' => $this->getKelompokKerjaStats()['total'] > 0,
        ];

        $completed = array_filter($criteria);
        return round((count($completed) / count($criteria)) * 100);
    }

    /* ===================== VALIDATION METHODS ===================== */

    /**
     * Validate unit completeness
     */
    public function validateCompleteness()
    {
        $issues = [];

        if (empty($this->kode_unit)) {
            $issues[] = 'Kode unit belum diisi';
        }

        if (empty($this->judul_unit)) {
            $issues[] = 'Judul unit belum diisi';
        }

        if ($this->elemen_count === 0) {
            $issues[] = 'Belum ada elemen kompetensi';
        }

        if ($this->kriteria_count === 0) {
            $issues[] = 'Belum ada kriteria kerja';
        }

        if ($this->portfolio_file_count === 0) {
            $issues[] = 'Belum ada dokumen portofolio yang diperlukan';
        }

        if ($this->required_portfolio_file_count === 0 && $this->portfolio_file_count > 0) {
            $issues[] = 'Belum ada dokumen portofolio yang diwajibkan';
        }

        if ($this->getKelompokKerjaStats()['total'] === 0) {
            $issues[] = 'Belum terhubung dengan kelompok kerja';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
            'completion_score' => $this->getCompletionPercentage()
        ];
    }

    /**
     * Check if unit can be activated
     */
    public function canBeActivated()
    {
        $validation = $this->validateCompleteness();
        return $validation['is_valid'] && $validation['completion_score'] >= 80;
    }

    /* ===================== EXPORT/IMPORT METHODS ===================== */

    /**
     * Export unit data for backup/transfer
     */
    public function exportData()
    {
        return [
            'unit_info' => [
                'kode_unit' => $this->kode_unit,
                'judul_unit' => $this->judul_unit,
                'standar_kompetensi_kerja' => $this->standar_kompetensi_kerja,
                'sort_order' => $this->sort_order,
                'is_active' => $this->is_active,
            ],
            'elemen_kompetensi' => $this->elemenKompetensis->map(function ($elemen) {
                return [
                    'kode_elemen' => $elemen->kode_elemen,
                    'nama_elemen' => $elemen->nama_elemen,
                    'sort_order' => $elemen->sort_order,
                    'is_active' => $elemen->is_active,
                    'kriteria_kerja' => $elemen->kriteriaKerjas->map(function ($kriteria) {
                        return [
                            'kode_kriteria' => $kriteria->kode_kriteria,
                            'kriteria' => $kriteria->kriteria,
                            'sort_order' => $kriteria->sort_order,
                            'is_active' => $kriteria->is_active,
                        ];
                    }),
                ];
            }),
            'portfolio_files' => $this->portfolioFiles->map(function ($file) {
                return [
                    'document_name' => $file->document_name,
                    'document_description' => $file->document_description,
                    'is_required' => $file->is_required,
                    'sort_order' => $file->sort_order,
                    'is_active' => $file->is_active,
                ];
            }),
            'kelompok_kerja' => $this->kelompokKerjas->map(function ($kelompok) {
                return [
                    'id' => $kelompok->id,
                    'nama' => $kelompok->nama,
                    'sort_order' => $kelompok->pivot->sort_order,
                    'is_active' => $kelompok->pivot->is_active,
                ];
            }),
            'statistics' => $this->getComprehensiveStats(),
        ];
    }

    /* ===================== QUERY OPTIMIZATION METHODS ===================== */

    /**
     * Load complete unit data with all relationships
     */
    public function loadCompleteData()
    {
        return $this->load([
            'certificationScheme',
            'elemenKompetensis.kriteriaKerjas',
            'portfolioFiles',
            'kelompokKerjas' => function ($query) {
                $query->withPivot(['sort_order', 'is_active']);
            }
        ]);
    }

    /**
     * Load basic unit data
     */
    public function loadBasicData()
    {
        return $this->load([
            'certificationScheme:id,nama,code_1',
            'elemenKompetensis:id,unit_kompetensi_id,nama_elemen,is_active',
            'portfolioFiles:id,unit_kompetensi_id,document_name,is_required,is_active'
        ]);
    }

    /* ===================== UTILITY METHODS ===================== */

    /**
     * Generate summary array for API responses
     */
    public function toSummaryArray()
    {
        return [
            'id' => $this->id,
            'kode_unit' => $this->kode_unit,
            'judul_unit' => $this->judul_unit,
            'full_kode' => $this->full_kode,
            'status' => $this->status_text,
            'completion' => $this->getCompletionPercentage(),
            'counts' => [
                'elemen' => $this->elemen_count,
                'kriteria' => $this->kriteria_count,
                'portfolio_files' => $this->portfolio_file_count,
                'required_portfolio_files' => $this->required_portfolio_file_count,
                'optional_portfolio_files' => $this->optional_portfolio_file_count,
                'kelompok_kerja' => $this->getKelompokKerjaStats()['total']
            ],
            'portfolio_requirements' => [
                'total_requirements' => $this->portfolio_file_count,
                'required_documents' => $this->required_portfolio_file_count,
                'optional_documents' => $this->optional_portfolio_file_count,
                'active_requirements' => $this->active_portfolio_file_count,
            ],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Create detailed report for this unit
     */
    public function generateDetailedReport()
    {
        $validation = $this->validateCompleteness();
        $stats = $this->getComprehensiveStats();

        return [
            'unit_summary' => [
                'kode' => $this->kode_unit,
                'judul' => $this->judul_unit,
                'status' => $this->status_text,
                'completion' => $this->getCompletionPercentage(),
                'scheme' => $this->certificationScheme?->full_name,
            ],
            'content_analysis' => [
                'elemen_kompetensi' => [
                    'total' => $stats['elemen']['total'],
                    'active' => $stats['elemen']['active'],
                    'completion_rate' => $stats['elemen']['total'] > 0 ?
                        round(($stats['elemen']['active'] / $stats['elemen']['total']) * 100) : 0
                ],
                'kriteria_kerja' => [
                    'total' => $stats['kriteria']['total'],
                    'active' => $stats['kriteria']['active'],
                    'avg_per_element' => $stats['elemen']['total'] > 0 ?
                        round($stats['kriteria']['total'] / $stats['elemen']['total'], 1) : 0
                ],
                'portfolio_requirements' => [
                    'total' => $stats['portfolio_files']['total'],
                    'active' => $stats['portfolio_files']['active'],
                    'required' => $stats['portfolio_files']['required'],
                    'optional' => $stats['portfolio_files']['optional'],
                    'required_active' => $stats['portfolio_files']['required_active'],
                    'optional_active' => $stats['portfolio_files']['optional_active'],
                ]
            ],
            'relationships' => [
                'kelompok_kerja' => [
                    'connected_count' => $stats['kelompok_kerja']['total'],
                    'active_connections' => $stats['kelompok_kerja']['active']
                ]
            ],
            'validation' => $validation,
            'recommendations' => $this->generateRecommendations($validation, $stats)
        ];
    }

    /**
     * Generate recommendations based on unit analysis
     */
    private function generateRecommendations($validation, $stats)
    {
        $recommendations = [];

        if (!$validation['is_valid']) {
            $recommendations[] = 'Lengkapi komponen yang masih kurang sebelum mengaktifkan unit';
        }

        if ($stats['elemen']['total'] < 3) {
            $recommendations[] = 'Pertimbangkan menambah elemen kompetensi untuk unit yang lebih komprehensif';
        }

        if ($stats['kriteria']['total'] > 0 && ($stats['kriteria']['total'] / $stats['elemen']['total']) < 2) {
            $recommendations[] = 'Tambahkan lebih banyak kriteria kerja untuk setiap elemen kompetensi';
        }

        if ($stats['portfolio_files']['total'] === 0) {
            $recommendations[] = 'Tentukan dokumen portofolio yang diperlukan untuk unit ini';
        }

        if ($stats['portfolio_files']['total'] > 0 && $stats['portfolio_files']['required'] === 0) {
            $recommendations[] = 'Tentukan dokumen mana yang wajib untuk penilaian';
        }

        if ($stats['portfolio_files']['total'] > 0 && $stats['portfolio_files']['active'] === 0) {
            $recommendations[] = 'Aktifkan dokumen portofolio yang sudah ditentukan';
        }

        if ($stats['portfolio_files']['total'] > 15) {
            $recommendations[] = 'Pertimbangkan mengurangi jumlah dokumen portofolio untuk kemudahan manajemen';
        }

        if ($stats['kelompok_kerja']['total'] === 0) {
            $recommendations[] = 'Hubungkan unit dengan kelompok kerja yang relevan';
        }

        return $recommendations;
    }

    /* ===================== PORTFOLIO REQUIREMENTS HELPERS ===================== */

    /**
     * Get portfolio files grouped by requirement type
     */
    public function getPortfolioFilesGrouped()
    {
        $files = $this->portfolioFiles()->active()->ordered()->get();

        return [
            'required' => $files->filter(fn($file) => $file->is_required)->values(),
            'optional' => $files->filter(fn($file) => !$file->is_required)->values()
        ];
    }

    /**
     * Get portfolio requirements summary
     */
    public function getPortfolioRequirementsSummary()
    {
        $stats = $this->getPortfolioFileStats();
        $grouped = $this->getPortfolioFilesGrouped();

        return [
            'summary' => [
                'total_requirements' => $stats['total'],
                'active_requirements' => $stats['active'],
                'required_documents' => $stats['required'],
                'optional_documents' => $stats['optional'],
            ],
            'requirements' => [
                'required' => $grouped['required']->map(function ($file) {
                    return $file->getPreviewData();
                }),
                'optional' => $grouped['optional']->map(function ($file) {
                    return $file->getPreviewData();
                }),
            ],
            'validation' => [
                'has_requirements' => $stats['total'] > 0,
                'has_required_documents' => $stats['required'] > 0,
                'has_active_requirements' => $stats['active'] > 0,
                'completion_percentage' => $stats['total'] > 0 ?
                    round(($stats['active'] / $stats['total']) * 100) : 0
            ]
        ];
    }

    /**
     * Check if unit has specific document requirement
     */
    public function hasDocumentRequirement($documentName, $activeOnly = true)
    {
        $query = $this->portfolioFiles()->where('document_name', 'like', "%{$documentName}%");

        if ($activeOnly) {
            $query->active();
        }

        return $query->exists();
    }

    /**
     * Get required documents that are missing (inactive or not defined)
     */
    public function getMissingRequiredDocuments()
    {
        $required = $this->requiredPortfolioFiles()->get();
        $inactive = $required->filter(fn($file) => !$file->is_active);

        return [
            'total_required' => $required->count(),
            'inactive_required' => $inactive->count(),
            'missing_documents' => $inactive->map(function ($file) {
                return [
                    'id' => $file->id,
                    'document_name' => $file->document_name,
                    'description' => $file->document_description,
                    'sort_order' => $file->sort_order
                ];
            })
        ];
    }

    /**
     * Validate document requirements completeness
     */
    public function validateDocumentRequirements()
    {
        $stats = $this->getPortfolioFileStats();
        $missing = $this->getMissingRequiredDocuments();
        $issues = [];

        if ($stats['total'] === 0) {
            $issues[] = 'Belum ada dokumen portofolio yang ditentukan';
        }

        if ($stats['required'] === 0) {
            $issues[] = 'Belum ada dokumen yang diwajibkan';
        }

        if ($missing['inactive_required'] > 0) {
            $issues[] = "Ada {$missing['inactive_required']} dokumen wajib yang tidak aktif";
        }

        if ($stats['required'] > 10) {
            $issues[] = 'Terlalu banyak dokumen wajib (>10), pertimbangkan untuk mengurangi';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
            'statistics' => $stats,
            'missing_required' => $missing,
            'recommendations' => $this->generateDocumentRecommendations($stats, $missing)
        ];
    }

    /**
     * Generate recommendations for document requirements
     */
    private function generateDocumentRecommendations($stats, $missing)
    {
        $recommendations = [];

        if ($stats['total'] === 0) {
            $recommendations[] = 'Import template dokumen standar untuk memulai';
        }

        if ($stats['total'] > 0 && $stats['required'] === 0) {
            $recommendations[] = 'Tentukan dokumen mana yang wajib untuk penilaian';
        }

        if ($missing['inactive_required'] > 0) {
            $recommendations[] = 'Aktifkan semua dokumen wajib atau ubah status menjadi opsional';
        }

        if ($stats['required'] > 8) {
            $recommendations[] = 'Pertimbangkan mengurangi dokumen wajib untuk menghindari beban berlebihan pada peserta';
        }

        if ($stats['optional'] === 0 && $stats['required'] > 0) {
            $recommendations[] = 'Tambahkan beberapa dokumen opsional untuk memberikan fleksibilitas';
        }

        if ($stats['total'] < 3) {
            $recommendations[] = 'Pertimbangkan menambah lebih banyak jenis dokumen untuk penilaian yang komprehensif';
        }

        return $recommendations;
    }

    /**
     * Bulk update portfolio files requirement status
     */
    public function bulkUpdatePortfolioRequirements($updates)
    {
        DB::beginTransaction();
        try {
            $updated = [];
            $errors = [];

            foreach ($updates as $update) {
                $fileId = $update['file_id'];
                $isRequired = $update['is_required'] ?? null;
                $isActive = $update['is_active'] ?? null;

                $file = $this->portfolioFiles()->find($fileId);

                if (!$file) {
                    $errors[] = [
                        'file_id' => $fileId,
                        'error' => 'Portfolio file not found'
                    ];
                    continue;
                }

                $updateData = [];
                if ($isRequired !== null) {
                    $updateData['is_required'] = $isRequired;
                }
                if ($isActive !== null) {
                    $updateData['is_active'] = $isActive;
                }

                if (!empty($updateData)) {
                    $file->update($updateData);
                    $updated[] = $file->fresh();
                }
            }

            DB::commit();
            return [
                'updated' => $updated,
                'errors' => $errors,
                'success_count' => count($updated),
                'error_count' => count($errors)
            ];
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get template portfolio files that are not yet added to this unit
     */
    public function getAvailableTemplateDocuments()
    {
        $templates = PortfolioFile::getTemplateDocuments();
        $existingNames = $this->portfolioFiles()->pluck('document_name')->toArray();

        return array_filter($templates, function ($template) use ($existingNames) {
            return !in_array($template['name'], $existingNames);
        });
    }

    /**
     * Suggest portfolio files based on certification scheme or similar units
     */
    public function suggestPortfolioFiles()
    {
        $suggestions = [];

        // Get templates that haven't been added
        $availableTemplates = $this->getAvailableTemplateDocuments();
        if (!empty($availableTemplates)) {
            $suggestions['templates'] = [
                'type' => 'Standard Templates',
                'description' => 'Template dokumen yang umum digunakan',
                'documents' => array_values($availableTemplates)
            ];
        }

        // Get portfolio files from other units in same scheme
        $similarUnits = UnitKompetensi::where('certification_scheme_id', $this->certification_scheme_id)
            ->where('id', '!=', $this->id)
            ->whereHas('portfolioFiles')
            ->with('portfolioFiles')
            ->get();

        if ($similarUnits->isNotEmpty()) {
            $commonDocuments = [];
            $existingNames = $this->portfolioFiles()->pluck('document_name')->toArray();

            foreach ($similarUnits as $unit) {
                foreach ($unit->portfolioFiles as $file) {
                    if (!in_array($file->document_name, $existingNames)) {
                        $key = $file->document_name;
                        if (!isset($commonDocuments[$key])) {
                            $commonDocuments[$key] = [
                                'document_name' => $file->document_name,
                                'document_description' => $file->document_description,
                                'is_required' => $file->is_required,
                                'usage_count' => 0
                            ];
                        }
                        $commonDocuments[$key]['usage_count']++;
                    }
                }
            }

            // Sort by usage count
            uasort($commonDocuments, function ($a, $b) {
                return $b['usage_count'] <=> $a['usage_count'];
            });

            if (!empty($commonDocuments)) {
                $suggestions['similar_units'] = [
                    'type' => 'From Similar Units',
                    'description' => 'Dokumen yang digunakan unit lain dalam skema yang sama',
                    'documents' => array_values(array_slice($commonDocuments, 0, 10))
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Apply suggested portfolio files to this unit
     */
    public function applySuggestedPortfolioFiles($suggestions, $type = 'templates')
    {
        $applied = [];
        $errors = [];

        foreach ($suggestions as $suggestion) {
            try {
                // Validate document name uniqueness
                if (!$this->validatePortfolioFileName($suggestion['document_name'])) {
                    $errors[] = [
                        'document_name' => $suggestion['document_name'],
                        'error' => 'Document name already exists'
                    ];
                    continue;
                }

                $applied[] = $this->addPortfolioFile(
                    $suggestion['document_name'],
                    $suggestion['document_description'] ?? null,
                    $suggestion['is_required'] ?? true
                );
            } catch (\Exception $e) {
                $errors[] = [
                    'document_name' => $suggestion['document_name'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'applied' => $applied,
            'errors' => $errors,
            'success_count' => count($applied),
            'error_count' => count($errors)
        ];
    }

    /**
     * Generate portfolio checklist for assessment purposes
     */
    public function generatePortfolioChecklist()
    {
        $required = $this->requiredPortfolioFiles()->active()->ordered()->get();
        $optional = $this->optionalPortfolioFiles()->active()->ordered()->get();

        return [
            'unit_info' => [
                'kode_unit' => $this->kode_unit,
                'judul_unit' => $this->judul_unit,
                'scheme' => $this->certificationScheme?->full_name
            ],
            'required_documents' => $required->map(function ($file, $index) {
                return [
                    'no' => $index + 1,
                    'document_name' => $file->document_name,
                    'description' => $file->document_description,
                    'status' => 'Wajib',
                    'checkbox' => '☐'
                ];
            }),
            'optional_documents' => $optional->map(function ($file, $index) {
                return [
                    'no' => count($required) + $index + 1,
                    'document_name' => $file->document_name,
                    'description' => $file->document_description,
                    'status' => 'Opsional',
                    'checkbox' => '☐'
                ];
            }),
            'summary' => [
                'total_documents' => $required->count() + $optional->count(),
                'required_count' => $required->count(),
                'optional_count' => $optional->count(),
                'generated_at' => now()->format('d/m/Y H:i:s')
            ]
        ];
    }

    /**
     * Export portfolio requirements for external use
     */
    public function exportPortfolioRequirements($format = 'array')
    {
        $data = [
            'unit' => [
                'id' => $this->id,
                'kode_unit' => $this->kode_unit,
                'judul_unit' => $this->judul_unit,
                'scheme' => $this->certificationScheme?->nama,
                'scheme_code' => $this->certificationScheme?->code_1,
            ],
            'requirements' => $this->portfolioFiles()->active()->ordered()->get()->map(function ($file) {
                return [
                    'document_name' => $file->document_name,
                    'document_description' => $file->document_description,
                    'is_required' => $file->is_required,
                    'requirement_text' => $file->requirement_text,
                    'sort_order' => $file->sort_order
                ];
            }),
            'statistics' => $this->getPortfolioFileStats(),
            'exported_at' => now()->toISOString()
        ];

        if ($format === 'json') {
            return json_encode($data, JSON_PRETTY_PRINT);
        }

        if ($format === 'csv') {
            $csv = "Unit Kompetensi: {$this->kode_unit} - {$this->judul_unit}\n";
            $csv .= "No,Nama Dokumen,Deskripsi,Status,Urutan\n";

            foreach ($data['requirements'] as $index => $req) {
                $csv .= sprintf(
                    "%d,\"%s\",\"%s\",\"%s\",%d\n",
                    $index + 1,
                    $req['document_name'],
                    $req['document_description'] ?? '',
                    $req['requirement_text'],
                    $req['sort_order']
                );
            }

            return $csv;
        }

        return $data;
    }
}
