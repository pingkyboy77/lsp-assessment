<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPortofolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_kompetensi_id',
        'bukti_portofolio',
        'sort_order',
        'is_active',
        'dependency_type',
        'dependency_rules',
        'group_identifier'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'dependency_rules' => 'array',
        'sort_order' => 'integer',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    /**
     * Get the unit kompetensi that owns the bukti portofolio
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
            'id', // Foreign key pada unit_kompetensis
            'id', // Foreign key pada certification_schemes  
            'unit_kompetensi_id', // Local key pada bukti_portofolios
            'certification_scheme_id' // Local key pada unit_kompetensis
        );
    }

    /**
     * Get elemen kompetensi through unit kompetensi
     */
    public function elemenKompetensis()
    {
        return $this->hasManyThrough(
            ElemenKompetensi::class,
            UnitKompetensi::class,
            'id', // Foreign key pada unit_kompetensis 
            'unit_kompetensi_id', // Foreign key pada elemen_kompetensis
            'unit_kompetensi_id', // Local key pada bukti_portofolios
            'id' // Local key pada unit_kompetensis
        );
    }

    /* ===================== SCOPES ===================== */

    /**
     * Scope to only get active bukti portofolio
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
     * Scope by dependency type
     */
    public function scopeByDependencyType($query, $type)
    {
        return $query->where('dependency_type', $type);
    }

    /**
     * Scope by group identifier
     */
    public function scopeByGroup($query, $groupId)
    {
        return $query->where('group_identifier', $groupId);
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
     * Get formatted bukti portofolio text
     */
    public function getFormattedBuktiAttribute()
    {
        return $this->sort_order . '. ' . $this->bukti_portofolio;
    }

    /**
     * Get dependency type label
     */
    public function getDependencyTypeTextAttribute()
    {
        return match ($this->dependency_type) {
            'required_with' => 'Wajib dengan syarat',
            'exclusive' => 'Eksklusif',
            'conditional' => 'Bersyarat',
            'grouped' => 'Berkelompok',
            default => 'Mandiri'
        };
    }

    /**
     * Get dependency color
     */
    public function getDependencyColorAttribute()
    {
        return match ($this->dependency_type) {
            'required_with' => 'warning',
            'exclusive' => 'danger',
            'conditional' => 'info',
            'grouped' => 'primary',
            default => 'secondary'
        };
    }

    /* ===================== DEPENDENCY VALIDATION ===================== */

    /**
     * Validate dependencies against selected bukti portofolio IDs
     */
    public function validateDependencies($selectedIds = [])
    {
        if ($this->dependency_type === 'standalone' || empty($this->dependency_rules)) {
            return true;
        }

        return match ($this->dependency_type) {
            'required_with' => $this->validateRequiredWith($selectedIds),
            'exclusive' => $this->validateExclusive($selectedIds),
            'conditional' => $this->validateConditional($selectedIds),
            'grouped' => $this->validateGrouped($selectedIds),
            default => true
        };
    }

    /**
     * Validate required_with dependency
     */
    private function validateRequiredWith($selectedIds)
    {
        $requiredIds = $this->dependency_rules['required_bukti_ids'] ?? [];

        if (empty($requiredIds)) {
            return true;
        }

        // At least one of the required bukti must be selected
        return count(array_intersect($requiredIds, $selectedIds)) > 0;
    }

    /**
     * Validate exclusive dependency  
     */
    private function validateExclusive($selectedIds)
    {
        $exclusiveIds = $this->dependency_rules['exclusive_bukti_ids'] ?? [];

        if (empty($exclusiveIds)) {
            return true;
        }

        // None of the exclusive bukti should be selected
        return count(array_intersect($exclusiveIds, $selectedIds)) === 0;
    }

    /**
     * Validate conditional dependency
     */
    private function validateConditional($selectedIds)
    {
        $conditions = $this->dependency_rules['conditions'] ?? [];

        foreach ($conditions as $condition) {
            $ifIds = $condition['if_selected'] ?? [];
            $thenIds = $condition['then_required'] ?? [];

            // If condition bukti are selected, then required bukti must also be selected
            if (count(array_intersect($ifIds, $selectedIds)) > 0) {
                if (count(array_intersect($thenIds, $selectedIds)) === 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validate grouped dependency
     */
    private function validateGrouped($selectedIds)
    {
        $groupRules = $this->dependency_rules['group_rules'] ?? [];
        $minRequired = $groupRules['min_required'] ?? 1;
        $maxAllowed = $groupRules['max_allowed'] ?? null;
        $groupIds = $groupRules['group_bukti_ids'] ?? [];

        $selectedFromGroup = array_intersect($groupIds, $selectedIds);
        $selectedCount = count($selectedFromGroup);

        // Check minimum requirement
        if ($selectedCount < $minRequired) {
            return false;
        }

        // Check maximum limit
        if ($maxAllowed !== null && $selectedCount > $maxAllowed) {
            return false;
        }

        return true;
    }

    /**
     * Get dependency validation message
     */
    public function getDependencyMessage($selectedIds = [])
    {
        if (!$this->validateDependencies($selectedIds)) {
            return match ($this->dependency_type) {
                'required_with' => 'Memerlukan bukti portofolio lain untuk dipilih terlebih dahulu',
                'exclusive' => 'Tidak dapat dipilih bersamaan dengan bukti portofolio tertentu',
                'conditional' => 'Tidak memenuhi syarat kondisional yang ditetapkan',
                'grouped' => 'Tidak memenuhi aturan grup yang ditetapkan',
                default => 'Tidak memenuhi persyaratan dependensi'
            };
        }

        return null;
    }

    /* ===================== HELPER METHODS ===================== */

    /**
     * Check if this bukti has dependencies
     */
    public function hasDependencies()
    {
        return $this->dependency_type !== 'standalone' && !empty($this->dependency_rules);
    }

    /**
     * Get related bukti portofolio IDs from dependency rules
     */
    public function getRelatedBuktiIds()
    {
        if (!$this->hasDependencies()) {
            return [];
        }

        $relatedIds = [];

        switch ($this->dependency_type) {
            case 'required_with':
                $relatedIds = $this->dependency_rules['required_bukti_ids'] ?? [];
                break;
            case 'exclusive':
                $relatedIds = $this->dependency_rules['exclusive_bukti_ids'] ?? [];
                break;
            case 'conditional':
                foreach ($this->dependency_rules['conditions'] ?? [] as $condition) {
                    $relatedIds = array_merge(
                        $relatedIds,
                        $condition['if_selected'] ?? [],
                        $condition['then_required'] ?? []
                    );
                }
                break;
            case 'grouped':
                $relatedIds = $this->dependency_rules['group_rules']['group_bukti_ids'] ?? [];
                break;
        }

        return array_unique($relatedIds);
    }

    /**
     * Duplicate bukti portofolio to another unit
     */
    public function duplicateToUnit($newUnitId)
    {
        $newBukti = $this->replicate();
        $newBukti->unit_kompetensi_id = $newUnitId;
        $newBukti->save();

        return $newBukti;
    }

    /**
     * Toggle status
     */
    public function toggleStatus()
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Update dependency rules
     */
    public function updateDependencyRules($type, $rules)
    {
        return $this->update([
            'dependency_type' => $type,
            'dependency_rules' => $rules
        ]);
    }

    /**
     * Clear all dependencies
     */
    public function clearDependencies()
    {
        return $this->update([
            'dependency_type' => 'standalone',
            'dependency_rules' => null
        ]);
    }

    /* ===================== STATIC METHODS ===================== */

    /**
     * Get all bukti portofolio for a certification scheme
     */
    public static function getByCertificationScheme($schemeId)
    {
        return static::whereHas('unitKompetensi', function ($query) use ($schemeId) {
            $query->where('certification_scheme_id', $schemeId);
        })->with('unitKompetensi')->ordered()->get();
    }

    /**
     * Validate multiple bukti selections
     */
    public static function validateSelections($buktiIds, $unitId = null, $schemeId = null)
    {
        $query = static::whereIn('id', $buktiIds);

        if ($unitId) {
            $query->where('unit_kompetensi_id', $unitId);
        } elseif ($schemeId) {
            $query->whereHas('unitKompetensi', function ($q) use ($schemeId) {
                $q->where('certification_scheme_id', $schemeId);
            });
        }

        $buktiList = $query->get();
        $errors = [];

        foreach ($buktiList as $bukti) {
            if (!$bukti->validateDependencies($buktiIds)) {
                $errors[] = [
                    'bukti_id' => $bukti->id,
                    'bukti_text' => $bukti->bukti_portofolio,
                    'message' => $bukti->getDependencyMessage($buktiIds)
                ];
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Reorder bukti portofolio for a unit
     */
    public static function reorderForUnit($unitId, $orderedIds)
    {
        foreach ($orderedIds as $index => $buktiId) {
            static::where('id', $buktiId)
                ->where('unit_kompetensi_id', $unitId)
                ->update(['sort_order' => $index + 1]);
        }

        return true;
    }

    /**
     * Get statistics for a unit
     */
    public static function getStatsForUnit($unitId)
    {
        $total = static::where('unit_kompetensi_id', $unitId)->count();
        $active = static::where('unit_kompetensi_id', $unitId)->where('is_active', true)->count();
        $withDependencies = static::where('unit_kompetensi_id', $unitId)
            ->where('dependency_type', '!=', 'standalone')
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'with_dependencies' => $withDependencies,
            'standalone' => $total - $withDependencies
        ];
    }
}
