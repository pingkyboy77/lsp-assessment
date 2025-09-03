<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'string'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    // Accessors
    public function getFormattedValueAttribute()
    {
        switch ($this->type) {
            case 'json':
                return json_decode($this->value, true);
            case 'boolean':
                return (bool) $this->value;
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            default:
                return $this->value;
        }
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Active</span>' 
            : '<span class="badge bg-danger">Inactive</span>';
    }

    public function getTypeBadgeAttribute()
    {
        $colors = [
            'string' => 'primary',
            'integer' => 'info',
            'json' => 'warning',
            'boolean' => 'success',
            'float' => 'secondary'
        ];
        
        $color = $colors[$this->type] ?? 'dark';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->type) . '</span>';
    }

    // Static methods
    public static function getValue($key, $default = null)
    {
        $setting = static::active()->where('key', $key)->first();
        return $setting ? $setting->formatted_value : $default;
    }

    public static function setValue($key, $value, $type = 'string')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'updated_by' => auth()->id()
            ]
        );
    }

    // Validation rules
    public static function validationRules($id = null)
    {
        return [
            'key' => 'required|string|max:255|unique:system_settings,key,' . $id,
            'value' => 'required',
            'type' => 'required|in:string,integer,json,boolean,float',
            'description' => 'nullable|string|max:500',
            'group' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ];
    }
}
