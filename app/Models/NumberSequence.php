<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class NumberSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'sequence_key',
        'name',
        'prefix',
        'suffix',
        'digits',
        'separator',
        'use_year',
        'year_format',
        'reset_yearly',
        'reset_monthly',
        'format_template',
        'start_number',
        'current_number',
        'is_active',
        'description',
        'sample_output'
    ];

    protected $casts = [
        'use_year' => 'boolean',
        'reset_yearly' => 'boolean',
        'reset_monthly' => 'boolean',
        'is_active' => 'boolean',
        'sample_output' => 'array',
        'digits' => 'integer',
        'start_number' => 'integer',
        'current_number' => 'integer',
    ];

    protected $attributes = [
        'digits' => 6,
        'separator' => '-',
        'use_year' => true,
        'year_format' => 'Y',
        'reset_yearly' => true,
        'reset_monthly' => false,
        'start_number' => 1,
        'current_number' => 0,
        'is_active' => true,
        'format_template' => '{prefix}{separator}{year}{separator}{number}'
    ];

    public static function validationRules($id = null)
    {
        return [
            'sequence_key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                // Fix: Handle null ID properly for PostgreSQL
                Rule::unique('number_sequences', 'sequence_key')->ignore($id)
            ],
            'name' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:10',
            'suffix' => 'nullable|string|max:10',
            'digits' => 'required|integer|min:1|max:10',
            'separator' => 'required|string|max:3',
            'use_year' => 'boolean',
            'year_format' => 'required_if:use_year,true|in:Y,y,Ym',
            'reset_yearly' => 'boolean',
            'reset_monthly' => 'boolean',
            'format_template' => 'required|string|max:255',
            'start_number' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ];
    }

    public function generateNext()
    {
        $this->increment('current_number');
        return $this->formatNumber($this->current_number);
    }

    public function generatePreview($count = 3)
    {
        $previews = [];
        $startFrom = $this->current_number + 1;
        
        for ($i = 0; $i < $count; $i++) {
            $previews[] = $this->formatNumber($startFrom + $i);
        }
        
        return $previews;
    }

    protected function formatNumber($number)
    {
        $year = '';
        if ($this->use_year) {
            switch ($this->year_format) {
                case 'Y':
                    $year = date('Y');
                    break;
                case 'y':
                    $year = date('y');
                    break;
                case 'Ym':
                    $year = date('Ym');
                    break;
            }
        }

        $paddedNumber = str_pad($number, $this->digits, '0', STR_PAD_LEFT);

        $replacements = [
            '{prefix}' => $this->prefix ?? '',
            '{suffix}' => $this->suffix ?? '',
            '{year}' => $year,
            '{number}' => $paddedNumber,
            '{separator}' => $this->separator,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->format_template);
    }

    public static function generate($sequenceKey)
    {
        $sequence = self::where('sequence_key', $sequenceKey)
                       ->where('is_active', true)
                       ->firstOrFail();

        // Check if reset is needed
        if ($sequence->reset_yearly || $sequence->reset_monthly) {
            $sequence->checkAndReset();
        }

        return $sequence->generateNext();
    }

    protected function checkAndReset()
    {
        $now = now();
        $lastUpdate = $this->updated_at;

        $shouldReset = false;

        if ($this->reset_monthly && $lastUpdate->format('Y-m') !== $now->format('Y-m')) {
            $shouldReset = true;
        } elseif ($this->reset_yearly && $lastUpdate->format('Y') !== $now->format('Y')) {
            $shouldReset = true;
        }

        if ($shouldReset) {
            $this->current_number = $this->start_number - 1;
            $this->save();
        }
    }
}