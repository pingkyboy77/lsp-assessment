<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TukRescheduleHistory extends Model
{
    use HasFactory;

    protected $table = 'tuk_reschedule_history';

    protected $fillable = [
        'kode_tuk',
        'apl01_id',
        'tuk_type',
        'reschedule_reason',
        'rescheduled_by',
        'rescheduled_at',
        'old_tanggal_assessment',
        'old_lokasi_assessment',
        'had_signature',
        'had_recommendation',
        'had_delegation',
        'had_mapa',
        'mapa_nomor',
        'apl01_status_before',
        'apl02_status_before',
    ];

    protected $casts = [
        'old_tanggal_assessment' => 'date',
        'rescheduled_at' => 'datetime',
        'had_signature' => 'boolean',
        'had_recommendation' => 'boolean',
        'had_delegation' => 'boolean',
        'had_mapa' => 'boolean',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function apl01()
    {
        return $this->belongsTo(Apl01Pendaftaran::class, 'apl01_id');
    }

    public function rescheduledBy()
    {
        return $this->belongsTo(User::class, 'rescheduled_by');
    }

    /* ===================== SCOPES ===================== */

    public function scopeSewaktu($query)
    {
        return $query->where('tuk_type', 'sewaktu');
    }

    public function scopeMandiri($query)
    {
        return $query->where('tuk_type', 'mandiri');
    }

    public function scopeByAdmin($query, $adminId)
    {
        return $query->where('rescheduled_by', $adminId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('rescheduled_at', '>=', now()->subDays($days));
    }

    /* ===================== ACCESSORS ===================== */

    public function getFormattedRescheduledAtAttribute()
    {
        return $this->rescheduled_at ? $this->rescheduled_at->format('d F Y H:i') : null;
    }

    public function getTukTypeTextAttribute()
    {
        return $this->tuk_type === 'sewaktu' ? 'TUK Sewaktu' : 'TUK Mandiri';
    }

    public function getDeletedDataSummaryAttribute()
    {
        $summary = [];

        if ($this->had_delegation) {
            $summary[] = 'Delegasi Personil';
        }

        if ($this->had_recommendation) {
            $summary[] = 'Rekomendasi TUK';
        }

        if ($this->had_mapa) {
            $summary[] = 'MAPA (' . $this->mapa_nomor . ')';
        }

        if ($this->had_signature) {
            $summary[] = 'Tanda Tangan';
        }

        return implode(', ', $summary);
    }

    /* ===================== STATIC METHODS ===================== */

    /**
     * Create reschedule history for TUK Sewaktu
     */
    public static function createForSewaktu(TukRequest $tukRequest, $reason, $adminId, $mapaInfo = null)
    {
        return static::create([
            'kode_tuk' => $tukRequest->kode_tuk,
            'apl01_id' => $tukRequest->apl01_id,
            'tuk_type' => 'sewaktu',
            'reschedule_reason' => $reason,
            'rescheduled_by' => $adminId,
            'rescheduled_at' => now(),
            'old_tanggal_assessment' => $tukRequest->tanggal_assessment,
            'old_lokasi_assessment' => $tukRequest->lokasi_assessment,
            'had_signature' => !empty($tukRequest->tanda_tangan_peserta_path),
            'had_recommendation' => !empty($tukRequest->recommended_at),
            'had_delegation' => $tukRequest->delegasi !== null,
            'had_mapa' => $mapaInfo !== null,
            'mapa_nomor' => $mapaInfo['nomor_mapa'] ?? null,
            'apl01_status_before' => $tukRequest->apl01?->status,
            'apl02_status_before' => $tukRequest->apl01?->apl02?->status,
        ]);
    }

    /**
     * Create reschedule history for TUK Mandiri
     */
    public static function createForMandiri(Apl01Pendaftaran $apl01, $reason, $adminId, $mapaInfo = null)
    {
        return static::create([
            'kode_tuk' => null,
            'apl01_id' => $apl01->id,
            'tuk_type' => 'mandiri',
            'reschedule_reason' => $reason,
            'rescheduled_by' => $adminId,
            'rescheduled_at' => now(),
            'old_tanggal_assessment' => null,
            'old_lokasi_assessment' => null,
            'had_signature' => false,
            'had_recommendation' => false,
            'had_delegation' => $apl01->delegasi !== null,
            'had_mapa' => $mapaInfo !== null,
            'mapa_nomor' => $mapaInfo['nomor_mapa'] ?? null,
            'apl01_status_before' => $apl01->status,
            'apl02_status_before' => $apl01->apl02?->status,
        ]);
    }

    /* ===================== HELPER METHODS ===================== */

    public function getImpactSummary()
    {
        $impacts = [];

        if ($this->tuk_type === 'sewaktu') {
            $impacts[] = 'TukRequest dihapus';
        } else {
            $impacts[] = 'Status APL direset ke pending';
        }

        if ($this->had_delegation) {
            $impacts[] = 'Delegasi dihapus';
        }

        if ($this->had_mapa) {
            $impacts[] = 'MAPA dihapus';
        }

        if ($this->had_recommendation) {
            $impacts[] = 'Rekomendasi dihapus';
        }

        return implode(' | ', $impacts);
    }
}
