@extends('layouts.admin')

@section('title', 'Manajemen TUK - Admin')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="main-card">
            <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-geo-alt-fill me-2"></i>Manajemen TUK (Tempat Uji Kompetensi)
                    </h4>
                    <p class="text-black mb-0">Kelola TUK Sewaktu dan TUK Mandiri dalam satu dashboard</p>
                </div>
                <div>
                    <button class="btn btn-outline-light" onclick="refreshAllTables()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh All
                    </button>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="row m-2">
                <div class="col-md-12">
                    <ul class="nav nav-pills custom-tabs nav-justified mb-4 gap-3" id="tukTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sewaktu-tab" data-bs-toggle="pill" data-bs-target="#sewaktu"
                                type="button" role="tab">
                                <i class="bi bi-clock me-2"></i>TUK Sewaktu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="mandiri-tab" data-bs-toggle="pill" data-bs-target="#mandiri"
                                type="button" role="tab">
                                <i class="bi bi-building me-2"></i>TUK Mandiri
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content m-3" id="tukTabsContent">
                <!-- TUK Sewaktu Tab -->
                <div class="tab-pane fade show active" id="sewaktu" role="tabpanel">
                    <!-- Filters for Sewaktu - Note Style -->
                    <div class="alert alert-light border-2 mb-4" style="background-color: #f0f7ff;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-calendar-event text-primary mt-1" style="font-size: 1.5rem;"></i>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-3">
                                    <i class="bi bi-funnel me-2"></i>Tanggal Asesmen
                                </h6>

                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Dari Tanggal</label>
                                        <input type="date" class="form-control form-control-sm" id="sewaktuDateFrom">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Sampai Tanggal</label>
                                        <input type="date" class="form-control form-control-sm" id="sewaktuDateTo">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Filter Status</label>
                                        <select class="form-select form-select-sm" id="sewaktuFilter">
                                            <option value="">Semua Status</option>
                                            <option value="pending">Pending Review</option>
                                            <option value="recommended">Sudah Direkomendasi</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Cari</label>
                                        <input type="text" class="form-control form-control-sm" id="sewaktuSearch"
                                            placeholder="Nama peserta, nomor APL, kode TUK...">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="d-flex gap-2 w-100">
                                            <button type="button" class="btn btn-primary btn-sm flex-fill"
                                                onclick="refreshSewaktuTable()">
                                                <i class="bi bi-search me-1"></i>Filter
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill"
                                                onclick="resetSewaktuFilter()">
                                                <i class="bi bi-x-circle me-1"></i>Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sewaktu Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-clock me-2"></i>TUK Sewaktu - Perlu Rekomendasi Admin
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="sewaktuTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Kode TUK</th>
                                            <th>Peserta</th>
                                            <th>Skema Sertifikasi</th>
                                            <th>Tanggal Assessment</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TUK Mandiri Tab -->
                <div class="tab-pane fade" id="mandiri" role="tabpanel">
                    <!-- Filters for Mandiri - Note Style -->
                    <div class="alert alert-light border-2 mb-4" style="background-color: #f0fdf4;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-calendar-event text-success mt-1" style="font-size: 1.5rem;"></i>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-3">
                                    <i class="bi bi-funnel me-2"></i>Tanggal dibuat APL01
                                </h6>

                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Dari Tanggal</label>
                                        <input type="date" class="form-control form-control-sm" id="mandiriDateFrom">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Sampai Tanggal</label>
                                        <input type="date" class="form-control form-control-sm" id="mandiriDateTo">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Cari</label>
                                        <input type="text" class="form-control form-control-sm" id="mandiriSearch"
                                            placeholder="Nama peserta, nomor APL, perusahaan, NIK...">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="d-flex flex-column gap-2 w-100">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="refreshMandiriTable()">
                                                <i class="bi bi-search me-1"></i>Filter
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                onclick="resetMandiriFilter()">
                                                <i class="bi bi-x-circle me-1"></i>Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mandiri Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-building me-2"></i>TUK Mandiri - Proses Delegasi
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="mandiriTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nomor APL</th>
                                            <th>Peserta</th>
                                            <th>Skema Sertifikasi</th>
                                            <th>Perusahaan/Jabatan</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rekomendasi TUK -->
    <div class="modal fade" id="recommendModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2"></i>Rekomendasi TUK Sewaktu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="recommendModalBody"></div>
            </div>
        </div>
    </div>

    <!-- Modal View APL01 Detail -->
    <div class="modal fade" id="viewApl01Modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-eye me-2"></i>Detail APL01 - TUK Mandiri
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewApl01ModalBody"></div>
            </div>
        </div>
    </div>

    <!-- Modal Review APL -->
    <div class="modal fade" id="reviewAplModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewAplModalTitle">
                        <i class="bi bi-file-earmark-check me-2"></i>Review Dokumen
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reviewAplModalBody"></div>
                <div class="modal-footer border-top">
                    <div class="w-100">
                        <div class="mb-3" id="approvalNotesSection" style="display: none;">
                            <label class="form-label fw-semibold">Catatan Persetujuan (Opsional)</label>
                            <textarea class="form-control" id="approvalNotes" rows="2"></textarea>
                        </div>
                        <div class="mb-3" id="rejectionNotesSection" style="display: none;">
                            <div class="alert alert-warning mb-2">
                                <i class="bi bi-info-circle me-2"></i>Dokumen akan dibuka kembali untuk diperbaiki
                            </div>
                            <label class="form-label fw-semibold">Alasan Penolakan <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejectionNotes" rows="3" required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Tutup
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger" id="btnReject" onclick="handleReject()">
                                    <i class="bi bi-x-circle me-1"></i>Reject
                                </button>
                                <button type="button" class="btn btn-success" id="btnApprove"
                                    onclick="handleApprove()">
                                    <i class="bi bi-check-circle me-1"></i>Approve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal TUK Mandiri PDF -->
    <div class="modal fade" id="viewTukMandiriModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Dokumen TUK Mandiri
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="tukMandiriModalBody"></div>
            </div>
        </div>
    </div>

    <!-- Modal Reschedule -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-x me-2"></i>Reschedule Asemen
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rescheduleForm">
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong> Data delegasi dan rekomendasi akan dihapus.
                        </div>
                        <input type="hidden" id="reschedule_tuk_request_id">
                        <input type="hidden" id="reschedule_apl01_id">
                        <input type="hidden" id="reschedule_delegasi_id">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alasan Reschedule <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="reschedule_reason" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.tuk-requests.delegasi-detail')
@endsection

@push('styles')
    <style>
        /* ============================================
                                                   RESPONSIVE TABLE STYLES
                                                   ============================================ */

        .custom-tabs .nav-link {
            border-radius: 50px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .custom-tabs .nav-link.active {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: #fff;
        }

        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .table-responsive {
            border: none;
            padding: 1rem;
            overflow-x: auto;
        }

        /* Actions Cell - Horizontal Button Layout */
        .table td:last-child {
            min-width: 450px;
            padding: 0.75rem !important;
        }

        /* Button Group Horizontal */
        .btn-group-horizontal {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-group-horizontal .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        #reviewAplModal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1200px) {
            .table td:last-child {
                min-width: 400px;
            }

            .btn-group-horizontal .btn {
                padding: 0.375rem 0.6rem;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 992px) {
            .table td:last-child {
                min-width: 350px;
            }

            .btn-group-horizontal {
                gap: 0.25rem;
            }

            .btn-group-horizontal .btn {
                padding: 0.35rem 0.5rem;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 768px) {
            .table-responsive {
                padding: 0.75rem;
            }

            .table td:last-child {
                min-width: auto;
                white-space: normal;
            }

            .btn-group-horizontal {
                flex-direction: column;
                width: 100%;
                gap: 0.25rem;
            }

            .btn-group-horizontal .btn {
                width: 100%;
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }
        }

        /* Badge Styling */
        .badge {
            padding: 0.3rem 0.6rem;
            font-size: 0.7rem;
            white-space: nowrap;
        }

        /* Smooth Transitions */
        .btn:hover {
            transform: none;
            opacity: 0.9;
        }

        .review-tabs-wrapper {
            width: 100%;
            margin: 0 auto;
        }

        .review-tabs {
            display: flex;
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            border: none;
            padding: 0;
        }

        .review-tabs .nav-item {
            flex: 1;
        }

        .review-tabs .nav-link {
            border: none;
            border-radius: 0;
            width: 100%;
            text-align: center;
            padding: 0.9rem 0;
            color: #0066ff;
            background: #e9ecef;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        /* radius kiri hanya untuk tombol pertama */
        .review-tabs .nav-item:first-child .nav-link {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        /* radius kanan hanya untuk tombol terakhir */
        .review-tabs .nav-item:last-child .nav-link {
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        /* tombol aktif */
        .review-tabs .nav-link.active {
            background: linear-gradient(90deg, #0066ff, #0048d0);
            color: #fff !important;
        }

    </style>
@endpush

@push('scripts')
    <script>
        // ============================================
        // GLOBAL VARIABLES
        // ============================================
        let sewaktuTable, mandiriTable;
        let verifikatorList = [],
            observerList = [],
            asesorList = [];
        let currentReviewType = null,
            currentReviewId = null;
        let rekomendasiSignaturePad;
    </script>

    {{-- Load separated scripts --}}
    <script src="{{ asset('js/admin/tuk/tuk-datatable.js') }}"></script>
    <script src="{{ asset('js/admin/tuk/tuk-review.js') }}"></script>
    <script src="{{ asset('js/admin/tuk/tuk-delegasi.js') }}"></script>
    <script src="{{ asset('js/admin/tuk/tuk-rekomendasi.js') }}"></script>
    <script src="{{ asset('js/admin/tuk/tuk-utility.js') }}"></script>

    <script>
        // ============================================
        // MAIN INITIALIZATION
        // ============================================
        $(document).ready(function() {
            initializeSewaktuTable();
            initializeMandiriTable();
            $('[data-bs-toggle="tooltip"]').tooltip();
            loadUsersByRole();
            setupDelegasiFormHandlers();
            setupRescheduleHandler();

            // Tab switching
            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                const targetTab = e.target.getAttribute('data-bs-target');
                if (targetTab === '#sewaktu' && sewaktuTable) {
                    sewaktuTable.ajax.reload();
                } else if (targetTab === '#mandiri' && mandiriTable) {
                    mandiriTable.ajax.reload();
                }
            });

            $('#sewaktuSearch').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    refreshSewaktuTable();
                }
            });

            $('#mandiriSearch').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    refreshMandiriTable();
                }
            });

            // Modal reset
            $('#reviewAplModal').on('hidden.bs.modal', function() {
                currentReviewType = null;
                currentReviewId = null;
                resetReviewModalUI();
            });

            $('#reviewAplModal').on('shown.bs.modal', function() {
                setTimeout(initRekomendasiSignaturePad, 500);
            });
        });
    </script>
@endpush
