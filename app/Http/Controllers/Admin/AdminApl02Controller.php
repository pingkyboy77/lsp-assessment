<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apl02;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminApl02Controller extends Controller
{
    public function index(Request $request)
    {
        $query = Apl02::with([
            'user:id,name,nama_lengkap,email',
            'certificationScheme:id,nama,code_1,jenjang',
            'reviewer:id,name'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('scheme')) {
            $query->where('certification_scheme_id', $request->scheme);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('nomor_apl_02', 'like', "%{$search}%");
        }

        $apl02s = $query->orderBy('submitted_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get filter options
        $schemes = \App\Models\CertificationScheme::active()
            ->select('id', 'nama', 'code_1')
            ->orderBy('nama')
            ->get();

        $stats = $this->getStats();

        return view('admin.apl02.index', compact('apl02s', 'schemes', 'stats'));
    }

    public function show(Apl02 $apl02)
    {
        $this->authorize('view', $apl02);

        $apl02->load([
            'user',
            'apl01',
            'certificationScheme',
            'reviewer',
            'asesor',
            'elementAssessments.elemenKompetensi.kriteriaKerjas',
            'elementAssessments.unitKompetensi',
            'evidenceSubmissions.portfolioFile'
        ]);

        // Group assessments by unit
        $assessmentsByUnit = $apl02->elementAssessments
            ->groupBy('unit_kompetensi_id')
            ->map(function ($assessments, $unitId) {
                $unit = $assessments->first()->unitKompetensi;
                return [
                    'unit' => $unit,
                    'assessments' => $assessments,
                    'stats' => [
                        'total' => $assessments->count(),
                        'kompeten' => $assessments->where('assessment_result', 'kompeten')->count(),
                        'belum_kompeten' => $assessments->where('assessment_result', 'belum_kompeten')->count(),
                    ]
                ];
            });

        // Evidence submissions grouped by portfolio file
        $evidenceByPortfolio = $apl02->evidenceSubmissions
            ->groupBy('portfolio_file_id')
            ->map(function ($submissions, $portfolioId) {
                return [
                    'portfolio_file' => $submissions->first()->portfolioFile,
                    'submission' => $submissions->first()
                ];
            });

        return view('admin.apl02.show', compact('apl02', 'assessmentsByUnit', 'evidenceByPortfolio'));
    }

    public function review(Apl02 $apl02)
    {
        $this->authorize('review', $apl02);

        if (!in_array($apl02->status, ['submitted', 'review'])) {
            return redirect()->route('admin.apl02.show', $apl02)
                ->with('error', 'APL 02 tidak dalam status yang dapat direview.');
        }

        // Set status to review if currently submitted
        if ($apl02->status === 'submitted') {
            $apl02->setReview(auth()->id());
        }

        return $this->show($apl02);
    }

    public function setReview(Apl02 $apl02)
    {
        $this->authorize('review', $apl02);

        if ($apl02->status !== 'submitted') {
            return response()->json(['error' => 'APL 02 tidak dapat diset ke status review'], 422);
        }

        try {
            $apl02->setReview(auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'APL 02 berhasil diset ke status review',
                'status' => $apl02->status_text
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }

    public function approve(Request $request, Apl02 $apl02)
    {
        $this->authorize('approve', $apl02);

        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        if (!$apl02->is_signed_by_asesor) {
            return response()->json(['error' => 'APL 02 harus ditandatangani asesor sebelum disetujui'], 422);
        }

        try {
            $apl02->approve(auth()->id(), $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'APL 02 berhasil disetujui',
                'status' => $apl02->status_text
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyetujui APL 02: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, Apl02 $apl02)
    {
        $this->authorize('reject', $apl02);

        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        try {
            $apl02->reject(auth()->id(), $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'APL 02 berhasil ditolak',
                'status' => $apl02->status_text
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menolak APL 02: ' . $e->getMessage()], 500);
        }
    }

    public function returnToAsesi(Request $request, Apl02 $apl02)
    {
        $this->authorize('returnToAsesi', $apl02);

        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        try {
            $apl02->returnToAsesi(auth()->id(), $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'APL 02 berhasil dikembalikan ke asesi',
                'status' => $apl02->status_text
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengembalikan APL 02: ' . $e->getMessage()], 500);
        }
    }

    public function signAsesor(Request $request, Apl02 $apl02)
    {
        $this->authorize('signAsesor', $apl02);

        $request->validate([
            'signature' => 'required|string'
        ]);

        try {
            $apl02->signByAsesor($request->signature, auth()->id(), $request->ip());

            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan asesor berhasil disimpan',
                'signed_at' => $apl02->asesor_signed_at->format('d/m/Y H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()], 500);
        }
    }

    public function downloadEvidence(Apl02 $apl02, $evidenceId)
    {
        $this->authorize('downloadEvidence', $apl02);

        $evidence = $apl02->evidenceSubmissions()->findOrFail($evidenceId);

        if (!\Storage::disk('public')->exists($evidence->file_path)) {
            return abort(404, 'File tidak ditemukan');
        }

        return \Storage::disk('public')->download(
            $evidence->file_path,
            $evidence->file_name
        );
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:set_review,bulk_approve,bulk_reject',
            'apl02_ids' => 'required|array|min:1',
            'apl02_ids.*' => 'exists:apl_02,id',
            'notes' => 'required_if:action,bulk_reject|nullable|string|max:1000'
        ]);

        $apl02s = Apl02::whereIn('id', $request->apl02_ids)->get();
        $processed = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($apl02s as $apl02) {
                try {
                    switch ($request->action) {
                        case 'set_review':
                            if ($this->authorize('review', $apl02) && $apl02->status === 'submitted') {
                                $apl02->setReview(auth()->id());
                                $processed++;
                            }
                            break;

                        case 'bulk_approve':
                            if ($this->authorize('approve', $apl02) && $apl02->is_signed_by_asesor) {
                                $apl02->approve(auth()->id(), $request->notes);
                                $processed++;
                            }
                            break;

                        case 'bulk_reject':
                            if ($this->authorize('reject', $apl02)) {
                                $apl02->reject(auth()->id(), $request->notes);
                                $processed++;
                            }
                            break;
                    }
                } catch (\Exception $e) {
                    $errors[] = "APL 02 #{$apl02->id}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil memproses {$processed} APL 02";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'processed' => $processed,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Gagal melakukan bulk action: ' . $e->getMessage()], 500);
        }
    }

    public function reportSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $stats = [
            'total' => Apl02::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'by_status' => Apl02::whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->pluck('count', 'status'),
            'by_scheme' => Apl02::whereBetween('created_at', [$dateFrom, $dateTo])
                ->join('certification_schemes', 'apl_02.certification_scheme_id', '=', 'certification_schemes.id')
                ->groupBy('certification_schemes.nama')
                ->selectRaw('certification_schemes.nama, count(*) as count')
                ->pluck('count', 'nama'),
            'competency_stats' => Apl02::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('
                    AVG(competency_percentage) as avg_competency,
                    MIN(competency_percentage) as min_competency,
                    MAX(competency_percentage) as max_competency,
                    COUNT(CASE WHEN competency_percentage >= 80 THEN 1 END) as high_competency,
                    COUNT(CASE WHEN competency_percentage BETWEEN 65 AND 79 THEN 1 END) as medium_competency,
                    COUNT(CASE WHEN competency_percentage < 65 THEN 1 END) as low_competency
                ')
                ->first()
        ];

        return response()->json($stats);
    }

    public function exportReport(Request $request)
    {
        // Implementation for exporting comprehensive APL 02 reports
        // This would generate Excel/PDF reports with detailed statistics
        return response()->json(['message' => 'Export feature in development']);
    }

    private function getStats()
    {
        return [
            'total' => Apl02::count(),
            'draft' => Apl02::where('status', 'draft')->count(),
            'submitted' => Apl02::where('status', 'submitted')->count(),
            'review' => Apl02::where('status', 'review')->count(),
            'approved' => Apl02::where('status', 'approved')->count(),
            'rejected' => Apl02::where('status', 'rejected')->count(),
            'returned' => Apl02::where('status', 'returned')->count(),
            'pending_review' => Apl02::whereIn('status', ['submitted', 'review'])->count()
        ];
    }
}
