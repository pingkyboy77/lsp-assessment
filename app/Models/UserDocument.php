<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'file_path',
        'original_name',
        'file_name',
        'file_size',
        'mime_type',
        'description'
    ];

    protected $appends = [
        'file_exists',
        'file_url',
        'file_size_formatted',
        'file_extension',
        'file_type_text'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ===================== FILE STORAGE METHODS ===================== */

    /**
     * Store user document with organized folder structure
     * Structure: user-documents/YYYY/MM/user-name/document-type/
     */
    public static function storeDocument($file, $userId, $documentType, $description = null)
    {
        try {
            $user = User::findOrFail($userId);
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::random(10) . '.' . $extension;

            $year = date('Y');
            $month = date('m');
            $userName = Str::slug($user->name ?? 'user-' . $userId);
            $documentTypeSlug = Str::slug($documentType);

            $folderPath = "user-documents/{$year}/{$month}/{$userName}/{$documentTypeSlug}";
            $filePath = $folderPath . '/' . $fileName;

            // PERBAIKAN: Hapus 'public/' prefix
            $storedPath = $file->storeAs($folderPath, $fileName, 'public');

            if (!$storedPath) {
                throw new \Exception('Failed to store document');
            }

            return static::updateOrCreate(
                [
                    'user_id' => $userId,
                    'document_type' => $documentType
                ],
                [
                    'file_path' => $storedPath, // Gunakan $storedPath, bukan $filePath
                    'original_name' => $originalName,
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'description' => $description
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception('Error storing user document: ' . $e->getMessage());
        }
    }

    /**
     * Replace existing document
     */
    public function replaceDocument($file, $description = null)
    {
        try {
            // Delete old file if exists
            if ($this->file_exists) {
                Storage::disk('public')->delete($this->file_path);
            }

            // Get user data
            $user = $this->user ?? User::find($this->user_id);
            if (!$user) {
                throw new \Exception('User not found');
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::random(10) . '.' . $extension;

            // Create organized folder structure
            $year = date('Y');
            $month = date('m');
            $userName = Str::slug($user->name ?? 'user-' . $this->user_id);
            $documentTypeSlug = Str::slug($this->document_type);

            $folderPath = "user-documents/{$year}/{$month}/{$userName}/{$documentTypeSlug}";
            $filePath = $folderPath . '/' . $fileName;

            // Store new file
            $storedPath = $file->storeAs('public/' . $folderPath, $fileName);

            if (!$storedPath) {
                throw new \Exception('Failed to store replacement document');
            }

            // Update record
            return $this->update([
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $description ?? $this->description
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error replacing user document: ' . $e->getMessage());
        }
    }

    /* ===================== ACCESSORS ===================== */

    public function getFileExistsAttribute()
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_exists) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return $this->getFileSizeFromStorage();
        }

        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getFileExtensionAttribute()
    {
        if ($this->original_name) {
            return pathinfo($this->original_name, PATHINFO_EXTENSION);
        }

        if ($this->file_path) {
            return pathinfo($this->file_path, PATHINFO_EXTENSION);
        }

        return null;
    }

    public function getFileTypeTextAttribute()
    {
        if (!$this->mime_type) {
            return 'Unknown';
        }

        return match ($this->mime_type) {
            'application/pdf' => 'PDF Document',
            'application/msword' => 'Word Document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word Document',
            'application/vnd.ms-excel' => 'Excel Spreadsheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel Spreadsheet',
            'text/plain' => 'Text File',
            default => $this->isImage() ? 'Image File' : 'File'
        };
    }

    public function getFileIconAttribute()
    {
        if (!$this->mime_type) {
            return 'fa-file';
        }

        return match ($this->mime_type) {
            'application/pdf' => 'fa-file-pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel',
            'text/plain' => 'fa-file-text',
            default => $this->isImage() ? 'fa-file-image' : 'fa-file'
        };
    }

    /* ===================== HELPER METHODS ===================== */

    public function isImage()
    {
        $imageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        return in_array($this->mime_type, $imageTypes);
    }

    public function isDocument()
    {
        if (!$this->mime_type) {
            return false;
        }

        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ]);
    }

    /**
     * Get file size from storage if not stored in database
     */
    private function getFileSizeFromStorage()
    {
        if ($this->file_exists) {
            $sizeInBytes = Storage::disk('public')->size($this->file_path);
            return number_format($sizeInBytes / 1024, 2) . ' KB';
        }
        return 'Unknown';
    }

    /**
     * Download the document
     */
    public function download()
    {
        if (!$this->file_exists) {
            throw new \Exception('File not found in storage');
        }

        $downloadName = $this->original_name ?? ($this->document_type . '.' . $this->file_extension);
        return Storage::disk('public')->download($this->file_path, $downloadName);
    }

    /**
     * Delete document file and record
     */
    public function deleteDocument()
    {
        try {
            // Delete from storage
            if ($this->file_exists) {
                Storage::disk('public')->delete($this->file_path);
            }

            // Delete record
            return $this->delete();
        } catch (\Exception $e) {
            throw new \Exception('Error deleting document: ' . $e->getMessage());
        }
    }

    /**
     * Move document to new location (when user data changes)
     */
    public function moveToNewLocation($newUserId = null)
    {
        try {
            $userId = $newUserId ?? $this->user_id;
            $user = User::find($userId);

            if (!$user) {
                throw new \Exception('User not found');
            }

            if (!$this->file_exists) {
                // Just update the record if file doesn't exist
                if ($newUserId) {
                    $this->update(['user_id' => $newUserId]);
                }
                return true;
            }

            // Create new path structure
            $year = date('Y');
            $month = date('m');
            $userName = Str::slug($user->name ?? 'user-' . $userId);
            $documentTypeSlug = Str::slug($this->document_type);

            $newFolderPath = "user-documents/{$year}/{$month}/{$userName}/{$documentTypeSlug}";
            $newFilePath = $newFolderPath . '/' . $this->file_name;

            // Move file in storage
            Storage::disk('public')->makeDirectory($newFolderPath);
            Storage::disk('public')->move($this->file_path, $newFilePath);

            // Update record
            return $this->update([
                'user_id' => $userId,
                'file_path' => $newFilePath
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error moving document: ' . $e->getMessage());
        }
    }

    /* ===================== SCOPES ===================== */

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDocumentType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ]);
    }

    public function scopeByMimeType($query, $mimeType)
    {
        return $query->where('mime_type', 'like', $mimeType . '%');
    }

    /* ===================== STATIC METHODS ===================== */

    /**
     * Get all documents for a user grouped by type
     */
    public static function getUserDocumentsGrouped($userId)
    {
        $documents = static::where('user_id', $userId)->get();
        return $documents->groupBy('document_type');
    }

    /**
     * Get storage statistics for a user
     */
    public static function getUserStorageStats($userId)
    {
        $documents = static::where('user_id', $userId)->get();
        $totalSize = 0;
        $fileCount = 0;
        $typeBreakdown = [];

        foreach ($documents as $document) {
            if ($document->file_exists) {
                $fileSize = $document->file_size ?? Storage::disk('public')->size($document->file_path);
                $totalSize += $fileSize;
                $fileCount++;

                $type = $document->document_type;
                if (!isset($typeBreakdown[$type])) {
                    $typeBreakdown[$type] = [
                        'count' => 0,
                        'size' => 0
                    ];
                }

                $typeBreakdown[$type]['count']++;
                $typeBreakdown[$type]['size'] += $fileSize;
            }
        }

        return [
            'total_files' => $fileCount,
            'total_size' => $totalSize,
            'total_size_formatted' => static::formatFileSize($totalSize),
            'by_type' => $typeBreakdown
        ];
    }

    /**
     * Cleanup orphaned files (files in storage without database records)
     */
    public static function cleanupOrphanedFiles($userId = null)
    {
        $cleanedCount = 0;
        $basePath = storage_path('app/public/user-documents');

        if (!is_dir($basePath)) {
            return $cleanedCount;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace(storage_path('app/public/'), '', $file->getPathname());

                $query = static::where('file_path', $relativePath);
                if ($userId) {
                    $query->where('user_id', $userId);
                }

                // Check if file exists in database
                if (!$query->exists()) {
                    unlink($file->getPathname());
                    $cleanedCount++;
                }
            }
        }

        return $cleanedCount;
    }

    /**
     * Validate document file
     */
    public static function validateDocument($file, $maxSize = null)
    {
        $maxSize = $maxSize ?? (5 * 1024 * 1024); // 5MB default
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ];

        $errors = [];

        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size of ' . static::formatFileSize($maxSize);
        }

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'File type not allowed. Allowed types: PDF, Word, Excel, Images, Text';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Format file size helper
     */
    public static function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get common document types
     */
    public static function getCommonDocumentTypes()
    {
        return [
            'ktp' => 'KTP/Identitas',
            'ijazah' => 'Ijazah',
            'sertifikat' => 'Sertifikat',
            'cv' => 'Curriculum Vitae',
            'foto' => 'Foto',
            'kk' => 'Kartu Keluarga',
            'npwp' => 'NPWP',
            'bpjs' => 'BPJS',
            'surat_kerja' => 'Surat Keterangan Kerja',
            'portofolio' => 'Portofolio',
            'lainnya' => 'Dokumen Lainnya'
        ];
    }

    /* ===================== MIGRATION HELPER METHODS ===================== */

    /**
     * Migrate old file structure to new organized structure
     */
    public function migrateToNewStructure()
    {
        if (!$this->file_exists) {
            return false;
        }

        try {
            $user = $this->user ?? User::find($this->user_id);
            if (!$user) {
                return false;
            }

            // Create new path structure
            $year = date('Y');
            $month = date('m');
            $userName = Str::slug($user->name ?? 'user-' . $this->user_id);
            $documentTypeSlug = Str::slug($this->document_type);

            $newFolderPath = "user-documents/{$year}/{$month}/{$userName}/{$documentTypeSlug}";

            // Generate new file name if not exists
            if (!$this->file_name) {
                $extension = $this->file_extension ?? 'bin';
                $this->file_name = time() . '_' . Str::random(10) . '.' . $extension;
            }

            $newFilePath = $newFolderPath . '/' . $this->file_name;

            // Don't move if already in correct location
            if ($this->file_path === $newFilePath) {
                return true;
            }

            // Move file in storage
            Storage::disk('public')->makeDirectory($newFolderPath);
            Storage::disk('public')->move($this->file_path, $newFilePath);

            // Update record
            return $this->update(['file_path' => $newFilePath]);
        } catch (\Exception $e) {
            \Log::warning("Failed to migrate document {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Batch migrate multiple documents
     */
    public static function batchMigrateToNewStructure($userId = null)
    {
        $query = static::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $documents = $query->get();
        $migratedCount = 0;
        $failedCount = 0;

        foreach ($documents as $document) {
            if ($document->migrateToNewStructure()) {
                $migratedCount++;
            } else {
                $failedCount++;
            }
        }

        return [
            'migrated' => $migratedCount,
            'failed' => $failedCount,
            'total' => $documents->count()
        ];
    }

    /* ===================== UTILITY METHODS ===================== */

    /**
     * Get document preview data for JSON responses
     */
    public function getPreviewData()
    {
        return [
            'id' => $this->id,
            'document_type' => $this->document_type,
            'original_name' => $this->original_name,
            'file_size_formatted' => $this->file_size_formatted,
            'file_type_text' => $this->file_type_text,
            'file_icon' => $this->file_icon,
            'file_exists' => $this->file_exists,
            'file_url' => $this->file_url,
            'is_image' => $this->isImage(),
            'description' => $this->description,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Check if user has specific document type
     */
    public static function userHasDocument($userId, $documentType)
    {
        return static::where('user_id', $userId)
            ->where('document_type', $documentType)
            ->exists();
    }

    /**
     * Get user's document by type
     */
    public static function getUserDocument($userId, $documentType)
    {
        return static::where('user_id', $userId)
            ->where('document_type', $documentType)
            ->first();
    }
}
