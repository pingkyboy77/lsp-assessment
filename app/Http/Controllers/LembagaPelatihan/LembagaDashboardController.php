<?php

namespace App\Http\Controllers\LembagaPelatihan;

use App\Http\Controllers\Controller;
use App\Models\Apl01Pendaftaran;
use App\Models\Apl02;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LembagaDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $lembagaId = Auth::user()->company;
            $year = $request->input('year', 'all');

            // Get statistics
            $statistics = $this->getStatistics($lembagaId, $year);

            // Get chart data for APL 01 ONLY (FIXED)
            $chartData = $this->prepareChartData($lembagaId, $year);

            // Get recent activities
            $recentActivities = $this->getRecentActivities($lembagaId);

            return view('dashboard.lembagaPelatihan', [
                'totalPeserta' => $statistics['totalPeserta'],
                'totalApl01' => $statistics['totalApl01'],
                'totalApl02' => $statistics['totalApl02'],
                'totalApproved' => $statistics['totalApproved'],
                'chartLabels' => $chartData['labels'],
                'chartValues' => $chartData['values'],
                'recentActivities' => $recentActivities,
                'year' => $year,
            ]);
        } catch (\Exception $e) {
            \Log::error('Lembaga Dashboard Error: ' . $e->getMessage(), [
                'lembaga_id' => Auth::user()->company ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return view('dashboard.lembagaPelatihan', [
                'error' => 'Gagal memuat data dashboard: ' . $e->getMessage(),
                'totalPeserta' => 0,
                'totalApl01' => 0,
                'totalApl02' => 0,
                'totalApproved' => 0,
                'chartLabels' => [],
                'chartValues' => [],
                'recentActivities' => collect(),
                'year' => 'all',
            ]);
        }
    }

    /**
     * Get statistics for lembaga
     */
    private function getStatistics(string $lembagaId, string $year): array
    {
        // Total Peserta - berdasarkan user yang punya APL01 di lembaga ini
        $pesertaQuery = User::where('company', $lembagaId)
            ->whereHas('apl01Registrations', function ($q) use ($lembagaId) {
                $q->where('training_provider', $lembagaId);
            });

        if ($year !== 'all') {
            $pesertaQuery->whereHas('apl01Registrations', function ($q) use ($year) {
                $q->whereYear('created_at', $year);
            });
        }

        $totalPeserta = $pesertaQuery->distinct()->count();

        // APL 01 Statistics
        $apl01Query = Apl01Pendaftaran::where('training_provider', $lembagaId)
            ->whereNotNull('submitted_at');

        if ($year !== 'all') {
            $apl01Query->whereYear('submitted_at', $year);
        }

        $totalApl01 = $apl01Query->count();
        $approvedApl01 = (clone $apl01Query)->where('status', 'approved')->count();

        // APL 02 Statistics
        $apl02Query = Apl02::whereHas('apl01', function ($q) use ($lembagaId) {
            $q->where('training_provider', $lembagaId);
        })->whereNotNull('submitted_at');

        if ($year !== 'all') {
            $apl02Query->whereYear('submitted_at', $year);
        }

        $totalApl02 = $apl02Query->count();
        $approvedApl02 = (clone $apl02Query)->where('status', 'approved')->count();

        // Total Approved (APL01 + APL02)
        $totalApproved = $approvedApl01 + $approvedApl02;

        \Log::info('Lembaga Statistics', [
            'lembaga_id' => $lembagaId,
            'year' => $year,
            'totalPeserta' => $totalPeserta,
            'totalApl01' => $totalApl01,
            'approvedApl01' => $approvedApl01,
            'totalApl02' => $totalApl02,
            'approvedApl02' => $approvedApl02,
            'totalApproved' => $totalApproved,
        ]);

        return [
            'totalPeserta' => $totalPeserta,
            'totalApl01' => $totalApl01,
            'totalApl02' => $totalApl02,
            'totalApproved' => $totalApproved,
        ];
    }

    /**
     * PRIVATE: Prepare chart data for APL 01 ONLY per month (NO APL 02)
     */
    private function prepareChartData(string $lembagaId, string $year): array
    {
        $targetYear = $year === 'all' ? date('Y') : $year;

        // Detect database driver
        $driver = DB::connection()->getDriverName();

        // HANYA APL01 - TIDAK ADA APL02
        if ($driver === 'pgsql') {
            // PostgreSQL
            $apl01Data = Apl01Pendaftaran::where('training_provider', $lembagaId)
                ->whereNotNull('submitted_at')
                ->whereYear('submitted_at', $targetYear)
                ->select(DB::raw('EXTRACT(MONTH FROM submitted_at) as month'), DB::raw('count(*) as total'))
                ->groupBy(DB::raw('EXTRACT(MONTH FROM submitted_at)'))
                ->pluck('total', 'month')
                ->toArray();
        } else {
            // MySQL
            $apl01Data = Apl01Pendaftaran::where('training_provider', $lembagaId)
                ->whereNotNull('submitted_at')
                ->whereYear('submitted_at', $targetYear)
                ->select(DB::raw('MONTH(submitted_at) as month'), DB::raw('count(*) as total'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
        }

        // Prepare labels and values for all 12 months
        $labels = [];
        $values = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $months[$i - 1];
            // HANYA APL 01, TIDAK ADA APL 02
            $apl01Count = $apl01Data[$i] ?? 0;
            $values[] = $apl01Count;
        }

        \Log::info('Lembaga Chart Data (APL 01 ONLY)', [
            'lembaga_id' => $lembagaId,
            'year' => $targetYear,
            'data' => $apl01Data,
            'values' => $values
        ]);

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(string $lembagaId)
    {
        // APL01 activities
        $apl01Activities = Apl01Pendaftaran::where('training_provider', $lembagaId)
            ->whereNotNull('submitted_at')
            ->with('user:id,name')
            ->select('id', 'nomor_apl_01 as nomor', 'nama_lengkap', 'status', 'submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nomor' => $item->nomor,
                    'nama_lengkap' => $item->nama_lengkap,
                    'status' => $item->status,
                    'submitted_at' => $item->submitted_at,
                    'type' => 'APL 01'
                ];
            });

        // APL02 activities
        $apl02Activities = Apl02::whereHas('apl01', function ($q) use ($lembagaId) {
            $q->where('training_provider', $lembagaId);
        })
            ->whereNotNull('submitted_at')
            ->with('apl01:id,nama_lengkap')
            ->select('id', 'nomor_apl_02 as nomor', 'status', 'submitted_at', 'apl_01_id')
            ->orderBy('submitted_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nomor' => $item->nomor,
                    'nama_lengkap' => $item->apl01->nama_lengkap ?? 'Unknown',
                    'status' => $item->status,
                    'submitted_at' => $item->submitted_at,
                    'type' => 'APL 02'
                ];
            });

        // Merge and sort
        $activities = $apl01Activities->concat($apl02Activities)
            ->sortByDesc('submitted_at')
            ->take(10)
            ->values();

        return $activities;
    }

    /**
     * AJAX: Get statistics data
     */
    public function getStatisticsData(Request $request)
    {
        try {
            $lembagaId = Auth::user()->company;
            $year = $request->input('year', 'all');

            $statistics = $this->getStatistics($lembagaId, $year);

            return response()->json([
                'success' => true,
                'totalPeserta' => $statistics['totalPeserta'],
                'totalApl01' => $statistics['totalApl01'],
                'totalApl02' => $statistics['totalApl02'],
                'totalApproved' => $statistics['totalApproved'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Lembaga Statistics AJAX Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX: Get chart data (APL 01 ONLY)
     */
    public function getChartData(Request $request)
    {
        try {
            $lembagaId = Auth::user()->company;
            $year = $request->input('year', 'all');

            // Call private method prepareChartData
            $chartData = $this->prepareChartData($lembagaId, $year);

            return response()->json([
                'success' => true,
                'labels' => $chartData['labels'],
                'values' => $chartData['values'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Lembaga Chart Data AJAX Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
