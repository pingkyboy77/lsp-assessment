<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_bidang',
        'code_2',
        'bidang',
        'bidang_ing',
        'kbbli_bidang',
        'kode_web',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function certificationSchemes(): HasMany
    {
        return $this->hasMany(CertificationScheme::class, 'code_2', 'code_2');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKodeWeb($query, $kodeWeb)
    {
        return $query->where('kode_web', $kodeWeb);
    }

    public function scopeByKBBLI($query, $kbbli)
    {
        return $query->where('kbbli_bidang', $kbbli);
    }

    public function scopeByKodeBidang($query, $kodeBidang)
    {
        return $query->where('kode_bidang', $kodeBidang);
    }

    // Accessors
    public function getSchemesCountAttribute()
    {
        return $this->certificationSchemes()->count();
    }

    public function getActiveSchemesCountAttribute()
    {
        return $this->certificationSchemes()->where('is_active', true)->count();
    }

    public function getFullNameAttribute()
    {
        return $this->bidang ? $this->bidang . ' (' . $this->code_2 . ')' : 'Kode: ' . $this->code_2;
    }

    // Mutators
    public function setKodeBidangAttribute($value)
    {
        $this->attributes['kode_bidang'] = strtoupper($value);
    }

    public function setCode2Attribute($value)
    {
        $this->attributes['code_2'] = strtoupper($value);
    }

    public function setKodeWebAttribute($value)
    {
        $this->attributes['kode_web'] = strtoupper($value);
    }
}