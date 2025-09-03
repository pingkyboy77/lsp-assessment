<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'elemen_kompetensi_id',
        'kode_kriteria',
        'uraian_kriteria',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function elemenKompetensi()
    {
        return $this->belongsTo(ElemenKompetensi::class);
    }

    public function unitKompetensi()
    {
        return $this->elemenKompetensi->unitKompetensi();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByElemen($query, $elemenId)
    {
        return $query->where('elemen_kompetensi_id', $elemenId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('kode_kriteria');
    }

    // Accessors
    public function getFullKodeAttribute()
    {
        return $this->kode_kriteria . ' - ' . $this->uraian_kriteria;
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function getShortUraianAttribute()
    {
        return str_limit($this->uraian_kriteria, 100);
    }

    // Helper methods
    public function duplicateToElemen($elemenId)
    {
        $newKriteria = $this->replicate();
        $newKriteria->elemen_kompetensi_id = $elemenId;
        $newKriteria->save();

        return $newKriteria;
    }

    public function buktiPortofolios()
{
    return $this->hasMany(BuktiPortofolio::class)->orderBy('sort_order');
}

/**
 * Get only active bukti portofolio
 */
public function activeBuktiPortofolios()
{
    return $this->hasMany(BuktiPortofolio::class)->where('is_active', true)->orderBy('sort_order');
}
}
