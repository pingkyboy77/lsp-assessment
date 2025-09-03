<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificationScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'code_1',
        'code_2', 
        'fee_tanda_tangan',
        'skema_ing',
        'jenjang',
        'is_active',
        'requirement_template_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fee_tanda_tangan' => 'decimal:2',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function field()
    {
        return $this->belongsTo(Field::class, 'code_2', 'code_2');
    }

    public function requirementTemplate()
    {
        return $this->belongsTo(RequirementTemplate::class, 'requirement_template_id');
    }

    public function requirementTemplates()
    {
        return $this->belongsToMany(RequirementTemplate::class, 'certification_scheme_requirements')
            ->withPivot(['sort_order', 'is_active'])
            ->withTimestamps()
            ->wherePivot('is_active', true)
            ->orderBy('sort_order');
    }

    public function allRequirementTemplates()
    {
        return $this->belongsToMany(RequirementTemplate::class, 'certification_scheme_requirements')
            ->withPivot(['sort_order', 'is_active'])
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    public function kelompokKerjas()
    {
        return $this->hasMany(KelompokKerja::class)->orderBy('sort_order');
    }

    public function activeKelompokKerjas()
    {
        return $this->hasMany(KelompokKerja::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function units()
    {
        return $this->hasMany(UnitKompetensi::class)->orderBy('sort_order');
    }

    public function unitKompetensis()
    {
        return $this->units();
    }

    public function activeUnits()
    {
        return $this->hasMany(UnitKompetensi::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function activeUnitKompetensis()
    {
        return $this->activeUnits();
    }

    public function elemenKompetensis()
    {
        return $this->hasManyThrough(ElemenKompetensi::class, UnitKompetensi::class);
    }

    public function kriteriaKerjas()
    {
        return $this->hasManyThrough(
            KriteriaKerja::class, 
            ElemenKompetensi::class,
            'unit_kompetensi_id',
            'elemen_kompetensi_id',
            'id',
            'id'
        )->join('unit_kompetensis', 'elemen_kompetensis.unit_kompetensi_id', '=', 'unit_kompetensis.id')
         ->where('unit_kompetensis.certification_scheme_id', $this->id);
    }

    public function buktiPortofolios()
    {
        return $this->hasManyThrough(BuktiPortofolio::class, KelompokKerja::class);
    }

    /* ===================== SCOPES ===================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode2($query, $code2)
    {
        return $query->where('code_2', $code2);
    }

    public function scopeByJenjang($query, $jenjang)
    {
        return $query->where('jenjang', $jenjang);
    }

    public function scopeComplete($query)
    {
        return $query->whereHas('units')
            ->whereHas('kelompokKerjas');
    }

    public function scopeIncomplete($query)
    {
        return $query->where(function ($q) {
            $q->doesntHave('units')
              ->orDoesntHave('kelompokKerjas');
        });
    }

    /* ===================== ACCESSORS ===================== */

    public function getFullNameAttribute()
    {
        return $this->code_1 . ' - ' . $this->nama;
    }

    public function getFormattedFeeAttribute()
    {
        return $this->fee_tanda_tangan 
            ? 'Rp ' . number_format($this->fee_tanda_tangan, 0, ',', '.') 
            : '-';
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function getJenjangColorAttribute()
    {
        return match ($this->jenjang) {
            'Utama' => 'danger',
            'Madya' => 'warning', 
            'Menengah' => 'info',
            default => 'secondary'
        };
    }

    /* ===================== STATISTICS ===================== */

    public function getUnitKompetensiCountAttribute()
    {
        return $this->units()->count();
    }

    public function getActiveUnitKompetensiCountAttribute()
    {
        return $this->activeUnits()->count();
    }

    public function getKelompokKerjaCountAttribute()
    {
        return $this->kelompokKerjas()->count();
    }

    public function getActiveKelompokKerjaCountAttribute()
    {
        return $this->activeKelompokKerjas()->count();
    }

    public function getTotalElemenCountAttribute()
    {
        return $this->elemenKompetensis()->count();
    }

    public function getTotalKriteriaCountAttribute()
    {
        return $this->kriteriaKerjas()->count();
    }

    public function getTotalBuktiPortofolioCountAttribute()
    {
        return $this->buktiPortofolios()->count();
    }

    public function getStatsAttribute()
    {
        return [
            'units_total' => $this->unit_kompetensi_count,
            'units_active' => $this->active_unit_kompetensi_count,
            'kelompoks_total' => $this->kelompok_kerja_count,
            'kelompoks_active' => $this->active_kelompok_kerja_count,
            'elements_total' => $this->total_elemen_count,
            'criterias_total' => $this->total_kriteria_count,
            'portfolios_total' => $this->total_bukti_portofolio_count,
        ];
    }

    /* ===================== COMPLETION STATUS ===================== */

    public function getIsCompleteAttribute()
    {
        return $this->unit_kompetensi_count > 0 && $this->kelompok_kerja_count > 0;
    }

    public function getCompletionPercentageAttribute()
    {
        $criteria = [
            'nama' => !empty($this->nama),
            'code_1' => !empty($this->code_1),
            'has_units' => $this->unit_kompetensi_count > 0,
            'has_elements' => $this->total_elemen_count > 0,
            'has_kelompoks' => $this->kelompok_kerja_count > 0,
            'has_portfolios' => $this->total_bukti_portofolio_count > 0,
        ];

        $completed = array_filter($criteria);
        $total = count($criteria);

        return round((count($completed) / $total) * 100);
    }

    public function getProgressStatusAttribute()
    {
        return match (true) {
            $this->completion_percentage >= 100 => 'Lengkap',
            $this->completion_percentage >= 75 => 'Hampir Lengkap',
            $this->completion_percentage >= 50 => 'Sebagian',
            $this->completion_percentage >= 25 => 'Dasar',
            default => 'Belum Dimulai'
        };
    }

    public function getProgressColorAttribute()
    {
        return match (true) {
            $this->completion_percentage >= 100 => 'success',
            $this->completion_percentage >= 75 => 'info',
            $this->completion_percentage >= 50 => 'warning',
            $this->completion_percentage >= 25 => 'secondary',
            default => 'danger'
        };
    }

    /* ===================== REQUIREMENT MANAGEMENT ===================== */

    public function hasRequirements()
    {
        return $this->requirement_template_id !== null || $this->requirementTemplates()->exists();
    }

    public function getRequirementsAttribute()
    {
        $requirements = collect();
        
        if ($this->requirementTemplate?->activeItems) {
            $requirements = $requirements->merge($this->requirementTemplate->activeItems);
        }
        
        foreach ($this->requirementTemplates as $template) {
            if ($template->activeItems) {
                $requirements = $requirements->merge($template->activeItems);
            }
        }
        
        return $requirements->unique('id');
    }

    public function getAllActiveTemplatesAttribute()
    {
        $templates = collect();
        
        if ($this->requirementTemplate) {
            $templates->push($this->requirementTemplate);
        }
        
        return $templates->merge($this->requirementTemplates)->unique('id');
    }

    public function getRequirementTemplatesCountAttribute()
    {
        $count = $this->requirementTemplates()->count();
        return $this->requirement_template_id ? $count + 1 : $count;
    }

    public function getTotalRequiredDocumentsAttribute()
    {
        $total = 0;
        
        if ($this->requirementTemplate) {
            $total += $this->calculateTemplateRequiredDocs($this->requirementTemplate);
        }
        
        foreach ($this->requirementTemplates as $template) {
            $total += $this->calculateTemplateRequiredDocs($template);
        }
        
        return $total;
    }

    private function calculateTemplateRequiredDocs($template)
    {
        return match ($template->requirement_type) {
            'all_required' => $template->activeItems?->count() ?? 0,
            'choose_one' => 1,
            'choose_min' => $template->min_required ?? 1,
            default => 0
        };
    }

    /* ===================== TEMPLATE MANAGEMENT ===================== */

    public function syncRequirementTemplates(array $ids)
    {
        $syncData = [];
        foreach ($ids as $index => $id) {
            $syncData[$id] = [
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        return $this->requirementTemplates()->sync($syncData);
    }

    public function addRequirementTemplate($id, $sortOrder = null)
    {
        if ($this->requirementTemplates()->where('requirement_template_id', $id)->exists()) {
            return false;
        }

        $maxSortOrder = $this->allRequirementTemplates()->max('sort_order') ?? 0;
        $sortOrder = $sortOrder ?? $maxSortOrder + 1;
        
        return $this->requirementTemplates()->attach($id, [
            'sort_order' => $sortOrder,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function removeRequirementTemplate($id)
    {
        return $this->requirementTemplates()->detach($id);
    }

    /* ===================== VALIDATION METHODS ===================== */

    public function validateCompleteness()
    {
        $issues = [];

        if (empty($this->nama)) {
            $issues[] = 'Nama skema belum diisi';
        }

        if (empty($this->code_1)) {
            $issues[] = 'Kode skema belum diisi';
        }

        if ($this->unit_kompetensi_count === 0) {
            $issues[] = 'Belum ada unit kompetensi';
        }

        if ($this->kelompok_kerja_count === 0) {
            $issues[] = 'Belum ada kelompok kerja';
        }

        if ($this->total_elemen_count === 0) {
            $issues[] = 'Belum ada elemen kompetensi';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
            'completion_score' => $this->completion_percentage
        ];
    }

    /* ===================== EXPORT/IMPORT METHODS ===================== */

    public function exportData()
    {
        return [
            'basic_info' => [
                'nama' => $this->nama,
                'code_1' => $this->code_1,
                'code_2' => $this->code_2,
                'jenjang' => $this->jenjang,
                'fee_tanda_tangan' => $this->fee_tanda_tangan,
            ],
            'statistics' => $this->stats,
            'requirements' => $this->getAllActiveTemplatesAttribute()->map(function($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'items_count' => $template->activeItems?->count() ?? 0
                ];
            }),
            'units' => $this->activeUnits->map(function($unit) {
                return [
                    'id' => $unit->id,
                    'kode_unit' => $unit->kode_unit,
                    'judul_unit' => $unit->judul_unit,
                    'elements_count' => $unit->elemenKompetensis?->count() ?? 0
                ];
            })
        ];
    }

    public function generateReport()
    {
        $validation = $this->validateCompleteness();
        
        return [
            'scheme_info' => [
                'nama' => $this->nama,
                'code' => $this->code_1,
                'jenjang' => $this->jenjang,
                'status' => $this->status_text,
                'completion' => $this->completion_percentage,
                'progress_status' => $this->progress_status
            ],
            'content_summary' => [
                'units_count' => $this->unit_kompetensi_count,
                'active_units_count' => $this->active_unit_kompetensi_count,
                'elements_count' => $this->total_elemen_count,
                'criteria_count' => $this->total_kriteria_count,
                'kelompok_count' => $this->kelompok_kerja_count,
                'portfolio_count' => $this->total_bukti_portofolio_count
            ],
            'requirements_summary' => [
                'templates_count' => $this->requirement_templates_count,
                'total_required_docs' => $this->total_required_documents,
                'has_legacy_template' => !empty($this->requirement_template_id)
            ],
            'validation' => $validation,
            'recommendations' => $this->generateRecommendations($validation)
        ];
    }

    private function generateRecommendations($validation)
    {
        $recommendations = [];

        if (!$validation['is_valid']) {
            $recommendations[] = 'Lengkapi semua komponen yang masih kurang untuk mengaktifkan skema';
        }

        if ($this->active_unit_kompetensi_count < 3) {
            $recommendations[] = 'Pertimbangkan menambah unit kompetensi untuk skema yang lebih komprehensif';
        }

        if ($this->requirement_templates_count === 0) {
            $recommendations[] = 'Tambahkan template persyaratan untuk mempermudah proses pendaftaran';
        }

        if ($this->total_bukti_portofolio_count === 0) {
            $recommendations[] = 'Tambahkan bukti portofolio untuk setiap kelompok kerja';
        }

        return $recommendations;
    }

    /* ===================== TEMPLATE SYNC METHODS ===================== */

    public function syncRequirementTemplatesWithOrder(array $templateData)
    {
        $syncData = [];
        
        foreach ($templateData as $data) {
            $templateId = $data['id'];
            $syncData[$templateId] = [
                'sort_order' => $data['sort_order'] ?? 1,
                'is_active' => $data['is_active'] ?? true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        return $this->requirementTemplates()->sync($syncData);
    }

    public function reorderRequirementTemplates(array $orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            $this->requirementTemplates()->updateExistingPivot($id, [
                'sort_order' => $index + 1,
                'updated_at' => now()
            ]);
        }
        
        return true;
    }

    public function toggleRequirementTemplate($id, $isActive)
    {
        return $this->requirementTemplates()->updateExistingPivot($id, [
            'is_active' => $isActive,
            'updated_at' => now()
        ]);
    }

    /* ===================== QUERY OPTIMIZATION ===================== */

    public function loadCompleteData()
    {
        return $this->load([
            'units.elemenKompetensis.kriteriaKerjas',
            'kelompokKerjas.buktiPortofolios',
            'requirementTemplates.activeItems',
            'field'
        ]);
    }

    public function loadBasicData()
    {
        return $this->load([
            'units:id,certification_scheme_id,kode_unit,judul_unit,is_active',
            'kelompokKerjas:id,certification_scheme_id,nama,is_active', 
            'requirementTemplates:id,name,requirement_type'
        ]);
    }

    /* ===================== UTILITY METHODS ===================== */

    public function duplicate($newName = null)
    {
        $newScheme = $this->replicate();
        $newScheme->nama = $newName ?? $this->nama . ' (Copy)';
        $newScheme->code_1 = $this->generateUniqueCode();
        $newScheme->is_active = false;
        $newScheme->save();

        // Copy requirement templates
        foreach ($this->requirementTemplates as $template) {
            $newScheme->requirementTemplates()->attach($template->id, [
                'sort_order' => $template->pivot->sort_order,
                'is_active' => $template->pivot->is_active,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $newScheme;
    }

    private function generateUniqueCode()
    {
        $baseCode = $this->code_1;
        $counter = 1;
        
        do {
            $newCode = $baseCode . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $exists = static::where('code_1', $newCode)->exists();
            $counter++;
        } while ($exists);
        
        return $newCode;
    }

    public function archive()
    {
        return $this->update(['is_active' => false]);
    }

    public function activate()
    {
        if (!$this->is_complete) {
            return false;
        }
        
        return $this->update(['is_active' => true]);
    }

    /* ===================== SEARCH AND FILTERING ===================== */

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('code_1', 'like', "%{$search}%")
              ->orWhere('jenjang', 'like', "%{$search}%");
        });
    }

    public function scopeWithCounts($query)
    {
        return $query->withCount([
            'units',
            'activeUnits',
            'kelompokKerjas', 
            'activeKelompokKerjas',
            'elemenKompetensis',
            'kriteriaKerjas',
            'buktiPortofolios'
        ]);
    }

    /* ===================== BULK OPERATIONS ===================== */

    public static function bulkActivate(array $ids)
    {
        return static::whereIn('id', $ids)
            ->where('is_active', false)
            ->update(['is_active' => true]);
    }

    public static function bulkDeactivate(array $ids) 
    {
        return static::whereIn('id', $ids)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    public static function getActiveSchemesByJenjang()
    {
        return static::active()
            ->select('id', 'nama', 'code_1', 'jenjang')
            ->orderBy('jenjang')
            ->orderBy('nama')
            ->get()
            ->groupBy('jenjang');
    }

    /* ===================== HELPER METHODS ===================== */

    public function canBeDeleted()
    {
        // Check if scheme has any APL submissions
        return !$this->hasRelatedSubmissions();
    }

    private function hasRelatedSubmissions()
    {
        return Apl01Pendaftaran::where('certification_scheme_id', $this->id)
            ->whereIn('status', ['submitted', 'approved'])
            ->exists();
    }

    public function getSchemeCodeAttribute()
    {
        return $this->code_1;
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->jenjang} - {$this->nama}";
    }

    public function toSummaryArray()
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'code_1' => $this->code_1,
            'jenjang' => $this->jenjang,
            'status' => $this->status_text,
            'completion' => $this->completion_percentage,
            'units_count' => $this->unit_kompetensi_count,
            'requirements_count' => $this->requirement_templates_count,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i')
        ];
    }
}