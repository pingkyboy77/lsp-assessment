@extends('layouts.admin')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        /* Status Badge Improvements */
        .status-badge {
            padding: 0.4rem 0.85rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .status-badge i {
            margin-right: 0.4rem;
            font-size: 0.9rem;
        }

        .status-approved {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            color: #065F46;
            border: 1px solid #6EE7B7;
        }

        .status-submitted {
            background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
            color: #1E40AF;
            border: 1px solid #93C5FD;
        }

        .status-draft {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #92400E;
            border: 1px solid #FCD34D;
        }

        .status-rejected {
            background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }



        /* Asesi Info Card */
        .asesi-info {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .asesi-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover .asesi-avatar {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }

        .asesi-details {
            flex: 1;
            min-width: 0;
        }

        .asesi-name {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 0.2rem;
            font-size: 0.95rem;
        }

        .asesi-email {
            font-size: 0.8rem;
            color: #6B7280;
        }

        /* Filter Section Enhancements */
        .form-label {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        /* Empty State Enhancement */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 5rem;
            color: #D1D5DB;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Loading Overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 12px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #F3F4F6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Pagination Styling */
        .pagination {
            gap: 0.25rem;
        }

        .page-link {
            border-radius: 8px;
            border: 2px solid #E5E7EB;
            color: #6B7280;
            font-weight: 500;
            padding: 0.5rem 0.85rem;
            transition: all 0.3s ease;
            margin: 0 2px;
        }

        .page-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        #resetFilterEmpty {
            all: unset;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            background-color: #fff;
            color: #374151;
            font-size: 0.875rem;
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #resetFilterEmpty i {
            font-size: 0.9rem;
            margin-right: 6px;
        }

        #resetFilterEmpty:hover {
            background-color: #f3f4f6;
            color: #111827;
            border-color: #9ca3af;
        }
    </style>
@endpush

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
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-clipboard-check me-2"></i>Proses Perencanaan Asesmen dan Kosultasi
                    </h4>
                    <p class="mb-0 opacity-90">Merencanakan Aktivitas dan Proses Asesmen untuk setiap Asesi</p>
                </div>
            </div>
        </div>


        <!-- Main Card -->
        <div class="main-card">
            <!-- Card Header -->
            <div class="card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: #2c5282;">
                            <i class="bi bi-list-ul me-2"></i>Daftar Delegasi Asesmen
                        </h5>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="p-4 bg-light border-bottom">
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-search me-1"></i>Cari Data
                            </label>
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Nama asesi atau skema..." value="{{ request('search') }}">
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>Tanggal Asesmen
                            </label>
                            <input type="text" name="date_range" id="dateRangeFilter" class="form-control"
                                placeholder="Pilih rentang tanggal" value="{{ request('date_range') }}" readonly>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-funnel me-1"></i>Status MAPA
                            </label>
                            <select name="status_mapa" id="statusMapaFilter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="belum" {{ request('status_mapa') === 'belum' ? 'selected' : '' }}>
                                    Belum Dibuat
                                </option>
                                <option value="draft" {{ request('status_mapa') === 'draft' ? 'selected' : '' }}>
                                    Draft
                                </option>
                                <option value="submitted" {{ request('status_mapa') === 'submitted' ? 'selected' : '' }}>
                                    Submitted
                                </option>
                                <option value="approved" {{ request('status_mapa') === 'approved' ? 'selected' : '' }}>
                                    Approved
                                </option>
                            </select>
                        </div>

                        <div class="col-lg- col-md-12">
                            <label class="form-label fw-semibold small text-muted d-none d-md-block">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" id="applyFilter" class=" btn-primary-custom flex-fill">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <button type="button" id="resetFilter" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Container -->
            <div class="table-container" style="position: relative; min-height: 400px;">
                <div class="table-responsive">
                    <table class="table" id="mapaTable">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 25%;">Asesi</th>
                                <th style="width: 20%;">Skema Sertifikasi</th>
                                <th style="width: 12%;">Tanggal Asesmen</th>
                                <th style="width: 10%;">Status MAPA</th>
                                <th style="width: 18%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @include('asesor.mapa.partials.table-rows', ['delegasiList' => $delegasiList])
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 bg-light border-top" id="paginationContainer">
                    @if ($delegasiList->total() > 0)
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="text-muted small" id="paginationInfo">
                                Menampilkan {{ $delegasiList->firstItem() }} - {{ $delegasiList->lastItem() }}
                                dari {{ $delegasiList->total() }} data
                            </div>
                            <div id="paginationLinks">
                                {{ $delegasiList->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- Include external JS file --}}
    <script src="{{ asset('js/asesor/mapa-index.js') }}"></script>
    <script>
        // Initialize with config
        window.mapaConfig = {
            indexRoute: '{{ route('asesor.mapa.index') }}',
            currentFilters: {
                search: '{{ request('search') }}',
                date_range: '{{ request('date_range') }}',
                status_mapa: '{{ request('status_mapa') }}',
                status_apl: '{{ request('status_apl') }}'
            }
        };
    </script>
@endpush
