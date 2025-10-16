<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Apl02ElementAssessment extends Model
{
    use HasFactory;

    protected $table = 'apl_02_element_assessments';
    protected $fillable = [
        'apl_02_id',
        'unit_kompetensi_id',
        'elemen_kompetensi_id',
        'assessment_result',
        'notes', // Now stores JSON of selected documents
        'asesor_feedback',
        'asesor_result',
        'created_at',
        'updated_at'
    ];

    // FIXED: Proper JSON casting for PostgreSQL and MySQL
    protected $casts = [
        'notes' => 'json', // This will handle JSON encoding/decoding automatically
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function apl02()
    {
        return $this->belongsTo(Apl02::class, 'apl_02_id');
    }

    public function unitKompetensi()
    {
        return $this->belongsTo(UnitKompetensi::class, 'unit_kompetensi_id');
    }

    public function elemenKompetensi()
    {
        return $this->belongsTo(ElemenKompetensi::class, 'elemen_kompetensi_id');
    }

    /* ===================== FIXED: ACCESSORS & MUTATORS ===================== */

    /**
     * Get selected documents from notes JSON
     * Now works directly with JSON cast
     */
    public function getSelectedDocumentsAttribute()
    {
        // With JSON casting, $this->notes is already decoded
        if (is_null($this->notes)) {
            return [];
        }

        if (is_array($this->notes)) {
            return $this->notes;
        }

        // Fallback for any string data that wasn't properly cast
        if (is_string($this->notes)) {
            try {
                $decoded = json_decode($this->notes, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to decode element assessment notes JSON', [
                    'assessment_id' => $this->id,
                    'notes' => $this->notes,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [];
    }

    /**
     * Set selected documents as JSON in notes
     * Laravel's JSON casting will handle the encoding automatically
     */
    public function setSelectedDocumentsAttribute($documents)
    {
        if (is_array($documents)) {
            $this->attributes['notes'] = $documents; // JSON cast will encode this
        } else if (is_string($documents)) {
            // If it's already JSON string, try to decode it first to validate
            try {
                $decoded = json_decode($documents, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->attributes['notes'] = $decoded; // Store as array, will be encoded to JSON
                } else {
                    $this->attributes['notes'] = null;
                }
            } catch (\Exception $e) {
                $this->attributes['notes'] = null;
            }
        } else {
            $this->attributes['notes'] = null;
        }
    }

    /**
     * Get portfolio file IDs from selected documents
     */
    public function getSelectedPortfolioIdsAttribute()
    {
        $documents = $this->selected_documents;
        return collect($documents)->map(function ($doc) {
            return $doc['portfolioId'] ?? $doc['portfolio_id'] ?? null;
        })->filter()->unique()->values()->toArray();
    }

    /* ===================== SCOPES ===================== */

    public function scopeByApl02($query, $apl02Id)
    {
        return $query->where('apl_02_id', $apl02Id);
    }

    public function scopeByElement($query, $elementId)
    {
        return $query->where('elemen_kompetensi_id', $elementId);
    }

    public function scopeKompeten($query)
    {
        return $query->where('assessment_result', 'kompeten');
    }

    public function scopeBelumKompeten($query)
    {
        return $query->where('assessment_result', 'belum_kompeten');
    }

    public function scopeWithDocuments($query)
    {
        return $query->whereNotNull('notes')
            ->where('notes', '!=', '[]') // For JSON null/empty array
            ->whereRaw('JSON_TYPE(notes) = "ARRAY"'); // MySQL specific, for PostgreSQL use jsonb_typeof(notes) = 'array'
    }

    /* ===================== HELPER METHODS ===================== */

    /**
     * Check if assessment has selected documents
     */
    public function hasSelectedDocuments()
    {
        return count($this->selected_documents) > 0;
    }

    /**
     * Get document names from selected documents
     */
    public function getSelectedDocumentNames()
    {
        $documents = $this->selected_documents;
        return collect($documents)->map(function ($doc) {
            return $doc['documentName'] ?? $doc['document_name'] ?? null;
        })->filter()->unique()->values()->toArray();
    }

    /**
     * Check if specific portfolio is selected
     */
    public function hasPortfolioSelected($portfolioId)
    {
        $portfolioIds = $this->selected_portfolio_ids;
        return in_array($portfolioId, $portfolioIds);
    }

    /**
     * Add document to selected documents
     */
    public function addSelectedDocument($portfolioId, $documentName)
    {
        $documents = $this->selected_documents;

        // Check if already exists
        $exists = collect($documents)->contains(function ($doc) use ($portfolioId) {
            return ($doc['portfolioId'] ?? $doc['portfolio_id'] ?? null) == $portfolioId;
        });

        if (!$exists) {
            $documents[] = [
                'portfolioId' => $portfolioId,
                'documentName' => $documentName
            ];
            $this->notes = $documents; // This will be automatically cast to JSON
        }

        return $this;
    }

    /**
     * Remove document from selected documents
     */
    public function removeSelectedDocument($portfolioId)
    {
        $documents = $this->selected_documents;
        $filtered = collect($documents)->reject(function ($doc) use ($portfolioId) {
            return ($doc['portfolioId'] ?? $doc['portfolio_id'] ?? null) == $portfolioId;
        })->values()->toArray();

        $this->notes = $filtered; // This will be automatically cast to JSON
        return $this;
    }

    /**
     * Update selected documents array
     */
    public function updateSelectedDocuments(array $documents)
    {
        // Validate structure
        $validDocuments = collect($documents)->filter(function ($doc) {
            $portfolioId = $doc['portfolioId'] ?? $doc['portfolio_id'] ?? null;
            $documentName = $doc['documentName'] ?? $doc['document_name'] ?? null;

            return !empty($portfolioId) && !empty($documentName);
        })->map(function ($doc) {
            // Normalize the structure
            return [
                'portfolioId' => $doc['portfolioId'] ?? $doc['portfolio_id'],
                'documentName' => $doc['documentName'] ?? $doc['document_name']
            ];
        })->values()->toArray();

        $this->notes = $validDocuments; // This will be automatically cast to JSON
        return $this;
    }

    /**
     * Get assessment result with color class
     */
    public function getResultWithColorAttribute()
    {
        switch ($this->assessment_result) {
            case 'kompeten':
                return [
                    'text' => 'Kompeten',
                    'class' => 'result-kompeten',
                    'color' => 'success'
                ];
            case 'belum_kompeten':
                return [
                    'text' => 'Belum Kompeten',
                    'class' => 'result-belum-kompeten',
                    'color' => 'danger'
                ];
            default:
                return [
                    'text' => 'Belum Dinilai',
                    'class' => 'result-not-assessed',
                    'color' => 'secondary'
                ];
        }
    }

    /* ===================== STATIC METHODS ===================== */

    /**
     * Create assessment with selected documents
     */
    public static function createWithDocuments($apl02Id, $unitId, $elementId, $result, array $documents = [])
    {
        return self::create([
            'apl_02_id' => $apl02Id,
            'unit_kompetensi_id' => $unitId,
            'elemen_kompetensi_id' => $elementId,
            'assessment_result' => $result,
            'notes' => $documents // JSON cast will handle encoding
        ]);
    }

    /**
     * FIXED: Update or create assessment with documents
     */
    public static function updateOrCreateWithDocuments($apl02Id, $elementId, $data)
    {
        // Prepare the documents data
        $notesData = null;
        if (isset($data['documents']) && is_array($data['documents'])) {
            $notesData = $data['documents'];
        } elseif (isset($data['notes'])) {
            if (is_string($data['notes'])) {
                // Try to decode JSON string
                try {
                    $decoded = json_decode($data['notes'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $notesData = $decoded;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to decode notes in updateOrCreateWithDocuments', [
                        'notes' => $data['notes'],
                        'error' => $e->getMessage()
                    ]);
                }
            } elseif (is_array($data['notes'])) {
                $notesData = $data['notes'];
            }
        }

        $assessment = self::updateOrCreate(
            [
                'apl_02_id' => $apl02Id,
                'elemen_kompetensi_id' => $elementId,
            ],
            [
                'unit_kompetensi_id' => $data['unit_id'] ?? null,
                'assessment_result' => $data['result'],
                'notes' => $notesData, // JSON cast will handle encoding
            ]
        );

        return $assessment;
    }

    /* ===================== DATABASE-SPECIFIC HELPER METHODS ===================== */

    /**
     * Get scope for records with documents (database-agnostic)
     */
    public function scopeWithDocumentsGeneric($query)
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");

        if ($connection === 'pgsql') {
            // PostgreSQL
            return $query->whereNotNull('notes')
                ->whereRaw("jsonb_typeof(notes) = 'array'")
                ->whereRaw("jsonb_array_length(notes) > 0");
        } else {
            // MySQL
            return $query->whereNotNull('notes')
                ->whereRaw("JSON_TYPE(notes) = 'ARRAY'")
                ->whereRaw("JSON_LENGTH(notes) > 0");
        }
    }

    /**
     * Search documents by portfolio ID (database-agnostic)
     */
    public function scopeHasPortfolio($query, $portfolioId)
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");

        if ($connection === 'pgsql') {
            // PostgreSQL - search in JSONB array
            return $query->whereRaw("notes::jsonb @> ?", [json_encode([['portfolioId' => $portfolioId]])]);
        } else {
            // MySQL - search in JSON array
            return $query->whereRaw("JSON_SEARCH(notes, 'one', ?, NULL, '$[*].portfolioId') IS NOT NULL", [$portfolioId]);
        }
    }
}
