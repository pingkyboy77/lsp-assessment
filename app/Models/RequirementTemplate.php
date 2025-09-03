<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementTemplate extends Model
{
    protected $fillable = [
        'name',
        'description', 
        'requirement_type', 
        'min_required',     
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_required' => 'integer',
    ];

    // Constants
    const REQUIREMENT_TYPES = [
        'all_required' => 'Semua dokumen wajib diupload',
        'choose_one' => 'Pilih salah satu dokumen saja', 
        'choose_min' => 'Pilih minimal beberapa dokumen'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationships
    public function items()
    {
        return $this->hasMany(RequirementItem::class, 'template_id');
    }

    public function activeItems()
    {
        return $this->hasMany(RequirementItem::class, 'template_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    // Many-to-many dengan CertificationScheme
    public function certificationSchemes()
    {
        return $this->belongsToMany(CertificationScheme::class, 'certification_scheme_requirements')
                    ->withPivot(['sort_order', 'is_active'])
                    ->withTimestamps()
                    ->orderBy('sort_order'); // Fixed: removed 'pivot_' prefix
    }

    // Helper methods
    public function getTypeDisplayAttribute()
    {
        return self::REQUIREMENT_TYPES[$this->requirement_type] ?? 'Unknown';
    }

    public function getRequirementDescriptionAttribute()
    {
        switch ($this->requirement_type) {
            case 'all_required':
                return "Semua {$this->items->count()} dokumen wajib diupload";
            case 'choose_one':
                return "Pilih 1 dari {$this->items->count()} dokumen";
            case 'choose_min':
                return "Pilih minimal {$this->min_required} dari {$this->items->count()} dokumen";
            default:
                return 'Tidak diketahui';
        }
    }

    /**
     * Get status text attribute
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }
}