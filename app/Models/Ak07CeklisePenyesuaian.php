<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ak07CeklisePenyesuaian extends Model
{
    use SoftDeletes;

    protected $table = 'ak07_ceklis_penyesuaian';

    protected $fillable = [
        'mapa_id',
        'delegasi_personil_asesmen_id',
        'asesor_id',
        'asesi_id',
        'certification_scheme_id',
        'nomor_ak07',
        'potensi_asesi', // Field untuk potensi asesi (array)
        'answers',
        'hasil_penyesuaian',
        'catatan_asesor',
        'asesor_signature',
        'asesor_signed_at',
        'asesor_ip',
        'asesi_signature',
        'asesi_signed_at',
        'asesi_ip',
        'status',
        // NEW: Final Recommendation Fields
        'final_recommendation',
        'recommendation_notes',
        'final_signature_path',
        'final_signed_at',
        'final_signed_by',
    ];

    protected $casts = [
        'potensi_asesi' => 'array',
        'answers' => 'array',
        'hasil_penyesuaian' => 'array',
        'asesor_signed_at' => 'datetime',
        'asesi_signed_at' => 'datetime',
        'final_signed_at' => 'datetime',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function mapa()
    {
        return $this->belongsTo(Mapa::class);
    }

    public function delegasi()
    {
        return $this->belongsTo(DelegasiPersonilAsesmen::class, 'delegasi_personil_asesmen_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function finalSignedByUser()
    {
        return $this->belongsTo(User::class, 'final_signed_by');
    }

    public function asesi()
    {
        return $this->belongsTo(User::class, 'asesi_id');
    }

    public function certificationScheme()
    {
        return $this->belongsTo(CertificationScheme::class);
    }

    /* ===================== ACCESSORS ===================== */

    /**
     * Get potensi asesi as text array
     */
    public function getPotensiAsesiTextAttribute()
    {
        if (!$this->potensi_asesi || empty($this->potensi_asesi)) {
            return [];
        }

        $options = \App\Models\KelompokKerja::POTENSI_ASESI_OPTIONS;

        return array_map(function ($key) use ($options) {
            return $options[$key] ?? $key;
        }, $this->potensi_asesi);
    }

    /**
     * Get formatted potensi asesi for display
     */
    public function getFormattedPotensiAsesiAttribute()
    {
        if (!$this->potensi_asesi || empty($this->potensi_asesi)) {
            return 'Tidak ada';
        }

        $formatted = [];
        $options = \App\Models\KelompokKerja::POTENSI_ASESI_OPTIONS;

        foreach ($this->potensi_asesi as $key) {
            $formatted[] = strtoupper($key) . ': ' . ($options[$key] ?? $key);
        }

        return implode(', ', $formatted);
    }

    /**
     * Get potensi asesi count
     */
    public function getPotensiAsesiCountAttribute()
    {
        return is_array($this->potensi_asesi) ? count($this->potensi_asesi) : 0;
    }

    /**
     * Get recommendation label
     */
    public function getRecommendationLabelAttribute()
    {
        if ($this->final_recommendation === 'continue') {
            return 'Dilanjutkan';
        } elseif ($this->final_recommendation === 'not_continue') {
            return 'Tidak Dilanjutkan';
        }
        return 'Belum Ada Rekomendasi';
    }

    /* ===================== HELPER METHODS ===================== */

    public static function generateNomorAk07($certificationSchemeId, $asesorId)
    {
        $scheme = CertificationScheme::find($certificationSchemeId);
        $year = now()->year;
        $month = now()->format('m');

        $lastAk07 = self::where('certification_scheme_id', $certificationSchemeId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest('id')
            ->first();

        $sequence = $lastAk07 ? intval(substr($lastAk07->nomor_ak07, -4)) + 1 : 1;

        return sprintf(
            'AK07/%s/%s/%s/%04d',
            $scheme->code_1,
            $asesorId,
            $year . $month,
            $sequence
        );
    }

    /**
     * Check if AK07 is completed (both asesor and asesi signed)
     */
    public function isCompleted()
    {
        return $this->status === 'completed'
            && !is_null($this->asesor_signature)
            && !is_null($this->asesi_signature);
    }

    /**
     * Check if AK07 can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'waiting_asesi']);
    }

    /**
     * Check if AK07 can show final recommendation view
     */
    public function canShowFinalRecommendation()
    {
        return $this->status === 'completed' && is_null($this->final_recommendation);
    }

    /**
     * Check if final recommendation already exists
     */
    public function hasFinalRecommendation()
    {
        return !is_null($this->final_recommendation);
    }

    /**
     * Get specific answer by question number
     */
    public function getAnswer($questionNumber)
    {
        return $this->answers["q{$questionNumber}"] ?? ['answer' => null, 'keterangan' => null];
    }

    /**
     * Check if potensi asesi is selected
     */
    public function hasPotensiAsesi($key)
    {
        if (!$this->potensi_asesi) {
            return false;
        }

        return in_array($key, $this->potensi_asesi);
    }

    /**
     * Get all selected potensi asesi labels
     */
    public function getSelectedPotensiAsesiLabels()
    {
        if (!$this->potensi_asesi || empty($this->potensi_asesi)) {
            return [];
        }

        $options = \App\Models\KelompokKerja::POTENSI_ASESI_OPTIONS;
        $labels = [];

        foreach ($this->potensi_asesi as $key) {
            if (isset($options[$key])) {
                $labels[$key] = $options[$key];
            }
        }

        return $labels;
    }

    /**
     * Get final recommendation status for display
     */
    public function getFinalRecommendationStatus()
    {
        if ($this->status !== 'completed') {
            return [
                'label' => 'AK07 Belum Selesai',
                'badge_class' => 'badge-secondary',
                'can_recommend' => false,
            ];
        }

        if (is_null($this->final_recommendation)) {
            return [
                'label' => 'Menunggu Rekomendasi',
                'badge_class' => 'badge-warning',
                'can_recommend' => true,
            ];
        }

        if ($this->final_recommendation === 'continue') {
            return [
                'label' => 'Dilanjutkan',
                'badge_class' => 'badge-success',
                'can_recommend' => false,
            ];
        }

        if ($this->final_recommendation === 'not_continue') {
            return [
                'label' => 'Tidak Dilanjutkan (Reschedule)',
                'badge_class' => 'badge-danger',
                'can_recommend' => false,
            ];
        }

        return [
            'label' => 'Status Tidak Diketahui',
            'badge_class' => 'badge-secondary',
            'can_recommend' => false,
        ];
    }

    /**
     * Scope: Get completed AK07
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get pending final recommendation
     */
    public function scopePendingRecommendation($query)
    {
        return $query->where('status', 'completed')
            ->whereNull('final_recommendation');
    }

    /**
     * Scope: Get with recommendation
     */
    public function scopeWithRecommendation($query)
    {
        return $query->whereNotNull('final_recommendation');
    }

    /**
     * Scope: Get by recommendation type
     */
    public function scopeByRecommendation($query, $type)
    {
        if (in_array($type, ['continue', 'not_continue'])) {
            return $query->where('final_recommendation', $type);
        }

        return $query;
    }
}
