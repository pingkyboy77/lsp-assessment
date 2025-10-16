<?php

namespace App\Services;

use App\Models\Apl02;
use App\Models\UserDocument;
use App\Models\RequirementItem;
use App\Models\Apl01Pendaftaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AplReviewService
{
    public function getApl01ReviewData(Apl01Pendaftaran $apl): array
    {
        $apl->load(['certificationScheme', 'reviewer', 'lembagaPelatihan']);
        // dd($this->getRequirementDocuments($apl));
        return [
            'apl' => $this->formatApl01Data($apl),
            'user_documents' => $this->getUserDocuments($apl->user_id),
            'requirement_documents' => $this->getRequirementDocuments($apl),
        ];
    }

    public function getApl02ReviewData(Apl02 $apl02): array
    {
        $apl02->load(['user', 'apl01:id,nama_lengkap,email', 'certificationScheme:id,nama,code_1', 'elementAssessments', 'evidenceSubmissions.portfolioFile']);

        $certificationScheme = $apl02
            ->certificationScheme()
            ->with([
                'activeUnits.activeElemenKompetensis.activeKriteriaKerjas',
                'activeUnits.activeElemenKompetensis.assessments' => function ($q) use ($apl02) {
                    $q->where('apl_02_id', $apl02->id);
                },
                'activeUnits.portfolioFiles' => function ($q) {
                    $q->where('portfolio_files.is_active', true)->orderBy('portfolio_files.sort_order')->orderBy('portfolio_files.document_name');
                },
            ])
            ->first();

        $assessmentData = $this->buildAssessmentData($certificationScheme, $apl02);

        return [
            'apl02' => $this->formatApl02Data($apl02, $assessmentData),
            'participant' => $this->formatParticipantData($apl02),
            'certification_scheme' => $this->formatCertificationSchemeData($apl02->certificationScheme),
            'assessment_units' => $assessmentData['units'],
        ];
    }

    public function approveApl01(Apl01Pendaftaran $apl, ?string $notes, int $reviewerId): void
    {
        DB::transaction(function () use ($apl, $notes, $reviewerId) {
            $apl->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewer_id' => $reviewerId,
                'catatan_reviewer' => $notes,
            ]);
        });
    }

    public function rejectApl01(Apl01Pendaftaran $apl, string $notes, int $reviewerId): void
    {
        DB::transaction(function () use ($apl, $notes, $reviewerId) {
            $apl->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewer_id' => $reviewerId,
                'catatan_reviewer' => $notes,
            ]);
        });
    }

    public function reopenApl01(Apl01Pendaftaran $apl, ?string $notes, int $userId): void
    {
        DB::transaction(function () use ($apl, $notes, $userId) {
            $apl->update([
                'status' => 'open',
                'reopened_at' => now(),
                'reopened_by' => $userId,
                'reopen_notes' => $notes,
            ]);
        });
    }

    public function approveApl02(Apl02 $apl02, ?string $notes, int $reviewerId): void
    {
        DB::transaction(function () use ($apl02, $notes, $reviewerId) {
            $apl02->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewer_id' => $reviewerId,
                'catatan_reviewer' => $notes,
            ]);
        });
    }

    public function rejectApl02(Apl02 $apl02, string $notes, int $reviewerId): void
    {
        DB::transaction(function () use ($apl02, $notes, $reviewerId) {
            $apl02->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewer_id' => $reviewerId,
                'catatan_reviewer' => $notes,
            ]);
        });
    }

    public function reopenApl02(Apl02 $apl02, ?string $notes, int $userId): void
    {
        DB::transaction(function () use ($apl02, $notes, $userId) {
            $apl02->update([
                'status' => 'open',
                'reopened_at' => now(),
                'reopened_by' => $userId,
                'reopen_notes' => $notes,
            ]);
        });
    }

    public function processBulkAction(string $aplType, array $ids, string $action, ?string $notes, int $userId): array
    {
        $successful = 0;
        $failed = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                if ($aplType === 'apl01') {
                    $apl = Apl01Pendaftaran::findOrFail($id);
                    $this->processSingleApl01Action($apl, $action, $notes, $userId);
                } else {
                    $apl = Apl02::findOrFail($id);
                    $this->processSingleApl02Action($apl, $action, $notes, $userId);
                }
                $successful++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "ID {$id}: " . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'message' => "Berhasil memproses {$successful} dari " . count($ids) . ' item.',
            'details' => [
                'successful' => $successful,
                'failed' => $failed,
                'total' => count($ids),
                'errors' => $errors,
            ],
        ];
    }

    private function formatApl01Data(Apl01Pendaftaran $apl): array
    {
        return [
            'id' => $apl->id,
            'nomor_apl_01' => $apl->nomor_apl_01,
            'nama_lengkap' => $apl->nama_lengkap,
            'email' => $apl->email,
            'no_hp' => $apl->no_hp,
            'status' => $apl->status,
            'status_text' => ucfirst($apl->status),
            'submitted_at' => $apl->submitted_at?->format('d F Y H:i'),
            'certification_scheme' => $apl->certificationScheme->nama ?? 'N/A',
            'reviewer_name' => $apl->reviewer->name ?? null,
            'lembaga_pelatihan' => $apl->lembagaPelatihan->name ?? null,
            'tuk' => $apl->tuk ?? null,
        ];
    }

    private function getUserDocuments(int $userId): array
    {
        return UserDocument::where('user_id', $userId)
            ->get()
            ->map(function (UserDocument $doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'original_name' => $doc->original_name,
                    'file_name' => $doc->file_name,
                    'file_extension' => $doc->file_extension, // accessor dari model
                    'file_size_kb' => $doc->file_size ? round($doc->file_size / 1024, 2) : 0,
                    'file_size_formatted' => $doc->file_size_formatted, // accessor
                    'file_exists' => $doc->file_exists, // accessor
                    'file_url' => $doc->file_url, // accessor
                    'file_type_text' => $doc->file_type_text, // accessor
                    'uploaded_at' => $doc->created_at->format('d M Y H:i'),
                ];
            })
            ->toArray();
    }

    private function getRequirementDocuments(Apl01Pendaftaran $apl): array
    {
        $requirementDocuments = [];

        try {
            if ($apl->requirement_answers && is_array($apl->requirement_answers)) {
                foreach ($apl->requirement_answers as $itemId => $filePath) {
                    try {
                        // Skip kalau tidak ada file path
                        if (!$filePath) {
                            continue;
                        }

                        // Ambil info requirement item
                        $requirementItem = RequirementItem::find($itemId);
                        $itemName = $requirementItem ? $requirementItem->document_name : "Dokumen {$itemId}";

                        // Default
                        $fileExists = false;
                        $fileSize = 0;
                        $fileUrl = null;

                        // Cek file
                        if (Storage::disk('public')->exists($filePath)) {
                            $fileExists = true;
                            $fileSize = Storage::disk('public')->size($filePath);
                            $fileUrl = Storage::url($filePath);
                        }

                        // Masukin hanya kalau file valid
                        if ($fileExists && $fileUrl) {
                            $requirementDocuments[] = [
                                'item_id' => $itemId,
                                'item_name' => $itemName,
                                'file_path' => $filePath,
                                'file_url' => $fileUrl,
                                'file_size' => $fileSize,
                                'file_extension' => pathinfo($filePath, PATHINFO_EXTENSION) ?: 'unknown',
                                'file_exists' => true,
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::warning("Requirement file check failed for item {$itemId}: " . $e->getMessage());
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('getRequirementDocuments Error: ' . $e->getMessage(), [
                'apl_id' => $apl->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $requirementDocuments;
    }

    private function buildAssessmentData($certificationScheme, Apl02 $apl02): array
    {
        $assessmentData = [];
        $totalElements = 0;
        $kompetenCount = 0;
        $belumKompetenCount = 0;

        foreach ($certificationScheme->activeUnits as $unit) {
            $unitData = [
                'id' => $unit->id,
                'kode_unit' => $unit->kode_unit,
                'judul_unit' => $unit->judul_unit,
                'elements' => [],
            ];

            foreach ($unit->activeElemenKompetensis as $element) {
                $totalElements++;
                $assessment = $element->assessments->first();
                $result = $assessment ? $assessment->assessment_result : null;

                if ($result === 'kompeten') {
                    $kompetenCount++;
                } elseif ($result === 'belum_kompeten') {
                    $belumKompetenCount++;
                }

                $unitData['elements'][] = $this->buildElementData($element, $assessment, $unit, $apl02);
            }

            $assessmentData[] = $unitData;
        }

        return [
            'units' => $assessmentData,
            'totals' => [
                'total_elements' => $totalElements,
                'kompeten_count' => $kompetenCount,
                'belum_kompeten_count' => $belumKompetenCount,
            ],
        ];
    }

    private function buildElementData($element, $assessment, $unit, Apl02 $apl02): array
    {
        $result = $assessment ? $assessment->assessment_result : null;

        $elementDocuments = [];
        if ($assessment && $assessment->notes) {
            $decoded = json_decode($assessment->notes, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $elementDocuments = $decoded;
            }
        }

        $elementEvidences = $this->buildElementEvidences($elementDocuments, $unit, $apl02);

        return [
            'id' => $element->id,
            'kode_elemen' => $element->kode_elemen,
            'judul_elemen' => $element->judul_elemen,
            'assessment_result' => $result,
            'criteria' => $element->activeKriteriaKerjas
                ->map(function ($criteria) {
                    return [
                        'id' => $criteria->id,
                        'uraian_kriteria' => $criteria->uraian_kriteria,
                    ];
                })
                ->toArray(),
            'evidences' => $elementEvidences,
        ];
    }

    private function buildElementEvidences(array $elementDocuments, $unit, Apl02 $apl02): array
    {
        $elementEvidences = [];
        $allEvidenceFiles = $apl02
            ->evidenceSubmissions()
            ->with('portfolioFile')
            ->get()
            ->groupBy(function ($evidence) {
                return $evidence->portfolioFile ? $evidence->portfolioFile->document_name : null;
            })
            ->filter(function ($group, $documentName) {
                return $documentName !== null;
            })
            ->map(function ($evidenceGroup) {
                return $evidenceGroup->first();
            });

        foreach ($elementDocuments as $document) {
            $portfolioFile = $unit->portfolioFiles->firstWhere('id', $document['portfolioId']);
            $uploadedEvidence = $allEvidenceFiles->get($document['documentName']);

            if ($portfolioFile) {
                $elementEvidences[] = [
                    'portfolio_id' => $portfolioFile->id,
                    'document_name' => $portfolioFile->document_name,
                    'uploaded_evidence' => $uploadedEvidence
                        ? [
                            'id' => $uploadedEvidence->id,
                            'file_name' => $uploadedEvidence->file_name,
                            'file_type' => $uploadedEvidence->file_type,
                            'file_size' => $uploadedEvidence->file_size,
                            'file_size_formatted' => $this->formatFileSize($uploadedEvidence->file_size),
                            // 'download_url' => route('admin.apl02.download-evidence', [$apl02->id, $uploadedEvidence->id]),
                            // 'preview_url' => in_array(strtolower($uploadedEvidence->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif'])
                            //     ? route('admin.apl02.preview-evidence', [$apl02->id, $uploadedEvidence->id])
                            //     : null,
                        ]
                        : null,
                ];
            }
        }

        return $elementEvidences;
    }

    private function formatApl02Data(Apl02 $apl02, array $assessmentData): array
    {
        $totals = $assessmentData['totals'];
        $evidenceCount = $apl02->evidenceSubmissions->count();

        return [
            'id' => $apl02->id,
            'nomor_apl_02' => $apl02->nomor_apl_02,
            'status' => $apl02->status,
            'status_text' => $this->getStatusText($apl02->status),
            'submitted_at' => $apl02->submitted_at?->format('d M Y H:i'),
            'reviewed_at' => $apl02->reviewed_at?->format('d M Y H:i'),
            'catatan_reviewer' => $apl02->catatan_reviewer,
            'catatan_asesor' => $apl02->catatan_asesor,
            'kompeten_count' => $totals['kompeten_count'],
            'belum_kompeten_count' => $totals['belum_kompeten_count'],
            'evidence_count' => $evidenceCount,
            'total_elements' => $totals['total_elements'],
            'tanda_tangan_asesi' => $apl02->tanda_tangan_asesi,
            'tanggal_tanda_tangan_asesi' => $apl02->tanggal_tanda_tangan_asesi?->format('d M Y H:i'),
        ];
    }

    private function formatParticipantData(Apl02 $apl02): array
    {
        return [
            'nama_lengkap' => $apl02->apl01->nama_lengkap ?? 'N/A',
            'email' => $apl02->apl01->email ?? 'N/A',
        ];
    }

    private function formatCertificationSchemeData($certificationScheme): array
    {
        return [
            'nama' => $certificationScheme->nama,
            'code_1' => $certificationScheme->code_1,
        ];
    }

    private function processSingleApl01Action(Apl01Pendaftaran $apl, string $action, ?string $notes, int $userId): void
    {
        switch ($action) {
            case 'approve':
                $this->approveApl01($apl, $notes, $userId);
                break;
            case 'reject':
                $this->rejectApl01($apl, $notes, $userId);
                break;
            case 'reopen':
                $this->reopenApl01($apl, $notes, $userId);
                break;
        }
    }

    private function processSingleApl02Action(Apl02 $apl02, string $action, ?string $notes, int $userId): void
    {
        switch ($action) {
            case 'approve':
                $this->approveApl02($apl02, $notes, $userId);
                break;
            case 'reject':
                $this->rejectApl02($apl02, $notes, $userId);
                break;
            case 'reopen':
                $this->reopenApl02($apl02, $notes, $userId);
                break;
        }
    }

    private function getStatusText(string $status): string
    {
        $statusMap = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'returned' => 'Returned',
        ];
        return $statusMap[$status] ?? ucfirst($status);
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
