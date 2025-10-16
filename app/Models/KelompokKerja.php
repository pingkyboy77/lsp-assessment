<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'certification_scheme_id',
        'nama_kelompok',
        'deskripsi',
        'sort_order',
        'p_level',
        'potensi_asesi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'p_level' => 'integer',
        'potensi_asesi' => 'array',
    ];

    /* ===================== CONSTANTS ===================== */

    public const POTENSI_ASESI_OPTIONS = [
        'p1' => 'Hasil pelatihan dan/atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi',
        'p2' => 'Hasil pelatihan dan/atau pendidikan, dimana kurikulum belum berbasis kompetensi',
        'p3' => 'Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi',
        'p4' => 'Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi',
        'p5' => 'Pelatihan/belajar mandiri atau otodidak',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function certificationScheme()
    {
        return $this->belongsTo(CertificationScheme::class);
    }

    public function unitKompetensis()
    {
        return $this->belongsToMany(UnitKompetensi::class, 'kelompok_kerja_unit_kompetensi')
            ->withPivot(['sort_order', 'is_active'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function activeUnitKompetensis()
    {
        return $this->unitKompetensis()
            ->wherePivot('is_active', true)
            ->orderByPivot('sort_order');
    }

    /* ===================== SCOPES ===================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeWithUnitCounts($query)
    {
        return $query->withCount([
            'unitKompetensis',
            'activeUnitKompetensis'
        ]);
    }

    public function scopeByPLevel($query, $pLevel)
    {
        return $query->where('p_level', $pLevel);
    }

    /* ===================== ACCESSORS ===================== */

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function getUnitCountAttribute()
    {
        return $this->unitKompetensis()->count();
    }

    public function getActiveUnitCountAttribute()
    {
        return $this->activeUnitKompetensis()->count();
    }

    public function getDisplayNameAttribute()
    {
        return $this->nama_kelompok;
    }

    public function getPLevelCodeAttribute()
    {
        return $this->p_level ? 'P' . $this->p_level : null;
    }

    public function getPotensiAsesiTextAttribute()
    {
        if (!$this->potensi_asesi || empty($this->potensi_asesi)) {
            return [];
        }

        return array_map(function ($key) {
            return self::POTENSI_ASESI_OPTIONS[$key] ?? $key;
        }, $this->potensi_asesi);
    }

    /* ===================== UNIT KOMPETENSI MANAGEMENT ===================== */

    /**
     * Add unit kompetensi to this kelompok kerja
     */
    public function addUnitKompetensi($unitId, $sortOrder = null)
    {
        if ($this->unitKompetensis()->where('unit_kompetensi_id', $unitId)->exists()) {
            return false; // Already exists
        }

        $sortOrder = $sortOrder ?? ($this->getMaxUnitSortOrder() + 1);

        $this->unitKompetensis()->attach($unitId, [
            'sort_order' => $sortOrder,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    /**
     * Remove unit kompetensi from this kelompok kerja
     */
    public function removeUnitKompetensi($unitId)
    {
        return $this->unitKompetensis()->detach($unitId) > 0;
    }

    /**
     * Sync unit kompetensi with sort order
     */
    public function syncUnitKompetensis(array $unitIds)
    {
        $syncData = [];

        foreach ($unitIds as $index => $unitId) {
            $syncData[$unitId] = [
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $this->unitKompetensis()->sync($syncData);
    }

    /**
     * Reorder unit kompetensi
     */
    public function reorderUnitKompetensis(array $unitIds)
    {
        foreach ($unitIds as $index => $unitId) {
            $this->unitKompetensis()->updateExistingPivot($unitId, [
                'sort_order' => $index + 1,
                'updated_at' => now(),
            ]);
        }

        return true;
    }

    /**
     * Toggle unit kompetensi status
     */
    public function toggleUnitStatus($unitId)
    {
        $pivot = $this->unitKompetensis()->where('unit_kompetensi_id', $unitId)->first();

        if (!$pivot) {
            return false;
        }

        $newStatus = !$pivot->pivot->is_active;

        $this->unitKompetensis()->updateExistingPivot($unitId, [
            'is_active' => $newStatus,
            'updated_at' => now(),
        ]);

        return $newStatus;
    }

    /**
     * Get maximum sort order for units
     */
    private function getMaxUnitSortOrder()
    {
        return $this->unitKompetensis()->max('kelompok_kerja_unit_kompetensi.sort_order') ?? 0;
    }

    /* ===================== VALIDATION METHODS ===================== */

    public function canBeDeleted()
    {
        // Check if kelompok kerja has any active units or other dependencies
        return $this->activeUnitKompetensis()->count() === 0;
    }

    public function validateCompleteness()
    {
        $issues = [];

        if (empty($this->nama_kelompok)) {
            $issues[] = 'Nama kelompok belum diisi';
        }

        if (!$this->p_level) {
            $issues[] = 'P Level belum ditentukan';
        }

        if (empty($this->potensi_asesi)) {
            $issues[] = 'Potensi asesi belum dipilih';
        }

        if ($this->unit_count === 0) {
            $issues[] = 'Belum ada unit kompetensi';
        }

        if ($this->active_unit_count === 0) {
            $issues[] = 'Belum ada unit kompetensi aktif';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
        ];
    }

    /* ===================== STATISTICS METHODS ===================== */

    public function getStatsAttribute()
    {
        return [
            'total_units' => $this->unit_count,
            'active_units' => $this->active_unit_count,
            'p_level' => $this->p_level_code,
            'potensi_asesi_count' => is_array($this->potensi_asesi) ? count($this->potensi_asesi) : 0,
            'completion_percentage' => $this->getCompletionPercentage(),
        ];
    }

    private function getCompletionPercentage()
    {
        $criteria = [
            'has_name' => !empty($this->nama_kelompok),
            'has_p_level' => !empty($this->p_level),
            'has_potensi_asesi' => !empty($this->potensi_asesi),
            'has_units' => $this->unit_count > 0,
            'has_active_units' => $this->active_unit_count > 0,
        ];

        $completed = array_filter($criteria);
        return round((count($completed) / count($criteria)) * 100);
    }

    /* ===================== UTILITY METHODS ===================== */

    public function duplicate($newName = null, $copyUnits = true)
    {
        $duplicate = $this->replicate();
        $duplicate->nama_kelompok = $newName ?? ($this->nama_kelompok . ' (Copy)');
        $duplicate->is_active = false;
        $duplicate->sort_order = $this->getMaxSortOrderInScheme() + 1;
        $duplicate->save();

        if ($copyUnits) {
            foreach ($this->unitKompetensis as $unit) {
                $duplicate->unitKompetensis()->attach($unit->id, [
                    'sort_order' => $unit->pivot->sort_order,
                    'is_active' => false, // Start inactive for safety
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return $duplicate;
    }

    private function getMaxSortOrderInScheme()
    {
        return $this->certificationScheme->kelompokKerjas()->max('sort_order') ?? 0;
    }

    public function activate()
    {
        return $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        return $this->update(['is_active' => false]);
    }

    /* ===================== QUERY OPTIMIZATION ===================== */

    public function loadWithUnits()
    {
        return $this->load([
            'unitKompetensis' => function ($query) {
                $query->withPivot(['sort_order', 'is_active'])
                    ->orderByPivot('sort_order');
            }
        ]);
    }

    public function loadBasicData()
    {
        return $this->load([
            'certificationScheme:id,nama,code_1',
            'unitKompetensis:id,kode_unit,judul_unit,is_active'
        ]);
    }

    /* ===================== STATIC METHODS ===================== */

    public static function createWithUnits($schemeId, $data, $unitIds = [])
    {
        $kelompok = static::create([
            'certification_scheme_id' => $schemeId,
            'nama_kelompok' => $data['nama_kelompok'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'p_level' => $data['p_level'] ?? null,
            'potensi_asesi' => $data['potensi_asesi'] ?? [],
            'sort_order' => $data['sort_order'] ?? static::getNextSortOrder($schemeId),
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (!empty($unitIds)) {
            $kelompok->syncUnitKompetensis($unitIds);
        }

        return $kelompok;
    }

    public static function getNextSortOrder($schemeId)
    {
        return static::where('certification_scheme_id', $schemeId)->max('sort_order') + 1;
    }

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

    /* ===================== MAPA RELATED METHODS ===================== */

    /**
     * Get kelompok kerja by P Level (matching dengan MAPA)
     * P Level di MAPA hanya angka (contoh: 2, 3, 4)
     * P Level di KelompokKerja juga hanya angka untuk sinkronisasi
     */
    public static function getByPLevel($schemeId, $pLevel)
    {
        return static::where('certification_scheme_id', $schemeId)
            ->where('p_level', $pLevel)
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Get auto-selected potensi asesi based on P Level
     * Digunakan saat create MAPA untuk auto-populate potensi asesi
     */
    public static function getPotensiAsesiByPLevel($schemeId, $pLevel)
    {
        $kelompokKerja = static::where('certification_scheme_id', $schemeId)
            ->where('p_level', $pLevel)
            ->first();

        return $kelompokKerja ? $kelompokKerja->potensi_asesi : [];
    }

    /**
     * Check if P Level is available for the scheme
     */
    public static function isPLevelAvailable($schemeId, $pLevel)
    {
        return static::where('certification_scheme_id', $schemeId)
            ->where('p_level', $pLevel)
            ->exists();
    }

    /**
     * Get available P Levels for scheme
     */
    public static function getAvailablePLevels($schemeId)
    {
        return static::where('certification_scheme_id', $schemeId)
            ->whereNotNull('p_level')
            ->pluck('p_level', 'id')
            ->unique()
            ->sort()
            ->values();
    }

    /* ===================== EXPORT/SUMMARY METHODS ===================== */

    public function toSummaryArray()
    {
        return [
            'id' => $this->id,
            'nama_kelompok' => $this->nama_kelompok,
            'deskripsi' => $this->deskripsi,
            'p_level' => $this->p_level,
            'p_level_code' => $this->p_level_code,
            'potensi_asesi' => $this->potensi_asesi,
            'potensi_asesi_text' => $this->potensi_asesi_text,
            'is_active' => $this->is_active,
            'status_text' => $this->status_text,
            'sort_order' => $this->sort_order,
            'units_count' => $this->unit_count,
            'active_units_count' => $this->active_unit_count,
            'completion_percentage' => $this->getCompletionPercentage(),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }
}
