@extends('layouts.admin')

@section('title', 'Monitoring Profil Pengguna')

@section('content')

    <!-- Main Content Card -->
    <div class="main-card">
        @if (session('success'))
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-danger-custom">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif
        <!-- Card Header -->
        <div class="card-header-custom">
            

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-clipboard-data me-2"></i>Monitoring Profile
                    </h5>
                    <p class="mb-0 text-muted">Monitoring pengguna yang telah mengisi data Profile</p>
                </div>

                <!-- Filter -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
                        <label class="form-label fw-bold small mb-1">Tanggal Dibuat</label>
                        <input type="text" id="filterTanggal" class="form-control form-control-sm"
                            placeholder="Pilih tanggal">
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-sm btn-light border" id="resetFilter">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="applyFilter">
                                <i class="bi bi-check2 me-1"></i>Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container position-relative mt-3">
            <div class="loading-overlay d-none" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="profileTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 5%">
                                <i class="bi bi-hash"></i>
                            </th>
                            <th>
                                <i class="bi bi-person-fill me-2"></i>Nama Lengkap
                            </th>
                            <th>
                                <i class="bi bi-card-text me-2"></i>NIK
                            </th>
                            <th>
                                <i class="bi bi-building me-2"></i>Tempat Kerja
                            </th>
                            <th>
                                <i class="bi bi-envelope me-2"></i>Email
                            </th>
                            <th>
                                <i class="bi bi-file-earmark-check me-2"></i>Status Dokumen
                            </th>
                            <th>
                                <i class="bi bi-calendar me-2"></i>Tanggal Dibuat
                            </th>
                            <th style="width: 12%">
                                <i class="bi bi-gear me-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <!-- Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(function() {
            // Init Date Range Picker
            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            $('#filterTanggal').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' s/d ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                table.draw();
            });

            $('#filterTanggal').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.draw();
            });

            // Init DataTable
            var table = $('#profileTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('admin.monitoring-profile.getData') }}",
                    type: 'GET',
                    data: function(d) {
                        d.tanggal_filter = $('#filterTanggal').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold text-muted'
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'nik',
                        name: 'nik',
                        render: data => data ? data : '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'nama_tempat_kerja',
                        name: 'nama_tempat_kerja',
                        render: data => data ? data : '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        render: data => data ? data : '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'status_dokumen',
                        name: 'status_dokumen',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [6, 'desc']
                ]
            });
        });


        // Apply filter
        $('#applyFilter').click(function() {
            table.draw();
            $('.dropdown-menu').removeClass('show');
        });

        // Reset filter
        $('#resetFilter').click(function() {
            $('#filterTanggal').val('');
            table.draw();
            $('.dropdown-menu').removeClass('show');
        });
    </script>
@endpush
