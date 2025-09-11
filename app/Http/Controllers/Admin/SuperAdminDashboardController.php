<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PesertaSertifikat;
use App\Models\CertificationScheme;
use App\Models\LembagaPelatihan;
use App\Models\ActivityLog;

class SuperAdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get filter parameters
            $year = $request->get('year', 'all');
            $month = $request->get('month', 'all');

            // Build query for filtered data
            $query = PesertaSertifikat::query();

            if ($year !== 'all') {
                $query->whereYear('created_at', $year);
            }

            if ($month !== 'all') {
                $query->whereMonth('created_at', $month);
            }

            // Get statistics based on filter
            $totalAsesi = (clone $query)->count();
            $totalSertifikat = (clone $query)->whereNotNull('tanggal_terbit')->count();

            // Total Lembaga Pelatihan dan Skema tidak difilter berdasarkan tanggal
            $totalLembagaPelatihan = LembagaPelatihan::count();
            $totalSkema = CertificationScheme::count();

            // Data untuk chart dengan filter
            $chartQuery = DB::table('peserta_sertifikats as ps')->join('certification_schemes as cs', 'ps.sertifikasi', '=', 'cs.nama')->select('cs.code_1', DB::raw('COUNT(ps.id) as total_peserta'));

            if ($year !== 'all') {
                $chartQuery->whereYear('ps.created_at', $year);
            }

            if ($month !== 'all') {
                $chartQuery->whereMonth('ps.created_at', $month);
            }

            $chartData = $chartQuery->groupBy('cs.code_1')->orderBy('total_peserta', 'desc')->limit(10)->get();

            // Format data untuk chart
            $chartLabels = $chartData->pluck('code_1')->toArray();
            $chartValues = $chartData->pluck('total_peserta')->toArray();

            // Ambil Activity Logs dengan filter tanggal
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            $activityLogs = ActivityLog::whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
                ->with([
                    'causer' => function ($query) {
                        $query->select('id', 'name', 'email');
                    },
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Expired certificates sekarang akan di-load via AJAX DataTable
            // Jadi kita tidak perlu query di sini lagi

            return view('dashboard.superAdmin', compact('totalAsesi', 'totalSertifikat', 'totalLembagaPelatihan', 'totalSkema', 'chartLabels', 'chartValues', 'activityLogs', 'startDate', 'endDate', 'year', 'month'));
        } catch (\Exception $e) {
            return view('dashboard.superAdmin')->with('error', 'Terjadi kesalahan dalam memuat data dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics data via AJAX
     */
    public function getStatisticsData(Request $request)
    {
        try {
            $year = $request->get('year', 'all');

            // Build query for filtered data based on tahun_registrasi
            $query = PesertaSertifikat::query();

            if ($year !== 'all') {
                $query->where('tahun_registrasi', $year);
            }

            $totalAsesi = (clone $query)->count();
            $totalSertifikat = (clone $query)->whereNotNull('tanggal_terbit')->count();
            $totalLembagaPelatihan = LembagaPelatihan::count();
            $totalSkema = CertificationScheme::count();

            return response()->json([
                'success' => true,
                'totalAsesi' => $totalAsesi,
                'totalSertifikat' => $totalSertifikat,
                'totalLembagaPelatihan' => $totalLembagaPelatihan,
                'totalSkema' => $totalSkema,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get chart data via AJAX
     */
    public function getChartData(Request $request)
    {
        try {
            $year = $request->get('year', 'all');

            \Log::info('Chart data request:', ['year' => $year]);

            // Generate 26 dummy data points for demonstration
            // $dummyData = [
            //     ['code_1' => 'SKM-001', 'total_peserta' => 156],
            //     ['code_1' => 'SKM-002', 'total_peserta' => 142],
            //     ['code_1' => 'SKM-003', 'total_peserta' => 128],
            //     ['code_1' => 'SKM-004', 'total_peserta' => 118],
            //     ['code_1' => 'SKM-005', 'total_peserta' => 105],
            //     ['code_1' => 'SKM-006', 'total_peserta' => 98],
            //     ['code_1' => 'SKM-007', 'total_peserta' => 87],
            //     ['code_1' => 'SKM-008', 'total_peserta' => 82],
            //     ['code_1' => 'SKM-009', 'total_peserta' => 76],
            //     ['code_1' => 'SKM-010', 'total_peserta' => 69],
            //     ['code_1' => 'SKM-011', 'total_peserta' => 64],
            //     ['code_1' => 'SKM-012', 'total_peserta' => 58],
            //     ['code_1' => 'SKM-013', 'total_peserta' => 53],
            //     ['code_1' => 'SKM-014', 'total_peserta' => 47],
            //     ['code_1' => 'SKM-015', 'total_peserta' => 43],
            //     ['code_1' => 'SKM-016', 'total_peserta' => 39],
            //     ['code_1' => 'SKM-017', 'total_peserta' => 35],
            //     ['code_1' => 'SKM-018', 'total_peserta' => 31],
            //     ['code_1' => 'SKM-019', 'total_peserta' => 28],
            //     ['code_1' => 'SKM-020', 'total_peserta' => 24],
            //     ['code_1' => 'SKM-021', 'total_peserta' => 21],
            //     ['code_1' => 'SKM-022', 'total_peserta' => 18],
            //     ['code_1' => 'SKM-023', 'total_peserta' => 15],
            //     ['code_1' => 'SKM-024', 'total_peserta' => 12],
            //     ['code_1' => 'SKM-025', 'total_peserta' => 9],
            //     ['code_1' => 'SKM-026', 'total_peserta' => 6]
            // ];

            // If you want to use real data, uncomment this block:
            $chartQuery = DB::table('peserta_sertifikats as ps')->join('certification_schemes as cs', 'ps.sertifikasi', '=', 'cs.nama')->select('cs.code_1', DB::raw('COUNT(ps.id) as total_peserta'));

            if ($year !== 'all') {
                $chartQuery->where('ps.tahun_registrasi', $year);
            }

            $chartData = $chartQuery
                ->groupBy('cs.code_1')
                ->orderBy('cs.code_1', 'asc')
                ->limit(26) // Show 26 items instead of 10
                ->get();

            // Use dummy data for now
            // $chartData = collect($dummyData);

            // Apply year filter to dummy data (simulate filtering)
            if ($year !== 'all') {
                // Simulate year filtering by reducing counts for older years
                $yearMultiplier = $year == date('Y') ? 1 : 0.7;
                $chartData = $chartData->map(function ($item) use ($yearMultiplier) {
                    return [
                        'code_1' => $item['code_1'],
                        'total_peserta' => (int) ($item['total_peserta'] * $yearMultiplier),
                    ];
                });
            }

            $labels = $chartData->pluck('code_1')->toArray();
            $values = $chartData->pluck('total_peserta')->toArray();

            // Convert to numbers to ensure JSON is correct
            $values = array_map('intval', $values);

            \Log::info('Chart data response:', [
                'labels' => $labels,
                'values' => $values,
                'count' => count($labels),
            ]);

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'values' => $values,
            ]);
        } catch (\Exception $e) {
            \Log::error('Chart data error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'labels' => [],
                    'values' => [],
                ],
                500,
            );
        }
    }

    public function getExpiringCertificatesDataTable(Request $request)
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $threeMonthsLater = Carbon::now()->addMonths(3)->format('Y-m-d');

            // Base query - PostgreSQL compatible
            $query = PesertaSertifikat::whereNotNull('tanggal_exp')
                ->whereBetween('tanggal_exp', [$today, $threeMonthsLater])
                ->select([
                    'id',
                    'nama',
                    'sertifikasi',
                    'tanggal_exp',
                    // PostgreSQL syntax untuk calculate days difference
                    DB::raw('(tanggal_exp::date - CURRENT_DATE) as days_left'),
                ]);

            // Get total records without filtering
            $totalRecords = PesertaSertifikat::whereNotNull('tanggal_exp')
                ->whereBetween('tanggal_exp', [$today, $threeMonthsLater])
                ->count();

            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('nama', 'ILIKE', "%{$searchValue}%") // ILIKE untuk case-insensitive di PostgreSQL
                        ->orWhere('sertifikasi', 'ILIKE', "%{$searchValue}%");
                });
            }

            // Get filtered count
            $filteredRecords = $query->count();

            // Apply ordering
            if ($request->has('order')) {
                $orderColumn = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];

                $columns = ['id', 'nama', 'sertifikasi', 'tanggal_exp', 'days_left', 'status'];

                if (isset($columns[$orderColumn])) {
                    if ($columns[$orderColumn] === 'days_left') {
                        // PostgreSQL syntax untuk order by calculated field
                        $query->orderByRaw("(tanggal_exp::date - CURRENT_DATE) {$orderDir}");
                    } else {
                        $query->orderBy($columns[$orderColumn], $orderDir);
                    }
                }
            } else {
                // Default order by days_left ascending (most critical first)
                $query->orderByRaw('(tanggal_exp::date - CURRENT_DATE) ASC');
            }

            // Apply pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 25;

            $data = $query->skip($start)->take($length)->get();

            // Format data for DataTables
            $formattedData = $data->map(function ($certificate) {
                $expDate = Carbon::parse($certificate->tanggal_exp);
                $now = Carbon::now();
                $daysLeft = $now->diffInDays($expDate, false);

                // If date is in the past, make it negative
                if ($expDate->lt($now)) {
                    $daysLeft = -$daysLeft;
                }

                return [
                    'id' => $certificate->id,
                    'nama' => $certificate->nama,
                    'sertifikasi' => $certificate->sertifikasi,
                    'tanggal_exp' => $certificate->tanggal_exp,
                    'days_left' => (int) $daysLeft,
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getExpiringCertificatesDataTable:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'draw' => intval($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage(),
            ]);
        }
    }
}
