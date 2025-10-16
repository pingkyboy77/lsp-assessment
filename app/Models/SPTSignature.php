<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SPTSignature extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'spt_signatures';

    protected $fillable = [
        'delegasi_personil_id',
        'spt_verifikator_number',
        'spt_observer_number',
        'spt_asesor_number',
        'spt_verifikator_file',
        'spt_observer_file',
        'spt_asesor_file',
        'signed_by',
        'signed_at',
        'signature_image',
        'status',
        'notes',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate SPT numbers automatically saat record dibuat
            if (empty($model->spt_verifikator_number)) {
                $model->spt_verifikator_number = static::generateSPTNumber('spt_verifikator');
            }

            if (empty($model->spt_observer_number)) {
                $model->spt_observer_number = static::generateSPTNumber('spt_observer');
            }

            if (empty($model->spt_asesor_number)) {
                $model->spt_asesor_number = static::generateSPTNumber('spt_asesor');
            }
        });
    }

    // ==================== STATIC METHODS ====================

    /**
     * Generate SPT number using NumberSequence
     * 
     * @param string $type 'spt_verifikator', 'spt_observer', 'spt_asesor'
     * @return string
     */
    public static function generateSPTNumber($type)
    {
        $runningNumber = \App\Models\NumberSequence::generate($type);


        return $runningNumber;
    }

    // ==================== RELATIONSHIPS ====================

    public function delegasiPersonil()
    {
        return $this->belongsTo(DelegasiPersonilAsesmen::class, 'delegasi_personil_id');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopeForDelegasi($query, $delegasiId)
    {
        return $query->where('delegasi_personil_id', $delegasiId);
    }

    // ==================== ACCESSORS ====================

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsSignedAttribute()
    {
        return $this->status === 'signed';
    }

    public function getSignedByNameAttribute()
    {
        return $this->signedBy->name ?? '-';
    }

    public function getFormattedSignedAtAttribute()
    {
        return $this->signed_at ? $this->signed_at->format('d F Y H:i') : '-';
    }

    public function getAsesiNameAttribute()
    {
        return $this->delegasiPersonil->asesi->name ?? '-';
    }

    public function getSchemeNameAttribute()
    {
        return $this->delegasiPersonil->certificationScheme->nama ?? '-';
    }

    // ==================== HELPER METHODS ====================

    public function markAsSigned($signedBy, $signatureImage = null)
    {
        $this->status = 'signed';
        $this->signed_by = $signedBy;
        $this->signed_at = now();

        if ($signatureImage) {
            $this->signature_image = $signatureImage;
        }

        $this->save();

        return $this;
    }

    public function hasAllSPTFiles()
    {
        return $this->spt_verifikator_file &&
            $this->spt_observer_file &&
            $this->spt_asesor_file;
    }

    public function hasAllSPTNumbers()
    {
        return $this->spt_verifikator_number &&
            $this->spt_observer_number &&
            $this->spt_asesor_number;
    }

    public function getMissingSPTFiles()
    {
        $missing = [];

        if (!$this->spt_verifikator_file) {
            $missing[] = 'SPT Verifikator';
        }

        if (!$this->spt_observer_file) {
            $missing[] = 'SPT Observer';
        }

        if (!$this->spt_asesor_file) {
            $missing[] = 'SPT Asesor';
        }

        return $missing;
    }
}
