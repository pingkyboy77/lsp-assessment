@extends('layouts.admin')

@section('title', 'Data Bidang')

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
            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-diagram-3-fill me-2"></i>Data Bidang
                    </h5>
                    <p class="mb-0 text-muted">Kelola dan atur bidang sertifikasi</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Filter Button -->
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#filterModal">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                    <!-- Add Button -->
                    <a href="{{ route('admin.fields.create') }}" class=" btn-primary-custom btn-sm text-light">
                        <i class="bi bi-plus-circle me-2 text-light"></i>Tambah Bidang
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
                    <table class="table table-hover" id="fieldsTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%">
                                    <i class="bi bi-hash"></i>
                                </th>
                                <th style="width: 7%">
                                    <i class=""></i>Kode Bidang
                                </th>
                                <th style="width: 7%">
                                    <i class=""></i>Scheme Code
                                </th>
                                <th>
                                    <i class="bi bi-building me-2"></i>Bidang
                                </th>
                                <th style="width: 7%">
                                    <i class="bi bi-file-earmark-text me-2"></i>KBBLI
                                </th>
                                <th style="width: 7%">
                                    <i class="bi bi-toggle-on me-2"></i>Status
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
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="bi bi-funnel me-2"></i>Filter Data Bidang
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
                            <label for="kbbli_filter" class="form-label">KBBLI Bidang</label>
                            <input type="text" class="form-control" id="kbbli_filter" name="kbbli_filter"
                                placeholder="Masukkan KBBLI...">
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#fieldsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.fields.index') }}',
                    data: function(d) {
                        d.status_filter = $('#status_filter').val();
                        d.kbbli_filter = $('#kbbli_filter').val();
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
                        data: 'kode_bidang_badge',
                        name: 'kode_bidang',
                        searchable: true
                    },
                    {
                        data: 'code_2_badge',
                        name: 'code_2',
                        searchable: true
                    },
                    {
                        data: 'bidang_display',
                        name: 'bidang',
                        searchable: true
                    },
                    {
                        data: 'kbbli_bidang',
                        name: 'kbbli_bidang',
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
                        searchable: false
                    }
                ],
                order: [
                    [3, 'asc']
                ],
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
            $('#fieldsTable').on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#loadingOverlay').removeClass('d-none');
                } else {
                    $('#loadingOverlay').addClass('d-none');
                }
            });
        });
    </script>
@endpush
