<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Apl02EvidenceSubmission extends Model
{
    use HasFactory;

    protected $table = 'apl_02_evidence_submissions';

    protected $fillable = [
        'apl_02_id',
        'portfolio_file_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'mime_type',
        'description',
        'is_submitted'
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function apl02()
    {
        return $this->belongsTo(Apl02::class, 'apl_02_id');
    }

    public function portfolioFile()
    {
        return $this->belongsTo(PortfolioFile::class, 'portfolio_file_id');
    }

    /* ===================== ACCESSORS ===================== */

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return '0 B';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileUrlAttribute()
    {
        if (empty($this->file_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public function getFullFilePathAttribute()
    {
        if (empty($this->file_path)) {
            return null;
        }

        return storage_path('app/public/' . $this->file_path);
    }

    /* ===================== FILE MANAGEMENT METHODS ===================== */

    /**
     * Delete the file from storage and the database record
     */
    public function deleteFile()
    {
        try {
            // Delete physical file if it exists
            if (!empty($this->file_path) && Storage::disk('public')->exists($this->file_path)) {
                $deleted = Storage::disk('public')->delete($this->file_path);

                if ($deleted) {
                    Log::info('Evidence file deleted from storage', [
                        'file_path' => $this->file_path,
                        'file_name' => $this->file_name,
                        'evidence_id' => $this->id
                    ]);
                } else {
                    Log::warning('Failed to delete evidence file from storage', [
                        'file_path' => $this->file_path,
                        'evidence_id' => $this->id
                    ]);
                }
            }

            // Delete database record
            $this->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting evidence file: ' . $e->getMessage(), [
                'evidence_id' => $this->id,
                'file_path' => $this->file_path,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Check if file exists in storage
     */
    public function fileExists()
    {
        if (empty($this->file_path)) {
            return false;
        }

        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Get file contents
     */
    public function getFileContents()
    {
        if (!$this->fileExists()) {
            return null;
        }

        return Storage::disk('public')->get($this->file_path);
    }

    /**
     * Move file to new path (for reorganization)
     */
    public function moveFile($newPath)
    {
        if (!$this->fileExists()) {
            throw new \Exception('Original file does not exist');
        }

        try {
            // Move file to new location
            $moved = Storage::disk('public')->move($this->file_path, $newPath);

            if ($moved) {
                // Update database record
                $oldPath = $this->file_path;
                $this->update(['file_path' => $newPath]);

                Log::info('Evidence file moved successfully', [
                    'evidence_id' => $this->id,
                    'old_path' => $oldPath,
                    'new_path' => $newPath
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error moving evidence file: ' . $e->getMessage(), [
                'evidence_id' => $this->id,
                'old_path' => $this->file_path,
                'new_path' => $newPath
            ]);

            throw $e;
        }
    }

    /* ===================== VALIDATION METHODS ===================== */

    /**
     * Validate file type
     */
    public function isValidFileType()
    {
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'];
        return in_array(strtolower($this->file_type), $allowedTypes);
    }

    /**
     * Check if file is image
     */
    public function isImage()
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
        return in_array(strtolower($this->file_type), $imageTypes);
    }

    /**
     * Check if file is document
     */
    public function isDocument()
    {
        $docTypes = ['pdf', 'doc', 'docx'];
        return in_array(strtolower($this->file_type), $docTypes);
    }

    /**
     * Check if file can be previewed in browser
     */
    public function canPreview()
    {
        $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
        return in_array(strtolower($this->file_type), $previewableTypes);
    }
}
