<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElemenKompetensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_kompetensi_id',
        'kode_elemen',
        'judul_elemen',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function unitKompetensi()
    {
        return $this->belongsTo(UnitKompetensi::class);
    }

    public function kriteriaKerjas()
    {
        return $this->hasMany(KriteriaKerja::class)->orderBy('sort_order');
    }

    public function activeKriteriaKerjas()
    {
        return $this->hasMany(KriteriaKerja::class)
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_kompetensi_id', $unitId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('kode_elemen');
    }

    // Accessors
    public function getFullKodeAttribute()
    {
        return $this->kode_elemen . ' - ' . $this->judul_elemen;
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
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
    public function reorderKriteria($kriteriaIds)
    {
        foreach ($kriteriaIds as $index => $kriteriaId) {
            $this->kriteriaKerjas()
                 ->where('id', $kriteriaId)
                 ->update(['sort_order' => $index + 1]);
        }
    }

    public function duplicateToUnit($unitId)
    {
        $newElemen = $this->replicate();
        $newElemen->unit_kompetensi_id = $unitId;
        $newElemen->save();

        // Duplicate kriteria kerja
        foreach ($this->kriteriaKerjas as $kriteria) {
            $kriteria->duplicateToElemen($newElemen->id);
        }

        return $newElemen;
    }
}