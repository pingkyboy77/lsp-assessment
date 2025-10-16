<?php

namespace App\Http\Controllers\Asesor;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DelegasiPersonilAsesmen;

class AsesorDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // dd('masuk');
            $user = Auth::user();

            // Get filter parameters
            $month = $request->get('month', now()->format('Y-m'));
            $status = $request->get('status', 'all');

            // Parse month for query
            $monthStart = Carbon::parse($month . '-01')->startOfMonth();
            $monthEnd = Carbon::parse($month . '-01')->endOfMonth();

            // Base query untuk jadwal assessment asesor
            $baseQuery = DelegasiPersonilAsesmen::with([
                'asesi:id,name,email',
                'certificationScheme:id,nama,code_1',
                'verifikatorTuk:id,name',
                'observer:id,name',
                'asesor:id,name',
                'apl01:id,nomor_apl_01',
                'tukRequest:id,kode_tuk'
            ])->where('asesor_id', $user->id);

            // Statistics
            $totalAssessments = (clone $baseQuery)->count();

            $upcomingAssessments = (clone $baseQuery)
                ->where('tanggal_pelaksanaan_asesmen', '>=', now()->toDateString())
                ->where('status_assessment', '!=', 'completed')
                ->count();

            $completedAssessments = (clone $baseQuery)
                ->where('status_assessment', 'completed')
                ->count();

            $todayAssessments = (clone $baseQuery)
                ->whereDate('tanggal_pelaksanaan_asesmen', now()->toDateString())
                ->where('status_assessment', '!=', 'completed')
                ->count();

            // Get scheduled assessments with filters
            $query = (clone $baseQuery)
                ->whereBetween('tanggal_pelaksanaan_asesmen', [$monthStart, $monthEnd]);

            // Apply status filter
            if ($status !== 'all') {
                if ($status === 'upcoming') {
                    $query->where('tanggal_pelaksanaan_asesmen', '>=', now()->toDateString())
                        ->where('status_assessment', '!=', 'completed');
                } elseif ($status === 'completed') {
                    $query->where('status_assessment', 'completed');
                } elseif ($status === 'today') {
                    $query->whereDate('tanggal_pelaksanaan_asesmen', now()->toDateString());
                }
            }

            $scheduledAssessments = $query
                ->orderBy('tanggal_pelaksanaan_asesmen', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->paginate(10);

            // Calendar data for current month
            $calendarData = $this->getCalendarData($user->id, $monthStart, $monthEnd);

            return view('dashboard.asesor', compact(
                'totalAssessments',
                'upcomingAssessments',
                'completedAssessments',
                'todayAssessments',
                'scheduledAssessments',
                'calendarData',
                'month',
                'status'
            ));
        } catch (\Exception $e) {
            \Log::error('Error loading asesor dashboard: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return empty paginator instead of collection
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('dashboard.asesor', [
                'error' => 'Terjadi kesalahan saat memuat dashboard: ' . $e->getMessage(),
                'totalAssessments' => 0,
                'upcomingAssessments' => 0,
                'completedAssessments' => 0,
                'todayAssessments' => 0,
                'scheduledAssessments' => $emptyPaginator,
                'calendarData' => [],
                'month' => now()->format('Y-m'),
                'status' => 'all'
            ]);
        }
    }

    private function getCalendarData($asesorId, $startDate, $endDate)
    {
        $assessments = DelegasiPersonilAsesmen::with([
            'asesi:id,name',
            'certificationScheme:id,nama,code_1'
        ])
            ->where('asesor_id', $asesorId)
            ->whereBetween('tanggal_pelaksanaan_asesmen', [$startDate, $endDate])
            ->get();

        $calendar = [];
        foreach ($assessments as $assessment) {
            $date = Carbon::parse($assessment->tanggal_pelaksanaan_asesmen)->format('Y-m-d');

            if (!isset($calendar[$date])) {
                $calendar[$date] = [];
            }

            $calendar[$date][] = [
                'id' => $assessment->id,
                'asesi' => $assessment->asesi->name ?? 'Unknown',
                'scheme' => $assessment->certificationScheme->code_1 ?? 'N/A',
                'time' => $assessment->waktu_mulai,
                'type' => $assessment->jenis_ujian,
                'status' => $assessment->status_assessment ?? 'scheduled'
            ];
        }

        return $calendar;
    }

    public function getAssessmentDetail($id)
    {
        try {
            $user = Auth::user();

            $assessment = DelegasiPersonilAsesmen::with([
                'asesi:id,name,email',
                'certificationScheme:id,nama,code_1',
                'verifikatorTuk:id,name,email',
                'observer:id,name,email',
                'asesor:id,name,email',
                'apl01',
                'tukRequest'
            ])
                ->where('asesor_id', $user->id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $assessment->id,
                    'asesi' => [
                        'name' => $assessment->asesi->name ?? '-',
                        'email' => $assessment->asesi->email ?? '-',
                    ],
                    'certification_scheme' => [
                        'nama' => $assessment->certificationScheme->nama ?? '-',
                        'kode_skema' => $assessment->certificationScheme->code_1 ?? '-'
                    ],
                    'tanggal_pelaksanaan_asesmen' => $assessment->tanggal_pelaksanaan_asesmen,
                    'waktu_mulai' => $assessment->waktu_mulai ?? '-',
                    'jenis_ujian' => $assessment->jenis_ujian ?? '-',
                    'asesor_met' => $assessment->asesor_met ?? '-',
                    'verifikator_tuk' => [
                        'name' => $assessment->verifikatorTuk->name ?? '-'
                    ],
                    'observer' => [
                        'name' => $assessment->observer->name ?? '-'
                    ],
                    'asesor' => [
                        'name' => $assessment->asesor->name ?? '-'
                    ],
                    'notes' => $assessment->notes ?? '-'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getAssessmentDetail: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail assessment: ' . $e->getMessage()
            ], 500);
        }
    }
}
