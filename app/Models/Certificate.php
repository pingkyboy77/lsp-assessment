<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'scheme_id',
        'holder_name',
        'holder_id',
        'issue_date',
        'expiry_date',
        'status',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function certificationScheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByScheme($query, $schemeId)
    {
        return $query->where('scheme_id', $schemeId);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                    ->where('expiry_date', '>=', Carbon::now())
                    ->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::now())
                    ->where('status', 'active');
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'success',
            'expired' => 'danger',
            'revoked' => 'warning',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getValidityStatusAttribute()
    {
        if ($this->status !== 'active') {
            return ucfirst($this->status);
        }

        if ($this->is_expired) {
            return 'Expired';
        }

        $daysUntilExpiry = $this->days_until_expiry;
        if ($daysUntilExpiry !== null && $daysUntilExpiry <= 30) {
            return 'Expiring Soon';
        }

        return 'Valid';
    }

    // Mutators
    public function setCertificateNumberAttribute($value)
    {
        $this->attributes['certificate_number'] = strtoupper($value);
    }

    public function setHolderNameAttribute($value)
    {
        $this->attributes['holder_name'] = ucwords(strtolower($value));
    }

    // Auto-update status when expiry date changes
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($certificate) {
            if ($certificate->expiry_date && $certificate->expiry_date->isPast() && $certificate->status === 'active') {
                $certificate->status = 'expired';
            }
        });
    }
}