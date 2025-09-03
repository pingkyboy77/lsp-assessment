<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPortofolio extends Model
{
    use HasFactory;

    protected $fillable = ['bukti_portofolio', 'sort_order', 'is_active', 'dependency_type', 'dependency_rules', 'group_identifier'];

    protected $casts = [
        'dependency_rules' => 'array',
    ];


    /**
     * Get the kelompok kerja that owns the bukti portofolio
     */
    public function kelompokKerja()
    {
        return $this->belongsTo(KelompokKerja::class);
    }

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
        return $query->orderBy('sort_order');
    }

    public function validateDependencies($selectedIds)
{
    switch ($this->dependency_type) {
        case 'required_with':
            if (isset($this->dependency_rules['required_bukti_ids'])) {
                return count(array_intersect($this->dependency_rules['required_bukti_ids'], $selectedIds)) > 0;
            }
            break;
        case 'exclusive':
            if (isset($this->dependency_rules['exclusive_bukti_ids'])) {
                return count(array_intersect($this->dependency_rules['exclusive_bukti_ids'], $selectedIds)) === 0;
            }
            break;
    }
    return true;
}
}
