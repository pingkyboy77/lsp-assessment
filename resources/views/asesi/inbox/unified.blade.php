@extends('layouts.admin')

@section('title', 'Inbox APL - Dashboard Asesi')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
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
        <!-- Header -->
        <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="bi bi-inbox me-2"></i>Pra Assessment
                </h4>
                <p class="text-muted mb-0">Kelola Pra Assessment dalam satu dashboard terpadu</p>
            </div>
        </div>

        <div class="row m-3">
            <div class="col-12">
                <!-- Filter Button -->
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterSection">
                        <i class="bi bi-funnel me-1"></i> Filter
                        <i class="bi bi-chevron-down ms-1" id="filterToggleIcon"></i>
                    </button>
                </div>

                <!-- Collapsible Filter Section -->
                <div class="collapse mb-4" id="filterSection">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-funnel me-2"></i>Filter & Pencarian
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Search -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cari APL</label>
                                    <input type="text" class="form-control" placeholder="Masukkan kata kunci..."
                                        id="searchInput">
                                </div>

                                <!-- Status Filter -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select class="form-select" id="statusFilter">
                                        <option value="">Semua Status</option>
                                        <option value="draft">Draft</option>
                                        <option value="submitted">Submitted</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="open">Re Open</option>
                                        <option value="returned">Returned</option>
                                    </select>
                                </div>

                                <!-- Certification Scheme Filter -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Skema Sertifikasi</label>
                                    <select class="form-select" id="schemeFilter">
                                        <option value="">Semua Skema</option>
                                        @foreach (\App\Models\CertificationScheme::where('is_active', true)->get() as $scheme)
                                            <option value="{{ $scheme->id }}">{{ $scheme->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Date Filter -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Periode Dari</label>
                                    <input type="date" class="form-control" id="dateFrom">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Sampai</label>
                                    <input type="date" class="form-control" id="dateTo">
                                </div>

                                <!-- Type Filter Pills -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tipe APL</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="aplType" id="typeAll" value="all"
                                            checked>
                                        <label class="btn btn-outline-primary" for="typeAll">
                                            <i class="bi bi-list-ul me-1"></i>Semua
                                        </label>

                                        <input type="radio" class="btn-check" name="aplType" id="type01"
                                            value="apl01">
                                        <label class="btn btn-outline-primary" for="type01">
                                            <i class="bi bi-file-earmark me-1"></i>APL 01
                                        </label>

                                        <input type="radio" class="btn-check" name="aplType" id="type02"
                                            value="apl02">
                                        <label class="btn btn-outline-primary" for="type02">
                                            <i class="bi bi-file-earmark-check me-1"></i>APL 02
                                        </label>

                                        <input type="radio" class="btn-check" name="aplType" id="typeTuk"
                                            value="tuk">
                                        <label class="btn btn-outline-primary" for="typeTuk">
                                            <i class="bi bi-geo-alt me-1"></i>TUK
                                        </label>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-warning flex-fill"
                                            id="resetFilterBtn">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <button type="button" class="btn btn-outline-primary flex-fill"
                                            id="applyFilterBtn">
                                            <i class="bi bi-search me-1"></i>Terapkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;" class="text-center">APL</th>
                                        <th style="width: 100px;" class="text-center">STATUS</th>
                                        <th>SKEMA SERTIFIKASI</th>
                                        <th style="width: 130px;" class="text-center">TANGGAL</th>
                                        <th style="width: 120px;" class="text-center">PROGRESS</th>
                                        <th style="width: 200px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="aplTableBody">
                                    <!-- Data akan dimuat di sini -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Loading State -->
                        <div id="loadingState" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Memuat data APL...</p>
                        </div>

                        <!-- Empty State -->
                        <div id="emptyState" class="text-center py-5" style="display: none;">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Tidak ada APL ditemukan</h5>
                            <p class="text-muted">Silakan ubah filter pencarian Anda</p>
                        </div>
                    </div>
                </div>

                <!-- Load More Button -->
                <div class="text-center mt-3" id="loadMoreContainer" style="display: none;">
                    <button class="btn btn-outline-primary" id="loadMoreBtn">
                        <i class="bi bi-arrow-down-circle me-2"></i>Muat Lebih Banyak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Filter Toggle Button */
        #filterToggleIcon {
            transition: transform 0.3s ease;
        }

        [data-bs-toggle="collapse"][aria-expanded="true"] #filterToggleIcon {
            transform: rotate(180deg);
        }

        /* Filter Section Animation */
        #filterSection {
            transition: all 0.3s ease;
        }

        /* Table Styles */
        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            color: #6c757d;
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

        /* Badge Styles */
        .apl-badge {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
            color: white;
        }

        .apl-badge-01 {
            background: linear-gradient(135deg, #0d6efd, #4dabf7);
        }

        .apl-badge-02 {
            background: linear-gradient(135deg, #198754, #51cf66);
        }

        .apl-badge-tuk {
            background: linear-gradient(135deg, #6f42c1, #8a2be2);
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            min-width: 70px;
        }

        .status-draft {
            background: #6c757d;
            color: white;
        }

        .status-submitted {
            background: #17a2b8;
            color: white;
        }

        .status-approved {
            background: #28a745;
            color: white;
        }

        .status-rejected {
            background: #dc3545;
            color: white;
        }

        .status-open {
            background: #ffc107;
            color: #000;
        }

        .status-returned {
            background: #fd7e14;
            color: white;
        }

        .progress-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .progress-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: bold;
            color: white;
        }

        .progress-text {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* Action Buttons */
        .btn-action {
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 500;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            margin: 0.1rem;
            transition: all 0.2s ease;
        }

        .btn-view {
            background: #0d6efd;
            color: white;
        }

        .btn-view:hover {
            background: #0b5ed7;
            color: white;
            transform: translateY(-1px);
        }

        .btn-edit {
            background: #ffc107;
            color: #000;
        }

        .btn-edit:hover {
            background: #ffca2c;
            color: #000;
            transform: translateY(-1px);
        }

        .btn-verify {
            background: #198754;
            color: white;
        }

        .btn-verify:hover {
            background: #157347;
            color: white;
            transform: translateY(-1px);
        }

        .btn-download {
            background: #6c757d;
            color: white;
        }

        .btn-download:hover {
            background: #5c636a;
            color: white;
            transform: translateY(-1px);
        }

        .scheme-title {
            font-weight: 600;
            color: #212529;
            font-size: 0.85rem;
            line-height: 1.2;
            margin-bottom: 0.2rem;
        }

        .scheme-subtitle {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .date-text {
            font-size: 0.8rem;
            color: #495057;
            font-weight: 500;
        }

        /* Radio Button Styles */
        .btn-check:checked+.btn-outline-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }
    </style>
@endsection

@push('scripts')
    <script>
        let currentPage = 1;
        let isLoading = false;
        let hasMoreData = true;
        let searchTimeout = null;

        $(document).ready(function() {
            initializeFilters();
            loadData();

            // Toggle icon animation
            $('#filterSection').on('show.bs.collapse', function() {
                $('#filterToggleIcon').css('transform', 'rotate(180deg)');
            }).on('hide.bs.collapse', function() {
                $('#filterToggleIcon').css('transform', 'rotate(0deg)');
            });
        });

        function initializeFilters() {
            // Type filter dengan radio buttons
            $('input[name="aplType"]').on('change', function() {
                if (!isLoading) resetAndLoad();
            });

            // Other filters
            $('#statusFilter, #schemeFilter, #dateFrom, #dateTo').on('change', function() {
                if (!isLoading) resetAndLoad();
            });

            // Search dengan debounce
            $('#searchInput').on('input', function() {
                if (isLoading) return;
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(resetAndLoad, 500);
            });

            // Apply filter button
            $('#applyFilterBtn').on('click', function() {
                if (!isLoading) resetAndLoad();
            });

            // Load more
            $('#loadMoreBtn').on('click', function() {
                if (!isLoading && hasMoreData) {
                    loadData();
                }
            });

            // Reset filter
            $('#resetFilterBtn').on('click', function() {
                resetAllFilters();
                resetAndLoad();
            });
        }

        function resetAllFilters() {
            $('#searchInput').val('');
            $('#typeAll').prop('checked', true);
            $('#statusFilter').val('');
            $('#dateFrom').val('{{ date('Y-m-01') }}');
            $('#dateTo').val('{{ date('Y-m-d') }}');
            $('#schemeFilter').val('');
        }

        function resetAndLoad() {
            currentPage = 1;
            hasMoreData = true;
            $('#aplTableBody').empty();
            $('#emptyState').hide();
            loadData();
        }

        function loadData() {
            if (isLoading) return;

            isLoading = true;

            if (currentPage === 1) {
                showLoading();
            }

            const filters = {
                apl_type: $('input[name="aplType"]:checked').val(),
                status: $('#statusFilter').val(),
                scheme_id: $('#schemeFilter').val(),
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val(),
                search: $('#searchInput').val(),
                page: currentPage,
                per_page: 10
            };

            $.ajax({
                url: '{{ route('asesi.inbox.data') }}',
                type: 'POST',
                data: filters,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();

                    if (response.success && response.data && response.data.length > 0) {
                        appendData(response.data);

                        if (response.data.length < filters.per_page) {
                            hasMoreData = false;
                            $('#loadMoreContainer').hide();
                        } else {
                            hasMoreData = true;
                            currentPage++;
                            $('#loadMoreContainer').show();
                        }
                    } else {
                        if (currentPage === 1) {
                            showEmpty();
                        }
                        hasMoreData = false;
                        $('#loadMoreContainer').hide();
                    }
                },
                error: function() {
                    hideLoading();
                    if (currentPage === 1) {
                        showEmpty();
                    }
                    showToast('error', 'Gagal memuat data APL');
                },
                complete: function() {
                    isLoading = false;
                }
            });
        }

        function appendData(items) {
            items.forEach(item => {
                $('#aplTableBody').append(createRow(item));
            });
        }

        function createRow(item) {
            const aplType = item.apl_type;
            let typeLabel, badgeClass, subtitle;

            if (aplType === 'apl01') {
                typeLabel = '01';
                badgeClass = 'apl-badge-01';
                subtitle = 'APL01 - Assessment Pre-Liminary';
            } else if (aplType === 'apl02') {
                typeLabel = '02';
                badgeClass = 'apl-badge-02';
                subtitle = 'APL02 - Assessment Pre-Liminary';
            } else if (aplType === 'tuk') {
                typeLabel = 'TUK';
                badgeClass = 'apl-badge-tuk';
                subtitle = 'TUK Sewaktu - Permohonan Verifikasi';
            }

            return `
        <tr>
            <td class="text-center">
                <div class="apl-badge ${badgeClass}">${typeLabel}</div>
            </td>
            <td class="text-center">
                <span class="status-badge status-${item.status}">
                    ${getStatusText(item.status)}
                </span>
            </td>
            <td>
                <div class="scheme-title">${item.certification_scheme || 'Skema tidak tersedia'}</div>
                <div class="scheme-subtitle">${subtitle}</div>
            </td>
            <td class="text-center">
                <div class="date-text">
                    <i class="bi bi-calendar3 me-1"></i>
                    ${item.created_at_formatted || 'N/A'}
                </div>
            </td>
            <td class="text-center">
                <div class="progress-item">
                    ${getProgressContent(item)}
                </div>
            </td>
            <td class="text-center">
                ${getActionButtons(item)}
            </td>
        </tr>
    `;
        }

        function getStatusText(status) {
            const statusMap = {
                'draft': 'Draft',
                'submitted': 'Submitted',
                'approved': 'Approved',
                'rejected': 'Rejected',
                'open': 'Re Open',
                'returned': 'Returned'
            };
            return statusMap[status] || status;
        }

        function getProgressContent(item) {
            if (item.apl_type === 'tuk') {
                if (!item.tuk_recommended_by) {
                    return `
                <div class="progress-circle bg-info">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="progress-text">Submitted</div>
            `;
                } else {
                    return `
                <div class="progress-circle bg-success">
                    <i class="bi bi-check"></i>
                </div>
                <div class="progress-text">Approved</div>
            `;
                }
            }

            const progressConfig = {
                'approved': {
                    circle: 'bg-success',
                    icon: 'bi-check',
                    text: 'Approved'
                },
                'open': {
                    circle: 'bg-warning',
                    icon: 'bi-unlock',
                    text: 'Re Open'
                },
                'rejected': {
                    circle: 'bg-danger',
                    icon: 'bi-x',
                    text: 'Rejected'
                },
                'submitted': {
                    circle: 'bg-info',
                    icon: 'bi-clock',
                    text: 'Submitted'
                },
                'default': {
                    circle: 'bg-secondary',
                    icon: 'bi-pencil',
                    text: 'Draft'
                }
            };

            const config = progressConfig[item.status] || progressConfig.default;

            if (item.progress_percentage && item.status === 'draft') {
                return `
            <div class="progress-circle bg-warning">${item.progress_percentage}%</div>
            <div class="progress-text">Progress</div>
        `;
            }

            return `
        <div class="progress-circle ${config.circle}">
            <i class="bi ${config.icon}"></i>
        </div>
        <div class="progress-text">${config.text}</div>
    `;
        }

        function getActionButtons(item) {
            let buttons = '';

            if (item.apl_type === 'tuk') {
                return '';
            }

            if (item.status !== 'draft') {
                buttons += `<button class="btn btn-action btn-view" onclick="viewApl('${item.apl_type}', ${item.id})">
            <i class="bi bi-eye"></i> Lihat
        </button>`;
            }
            if (item.apl_type === 'apl01' && item.status === 'approved' && !item.has_apl02) {
                buttons += `<a href="{{ route('asesi.apl02.create') }}" class="btn btn-action btn-verify">
            <i class="bi bi-plus-circle"></i> Buat APL 02
        </a>`;
            }

            if (item.apl_type === 'apl02' && item.status === 'approved') {
                const tukType = (item.tuk_type || '').toLowerCase().trim();

                if (item.is_tuk_mandiri === true || tukType === 'mandiri') {
                    buttons += `<a href="/asesi/tuk/mandiri/${item.apl01_id}" class="btn btn-action" style="background: #17a2b8; color: white;">
                <i class="bi bi-file-earmark-pdf"></i> Lihat TUK
            </a>`;
                } else if (item.is_tuk_sewaktu === true || tukType === 'sewaktu') {
                    if (!item.has_tuk_request) {
                        buttons += `<a href="/asesi/tuk/${item.apl01_id}/form" class="btn btn-action btn-verify">
                    <i class="bi bi-clipboard-check"></i> Ajukan TUK
                </a>`;
                    }
                }
            }

            if (['draft', 'open', 'returned'].includes(item.status)) {
                buttons += `<a href="${getEditUrl(item.apl_type, item.id)}" class="btn btn-action btn-edit">
            <i class="bi bi-pencil"></i> Edit
        </a>`;
            }

            if (item.status === 'draft' && (item.progress_percentage && item.progress_percentage > 0)) {
                buttons += `<button class="btn btn-action btn-view" onclick="viewApl('${item.apl_type}', ${item.id})" style="background: #6c757d;">
            <i class="bi bi-eye"></i> Preview
        </button>`;
            }

            return buttons;
        }

        function showLoading() {
            $('#loadingState').show();
            $('#emptyState').hide();
        }

        function hideLoading() {
            $('#loadingState').hide();
        }

        function showEmpty() {
            $('#emptyState').show();
        }

        function getEditUrl(aplType, id) {
            return aplType === 'apl01' ?
                `{{ url('asesi/apl01') }}/${id}/edit` :
                `{{ url('asesi/apl02') }}/${id}/edit`;
        }

        function viewApl(aplType, id) {
            const url = aplType === 'apl01' ?
                `{{ url('asesi/apl01') }}/${id}/show` :
                `{{ url('asesi/apl02') }}/${id}`;
            window.open(url, '_blank');
        }

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
