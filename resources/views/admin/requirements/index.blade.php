@extends('layouts.admin')

@section('title', 'Template Persyaratan')

@section('content')
    <div class="main-card">
        <!-- Card Header -->
        <div class="card-header-custom">
            @if (session('success'))
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-file-earmark-text me-2"></i>Template Persyaratan
                    </h5>
                    <p class="mb-0 text-muted">Kelola template persyaratan untuk skema sertifikasi</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Filter Button -->
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#filterModal">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                    <!-- Add Button -->
                    <a href="{{ route('admin.requirements.create') }}" class=" btn-primary-custom btn-sm text-light">
                        <i class="bi bi-plus-circle me-2 text-light"></i>Tambah Template
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-container position-relative">
                <div class="loading-overlay d-none" id="loadingOverlay">
                    <div class="loading-spinner"></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="requirementsTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%">
                                    <i class="bi bi-hash"></i>
                                </th>
                                <th>
                                    <i class="bi bi-card-text me-2"></i>Nama Template
                                </th>
                                <th>
                                    <i class="bi bi-file-text me-2"></i>Deskripsi
                                </th>
                                <th style="width: 10%">
                                    <i class="bi bi-list-check me-2"></i>Jumlah Item
                                </th>
                                <th>
                                    <i class="bi bi-diagram-3 me-2"></i>Digunakan
                                </th>
                                <th style="width: 8%">
                                    <i class="bi bi-toggle-on me-2"></i>Status
                                </th>
                                <th style="width: 15%">
                                    <i class="bi bi-gear me-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="bi bi-funnel me-2"></i>Filter Template Persyaratan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="status_filter" class="form-label">Status</label>
                            <select class="form-select" id="status_filter" name="status_filter">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="used_filter" class="form-label">Status Penggunaan</label>
                            <select class="form-select" id="used_filter" name="used_filter">
                                <option value="">Semua</option>
                                <option value="1">Sedang Digunakan</option>
                                <option value="0">Belum Digunakan</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="resetFilter">
                        <i class="bi bi-x-circle me-2"></i>Reset
                    </button>
                    <button type="button" class=" btn-primary-custom btn-sm text-light" id="applyFilter">
                        <i class="bi bi-check-circle me-2"></i>Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="bi bi-eye me-2"></i>Preview Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#requirementsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.requirements.index') }}',
                    data: function(d) {
                        var statusFilter = $('#status_filter').val();
                        var usedFilter = $('#used_filter').val();

                        if (statusFilter !== '') {
                            d.status_filter = statusFilter;
                        }
                        if (usedFilter !== '') {
                            d.used_filter = usedFilter;
                        }
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
                        data: 'name',
                        name: 'name',
                        searchable: true
                    },
                    {
                        data: 'description_display',
                        name: 'description',
                        searchable: true
                    },
                    {
                        data: 'items_count_display',
                        name: 'items_count',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'schemes_count_display',
                        name: 'schemes_count',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'status_badge',
                        name: 'is_active',
                        searchable: false,
                        className: 'text-center'
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
                    [1, 'asc']
                ]
            });

            // Apply Filter
            $('#applyFilter').click(function() {
                table.ajax.reload();
                $('#filterModal').modal('hide');
            });

            // Reset Filter
            $('#resetFilter').click(function() {
                $('#filterForm')[0].reset();
                table.ajax.reload();
                $('#filterModal').modal('hide');
            });

            // Show loading overlay during AJAX requests
            $('#requirementsTable').on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#loadingOverlay').removeClass('d-none');
                } else {
                    $('#loadingOverlay').addClass('d-none');
                }
            });

            // Handle toggle status
            $(document).on('click', '.toggle-status', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var button = $(this);
                var isActive = button.data('active');
                var statusText = isActive ? 'menonaktifkan' : 'mengaktifkan';

                if (confirm('Apakah Anda yakin ingin ' + statusText + ' template ini?')) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            '_method': 'PATCH'
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);
                            $('.alert-success-custom').remove();
                            $('.card-header-custom').prepend(
                                '<div class="alert-success-custom">' +
                                '<i class="bi bi-check-circle-fill me-2"></i>' +
                                'Status template berhasil diubah.' +
                                '</div>'
                            );
                            setTimeout(function() {
                                $('.alert-success-custom').fadeOut();
                            }, 3000);
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan saat mengubah status.');
                        }
                    });
                }
            });

            // Handle delete
            $(document).on('click', '.delete-template', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                if (confirm(
                        'Apakah Anda yakin ingin menghapus template ini? Template yang sedang digunakan tidak dapat dihapus.'
                        )) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);
                            $('.alert-success-custom').remove();
                            $('.card-header-custom').prepend(
                                '<div class="alert-success-custom">' +
                                '<i class="bi bi-check-circle-fill me-2"></i>' +
                                response.message +
                                '</div>'
                            );
                            setTimeout(function() {
                                $('.alert-success-custom').fadeOut();
                            }, 3000);
                        },
                        error: function(xhr) {
                            var response = JSON.parse(xhr.responseText);
                            alert(response.message ||
                                'Terjadi kesalahan saat menghapus template.');
                        }
                    });
                }
            });

            $(document).on('click', '.toggle-template', function(e) {
                e.preventDefault();
                var templateId = $(this).data('id');

                if (confirm('Yakin ingin mengubah status template ini?')) {
                    $.ajax({
                        url: '{{ route('admin.requirements.toggle-status', ':id') }}'.replace(':id',
                            templateId),
                        type: 'PATCH', // sekarang PATCH, cocok dengan route
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                // refresh halaman biar status baru kelihatan
                                location.reload();
                            } else {
                                alert('Gagal mengubah status.');
                            }
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan saat mengubah status.');
                        }
                    });
                }
            });
        });
    </script>
@endpush
