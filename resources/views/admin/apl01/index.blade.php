@extends('layouts.admin')

@section('title', 'Kelola APL 01')

@section('content')
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
    <div class="main-card">
        <!-- Header Section -->
        <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Monitoring APL 01</h2>
                <p class="text-muted mb-0">Kelola permohonan sertifikasi profesi</p>
            </div>
            <div class="d-flex gap-2">
                {{-- <button class="btn btn-outline-secondary" onclick="exportData()">
                    <i class="bi bi-download"></i> Export
                </button> --}}
                <button class="btn btn-outline-info" onclick="refreshStats()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row m-2" id="statisticsCards">
            <div class="col-md-4">
                <div class="card border-0 bg-primary bg-opacity-10">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text text-primary fs-1"></i>
                        <h4 class="text-primary mb-0" id="stat-total">0</h4>
                        <small class="text-muted">Total APL</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 bg-secondary bg-opacity-10">
                    <div class="card-body text-center">
                        <i class="bi bi-pencil-square text-secondary fs-1"></i>
                        <h4 class="text-secondary mb-0" id="stat-draft">0</h4>
                        <small class="text-muted">Draft</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 bg-info bg-opacity-10">
                    <div class="card-body text-center">
                        <i class="bi bi-send text-info fs-1"></i>
                        <h4 class="text-info mb-0" id="stat-submitted">0</h4>
                        <small class="text-muted">Submitted</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 bg-success bg-opacity-10">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle text-success fs-1"></i>
                        <h4 class="text-success mb-0" id="stat-approved">0</h4>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 bg-danger bg-opacity-10">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle text-danger fs-1"></i>
                        <h4 class="text-danger mb-0" id="stat-rejected">0</h4>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </div>
        </div>




        <!-- Bulk Actions -->
        <div class="card mb-4" id="bulkActionsCard" style="display: none;">
            <div class="card-body bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong><span id="selectedCount">0</span> APL dipilih</strong>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" onclick="bulkAction('approve')">
                            <i class="bi bi-check"></i> Approve
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="bulkAction('reject')">
                            <i class="bi bi-x"></i> Reject
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                            <i class="bi bi-x"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- APL List -->
        <div class="card m-3">
            <div class="card-body p-0 m-3">
                <!-- Filters -->
                <div class="d-flex justify-content-end m-3">
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#filterWrapper" aria-expanded="false" aria-controls="filterWrapper">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>

                <div class="collapse" id="filterWrapper">
                    <div class="card m-3">
                        <div class="card-body">
                            <div class="row g-3" id="filterForm">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Dari</label>
                                    <input type="date" id="date_from" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Sampai</label>
                                    <input type="date" id="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select id="status_filter" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="draft">Draft</option>
                                        <option value="submitted">Submitted</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="returned">Returned</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Skema Sertifikasi</label>
                                    <select id="scheme_filter" class="form-select">
                                        <option value="">Semua Skema</option>
                                        @foreach (\App\Models\CertificationScheme::where('is_active', true)->get() as $scheme)
                                            <option value="{{ $scheme->id }}">{{ $scheme->nama }} ({{ $scheme->code_1 }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Pencarian</label>
                                    <input type="text" id="search_input" class="form-control"
                                        placeholder="Nama, NIK, APL">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary w-100"
                                        onclick="resetFilters()">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="aplTable" class="table table-hover mb-0" style="width: 100%">
                        <thead class="bg-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>No. APL / Peserta</th>
                                <th>Skema Sertifikasi</th>
                                <th>Lembaga Pelatihan</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Reviewer</th>
                                <th width="160">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review APL 01</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="reviewModalBody">
                    <!-- Content will be loaded here -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="flex-fill">
                        <textarea class="form-control" id="reviewNotes" placeholder="Catatan review (opsional)..." rows="3"></textarea>
                    </div>
                    <div class="d-flex flex-column gap-2 ms-3">
                        <button type="button" class="btn btn-success" onclick="processReview('approve')">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger" onclick="processReview('reject')">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="processReview('return')">
                            <i class="bi bi-arrow-return-left"></i> Return
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reopenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reopen APL 01</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membuka kembali APL 01 ini?</p>
                    <p class="text-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Setelah dibuka kembali, status akan berubah menjadi "Open" dan asesi dapat mengedit form ini
                        kembali.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional):</label>
                        <textarea class="form-control" id="reopenNotes" rows="3"
                            placeholder="Berikan catatan mengapa form dibuka kembali..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" onclick="confirmReopen()">
                        <i class="bi bi-unlock"></i> Reopen
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .document-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fafafa;
        }

        .document-card:hover {
            background: #f0f0f0;
            border-color: #007bff;
        }

        .file-icon {
            font-size: 2rem;
            margin-right: 0.5rem;
        }

        .file-info small {
            color: #6c757d;
        }
    </style>
@endsection

@push('scripts')
    <script>
        let currentAplId = null;
        let selectedRows = new Set();
        let dataTable = null;
        $('#status_filter, #scheme_filter').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            placeholder: 'Pilih opsi',
            width: '100%'
        });
        // Initialize DataTable
        $(document).ready(function() {
            initializeDataTable();
            initializeEventHandlers();
            refreshStats();
        });

        function initializeDataTable() {
            dataTable = $('#aplTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.apl01.data') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                        d.status = $('#status_filter').val();
                        d.scheme_id = $('#scheme_filter').val();
                        d.search_input = $('#search_input').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="form-check-input row-select" value="${data}">`;
                        }
                    },
                    {
                        data: null,
                        name: 'participant_info',
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                        <div class="d-flex flex-column">
                            <span class="fw-bold">${row.nomor_apl_01 || 'DRAFT'}</span>
                            <small class="text-muted">${row.nama_lengkap}</small>
                            <small class="text-muted">${row.email}</small>
                        </div>
                    `;
                        }
                    },
                    {
                        data: null,
                        name: 'scheme_info',
                        render: function(data, type, row) {
                            return `
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${row.certification_scheme_nama || '-'}</span>
                            <small class="text-muted">${row.certification_scheme_jenjang || ''}</small>
                            <small class="text-muted">${row.units_count || 0} Unit Kompetensi</small>
                        </div>
                    `;
                        }
                    },
                    {
                        data: 'lembaga_pelatihan_nama',
                        name: 'lembaga_pelatihan.name',
                        render: function(data, type, row) {
                            return data ? `<span class="text-primary">${data}</span>` :
                                '<span class="text-muted">Individu</span>';
                        }
                    },
                    {
                        data: 'submitted_at',
                        name: 'submitted_at',
                        render: function(data, type, row) {
                            if (data) {
                                const date = new Date(data);
                                return `
                            <small class="text-muted">
                                ${date.toLocaleDateString('id-ID')}<br>
                                ${date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}
                            </small>
                        `;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            const colorMap = {
                                'draft': 'text-secondary',
                                'submitted': 'text-info',
                                'approved': 'text-success',
                                'rejected': 'text-danger',
                                'returned': 'text-warning'
                            };
                            const textMap = {
                                'draft': 'Draft',
                                'submitted': 'Submitted',
                                'approved': 'Approved',
                                'rejected': 'Rejected',
                                'returned': 'Returned'
                            };
                            return `<span class="fw-semibold ${colorMap[data] || 'text-secondary'}">${textMap[data] || data}</span>`;
                        }
                    },
                    {
                        data: null,
                        name: 'reviewer_info',
                        orderable: false,
                        render: function(data, type, row) {
                            if (row.reviewer_name && row.reviewed_at) {
                                const reviewDate = new Date(row.reviewed_at);
                                return `
                            <small class="text-muted">
                                ${row.reviewer_name}<br>
                                ${reviewDate.toLocaleDateString('id-ID')} ${reviewDate.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}
                            </small>
                        `;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let actions = `
                        <div class="d-flex gap-1">
                            <a href="/admin/apl01/${row.id}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/admin/apl01/${row.id}/edit" class="btn btn-sm btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                    `;

                            if (['submitted', 'review', 'reviewed'].includes(row.status)) {
                                actions += `
                            <button class="btn btn-sm btn-outline-info" onclick="openReviewModal(${row.id})" title="Review">
                                <i class="bi bi-clipboard-check"></i>
                            </button>
                        `;
                            }
                            if (['submitted', 'approved'].includes(row
                                    .status)) {
                                actions += `
                    <button class="btn btn-sm btn-outline-warning" onclick="reopenApl(${row.id})">
                        <i class="bi bi-unlock"></i>
                    </button>
                        `;
                            }

                            actions += '</div>';
                            return actions;
                        }
                    }
                ],
                order: [
                    [4, 'desc']
                ], // Order by submitted_at desc
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                responsive: true,
                language: {
                    processing: "Memuat data...",
                    search: "Pencarian:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data yang tersedia",
                    zeroRecords: "Tidak ada data yang cocok dengan pencarian"
                },
                drawCallback: function(settings) {
                    bindRowSelectHandlers();
                }
            });
        }


        function reopenApl(aplId) {
            currentAplId = aplId;
            const modal = new bootstrap.Modal(document.getElementById('reopenModal'));
            modal.show();
        }

        async function confirmReopen() {
            const notes = document.getElementById('reopenNotes').value;
            const modal = bootstrap.Modal.getInstance(document.getElementById('reopenModal'));

            try {
                const response = await fetch(`/admin/apl01/${currentAplId}/reopen`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        notes: notes
                    })
                });

                const result = await response.json();

                if (result.success) {
                    modal.hide();
                    showToast('success', result.message);

                    // Reload page to show updated status
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Failed to reopen APL');
                }
            } catch (error) {
                console.error('Reopen error:', error);
                showToast('error', 'Gagal membuka kembali APL: ' + error.message);
            }
        }

        function initializeEventHandlers() {
            // Filter change handlers
            $('#date_from, #date_to, #status_filter, #scheme_filter').on('change', function() {
                dataTable.ajax.reload();
            });

            // Search input with debounce
            let searchTimeout;
            $('#search_input').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    dataTable.ajax.reload();
                }, 500);
            });

            // Select all handler
            $('#selectAll').on('change', function() {
                const isChecked = this.checked;
                $('.row-select').prop('checked', isChecked);

                if (isChecked) {
                    $('.row-select').each(function() {
                        selectedRows.add(parseInt($(this).val()));
                    });
                } else {
                    selectedRows.clear();
                }
                updateBulkActionsVisibility();
            });
        }

        function bindRowSelectHandlers() {
            $('.row-select').off('change').on('change', function() {
                const id = parseInt($(this).val());
                if ($(this).is(':checked')) {
                    selectedRows.add(id);
                } else {
                    selectedRows.delete(id);
                    $('#selectAll').prop('checked', false);
                }
                updateBulkActionsVisibility();
            });
        }

        function resetFilters() {
            $('#date_from').val('{{ date('Y-m-d') }}');
            $('#date_to').val('{{ date('Y-m-d') }}');
            $('#status_filter').val('').trigger('change');
            $('#scheme_filter').val('').trigger('change');
            $('#search_input').val('');
            dataTable.ajax.reload();
        }

        function updateBulkActionsVisibility() {
            const count = selectedRows.size;
            const bulkCard = document.getElementById('bulkActionsCard');
            const countSpan = document.getElementById('selectedCount');

            if (count > 0) {
                bulkCard.style.display = 'block';
                countSpan.textContent = count;
            } else {
                bulkCard.style.display = 'none';
            }
        }

        function clearSelection() {
            selectedRows.clear();
            $('.row-select, #selectAll').prop('checked', false);
            updateBulkActionsVisibility();
        }

        async function openReviewModal(aplId) {
            currentAplId = aplId;
            const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
            const modalBody = document.getElementById('reviewModalBody');

            // Show loading
            modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat data review...</p>
        </div>
    `;

            modal.show();

            try {
                const response = await fetch(`/admin/apl01/${aplId}/review-data`);
                const result = await response.json();

                if (result.success) {
                    renderReviewData(result.data);
                } else {
                    throw new Error(result.message || 'Failed to load review data');
                }
            } catch (error) {
                modalBody.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                Error: ${error.message}
            </div>
        `;
            }
        }

        function renderReviewData(data) {
            const modalBody = document.getElementById('reviewModalBody');
            const apl = data.apl;

            let documentsHtml = '';

            // User Documents
            if (data.user_documents && data.user_documents.length > 0) {
                documentsHtml += `
            <div class="mb-4">
                <h6 class="border-bottom pb-2"><i class="bi bi-person-badge"></i> Dokumen Administrasi</h6>
                <div class="row g-3">
        `;

                data.user_documents.forEach(doc => {
                    const iconClass = getFileIcon(doc.file_extension);
                    documentsHtml += `
                <div class="col-md-6">
                    <div class="document-card">
                        <div class="d-flex align-items-center">
                            <i class="${iconClass} file-icon"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${doc.jenis_dokumen}</div>
                                <small class="text-muted">
                                    ${doc.file_extension.toUpperCase()} • ${doc.file_size_kb} KB
                                </small>
                            </div>
                            <div class="ms-2">
                                ${doc.file_exists ? 
                                    `<a href="${doc.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>` : 
                                    '<span class="badge bg-danger">File Tidak Ada</span>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
                });

                documentsHtml += `
                </div>
            </div>
        `;
            }

            // Requirement Documents
            if (data.requirement_documents && data.requirement_documents.length > 0) {
                documentsHtml += `
            <div class="mb-4">
                <h6 class="border-bottom pb-2"><i class="bi bi-file-earmark-check"></i> Dokumen Persyaratan</h6>
                <div class="row g-3">
        `;

                data.requirement_documents.forEach(doc => {
                    const iconClass = getFileIcon(doc.file_extension);
                    const fileSize = (doc.file_size / 1024).toFixed(2);

                    documentsHtml += `
                <div class="col-md-6">
                    <div class="document-card">
                        <div class="d-flex align-items-center">
                            <i class="${iconClass} file-icon"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${doc.item_name}</div>
                                <small class="text-muted">
                                    ${doc.file_extension.toUpperCase()} • ${fileSize} KB
                                </small>
                            </div>
                            <div class="ms-2">
                                <a href="${doc.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                });

                documentsHtml += `
                </div>
            </div>
        `;
            }

            modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-person-circle"></i> Informasi Peserta</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-semibold">No. APL:</td>
                                <td>${apl.nomor_apl_01}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Nama:</td>
                                <td>${apl.nama_lengkap}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Email:</td>
                                <td>${apl.email}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">No. HP:</td>
                                <td>${apl.no_hp}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Status:</td>
                                <td><span class="badge bg-info">${apl.status_text}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Submitted:</td>
                                <td>${apl.submitted_at || '-'}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Skema:</td>
                                <td>${apl.certification_scheme || '-'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-files"></i>DATA DOKUMEN PESERTA</h6>
                        ${documentsHtml || '<p class="text-muted">Tidak ada dokumen yang diupload.</p>'}
                    </div>
                </div>
            </div>
        </div>
    `;
        }

        function getFileIcon(extension) {
            const ext = extension.toLowerCase();
            switch (ext) {
                case 'pdf':
                    return 'bi bi-file-earmark-pdf text-danger';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'bi bi-file-earmark-image text-success';
                case 'doc':
                case 'docx':
                    return 'bi bi-file-earmark-word text-primary';
                case 'xls':
                case 'xlsx':
                    return 'bi bi-file-earmark-excel text-success';
                default:
                    return 'bi bi-file-earmark text-secondary';
            }
        }

        async function processReview(action) {
            const notes = document.getElementById('reviewNotes').value;
            const button = event.target;
            const originalContent = button.innerHTML;

            // Validate required notes for reject and return actions
            if ((action === 'reject' || action === 'return') && !notes.trim()) {
                alert('Catatan wajib diisi untuk aksi ini.');
                return;
            }

            // Show loading
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

            try {
                let endpoint = '';
                switch (action) {
                    case 'approve':
                        endpoint = `/admin/apl01/${currentAplId}/approve`;
                        break;
                    case 'reject':
                        endpoint = `/admin/apl01/${currentAplId}/reject`;
                        break;
                    case 'set_review':
                        endpoint = `/admin/apl01/${currentAplId}/set-review`;
                        break;
                    case 'return':
                        endpoint = `/admin/apl01/${currentAplId}/return-revision`;
                        break;
                }

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        notes: notes
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();

                    // Show success message
                    showToast('success', result.message);

                    // Reload DataTable
                    dataTable.ajax.reload();
                    refreshStats();
                } else {
                    throw new Error(result.message || 'Action failed');
                }
            } catch (error) {
                showToast('error', error.message);
            } finally {
                button.disabled = false;
                button.innerHTML = originalContent;
            }
        }

        async function bulkAction(action) {
            if (selectedRows.size === 0) {
                alert('Pilih minimal satu APL untuk diproses.');
                return;
            }

            const notes = prompt('Catatan (opsional):');
            if (action === 'reject' && !notes) {
                alert('Catatan wajib diisi untuk reject.');
                return;
            }

            if (!confirm(`Yakin ingin ${action} ${selectedRows.size} APL?`)) {
                return;
            }

            try {
                const response = await fetch('/admin/apl01/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        ids: Array.from(selectedRows),
                        action: action,
                        notes: notes
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showToast('success', result.message);
                    dataTable.ajax.reload();
                    refreshStats();
                    clearSelection();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('error', error.message);
            }
        }

        function showToast(type, message) {
            // Create toast element
            const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

            // Add to toast container
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(container);
            }

            container.insertAdjacentHTML('beforeend', toastHtml);

            // Show toast
            const toastElement = container.lastElementChild;
            const toast = new bootstrap.Toast(toastElement, {
                delay: 5000
            });
            toast.show();

            // Remove from DOM after hide
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        async function exportData() {
            try {
                const response = await fetch('/admin/apl01/export', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `apl01_export_${new Date().getTime()}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    showToast('success', 'Export berhasil diunduh.');
                } else {
                    throw new Error('Export failed');
                }
            } catch (error) {
                showToast('error', 'Export gagal: ' + error.message);
            }
        }

        async function refreshStats() {
            try {
                const response = await fetch('/admin/apl01/statistics');
                const stats = await response.json();

                // Update statistics cards
                document.getElementById('stat-total').textContent = stats.total || 0;
                document.getElementById('stat-draft').textContent = stats.draft || 0;
                document.getElementById('stat-submitted').textContent = stats.submitted || 0;
                document.getElementById('stat-approved').textContent = stats.approved || 0;
                document.getElementById('stat-rejected').textContent = stats.rejected || 0;

            } catch (error) {
                console.error('Failed to refresh stats:', error);
            }
        }
    </script>
@endpush
