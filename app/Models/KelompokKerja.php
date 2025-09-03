<?php

// app/Models/KelompokKerja.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokKerja extends Model
{
    use HasFactory;

    protected $fillable = ['certification_scheme_id', 'nama_kelompok', 'deskripsi', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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

    public function buktiPortofolios()
    {
        return $this->hasMany(BuktiPortofolio::class)->orderBy('sort_order');
    }

    public function activeBuktiPortofolios()
    {
        return $this->hasMany(BuktiPortofolio::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }
}
