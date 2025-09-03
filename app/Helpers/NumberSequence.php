<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\NumberSequence as NumberSequenceModel;

class NumberSequence
{
    public static function generate($sequenceKey)
    {
        return DB::transaction(function () use ($sequenceKey) {
            // Get sequence configuration with lock for concurrency
            $sequence = NumberSequenceModel::where('sequence_key', $sequenceKey)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                throw new \Exception("Sequence configuration not found for key: {$sequenceKey}");
            }

            // Determine current period
            $currentPeriod = self::getCurrentPeriod($sequence);

            // Check if we need to reset the counter
            if (self::needsReset($sequence, $currentPeriod)) {
                $sequence->current_number = $sequence->start_number;
                $sequence->current_period = $currentPeriod;
            } else {
                $sequence->current_number += 1;
            }

            // Generate the formatted number
            $formattedNumber = self::formatNumber($sequence, $sequence->current_number);

            // Save the updated sequence
            $sequence->save();

            return $formattedNumber;
        });
    }

    private static function getCurrentPeriod($sequence)
    {
        if ($sequence->reset_monthly) {
            return date('Y-m'); // 2025-08
        } elseif ($sequence->reset_yearly) {
            return date('Y'); // 2025
        }
        
        return null; // No reset
    }

    private static function needsReset($sequence, $currentPeriod)
    {
        return $currentPeriod && $sequence->current_period !== $currentPeriod;
    }

    private static function formatNumber($sequence, $number)
    {
        $replacements = [
            '{prefix}' => $sequence->prefix ?? '',
            '{year}' => $sequence->use_year ? date($sequence->year_format) : '',
            '{number}' => str_pad($number, $sequence->digits, '0', STR_PAD_LEFT),
            '{suffix}' => $sequence->suffix ?? '',
            '{separator}' => $sequence->separator
        ];

        // If format_template exists, use it
        if ($sequence->format_template) {
            $result = $sequence->format_template;
            foreach ($replacements as $placeholder => $value) {
                $result = str_replace($placeholder, $value, $result);
            }
            
            // Clean up multiple separators
            $result = preg_replace('/(' . preg_quote($sequence->separator) . '){2,}/', $sequence->separator, $result);
            $result = trim($result, $sequence->separator);
            
            return $result;
        }

        // Fallback to simple format
        $parts = array_filter([
            $sequence->prefix,
            $sequence->use_year ? date($sequence->year_format) : null,
            str_pad($number, $sequence->digits, '0', STR_PAD_LEFT),
            $sequence->suffix
        ]);

        return implode($sequence->separator, $parts);
    }

    public static function preview($sequenceKey, $count = 5)
    {
        $sequence = NumberSequenceModel::where('sequence_key', $sequenceKey)->first();
        
        if (!$sequence) {
            return ['Error: Sequence not found'];
        }

        $previews = [];
        $tempNumber = $sequence->current_number;

        for ($i = 1; $i <= $count; $i++) {
            $previews[] = self::formatNumber($sequence, $tempNumber + $i);
        }

        return $previews;
    }

    public static function reset($sequenceKey)
    {
        $sequence = NumberSequenceModel::where('sequence_key', $sequenceKey)->first();
        
        if ($sequence) {
            $sequence->update([
                'current_number' => 0,
                'current_period' => self::getCurrentPeriod($sequence)
            ]);
            
            return true;
        }
        
        return false;
    }
}
