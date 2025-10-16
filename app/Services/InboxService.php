<?php

// app/Services/InboxService.php

namespace App\Services;

use App\Models\Apl01Pendaftaran;
use App\Models\Apl02;
use App\Models\TukRequest; // TAMBAHKAN INI
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InboxService
{
    /**
     * Get unified APL data with filters
     */
    public function getUnifiedAplData($filters = [])
    {
        $userId = Auth::id();
        $data = [];

        // Get APL01 data
        if (!isset($filters['apl_type']) || $filters['apl_type'] === 'all' || $filters['apl_type'] === 'apl01') {
            $apl01s = $this->getApl01Data($userId, $filters);
            foreach ($apl01s as $apl01) {
                $data[] = $this->formatApl01($apl01);
            }
        }

        // Get APL02 data
        if (!isset($filters['apl_type']) || $filters['apl_type'] === 'all' || $filters['apl_type'] === 'apl02') {
            $apl02s = $this->getApl02Data($userId, $filters);
            foreach ($apl02s as $apl02) {
                $data[] = $this->formatApl02($apl02);
            }
        }

        // NEW: Get TUK Request data
        if (!isset($filters['apl_type']) || $filters['apl_type'] === 'all' || $filters['apl_type'] === 'tuk') {
            $tukRequests = $this->getTukRequestData($userId, $filters);
            foreach ($tukRequests as $tukRequest) {
                $data[] = $this->formatTukRequest($tukRequest);
            }
        }

        // Sort by created_at desc
        usort($data, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $data;
    }

    /**
     * Get APL01 data with filters
     */
    private function getApl01Data($userId, $filters)
    {
        $query = Apl01Pendaftaran::where('user_id', $userId)->with(['certificationScheme', 'lembagaPelatihan']);

        $this->applyFilters($query, $filters, 'apl01');

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get APL02 data with filters
     */
    private function getApl02Data($userId, $filters)
    {
        $query = Apl02::whereHas('apl01', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            // Make sure to load the apl01 relationship
            ->with(['apl01', 'apl01.certificationScheme', 'elementAssessments', 'evidenceSubmissions']);

        $this->applyFilters($query, $filters, 'apl02');

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * NEW: Get TUK Request data with filters
     */
    private function getTukRequestData($userId, $filters)
    {
        $query = TukRequest::where('user_id', $userId)
            ->with([
                'apl01:id,nama_lengkap,certification_scheme_id',
                'apl01.certificationScheme:id,nama'
            ]);

        $this->applyFilters($query, $filters, 'tuk');

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, $filters, $type)
    {
        // Status filter - KHUSUS UNTUK TUK
        if (!empty($filters['status'])) {
            if ($type === 'tuk') {
                // TUK punya logic status yang berbeda
                if ($filters['status'] === 'submitted') {
                    $query->whereNull('recommended_by');
                } elseif ($filters['status'] === 'approved') {
                    $query->whereNotNull('recommended_by');
                }
            } else {
                $query->where('status', $filters['status']);
            }
        }

        // Date range filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('created_at', [$filters['date_from'] . ' 00:00:00', $filters['date_to'] . ' 23:59:59']);
        }

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            if ($type === 'apl01') {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'ILIKE', "%{$search}%")
                        ->orWhere('nomor_apl_01', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            } elseif ($type === 'apl02') {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_apl_02', 'ILIKE', "%{$search}%")->orWhereHas('apl01', function ($q2) use ($search) {
                        $q2->where('nama_lengkap', 'ILIKE', "%{$search}%");
                    });
                });
            } elseif ($type === 'tuk') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('apl01', function ($q2) use ($search) {
                        $q2->where('nama_lengkap', 'ILIKE', "%{$search}%");
                    })
                        ->orWhereHas('apl01.certificationScheme', function ($q2) use ($search) {
                            $q2->where('nama', 'ILIKE', "%{$search}%");
                        });
                });
            }
        }

        // Scheme filter
        if (!empty($filters['scheme_id'])) {
            if ($type === 'apl01') {
                $query->where('certification_scheme_id', $filters['scheme_id']);
            } elseif ($type === 'apl02') {
                $query->whereHas('apl01', function ($q) use ($filters) {
                    $q->where('certification_scheme_id', $filters['scheme_id']);
                });
            } elseif ($type === 'tuk') {
                $query->whereHas('apl01', function ($q) use ($filters) {
                    $q->where('certification_scheme_id', $filters['scheme_id']);
                });
            }
        }
    }

    /**
     * Format APL01 data
     */
    private function formatApl01($apl01)
    {
        return [
            'id' => $apl01->id,
            'apl_type' => 'apl01',
            'nomor_apl' => $apl01->nomor_apl_01 ?: 'DRAFT',
            'status' => $apl01->status,
            'certification_scheme' => $apl01->certificationScheme->nama ?? 'N/A',
            'created_at' => $apl01->created_at->toISOString(),
            'created_at_formatted' => $apl01->created_at->format('d M Y H:i'),
            'submitted_at' => $apl01->submitted_at?->format('d M Y H:i'),
            'progress_percentage' => $this->calculateApl01Progress($apl01),
            'timeline' => $this->getApl01Timeline($apl01),
            'rejection_note' => $apl01->catatan_reviewer,
            'can_create_apl02' => $apl01->status === 'approved' && strtolower($apl01->tuk) !== 'mandiri',
            'has_apl02' => Apl02::where('apl_01_id', $apl01->id)->exists(),
            'tuk_type' => $apl01->tuk, // Added
            'is_tuk_mandiri' => strtolower($apl01->tuk ?? '') === 'mandiri', // Added
        ];
    }

    /**
     * Format APL02 data
     */
    private function formatApl02($apl02)
    {
        $kompeten = $apl02->elementAssessments->where('assessment_result', 'kompeten')->count();
        $belumKompeten = $apl02->elementAssessments->where('assessment_result', 'belum_kompeten')->count();

        // Get TUK type from APL01
        $tukType = '';
        $isTukMandiri = false;
        $isTukSewaktu = false;

        if ($apl02->apl01) {
            $tukType = strtolower($apl02->apl01->tuk ?? '');
            $isTukMandiri = $tukType === 'mandiri';
            $isTukSewaktu = $tukType === 'sewaktu';
        }

        // Check if TUK request exists - ONLY for TUK Sewaktu
        $hasTukRequest = false;
        if ($isTukSewaktu && $apl02->apl_01_id) {
            // Fix: kolom nya apl01_id bukan apl_01_id
            $hasTukRequest = TukRequest::where('apl01_id', $apl02->apl_01_id)->exists();
        }

        return [
            'id' => $apl02->id,
            'apl_type' => 'apl02',
            'nomor_apl' => $apl02->nomor_apl_02 ?: 'DRAFT',
            'status' => $apl02->status,
            'certification_scheme' => $apl02->apl01->certificationScheme->nama ?? 'N/A',
            'created_at' => $apl02->created_at->toISOString(),
            'created_at_formatted' => $apl02->created_at->format('d M Y H:i'),
            'submitted_at' => $apl02->submitted_at?->format('d M Y H:i'),
            'progress_percentage' => $this->calculateApl02Progress($apl02),
            'assessment_summary' => $this->generateAssessmentSummary($kompeten, $belumKompeten),
            'timeline' => $this->getApl02Timeline($apl02),
            'rejection_note' => $apl02->catatan_reviewer,
            'apl01_id' => $apl02->apl01 ? $apl02->apl01->id : null,
            'has_tuk_request' => $hasTukRequest,
            'tuk_type' => $tukType,
            'is_tuk_mandiri' => $isTukMandiri,
            'is_tuk_sewaktu' => $isTukSewaktu,
        ];
    }

    /**
     * NEW: Format TUK Request data
     */
    private function formatTukRequest($tukRequest)
    {
        // Tentukan status berdasarkan recommended_by
        $status = $tukRequest->recommended_by ? 'approved' : 'submitted';

        return [
            'id' => $tukRequest->id,
            'apl_type' => 'tuk',
            'nomor_apl' => 'TUK-' . str_pad($tukRequest->id, 4, '0', STR_PAD_LEFT),
            'status' => $status,
            'certification_scheme' => $tukRequest->apl01->certificationScheme->nama ?? 'N/A',
            'created_at' => $tukRequest->created_at->toISOString(),
            'created_at_formatted' => $tukRequest->created_at->format('d M Y H:i'),
            'submitted_at' => $tukRequest->created_at->format('d M Y H:i'), // TUK langsung submitted
            'progress_percentage' => null, // TUK tidak punya progress
            'assessment_summary' => null,
            'timeline' => $this->getTukRequestTimeline($tukRequest),
            'rejection_note' => null, // TUK tidak bisa ditolak, hanya disetujui atau tidak
            'apl01_id' => $tukRequest->apl01_id,
            'has_tuk_request' => true,
            'tuk_type' => 'sewaktu',
            'is_tuk_mandiri' => false,
            'is_tuk_sewaktu' => true,
            'tuk_recommended_by' => $tukRequest->recommended_by,
            'tuk_request_id' => $tukRequest->id,
            'nama_lengkap' => $tukRequest->apl01->nama_lengkap ?? 'N/A',
        ];
    }

    /**
     * NEW: Get TUK Request timeline
     */
    private function getTukRequestTimeline($tukRequest)
    {
        $timeline = [];

        $timeline[] = [
            'icon' => 'check-circle',
            'color' => 'success',
            'text' => 'Permohonan TUK dibuat',
            'date' => $tukRequest->created_at->format('d M Y, H:i'),
        ];

        if ($tukRequest->recommended_by) {
            $timeline[] = [
                'icon' => 'check-circle',
                'color' => 'success',
                'text' => 'TUK disetujui',
                'date' => $tukRequest->updated_at->format('d M Y, H:i'),
            ];
        } else {
            $timeline[] = [
                'icon' => 'clock',
                'color' => 'warning',
                'text' => 'Menunggu persetujuan admin',
                'date' => 'Pending',
            ];
        }

        return $timeline;
    }

    /**
     * Get APL01 timeline
     */
    private function getApl01Timeline($apl01)
    {
        $timeline = [];

        $timeline[] = [
            'icon' => 'check-circle',
            'color' => 'success',
            'text' => 'Form dibuat',
            'date' => $apl01->created_at->format('d M Y, H:i'),
        ];

        if ($apl01->submitted_at) {
            $timeline[] = [
                'icon' => 'send',
                'color' => 'info',
                'text' => 'Form disubmit',
                'date' => $apl01->submitted_at->format('d M Y, H:i'),
            ];
        }

        if ($apl01->reviewed_at) {
            $icon = $apl01->status === 'approved' ? 'check-circle' : 'x-circle';
            $color = $apl01->status === 'approved' ? 'success' : 'danger';
            $text = $apl01->status === 'approved' ? 'Disetujui' : 'Ditolak';

            $timeline[] = [
                'icon' => $icon,
                'color' => $color,
                'text' => $text,
                'date' => $apl01->reviewed_at->format('d M Y, H:i'),
            ];
        } elseif ($apl01->status === 'submitted') {
            $timeline[] = [
                'icon' => 'clock',
                'color' => 'warning',
                'text' => 'Menunggu review admin',
                'date' => 'Pending',
            ];
        }

        return $timeline;
    }

    /**
     * Get APL02 timeline
     */
    private function getApl02Timeline($apl02)
    {
        $timeline = [];

        $timeline[] = [
            'icon' => 'check-circle',
            'color' => 'success',
            'text' => 'Assessment dimulai',
            'date' => $apl02->created_at->format('d M Y, H:i'),
        ];

        if ($apl02->submitted_at) {
            $timeline[] = [
                'icon' => 'send',
                'color' => 'info',
                'text' => 'Assessment disubmit',
                'date' => $apl02->submitted_at->format('d M Y, H:i'),
            ];
        }

        if ($apl02->reviewed_at) {
            $icon = $apl02->status === 'approved' ? 'check-circle' : 'x-circle';
            $color = $apl02->status === 'approved' ? 'success' : 'danger';
            $text = $apl02->status === 'approved' ? 'Assessment lulus' : 'Assessment tidak lulus';

            $timeline[] = [
                'icon' => $icon,
                'color' => $color,
                'text' => $text,
                'date' => $apl02->reviewed_at->format('d M Y, H:i'),
            ];
        }

        return $timeline;
    }

    /**
     * Calculate APL01 progress
     */
    private function calculateApl01Progress($apl01)
    {
        switch ($apl01->status) {
            case 'approved':
                return 100;
            case 'rejected':
                return 0;
            case 'submitted':
                return 75;
            case 'open':
                return 50;
            default:
                return 25; // draft
        }
    }

    /**
     * Calculate APL02 progress
     */
    private function calculateApl02Progress($apl02)
    {
        if ($apl02->status === 'approved') {
            return 100;
        }
        if ($apl02->status === 'rejected') {
            return 0;
        }

        $totalAssessments = $apl02->elementAssessments->count();
        if ($totalAssessments === 0) {
            return 25;
        }

        $completedAssessments = $apl02->elementAssessments->whereNotNull('assessment_result')->count();
        $progressPercentage = ($completedAssessments / $totalAssessments) * 100;

        return min(90, max(25, $progressPercentage));
    }

    /**
     * Generate assessment summary
     */
    private function generateAssessmentSummary($kompeten, $belumKompeten)
    {
        if ($kompeten === 0 && $belumKompeten === 0) {
            return '';
        }

        return '<div class="d-flex gap-2 mb-1">' . '<small class="badge bg-success">Kompeten: ' . $kompeten . '</small>' . '<small class="badge bg-danger">Belum Kompeten: ' . $belumKompeten . '</small>' . '</div>';
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        $userId = Auth::id();

        $apl01Stats = [
            'total' => Apl01Pendaftaran::where('user_id', $userId)->count(),
            'draft' => Apl01Pendaftaran::where('user_id', $userId)->where('status', 'draft')->count(),
            'submitted' => Apl01Pendaftaran::where('user_id', $userId)->where('status', 'submitted')->count(),
            'approved' => Apl01Pendaftaran::where('user_id', $userId)->where('status', 'approved')->count(),
            'rejected' => Apl01Pendaftaran::where('user_id', $userId)->where('status', 'rejected')->count(),
            'open' => Apl01Pendaftaran::where('user_id', $userId)->where('status', 'open')->count(),
        ];

        $apl02Stats = [
            'total' => Apl02::whereHas('apl01', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count(),
            'draft' => Apl02::whereHas('apl01', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->where('status', 'draft')
                ->count(),
            'submitted' => Apl02::whereHas('apl01', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->where('status', 'submitted')
                ->count(),
            'approved' => Apl02::whereHas('apl01', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->where('status', 'approved')
                ->count(),
            'rejected' => Apl02::whereHas('apl01', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->where('status', 'rejected')
                ->count(),
            'returned' => Apl02::whereHas('apl01', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->where('status', 'returned')
                ->count(),
        ];

        // NEW: TUK Request stats
        $tukStats = [
            'total' => TukRequest::where('user_id', $userId)->count(),
            'submitted' => TukRequest::where('user_id', $userId)->whereNull('recommended_by')->count(),
            'approved' => TukRequest::where('user_id', $userId)->whereNotNull('recommended_by')->count(),
        ];

        return [
            'total' => $apl01Stats['total'] + $apl02Stats['total'] + $tukStats['total'],
            'pending' => $apl01Stats['submitted'] + $apl02Stats['submitted'] + $tukStats['submitted'],
            'approved' => $apl01Stats['approved'] + $apl02Stats['approved'] + $tukStats['approved'],
            'rejected' => $apl01Stats['rejected'] + $apl02Stats['rejected'],
            'revision' => $apl01Stats['open'] + $apl02Stats['returned'],
            'draft' => $apl01Stats['draft'] + $apl02Stats['draft'],
        ];
    }
}
