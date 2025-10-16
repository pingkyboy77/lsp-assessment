@extends('layouts.admin')

@section('title', 'Pantauan Reschedule - Admin')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="main-card">
            <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-clock-history me-2"></i>Pantauan Reschedule TUK
                    </h4>
                    <p class="text-black mb-0">Daftar pengajuan TUK yang pernah di-reschedule</p>
                </div>
                <div>
                    <a href="{{ route('admin.tuk-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row m-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Reschedule</h6>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-clock-history fs-4 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">TUK Sewaktu</h6>
                                    <h3 class="mb-0">{{ $stats['sewaktu'] }}</h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-building fs-4 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">TUK Mandiri</h6>
                                    <h3 class="mb-0">{{ $stats['mandiri'] }}</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-house fs-4 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Bulan Ini</h6>
                                    <h3 class="mb-0">{{ $stats['this_month'] }}</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-calendar-check fs-4 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="card m-3 mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Jenis TUK</label>
                            <select name="type" id="filterType" class="form-select">
                                <option value="">Semua</option>
                                <option value="sewaktu">TUK Sewaktu</option>
                                <option value="mandiri">TUK Mandiri</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="filterDateFrom" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="filterDateTo" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetFilter()">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- DataTable -->
            <div class="card m-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2"></i>Daftar Reschedule
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive m-3">
                        <table id="rescheduleTable" class="table table-hover mb-0" style="width:100%">
                            <thead class="table-dark">
                                <tr>
                                    <th>Jenis TUK</th>
                                    <th>Kode/Nomor</th>
                                    <th>Peserta</th>
                                    <th>Skema Sertifikasi</th>
                                    <th>Alasan Reschedule</th>
                                    <th>Waktu Asesmen (Old)</th>
                                    <th>Admin</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>Detail Reschedule
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            color: white;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-weight: 500;
        }

        .badge+.badge {
            margin-left: 0.25rem;
        }

        /* Stats Card Animation */
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.3s ease;
        }

        
        /* Participant Info Style */
        .participant-name {
            font-weight: 600;
            color: #212529;
        }

        .participant-detail {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Scheme Name Style */
        .scheme-name {
            font-size: 0.85rem;
            color: #495057;
            line-height: 1.3;
            word-wrap: break-word;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .table th,
            .table td {
                font-size: 0.75rem;
                padding: 0.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let table;

        $(document).ready(function() {
            table = $('#rescheduleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.tuk-requests.reschedule-data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.type = $('#filterType').val();
                        d.date_from = $('#filterDateFrom').val();
                        d.date_to = $('#filterDateTo').val();
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTables Error:', error, code);
                        showToast('error', 'Gagal memuat data. Silakan refresh halaman.');
                    }
                },
                columns: [{
                        data: 'tuk_type_badge',
                        name: 'tuk_type',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'kode_nomor',
                        name: 'kode_tuk',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'peserta_info',
                        name: 'apl01.nama_lengkap',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'skema_sertifikasi',
                        name: 'apl01.certificationScheme.nama',
                        orderable: false,
                        searchable: true
                    },{
                        data: 'reschedule_reason',
                        name: 'reschedule_reason',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'waktu_reschedule',
                        name: 'rescheduled_at',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'admin_info',
                        name: 'rescheduledBy.name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [4, 'desc']
                ], // Default sort by waktu_reschedule descending
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                responsive: true,
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Filter form submit
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Reset filter function
        function resetFilter() {
            $('#filterType').val('');
            $('#filterDateFrom').val('');
            $('#filterDateTo').val('');
            table.ajax.reload();
        }

        // View detail function
        function viewRescheduleDetail(historyId) {
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();

            // Reset content
            $('#detailContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);

            // Load detail via AJAX
            $.ajax({
                url: '{{ route('admin.tuk-requests.reschedule-detail', ':id') }}'.replace(':id', historyId),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#detailContent').html(renderDetail(response.data));
                    } else {
                        $('#detailContent').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>${response.error || 'Gagal memuat detail'}
                    </div>
                `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Detail Error:', error);
                    $('#detailContent').html(`
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat detail: ${error}
                </div>
            `);
                }
            });
        }

        // Render detail content
        function renderDetail(data) {
            let html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Informasi Umum</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">Jenis TUK</td>
                        <td><strong>${data.tuk_type_text}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kode TUK</td>
                        <td>${data.kode_tuk || '-'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nomor APL</td>
                        <td>${data.nomor_apl}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Peserta</td>
                        <td>${data.peserta_nama}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu Reschedule</td>
                        <td>${data.formatted_rescheduled_at}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Admin</td>
                        <td>${data.admin_name}<br><small class="text-muted">${data.admin_email}</small></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Data Yang Dihapus</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">Delegasi</td>
                        <td>${data.had_delegation ? '<span class="badge bg-danger">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">MAPA</td>
                        <td>
                            ${data.had_mapa ? '<span class="badge bg-danger">Ya</span><br><small class="text-muted">' + (data.mapa_nomor || '') + '</small>' : '<span class="badge bg-secondary">Tidak</span>'}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Rekomendasi</td>
                        <td>${data.had_recommendation ? '<span class="badge bg-danger">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanda Tangan</td>
                        <td>${data.had_signature ? '<span class="badge bg-danger">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'}</td>
                    </tr>
                </table>
                <div class="mt-3">
                    <h6 class="text-muted mb-2">Status Sebelumnya</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">APL-01</td>
                            <td><span class="badge bg-secondary">${data.apl01_status_before || '-'}</span></td>
                        </tr>
                        ${data.apl02_status_before ? `
                            <tr>
                                <td class="text-muted">APL-02</td>
                                <td><span class="badge bg-secondary">${data.apl02_status_before}</span></td>
                            </tr>
                            ` : ''}
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-muted mb-2">Alasan Reschedule</h6>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-chat-left-text me-2"></i>${data.reschedule_reason}
                </div>
            </div>
        </div>
    `;

            if (data.old_tanggal_assessment) {
                html += `
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-muted mb-2">Data Assessment Sebelumnya</h6>
                <table class="table table-sm table-bordered">
                    <tr>
                        <td width="30%" class="table-light"><strong>Tanggal</strong></td>
                        <td>${data.old_tanggal_assessment}</td>
                    </tr>
                    <tr>
                        <td class="table-light"><strong>Lokasi</strong></td>
                        <td>${data.old_lokasi_assessment || '-'}</td>
                    </tr>
                </table>
            </div>
        </div>
        `;
            }

            return html;
        }

        // Toast notification function (same as document 7)
        function showToast(type, message) {
            const colors = {
                success: 'bg-success',
                error: 'bg-danger',
                info: 'bg-info',
                warning: 'bg-warning'
            };

            const icons = {
                success: 'bi-check-circle',
                error: 'bi-exclamation-triangle',
                info: 'bi-info-circle',
                warning: 'bi-exclamation-triangle'
            };

            const toastHtml = `
        <div class="toast align-items-center text-white ${colors[type]}" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${icons[type]} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }

            container.insertAdjacentHTML('beforeend', toastHtml);
            const toast = new bootstrap.Toast(container.lastElementChild);
            toast.show();
        }
    </script>
@endpush
