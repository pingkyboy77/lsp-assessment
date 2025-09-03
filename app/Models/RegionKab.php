<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionKab extends Model
{
    use HasFactory;

    protected $table = 'region_kab';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_prov',
        'nama',
        'kode',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public $timestamps = true;

    // Relationships
    public function province()
    {
        return $this->belongsTo(RegionProv::class, 'id_prov', 'id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProvince($query, $provinceId)
    {
        return $query->where('id_prov', $provinceId);
    }
}