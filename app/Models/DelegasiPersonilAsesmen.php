<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DelegasiPersonilAsesmen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'delegasi_personil_asesmen';

    protected $fillable = ['asesi_id', 'certification_scheme_id', 'apl01_id', 'tuk_request_id', 'jenis_ujian', 'verifikator_tuk_id', 'verifikator_nik', 'verifikator_spt_date', 'observer_id', 'observer_nik', 'observer_spt_date', 'asesor_id', 'asesor_met', 'asesor_spt_date', 'tanggal_pelaksanaan_asesmen', 'waktu_mulai', 'delegated_by', 'delegated_at', 'notes', 'is_rescheduled', 'rescheduled_at', 'rescheduled_by', 'reschedule_reason'];

    protected $casts = [
        'verifikator_spt_date' => 'date',
        'observer_spt_date' => 'date',
        'asesor_spt_date' => 'date',
        'tanggal_pelaksanaan_asesmen' => 'date',
        'delegated_at' => 'datetime',
        'is_rescheduled' => 'boolean',
        'rescheduled_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the Asesi (Peserta/User)
     */
    public function asesi()
    {
        return $this->belongsTo(User::class, 'asesi_id');
    }

    /**
     * Get the Certification Scheme
     */
    public function certificationScheme()
    {
        return $this->belongsTo(CertificationScheme::class, 'certification_scheme_id');
    }

    /**
     * Get the APL01 that owns this delegation
     */
    public function apl01()
    {
        return $this->belongsTo(Apl01Pendaftaran::class, 'apl01_id');
    }
    public function apl02()
    {
        // Gunakan hasManyThrough atau accessor sebagai gantinya
        return $this->belongsTo(Apl02::class, 'apl02_id');
    }

    /**
     * ATAU gunakan accessor untuk get APL02 via APL01 (RECOMMENDED)
     */
    public function getApl02Attribute()
    {
        return $this->apl01?->apl02;
    }

    /**
     * Check if has APL02
     */
    public function hasApl02()
    {
        return $this->apl01 && $this->apl01->apl02()->exists();
    }

    /**
     * Get APL02 safely through APL01
     * This avoids the ambiguous column error in PostgreSQL
     */
    public function getApl02SafeAttribute()
    {
        if (!$this->apl01) {
            return null;
        }

        return $this->apl01->apl02;
    }

    /**
     * Check if APL02 exists
     */
    public function hasApl02Safe()
    {
        return $this->apl01 && $this->apl01->apl02 !== null;
    }

    /**
     * Get APL02 ID safely
     */
    public function getApl02IdAttribute()
    {
        return $this->hasApl02Safe() ? $this->apl01->apl02->id : null;
    }

    /**
     * Sync APL02 ID from APL01
     * Call this after creating/updating APL02
     */
    public function syncApl02FromApl01()
    {
        if ($this->apl01_id && $this->apl01) {
            $apl02 = $this->apl01->apl02;
            if ($apl02) {
                // If you have apl02_id column, update it
                // $this->apl02_id = $apl02->id;
                // $this->save();
                return $apl02;
            }
        }
        return null;
    }

    public function formKerahasiaan()
    {
        return $this->hasOne(FormKerahasiaan::class, 'delegasi_personil_asesmen_id');
    }

    /**
     * Check if Form Kerahasiaan has been created
     */
    public function hasFormKerahasiaan()
    {
        return $this->formKerahasiaan !== null;
    }

    /**
     * Check if Form Kerahasiaan is completed
     */
    public function isFormKerahasiaanCompleted()
    {
        return $this->formKerahasiaan && $this->formKerahasiaan->status === 'completed';
    }

    /**
     * Get the TUK Request that owns this delegation
     */
    public function tukRequest()
    {
        return $this->belongsTo(TukRequest::class, 'tuk_request_id');
    }

    /**
     * Get the Verifikator TUK user
     */
    public function verifikatorTuk()
    {
        return $this->belongsTo(User::class, 'verifikator_tuk_id');
    }

    /**
     * Get the Observer user
     */
    public function observer()
    {
        return $this->belongsTo(User::class, 'observer_id');
    }

    /**
     * Get the Asesor user
     */
    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    /**
     * Get the admin who delegated
     */
    public function delegatedBy()
    {
        return $this->belongsTo(User::class, 'delegated_by');
    }
    public function rescheduledBy()
    {
        return $this->belongsTo(User::class, 'rescheduled_by');
    }

    public function sptSignature()
    {
        return $this->hasOne(SPTSignature::class, 'delegasi_personil_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope for specific Asesi
     */
    public function scopeForAsesi($query, $asesiId)
    {
        return $query->where('asesi_id', $asesiId);
    }

    /**
     * Scope for specific Certification Scheme
     */
    public function scopeForScheme($query, $schemeId)
    {
        return $query->where('certification_scheme_id', $schemeId);
    }

    /**
     * Scope for specific APL01
     */
    public function scopeForApl01($query, $apl01Id)
    {
        return $query->where('apl01_id', $apl01Id);
    }

    /**
     * Scope for specific TUK Request
     */
    public function scopeForTukRequest($query, $tukRequestId)
    {
        return $query->where('tuk_request_id', $tukRequestId);
    }

    /**
     * Scope for specific verifikator
     */
    public function scopeByVerifikator($query, $verifikatorId)
    {
        return $query->where('verifikator_tuk_id', $verifikatorId);
    }

    /**
     * Scope for specific observer
     */
    public function scopeByObserver($query, $observerId)
    {
        return $query->where('observer_id', $observerId);
    }

    /**
     * Scope for specific asesor
     */
    public function scopeByAsesor($query, $asesorId)
    {
        return $query->where('asesor_id', $asesorId);
    }

    /**
     * Scope for delegations by specific admin
     */
    public function scopeDelegatedBy($query, $adminId)
    {
        return $query->where('delegated_by', $adminId);
    }

    /**
     * Scope for delegations within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pelaksanaan_asesmen', [$startDate, $endDate]);
    }

    // ==================== ACCESSORS ====================

    /**
     * Get nama lengkap asesi dari relasi
     */
    public function getNamaLengkapAsesiAttribute()
    {
        return $this->asesi->nama_lengkap ?? ($this->asesi->name ?? '-');
    }

    /**
     * Get NIK asesi
     */
    public function getNikAsesiAttribute()
    {
        return $this->asesi->nik ?? ($this->asesi->id_number ?? '-');
    }

    /**
     * Get skema sertifikasi name
     */
    public function getSkemaSertifikasiAttribute()
    {
        return $this->certificationScheme->nama ?? '-';
    }

    /**
     * Get kode skema
     */
    public function getKodeSkemaAttribute()
    {
        return $this->certificationScheme->kode ?? '-';
    }

    /**
     * Get nama verifikator TUK
     */
    public function getNamaVerifikatorAttribute()
    {
        return $this->verifikatorTuk->name ?? '-';
    }

    /**
     * Get nama observer
     */
    public function getNamaObserverAttribute()
    {
        return $this->observer->name ?? '-';
    }

    /**
     * Get nama asesor
     */
    public function getNamaAsesorAttribute()
    {
        return $this->asesor->name ?? '-';
    }

    /**
     * Get jenis ujian label
     */
    public function getJenisUjianLabelAttribute()
    {
        return $this->jenis_ujian === 'online' ? 'Online' : 'Paperless Offline';
    }

    /**
     * Check if delegation is complete
     */
    public function getIsCompleteAttribute()
    {
        return $this->verifikator_tuk_id && $this->observer_id && $this->asesor_id && $this->tanggal_pelaksanaan_asesmen;
    }

    /**
     * Get delegation summary
     */
    public function getDelegationSummaryAttribute()
    {
        $summary = [];

        if ($this->verifikatorTuk) {
            $summary[] = "Verifikator: {$this->verifikatorTuk->name}";
        }

        if ($this->observer) {
            $summary[] = "Observer: {$this->observer->name}";
        }

        if ($this->asesor) {
            $summary[] = "Asesor: {$this->asesor->name}";
        }

        return implode(' | ', $summary);
    }

    /**
     * Get formatted waktu mulai
     */
    public function getFormattedWaktuMulaiAttribute()
    {
        if (!$this->waktu_mulai) {
            return '-';
        }

        return \Carbon\Carbon::parse($this->waktu_mulai)->format('H:i');
    }

    /**
     * Get formatted tanggal pelaksanaan
     */
    public function getFormattedTanggalPelaksanaanAttribute()
    {
        if (!$this->tanggal_pelaksanaan_asesmen) {
            return '-';
        }

        return $this->tanggal_pelaksanaan_asesmen->format('d F Y');
    }

    /**
     * Get formatted delegated at
     */
    public function getFormattedDelegatedAtAttribute()
    {
        if (!$this->delegated_at) {
            return '-';
        }

        return $this->delegated_at->format('d F Y H:i');
    }

    /**
     * Get delegated by name
     */
    public function getDelegatedByNameAttribute()
    {
        return $this->delegatedBy->name ?? 'System';
    }

    // ==================== HELPER METHODS ====================

    /**
     * Auto-populate NIK/MET from user relationships
     */
    public function syncPersonilData()
    {
        if ($this->verifikator_tuk_id && !$this->verifikator_nik) {
            $this->verifikator_nik = $this->verifikatorTuk->id_number ?? null;
        }

        if ($this->observer_id && !$this->observer_nik) {
            $this->observer_nik = $this->observer->id_number ?? null;
        }

        if ($this->asesor_id && !$this->asesor_met) {
            $this->asesor_met = $this->asesor->id_number ?? null;
        }

        return $this;
    }

    /**
     * Create delegation with auto-population
     */
    public static function createDelegation(array $data, $delegatedById = null)
    {
        $delegation = new static($data);
        $delegation->delegated_by = $delegatedById ?? auth()->id();
        $delegation->delegated_at = now();
        $delegation->syncPersonilData();
        $delegation->save();

        return $delegation;
    }

    /**
     * Update delegation with auto-population
     */
    public function updateDelegation(array $data)
    {
        $this->fill($data);
        $this->syncPersonilData();
        $this->save();

        return $this;
    }

    /**
     * Get complete delegation info as array
     */
    public function getDelegationInfo()
    {
        return [
            'asesi' => [
                'id' => $this->asesi_id,
                'nama' => $this->nama_lengkap_asesi,
                'nik' => $this->nik_asesi,
            ],
            'skema' => [
                'id' => $this->certification_scheme_id,
                'nama' => $this->skema_sertifikasi,
                'kode' => $this->kode_skema,
            ],
            'verifikator' => [
                'id' => $this->verifikator_tuk_id,
                'nama' => $this->nama_verifikator,
                'nik' => $this->verifikator_nik,
                'spt_date' => $this->verifikator_spt_date?->format('d/m/Y'),
            ],
            'observer' => [
                'id' => $this->observer_id,
                'nama' => $this->nama_observer,
                'nik' => $this->observer_nik,
                'spt_date' => $this->observer_spt_date?->format('d/m/Y'),
            ],
            'asesor' => [
                'id' => $this->asesor_id,
                'nama' => $this->nama_asesor,
                'met' => $this->asesor_met,
                'spt_date' => $this->asesor_spt_date?->format('d/m/Y'),
            ],
            'jadwal' => [
                'tanggal' => $this->formatted_tanggal_pelaksanaan,
                'waktu_mulai' => $this->formatted_waktu_mulai,
                'jenis_ujian' => $this->jenis_ujian_label,
            ],
            'delegasi' => [
                'by' => $this->delegated_by_name,
                'at' => $this->formatted_delegated_at,
                'notes' => $this->notes,
            ],
        ];
    }

    /**
     * Check if has specific personil
     */
    public function hasVerifikator()
    {
        return !is_null($this->verifikator_tuk_id);
    }

    public function hasObserver()
    {
        return !is_null($this->observer_id);
    }

    public function hasAsesor()
    {
        return !is_null($this->asesor_id);
    }

    /**
     * Get missing personil list
     */
    public function getMissingPersonil()
    {
        $missing = [];

        if (!$this->hasVerifikator()) {
            $missing[] = 'Verifikator TUK';
        }

        if (!$this->hasObserver()) {
            $missing[] = 'Observer';
        }

        if (!$this->hasAsesor()) {
            $missing[] = 'Asesor';
        }

        return $missing;
    }

    public function hasSPTGenerated()
    {
        return $this->sptSignature()->exists();
    }

    /**
     * Check if SPT has been signed
     */
    public function isSPTSigned()
    {
        return $this->sptSignature && $this->sptSignature->is_signed;
    }

    /**
     * Get SPT status
     */
    public function getSPTStatus()
    {
        if (!$this->hasSPTGenerated()) {
            return 'not_generated';
        }

        if ($this->sptSignature->is_pending) {
            return 'pending_signature';
        }

        return 'signed';
    }

    /**
     * Get SPT status badge HTML
     */
    public function getSPTStatusBadge()
    {
        $status = $this->getSPTStatus();

        return match ($status) {
            'not_generated' => '<span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Belum Digenerate</span>',
            'pending_signature' => '<span class="badge bg-warning"><i class="bi bi-clock-history me-1"></i>Menunggu TTD</span>',
            'signed' => '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Sudah TTD</span>',
            default => '<span class="badge bg-light text-dark">-</span>',
        };
    }

    /**
     * Get the MAPA (Merencanakan Aktivitas dan Proses Asesmen) for this delegation
     */
    public function mapa()
    {
        return $this->hasOne(Mapa::class, 'delegasi_personil_asesmen_id');
    }

    /**
     * Check if MAPA has been created
     */
    public function hasMapaAttribute()
    {
        return $this->mapa !== null;
    }

    /**
     * Get MAPA status for display
     */
    public function getMapaStatusAttribute()
    {
        if (!$this->mapa) {
            return [
                'exists' => false,
                'status' => 'not_created',
                'text' => 'Belum Dibuat',
                'color' => 'danger',
                'badge' => '<span class="badge bg-danger">Belum Dibuat</span>',
            ];
        }

        $statusMap = [
            'draft' => ['text' => 'Draft', 'color' => 'warning'],
            'submitted' => ['text' => 'Submitted', 'color' => 'success'],
            'approved' => ['text' => 'Approved', 'color' => 'primary'],
        ];

        $status = $statusMap[$this->mapa->status] ?? ['text' => 'Unknown', 'color' => 'secondary'];

        return [
            'exists' => true,
            'status' => $this->mapa->status,
            'text' => $status['text'],
            'color' => $status['color'],
            'badge' => '<span class="badge bg-' . $status['color'] . '">' . $status['text'] . '</span>',
            'data' => $this->mapa,
        ];
    }

    public function isRescheduled()
    {
        return $this->is_rescheduled === true;
    }

    /**
     * Get TUK type (Sewaktu or Mandiri)
     */
    public function getTukType()
    {
        if ($this->tuk_request_id) {
            return 'Sewaktu';
        }
        return 'Mandiri';
    }

    public function getFormattedRescheduledAtAttribute()
    {
        return $this->rescheduled_at ? $this->rescheduled_at->format('d F Y H:i') : null;
    }
}
