<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Apl02 extends Model
{
    use HasFactory;

    protected $table = 'apl_02';

    protected $fillable = ['user_id', 'apl_01_id', 'certification_scheme_id', 'nomor_apl_02', 'status', 'file_folder_path', 'tanda_tangan_asesi', 'tanggal_tanda_tangan_asesi', 'ip_tanda_tangan_asesi', 'tanda_tangan_asesor', 'tanggal_tanda_tangan_asesor', 'ip_tanda_tangan_asesor', 'reviewer_notes', 'submitted_at', 'reviewed_at', 'approved_at', 'reviewer_id', 'completed_at', 'returned_at', 'rejected_at'];

    protected $casts = [
        'tanggal_tanda_tangan_asesi' => 'datetime',
        'tanggal_tanda_tangan_asesor' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /* ===================== RELATIONSHIPS ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asesi()
    {
        return $this->user();
    }

    public function apl01()
    {
        return $this->belongsTo(Apl01Pendaftaran::class, 'apl_01_id');
    }

    public function certificationScheme()
    {
        return $this->belongsTo(CertificationScheme::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function elementAssessments()
    {
        return $this->hasMany(Apl02ElementAssessment::class, 'apl_02_id');
    }

    public function evidenceSubmissions()
    {
        return $this->hasMany(Apl02EvidenceSubmission::class, 'apl_02_id');
    }

    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    /* ===================== SCOPES ===================== */

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByScheme($query, $schemeId)
    {
        return $query->where('certification_scheme_id', $schemeId);
    }

    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'review', 'approved', 'rejected']);
    }

    public function scopeSigned($query)
    {
        return $query->whereNotNull('tanggal_tanda_tangan_asesi');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'approved')->whereNotNull('completed_at');
    }

    /* ===================== ACCESSORS ===================== */

    public function getStatusTextAttribute()
    {
        $statusMap = [
            'draft' => 'Draft',
            'submitted' => 'Submited',
            'reviewed' => 'Reviewed',
            'approved' => 'Approved',
            'returned' => 'Dikembalikan',
            'open' => 'Re Open',
            'rejected' => 'Ditolak',
        ];

        return $statusMap[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        $colorMap = [
            'draft' => 'secondary',
            'submitted' => 'info',
            'review' => 'primary',
            'approved' => 'success',
            'open' => 'warning',
            'rejected' => 'danger',
            'returned' => 'warning',
        ];

        return $colorMap[$this->status] ?? 'secondary';
    }

    public function getCompetencyLevelAttribute()
    {
        if ($this->competency_percentage >= 80) {
            return 'Sangat Kompeten';
        } elseif ($this->competency_percentage >= 65) {
            return 'Kompeten';
        } elseif ($this->competency_percentage >= 50) {
            return 'Cukup Kompeten';
        } else {
            return 'Belum Kompeten';
        }
    }

    public function getIsSignedByAsesiAttribute()
    {
        return !empty($this->tanda_tangan_asesi) && !empty($this->tanggal_tanda_tangan_asesi);
    }

    public function getIsSignedByAsesorAttribute()
    {
        return !empty($this->tanda_tangan_asesor) && !empty($this->tanggal_tanda_tangan_asesor);
    }

    public function getIsFullySignedAttribute()
    {
        return $this->is_signed_by_asesi && $this->is_signed_by_asesor;
    }

    /* ===================== FILE MANAGEMENT ===================== */

    public function generateFolderPath()
    {
        $scheme = $this->certificationScheme;
        $user = $this->user;

        $year = now()->format('Y');
        $month = now()->format('m');

        $folderName = sprintf('apl02-files/%s/%s/%s/%s', $year, $month, $scheme->code_1, $this->sanitizeFileName($user->nama_lengkap ?? $user->name));

        return $folderName;
    }

    private function sanitizeFileName($name)
    {
        // Remove special characters and spaces
        return preg_replace('/[^A-Za-z0-9\-_]/', '_', $name);
    }

    public function ensureFolderPath()
    {
        if (empty($this->file_folder_path)) {
            $this->file_folder_path = $this->generateFolderPath();
            $this->save();
        }

        // Create directory if not exists
        if (!Storage::disk('public')->exists($this->file_folder_path)) {
            Storage::disk('public')->makeDirectory($this->file_folder_path);
        }

        return $this->file_folder_path;
    }

    public function getFullFileUrl($fileName)
    {
        $path = $this->file_folder_path . '/' . $fileName;
        return Storage::disk('public')->url($path);
    }

    /* ===================== SIGNATURE MANAGEMENT ===================== */

    public function signByAsesi($signatureData, $ipAddress = null)
    {
        $this->update([
            'tanda_tangan_asesi' => $signatureData,
            'tanggal_tanda_tangan_asesi' => now(),
            'ip_tanda_tangan_asesi' => $ipAddress ?? request()->ip(),
        ]);

        return true;
    }

    public function signByAsesor($signatureData, $asesorId, $ipAddress = null)
    {
        $this->update([
            'tanda_tangan_asesor' => $signatureData,
            'tanggal_tanda_tangan_asesor' => now(),
            'ip_tanda_tangan_asesor' => $ipAddress ?? request()->ip(),
            'asesor_id' => $asesorId,
        ]);

        return true;
    }

    public function clearAsesiSignature()
    {
        $this->update([
            'tanda_tangan_asesi' => null,
            'tanggal_tanda_tangan_asesi' => null,
            'ip_tanda_tangan_asesi' => null,
        ]);
    }

    public function clearAsesorSignature()
    {
        $this->update([
            'tanda_tangan_asesor' => null,
            'tanggal_tanda_tangan_asesor' => null,
            'ip_tanda_tangan_asesor' => null,
            'asesor_id' => null,
        ]);
    }

    /* ===================== ASSESSMENT MANAGEMENT ===================== */

    public function canBeSubmitted()
    {
        // Check if all elements have been assessed
        $totalElements = $this->certificationScheme
            ->elemenKompetensis()
            ->whereHas('unitKompetensi', function ($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->count();

        $assessedElements = $this->elementAssessments()->whereNotNull('assessment_result')->count();

        // Check if asesi has signed
        $isSignedByAsesi = $this->is_signed_by_asesi;

        // Check if minimum evidence is submitted
        $requiredEvidence = $this->certificationScheme->portfolioFiles()->where('is_required', true)->where('is_active', true)->count();

        $submittedEvidence = $this->evidenceSubmissions()->where('is_submitted', true)->count();

        return [
            'can_submit' => $assessedElements >= $totalElements && $isSignedByAsesi && $submittedEvidence >= $requiredEvidence,
            'checks' => [
                'all_elements_assessed' => $assessedElements >= $totalElements,
                'asesi_signed' => $isSignedByAsesi,
                'minimum_evidence' => $submittedEvidence >= $requiredEvidence,
            ],
            'stats' => [
                'assessed_elements' => $assessedElements,
                'total_elements' => $totalElements,
                'submitted_evidence' => $submittedEvidence,
                'required_evidence' => $requiredEvidence,
            ],
        ];
    }

    private function generateNomorApl02()
    {
        $scheme = $this->certificationScheme;
        $year = now()->format('Y');
        $month = now()->format('m');

        // Format: APL02/SCHEME_CODE/YYYY/MM/XXXX
        $prefix = "APL02/{$scheme->code_1}/{$year}/{$month}/";

        $lastNumber = static::where('nomor_apl_02', 'like', $prefix . '%')
            ->orderBy('nomor_apl_02', 'desc')
            ->first();

        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->nomor_apl_02, -4);
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return $prefix . str_pad($newNum, 4, '0', STR_PAD_LEFT);
    }

    public function calculateCompetencyStats()
    {
        $assessments = $this->elementAssessments;

        $stats = [
            'total_elements' => $assessments->count(),
            'kompeten_count' => $assessments->where('assessment_result', 'kompeten')->count(),
            'belum_kompeten_count' => $assessments->where('assessment_result', 'belum_kompeten')->count(),
        ];

        // You can store these stats in additional fields if needed
        return $stats;
    }

    /* ===================== STATUS MANAGEMENT ===================== */

    public function approve($reviewerId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewer_id' => $reviewerId,
            'reviewed_at' => now(),
            'reviewer_notes' => $notes,
            'completed_at' => now(),
        ]);

        return true;
    }

    public function reject($reviewerId, $notes)
    {
        $this->update([
            'status' => 'rejected',
            'reviewer_id' => $reviewerId,
            'reviewed_at' => now(),
            'reviewer_notes' => $notes,
        ]);

        return true;
    }

    public function returnToAsesi($reviewerId, $notes)
    {
        $this->update([
            'status' => 'returned',
            'reviewer_id' => $reviewerId,
            'reviewed_at' => now(),
            'reviewer_notes' => $notes,
        ]);

        return true;
    }

    public function setReview($reviewerId)
    {
        $this->update([
            'status' => 'review',
            'reviewer_id' => $reviewerId,
        ]);

        return true;
    }

    /* ===================== VALIDATION & EXPORT ===================== */

    public function validateCompleteness()
    {
        $issues = [];

        // Check element assessments
        $totalElements = $this->certificationScheme
            ->elemenKompetensis()
            ->whereHas('unitKompetensi', function ($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->count();

        $assessedElements = $this->elementAssessments()->whereNotNull('assessment_result')->count();

        if ($assessedElements < $totalElements) {
            $issues[] = 'Masih ada ' . ($totalElements - $assessedElements) . ' elemen yang belum dinilai';
        }

        // Check evidence submissions
        $requiredEvidence = $this->certificationScheme->portfolioFiles()->where('is_required', true)->where('is_active', true)->count();

        $submittedEvidence = $this->evidenceSubmissions()->where('is_submitted', true)->count();

        if ($submittedEvidence < $requiredEvidence) {
            $issues[] = 'Masih kurang ' . ($requiredEvidence - $submittedEvidence) . ' bukti yang wajib diupload';
        }

        // Check signatures
        if (!$this->is_signed_by_asesi) {
            $issues[] = 'Belum ditandatangani oleh asesi';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
            'stats' => [
                'assessed_elements' => $assessedElements,
                'total_elements' => $totalElements,
                'submitted_evidence' => $submittedEvidence,
                'required_evidence' => $requiredEvidence,
                'competency_percentage' => $this->competency_percentage,
            ],
        ];
    }

    public function generateReport()
    {
        return [
            'apl02_info' => [
                'nomor' => $this->nomor_apl_02,
                'status' => $this->status_text,
                'asesi' => $this->user->nama_lengkap ?? $this->user->name,
                'scheme' => $this->certificationScheme->full_name,
                'submitted_at' => $this->submitted_at?->format('d/m/Y H:i'),
                'completed_at' => $this->completed_at?->format('d/m/Y H:i'),
            ],
            'competency_summary' => [
                'total_elements' => $this->total_elements,
                'kompeten_count' => $this->kompeten_count,
                'belum_kompeten_count' => $this->belum_kompeten_count,
                'percentage' => $this->competency_percentage,
                'level' => $this->competency_level,
            ],
            'signatures' => [
                'asesi_signed' => $this->is_signed_by_asesi,
                'asesi_signed_at' => $this->asesi_signed_at?->format('d/m/Y H:i'),
                'asesor_signed' => $this->is_signed_by_asesor,
                'asesor_signed_at' => $this->asesor_signed_at?->format('d/m/Y H:i'),
                'asesor_name' => $this->asesor?->name,
            ],
            'evidence_summary' => [
                'total_evidence' => $this->evidenceSubmissions()->count(),
                'submitted_evidence' => $this->evidenceSubmissions()->where('is_submitted', true)->count(),
                'file_size_total' => $this->evidenceSubmissions()->sum('file_size'),
            ],
            'validation' => $this->validateCompleteness(),
        ];
    }

    /* ===================== DATA LOADING ===================== */

    public function loadCompleteData()
    {
        return $this->load([
            'user:id,name,email',
            'user.profile:id,user_id,nama_lengkap,no_hp,no_telp_rumah,nik,tempat_lahir,tanggal_lahir,jenis_kelamin,alamat_rumah,kota_rumah,provinsi_rumah',
            'apl01:id,nomor_apl_01,user_id,certification_scheme_id,status,nama_lengkap',
            'certificationScheme:id,code_1,code_2,nama,jenjang,is_active',
            'certificationScheme.units' => function ($query) {
                $query
                    ->select('id', 'certification_scheme_id', 'kode_unit', 'judul_unit', 'is_active')
                    ->where('is_active', true)
                    ->with([
                        'elemenKompetensis' => function ($subQuery) {
                            $subQuery
                                ->select('id', 'unit_kompetensi_id', 'kode_elemen', 'judul_elemen', 'is_active')
                                ->where('is_active', true)
                                ->with([
                                    'kriteriaKerjas' => function ($kritQuery) {
                                        $kritQuery->select('id', 'elemen_kompetensi_id', 'kode_kriteria', 'uraian_kriteria', 'is_active')->where('is_active', true);
                                    },
                                ]);
                        },
                    ]);
            },
            'certificationScheme.portfolioFiles' => function ($query) {
                $query->select('portfolio_files.id', 'portfolio_files.unit_kompetensi_id', 'portfolio_files.document_name', 'portfolio_files.document_description', 'portfolio_files.is_required', 'portfolio_files.is_active', 'portfolio_files.sort_order')->where('portfolio_files.is_active', true)->orderBy('portfolio_files.sort_order', 'asc');
            },
            'evidenceSubmissions:id,apl_02_id,portfolio_file_id,file_name,file_path,file_type,file_size,mime_type,description,is_submitted,created_at',
            'evidenceSubmissions.portfolioFile:id,document_name,is_required',
            'reviewer:id,name,email',
            'reviewer.profile:id,user_id,nama_lengkap',
            'asesor:id,name,email',
            'asesor.profile:id,user_id,nama_lengkap,no_hp',
        ]);
    }

    public function loadWithAssessmentData()
    {
        return $this->load([
            'user:id,name,email',
            'user.profile:id,user_id,nama_lengkap',
            'certificationScheme:id,code_1,nama,jenjang',
            'certificationScheme.units' => function ($query) {
                $query->where('is_active', true)->with([
                    'elemenKompetensis' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    },
                ]);
            },
            'evidenceSubmissions' => function ($query) {
                $query->where('is_submitted', true);
            },
        ]);
    }

    public function loadWithEvidenceData()
    {
        return $this->load([
            'certificationScheme.portfolioFiles' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            },
            'evidenceSubmissions.portfolioFile',
        ]);
    }

    public function getSignatureUrl()
    {
        if (empty($this->tanda_tangan_asesi)) {
            return null;
        }

        // Jika sudah berupa URL lengkap (data lama), return as is
        if (str_starts_with($this->tanda_tangan_asesi, 'http')) {
            return $this->tanda_tangan_asesi;
        }

        // Jika berupa path (data baru), convert ke URL sesuai environment
        return Storage::disk('public')->url($this->tanda_tangan_asesi);
    }

    public function getAsesorSignatureUrl()
    {
        if (empty($this->tanda_tangan_asesor)) {
            return null;
        }

        if (str_starts_with($this->tanda_tangan_asesor, 'http')) {
            return $this->tanda_tangan_asesor;
        }

        return Storage::disk('public')->url($this->tanda_tangan_asesor);
    }

    public function signatureFileExists()
    {
        if (empty($this->tanda_tangan_asesi)) {
            return false;
        }

        // If it's a full URL, we can't easily check file existence
        if (str_starts_with($this->tanda_tangan_asesi, 'http')) {
            return true; // Assume it exists for old format
        }

        // For path format, check if file exists
        return Storage::disk('public')->exists($this->tanda_tangan_asesi);
    }
}
