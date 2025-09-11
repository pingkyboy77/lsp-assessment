<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Apl01Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'apl_01_pendaftarans';

    protected $fillable = [
        'nomor_apl_01', 'selected_requirement_template_id', 'user_id', 'certification_scheme_id', 
        'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'kebangsaan', 
        'alamat_rumah', 'no_telp_rumah', 'kota_rumah', 'provinsi_rumah', 'kode_pos', 'no_hp', 
        'email', 'pendidikan_terakhir', 'nama_sekolah_terakhir', 'jabatan', 'nama_tempat_kerja', 
        'kategori_pekerjaan', 'nama_jalan_kantor', 'kota_kantor', 'provinsi_kantor', 
        'kode_pos_kantor', 'negara_kantor', 'no_telp_kantor', 'tujuan_asesmen', 'tuk', 
        'kategori_peserta', 'training_provider', 'pernah_asesmen_lsp', 'pernah_aplikasi', 
        'aplikasi_yang_digunakan', 'bisa_share_screen', 'bisa_gunakan_browser', 
        'nama_lengkap_ktp', 'pernyataan_benar', 'tanda_tangan_asesi', 'tanggal_tanda_tangan_asesi', 
        'tanda_tangan_asesor', 'tanggal_tanda_tangan_asesor', 'nama_asesor', 'requirement_answers', 
        'status', 'notes', 'submitted_at', 'reviewed_at', 'reviewed_by'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'requirement_answers' => 'array',
        'aplikasi_yang_digunakan' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'pernyataan_benar' => 'boolean',
        'tanggal_tanda_tangan_asesi' => 'datetime',
        'tanggal_tanda_tangan_asesor' => 'datetime',
    ];

    /* ===================== LIFECYCLE HOOKS ===================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_apl_01)) {
                // Hanya generate jika NumberSequence class ada
                if (class_exists('App\Models\NumberSequence')) {
                    $model->nomor_apl_01 = \App\Models\NumberSequence::generate('apl_01_pendaftaran');
                }
            }
        });
    }

    protected static function booted()
    {
        static::deleting(function ($apl) {
            $apl->cleanupFiles();
        });
    }

    /* ===================== RELATIONSHIPS ===================== */

    public function selectedRequirementTemplate()
    {
        return $this->belongsTo(RequirementTemplate::class, 'selected_requirement_template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function certificationScheme()
    {
        return $this->belongsTo(CertificationScheme::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Region relationships - PERBAIKAN: Pastikan relasi menggunakan field yang benar
    public function kotaRumah()
    {
        return $this->belongsTo(RegionKab::class, 'kota_rumah', 'id');
    }

    public function provinsiRumah()
    {
        return $this->belongsTo(RegionProv::class, 'provinsi_rumah', 'id');
    }

    public function kotaKantor()
    {
        return $this->belongsTo(RegionKab::class, 'kota_kantor', 'id');
    }

    public function provinsiKantor()
    {
        return $this->belongsTo(RegionProv::class, 'provinsi_kantor', 'id');
    }

    // PERBAIKAN: Tambahkan alias untuk backward compatibility
    public function provinceRumah()
    {
        return $this->provinsiRumah();
    }

    public function provinceKantor()
    {
        return $this->provinsiKantor();
    }

    public function lembagaPelatihan()
    {
        return $this->belongsTo(LembagaPelatihan::class, 'training_provider');
    }

    /* ===================== SCOPES ===================== */

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByScheme($query, $schemeId)
    {
        return $query->where('certification_scheme_id', $schemeId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'review', 'reviewed', 'approved', 'rejected']);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /* ===================== ACCESSORS ===================== */

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'open' => 'Re Open',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'returned' => 'Returned for Revision',
            default => 'Unknown',
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'open' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getJenisKelaminTextAttribute()
    {
        return match ($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    public function getTanggalLahirFormattedAttribute()
    {
        if (!$this->tanggal_lahir) {
            return '-';
        }

        // Handle string date
        if (is_string($this->tanggal_lahir)) {
            try {
                return Carbon::parse($this->tanggal_lahir)->format('d/m/Y');
            } catch (\Exception $e) {
                return '-';
            }
        }

        // Handle Carbon instance
        if ($this->tanggal_lahir instanceof Carbon) {
            return $this->tanggal_lahir->format('d/m/Y');
        }

        return '-';
    }

    public function getKategoriPesertaTextAttribute()
    {
        return $this->kategori_peserta === 'individu' ? 'Individu / Mandiri' : 'Training Provider';
    }

    public function getAplikasiYangDigunakanTextAttribute()
    {
        if (!$this->aplikasi_yang_digunakan) {
            return '-';
        }

        return is_array($this->aplikasi_yang_digunakan) 
            ? implode(', ', $this->aplikasi_yang_digunakan) 
            : $this->aplikasi_yang_digunakan;
    }

    public function getIsEditableAttribute()
    {
        return in_array($this->status, ['draft', 'rejected', 'open']);
    }

    public function getCanSubmitAttribute()
    {
        return $this->status === 'draft' && $this->isComplete();
    }

    public function getCanDeleteAttribute()
    {
        return $this->status === 'draft';
    }

    // PERBAIKAN: Fix method getSelectedUnitsCountAttribute - harus cek relasi yang benar
    public function getSelectedUnitsCountAttribute()
    {
        // Cek apakah ada relasi dengan certification scheme dan unit kompetensi
        if ($this->certificationScheme && $this->certificationScheme->activeUnitKompetensis) {
            return $this->certificationScheme->activeUnitKompetensis->count();
        }
        return 0;
    }

    public function getLembagaPelatihanNamaAttribute()
    {
        return $this->lembagaPelatihan->nama ?? ($this->training_provider ? 'Lembaga tidak ditemukan' : null);
    }

    /* ===================== STATUS MANAGEMENT ===================== */

    public function submit()
    {
        if (!$this->canSubmit) {
            return false;
        }

        return $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function approve($reviewedBy, $notes = null)
    {
        return $this->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewedBy,
            'notes' => $notes,
        ]);
    }

    public function reject($reviewedBy, $notes)
    {
        return $this->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewedBy,
            'notes' => $notes,
        ]);
    }

    public function setUnderReview($reviewedBy, $notes = null)
    {
        return $this->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewedBy,
            'notes' => $notes,
        ]);
    }

    public function returnForRevision($reviewedBy, $notes)
    {
        return $this->update([
            'status' => 'returned',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewedBy,
            'notes' => $notes,
        ]);
    }

    /* ===================== DATA POPULATION ===================== */

    public function fillFromProfile($profile)
    {
        if (!$profile) {
            return $this;
        }

        $profileFields = [
            'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 
            'kebangsaan', 'alamat_rumah', 'no_telp_rumah', 'kota_rumah', 'provinsi_rumah', 
            'kode_pos', 'no_hp', 'email', 'pendidikan_terakhir', 'nama_sekolah_terakhir', 
            'jabatan', 'nama_tempat_kerja', 'kategori_pekerjaan', 'nama_jalan_kantor', 
            'kota_kantor', 'provinsi_kantor', 'kode_pos_kantor', 'negara_kantor', 'no_telp_kantor'
        ];

        $fillData = [];
        foreach ($profileFields as $field) {
            if (isset($profile->$field)) {
                $fillData[$field] = $profile->$field;
            }
        }

        $this->fill($fillData);
        return $this;
    }

    /* ===================== REQUIREMENT MANAGEMENT ===================== */

    public function getRequirementAnswer($requirementId)
    {
        return $this->requirement_answers[$requirementId] ?? null;
    }

    public function getRequirementItemAnswer($itemId)
    {
        $answers = $this->requirement_answers ?? [];
        return $answers[$itemId] ?? null;
    }

    public function setRequirementItemAnswer($itemId, $value)
    {
        $answers = $this->requirement_answers ?? [];
        $answers[$itemId] = $value;
        $this->requirement_answers = $answers;
        return $this;
    }

    public function getRequirementResponses()
    {
        // Alias untuk konsistensi
        return $this->requirement_answers ?? [];
    }

    public function getSelectedRequirementTemplate()
    {
        return $this->selected_requirement_template_id;
    }

    public function setSelectedRequirementTemplate($templateId)
    {
        $this->selected_requirement_template_id = $templateId;
        return $this;
    }

    /* ===================== FILE MANAGEMENT ===================== */

    public function hasRequirementFile($itemId)
    {
        $answer = $this->getRequirementItemAnswer($itemId);
        return $answer && is_string($answer) && Storage::disk('public')->exists($answer);
    }

    public function getRequirementFileUrl($itemId)
    {
        $answer = $this->getRequirementItemAnswer($itemId);
        if ($answer && is_string($answer) && Storage::disk('public')->exists($answer)) {
            return Storage::url($answer);
        }
        return null;
    }

    public function getRequirementFileName($itemId)
    {
        $answer = $this->getRequirementItemAnswer($itemId);
        if ($answer && is_string($answer)) {
            return basename($answer);
        }
        return null;
    }

    public function deleteRequirementFile($itemId)
    {
        $answer = $this->getRequirementItemAnswer($itemId);
        if ($answer && Storage::disk('public')->exists($answer)) {
            Storage::disk('public')->delete($answer);
            $this->setRequirementItemAnswer($itemId, null);
            return true;
        }
        return false;
    }

    private function cleanupFiles()
    {
        if ($this->requirement_answers) {
            foreach ($this->requirement_answers as $itemId => $answer) {
                if (is_string($answer) && Storage::disk('public')->exists($answer)) {
                    Storage::disk('public')->delete($answer);
                }
            }
        }
    }

    /* ===================== DIGITAL SIGNATURE ===================== */

    public function signByAsesi($signatureData)
    {
        return $this->update([
            'tanda_tangan_asesi' => $signatureData,
            'tanggal_tanda_tangan_asesi' => now(),
        ]);
    }

    public function signByAsesor($signatureData, $asesorName)
    {
        return $this->update([
            'tanda_tangan_asesor' => $signatureData,
            'tanggal_tanda_tangan_asesor' => now(),
            'nama_asesor' => $asesorName,
        ]);
    }

    public function getHasTandaTanganAsesiAttribute()
    {
        return !empty($this->tanda_tangan_asesi);
    }

    public function getHasTandaTanganAsesorAttribute()
    {
        return !empty($this->tanda_tangan_asesor);
    }

    /* ===================== UNIT KOMPETENSI MANAGEMENT - PERBAIKAN ===================== */

    // PERBAIKAN: Method ini perlu diperbaiki karena tidak ada field selected_units di fillable
    public function getSelectedUnitsInfo()
    {
        // Karena semua unit dalam skema otomatis dipilih, return semua active units
        if ($this->certificationScheme) {
            return $this->certificationScheme->activeUnitKompetensis ?? collect();
        }
        return collect();
    }

    public function getSelectedUnitsWithInfo()
    {
        // Return semua unit aktif dari skema sertifikasi
        if ($this->certificationScheme) {
            return $this->certificationScheme->activeUnitKompetensis ?? collect();
        }
        return collect();
    }

    /* ===================== TEMPLATE REQUIREMENT VALIDATION - PERBAIKAN ===================== */

    public function getTemplateRequirementResponses($templateId)
    {
        $allResponses = $this->requirement_answers ?? [];
        $templateResponses = [];

        // PERBAIKAN: Pastikan relasi ter-load dengan benar
        if (!$this->certificationScheme) {
            // Lazy load jika belum ter-load
            $this->load('certificationScheme.requirementTemplates.activeItems');
        }

        if (!$this->certificationScheme?->requirementTemplates) {
            return $templateResponses;
        }

        $template = $this->certificationScheme->requirementTemplates->find($templateId);
        if ($template?->activeItems) {
            foreach ($template->activeItems as $item) {
                if (isset($allResponses[$item->id])) {
                    $templateResponses[$item->id] = $allResponses[$item->id];
                }
            }
        }

        return $templateResponses;
    }

    public function isTemplateRequirementComplete($templateId = null)
    {
        $templateId = $templateId ?? $this->selected_requirement_template_id;

        if (!$templateId) {
            return false;
        }

        // PERBAIKAN: Pastikan relasi ter-load
        if (!$this->certificationScheme) {
            $this->load('certificationScheme.requirementTemplates.activeItems');
        }

        if (!$this->certificationScheme) {
            return false;
        }

        $template = $this->certificationScheme->requirementTemplates->find($templateId);
        if (!$template?->activeItems) {
            return true; // Jika tidak ada item, anggap complete
        }

        $answers = $this->requirement_answers ?? [];

        foreach ($template->activeItems as $item) {
            if (!$item->is_required) {
                continue; // Skip item yang tidak wajib
            }

            $answer = $answers[$item->id] ?? null;

            // Cek jika item kosong
            if (empty($answer)) {
                return false;
            }

            // Validasi khusus untuk file upload
            if ($item->type === 'file_upload' && is_string($answer)) {
                if (!Storage::disk('public')->exists($answer)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getTemplateCompletionPercentage($templateId = null)
    {
        $templateId = $templateId ?? $this->selected_requirement_template_id;

        if (!$templateId) {
            return 0;
        }

        // PERBAIKAN: Pastikan relasi ter-load
        if (!$this->certificationScheme) {
            $this->load('certificationScheme.requirementTemplates.activeItems');
        }

        if (!$this->certificationScheme) {
            return 0;
        }

        $template = $this->certificationScheme->requirementTemplates->find($templateId);
        if (!$template?->activeItems) {
            return 100; // Jika tidak ada item, anggap 100%
        }

        $totalItems = $template->activeItems->count();
        if ($totalItems === 0) {
            return 100;
        }

        $completedItems = 0;
        $answers = $this->requirement_answers ?? [];

        foreach ($template->activeItems as $item) {
            $answer = $answers[$item->id] ?? null;

            if (!empty($answer)) {
                // Cek file upload dengan benar
                if ($item->type === 'file_upload' && is_string($answer)) {
                    if (Storage::disk('public')->exists($answer)) {
                        $completedItems++;
                    }
                } else {
                    $completedItems++;
                }
            }
        }

        return round(($completedItems / $totalItems) * 100);
    }

    public function getTemplateRequirementSummary($templateId = null)
    {
        $templateId = $templateId ?? $this->selected_requirement_template_id;

        if (!$templateId) {
            return null;
        }

        // PERBAIKAN: Pastikan relasi ter-load
        if (!$this->certificationScheme) {
            $this->load('certificationScheme.requirementTemplates.activeItems');
        }

        if (!$this->certificationScheme) {
            return null;
        }

        $template = $this->certificationScheme->requirementTemplates->find($templateId);
        if (!$template) {
            return null;
        }

        $totalItems = $template->activeItems?->count() ?? 0;
        $requiredItems = $template->activeItems?->where('is_required', true)->count() ?? 0;
        $completedItems = 0;
        $completedRequiredItems = 0;

        $answers = $this->requirement_answers ?? [];

        if ($template->activeItems) {
            foreach ($template->activeItems as $item) {
                $answer = $answers[$item->id] ?? null;

                $isCompleted = false;
                if (!empty($answer)) {
                    // Validasi file dengan benar
                    if ($item->type === 'file_upload' && is_string($answer)) {
                        $isCompleted = Storage::disk('public')->exists($answer);
                    } else {
                        $isCompleted = true;
                    }
                }

                if ($isCompleted) {
                    $completedItems++;
                    if ($item->is_required) {
                        $completedRequiredItems++;
                    }
                }
            }
        }

        return [
            'template_name' => $template->name,
            'total_items' => $totalItems,
            'required_items' => $requiredItems,
            'completed_items' => $completedItems,
            'completed_required_items' => $completedRequiredItems,
            'completion_percentage' => $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 100,
            'required_completion_percentage' => $requiredItems > 0 ? round(($completedRequiredItems / $requiredItems) * 100) : 100,
            'is_complete' => $completedRequiredItems === $requiredItems,
        ];
    }

    /* ===================== FORM COMPLETION VALIDATION - PERBAIKAN ===================== */

    public function isComplete()
    {
        // Check basic required fields
        $requiredFields = [
            'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 
            'alamat_rumah', 'kota_rumah', 'provinsi_rumah', 'no_hp', 'email', 
            'pendidikan_terakhir', 'nama_sekolah_terakhir', 'jabatan', 
            'nama_tempat_kerja', 'kategori_pekerjaan'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // Check template requirements if applicable
        if ($this->hasTemplateRequirements()) {
            $selectedTemplate = $this->selected_requirement_template_id;
            if (!$selectedTemplate || !$this->isTemplateRequirementComplete($selectedTemplate)) {
                return false;
            }
        }

        return true;
    }

    private function hasTemplateRequirements()
    {
        // PERBAIKAN: Pastikan relasi ter-load
        if (!$this->certificationScheme) {
            $this->load('certificationScheme.requirementTemplates');
        }
        
        return $this->certificationScheme?->requirementTemplates?->count() > 0;
    }

    /* ===================== UTILITY METHODS ===================== */

    public function generateAplNumber()
    {
        if (!empty($this->nomor_apl_01)) {
            return $this->nomor_apl_01;
        }

        $year = date('Y');
        $month = date('m');
        $lastNumber = static::whereNotNull('nomor_apl_01')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $number = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $this->nomor_apl_01 = "APL01/{$year}/{$month}/{$number}";
        $this->save();

        return $this->nomor_apl_01;
    }

    /* ===================== HELPER METHODS - PERBAIKAN ===================== */

    public function getCompletionPercentage()
    {
        $totalSections = 3; // Basic info, template requirements, signature
        $completedSections = 0;

        // 1. Basic info completion
        $requiredFields = [
            'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 
            'alamat_rumah', 'kota_rumah', 'provinsi_rumah', 'no_hp', 'email', 
            'pendidikan_terakhir', 'nama_sekolah_terakhir', 'jabatan', 
            'nama_tempat_kerja', 'kategori_pekerjaan'
        ];

        $completedFields = 0;
        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }
        
        if ($completedFields === count($requiredFields)) {
            $completedSections++;
        }

        // 2. Template requirements completion
        if ($this->hasTemplateRequirements()) {
            if ($this->selected_requirement_template_id && $this->isTemplateRequirementComplete()) {
                $completedSections++;
            }
        } else {
            $completedSections++; // No template requirements = completed
        }

        // 3. Signature completion (only for submitted forms)
        if ($this->status === 'draft' || !empty($this->tanda_tangan_asesi)) {
            $completedSections++;
        }

        return round(($completedSections / $totalSections) * 100);
    }

    public function getSummaryData()
    {
        return [
            'basic_info' => [
                'nama_lengkap' => $this->nama_lengkap,
                'nik' => $this->nik,
                'email' => $this->email,
                'no_hp' => $this->no_hp,
            ],
            'address' => [
                'alamat_rumah' => $this->alamat_rumah,
                'kota_rumah' => $this->kota_rumah,
                'provinsi_rumah' => $this->provinsi_rumah,
            ],
            'employment' => [
                'nama_tempat_kerja' => $this->nama_tempat_kerja,
                'jabatan' => $this->jabatan,
                'kategori_pekerjaan' => $this->kategori_pekerjaan,
            ],
            'education' => [
                'pendidikan_terakhir' => $this->pendidikan_terakhir,
                'nama_sekolah_terakhir' => $this->nama_sekolah_terakhir,
            ],
            'assessment' => [
                'tujuan_asesmen' => $this->tujuan_asesmen,
                'kategori_peserta' => $this->kategori_peserta_text,
                'selected_units_count' => $this->selected_units_count,
            ],
            'status' => [
                'status' => $this->status_text,
                'submitted_at' => $this->submitted_at ? $this->formatDatetime($this->submitted_at) : null,
                'completion_percentage' => $this->getCompletionPercentage(),
            ],
        ];
    }

    /**
     * Helper method to format datetime safely
     */
    private function formatDatetime($datetime)
    {
        if (!$datetime) {
            return null;
        }

        if (is_string($datetime)) {
            try {
                return Carbon::parse($datetime)->format('d/m/Y H:i');
            } catch (\Exception $e) {
                return null;
            }
        }

        if ($datetime instanceof Carbon) {
            return $datetime->format('d/m/Y H:i');
        }

        return null;
    }
}