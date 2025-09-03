<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionProv extends Model
{
    use HasFactory;

    protected $table = 'region_prov';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nama',
        'kode',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public $timestamps = true;

    // Relationships
    public function cities()
    {
        return $this->hasMany(RegionKab::class, 'id_prov', 'id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
