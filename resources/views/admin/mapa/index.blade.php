{{-- resources/views/admin/mapa/index.blade.php --}}
@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
    <style>
        .checkbox-cell {
            width: 40px;
            text-align: center;
        }

        .bulk-action-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: none;
            animation: slideDown 0.3s ease;
        }

        .bulk-action-bar.show {
            display: flex;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mapa-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            font-weight: 600;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        @media (max-width: 768px) {
            .btn-action-group {
                flex-direction: column;
            }
            
            .btn-action-group .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-clipboard-check me-2"></i>Review MAPA
                    </h4>
                    <p class="mb-0 opacity-90">Review dan Approve MAPA dari Asesor</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon total-asesi me-3">
                            <i class="bi bi-files"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total MAPA</div>
                            <div class="stat-value counter-number" data-target="{{ $stats['total'] }}">{{ $stats['total'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon skema me-3">
                            <i class="bi bi-send"></i>
                        </div>
                        <div>
                            <div class="stat-label">Perlu Review</div>
                            <div class="stat-value counter-number" data-target="{{ $stats['submitted'] }}">
                                {{ $stats['submitted'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon sertifikat me-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="stat-label">Approved</div>
                            <div class="stat-value counter-number" data-target="{{ $stats['approved'] }}">
                                {{ $stats['approved'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon lp-aktif me-3">
                            <i class="bi bi-check-all"></i>
                        </div>
                        <div>
                            <div class="stat-label">Validated</div>
                            <div class="stat-value counter-number" data-target="{{ $stats['validated'] }}">
                                {{ $stats['validated'] }}</div>
                        </div>
                    </div>
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
                            <i class="bi bi-list-ul me-2"></i>Daftar MAPA
                        </h5>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="p-4 bg-light border-bottom">
                <form id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-search me-1"></i>Cari Nama Asesi
                            </label>
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Nama asesi..." value="{{ request('search') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-calendar-event me-1"></i>Tanggal Dari
                            </label>
                            <input type="date" name="date_from" id="dateFromInput" class="form-control"
                                value="{{ request('date_from') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-calendar-event me-1"></i>Sampai
                            </label>
                            <input type="date" name="date_to" id="dateToInput" class="form-control"
                                value="{{ request('date_to') }}">
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="d-flex gap-2">
                                <button type="button" id="applyFilter" class="btn-primary-custom flex-fill">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <button type="button" id="resetFilter" class="btn btn-outline-secondary" title="Reset Semua Filter">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Skema Filter (Always Visible) -->
                    <div class="row g-3 mt-2">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-award me-1"></i>Skema Sertifikasi
                            </label>
                            <select name="skema" id="skemaFilter" class="form-select">
                                <option value="">Semua Skema</option>
                                @php
                                    // Get all certification schemes from database
                                    $schemes = \App\Models\CertificationScheme::pluck('nama', 'id');
                                @endphp
                                @foreach($schemes as $id => $nama)
                                    <option value="{{ $id }}" {{ request('skema') == $id ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Container -->
            <div class="table-container" style="position: relative; min-height: 400px;">
                <div class="table-responsive">
                    <!-- Bulk Action Bar -->
                    <div class="bulk-action-bar" id="bulkActionBar">
                        <div
                            class="d-flex justify-content-between align-items-center w-100 p-3 shadow-sm border rounded-3 bg-white">
                            <div class="d-flex align-items-center text-secondary">
                                <i class="bi bi-check-square-fill fs-5 me-2 text-success"></i>
                                <span id="selectedCount" class="fw-semibold">0</span>
                                <span class="ms-1">MAPA dipilih</span>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-success btn-sm d-flex align-items-center shadow-sm"
                                    onclick="bulkApprove()">
                                    <i class="bi bi-check-circle-fill me-1"></i>Approve Semua
                                </button>
                                <button class="btn btn-danger btn-sm d-flex align-items-center shadow-sm"
                                    onclick="bulkReject()">
                                    <i class="bi bi-x-circle-fill me-1"></i>Reject Semua
                                </button>
                                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center"
                                    onclick="clearSelection()" title="Bersihkan pilihan">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">
                                    <input type="checkbox" class="mapa-checkbox" id="selectAll">
                                </th>
                                <th style="width: 5%;">No</th>
                                <th style="width: 12%;">Nomor MAPA</th>
                                <th style="width: 16%;">Skema</th>
                                <th style="width: 16%;">Asesi</th>
                                <th style="width: 12%;">Asesor</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 14%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @include('admin.mapa.partials.table-rows', ['mapaList' => $mapaList])
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 bg-light border-top" id="paginationContainer">
                    @if ($mapaList->total() > 0)
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="text-muted small" id="paginationInfo">
                                Menampilkan {{ $mapaList->firstItem() }} - {{ $mapaList->lastItem() }}
                                dari {{ $mapaList->total() }} data
                            </div>
                            <div id="paginationLinks">
                                {{ $mapaList->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-check me-2"></i>Review MAPA
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="mapaInfo" class="mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Review</label>
                        <textarea class="form-control" id="reviewNotes" rows="4"
                            placeholder="Tulis catatan untuk asesor (opsional untuk approve, wajib untuk reject)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="confirmReject()">
                        <i class="bi bi-x-circle me-1"></i>Reject
                    </button>
                    <button type="button" class="btn btn-success" onclick="confirmApprove()">
                        <i class="bi bi-check-circle me-1"></i>Approve
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Review Modal -->
    <div class="modal fade" id="bulkReviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-check-square me-2"></i><span id="bulkModalTitle">Bulk Review MAPA</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="bulkInfo" class="mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Review</label>
                        <textarea class="form-control" id="bulkReviewNotes" rows="4"
                            placeholder="Tulis catatan untuk semua MAPA yang dipilih..."></textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Catatan ini akan diterapkan ke semua MAPA yang dipilih
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="bulkConfirmBtn" onclick="confirmBulkAction()">
                        Proses
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/mapa-index.js') }}"></script>
    <script>
        // Initialize with config
        window.mapaAdminConfig = {
            indexRoute: '{{ route('admin.mapa.index') }}',
            approveRoute: '{{ route('admin.mapa.approve', ':id') }}',
            rejectRoute: '{{ route('admin.mapa.reject', ':id') }}',
            bulkApproveRoute: '{{ route('admin.mapa.bulk-approve') }}',
            bulkRejectRoute: '{{ route('admin.mapa.bulk-reject') }}'
        };

        // Override resetFilter function to clear all fields
        document.getElementById('resetFilter').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('searchInput').value = '';
            document.getElementById('dateFromInput').value = '';
            document.getElementById('dateToInput').value = '';
            document.getElementById('skemaFilter').value = '';
            applyFilters();
        });

        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush