<?php

namespace App\Services;

use App\Models\Apl01Pendaftaran;
use App\Models\Apl02;
use Illuminate\Http\Request;

class LembagaAplDataService
{
    public function getStatistics(string $aplType, string $lembagaId): array
    {
        if ($aplType === 'apl01') {
            return $this->getApl01Statistics($lembagaId);
        }

        return $this->getApl02Statistics($lembagaId);
    }

    public function getDataTable(string $aplType, string $lembagaId, Request $request): array
    {
        if ($aplType === 'apl01') {
            return $this->getApl01DataTable($lembagaId, $request);
        }

        return $this->getApl02DataTable($lembagaId, $request);
    }

    private function getApl01Statistics(string $lembagaId): array
    {
        $baseQuery = Apl01Pendaftaran::whereHas('user', function ($q) use ($lembagaId) {
            $q->where('company', $lembagaId);
        });

        return [
            'total' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'open' => (clone $baseQuery)->where('status', 'open')->count(),
        ];
    }

    private function getApl02Statistics(string $lembagaId): array
    {
        // ✅ Langsung cek training_provider di apl01
        $baseQuery = Apl02::whereHas('apl01', function ($q) use ($lembagaId) {
            $q->where('training_provider', $lembagaId);
        });

        return [
            'total' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'returned' => (clone $baseQuery)->where('status', 'returned')->count(),
        ];
    }

    private function getApl01DataTable(string $lembagaId, Request $request): array
    {
        $query = Apl01Pendaftaran::with([
            'certificationScheme:id,nama,code_1',
            'reviewer:id,name',
            'user:id,name,company'
        ])
            ->whereHas('user', function ($q) use ($lembagaId) {
                $q->where('training_provider', $lembagaId);
            });

        $this->applyFilters($query, $request, 'apl01');

        $totalRecords = Apl01Pendaftaran::whereHas('user', function ($q) use ($lembagaId) {
            $q->where('training_provider', $lembagaId);
        })->count();

        $filteredRecords = (clone $query)->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        $query->orderBy('submitted_at', 'desc')->skip($start)->take($length);

        $data = $query->get()->map(function ($apl) {
            return [
                'id' => $apl->id,
                'nomor_apl_01' => $apl->nomor_apl_01 ?: 'DRAFT',
                'nama_lengkap' => $apl->nama_lengkap,
                'email' => $apl->email,
                'status' => $apl->status,
                'submitted_at' => $apl->submitted_at?->toISOString(),
                'reviewed_at' => $apl->reviewed_at?->toISOString(),
                'certification_scheme_nama' => $apl->certificationScheme->nama ?? null,
                'code_1' => $apl->certificationScheme->code_1 ?? null,
                'reviewer_name' => $apl->reviewer->name ?? null,
            ];
        });

        return [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    }

    private function getApl02DataTable(string $lembagaId, Request $request): array
    {
        // ✅ Langsung cek training_provider di apl01
        $query = Apl02::with([
            'apl01:id,nama_lengkap,email,training_provider',
            'certificationScheme:id,nama,code_1',
            'elementAssessments',
            'evidenceSubmissions'
        ])
            ->whereHas('apl01', function ($q) use ($lembagaId) {
                $q->where('training_provider', $lembagaId);
            });

        $this->applyFilters($query, $request, 'apl02');

        $totalRecords = Apl02::whereHas('apl01', function ($q) use ($lembagaId) {
            $q->where('training_provider', $lembagaId);
        })->count();

        $filteredRecords = (clone $query)->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        $query->orderBy('submitted_at', 'desc')->skip($start)->take($length);

        $data = $query->get()->map(function ($apl02) {
            return [
                'id' => $apl02->id,
                'nomor_apl_02' => $apl02->nomor_apl_02 ?: 'DRAFT',
                'nama_lengkap' => $apl02->apl01->nama_lengkap ?? 'Unknown',
                'email' => $apl02->apl01->email ?? 'Unknown',
                'status' => $apl02->status,
                'submitted_at' => $apl02->submitted_at?->toISOString(),
                'certification_scheme' => $apl02->certificationScheme->nama ?? null,
                'kompeten_count' => $apl02->elementAssessments->where('assessment_result', 'kompeten')->count(),
                'belum_kompeten_count' => $apl02->elementAssessments->where('assessment_result', 'belum_kompeten')->count(),
                'evidence_count' => $apl02->evidenceSubmissions->count(),
            ];
        });

        return [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    }

    private function applyFilters($query, Request $request, string $aplType): void
    {
        // Date filters
        $dateFrom = $request->date_from ?: date('Y-m-01');
        $dateTo = $request->date_to ?: date('Y-m-d');

        if ($dateFrom && $dateTo) {
            $query->whereBetween('submitted_at', [
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59'
            ]);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');

            if ($aplType === 'apl01') {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'ILIKE', "%{$search}%")
                        ->orWhere('nomor_apl_01', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            } else {
                $query->whereHas('apl01', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                })->orWhere('nomor_apl_02', 'ILIKE', "%{$search}%");
            }
        }
    }
}
