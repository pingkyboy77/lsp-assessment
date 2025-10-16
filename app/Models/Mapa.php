<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapa extends Model
{
    use HasFactory;

    protected $table = 'mapa';

    protected $fillable = [
        'delegasi_personil_asesmen_id',
        'asesor_id',
        'apl01_id',
        'apl02_id',
        'certification_scheme_id',
        'nomor_mapa',
        'p_level',
        'status',
        'catatan_asesor',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'approved_at',
        'signed_by',
        'signed_at',
        'signature_image'
    ];

    protected $casts = [
        'p_level' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function delegasi()
    {
        return $this->belongsTo(DelegasiPersonilAsesmen::class, 'delegasi_personil_asesmen_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function apl01()
    {
        return $this->belongsTo(Apl01Pendaftaran::class, 'apl01_id');
    }

    public function apl02()
    {
        return $this->belongsTo(Apl02::class, 'apl02_id');
    }

    public function certificationScheme()
    {
        return $this->belongsTo(CertificationScheme::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // ⭐ TAMBAHKAN RELATIONSHIP INI (YANG KURANG)
    public function ak07()
    {
        return $this->hasOne(Ak07CeklisePenyesuaian::class, 'mapa_id');
    }

    /* ===================== SCOPES ===================== */

    public function scopeByAsesor($query, $asesorId)
    {
        return $query->where('asesor_id', $asesorId);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    /* ===================== ACCESSORS ===================== */

    public function getMapaCodeAttribute()
    {
        return 'P' . $this->p_level;
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'validated' => 'Validated',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'approved' => 'warning',
            'rejected' => 'danger',
            'validated' => 'success',
            default => 'secondary'
        };
    }

    public function getIsSignedAttribute()
    {
        return !empty($this->signature_image) && !empty($this->signed_at);
    }

    public function getIsDraftAttribute()
    {
        return $this->status === 'draft';
    }

    public function getIsSubmittedAttribute()
    {
        return $this->status === 'submitted';
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute()
    {
        return $this->status === 'rejected';
    }

    public function getIsValidatedAttribute()
    {
        return $this->status === 'validated';
    }

    /* ===================== HELPER METHODS ===================== */

    public function getMetodeForKelompok($kelompokIndex)
    {
        return $kelompokIndex < $this->p_level ? 'tidak_langsung' : 'langsung';
    }

    public function getKelompokMetodeDetails()
    {
        $kelompoks = $this->certificationScheme->kelompokKerjas()->orderBy('sort_order')->get();
        $details = [];

        foreach ($kelompoks as $index => $kelompok) {
            $metode = $this->getMetodeForKelompok($index);

            $details[] = [
                'kelompok' => $kelompok,
                'index' => $index,
                'metode' => $metode,
                'metode_text' => $metode === 'tidak_langsung'
                    ? 'Tidak Langsung (Portofolio & Wawancara)'
                    : 'Langsung (Tertulis & DIT)',
                'p_number' => $index < $this->p_level ? ($index + 1) : 0,
            ];
        }

        return $details;
    }

    public function getSummary()
    {
        $kelompoks = $this->certificationScheme->kelompokKerjas()->count();
        $tidakLangsung = min($this->p_level, $kelompoks);
        $langsung = max(0, $kelompoks - $tidakLangsung);

        return [
            'total_kelompok' => $kelompoks,
            'tidak_langsung' => $tidakLangsung,
            'langsung' => $langsung,
            'mapa_code' => $this->mapa_code,
        ];
    }

    public function getDescription()
    {
        $summary = $this->getSummary();

        if ($this->p_level == 0) {
            return 'Semua kelompok menggunakan metode Langsung (Tertulis & DIT)';
        }

        $parts = [];

        if ($summary['tidak_langsung'] > 0) {
            $parts[] = "Kelompok 1-{$summary['tidak_langsung']}: Tidak Langsung (Portofolio & Wawancara)";
        }

        if ($summary['langsung'] > 0) {
            $start = $summary['tidak_langsung'] + 1;
            $end = $summary['total_kelompok'];
            if ($start == $end) {
                $parts[] = "Kelompok {$start}: Langsung (Tertulis & DIT)";
            } else {
                $parts[] = "Kelompok {$start}-{$end}: Langsung (Tertulis & DIT)";
            }
        }

        return implode(' + ', $parts);
    }

    /* ===================== STATIC METHODS ===================== */

    public static function generateNomorMapa($schemeId, $asesorId)
    {
        $scheme = CertificationScheme::find($schemeId);
        $year = now()->format('Y');
        $month = now()->format('m');

        $prefix = "MAPA/{$scheme->code_1}/{$year}/{$month}/";

        $lastNumber = static::where('nomor_mapa', 'like', $prefix . '%')
            ->orderBy('nomor_mapa', 'desc')
            ->first();

        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->nomor_mapa, -4);
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return $prefix . str_pad($newNum, 4, '0', STR_PAD_LEFT);
    }

    /* ===================== ACTION METHODS ===================== */

    public function signByAsesor($signatureData, $ipAddress = null)
    {
        $this->update([
            'signed_by' => auth()->id(),
            'signed_at' => now(),
            'signature_image' => $signatureData,
        ]);

        return true;
    }

    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function approve($reviewedBy, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function reject($reviewedBy, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'review_notes' => $notes,
            'signed_by' => null,
            'signed_at' => null,
            'signature_image' => null,
        ]);
    }

    public function validate($signatureData, $ipAddress = null)
    {
        $this->update([
            'status' => 'validated',
            'signed_by' => auth()->id(),
            'signed_at' => now(),
            'signature_image' => $signatureData,
        ]);
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft' && $this->is_signed;
    }

    public function canBeReviewed()
    {
        return $this->status === 'submitted';
    }

    public function canBeValidated()
    {
        return $this->status === 'approved' && !$this->is_signed;
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }
}
