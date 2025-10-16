<?php

// Model: TukRequest
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TukRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_tuk',
        'apl01_id',
        'user_id',
        'tanggal_assessment',
        'lokasi_assessment',
        'tanda_tangan_peserta_path',
        'jam_mulai',
        'catatan_rekomendasi',
        'recommended_at',
        'recommended_by',
        'jenis_tuk',
        'is_rescheduled',
        'rescheduled_at',
        'rescheduled_by',
        'reschedule_reason',
    ];

    protected $casts = [
        'tanggal_assessment' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'recommended_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'is_rescheduled' => 'boolean',
    ];

    public function getJenisTukAttribute()
    {
        // If jenis_tuk is set directly on TukRequest, use it
        if (isset($this->attributes['jenis_tuk']) && !empty($this->attributes['jenis_tuk'])) {
            return $this->attributes['jenis_tuk'];
        }

        // Otherwise, get from APL01
        if ($this->apl01) {
            return $this->apl01->tuk === 'Sewaktu' ? 'sewaktu' : 'mandiri';
        }

        // Default fallback
        return 'sewaktu';
    }

    // ==================== RELATIONSHIPS ====================

    public function apl01(): BelongsTo
    {
        return $this->belongsTo(Apl01Pendaftaran::class, 'apl01_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recommendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }
    public function delegasi()
    {
        return $this->hasOne(DelegasiPersonilAsesmen::class, 'tuk_request_id');
    }
    public function rescheduledBy()
    {
        return $this->belongsTo(User::class, 'rescheduled_by');
    }

    // ==================== LIFECYCLE HOOKS ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_tuk)) {
                $model->kode_tuk = static::generateKodeTuk();
            }

            // Auto-set jenis_tuk from APL01 if not set
            if (empty($model->jenis_tuk) && $model->apl01_id) {
                $apl01 = \App\Models\Apl01Pendaftaran::find($model->apl01_id);
                if ($apl01) {
                    $model->jenis_tuk = $apl01->tuk === 'Sewaktu' ? 'Sewaktu' : 'Mandiri';
                }
            }
        });

        static::deleting(function ($tukRequest) {
            // Cleanup signature file when deleting
            if ($tukRequest->tanda_tangan_peserta_path && Storage::disk('public')->exists($tukRequest->tanda_tangan_peserta_path)) {
                Storage::disk('public')->delete($tukRequest->tanda_tangan_peserta_path);
            }
        });
    }

    // ==================== KODE TUK GENERATION ====================

    public static function generateKodeTuk()
    {
        $runningNumber = \App\Models\NumberSequence::generate('tuk_running_number');

        $kodeTuk = $runningNumber;

        return $kodeTuk;
    }

    // ==================== SIGNATURE METHODS ====================

    /**
     * Store signature with organized folder structure
     */
    public function storeSignature($signatureData)
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

            $fileName = 'tuk_signature_' . time() . '.png';

            // Create organized folder structure
            $year = date('Y');
            $month = date('m');
            $userName = \Str::slug($this->user->name ?? 'user-' . $this->user_id);

            $folderPath = "tuk-signatures/{$year}/{$month}/{$userName}";
            $filePath = $folderPath . '/' . $fileName;

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
     * Sign by peserta
     */
    public function signByPeserta($signatureData)
    {
        try {
            $signaturePath = $this->storeSignature($signatureData);

            return $this->update([
                'tanda_tangan_peserta_path' => $signaturePath,
            ]);
        } catch (\Exception $e) {
            // Fallback: simpan sebagai base64 jika gagal store ke file
            \Log::warning('Failed to store TUK signature file, using base64 fallback: ' . $e->getMessage());

            return $this->update([
                'tanda_tangan_peserta_path' => $signatureData,
            ]);
        }
    }

    /**
     * Get signature URL
     */
    public function getSignatureUrl()
    {
        if (!$this->tanda_tangan_peserta_path) {
            return null;
        }

        // Handle base64 signature
        if (str_starts_with($this->tanda_tangan_peserta_path, 'data:image/')) {
            return $this->tanda_tangan_peserta_path;
        }

        // Handle file path signature
        if (Storage::disk('public')->exists($this->tanda_tangan_peserta_path)) {
            return Storage::disk('public')->url($this->tanda_tangan_peserta_path);
        }

        return null;
    }

    /**
     * Check if signature exists
     */
    public function hasSignature()
    {
        return !empty($this->tanda_tangan_peserta_path);
    }

    // ==================== RECOMMENDATION METHODS ====================

    /**
     * Admin create recommendation
     */
    public function createRecommendation($tanggalAssessment, $jamMulai, $catatanRekomendasi, $adminId)
    {
        return $this->update([
            'tanggal_assessment' => $tanggalAssessment,
            'jam_mulai' => $jamMulai,
            'catatan_rekomendasi' => $catatanRekomendasi,
            'recommended_at' => now(),
            'recommended_by' => $adminId,
        ]);
    }

    /**
     * Check if recommendation exists
     */
    public function hasRecommendation()
    {
        return !empty($this->recommended_at) && !empty($this->recommended_by);
    }

    // ==================== ACCESSORS & HELPERS ====================

    public function getFormattedTanggalAssessmentAttribute()
    {
        return $this->tanggal_assessment ? $this->tanggal_assessment->format('d F Y') : null;
    }

    public function getFormattedJamMulaiAttribute()
    {
        return $this->jam_mulai ? $this->jam_mulai->format('H:i') : null;
    }

    public function getFormattedRecommendedAtAttribute()
    {
        return $this->recommended_at ? $this->recommended_at->format('d F Y H:i') : null;
    }

    /**
     * Check if form is complete (ready for admin recommendation)
     */
    public function isComplete()
    {
        return !empty($this->tanggal_assessment) && !empty($this->lokasi_assessment) && !empty($this->tanda_tangan_peserta_path);
    }

    /**
     * Get signature info for display
     */
    public function getSignatureInfo()
    {
        $signaturePath = $this->tanda_tangan_peserta_path;

        if (!$signaturePath) {
            return [
                'exists' => false,
                'url' => null,
                'is_base64' => false,
                'file_exists' => false,
            ];
        }

        $isBase64 = str_starts_with($signaturePath, 'data:image/');
        $fileExists = false;
        $url = null;

        if ($isBase64) {
            $url = $signaturePath;
        } else {
            $fileExists = Storage::disk('public')->exists($signaturePath);
            if ($fileExists) {
                $url = Storage::disk('public')->url($signaturePath);
            }
        }

        return [
            'exists' => true,
            'url' => $url,
            'is_base64' => $isBase64,
            'file_exists' => $fileExists,
        ];
    }

    public function markAsRescheduled($reason, $adminId)
    {
        try {
            DB::beginTransaction();

            // Delete existing delegasi if exists
            if ($this->delegasi) {
                $this->delegasi->delete();
            }

            // Delete rekomendasi LSP if exists
            if ($this->apl01 && $this->apl01->rekomendasiLsp) {
                $rekomendasiLsp = $this->apl01->rekomendasiLsp;

                // Delete signature file if exists
                if ($rekomendasiLsp->ttd_admin_path && Storage::disk('public')->exists($rekomendasiLsp->ttd_admin_path)) {
                    Storage::disk('public')->delete($rekomendasiLsp->ttd_admin_path);
                }

                $rekomendasiLsp->delete();
            }

            // Update TukRequest
            $this->update([
                'is_rescheduled' => true,
                'rescheduled_at' => now(),
                'rescheduled_by' => $adminId,
                'reschedule_reason' => $reason,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking TUK as rescheduled: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if rescheduled
     */
    public function isRescheduled()
    {
        return $this->is_rescheduled === true;
    }
}
