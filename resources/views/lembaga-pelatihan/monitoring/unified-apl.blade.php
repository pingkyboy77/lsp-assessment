{{-- resources/views/lembaga-pelatihan/monitoring/unified-apl.blade.php --}}
@extends('layouts.lembaga-pelatihan')

@section('title', 'Monitoring APL 01 & APL 02')

@push('styles')
    <style>
        .custom-select {
            padding: 12px 20px;
            font-size: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background-color: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 400;
            color: #495057;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .custom-select:hover {
            border-color: #adb5bd;
        }

        .custom-select:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .custom-select option {
            padding: 10px;
        }

        .apl-type-selector .form-label {
            font-size: 1rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .apl-type-selector .form-label i {
            color: #667eea;
        }

        .apl-type-selector {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .document-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        .document-card:hover {
            background: #f0f0f0;
            border-color: #007bff;
            transform: translateY(-2px);
        }

        .file-icon {
            font-size: 2rem;
            margin-right: 0.5rem;
        }

        .data-loading {
            display: none;
            text-align: center;
            padding: 3rem;
        }

        .hidden {
            display: none !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .main-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
        }

        .scheme-info {
            min-width: 200px;
            max-width: 300px;
            word-wrap: break-word;
            white-space: normal !important;
            overflow-wrap: break-word;
            line-height: 1.4;
        }

        .scheme-name {
            font-size: 0.85rem;
            color: #495057;
            word-break: break-word;
            white-space: normal !important;
            display: block;
        }
    </style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-card">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-white fw-bold">
                        <i class="bi bi-clipboard2-check me-2"></i>Monitoring Verifikasi APL
                    </h5>
                    <p class="mb-0 text-white-100">
                        Monitoring dan verifikasi APL 01 & APL 02 -
                        {{ auth()->user()->lembaga->name ?? 'Lembaga Pelatihan' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- APL Type Selector --}}
        <div class="apl-type-selector m-3">
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label fw-bold mb-3">
                        <i class="bi bi-list-check me-2"></i>Pilih Jenis APL:
                    </label>
                    <select id="aplTypeSelector" class="form-select custom-select">
                        <option value="" disabled selected>-- Pilih Jenis APL --</option>
                        <option value="apl01">APL 01 - Formulir Permohonan Sertifikasi</option>
                        <option value="apl02">APL 02 - Self Assessment</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div id="filtersSection" class="hidden">
            <div class="card m-3">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#filterWrapper">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>

                    <div class="collapse" id="filterWrapper">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Dari</label>
                                <input type="date" id="date_from" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Sampai</label>
                                <input type="date" id="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select id="status_filter" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="open">Re Open</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Table Section --}}
        <div id="tableSection" class="hidden">
            <div class="card m-3">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-table me-2"></i>
                        <span id="tableTitle">Data APL</span>
                    </h6>
                    <button class="btn btn-sm btn-outline-secondary" onclick="aplMonitoring.refreshTable()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="data-loading">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mb-0">Memuat data...</p>
                        </div>
                    </div>

                    <div class="table-responsive m-3">
                        <table id="aplDataTable" class="table table-hover mb-0 nowrap" style="width:100%; display: none;">
                            {{-- Table structure will be built dynamically --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('lembaga-pelatihan.monitoring.partials.apl01-review-modal')
    @include('lembaga-pelatihan.monitoring.partials.apl02-review-modal')
    @include('lembaga-pelatihan.monitoring.partials.reopen-modal')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aplSelector = document.getElementById('aplTypeSelector');

            if (aplSelector) {
                aplSelector.addEventListener('change', function() {
                    const selectedValue = this.value;
                    const statsContainer = document.getElementById('statsContainer');

                    if (selectedValue) {
                        console.log('Selected APL Type:', selectedValue);

                        if (statsContainer) {
                            statsContainer.style.display = 'block';
                        }
                    } else {
                        if (statsContainer) {
                            statsContainer.style.display = 'none';
                        }
                    }
                });
            }
        });
    </script>
    <script src="{{ asset('js/lembaga-pelatihan/unified-apl-monitoring.js') }}"></script>
@endpush
