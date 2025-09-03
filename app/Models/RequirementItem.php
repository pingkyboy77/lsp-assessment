<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementItem extends Model
{
    protected $fillable = [
        'template_id',
        'document_name',
        'description', 
        'type',
        'validation_rules',
        'options',
        'sort_order',
        'is_active',
        'is_required',
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'options' => 'array', 
        'is_active' => 'boolean',
    ];

    // Types
    const TYPES = [
        'file_upload' => 'Upload File',
        'text_input' => 'Input Teks',
        'number' => 'Input Angka', 
        'select' => 'Dropdown',
        'checkbox' => 'Checkbox'
    ];

    // Relationships
    public function template()
    {
        return $this->belongsTo(RequirementTemplate::class, 'template_id');
    }
}