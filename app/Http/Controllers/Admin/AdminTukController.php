<?php

namespace App\Http\Controllers\Admin;

use App\Models\TukRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RekomendasiLSP;
use App\Models\Apl01Pendaftaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\TukRescheduleHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AdminTukController extends Controller
{
    /**
     * Show TUK management dashboard with separate tables
     */
    public function index(Request $request)
    {
        $stats = $this->getStats();
        return view('admin.tuk-requests.index', compact('stats'));
    }

    /**
     * Get DataTables data for TUK Sewaktu (needs recommendation)
     */
    private function checkRekomendasiStatus($apl01)
    {
        $rekomendasi = $apl01->rekomendasiLsp;

        if (!$rekomendasi) {
            return [
                'exists' => false,
                'is_signed' => false,
                'can_sign' => true,
            ];
        }

        return [
            'exists' => true,
            'is_signed' => !empty($rekomendasi->ttd_admin_path) && !empty($rekomendasi->tanggal_ttd_admin),
            'can_sign' => false,
            'data' => $rekomendasi,
        ];
    }

    /**
     * Update getTukSewaktuData method
     */
    public function getTukSewaktuData(Request $request)
    {
        try {
            $query = TukRequest::with([
                'apl01' => function ($q) {
                    $q->with(['user', 'certificationScheme', 'apl02', 'rekomendasiLsp']);
                },
                'recommendedBy',
                'delegasi',
            ])
                ->whereHas('apl01', function ($q) {
                    $q->where('tuk', 'Sewaktu');
                })
                ->whereNotNull('tanggal_assessment')
                ->whereNotNull('lokasi_assessment')
                ->whereNotNull('tanda_tangan_peserta_path');

            // ✅ FILTER TANGGAL ASSESSMENT
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('tanggal_assessment', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('tanggal_assessment', '<=', $request->date_to);
            }

            // ✅ FILTER STATUS REKOMENDASI
            if ($request->has('filter') && $request->filter) {
                switch ($request->filter) {
                    case 'recommended':
                        $query->whereNotNull('recommended_at');
                        break;
                    case 'pending':
                        $query->whereNull('recommended_at');
                        break;
                }
            }

            return DataTables::of($query)
                // Kolom yang searchable harus raw data, bukan HTML
                ->addColumn('kode_tuk', function ($row) {
                    return $row->kode_tuk ?? 'N/A';
                })
                ->addColumn('kode_tuk_display', function ($row) {
                    $kodeTuk = $row->kode_tuk ?? 'N/A';
                    $createdAt = $row->created_at ? $row->created_at->format('d M Y') : 'N/A';
                    return '<strong class="text-primary">' . e($kodeTuk) . '</strong><br>' . '<small class="text-muted">' . e($createdAt) . '</small>';
                })
                ->addColumn('participant_info', function ($row) {
                    $nama = $row->apl01?->nama_lengkap ?? 'N/A';
                    $nomor = $row->apl01?->nomor_apl_01 ?? 'N/A';
                    $email = $row->apl01?->user?->email ?? 'N/A';

                    return '<div class="participant-info">' . '<strong class="d-block">' . e($nama) . '</strong>' . '<small class="text-muted d-block">' . e($nomor) . '</small>' . '<small class="text-muted">' . e($email) . '</small>' . '</div>';
                })
                ->addColumn('scheme_name', function ($row) {
                    $schemeName = $row->apl01?->certificationScheme?->nama ?? 'N/A';
                    return e($schemeName);
                })
                ->addColumn('assessment_info', function ($row) {
                    $tanggal = 'N/A';
                    $jamMulai = '';

                    if ($row->tanggal_assessment) {
                        try {
                            $tanggal = $row->tanggal_assessment->format('d F Y');
                        } catch (\Exception $e) {
                            $tanggal = 'Invalid Date';
                        }
                    }

                    if ($row->jam_mulai) {
                        try {
                            $jamMulai = '<br><span class="badge bg-info">' . $row->jam_mulai->format('H:i') . '</span>';
                        } catch (\Exception $e) {
                            $jamMulai = '';
                        }
                    }

                    return '<div class="text-center"><strong>' . e($tanggal) . '</strong>' . $jamMulai . '</div>';
                })
                ->addColumn('lokasi_info', function ($row) {
                    $lokasi = $row->lokasi_assessment ?? 'N/A';
                    $shortLokasi = Str::limit($lokasi, 60);
                    return '<small>' . e($shortLokasi) . '</small>';
                })
                ->addColumn('status_badge', function ($row) {
                    if ($row->recommended_at && $row->recommended_by) {
                        $html = '<span class="badge bg-success">Direkomendasi</span><br>';
                        $html .= '<small class="text-muted">' . $row->recommended_at->format('d M Y') . '</small>';
                        if ($row->delegasi) {
                            $html .= '<br><span class="badge bg-info mt-1">Sudah Didelegasi</span>';
                        }
                    } else {
                        $html = '<span class="badge bg-warning">Pending Review</span>';
                    }
                    return $html;
                })
                ->addColumn('actions', function ($row) {
                    $buttons = [];

                    // Status checks
                    $hasRecommendation = $row->recommended_at && $row->recommended_by;
                    $hasDelegation = $row->delegasi !== null;
                    $hasApl01 = $row->apl01 !== null;
                    $hasApl02 = $hasApl01 && $row->apl01->apl02 !== null;
                    $apl01Completed = $hasApl01 && !is_null($row->apl01->completed_at);
                    $apl02Completed = $hasApl02 && !is_null($row->apl01->apl02->completed_at);
                    $bothApproved = $apl01Completed && (!$hasApl02 || $apl02Completed);
                    $rekomendasiStatus = $this->checkRekomendasiStatus($row->apl01);
                    $hasSPTSigned = $hasDelegation && $row->delegasi->sptSignature && $row->delegasi->sptSignature->status === 'signed';

                    // Button logic sama seperti sebelumnya
                    if (!$hasRecommendation) {
                        $buttons[] = '<button type="button" class="btn btn-sm btn-warning" onclick="openRecommendModal(' . $row->id . ')" title="Buat Rekomendasi TUK"><i class="bi bi-clipboard-check me-1"></i>Rekomendasi</button>';
                    } else {
                        $buttons[] = '<button type="button" class="btn btn-sm btn-success" onclick="openRecommendModal(' . $row->id . ')" title="Lihat/Edit Rekomendasi"><i class="bi bi-check-circle me-1"></i>Direkomendasi</button>';

                        if ($bothApproved) {
                            $buttons[] = '<button type="button" class="btn btn-sm btn-outline-success" onclick="openCombinedReviewModal(' . $row->apl01->id . ', true)" title="Lihat Dokumen (View Only)"><i class="bi bi-eye me-1"></i>Lihat</button>';
                        } else {
                            $buttons[] = '<button type="button" class="btn btn-sm btn-outline-warning" onclick="openCombinedReviewModal(' . $row->apl01->id . ', false)" title="Review dan Approve/Reject"><i class="bi bi-clock me-1"></i>Review</button>';
                        }

                        if ($bothApproved) {
                            if ($hasDelegation) {
                                $buttons[] = '<button type="button" class="btn btn-sm btn-primary" onclick="viewDelegasi(' . $row->delegasi->id . ')" title="Lihat Delegasi"><i class="bi bi-eye me-1"></i>Delegasi</button>';
                            } else {
                                $buttons[] = '<button type="button" class="btn btn-sm btn-success" onclick="delegasiTuk(' . $row->id . ')" title="Delegasi TUK"><i class="bi bi-person-check me-1"></i>Delegasi</button>';
                            }
                        }

                        if ($bothApproved && $hasDelegation && !$hasSPTSigned) {
                            $buttons[] = '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="goToSPTPage(' . $row->delegasi->id . ')" title="Menunggu TTD Direktur"><i class="bi bi-hourglass-split me-1"></i>TTD SPT</button>';
                        }

                        if ($bothApproved && $hasDelegation && $hasSPTSigned) {
                            if ($rekomendasiStatus['is_signed']) {
                                $buttons[] = '<button type="button" class="btn btn-sm btn-success" onclick="viewRekomendasiLSP(' . $row->apl01->id . ')" title="Lihat Rekomendasi LSP"><i class="bi bi-award me-1"></i>Rekomendasi LSP</button>';
                            } else {
                                $buttons[] = '<button type="button" class="btn btn-sm btn-warning" onclick="openTTDRekomendasiModal(' . $row->apl01->id . ')" title="Tanda Tangan Rekomendasi LSP"><i class="bi bi-pencil-square me-1"></i>TTD Rekomendasi</button>';
                            }
                        }
                    }

                    $html = '<div class="btn-group-horizontal" role="group">' . implode('', $buttons) . '</div>';
                    return $html;
                })
                ->filter(function ($query) use ($request) {
                    // ✅ SERVER-SIDE SEARCH - Hanya cari di kolom yang penting
                    if ($request->has('search') && $request->search) {
                        $search = $request->search;
                        $query->where(function ($q) use ($search) {
                            $q->where('kode_tuk', 'like', "%{$search}%")->orWhereHas('apl01', function ($subQ) use ($search) {
                                $subQ
                                    ->where('nama_lengkap', 'like', "%{$search}%")
                                    ->orWhere('nomor_apl_01', 'like', "%{$search}%")
                                    ->orWhereHas('user', function ($userQ) use ($search) {
                                        $userQ->where('email', 'like', "%{$search}%");
                                    });
                            });
                        });
                    }
                })
                ->rawColumns(['kode_tuk_display', 'participant_info', 'scheme_name', 'assessment_info', 'lokasi_info', 'status_badge', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in getTukSewaktuData: ' . $e->getMessage());
            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    private function getSPTStatus($delegasi)
    {
        if (!$delegasi) {
            return [
                'exists' => false,
                'is_signed' => false,
            ];
        }

        $spt = $delegasi->sptSignature;

        return [
            'exists' => $spt !== null,
            'is_signed' => $spt && $spt->status === 'signed',
            'data' => $spt,
        ];
    }
    /**
     * View Rekomendasi LSP (after signed) - Combined with APL review
     */
    public function viewRekomendasiLSP($apl01Id)
    {
        try {
            $apl01 = Apl01Pendaftaran::with([
                'rekomendasiLsp.admin',
                'user:id,name,email',
                'user.documents',
                'certificationScheme:id,code_1,nama,jenjang',
                'apl02' => function ($q) {
                    $q->with(['certificationScheme.units.elemenKompetensis.kriteriaKerjas', 'certificationScheme.portfolioFiles', 'elementAssessments.elemenKompetensi.unitKompetensi', 'evidenceSubmissions.portfolioFile']);
                },
            ])->findOrFail($apl01Id);

            // Load template
            if ($apl01->selected_requirement_template_id) {
                $apl01->load([
                    'selectedRequirementTemplate' => function ($query) {
                        $query->with([
                            'activeItems' => function ($q) {
                                $q->where('is_active', true)->orderBy('sort_order', 'asc');
                            },
                        ]);
                    },
                ]);
            }

            // Check if rekomendasi exists
            if (!$apl01->hasRekomendasi()) {
                return response()->make(
                    "<div class='alert alert-warning'>
                    <i class='bi bi-exclamation-triangle me-2'></i>
                    <strong>Rekomendasi LSP belum dibuat.</strong>
                </div>",
                    200,
                );
            }

            // Return the view (bukan JSON)
            return view('admin.tuk-requests.view-rekomendasi-lsp-combined', compact('apl01'));
        } catch (\Exception $e) {
            Log::error('Error viewing rekomendasi LSP: ' . $e->getMessage(), [
                'apl01_id' => $apl01Id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->make(
                "<div class='alert alert-danger'>
                <i class='bi bi-exclamation-triangle me-2'></i>
                <strong>Gagal memuat rekomendasi LSP:</strong> {$e->getMessage()}
            </div>",
                500,
            );
        }
    }

    /**
     * Get DataTables data for TUK Mandiri
     */
    public function getTukMandiriData(Request $request)
    {
        try {
            $query = Apl01Pendaftaran::with(['user', 'certificationScheme', 'reviewer', 'delegasi', 'rekomendasiLsp', 'apl02'])
                ->where('tuk', 'Mandiri')
                ->where('status', 'approved');

            // ✅ FILTER TANGGAL CREATED
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($query)
                // Kolom searchable harus raw data
                ->addColumn('nomor_apl_01', function ($row) {
                    return $row->nomor_apl_01 ?? 'N/A';
                })
                ->addColumn('nomor_display', function ($row) {
                    $nomor = $row->nomor_apl_01 ?? 'N/A';
                    $createdAt = $row->created_at ? $row->created_at->format('d M Y') : 'N/A';
                    return '<strong class="text-info">' . e($nomor) . '</strong><br>' . '<small class="text-muted">' . e($createdAt) . '</small>';
                })
                ->addColumn('participant_info', function ($row) {
                    $nama = $row->nama_lengkap ?? 'N/A';
                    $nik = $row->nik ?? 'N/A';
                    $email = $row->user?->email ?? 'N/A';

                    return '<div class="participant-info">' . '<strong class="d-block">' . e($nama) . '</strong>' . '<small class="text-muted d-block">NIK: ' . e($nik) . '</small>' . '<small class="text-muted">' . e($email) . '</small>' . '</div>';
                })
                ->addColumn('scheme_name', function ($row) {
                    $schemeName = $row->certificationScheme?->nama ?? 'N/A';
                    return e($schemeName);
                })
                ->addColumn('company_info', function ($row) {
                    $perusahaan = $row->nama_tempat_kerja ?? 'N/A';
                    $jabatan = $row->jabatan ?? 'N/A';

                    return '<strong class="d-block">' . e($perusahaan) . '</strong>' . '<small class="text-muted">' . e($jabatan) . '</small>';
                })
                ->addColumn('status_badge', function ($row) {
                    $html = '<span class="badge bg-success">Approved</span>';

                    if ($row->reviewed_at) {
                        try {
                            $reviewedDate = $row->reviewed_at->format('d M Y');
                            $html .= '<br><small class="text-muted">' . e($reviewedDate) . '</small>';
                        } catch (\Exception $e) {
                            $html .= '';
                        }
                    }

                    if ($row->reviewer?->name) {
                        $html .= '<br><small class="text-muted">oleh: ' . e($row->reviewer->name) . '</small>';
                    }

                    if ($row->delegasi) {
                        $html .= '<br><span class="badge bg-info mt-1">Sudah Didelegasi</span>';
                    }

                    return $html;
                })
                ->addColumn('actions', function ($row) {
                    $buttons = [];

                    $hasDelegation = $row->delegasi !== null;
                    $hasApl02 = $row->apl02 !== null;
                    $apl01Approved = $row->status === 'approved';
                    $apl02Approved = $hasApl02 && $row->apl02->status === 'approved';
                    $bothApproved = $apl01Approved && (!$hasApl02 || $apl02Approved);
                    $hasSPTSigned = $hasDelegation && $row->delegasi->sptSignature && $row->delegasi->sptSignature->status === 'signed';
                    $rekomendasiStatus = $this->checkRekomendasiStatus($row);
                    // TUK Mandiri PDF button
                    $buttons[] = '<button type="button" class="btn btn-sm btn-outline-danger" onclick="viewTukMandiri(' . $row->id . ')" title="Lihat Dokumen TUK Mandiri"><i class="bi bi-file-earmark-pdf me-1"></i>Dokumen TUK</button>';

                    // Review button
                    if ($bothApproved) {
                        $buttons[] = '<button type="button" class="btn btn-sm btn-outline-success" onclick="openCombinedReviewModal(' . $row->id . ', true)" title="Lihat Dokumen (View Only)"><i class="bi bi-eye me-1"></i>Lihat</button>';
                    } else {
                        $buttons[] = '<button type="button" class="btn btn-sm btn-outline-warning" onclick="openCombinedReviewModal(' . $row->id . ', false)" title="Review dan Approve/Reject"><i class="bi bi-clock me-1"></i>Review</button>';
                    }

                    // Delegasi button
                    if ($bothApproved) {
                        if ($hasDelegation) {
                            $buttons[] = '<button type="button" class="btn btn-sm btn-info" onclick="viewDelegasi(' . $row->delegasi->id . ')" title="Lihat Delegasi"><i class="bi bi-eye me-1"></i>Delegasi</button>';
                        } else {
                            $buttons[] = '<button type="button" class="btn btn-sm btn-primary" onclick="delegasiAsesor(' . $row->id . ')" title="Delegasi Asesor"><i class="bi bi-person-check me-1"></i>Delegasi</button>';
                        }
                    }

                    // TTD SPT button
                    if ($bothApproved && $hasDelegation && !$hasSPTSigned) {
                        $buttons[] = '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="goToSPTPage(' . $row->delegasi->id . ')" title="Menunggu TTD Direktur"><i class="bi bi-hourglass-split me-1"></i>TTD SPT</button>';
                    }

                    // Rekomendasi LSP button
                    if ($bothApproved && $hasDelegation && $hasSPTSigned) {
                        if ($rekomendasiStatus['is_signed']) {
                            $buttons[] = '<button type="button" class="btn btn-sm btn-success" onclick="viewRekomendasiLSP(' . $row->id . ')" title="Lihat Rekomendasi LSP"><i class="bi bi-award me-1"></i>Rekomendasi LSP</button>';
                        } else {
                            $buttons[] = '<button type="button" class="btn btn-sm btn-warning" onclick="openTTDRekomendasiModal(' . $row->id . ')" title="Tanda Tangan Rekomendasi LSP"><i class="bi bi-pencil-square me-1"></i>TTD Rekomendasi</button>';
                        }
                    }
                    $html = '<div class="btn-group-horizontal" role="group">' . implode('', $buttons) . '</div>';
                    return $html;
                })
                ->filter(function ($query) use ($request) {
                    // ✅ SERVER-SIDE SEARCH - Hanya cari di kolom yang penting
                    if ($request->has('search') && $request->search) {
                        $search = $request->search;
                        $query->where(function ($q) use ($search) {
                            $q->where('nama_lengkap', 'like', "%{$search}%")
                                ->orWhere('nomor_apl_01', 'like', "%{$search}%")
                                ->orWhere('nama_tempat_kerja', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($userQ) use ($search) {
                                    $userQ->where('email', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->rawColumns(['nomor_display', 'participant_info', 'scheme_name', 'company_info', 'status_badge', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in getTukMandiriData: ' . $e->getMessage());
            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get delegation data for TUK Sewaktu
     */
    public function getDelegasiData(TukRequest $tukRequest)
    {
        try {
            $tukRequest->load(['apl01.user', 'apl01.certificationScheme', 'delegasi']);

            $data = [
                'asesi_id' => $tukRequest->apl01->user_id,
                'asesi_nama' => $tukRequest->apl01->nama_lengkap,
                'certification_scheme_id' => $tukRequest->apl01->certification_scheme_id,
                'skema_nama' => $tukRequest->apl01->certificationScheme->nama,
                'tuk_request_id' => $tukRequest->id,
                'apl01_id' => $tukRequest->apl01_id,

                // ✅ TAMBAHKAN: Kirim tanggal assessment dan jam mulai
                'tanggal_assessment' => $tukRequest->tanggal_assessment?->format('Y-m-d'),
                'jam_mulai' => $tukRequest->jam_mulai ? (is_string($tukRequest->jam_mulai) ? date('H:i', strtotime($tukRequest->jam_mulai)) : $tukRequest->jam_mulai->format('H:i')) : '08:00',

                'existing_delegation' => $tukRequest->delegasi
                    ? [
                        'id' => $tukRequest->delegasi->id,
                        'verifikator_tuk_id' => $tukRequest->delegasi->verifikator_tuk_id,
                        'verifikator_nik' => $tukRequest->delegasi->verifikator_nik,
                        'verifikator_spt_date' => $tukRequest->delegasi->verifikator_spt_date?->format('Y-m-d'),
                        'observer_id' => $tukRequest->delegasi->observer_id,
                        'observer_nik' => $tukRequest->delegasi->observer_nik,
                        'observer_spt_date' => $tukRequest->delegasi->observer_spt_date?->format('Y-m-d'),
                        'asesor_id' => $tukRequest->delegasi->asesor_id,
                        'asesor_met' => $tukRequest->delegasi->asesor_met,
                        'asesor_spt_date' => $tukRequest->delegasi->asesor_spt_date?->format('Y-m-d'),
                        'tanggal_pelaksanaan_asesmen' => $tukRequest->delegasi->tanggal_pelaksanaan_asesmen?->format('Y-m-d'),
                        'waktu_mulai' => $tukRequest->delegasi->waktu_mulai,
                        'jenis_ujian' => $tukRequest->delegasi->jenis_ujian,
                        'notes' => $tukRequest->delegasi->notes,
                    ]
                    : null,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting delegation data: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal memuat data delegasi',
                ],
                500,
            );
        }
    }

    /**
     * Get delegation data for TUK Mandiri
     */
    public function getDelegasiDataApl01($apl01Id)
    {
        try {
            $apl01 = Apl01Pendaftaran::with(['user', 'certificationScheme', 'delegasi'])->findOrFail($apl01Id);

            $data = [
                'asesi_id' => $apl01->user_id,
                'asesi_nama' => $apl01->nama_lengkap,
                'certification_scheme_id' => $apl01->certification_scheme_id,
                'skema_nama' => $apl01->certificationScheme->nama,
                'apl01_id' => $apl01->id,
                'tuk_request_id' => null,

                // ✅ UNTUK TUK MANDIRI: NULL (akan diisi manual oleh admin)
                'tanggal_assessment' => null,
                'jam_mulai' => null,
            ];

            // If delegation exists, include it
            if ($apl01->delegasi) {
                $data['existing_delegation'] = [
                    'id' => $apl01->delegasi->id,
                    'verifikator_tuk_id' => $apl01->delegasi->verifikator_tuk_id,
                    'verifikator_nik' => $apl01->delegasi->verifikator_nik,
                    'verifikator_spt_date' => $apl01->delegasi->verifikator_spt_date?->format('Y-m-d'),
                    'observer_id' => $apl01->delegasi->observer_id,
                    'observer_nik' => $apl01->delegasi->observer_nik,
                    'observer_spt_date' => $apl01->delegasi->observer_spt_date?->format('Y-m-d'),
                    'asesor_id' => $apl01->delegasi->asesor_id,
                    'asesor_met' => $apl01->delegasi->asesor_met,
                    'asesor_spt_date' => $apl01->delegasi->asesor_spt_date?->format('Y-m-d'),
                    'tanggal_pelaksanaan_asesmen' => $apl01->delegasi->tanggal_pelaksanaan_asesmen?->format('Y-m-d'),
                    'waktu_mulai' => $apl01->delegasi->waktu_mulai,
                    'jenis_ujian' => $apl01->delegasi->jenis_ujian,
                    'notes' => $apl01->delegasi->notes,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting APL01 delegation data: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal memuat data delegasi',
                ],
                500,
            );
        }
    }

    /**
     * Get statistics for dashboard
     */
    private function getStats()
    {
        try {
            $sewaktuQuery = TukRequest::whereHas('apl01', function ($q) {
                $q->where('tuk', 'Sewaktu');
            })
                ->whereNotNull('tanggal_assessment')
                ->whereNotNull('lokasi_assessment')
                ->whereNotNull('tanda_tangan_peserta_path');

            $sewaktuPending = (clone $sewaktuQuery)->whereNull('recommended_at')->count();
            $sewaktuRecommended = (clone $sewaktuQuery)->whereNotNull('recommended_at')->count();
            $sewaktuUpcoming = (clone $sewaktuQuery)->where('tanggal_assessment', '>=', now())->count();
            $sewaktuTotal = (clone $sewaktuQuery)->count();

            $mandiriQuery = Apl01Pendaftaran::where('tuk', 'mandiri')->where('status', 'approved');
            $mandiriTotal = (clone $mandiriQuery)->count();

            return [
                'sewaktu' => [
                    'pending' => $sewaktuPending,
                    'recommended' => $sewaktuRecommended,
                    'upcoming' => $sewaktuUpcoming,
                    'total' => $sewaktuTotal,
                ],
                'mandiri' => [
                    'total' => $mandiriTotal,
                ],
                'grand_total' => $sewaktuTotal + $mandiriTotal,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getStats: ' . $e->getMessage());
            return [
                'sewaktu' => ['pending' => 0, 'recommended' => 0, 'upcoming' => 0, 'total' => 0],
                'mandiri' => ['total' => 0],
                'grand_total' => 0,
            ];
        }
    }

    /**
     * Show TUK request detail for recommendation
     */
    public function show(TukRequest $tukRequest)
    {
        $tukRequest->load(['apl01.user', 'apl01.certificationScheme', 'recommendedBy']);
        return view('admin.tuk-requests.show', compact('tukRequest'));
    }

    /**
     * Create or Update recommendation for TUK request
     */
    public function recommend(Request $request, TukRequest $tukRequest)
    {
        if ($tukRequest->apl01->tuk !== 'Sewaktu') {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Rekomendasi hanya untuk TUK Sewaktu',
                ],
                400,
            );
        }

        $validated = $request->validate([
            'tanggal_assessment' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'catatan_rekomendasi' => 'required|in:direkomendasikan,tidak_direkomendasikan',
        ]);

        try {
            DB::beginTransaction();

            $isUpdate = $tukRequest->recommended_at && $tukRequest->recommended_by;

            $updateData = [
                'tanggal_assessment' => $validated['tanggal_assessment'],
                'jam_mulai' => $validated['jam_mulai'],
                'catatan_rekomendasi' => $validated['catatan_rekomendasi'],
                'recommended_at' => now(),
            ];

            if (!$isUpdate) {
                $updateData['recommended_by'] = Auth::id();
            }

            $tukRequest->update($updateData);

            DB::commit();

            $message = $isUpdate ? 'Rekomendasi TUK berhasil diupdate!' : 'Rekomendasi TUK berhasil dibuat!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'kode_tuk' => $tukRequest->kode_tuk,
                    'recommended_at' => $tukRequest->fresh()->recommended_at->format('d F Y H:i'),
                    'is_update' => $isUpdate,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing TUK recommendation: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal memproses rekomendasi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * View TUK Mandiri document
     */
    public function viewTukMandiri($apl01Id)
    {
        $apl01 = Apl01Pendaftaran::findOrFail($apl01Id);

        if (strtolower($apl01->tuk) !== 'mandiri') {
            return redirect()->back()->with('error', 'APL ini bukan TUK Mandiri');
        }

        $apl01->load(['user', 'certificationScheme']);
        return view('admin.tuk-requests.view-tuk-mandiri', compact('apl01'));
    }

    /**
     * Store Rekomendasi LSP with signature
     */
    public function storeRekomendasi(Request $request, $apl01Id)
    {
        $validated = $request->validate([
            'rekomendasi_text' => 'nullable|string|max:1000',
            'signature_data' => 'required|string', // base64 signature
        ]);

        try {
            DB::beginTransaction();

            $apl01 = Apl01Pendaftaran::findOrFail($apl01Id);
            $admin = Auth::user();

            // Cek apakah sudah ada rekomendasi
            $rekomendasi = $apl01->rekomendasiLsp;

            if (!$rekomendasi) {
                $rekomendasi = new RekomendasiLSP();
                $rekomendasi->apl01_id = $apl01Id;
            }

            $rekomendasi->admin_id = $admin->id;
            $rekomendasi->admin_nik = $admin->id_number;
            $rekomendasi->rekomendasi_text = $validated['rekomendasi_text'] ?? null;
            $rekomendasi->tanggal_ttd_admin = now();

            // Save signature image
            $signatureData = $validated['signature_data'];

            // Remove data:image/png;base64, prefix if exists
            if (strpos($signatureData, 'data:image') === 0) {
                $signatureData = explode(',', $signatureData)[1];
            }

            $imageData = base64_decode($signatureData);

            // Create folder structure: rekomendasi-lsp/admin/{admin_name}
            $adminName = Str::slug($admin->name);
            $folderPath = "rekomendasi-lsp/admin/{$adminName}";

            // Generate unique filename
            $filename = 'rekomendasi_' . $apl01Id . '_' . time() . '.png';
            $fullPath = "{$folderPath}/{$filename}";

            // Save to storage
            Storage::disk('public')->put($fullPath, $imageData);

            $rekomendasi->ttd_admin_path = $fullPath;
            $rekomendasi->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekomendasi LSP berhasil disimpan',
                'data' => [
                    'id' => $rekomendasi->id,
                    'tanggal_ttd' => $rekomendasi->formatted_tanggal_ttd,
                    'admin_nama' => $admin->name,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing rekomendasi LSP: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal menyimpan rekomendasi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getTTDRekomendasiForm($apl01Id)
    {
        try {
            $apl01 = Apl01Pendaftaran::with(['rekomendasiLsp.admin'])->findOrFail($apl01Id);

            // Cek apakah APL sudah approved
            $apl01Approved = $apl01->status === 'approved';
            $apl02Approved = !$apl01->apl02 || $apl01->apl02->status === 'approved';
            $bothApproved = $apl01Approved && $apl02Approved;

            if (!$bothApproved) {
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'Dokumen APL belum disetujui',
                    ],
                    400,
                );
            }

            return view('admin.tuk-requests.ttd-rekomendasi-combined', compact('apl01'));
        } catch (\Exception $e) {
            Log::error('Error loading TTD Rekomendasi form: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal memuat form',
                ],
                500,
            );
        }
    }

    /**
     * Get TTD Rekomendasi Combined Form (with APL01 & APL02 tabs)
     */
    public function getTTDRekomendasiCombined($apl01Id)
    {
        try {
            $apl01 = Apl01Pendaftaran::with(['user:id,name,email', 'user.documents', 'certificationScheme:id,code_1,nama,jenjang', 'reviewer:id,name', 'rekomendasiLsp.admin', 'apl02.certificationScheme.units.elemenKompetensis.kriteriaKerjas', 'apl02.certificationScheme.portfolioFiles', 'apl02.elementAssessments.elemenKompetensi.unitKompetensi', 'apl02.evidenceSubmissions.portfolioFile'])->findOrFail($apl01Id);

            if ($apl01->selected_requirement_template_id) {
                $apl01->load(['selectedRequirementTemplate.activeItems']);
            }

            $apl01Approved = $apl01->status === 'approved';
            $apl02Approved = !$apl01->apl02 || $apl01->apl02->status === 'approved';
            $bothApproved = $apl01Approved && $apl02Approved;

            if (!$bothApproved) {
                return response()->make("<div class='alert alert-warning'><i class='bi bi-exclamation-triangle me-2'></i><strong>Dokumen APL belum disetujui.</strong></div>", 200);
            }

            $hasRekomendasi = $apl01->rekomendasiLsp !== null;

            return response()->make(view('admin.tuk-requests.ttd-rekomendasi-combined', compact('apl01', 'hasRekomendasi'))->render());
        } catch (\Exception $e) {
            Log::error('Error in getTTDRekomendasiCombined: ' . $e->getMessage());
            return response()->make("<div class='alert alert-danger'><i class='bi bi-exclamation-triangle me-2'></i><strong>Error:</strong> {$e->getMessage()}</div>", 500);
        }
    }

    public function getCombinedReviewDetail($apl01Id)
    {
        try {
            $apl01 = Apl01Pendaftaran::with([
                'user:id,name,email',
                'user.documents',
                'certificationScheme:id,code_1,nama,jenjang',
                'reviewer:id,name',
                'rekomendasiLsp.admin',
                'apl02' => function ($q) {
                    $q->with(['reviewer:id,name', 'certificationScheme.units.elemenKompetensis.kriteriaKerjas', 'certificationScheme.portfolioFiles', 'elementAssessments.elemenKompetensi.unitKompetensi', 'evidenceSubmissions.portfolioFile']);
                },
            ])->findOrFail($apl01Id);
            // Load template
            if ($apl01->selected_requirement_template_id) {
                $apl01->load([
                    'selectedRequirementTemplate' => function ($query) {
                        $query->with([
                            'activeItems' => function ($q) {
                                $q->where('is_active', true)->orderBy('sort_order', 'asc');
                            },
                        ]);
                    },
                ]);
            }

            $completionStatus = [
                'apl01' => [
                    'completed' => !is_null($apl01->completed_at) && $apl01->status === 'approved',
                    'completed_at' => $apl01->completed_at,
                    'status' => $apl01->status,
                ],
                'apl02' => null,
                'rekomendasi' => [
                    'exists' => $apl01->hasRekomendasi(),
                    'data' => $apl01->rekomendasiLsp,
                ],
            ];

            if ($apl01->apl02) {
                $completionStatus['apl02'] = [
                    'completed' => !is_null($apl01->apl02->completed_at) && $apl01->apl02->status === 'approved',
                    'completed_at' => $apl01->apl02->completed_at,
                    'status' => $apl01->apl02->status,
                ];
            }

            return view('admin.tuk-requests.combined-review-detail', compact('apl01', 'completionStatus'));
        } catch (\Exception $e) {
            Log::error('Combined Review Detail Error: ' . $e->getMessage(), [
                'apl01_id' => $apl01Id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->make(
                "<div style='padding:20px; background:#ffecec; color:#d8000c; border:1px solid #d8000c; border-radius:6px; font-family:sans-serif'>
                <strong>Gagal memuat detail:</strong><br>
                {$e->getMessage()}
            </div>",
                500,
            );
        }
    }

    public function reschedule(Request $request, TukRequest $tukRequest)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        try {
            DB::beginTransaction();

            $kode_tuk = $tukRequest->kode_tuk;
            $apl01_id = $tukRequest->apl01_id;
            $adminId = Auth::id();

            // Store MAPA info before deletion
            $mapaInfo = null;

            // 0. Delete SPT (if exists) - HARUS DIHAPUS SEBELUM DELEGASI
            if ($tukRequest->delegasi && $tukRequest->delegasi->sptSignature) {
                $spt = $tukRequest->delegasi->sptSignature;

                // Delete SPT files from storage
                if ($spt->spt_verifikator_file && Storage::exists($spt->spt_verifikator_file)) {
                    Storage::delete($spt->spt_verifikator_file);
                    Log::info("Reschedule - Deleted SPT Verifikator file: {$spt->spt_verifikator_file}");
                }

                if ($spt->spt_observer_file && Storage::exists($spt->spt_observer_file)) {
                    Storage::delete($spt->spt_observer_file);
                    Log::info("Reschedule - Deleted SPT Observer file: {$spt->spt_observer_file}");
                }

                if ($spt->spt_asesor_file && Storage::exists($spt->spt_asesor_file)) {
                    Storage::delete($spt->spt_asesor_file);
                    Log::info("Reschedule - Deleted SPT Asesor file: {$spt->spt_asesor_file}");
                }

                // Delete signature image if exists (bukan default signature)
                if ($spt->signature_image && $spt->signature_image !== 'assets/signatures/direktur_signature.png' && Storage::disk('public')->exists($spt->signature_image)) {
                    Storage::disk('public')->delete($spt->signature_image);
                    Log::info("Reschedule - Deleted SPT signature: {$spt->signature_image}");
                }

                // Delete SPT record
                $spt->delete();
                Log::info("Reschedule - Deleted SPT for TukRequest: {$kode_tuk}");
            }

            // 1. Delete MAPA (if exists)
            if ($tukRequest->delegasi) {
                $mapa = \App\Models\Mapa::where('delegasi_personil_asesmen_id', $tukRequest->delegasi->id)->first();

                if ($mapa) {
                    $mapaInfo = [
                        'nomor_mapa' => $mapa->nomor_mapa,
                        'status' => $mapa->status,
                    ];

                    if ($mapa->signature_image && Storage::disk('public')->exists($mapa->signature_image)) {
                        Storage::disk('public')->delete($mapa->signature_image);
                        Log::info("Reschedule - Deleted MAPA signature: {$mapa->signature_image}");
                    }

                    $mapa->delete();
                    Log::info("Reschedule - Deleted MAPA: {$mapa->nomor_mapa} for TukRequest: {$kode_tuk}");
                }
            }

            // 2. Delete Delegasi Personil (if exists)
            if ($tukRequest->delegasi) {
                $tukRequest->delegasi->delete();
                Log::info("Reschedule - Deleted DelegasiPersonil for TukRequest: {$kode_tuk}");
            }

            // 3. Delete Rekomendasi LSP & signature file (if exists)
            if ($tukRequest->apl01 && $tukRequest->apl01->rekomendasiLsp) {
                $rekomendasiLsp = $tukRequest->apl01->rekomendasiLsp;

                if ($rekomendasiLsp->ttd_admin_path && Storage::disk('public')->exists($rekomendasiLsp->ttd_admin_path)) {
                    Storage::disk('public')->delete($rekomendasiLsp->ttd_admin_path);
                    Log::info("Reschedule - Deleted signature file: {$rekomendasiLsp->ttd_admin_path}");
                }

                $rekomendasiLsp->delete();
                Log::info("Reschedule - Deleted RekomendasiLSP for TukRequest: {$kode_tuk}");
            }

            // 4. Delete TUK signature file (if exists)
            if ($tukRequest->tanda_tangan_peserta_path) {
                if (!str_starts_with($tukRequest->tanda_tangan_peserta_path, 'data:image/')) {
                    if (Storage::disk('public')->exists($tukRequest->tanda_tangan_peserta_path)) {
                        Storage::disk('public')->delete($tukRequest->tanda_tangan_peserta_path);
                        Log::info("Reschedule - Deleted TUK signature file: {$tukRequest->tanda_tangan_peserta_path}");
                    }
                }
            }

            // 5. RESET APL01 approval status
            $oldApl01Status = null;
            if ($tukRequest->apl01) {
                $oldApl01Status = $tukRequest->apl01->status;
                $tukRequest->apl01->update([
                    'status' => 'pending',
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'rejection_reason' => null,
                    'completed_at' => null,
                ]);
                Log::info("Reschedule - Reset APL01 status to pending for TukRequest: {$kode_tuk}");
            }

            // 6. RESET APL02 approval status jika ada
            $oldApl02Status = null;
            if ($tukRequest->apl01 && $tukRequest->apl01->apl02) {
                $oldApl02Status = $tukRequest->apl01->apl02->status;
                $tukRequest->apl01->apl02->update([
                    'status' => 'pending',
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'rejection_reason' => null,
                    'completed_at' => null,
                ]);
                Log::info("Reschedule - Reset APL02 status to pending for TukRequest: {$kode_tuk}");
            }

            // 7. SAVE RESCHEDULE HISTORY TO DATABASE
            $history = TukRescheduleHistory::createForSewaktu($tukRequest, $request->reason, $adminId, $mapaInfo);

            Log::info("Reschedule - Saved history record ID: {$history->id}");

            // 8. DELETE TukRequest completely
            $tukRequest->delete();
            Log::info("Reschedule - DELETED TukRequest: {$kode_tuk}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reschedule berhasil! Semua data terkait (TUK, SPT, Delegasi, Rekomendasi LSP, MAPA) telah dihapus dan status APL direset. Peserta perlu review & approval ulang.',
                'data' => [
                    'history_id' => $history->id,
                    'deleted_kode_tuk' => $kode_tuk,
                    'reschedule_reason' => $request->reason,
                    'rescheduled_at' => now()->format('d F Y H:i'),
                    'deleted_items' => [
                        'tuk_request' => true,
                        'delegasi' => $tukRequest->delegasi !== null,
                        'rekomendasi_lsp' => $tukRequest->apl01?->rekomendasiLsp !== null,
                        'mapa' => $mapaInfo !== null,
                    ],
                    'reset_status' => [
                        'apl01' => $oldApl01Status ? 'pending' : null,
                        'apl02' => $oldApl02Status ? 'pending' : null,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reschedule TUK Sewaktu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal melakukan reschedule: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function rescheduleMandiri(Request $request, Apl01Pendaftaran $apl01)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        try {
            DB::beginTransaction();

            $nomor_apl = $apl01->nomor_apl_01;
            $adminId = Auth::id();

            // Store MAPA info before deletion
            $mapaInfo = null;

            // 0. Delete SPT (if exists) - HARUS DIHAPUS SEBELUM DELEGASI
            if ($apl01->delegasi && $apl01->delegasi->sptSignature) {
                $spt = $apl01->delegasi->sptSignature;

                // Delete SPT files from storage
                if ($spt->spt_verifikator_file && Storage::exists($spt->spt_verifikator_file)) {
                    Storage::delete($spt->spt_verifikator_file);
                    Log::info("Reschedule Mandiri - Deleted SPT Verifikator file: {$spt->spt_verifikator_file}");
                }

                if ($spt->spt_observer_file && Storage::exists($spt->spt_observer_file)) {
                    Storage::delete($spt->spt_observer_file);
                    Log::info("Reschedule Mandiri - Deleted SPT Observer file: {$spt->spt_observer_file}");
                }

                if ($spt->spt_asesor_file && Storage::exists($spt->spt_asesor_file)) {
                    Storage::delete($spt->spt_asesor_file);
                    Log::info("Reschedule Mandiri - Deleted SPT Asesor file: {$spt->spt_asesor_file}");
                }

                // Delete signature image if exists (bukan default signature)
                if ($spt->signature_image && $spt->signature_image !== 'assets/signatures/direktur_signature.png' && Storage::disk('public')->exists($spt->signature_image)) {
                    Storage::disk('public')->delete($spt->signature_image);
                    Log::info("Reschedule Mandiri - Deleted SPT signature: {$spt->signature_image}");
                }

                // Delete SPT record
                $spt->delete();
                Log::info("Reschedule Mandiri - Deleted SPT for APL01: {$nomor_apl}");
            }

            // 1. Delete MAPA (if exists)
            if ($apl01->delegasi) {
                $mapa = \App\Models\Mapa::where('delegasi_personil_asesmen_id', $apl01->delegasi->id)->first();

                if ($mapa) {
                    $mapaInfo = [
                        'nomor_mapa' => $mapa->nomor_mapa,
                        'status' => $mapa->status,
                    ];

                    if ($mapa->signature_image && Storage::disk('public')->exists($mapa->signature_image)) {
                        Storage::disk('public')->delete($mapa->signature_image);
                        Log::info("Reschedule Mandiri - Deleted MAPA signature: {$mapa->signature_image}");
                    }

                    $mapa->delete();
                    Log::info("Reschedule Mandiri - Deleted MAPA: {$mapa->nomor_mapa} for APL01: {$nomor_apl}");
                }
            }

            // 2. Delete Delegasi Personil (if exists)
            if ($apl01->delegasi) {
                $apl01->delegasi->delete();
                Log::info("Reschedule Mandiri - Deleted DelegasiPersonil for APL01: {$nomor_apl}");
            }

            // 3. Delete Rekomendasi LSP & signature file (if exists)
            if ($apl01->rekomendasiLsp) {
                $rekomendasiLsp = $apl01->rekomendasiLsp;

                if ($rekomendasiLsp->ttd_admin_path && Storage::disk('public')->exists($rekomendasiLsp->ttd_admin_path)) {
                    Storage::disk('public')->delete($rekomendasiLsp->ttd_admin_path);
                    Log::info("Reschedule Mandiri - Deleted signature file: {$rekomendasiLsp->ttd_admin_path}");
                }

                $rekomendasiLsp->delete();
                Log::info("Reschedule Mandiri - Deleted RekomendasiLSP for APL01: {$nomor_apl}");
            }

            // 4. RESET APL01 approval status
            $oldApl01Status = $apl01->status;
            $apl01->update([
                'status' => 'pending',
                'reviewed_at' => null,
                'reviewed_by' => null,
                'rejection_reason' => null,
                'completed_at' => null,
            ]);

            // 5. RESET APL02 approval status jika ada
            $oldApl02Status = null;
            if ($apl01->apl02) {
                $oldApl02Status = $apl01->apl02->status;
                $apl01->apl02->update([
                    'status' => 'pending',
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'rejection_reason' => null,
                    'completed_at' => null,
                ]);
            }

            // 6. SAVE RESCHEDULE HISTORY TO DATABASE
            $history = TukRescheduleHistory::createForMandiri($apl01, $request->reason, $adminId, $mapaInfo);

            Log::info("Reschedule Mandiri - Saved history record ID: {$history->id}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reschedule berhasil! Semua data terkait (SPT, Delegasi, Rekomendasi LSP, MAPA) telah dihapus dan status APL direset. Peserta perlu review & approval ulang.',
                'data' => [
                    'history_id' => $history->id,
                    'nomor_apl_01' => $nomor_apl,
                    'reschedule_reason' => $request->reason,
                    'rescheduled_at' => now()->format('d F Y H:i'),
                    'deleted_items' => [
                        'delegasi' => $apl01->delegasi !== null,
                        'rekomendasi_lsp' => $apl01->rekomendasiLsp !== null,
                        'mapa' => $mapaInfo !== null,
                    ],
                    'reset_status' => [
                        'apl01' => 'pending',
                        'apl02' => $apl01->apl02 ? 'pending' : null,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reschedule TUK Mandiri', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal melakukan reschedule: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function rescheduleMonitoring(Request $request)
    {
        try {
            // Calculate stats
            $stats = [
                'total' => TukRescheduleHistory::count(),
                'sewaktu' => TukRescheduleHistory::where('tuk_type', 'sewaktu')->count(),
                'mandiri' => TukRescheduleHistory::where('tuk_type', 'mandiri')->count(),
                'this_month' => TukRescheduleHistory::whereYear('rescheduled_at', now()->year)->whereMonth('rescheduled_at', now()->month)->count(),
            ];

            return view('admin.tuk-requests.reschedule-monitoring', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error in rescheduleMonitoring: ' . $e->getMessage());

            return back()->with('error', 'Gagal memuat halaman reschedule: ' . $e->getMessage());
        }
    }

    /**
     * Get DataTables data for reschedule history (server-side)
     */
    public function getRescheduleData(Request $request)
    {
        try {
            $query = TukRescheduleHistory::with(['apl01.user', 'apl01.certificationScheme', 'rescheduledBy'])->orderBy('rescheduled_at', 'desc');

            // Filter by TUK type
            if ($request->filled('type') && in_array($request->type, ['sewaktu', 'mandiri'])) {
                $query->where('tuk_type', $request->type);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('rescheduled_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('rescheduled_at', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addColumn('tuk_type_badge', function ($row) {
                    if ($row->tuk_type === 'sewaktu') {
                        return '<span class="badge bg-warning text-dark">Sewaktu</span>';
                    }
                    return '<span class="badge bg-info">Mandiri</span>';
                })
                ->addColumn('kode_nomor', function ($row) {
                    $html = '';
                    if ($row->kode_tuk) {
                        $html .= '<strong class="text-primary d-block">' . e($row->kode_tuk) . '</strong>';
                        $html .= '<small class="text-muted">' . e($row->apl01->nomor_apl_01) . '</small>';
                    } else {
                        $html .= '<strong class="text-info">' . e($row->apl01->nomor_apl_01) . '</strong>';
                    }
                    return $html;
                })
                ->addColumn('peserta_info', function ($row) {
                    $nama = $row->apl01->nama_lengkap ?? 'N/A';
                    $email = $row->apl01->user->email ?? 'N/A';

                    $html = '<div>';
                    $html .= '<strong class="d-block">' . e($nama) . '</strong>';
                    $html .= '<small class="text-muted">' . e($email) . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('skema_sertifikasi', function ($row) {
                    $skema = $row->apl01->certificationScheme->nama ?? 'N/A';
                    return '<small>' . e($skema) . '</small>';
                })
                ->addColumn('waktu_reschedule', function ($row) {
                    $html = '<div class="d-flex align-items-center">';
                    $html .= '<i class="bi bi-calendar3 me-2 text-muted"></i>';
                    $html .= '<div>';
                    $html .= '<div>' . $row->old_tanggal_assessment->format('d M Y') . '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('admin_info', function ($row) {
                    $html = '<div>';
                    $html .= '<strong class="d-block">' . e($row->rescheduledBy->name) . '</strong>';
                    $html .= '<small class="text-muted">' . e($row->rescheduledBy->email) . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('actions', function ($row) {
                    $html = '<div class="btn-group" role="group">';

                    // View APL01 button
                    $html .= '<a href="' . route('admin.apl01.show', $row->apl01_id) . '" ';
                    $html .= 'class="btn btn-sm btn-outline-primary" ';
                    $html .= 'title="Lihat APL-01">';
                    $html .= '<i class="bi bi-eye"></i>';
                    $html .= '</a>';

                    // View detail button
                    $html .= '<button type="button" class="btn btn-sm btn-outline-info" ';
                    $html .= 'onclick="viewRescheduleDetail(' . $row->id . ')" ';
                    $html .= 'title="Detail Reschedule">';
                    $html .= '<i class="bi bi-info-circle"></i>';
                    $html .= '</button>';

                    $html .= '</div>';
                    return $html;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && isset($request->search['value']) && $request->search['value']) {
                        $search = $request->search['value'];
                        $query->where(function ($q) use ($search) {
                            $q->where('kode_tuk', 'like', "%{$search}%")
                                ->orWhereHas('apl01', function ($subQ) use ($search) {
                                    $subQ->where('nomor_apl_01', 'like', "%{$search}%")->orWhere('nama_lengkap', 'like', "%{$search}%");
                                })
                                ->orWhereHas('rescheduledBy', function ($subQ) use ($search) {
                                    $subQ->where('name', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->rawColumns(['tuk_type_badge', 'kode_nomor', 'peserta_info', 'skema_sertifikasi', 'waktu_reschedule', 'admin_info', 'data_dihapus', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in getRescheduleData: ' . $e->getMessage());
            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading reschedule data: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get reschedule detail for modal (AJAX)
     */
    public function getRescheduleDetail($historyId)
    {
        try {
            $history = TukRescheduleHistory::with(['apl01', 'rescheduledBy'])->findOrFail($historyId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $history->id,
                    'tuk_type' => $history->tuk_type,
                    'tuk_type_text' => $history->tuk_type_text,
                    'kode_tuk' => $history->kode_tuk,
                    'nomor_apl' => $history->apl01->nomor_apl_01,
                    'peserta_nama' => $history->apl01->nama_lengkap,
                    'reschedule_reason' => $history->reschedule_reason,
                    'rescheduled_at' => $history->rescheduled_at,
                    'formatted_rescheduled_at' => $history->formatted_rescheduled_at,
                    'admin_name' => $history->rescheduledBy->name,
                    'admin_email' => $history->rescheduledBy->email,

                    // Deleted data flags
                    'had_delegation' => $history->had_delegation,
                    'had_mapa' => $history->had_mapa,
                    'had_recommendation' => $history->had_recommendation,
                    'had_signature' => $history->had_signature,
                    'mapa_nomor' => $history->mapa_nomor,

                    // Old assessment data (for TUK Sewaktu)
                    'old_tanggal_assessment' => $history->old_tanggal_assessment?->format('d F Y'),
                    'old_lokasi_assessment' => $history->old_lokasi_assessment,

                    // Status before reschedule
                    'apl01_status_before' => $history->apl01_status_before,
                    'apl02_status_before' => $history->apl02_status_before,

                    // Impact summary
                    'impact_summary' => $history->getImpactSummary(),
                    'deleted_summary' => $history->deleted_data_summary,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal memuat detail: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function approveCombined(Request $request, $apl01Id)
    {
        try {
            DB::beginTransaction();

            $apl01 = Apl01Pendaftaran::with(['apl02'])->findOrFail($apl01Id);
            $now = now();

            $results = [
                'apl01' => null,
                'apl02' => null,
            ];

            // ====================================
            // 1. ISI completed_at APL01
            // ====================================
            Log::info('Filling completed_at for APL01', [
                'apl01_id' => $apl01Id,
                'current_completed_at' => $apl01->completed_at,
            ]);

            if ($apl01->completed_at) {
                $results['apl01'] = [
                    'status' => 'already_completed',
                    'message' => 'APL01 sudah completed',
                    'completed_at' => $apl01->completed_at->format('d F Y H:i:s'),
                ];
            } else {
                // HANYA ISI completed_at, TIDAK UBAH FIELD LAIN
                $apl01->completed_at = $now;
                $apl01->save();

                Log::info('APL01 completed_at filled', [
                    'apl01_id' => $apl01Id,
                    'completed_at' => $apl01->completed_at,
                ]);

                $results['apl01'] = [
                    'status' => 'completed',
                    'message' => 'APL01 completed_at berhasil diisi',
                    'completed_at' => $apl01->completed_at->format('d F Y H:i:s'),
                ];
            }

            // ====================================
            // 2. ISI completed_at APL02 (jika ada)
            // ====================================
            if ($apl01->apl02) {
                Log::info('Filling completed_at for APL02', [
                    'apl02_id' => $apl01->apl02->id,
                    'current_completed_at' => $apl01->apl02->completed_at,
                ]);

                if ($apl01->apl02->completed_at) {
                    $results['apl02'] = [
                        'status' => 'already_completed',
                        'message' => 'APL02 sudah completed',
                        'completed_at' => $apl01->apl02->completed_at->format('d F Y H:i:s'),
                    ];
                } else {
                    // HANYA ISI completed_at, TIDAK UBAH FIELD LAIN
                    $apl02 = $apl01->apl02;
                    $apl02->completed_at = $now;
                    $apl02->save();

                    Log::info('APL02 completed_at filled', [
                        'apl02_id' => $apl02->id,
                        'completed_at' => $apl02->completed_at,
                    ]);

                    $results['apl02'] = [
                        'status' => 'completed',
                        'message' => 'APL02 completed_at berhasil diisi',
                        'completed_at' => $apl02->completed_at->format('d F Y H:i:s'),
                    ];
                }
            } else {
                $results['apl02'] = [
                    'status' => 'not_exist',
                    'message' => 'APL02 belum dibuat',
                ];
            }

            DB::commit();

            // ====================================
            // 3. BUILD RESPONSE
            // ====================================
            $messages = [];

            if ($results['apl01']['status'] === 'completed') {
                $messages[] = '✅ APL01 completed_at telah diisi';
            } elseif ($results['apl01']['status'] === 'already_completed') {
                $messages[] = 'ℹ️ APL01 sudah completed sebelumnya';
            }

            if ($results['apl02']['status'] === 'completed') {
                $messages[] = '✅ APL02 completed_at telah diisi';
            } elseif ($results['apl02']['status'] === 'already_completed') {
                $messages[] = 'ℹ️ APL02 sudah completed sebelumnya';
            } elseif ($results['apl02']['status'] === 'not_exist') {
                $messages[] = 'ℹ️ APL02 belum dibuat';
            }

            $message = implode('<br>', $messages);

            return response()->json([
                'success' => true,
                'message' => $message,
                'completed_at' => $now->format('d F Y, H:i:s'),
                'data' => [
                    'apl01' => [
                        'id' => $apl01->id,
                        'completed_at' => $apl01->completed_at ? $apl01->completed_at->toDateTimeString() : null,
                        'result' => $results['apl01'],
                    ],
                    'apl02' => $apl01->apl02
                        ? [
                            'id' => $apl01->apl02->id,
                            'completed_at' => $apl01->apl02->completed_at ? $apl01->apl02->completed_at->toDateTimeString() : null,
                            'result' => $results['apl02'],
                        ]
                        : null,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to fill completed_at', [
                'apl01_id' => $apl01Id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mengisi completed_at: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Combined Rejection - Reset completed_at ke NULL
     */
    public function rejectCombined(Request $request, $apl01Id)
    {
        $request->validate(
            [
                'notes' => 'required|string|min:10',
            ],
            [
                'notes.required' => 'Alasan penolakan harus diisi',
                'notes.min' => 'Alasan penolakan minimal 10 karakter',
            ],
        );

        try {
            DB::beginTransaction();

            $apl01 = Apl01Pendaftaran::with(['apl02'])->findOrFail($apl01Id);
            $rejectionNotes = $request->input('notes') ?? $request->input('rejection_notes');

            $results = [];

            // ====================================
            // 1. RESET completed_at APL01
            // ====================================
            Log::info('Resetting completed_at for APL01', [
                'apl01_id' => $apl01Id,
                'notes' => $rejectionNotes,
            ]);

            // HANYA RESET completed_at, TIDAK UBAH FIELD LAIN
            $apl01->completed_at = null;
            $apl01->save();

            $results['apl01'] = [
                'rejected' => true,
                'message' => 'APL01 completed_at direset',
            ];

            Log::info('APL01 completed_at reset', ['apl01_id' => $apl01Id]);

            // ====================================
            // 2. RESET completed_at APL02 (jika ada)
            // ====================================
            if ($apl01->apl02) {
                Log::info('Resetting completed_at for APL02', [
                    'apl02_id' => $apl01->apl02->id,
                    'notes' => $rejectionNotes,
                ]);

                // HANYA RESET completed_at, TIDAK UBAH FIELD LAIN
                $apl02 = $apl01->apl02;
                $apl02->completed_at = null;
                $apl02->save();

                $results['apl02'] = [
                    'rejected' => true,
                    'message' => 'APL02 completed_at direset',
                ];

                Log::info('APL02 completed_at reset', ['apl02_id' => $apl02->id]);
            }

            DB::commit();

            // Build message
            $messages = [];
            if ($results['apl01']['rejected']) {
                $messages[] = '❌ APL01 completed_at direset';
            }
            if (isset($results['apl02']) && $results['apl02']['rejected']) {
                $messages[] = '❌ APL02 completed_at direset';
            }

            $message = implode('<br>', $messages);
            $message .= '<br><br><strong>Alasan:</strong> ' . $rejectionNotes;

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'apl01' => [
                        'id' => $apl01->id,
                        'completed_at' => null,
                        'rejected' => true,
                    ],
                    'apl02' => $apl01->apl02
                        ? [
                            'id' => $apl01->apl02->id,
                            'completed_at' => null,
                            'rejected' => true,
                        ]
                        : null,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to reset completed_at', [
                'apl01_id' => $apl01Id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal mereset completed_at: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
