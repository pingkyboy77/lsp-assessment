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
    public function buktiPortofolios()
    {
        return $this->hasMany(BuktiPortofolio::class, 'unit_kompetensi_id');
    }

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

    // Method untuk menambah ke kelompok kerja
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

    // Method untuk menghapus dari kelompok kerja
    public function removeFromKelompokKerja($kelompokKerjaId)
    {
        $this->kelompokKerjas()->detach($kelompokKerjaId);
    }


    // Relationships
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

    // Through relationships
    public function kriteriaKerjas()
    {
        return $this->hasManyThrough(KriteriaKerja::class, ElemenKompetensi::class);
    }

    public function activeKriteriaKerjas()
    {
        return $this->hasManyThrough(KriteriaKerja::class, ElemenKompetensi::class)->where('kriteria_kerjas.is_active', true)->orderBy('elemen_kompetensis.sort_order')->orderBy('kriteria_kerjas.sort_order');
    }

    // Scopes
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

    // Accessors
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

    // Helper methods
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
        $newUnit = $this->replicate();
        if ($newSchemeId) {
            $newUnit->certification_scheme_id = $newSchemeId;
        }
        $newUnit->save();

        // Duplicate elemen kompetensi
        foreach ($this->elemenKompetensis as $elemen) {
            $elemen->duplicateToUnit($newUnit->id);
        }

        return $newUnit;
    }

    /**
     * Sync kelompok kerja with proper pivot data handling
     */
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

    /**
     * Add kelompok kerja to unit with specific pivot data
     */
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

    /**
     * Update pivot data for specific kelompok kerja
     */
    public function updateKelompokKerjaPivot($kelompokKerjaId, array $pivotData)
    {
        $pivotData['updated_at'] = now();

        return $this->kelompokKerjas()->updateExistingPivot($kelompokKerjaId, $pivotData);
    }

    /**
     * Toggle status for specific kelompok kerja in pivot table
     */
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

    /**
     * Reorder kelompok kerja in pivot table
     */
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

    /**
     * Get active kelompok kerja ordered by sort_order
     */
    public function activeKelompokKerjasOrdered()
    {
        return $this->kelompokKerjas()
            ->withPivot(['sort_order', 'is_active', 'created_at'])
            ->wherePivot('is_active', true)
            ->orderBy('kelompok_kerja_unit_kompetensi.sort_order');
    }

    /**
     * Get kelompok kerja count by status
     */
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

    /**
     * Check if unit has specific kelompok kerja
     */
    public function hasKelompokKerja($kelompokKerjaId, $activeOnly = false)
    {
        $query = $this->kelompokKerjas()->where('kelompok_kerjas.id', $kelompokKerjaId);

        if ($activeOnly) {
            $query->wherePivot('is_active', true);
        }

        return $query->exists();
    }

    /**
     * Duplicate unit with all kelompok kerja relationships
     */
    public function duplicateWithKelompokKerjas()
    {
        DB::beginTransaction();
        try {
            // Get original kelompok kerja data
            $originalKelompokKerjas = $this->kelompokKerjas()
                ->withPivot(['sort_order', 'is_active'])
                ->get();

            // Duplicate the unit first (assuming you have a duplicate method)
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

    /**
     * Scope untuk filter berdasarkan kelompok kerja
     */
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

    /**
     * Scope untuk mendapatkan unit yang tidak memiliki kelompok kerja
     */
    public function scopeWithoutKelompokKerja($query)
    {
        return $query->doesntHave('kelompokKerjas');
    }

    /**
     * Accessor untuk mendapatkan kelompok kerja aktif
     */
    public function getActiveKelompokKerjasAttribute()
    {
        return $this->kelompokKerjas()
            ->withPivot(['sort_order', 'is_active', 'created_at', 'updated_at'])
            ->wherePivot('is_active', true)
            ->orderBy('kelompok_kerja_unit_kompetensi.sort_order')
            ->get();
    }
}
