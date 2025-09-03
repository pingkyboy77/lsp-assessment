<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'log_name',
        'causer_id',
        'causer_type',
        'subject_id',
        'subject_type',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the causer (user who performed the action)
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the subject (the model that was affected)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan log name
     */
    public function scopeByLogName($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('causer_id', $userId);
    }

    /**
     * Get formatted description with user name
     */
    public function getFormattedDescriptionAttribute()
    {
        $description = $this->description;
        
        if ($this->causer) {
            $userName = $this->causer->name ?? $this->causer->email ?? 'Unknown User';
            return "{$userName}: {$description}";
        }
        
        return "System: {$description}";
    }

    /**
     * Get log level based on log name
     */
    public function getLogLevelAttribute()
    {
        $levels = [
            'created' => 'success',
            'updated' => 'info', 
            'deleted' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary',
            'failed_login' => 'warning',
            'default' => 'info'
        ];

        return $levels[$this->log_name] ?? $levels['default'];
    }
}