<?php

namespace App\Services;

use App\Models\Apl01Pendaftaran;
use App\Models\Apl02;
use Illuminate\Http\Request;

class AplDataService
{
    public function getStatistics(string $aplType): array
    {
        if ($aplType === 'apl01') {
            return $this->getApl01Statistics();
        }

        return $this->getApl02Statistics();
    }

    public function getDataTable(string $aplType, Request $request): array
    {
        if ($aplType === 'apl01') {
            return $this->getApl01DataTable($request);
        }

        return $this->getApl02DataTable($request);
    }

    private function getApl01Statistics(): array
    {
        return [
            'total' => Apl01Pendaftaran::count(),
            'draft' => Apl01Pendaftaran::where('status', 'draft')->count(),
            'submitted' => Apl01Pendaftaran::where('status', 'submitted')->count(),
            'approved' => Apl01Pendaftaran::where('status', 'approved')->count(),
            'rejected' => Apl01Pendaftaran::where('status', 'rejected')->count(),
            'open' => Apl01Pendaftaran::where('status', 'open')->count(),
        ];
    }

    private function getApl02Statistics(): array
    {
        return [
            'total' => Apl02::count(),
            'draft' => Apl02::where('status', 'draft')->count(),
            'submitted' => Apl02::where('status', 'submitted')->count(),
            'approved' => Apl02::where('status', 'approved')->count(),
            'rejected' => Apl02::where('status', 'rejected')->count(),
            'returned' => Apl02::where('status', 'returned')->count(),
        ];
    }

    private function getApl01DataTable(Request $request): array
    {
        $query = Apl01Pendaftaran::with([
            'certificationScheme:id,nama,code_1',
            'reviewer:id,name',
            'lembagaPelatihan:id,name'
        ]);

        $this->applyFilters($query, $request, 'apl01');

        $totalRecords = Apl01Pendaftaran::count();
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
                'lembaga_pelatihan_nama' => $apl->lembagaPelatihan->name ?? null,
            ];
        });

        return [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    }

    private function getApl02DataTable(Request $request): array
    {
        $query = Apl02::with([
            'apl01:id,nama_lengkap,email',
            'certificationScheme:id,nama,code_1',
            'elementAssessments',
            'evidenceSubmissions'
        ]);

        $this->applyFilters($query, $request, 'apl02');

        $totalRecords = Apl02::count();
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
