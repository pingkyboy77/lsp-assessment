<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Apl01Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'apl_01_pendaftarans';

    protected $fillable = ['nomor_apl_01', 'selected_requirement_template_id', 'user_id', 'certification_scheme_id', 'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'kebangsaan', 'alamat_rumah', 'no_telp_rumah', 'kota_rumah', 'provinsi_rumah', 'kode_pos', 'no_hp', 'email', 'pendidikan_terakhir', 'nama_sekolah_terakhir', 'jabatan', 'nama_tempat_kerja', 'kategori_pekerjaan', 'nama_jalan_kantor', 'kota_kantor', 'provinsi_kantor', 'kode_pos_kantor', 'negara_kantor', 'no_telp_kantor', 'tujuan_asesmen', 'tuk', 'kategori_peserta', 'training_provider', 'pernah_asesmen_lsp', 'pernah_aplikasi', 'aplikasi_yang_digunakan', 'bisa_share_screen', 'bisa_gunakan_browser', 'nama_lengkap_ktp', 'pernyataan_benar', 'tanda_tangan_asesi', 'tanggal_tanda_tangan_asesi', 'tanda_tangan_asesor', 'tanggal_tanda_tangan_asesor', 'nama_asesor', 'requirement_answers', 'status', 'notes', 'submitted_at', 'reviewed_at', 'reviewed_by', 'completed_at'];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'requirement_answers' => 'array',
        'aplikasi_yang_digunakan' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'pernyataan_benar' => 'boolean',
        'tanggal_tanda_tangan_asesi' => 'datetime',
        'tanggal_tanda_tangan_asesor' => 'datetime',
        'completed_at' => 'datetime',
    ];
    /* ===================== REVIEW & COMPLETION METHODS ===================== */

    public function rekomendasiLsp()
    {
        return $this->hasOne(RekomendasiLSP::class, 'apl01_id');
    }

    public function hasRekomendasi()
    {
        return $this->rekomendasiLsp()->exists();
    }
    /**
     * Approve APL 01 after admin review
     */
    public function approveByAdmin($adminId, $notes = null)
    {
        return $this->update([
            'status' => 'approved',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'completed_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Reject APL 01 and reopen for editing
     */
    public function rejectAndReopen($adminId, $notes)
    {
        return $this->update([
            'status' => 'open',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'completed_at' => null,
            'notes' => $notes,
        ]);
    }

    public function isCompleted()
    {
        return !is_null($this->completed_at) && $this->status === 'approved';
    }

    /**
     * Check if both APL 01 and APL 02 are completed
     */
    public function canShowDelegasi()
    {
        $apl01Completed = $this->isCompleted();

        $apl02Completed = $this->apl02 && $this->apl02->status === 'approved' && !is_null($this->apl02->completed_at);

        return $apl01Completed && $apl02Completed;
    }

    /**
     * Get completion status with details
     */
    public function getCompletionStatus()
    {
        return [
            'apl01' => [
                'completed' => $this->isCompleted(),
                'completed_at' => $this->completed_at,
                'status' => $this->status,
            ],
            'apl02' => [
                'completed' => $this->apl02 && $this->apl02->status === 'approved' && !is_null($this->apl02->completed_at),
                'completed_at' => $this->apl02?->completed_at,
                'status' => $this->apl02?->status,
            ],
            'can_delegasi' => $this->canShowDelegasi(),
        ];
    }

    /* ===================== RELATIONSHIPS ===================== */

    public function selectedRequirementTemplate()
    {
        return $this->belongsTo(RequirementTemplate::class, 'selected_requirement_template_id');
    }
    public function tukRequest()
    {
        return $this->hasOne(TukRequest::class, 'apl01_id');
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

    public function delegasi()
    {
        return $this->hasOne(DelegasiPersonilAsesmen::class, 'apl01_id');
    }

    // Keep existing regional relationships...
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

    public function lembagaPelatihan()
    {
        return $this->belongsTo(LembagaPelatihan::class, 'training_provider');
    }

    public function apl02()
    {
        return $this->hasOne(Apl02::class, 'apl_01_id');
    }

    /* ===================== FILE STORAGE METHODS ===================== */

    /**
     * Store requirement file with organized folder structure
     */
    public function storeRequirementFile($file, $requirementItemId)
    {
        try {
            // Load required relationships if not already loaded
            if (!$this->user || !$this->certificationScheme) {
                $this->load(['user', 'certificationScheme']);
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::random(10) . '.' . $extension;

            // Create organized folder structure: year/month/scheme-code/user-name/apl01
            $year = date('Y');
            $month = date('m');
            $schemeCode = Str::slug($this->certificationScheme->code_1 ?? 'no-scheme');
            $userName = Str::slug($this->user->name ?? 'user-' . $this->user_id);

            $folderPath = "apl01-files/{$year}/{$month}/{$schemeCode}/{$userName}";

            // Store file menggunakan Storage facade
            $storedPath = Storage::disk('public')->putFileAs($folderPath, $file, $fileName);

            if (!$storedPath) {
                throw new \Exception('Failed to store file');
            }

            // Update requirement answers dengan path yang benar
            $this->setRequirementItemAnswer($requirementItemId, $storedPath);
            $this->save();

            return $storedPath;
        } catch (\Exception $e) {
            throw new \Exception('Error storing requirement file: ' . $e->getMessage());
        }
    }

    /**
     * Store signature with organized folder structure
     */
    public function storeSignature($signatureData, $type = 'asesi')
    {
        try {
            // Load user if not already loaded
            if (!$this->user) {
                $this->load('user');
            }

            // Validate signature data
            if (!preg_match('/^data:image\/(\w+);base64,/', $signatureData, $matches)) {
                throw new \Exception('Invalid signature format');
            }

            // Decode base64 signature
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
            $base64Data = str_replace(' ', '+', $base64Data);
            $decodedData = base64_decode($base64Data);

            if (!$decodedData) {
                throw new \Exception('Invalid signature data');
            }

            $fileName = $type . '_signature_' . time() . '.png';

            // Create organized folder structure
            $year = date('Y');
            $month = date('m');
            $schemeCode = $this->certificationScheme ? Str::slug($this->certificationScheme->code_1) : 'no-scheme';
            $userName = Str::slug($this->user->name ?? 'user-' . $this->user_id);

            $folderPath = "apl01-signatures/{$year}/{$month}/{$schemeCode}/{$userName}";
            $filePath = $folderPath . '/' . $fileName;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($folderPath);

            // Store signature
            $stored = Storage::disk('public')->put($filePath, $decodedData);

            if (!$stored) {
                throw new \Exception('Failed to store signature');
            }

            return $filePath;
        } catch (\Exception $e) {
            throw new \Exception('Error storing signature: ' . $e->getMessage());
        }
    }
    /**
     * Enhanced sign by asesi with proper file storage
     */
    public function signByAsesi($signatureData)
    {
        try {
            $signaturePath = $this->storeSignature($signatureData, 'asesi');

            return $this->update([
                'tanda_tangan_asesi' => $signaturePath,
                'tanggal_tanda_tangan_asesi' => now(),
            ]);
        } catch (\Exception $e) {
            // Fallback: simpan sebagai base64 jika gagal store ke file
            \Log::warning('Failed to store signature file, using base64 fallback: ' . $e->getMessage());

            return $this->update([
                'tanda_tangan_asesi' => $signatureData,
                'tanggal_tanda_tangan_asesi' => now(),
            ]);
        }
    }

    /**
     * Enhanced sign by asesor with proper file storage
     */
    public function signByAsesor($signatureData, $asesorName)
    {
        try {
            $signaturePath = $this->storeSignature($signatureData, 'asesor');

            return $this->update([
                'tanda_tangan_asesor' => $signaturePath,
                'tanggal_tanda_tangan_asesor' => now(),
                'nama_asesor' => $asesorName,
            ]);
        } catch (\Exception $e) {
            // Fallback: simpan sebagai base64 jika gagal store ke file
            \Log::warning('Failed to store asesor signature file, using base64 fallback: ' . $e->getMessage());

            return $this->update([
                'tanda_tangan_asesor' => $signatureData,
                'tanggal_tanda_tangan_asesor' => now(),
                'nama_asesor' => $asesorName,
            ]);
        }
    }

    /**
     * Get signature URL
     */
    public function getSignatureUrl($type = 'asesi')
    {
        $signatureField = 'tanda_tangan_' . $type;
        $signaturePath = $this->$signatureField;

        if (!$signaturePath) {
            return null;
        }

        // Handle base64 signature (langsung return jika masih format base64)
        if (str_starts_with($signaturePath, 'data:image/')) {
            return $signaturePath;
        }

        // Handle file path signature - cek apakah file exists
        if (Storage::disk('public')->exists($signaturePath)) {
            // Return path saja tanpa 'storage/' prefix dan tanpa full URL
            return $signaturePath;
        }

        // Log untuk debugging
        \Log::warning("Signature file not found: {$signaturePath}");

        return null;
    }

    /* ===================== FILE MANAGEMENT METHODS ===================== */

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

    public function hasRequirementFile($itemId)
    {
        $answer = $this->getRequirementItemAnswer($itemId);
        return $answer && is_string($answer) && Storage::disk('public')->exists($answer);
    }

    public function getRequirementFileUrl($itemId)
    {
        $answer = $this->getRequirementItemAnswer($itemId);

        if ($answer && is_string($answer)) {
            $exists = Storage::disk('public')->exists($answer);
            if ($exists) {
                return Storage::url($answer); // -> otomatis "storage/apl01-files/..."
            }
        }

        return null;
    }
    public function getRequirementFilePath($itemId)
    {
        $answer = $this->getRequirementItemAnswer($itemId);
        return $answer && is_string($answer) ? storage_path('app/public/' . $answer) : null;
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
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Cleanup all files associated with this APL01
     */
    private function cleanupFiles()
    {
        // Clean up requirement files
        if ($this->requirement_answers) {
            foreach ($this->requirement_answers as $itemId => $answer) {
                if (is_string($answer) && Storage::disk('public')->exists($answer)) {
                    Storage::disk('public')->delete($answer);
                }
            }
        }

        // Clean up signatures
        if ($this->tanda_tangan_asesi && Storage::disk('public')->exists($this->tanda_tangan_asesi)) {
            Storage::disk('public')->delete($this->tanda_tangan_asesi);
        }

        if ($this->tanda_tangan_asesor && Storage::disk('public')->exists($this->tanda_tangan_asesor)) {
            Storage::disk('public')->delete($this->tanda_tangan_asesor);
        }
    }

    /* ===================== LIFECYCLE HOOKS ===================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_apl_01)) {
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

    /* ===================== EXISTING METHODS - KEEP AS IS ===================== */
    // Keep all existing methods from the original model...

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

    public function getIsEditableAttribute()
    {
        return in_array($this->status, ['draft', 'rejected', 'open']);
    }

    public function getCanSubmitAttribute()
    {
        return $this->status === 'draft' && $this->isComplete();
    }

    public function isComplete()
    {
        $requiredFields = ['nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat_rumah', 'kota_rumah', 'provinsi_rumah', 'no_hp', 'email', 'pendidikan_terakhir', 'nama_sekolah_terakhir', 'jabatan', 'nama_tempat_kerja', 'kategori_pekerjaan'];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        if ($this->hasTemplateRequirements()) {
            $selectedTemplate = $this->selected_requirement_template_id;
            if (!$selectedTemplate || !$this->isTemplateRequirementComplete($selectedTemplate)) {
                return false;
            }
        }

        return true;
    }

    public function isTemplateRequirementComplete($templateId = null)
    {
        $templateId = $templateId ?? $this->selected_requirement_template_id;

        if (!$templateId) {
            return false;
        }

        if (!$this->certificationScheme) {
            $this->load('certificationScheme.requirementTemplates.activeItems');
        }

        if (!$this->certificationScheme) {
            return false;
        }

        $template = $this->certificationScheme->requirementTemplates->find($templateId);
        if (!$template?->activeItems) {
            return true;
        }

        $answers = $this->requirement_answers ?? [];

        foreach ($template->activeItems as $item) {
            if (!$item->is_required) {
                continue;
            }

            $answer = $answers[$item->id] ?? null;

            if (empty($answer)) {
                return false;
            }

            // Validate file upload items
            if ($item->type === 'file_upload' && is_string($answer)) {
                if (!Storage::disk('public')->exists($answer)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function hasTemplateRequirements()
    {
        if (!$this->certificationScheme) {
            $this->load('certificationScheme.requirementTemplates');
        }

        return $this->certificationScheme?->requirementTemplates?->count() > 0;
    }

    /* ===================== STATUS MANAGEMENT METHODS ===================== */

    public function submit()
    {
        if (!$this->can_submit) {
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

    /* ===================== HELPER METHODS ===================== */

    /**
     * Get storage statistics for this APL01
     */
    public function getStorageStats()
    {
        $totalSize = 0;
        $fileCount = 0;

        // Count requirement files
        if ($this->requirement_answers) {
            foreach ($this->requirement_answers as $answer) {
                if (is_string($answer) && Storage::disk('public')->exists($answer)) {
                    $totalSize += Storage::disk('public')->size($answer);
                    $fileCount++;
                }
            }
        }

        // Count signature files
        if ($this->tanda_tangan_asesi && Storage::disk('public')->exists($this->tanda_tangan_asesi)) {
            $totalSize += Storage::disk('public')->size($this->tanda_tangan_asesi);
            $fileCount++;
        }

        if ($this->tanda_tangan_asesor && Storage::disk('public')->exists($this->tanda_tangan_asesor)) {
            $totalSize += Storage::disk('public')->size($this->tanda_tangan_asesor);
            $fileCount++;
        }

        return [
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatFileSize($totalSize),
        ];
    }

    public function setUnderReview($reviewedBy, $notes = null)
    {
        return $this->update([
            'status' => 'review',
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

    /* ===================== ENHANCED FILE HANDLING METHODS ===================== */

    /**
     * Get all requirement files with metadata
     */
    public function getRequirementFiles()
    {
        $files = [];
        if (!$this->requirement_answers) {
            return $files;
        }

        foreach ($this->requirement_answers as $itemId => $filePath) {
            if ($this->hasRequirementFile($itemId)) {
                $requirementItem = \App\Models\RequirementItem::find($itemId);
                $files[] = [
                    'item_id' => $itemId,
                    'item_name' => $requirementItem ? $requirementItem->document_name : "Dokumen {$itemId}",
                    'file_path' => $filePath,
                    'file_url' => $this->getRequirementFileUrl($itemId),
                    'file_name' => $this->getRequirementFileName($itemId),
                    'file_size' => $this->getRequirementFileSize($itemId),
                    'file_extension' => pathinfo($filePath, PATHINFO_EXTENSION),
                ];
            }
        }

        return $files;
    }

    /**
     * Get requirement file size
     */
    public function getRequirementFileSize($itemId)
    {
        $answer = $this->getRequirementItemAnswer($itemId);
        if ($answer && is_string($answer) && Storage::disk('public')->exists($answer)) {
            return Storage::disk('public')->size($answer);
        }
        return 0;
    }

    /**
     * Format file size for display
     */
    public function getRequirementFileSizeFormatted($itemId)
    {
        $size = $this->getRequirementFileSize($itemId);
        return $this->formatFileSize($size);
    }

    /**
     * Check if signature exists and get its info
     */
    public function getSignatureInfo($type = 'asesi')
    {
        $signatureField = 'tanda_tangan_' . $type;
        $dateField = 'tanggal_tanda_tangan_' . $type;

        $signaturePath = $this->$signatureField;
        $signatureDate = $this->$dateField;

        // Cek apakah base64
        $isBase64 = $signaturePath && str_starts_with($signaturePath, 'data:image/');
        $fileExists = false;
        $path = null;

        if ($isBase64) {
            $path = $signaturePath; // Return base64 as is
        } elseif ($signaturePath) {
            $fileExists = Storage::disk('public')->exists($signaturePath);
            if ($fileExists) {
                // Return only the path, tidak include 'storage/' atau full URL
                $path = $signaturePath;
            }
        }

        return [
            'exists' => !empty($signaturePath),
            'path' => $path,
            'date' => $signatureDate,
            'formatted_date' => $signatureDate ? $signatureDate->format('d F Y H:i') : null,
            'is_base64' => $isBase64,
            'file_exists' => $fileExists,
        ];
    }

    public function getRequirementFilesSafe()
    {
        $files = [];

        if (!$this->requirement_answers || !is_array($this->requirement_answers)) {
            return $files;
        }

        foreach ($this->requirement_answers as $itemId => $filePath) {
            try {
                // Skip empty paths
                if (!$filePath) {
                    continue;
                }

                // Get requirement item
                $requirementItem = RequirementItem::find($itemId);
                $itemName = $requirementItem ? $requirementItem->document_name : "Dokumen {$itemId}";

                // Check file existence safely
                $fileExists = false;
                $fileSize = 0;
                $fileUrl = null;

                if (Storage::disk('public')->exists($filePath)) {
                    $fileExists = true;
                    $fileSize = Storage::disk('public')->size($filePath);
                    $fileUrl = Storage::url($filePath);
                }

                $files[] = [
                    'item_id' => $itemId,
                    'item_name' => $itemName,
                    'file_path' => $filePath,
                    'file_url' => $fileUrl,
                    'file_exists' => $fileExists,
                    'file_size' => $fileSize,
                    'file_size_formatted' => $fileSize > 0 ? $this->formatFileSize($fileSize) : '0 KB',
                    'file_extension' => pathinfo($filePath, PATHINFO_EXTENSION) ?: 'unknown',
                    'error' => !$fileExists ? 'File tidak ditemukan' : null,
                ];
            } catch (\Exception $e) {
                Log::warning("Error processing requirement file {$itemId}: " . $e->getMessage());

                $files[] = [
                    'item_id' => $itemId,
                    'item_name' => "Dokumen {$itemId}",
                    'file_path' => $filePath,
                    'file_url' => null,
                    'file_exists' => false,
                    'file_size' => 0,
                    'file_size_formatted' => '0 KB',
                    'file_extension' => 'unknown',
                    'error' => 'Error: ' . $e->getMessage(),
                ];
            }
        }

        return $files;
    }

    /**
     * Check if requirement file exists safely
     */
    public function hasRequirementFileSafe($itemId)
    {
        try {
            if (!$this->requirement_answers || !is_array($this->requirement_answers)) {
                return false;
            }

            $filePath = $this->requirement_answers[$itemId] ?? null;
            if (!$filePath) {
                return false;
            }

            return Storage::disk('public')->exists($filePath);
        } catch (\Exception $e) {
            Log::warning("Error checking requirement file {$itemId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get requirement file size safely
     */
    public function getRequirementFileSizeFormattedSafe($itemId)
    {
        try {
            if (!$this->hasRequirementFileSafe($itemId)) {
                return '0 KB';
            }

            $filePath = $this->requirement_answers[$itemId];
            $size = Storage::disk('public')->size($filePath);
            return $this->formatFileSize($size);
        } catch (\Exception $e) {
            Log::warning("Error getting file size for requirement {$itemId}: " . $e->getMessage());
            return '0 KB';
        }
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 KB';
        }
    }

    public function isTukSewaktu()
    {
        return $this->tuk === 'Sewaktu';
    }

    /**
     * Check if TUK is Mandiri (need PDF embed)
     */
    public function isTukMandiri()
    {
        return $this->tuk === 'mandiri';
    }

    /**
     * Check if TUK form is needed
     */
    public function needsTukForm()
    {
        return $this->isTukSewaktu();
    }

    /**
     * Check if TUK form is completed
     */
    public function isTukFormCompleted()
    {
        if (!$this->needsTukForm()) {
            return true; // Mandiri doesn't need form
        }

        return $this->tukRequest && $this->tukRequest->isComplete();
    }

    /**
     * Get TUK status for display
     */
    public function getTukStatusAttribute()
    {
        if ($this->isTukMandiri()) {
            return 'mandiri';
        }

        if (!$this->tukRequest) {
            return 'form_needed';
        }

        if (!$this->tukRequest->isComplete()) {
            return 'form_incomplete';
        }

        if ($this->tukRequest->hasRecommendation()) {
            return 'recommended';
        }

        return 'pending_recommendation';
    }

    /**
     * Get TUK status text for display
     */
    public function getTukStatusTextAttribute()
    {
        return match ($this->tuk_status) {
            'mandiri' => 'TUK Mandiri',
            'form_needed' => 'Perlu Mengisi Form TUK',
            'form_incomplete' => 'Form TUK Belum Lengkap',
            'pending_recommendation' => 'Menunggu Rekomendasi Admin',
            'recommended' => 'Sudah Direkomendasi Admin',
            default => 'Status TUK Tidak Diketahui',
        };
    }

    /**
     * Get TUK status badge color
     */
    public function getTukStatusColorAttribute()
    {
        return match ($this->tuk_status) {
            'mandiri' => 'success',
            'form_needed' => 'warning',
            'form_incomplete' => 'danger',
            'pending_recommendation' => 'info',
            'recommended' => 'success',
            default => 'secondary',
        };
    }
}
