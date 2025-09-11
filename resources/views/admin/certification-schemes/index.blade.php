@extends('layouts.admin')

@section('title', 'Data Skema Sertifikasi')

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
                        <i class="bi bi-award me-2"></i>Data Skema Sertifikasi
                    </h5>
                    <p class="mb-0 text-muted">Kelola dan atur skema sertifikasi, unit kompetensi, dan kelompok kerja</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Filter Button -->
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#filterModal">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                    <!-- Add Button -->
                    <a href="{{ route('admin.certification-schemes.create') }}"
                        class="btn btn-primary-custom btn-sm text-light">
                        <i class="bi bi-plus-circle me-2 text-light"></i>Tambah Skema Sertifikasi
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Info Alert for All Management Options -->
            <div class="alert alert-info border-0 mb-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-info-circle-fill me-3 mt-1 text-info"></i>
                    <div>
                        <h6 class="alert-heading mb-2">Kelola Komponen Skema Sertifikasi</h6>
                        <p class="mb-2">Setiap skema sertifikasi dapat memiliki komponen berikut:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-2">
                                    <li><strong>Requirements/Persyaratan</strong> - Template dokumen yang diperlukan</li>
                                    <li><strong>Unit Kompetensi</strong> - Berisi elemen kompetensi dan kriteria kerja</li>
                                    <li><strong>Kelompok Kerja</strong> - Berisi bukti portofolio yang diperlukan</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0">Gunakan tombol berikut pada setiap baris:</p>
                                <div class="mt-2">
                                    <span class="badge bg-success me-1"><i class="bi bi-file-earmark-check"></i>
                                        Requirements</span>
                                    <span class="badge bg-info me-1"><i class="bi bi-list-check"></i> Unit Kompetensi</span>
                                    <span class="badge bg-secondary"><i class="bi bi-people"></i> Kelompok Kerja</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-container position-relative">
                <div class="loading-overlay d-none" id="loadingOverlay">
                    <div class="loading-spinner"></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="schemesTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 3%">
                                    <i class="bi bi-hash"></i>
                                </th>
                                <th style="width: 12%">
                                    <i class="bi bi-upc-scan me-2"></i>Kode Skema
                                </th>
                                <th>
                                    <i class="bi bi-award me-2"></i>Nama Skema
                                </th>
                                <th style="width: 15%">
                                    <i class="bi bi-diagram-3 me-2"></i>Bidang
                                </th>
                                <th style="width: 8%">
                                    <i class="bi bi-mortarboard me-2"></i>Jenjang
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
                        <i class="bi bi-funnel me-2"></i>Filter Data Skema Sertifikasi
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
                            <label for="field_filter" class="form-label">Bidang</label>
                            <select class="form-select" id="field_filter" name="field_filter">
                                <option value="">Semua Bidang</option>
                                @foreach ($fields as $field)
                                    <option value="{{ $field->code_2 }}">{{ $field->bidang }} - {{ $field->code_2 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenjang_filter" class="form-label">Jenjang</label>
                            <select class="form-select" id="jenjang_filter" name="jenjang_filter">
                                <option value="">Semua Jenjang</option>
                                <option value="Madya">Madya</option>
                                <option value="Menengah">Menengah</option>
                                <option value="Utama">Utama</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="resetFilter">
                        <i class="bi bi-x-circle me-2"></i>Reset
                    </button>
                    <button type="button" class="btn btn-primary-custom btn-sm text-light" id="applyFilter">
                        <i class="bi bi-check-circle me-2"></i>Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus skema sertifikasi ini?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Semua unit kompetensi, elemen kompetensi, kriteria kerja, kelompok
                        kerja, dan bukti portofolio yang terkait akan ikut terhapus!
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-xs {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.25rem;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .loading-spinner {
            width: 2rem;
            height: 2rem;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Custom button group spacing */
        .btn-group .btn {
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        /* Improve table responsiveness */
        .table-responsive {
            min-height: 400px;
        }

        /* Action column styling */
        #schemesTable td:last-child {
            white-space: nowrap;
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
             $(document).on('click', '.delete-btn', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const form = $('#delete-form-' + id);
    
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus skema sertifikasi "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
            var table = $('#schemesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.certification-schemes.index') }}',
                    data: function(d) {
                        // Only send filter values if they are not empty
                        var statusFilter = $('#status_filter').val();
                        var fieldFilter = $('#field_filter').val();
                        var jenjangFilter = $('#jenjang_filter').val();

                        if (statusFilter !== '') {
                            d.status_filter = statusFilter;
                        }
                        if (fieldFilter !== '') {
                            d.field_filter = fieldFilter;
                        }
                        if (jenjangFilter !== '') {
                            d.jenjang_filter = jenjangFilter;
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
                        data: 'code_1_badge',
                        name: 'code_1',
                        searchable: true
                    },
                    {
                        data: 'nama_display',
                        name: 'nama',
                        searchable: true
                    },
                    {
                        data: 'field_display',
                        name: 'field.bidang',
                        searchable: true
                    },
                    {
                        data: 'jenjang_badge',
                        name: 'jenjang',
                        searchable: true,
                        defaultContent: '-'
                    },
                    {
                        data: 'status_badge',
                        name: 'is_active',
                        searchable: false
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
                ], // Order by code_1
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });

            // Apply Filter
            $('#applyFilter').click(function() {
                table.ajax.reload();
                $('#filterModal').modal('hide');

                // Show applied filters info
                showFilterInfo();
            });

            // Reset Filter
            $('#resetFilter').click(function() {
                $('#filterForm')[0].reset();
                table.ajax.reload();
                $('#filterModal').modal('hide');

                // Remove filter info
                $('.filter-info').remove();
            });

            // Show loading overlay during AJAX requests
            $('#schemesTable').on('processing.dt', function(e, settings, processing) {
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

    if (confirm('Apakah Anda yakin ingin ' + statusText + ' skema sertifikasi ini?')) {
        $.ajax({
            url: url,
            type: 'PATCH', // langsung PATCH
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // tampilkan alert lalu refresh halaman
                    showSuccessAlert(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 800); // kasih delay dikit biar alert kebaca
                } else {
                    alert(response.message || 'Gagal mengubah status.');
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat mengubah status.');
            }
        });
    }
});



            // Handle delete confirmation
            window.confirmDelete = function(id) {
                $('#deleteForm').attr('action', '{{ url('admin/certification-schemes') }}/' + id);
                $('#deleteModal').modal('show');
            };

            // Delete form submission
            $('#deleteForm').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var url = $(form).attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(form).serialize(),
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        showSuccessAlert('Skema sertifikasi berhasil dihapus.');
                    },
                    error: function(xhr) {
                        $('#deleteModal').modal('hide');
                        alert('Terjadi kesalahan saat menghapus data.');
                    }
                });
            });

            // Helper functions
            function showSuccessAlert(message) {
                $('.alert-success-custom').remove();
                $('.card-header-custom').prepend(
                    '<div class="alert-success-custom">' +
                    '<i class="bi bi-check-circle-fill me-2"></i>' +
                    message +
                    '</div>'
                );
                setTimeout(function() {
                    $('.alert-success-custom').fadeOut();
                }, 3000);
            }

            function showFilterInfo() {
                var filters = [];
                var statusFilter = $('#status_filter').val();
                var fieldFilter = $('#field_filter').val();
                var jenjangFilter = $('#jenjang_filter').val();

                if (statusFilter !== '') {
                    filters.push('Status: ' + (statusFilter === '1' ? 'Aktif' : 'Tidak Aktif'));
                }
                if (fieldFilter !== '') {
                    filters.push('Bidang: ' + $('#field_filter option:selected').text());
                }
                if (jenjangFilter !== '') {
                    filters.push('Jenjang: ' + jenjangFilter);
                }

                if (filters.length > 0) {
                    $('.filter-info').remove();
                    $('.table-container').before(
                        '<div class="alert alert-primary alert-dismissible fade show filter-info" role="alert">' +
                        '<i class="bi bi-funnel me-2"></i><strong>Filter Aktif:</strong> ' + filters.join(
                            ', ') +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>'
                    );
                }
            }
        });
    </script>
@endpush
