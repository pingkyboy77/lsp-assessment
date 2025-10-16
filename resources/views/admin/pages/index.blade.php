@extends('layouts.admin')

@section('title', 'Page Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="main-card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-layout-text-sidebar me-2"></i>
                            Page Management
                        </h5>
                        <div class=" d-flex align-items-center">
                            <a href="{{ route('admin.pages.create') }}" class=" btn-primary-custom btn-sm" style="text-decoration: none">
                                <i class="bi bi-plus"></i> Tambah Halaman
                            </a>
                            {{-- <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#importModal">
                                <i class="bi bi-upload"></i> Import
                            </button>
                            <a href="{{ route('admin.pages.export') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-download"></i> Export
                            </a> --}}
                        </div>
                    </div>

                    <div class="card-body m-3">
                        <!-- Filters -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Group</label>
                                <select id="groupFilter" class="form-select form-select-sm">
                                    <option value="">Semua Group</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group }}">{{ $group }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select id="statusFilter" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Non-aktif</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" id="searchInput" class="form-control form-control-sm"
                                    placeholder="Cari nama, route, atau deskripsi...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-circle"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Actions -->
                        <form id="bulkForm" method="POST" action="{{ route('admin.pages.bulk-action') }}">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                    <label for="selectAll" class="form-check-label small">Pilih Semua</label>
                                    <div class="vr"></div>
                                    <select name="action" class="form-select form-select-sm" style="width: auto;" disabled
                                        id="bulkAction">
                                        <option value="">Pilih Aksi</option>
                                        <option value="activate">Aktifkan</option>
                                        <option value="deactivate">Non-aktifkan</option>
                                        <option value="delete">Hapus</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" disabled id="bulkSubmit">
                                        Jalankan
                                    </button>
                                </div>
                                <div id="tableInfo" class="text-muted small">
                                    <!-- Will be populated by DataTables -->
                                </div>
                            </div>

                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table id="pagesTable" class="table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                            </th>
                                            <th>Nama</th>
                                            <th>Route</th>
                                            <th>Group</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Order</th>
                                            <th width="150">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded by DataTables -->
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.pages.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Konfigurasi Halaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">File JSON</label>
                            <input type="file" name="file" class="form-control" accept=".json" required>
                            <div class="form-text">
                                Upload file JSON yang telah diekspor sebelumnya
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')

        <script>
            $(document).ready(function() {
                let table = $('#pagesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.pages.index') }}",
                        data: function(d) {
                            d.group_filter = $('#groupFilter').val();
                            d.status_filter = $('#statusFilter').val();
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            orderable: false,
                            searchable: false,
                            width: '30px'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'route_name',
                            name: 'route_name'
                        },
                        {
                            data: 'group',
                            name: 'group'
                        },
                        {
                            data: 'roles',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'sort_order',
                            name: 'sort_order'
                        },
                        {
                            data: 'actions',
                            orderable: false,
                            searchable: false,
                            width: '150px'
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
                    dom: '<"row mb-2"' +
                        '<"col-sm-12 col-md-6 d-flex align-items-center"l>' +
                        '<"col-sm-12 col-md-6 d-flex align-items-center justify-content-end"f>' +
                        '>' +
                        '<"table-responsive"tr>' +
                        '<"row mt-2"' +
                        '<"col-sm-12 col-md-5 d-flex align-items-center"i>' +
                        '<"col-sm-12 col-md-7 d-flex align-items-center justify-content-end"p>' +
                        '>',
                    drawCallback: function(settings) {
                        // Update table info
                        let info = settings.json;
                        if (info) {
                            $('#tableInfo').text(`Total: ${info.recordsTotal} halaman`);
                        }

                        // Reinitialize event listeners after table redraw
                        initializeTableEvents();
                    }
                });

                // Filter events
                $('#groupFilter, #statusFilter').on('change', function() {
                    table.draw();
                });

                // Custom search with delay
                let searchTimeout;
                $('#searchInput').on('input', function() {
                    clearTimeout(searchTimeout);
                    let searchValue = this.value;
                    searchTimeout = setTimeout(function() {
                        table.search(searchValue).draw();
                    }, 500);
                });

                // Reset filters
                $('#resetFilters').on('click', function() {
                    $('#groupFilter').val('');
                    $('#statusFilter').val('');
                    $('#searchInput').val('');
                    table.search('').draw();
                });

                // Initialize table events
                function initializeTableEvents() {
                    // Select All functionality
                    const selectAllCheckboxes = $('#selectAll, #selectAllHeader');
                    const pageCheckboxes = $('.page-checkbox');
                    const bulkAction = $('#bulkAction');
                    const bulkSubmit = $('#bulkSubmit');

                    selectAllCheckboxes.off('change').on('change', function() {
                        const isChecked = this.checked;
                        pageCheckboxes.prop('checked', isChecked);
                        toggleBulkActions();

                        // Sync both select all checkboxes
                        selectAllCheckboxes.prop('checked', isChecked);
                    });

                    pageCheckboxes.off('change').on('change', toggleBulkActions);

                    function toggleBulkActions() {
                        const checkedBoxes = $('.page-checkbox:checked');
                        const hasChecked = checkedBoxes.length > 0;

                        bulkAction.prop('disabled', !hasChecked);
                        bulkSubmit.prop('disabled', !hasChecked || !bulkAction.val());

                        // Update select all checkbox state
                        const allChecked = checkedBoxes.length === pageCheckboxes.length && pageCheckboxes.length > 0;
                        const someChecked = checkedBoxes.length > 0;

                        selectAllCheckboxes.prop('checked', allChecked);
                        selectAllCheckboxes.prop('indeterminate', someChecked && !allChecked);
                    }

                    bulkAction.off('change').on('change', function() {
                        bulkSubmit.prop('disabled', !this.value || $('.page-checkbox:checked').length === 0);
                    });
                }

                // Bulk form submission
                $('#bulkForm').on('submit', function(e) {
                    const checkedBoxes = $('.page-checkbox:checked');
                    const action = $('#bulkAction').val();

                    if (checkedBoxes.length === 0) {
                        e.preventDefault();
                        alert('Pilih minimal satu halaman');
                        return;
                    }

                    let message = `Yakin ingin ${action === 'activate' ? 'mengaktifkan' : 
                      action === 'deactivate' ? 'menonaktifkan' : 'menghapus'} ${checkedBoxes.length} halaman?`;

                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });

                // Initial event setup
                initializeTableEvents();
            });
        </script>
    @endpush

@endsection
