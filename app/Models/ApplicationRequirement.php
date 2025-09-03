<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'requirement_item_id',
        'value',
        'file_path',
        'original_filename',
        'status',
        'admin_notes'
    ];

    public function requirementItem()
    {
        return $this->belongsTo(RequirementItem::class);
    }

    // Jika ada model Application
    // public function application()
    // {
    //     return $this->belongsTo(Application::class);
    // }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}