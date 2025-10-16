<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Apl01Pendaftaran;
use App\Models\Apl02;
use App\Models\CertificationScheme;
use App\Services\InboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AsesiInboxController extends Controller
{
    public function __construct(protected InboxService $inboxService) {}

    /**
     * Display the unified inbox
     */
    public function index()
    {
        return view('asesi.inbox.unified', [
            'schemes' => CertificationScheme::where('is_active', true)->get(),
        ]);
    }

    public function getInboxData(Request $request)
    {
        $user = auth()->user();
        $query = collect();

        $aplType = $request->apl_type;
        $status = $request->status;
        $schemeId = $request->scheme_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $search = $request->search;
        $page = (int) $request->page ?: 1;
        $perPage = (int) $request->per_page ?: 10;

        // Get APL 01 data - STATUS DARI KOLOM STATUS
        if ($aplType === 'all' || $aplType === 'apl01') {
            $apl01Query = Apl01Pendaftaran::where('user_id', $user->id)
                ->with(['certificationScheme:id,nama'])
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status); // Status dari table
                })
                ->when($schemeId, function ($q) use ($schemeId) {
                    $q->where('certification_scheme_id', $schemeId);
                })
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($subQ) use ($search) {
                        $subQ->where('nama_lengkap', 'like', "%{$search}%")->orWhereHas('certificationScheme', function ($schemeQ) use ($search) {
                            $schemeQ->where('nama', 'like', "%{$search}%");
                        });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($apl01) {
                    $hasApl02 = Apl02::where('apl_01_id', $apl01->id)->exists();

                    return [
                        'id' => $apl01->id,
                        'apl_type' => 'apl01',
                        'status' => $apl01->status, // Status dari table
                        'certification_scheme' => $apl01->certificationScheme->nama ?? 'N/A',
                        'created_at_formatted' => $apl01->created_at->format('d M Y'),
                        'created_at' => $apl01->created_at,
                        'progress_percentage' => $apl01->getProgressPercentage(),
                        'has_apl02' => $hasApl02,
                    ];
                });

            $query = $query->merge($apl01Query);
        }

        // Get APL 02 data - STATUS DARI KOLOM STATUS
        if ($aplType === 'all' || $aplType === 'apl02') {
            $apl02Query = Apl02::where('user_id', $user->id)
                ->with(['certificationScheme:id,nama', 'apl01:id,user_id,tuk,certification_scheme_id'])
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status); // Status dari table
                })
                ->when($schemeId, function ($q) use ($schemeId) {
                    $q->where('certification_scheme_id', $schemeId);
                })
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($subQ) use ($search) {
                        $subQ
                            ->whereHas('apl01', function ($apl01Q) use ($search) {
                                $apl01Q->where('nama_lengkap', 'like', "%{$search}%");
                            })
                            ->orWhereHas('certificationScheme', function ($schemeQ) use ($search) {
                                $schemeQ->where('nama', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($apl02) {
                    $tukType = '';
                    $isTukMandiri = false;
                    $isTukSewaktu = false;
                    $apl01Id = null;

                    if ($apl02->apl01) {
                        $tukType = strtolower($apl02->apl01->tuk ?? '');
                        $isTukMandiri = $tukType === 'mandiri';
                        $isTukSewaktu = $tukType === 'sewaktu';
                        $apl01Id = $apl02->apl01->id;
                    }

                    $hasTukRequest = false;
                    if ($isTukSewaktu && $apl02->apl_01_id) {
                        $hasTukRequest = \App\Models\TukRequest::where('apl01_id', $apl02->apl_01_id)->exists();
                    }

                    return [
                        'id' => $apl02->id,
                        'apl_type' => 'apl02',
                        'status' => $apl02->status, // Status dari table
                        'certification_scheme' => $apl02->certificationScheme->nama ?? 'N/A',
                        'created_at_formatted' => $apl02->created_at->format('d M Y'),
                        'created_at' => $apl02->created_at,
                        'progress_percentage' => $apl02->getProgressPercentage(),
                        'has_apl02' => null,
                        'apl01_id' => $apl01Id,
                        'tuk_type' => $tukType,
                        'is_tuk_mandiri' => $isTukMandiri,
                        'is_tuk_sewaktu' => $isTukSewaktu,
                        'has_tuk_request' => $hasTukRequest,
                    ];
                });

            $query = $query->merge($apl02Query);
        }

        // Get TUK Request data - STATUS DARI recommended_by (BUKAN dari kolom status)
        if ($aplType === 'all' || $aplType === 'tuk') {
            $tukRequestQuery = \App\Models\TukRequest::where('user_id', $user->id)
                ->with(['apl01:id,nama_lengkap,certification_scheme_id', 'apl01.certificationScheme:id,nama'])
                // Filter berdasarkan recommended_by untuk menentukan status
                ->when($status === 'submitted', function ($q) {
                    $q->whereNull('recommended_by'); // Submitted = belum direkomendasi
                })
                ->when($status === 'approved', function ($q) {
                    $q->whereNotNull('recommended_by'); // Approved = sudah direkomendasi
                })
                // Status lain (draft, rejected, open, returned) tidak berlaku untuk TUK
                // Jika status kosong, tampilkan semua TUK
                ->when($schemeId, function ($q) use ($schemeId) {
                    $q->whereHas('apl01', function ($apl01Q) use ($schemeId) {
                        $apl01Q->where('certification_scheme_id', $schemeId);
                    });
                })
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($subQ) use ($search) {
                        $subQ
                            ->whereHas('apl01', function ($apl01Q) use ($search) {
                                $apl01Q->where('nama_lengkap', 'like', "%{$search}%");
                            })
                            ->orWhereHas('apl01.certificationScheme', function ($schemeQ) use ($search) {
                                $schemeQ->where('nama', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($tukRequest) {
                    // Status ditentukan dari recommended_by
                    $tukStatus = $tukRequest->recommended_by ? 'approved' : 'submitted';

                    return [
                        'id' => $tukRequest->id,
                        'apl_type' => 'tuk',
                        'status' => $tukStatus, // Status dari recommended_by
                        'certification_scheme' => $tukRequest->apl01->certificationScheme->nama ?? 'N/A',
                        'created_at_formatted' => $tukRequest->created_at->format('d M Y'),
                        'created_at' => $tukRequest->created_at,
                        'progress_percentage' => null,
                        'has_apl02' => null,
                        'apl01_id' => $tukRequest->apl01_id,
                        'tuk_type' => 'sewaktu',
                        'is_tuk_mandiri' => false,
                        'is_tuk_sewaktu' => true,
                        'has_tuk_request' => true,
                        'tuk_recommended_by' => $tukRequest->recommended_by,
                        'tuk_request_id' => $tukRequest->id,
                        'nama_lengkap' => $tukRequest->apl01->nama_lengkap ?? 'N/A',
                    ];
                });

            $query = $query->merge($tukRequestQuery);
        }

        // Sort by created_at desc
        $query = $query->sortByDesc('created_at');

        // Paginate manually
        $offset = ($page - 1) * $perPage;
        $paginatedQuery = $query->slice($offset, $perPage)->values();

        return response()->json([
            'success' => true,
            'data' => $paginatedQuery,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $query->count(),
        ]);
    }
    /**
     * Get unified APL data (APL01 & APL02)
     */
    public function getData(Request $request)
    {
        try {
            $filters = $request->only(['apl_type', 'status', 'date_from', 'date_to', 'search', 'scheme_id', 'page', 'per_page']);

            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;

            $allData = $this->inboxService->getUnifiedAplData($filters);

            // Apply pagination
            $paginatedData = array_slice($allData, $offset, $perPage);

            return response()->json([
                'success' => true,
                'data' => $paginatedData,
                'has_more' => count($allData) > $offset + $perPage,
                'total' => count($allData),
            ]);
        } catch (\Exception $e) {
            Log::error('Asesi Inbox Data Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal memuat data: ' . $e->getMessage(),
                    'data' => [],
                ],
                500,
            );
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $stats = $this->inboxService->getStatistics();
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Asesi Inbox Statistics Error: ' . $e->getMessage());
            return response()->json(
                [
                    'total' => 0,
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                    'revision' => 0,
                    'draft' => 0,
                ],
                500,
            );
        }
    }

    /**
     * Get approved APL01 list for APL02 creation
     */
    public function getApprovedApl01List()
    {
        try {
            $userId = Auth::id();

            // Only get APL 01 that are approved and don't have APL 02 yet
            $approvedApl01s = Apl01Pendaftaran::where('user_id', $userId)
                ->where('status', 'approved')
                ->whereDoesntHave('apl02') // This ensures APL 01 doesn't have APL 02 yet
                ->with('certificationScheme')
                ->orderBy('reviewed_at', 'desc')
                ->get()
                ->map(function ($apl01) {
                    return [
                        'id' => $apl01->id,
                        'nomor_apl_01' => $apl01->nomor_apl_01,
                        'certification_scheme' => $apl01->certificationScheme->nama ?? 'N/A',
                        'approved_at' => $apl01->reviewed_at ? $apl01->reviewed_at->format('d M Y') : 'N/A',
                    ];
                });

            return response()->json($approvedApl01s);
        } catch (\Exception $e) {
            Log::error('Approved APL01 List Error: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Export all APL data
     */
    public function exportData()
    {
        try {
            $userId = Auth::id();

            // Get all APL data for export
            $allData = $this->inboxService->getUnifiedAplData([]);

            // Generate filename
            $filename = 'apl_export_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($allData) {
                $file = fopen('php://output', 'w');

                // CSV Headers
                fputcsv($file, ['Tipe APL', 'Nomor APL', 'Skema Sertifikasi', 'Status', 'Tanggal Dibuat', 'Tanggal Submit', 'Progress (%)', 'Catatan']);

                // Data rows
                foreach ($allData as $item) {
                    fputcsv($file, [strtoupper($item['apl_type']), $item['nomor_apl'], $item['certification_scheme'], ucfirst($item['status']), $item['created_at_formatted'], $item['submitted_at'] ?? '-', $item['progress_percentage'], $item['rejection_note'] ?? '-']);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Export Data Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal membuat export: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function viewTukMandiri($apl01Id)
    {
        $apl01 = Apl01Pendaftaran::where('id', $apl01Id)->where('user_id', Auth::id())->firstOrFail();

        // Verify this is TUK Mandiri
        if (strtolower($apl01->tuk) !== 'mandiri') {
            return redirect()->back()->with('error', 'APL ini bukan TUK Mandiri');
        }

        $apl01->load(['certificationScheme']);

        return view('asesi.tuk-request.view-mandiri', compact('apl01'));
    }
}
