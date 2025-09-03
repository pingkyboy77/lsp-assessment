{{-- resources/views/admin/kelompok-kerja/show.blade.php --}}
@extends('layouts.admin')

@push('styles')
    <style>
        .unit-card {
            transition: all 0.3s ease;
            border: none !important;
        }

        .unit-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .unit-header {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .stat-card {
            border-left: 4px solid;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card.primary {
            border-left-color: #0d6efd;
        }

        .stat-card.success {
            border-left-color: #198754;
        }

        .stat-card.info {
            border-left-color: #0dcaf0;
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .section-divider {
            border-top: 2px solid #e9ecef;
            margin: 2rem 0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .meta-item {
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .meta-item:last-child {
            border-bottom: none;
        }

        .meta-label {
            font-weight: 600;
            color: #495057;
            width: 150px;
            display: inline-block;
        }

        .filter-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
        }

        .filter-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 10px 20px;
            border-bottom: 3px solid transparent;
        }

        .filter-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
            background: none;
        }

        .filter-tabs .nav-link:hover {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
        }

        .bukti-item {
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .bukti-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-color: #0d6efd;
        }

        .char-count {
            font-size: 0.8rem;
        }

        #buktiPortofolioModal .modal-dialog {
            max-width: 90vw;
        }

        @media (max-width: 768px) {
            .meta-label {
                width: 100px;
                font-size: 0.9rem;
            }

            #buktiPortofolioModal .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }

            .bukti-item .btn {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="main-card">
        <div class="card-header-custom mb-4">
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
                        <i class="bi bi-people-fill me-2"></i>Detail Kelompok Kerja
                    </h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 flex-wrap">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema
                                    Sertifikasi</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ Str::limit($scheme->nama, 20) }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}">Kelompok Kerja</a>
                            </li>
                            <li class="breadcrumb-item active">{{ Str::limit($kelompokKerja->nama_kelompok, 20) }}</li>
                        </ol>
                    </nav>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.schemes.kelompok-kerja.edit', [$scheme, $kelompokKerja]) }}"
                        class="btn btn-outline-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <div class="dropdown">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-gear"></i> Action
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('admin.schemes.kelompok-kerja.manage-unit-kompetensi', [$scheme, $kelompokKerja]) }}">
                                    <i class="bi bi-clipboard-check me-2"></i> Kelola Unit Kompetensi
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <button class="dropdown-item" onclick="toggleStatus({{ $kelompokKerja->id }})">
                                    <i class="bi bi-toggle-{{ $kelompokKerja->is_active ? 'off' : 'on' }} me-2"></i>
                                    {{ $kelompokKerja->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" onclick="duplicateKelompok({{ $kelompokKerja->id }})">
                                    <i class="bi bi-copy me-2"></i> Duplikasi
                                </button>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <button class="dropdown-item text-danger"
                                    onclick="deleteKelompok({{ $kelompokKerja->id }})">
                                    <i class="bi bi-trash me-2"></i> Hapus
                                </button>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ url()->previous() }}"
                        class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body m-3">
            <!-- Kelompok Kerja Information -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-people me-2"></i>Informasi Kelompok Kerja
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="meta-item">
                                <span class="meta-label">Nama Kelompok:</span>
                                <strong>{{ $kelompokKerja->nama_kelompok }}</strong>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">Status:</span>
                                <span class="badge bg-{{ $kelompokKerja->is_active ? 'success' : 'secondary' }}">
                                    <i class="bi bi-{{ $kelompokKerja->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                    {{ $kelompokKerja->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            @if ($kelompokKerja->deskripsi)
                                <div class="meta-item">
                                    <span class="meta-label">Deskripsi:</span>
                                    <div class="mt-2 text-muted">
                                        {{ $kelompokKerja->deskripsi }}
                                    </div>
                                </div>
                            @endif

                            <div class="meta-item">
                                <span class="meta-label">Skema Sertifikasi:</span>
                                <a href="{{ route('admin.certification-schemes.show', $scheme) }}"
                                    class="text-decoration-none">
                                    {{ $scheme->nama }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Statistics -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="card stat-card primary">
                                <div class="card-body text-center">
                                    <h2 class="text-primary mb-1">{{ $kelompokKerja->unitKompetensis->count() }}</h2>
                                    <p class="text-muted mb-0">Total Unit Kompetensi</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="card stat-card success">
                                <div class="card-body text-center">
                                    <h2 class="text-success mb-1">
                                        {{ $kelompokKerja->unitKompetensis->where('pivot.is_active', true)->count() }}
                                    </h2>
                                    <p class="text-muted mb-0">Unit Aktif</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="card stat-card info">
                                <div class="card-body text-center">
                                    <h2 class="text-info mb-1">
                                        {{ $kelompokKerja->unitKompetensis->sum(fn($unit) => $unit->elemenKompetensis->count()) }}
                                    </h2>
                                    <p class="text-muted mb-0">Total Elemen Kompetensi</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card stat-card warning">
                                <div class="card-body text-center">
                                    <h2 class="text-warning mb-1">
                                        {{ $kelompokKerja->unitKompetensis->sum(fn($unit) => $unit->elemenKompetensis->sum(fn($e) => $e->kriteriaKerjas->count())) }}
                                    </h2>
                                    <p class="text-muted mb-0">Total Kriteria Kerja</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-divider"></div>

            <!-- Unit Kompetensi Section -->
            <div class="card">
                <div
                    class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <h6 class="mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>Unit Kompetensi dalam Kelompok Kerja
                    </h6>
                    <a href="{{ route('admin.schemes.kelompok-kerja.manage-unit-kompetensi', [$scheme, $kelompokKerja]) }}"
                        class="btn btn-sm btn-primary">
                        <i class="bi bi-gear"></i> Kelola Unit Kompetensi
                    </a>
                </div>

                @if ($kelompokKerja->unitKompetensis->count() > 0)
                    <!-- Filter Tabs -->
                    <div class="card-body pb-0">
                        <ul class="nav nav-tabs filter-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab"
                                    data-bs-target="#all-units" type="button" role="tab">
                                    Semua ({{ $kelompokKerja->unitKompetensis->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="active-tab" data-bs-toggle="tab"
                                    data-bs-target="#active-units" type="button" role="tab">
                                    Aktif ({{ $kelompokKerja->unitKompetensis->where('pivot.is_active', true)->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="inactive-tab" data-bs-toggle="tab"
                                    data-bs-target="#inactive-units" type="button" role="tab">
                                    Nonaktif
                                    ({{ $kelompokKerja->unitKompetensis->where('pivot.is_active', false)->count() }})
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- All Units -->
                            <div class="tab-pane fade show active" id="all-units" role="tabpanel">
                                <div class="row">
                                    @foreach ($kelompokKerja->unitKompetensis->sortBy('pivot.sort_order') as $unit)
                                        @include('admin.kelompok-kerja.partials.unit-card', [
                                            'unit' => $unit,
                                        ])
                                    @endforeach
                                </div>
                            </div>

                            <!-- Active Units -->
                            <div class="tab-pane fade" id="active-units" role="tabpanel">
                                <div class="row">
                                    @forelse($kelompokKerja->unitKompetensis->where('pivot.is_active', true)->sortBy('pivot.sort_order') as $unit)
                                        @include('admin.kelompok-kerja.partials.unit-card', [
                                            'unit' => $unit,
                                        ])
                                    @empty
                                        <div class="col-12">
                                            <div class="text-center py-4">
                                                <i class="bi bi-info-circle text-muted fs-1"></i>
                                                <p class="text-muted mt-2">Belum ada unit kompetensi aktif dalam kelompok
                                                    kerja ini.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Inactive Units -->
                            <div class="tab-pane fade" id="inactive-units" role="tabpanel">
                                <div class="row">
                                    @forelse($kelompokKerja->unitKompetensis->where('pivot.is_active', false)->sortBy('pivot.sort_order') as $unit)
                                        @include('admin.kelompok-kerja.partials.unit-card', [
                                            'unit' => $unit,
                                            'inactive' => true,
                                        ])
                                    @empty
                                        <div class="col-12">
                                            <div class="text-center py-4">
                                                <i class="bi bi-check-circle text-success fs-1"></i>
                                                <p class="text-muted mt-2">Semua unit kompetensi dalam kelompok kerja ini
                                                    sudah aktif!</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-clipboard-check"></i>
                        <h5>Belum ada Unit Kompetensi</h5>
                        <p>Kelompok kerja ini belum memiliki unit kompetensi. Mulai dengan menambahkan unit kompetensi.</p>
                    </div>
                @endif
            </div>

            <div class="section-divider"></div>

            <!-- Bukti Portofolio Section -->
            <div class="card">
                <div
                    class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <h6 class="mb-0">
                        <i class="bi bi-folder-check me-2"></i>Bukti Portofolio
                        @if ($kelompokKerja->buktiPortofolios->where('dependency_type', '!=', 'standalone')->count() > 0)
                            <span class="badge bg-info ms-2">Dengan Dependensi</span>
                        @endif
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#buktiPortofolioModal">
                        <i class="bi bi-plus-lg"></i> Kelola Bukti Portofolio
                    </button>
                </div>

                @if ($kelompokKerja->buktiPortofolios->count() > 0)
                    <div class="card-body">
                        <!-- Dependency Groups Display -->
                        @php
                            $groupedBukti = $kelompokKerja->buktiPortofolios->groupBy('group_identifier');
                            $standaloneItems = $groupedBukti->pull('') ?? collect();
                            $hasGroups = $groupedBukti->count() > 0;
                        @endphp

                        @if ($hasGroups)
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-collection me-2"></i>Grup Bukti Portofolio
                                </h6>

                                @foreach ($groupedBukti as $groupName => $groupItems)
                                    @if ($groupName)
                                        <!-- Only show named groups -->
                                        <div class="card mb-3 border-start border-4 border-info">
                                            <div class="card-header bg-light py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-collection-fill text-info me-2"></i>
                                                        {{ $groupName }}
                                                    </h6>
                                                    <div class="d-flex gap-2">
                                                        @php
                                                            $groupDependencyType =
                                                                $groupItems->first()->dependency_type ?? 'standalone';
                                                        @endphp
                                                        @if ($groupDependencyType === 'required_with')
                                                            <span class="badge bg-success">AND - Harus Bersamaan</span>
                                                        @elseif($groupDependencyType === 'optional_with')
                                                            <span class="badge bg-info">OR - Dapat Terpisah</span>
                                                        @elseif($groupDependencyType === 'exclusive')
                                                            <span class="badge bg-warning">EXCLUSIVE - Tidak
                                                                Bersamaan</span>
                                                        @endif
                                                        <small class="text-muted">{{ $groupItems->count() }} item</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    @foreach ($groupItems->sortBy('sort_order') as $bukti)
                                                        <div class="col-lg-6 mb-2">
                                                            <div class="card border-0 bg-light h-100">
                                                                <div class="card-body p-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-start mb-2">
                                                                        <small class="text-muted">#
                                                                            {{ $bukti->sort_order }}</small>
                                                                        <div class="d-flex gap-1">
                                                                            <span
                                                                                class="badge bg-{{ $bukti->is_active ? 'success' : 'secondary' }}">
                                                                                {{ $bukti->is_active ? 'Aktif' : 'Nonaktif' }}
                                                                            </span>
                                                                            @if ($bukti->dependency_type !== 'standalone')
                                                                                <span class="badge bg-outline-primary">
                                                                                    <i class="bi bi-diagram-3"></i>
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <p class="card-text mb-2">
                                                                        {{ Str::limit($bukti->bukti_portofolio, 100) }}</p>

                                                                    @if ($bukti->dependency_rules && count($bukti->dependency_rules) > 0)
                                                                        <div class="dependency-info small text-muted">
                                                                            <i class="bi bi-link-45deg me-1"></i>
                                                                            @if (isset($bukti->dependency_rules['required_bukti_ids']))
                                                                                Terhubung dengan
                                                                                {{ count($bukti->dependency_rules['required_bukti_ids']) }}
                                                                                bukti lain
                                                                            @elseif(isset($bukti->dependency_rules['exclusive_bukti_ids']))
                                                                                Tidak boleh dengan
                                                                                {{ count($bukti->dependency_rules['exclusive_bukti_ids']) }}
                                                                                bukti lain
                                                                            @endif
                                                                        </div>
                                                                    @endif

                                                                    <div class="mt-2 d-flex gap-1">
                                                                        <button class="btn btn-outline-warning btn-sm"
                                                                            onclick="editBukti({{ $bukti->id }}, `{{ addslashes($bukti->bukti_portofolio) }}`, {{ $bukti->is_active ? 'true' : 'false' }})">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </button>
                                                                        <button class="btn btn-outline-danger btn-sm"
                                                                            onclick="deleteBukti({{ $bukti->id }}, `{{ addslashes($bukti->bukti_portofolio) }}`)">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if ($standaloneItems->count() > 0)
                            <div class="mb-3">
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-file-text me-2"></i>Bukti Portofolio Mandiri
                                </h6>
                                <div class="row">
                                    @foreach ($standaloneItems->sortBy('sort_order') as $bukti)
                                        <div class="col-lg-6 mb-3">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <small class="text-muted"># {{ $bukti->sort_order }}</small>
                                                        <span
                                                            class="badge bg-{{ $bukti->is_active ? 'success' : 'secondary' }}">
                                                            {{ $bukti->is_active ? 'Aktif' : 'Nonaktif' }}
                                                        </span>
                                                    </div>
                                                    <p class="card-text mb-2">
                                                        {{ Str::limit($bukti->bukti_portofolio, 100) }}</p>
                                                    <div class="text-muted small mb-2">
                                                        <i class="bi bi-calendar-plus"></i>
                                                        {{ $bukti->created_at->format('d M Y') }}
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent border-0">
                                                    <div class="d-flex gap-1">
                                                        <button class="btn btn-outline-warning btn-sm"
                                                            onclick="editBukti({{ $bukti->id }}, `{{ addslashes($bukti->bukti_portofolio) }}`, {{ $bukti->is_active ? 'true' : 'false' }})">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm"
                                                            onclick="deleteBukti({{ $bukti->id }}, `{{ addslashes($bukti->bukti_portofolio) }}`)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="bi bi-folder-x text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">Belum ada Bukti Portofolio</h5>
                        <p class="text-muted">Tambahkan bukti portofolio untuk melengkapi kelompok kerja ini.</p>
                    </div>
                @endif
            </div>

            <!-- Metadata and Actions -->
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-info-circle me-2"></i>Informasi Tambahan
                            </h6>
                            <div class="meta-item">
                                <span class="meta-label">Dibuat:</span>
                                <strong>{{ $kelompokKerja->created_at->format('d M Y, H:i') }}</strong>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Diperbarui:</span>
                                <strong>{{ $kelompokKerja->updated_at->format('d M Y, H:i') }}</strong>
                            </div>
                            @if ($kelompokKerja->updated_at != $kelompokKerja->created_at)
                                <div class="meta-item">
                                    <span class="meta-label">Terakhir diubah:</span>
                                    <span class="text-muted">{{ $kelompokKerja->updated_at->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteKelompokModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Kelompok Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kelompok kerja
                        <strong>{{ $kelompokKerja->nama_kelompok }}</strong>?
                    </p>
                    <p class="text-danger">
                        <strong>Peringatan:</strong> Semua relasi dengan unit kompetensi akan ikut terhapus, tetapi unit
                        kompetensi itu sendiri tidak akan dihapus.
                    </p>
                    @if ($kelompokKerja->unitKompetensis->count() > 0)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Kelompok kerja ini memiliki <strong>{{ $kelompokKerja->unitKompetensis->count() }} unit
                                kompetensi</strong> yang terkait.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteKelompokForm" method="POST"
                        action="{{ route('admin.schemes.kelompok-kerja.destroy', [$scheme, $kelompokKerja]) }}"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus Kelompok Kerja</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove Unit from Kelompok Modal -->
    <div class="modal fade" id="removeUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Lepas Unit dari Kelompok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin melepas unit kompetensi ini dari kelompok kerja?</p>
                    <p class="text-warning">
                        <strong>Catatan:</strong> Unit kompetensi akan dilepas dari kelompok kerja ini, tetapi unit itu
                        sendiri tidak akan dihapus.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="removeUnitForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning">Lepas dari Kelompok</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Duplicate Kelompok Modal -->
    <div class="modal fade" id="duplicateKelompokModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Duplikasi Kelompok Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="duplicateKelompokForm" method="POST"
                        action="{{ route('admin.schemes.kelompok-kerja.duplicate', [$scheme, $kelompokKerja]) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_kelompok_baru" class="form-label">Nama Kelompok Kerja Baru</label>
                            <input type="text" class="form-control" id="nama_kelompok_baru" name="nama_kelompok"
                                value="{{ $kelompokKerja->nama_kelompok }} (Copy)" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi_baru" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_baru" name="deskripsi" rows="3">{{ $kelompokKerja->deskripsi }}</textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="copy_units" name="copy_units" checked>
                            <label class="form-check-label" for="copy_units">
                                Salin juga unit kompetensi yang terkait ({{ $kelompokKerja->unitKompetensis->count() }}
                                unit)
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="duplicateKelompokForm" class="btn btn-primary">
                        <i class="bi bi-copy me-1"></i> Duplikasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bukti Portofolio Modal -->
    <div class="modal fade" id="buktiPortofolioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-folder-plus me-2"></i>Kelola Bukti Portofolio
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-4" id="buktiTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="add-tab" data-bs-toggle="tab"
                                data-bs-target="#add-bukti" type="button" role="tab">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Bukti
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="groups-tab" data-bs-toggle="tab"
                                data-bs-target="#manage-groups" type="button" role="tab">
                                <i class="bi bi-collection me-2"></i>Kelola Grup
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dependency-tab" data-bs-toggle="tab"
                                data-bs-target="#manage-dependencies" type="button" role="tab">
                                <i class="bi bi-diagram-3 me-2"></i>Kelola Dependensi
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Add Bukti Tab -->
                        <div class="tab-pane fade show active" id="add-bukti" role="tabpanel">
                            <form id="buktiPortofolioForm" novalidate>
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>
                                                    Tambah Bukti Portofolio Baru
                                                </h6>
                                                <small class="text-muted">Total: <span id="totalCount">0</span>
                                                    item</small>
                                            </div>

                                            <div id="buktiContainer">
                                                <!-- Dynamic bukti items will be inserted here -->
                                            </div>

                                            <button type="button" class="btn btn-outline-primary w-100 mb-3"
                                                id="addBukti">
                                                <i class="bi bi-plus-lg me-2"></i>
                                                Tambah Bukti Portofolio
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="mb-3">
                                                    <i class="bi bi-clipboard-check me-2"></i>
                                                    Ringkasan
                                                </h6>

                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Total Bukti Baru:</span>
                                                    <strong><span id="summaryNewCount">0</span></strong>
                                                </div>

                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Bukti Aktif:</span>
                                                    <strong><span id="summaryActiveCount">0</span></strong>
                                                </div>

                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Bukti Tidak Aktif:</span>
                                                    <strong><span id="summaryInactiveCount">0</span></strong>
                                                </div>

                                                <div class="d-flex justify-content-between mb-3">
                                                    <span>Dengan Dependensi:</span>
                                                    <strong><span id="summaryWithDependency">0</span></strong>
                                                </div>

                                                <hr>

                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-primary" id="saveAllBukti">
                                                        <i class="bi bi-check-lg me-2"></i>
                                                        Simpan Semua Bukti
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        id="resetForm">
                                                        <i class="bi bi-arrow-clockwise me-2"></i>
                                                        Reset Form
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Manage Groups Tab -->
                        <div class="tab-pane fade" id="manage-groups" role="tabpanel">
                            <div class="mb-3">
                                <h6>Grup Bukti Portofolio</h6>
                                <p class="text-muted">Kelola pengelompokan bukti portofolio untuk memudahkan penggunaan.
                                </p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Buat Grup Baru</h6>
                                        </div>
                                        <div class="card-body">
                                            <form id="createGroupForm">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Grup</label>
                                                    <input type="text" class="form-control" id="groupName"
                                                        placeholder="Contoh: Sertifikat Profesional">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tipe Dependensi</label>
                                                    <select class="form-select" id="groupDependencyType">
                                                        <option value="required_with">Harus dipilih bersamaan (AND)
                                                        </option>
                                                        <option value="optional_with">Dapat dipilih terpisah (OR)</option>
                                                        <option value="exclusive">Tidak boleh bersamaan (EXCLUSIVE)
                                                        </option>
                                                    </select>
                                                    <div class="form-text">
                                                        <small>
                                                            <strong>AND:</strong> Semua bukti dalam grup harus dipilih
                                                            bersamaan<br>
                                                            <strong>OR:</strong> Bukti dalam grup dapat dipilih
                                                            sendiri-sendiri<br>
                                                            <strong>EXCLUSIVE:</strong> Hanya satu bukti dalam grup yang
                                                            boleh dipilih
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Pilih Bukti Portofolio</label>
                                                    <div id="availableBuktiList" class="border rounded p-3"
                                                        style="max-height: 200px; overflow-y: auto;">
                                                        <div class="text-center text-muted">
                                                            <i class="bi bi-hourglass-split"></i> Memuat bukti
                                                            portofolio...
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-plus"></i> Buat Grup
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Grup Yang Ada</h6>
                                        </div>
                                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                            <div id="existingGroupsList">
                                                <div class="text-center text-muted">
                                                    <i class="bi bi-hourglass-split"></i> Memuat grup...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manage Dependencies Tab -->
                        {{-- <div class="tab-pane fade" id="manage-dependencies" role="tabpanel">
                            <div class="mb-3">
                                <h6>Kelola Dependensi Bukti Portofolio</h6>
                                <p class="text-muted">Atur hubungan antar bukti portofolio untuk validasi yang lebih baik.
                                </p>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Daftar Bukti Portofolio</h6>
                                                <button type="button" class="btn btn-sm btn-success"
                                                    id="validateAllDependencies">
                                                    <i class="bi bi-shield-check me-1"></i>Validasi Semua
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="dependencyList">
                                                <div class="text-center text-muted">
                                                    <i class="bi bi-hourglass-split"></i> Memuat dependensi...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Informasi Dependensi</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-success me-2">AND</span>
                                                    <small>Harus bersamaan</small>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-info me-2">OR</span>
                                                    <small>Dapat terpisah</small>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-warning me-2">EXCLUSIVE</span>
                                                    <small>Tidak bersamaan</small>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-secondary me-2">STANDALONE</span>
                                                    <small>Mandiri</small>
                                                </div>
                                            </div>

                                            <hr>

                                            <div id="dependencyStats">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Total Bukti:</span>
                                                    <strong><span id="totalBuktiCount">0</span></strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Dalam Grup:</span>
                                                    <strong><span id="groupedBuktiCount">0</span></strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Mandiri:</span>
                                                    <strong><span id="standaloneBuktiCount">0</span></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template for dynamic bukti items -->
    <template id="buktiItemTemplate">
        <div class="card bukti-item mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary bukti-counter">1</span>
                    <button type="button" class="btn btn-danger btn-sm remove-bukti">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Bukti Portofolio *</label>
                    <textarea class="form-control bukti-text" rows="4" placeholder="Masukkan deskripsi bukti portofolio..."
                        required maxlength="2000"></textarea>
                    <div class="form-text">
                        <span class="char-count">0</span>/2000 karakter
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input bukti-status" type="checkbox" checked>
                    <label class="form-check-label">
                        <span class="status-text">Aktif</span>
                    </label>
                </div>
            </div>
        </div>
    </template>

    <!-- Template for bukti checkbox in group creation -->
    <template id="buktiCheckboxTemplate">
        <div class="form-check mb-2">
            <input class="form-check-input bukti-checkbox" type="checkbox" value="">
            <label class="form-check-label bukti-label">
                <!-- Bukti description will be inserted here -->
            </label>
        </div>
    </template>

    <!-- Template for existing group item -->
    <template id="groupItemTemplate">
        <div class="card mb-3 group-item">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0 group-title"></h6>
                    <div class="d-flex gap-2">
                        <span class="badge group-type-badge"></span>
                        <button class="btn btn-outline-danger btn-sm remove-group" data-group="">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="group-items small text-muted">
                    <!-- Group items will be listed here -->
                </div>
            </div>
        </div>
    </template>

    <!-- Template for dependency item -->
    <template id="dependencyItemTemplate">
        <div class="card mb-3 dependency-item">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge me-2 dependency-type-badge"></span>
                            <small class="text-muted dependency-order"># 1</small>
                        </div>
                        <p class="mb-1 dependency-text"></p>
                        <div class="dependency-info small text-muted"></div>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        <span class="badge dependency-status-badge"></span>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        window.schemeId = {{ $scheme->id }};
        window.kelompokId = {{ $kelompokKerja->id }};
        let currentDeleteBuktiId = null;

        // CSRF Token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            '{{ csrf_token() }}';

        // Utility function for showing alerts
        function showAlert(type, message) {
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
            document.body.appendChild(alertContainer);

            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Enhanced Bukti Portofolio Manager with Group and Dependency Management
        class BuktiPortofolioManager {
            constructor() {
                this.buktiCount = 0;
                this.initialized = false;
                this.availableBukti = [];
                this.existingGroups = [];
            }

            init() {
                if (this.initialized) return;

                const requiredElements = [
                    'addBukti', 'resetForm', 'buktiPortofolioForm', 'buktiContainer',
                    'totalCount', 'summaryNewCount', 'summaryActiveCount',
                    'summaryInactiveCount', 'saveAllBukti'
                ];

                const missingElements = requiredElements.filter(id => !document.getElementById(id));

                if (missingElements.length > 0) {
                    console.warn('Missing required elements:', missingElements);
                    return false;
                }

                this.bindEvents();
                this.addFirstBukti();
                this.updateSummary();
                this.initialized = true;

                return true;
            }

            bindEvents() {
                // Add bukti button
                const addBtn = document.getElementById('addBukti');
                if (addBtn) {
                    addBtn.addEventListener('click', () => this.addBuktiItem());
                }

                // Reset form button
                const resetBtn = document.getElementById('resetForm');
                if (resetBtn) {
                    resetBtn.addEventListener('click', () => this.resetForm());
                }

                // Form submission
                const form = document.getElementById('buktiPortofolioForm');
                if (form) {
                    form.addEventListener('submit', (e) => this.handleFormSubmit(e));
                }

                // Dynamic event delegation for bukti items
                const container = document.getElementById('buktiContainer');
                if (container) {
                    container.addEventListener('click', (e) => {
                        if (e.target.closest('.remove-bukti')) {
                            this.removeBuktiItem(e.target.closest('.bukti-item'));
                        }
                    });

                    container.addEventListener('input', (e) => {
                        if (e.target.classList.contains('bukti-text')) {
                            this.updateCharacterCount(e.target);
                            this.updateSummary();
                        }
                    });

                    container.addEventListener('change', (e) => {
                        if (e.target.classList.contains('bukti-status')) {
                            this.updateStatusText(e.target);
                            this.updateSummary();
                        }
                    });
                }

                // Group management events
                const createGroupForm = document.getElementById('createGroupForm');
                if (createGroupForm) {
                    createGroupForm.addEventListener('submit', (e) => this.handleCreateGroup(e));
                }

                // Tab switch events
                const groupsTab = document.getElementById('groups-tab');
                if (groupsTab) {
                    groupsTab.addEventListener('shown.bs.tab', () => this.loadGroupsData());
                }

                const dependencyTab = document.getElementById('dependency-tab');
                if (dependencyTab) {
                    dependencyTab.addEventListener('shown.bs.tab', () => this.loadDependencyData());
                }

                // Validation button
                const validateBtn = document.getElementById('validateAllDependencies');
                if (validateBtn) {
                    validateBtn.addEventListener('click', () => this.validateAllDependencies());
                }
            }

            addFirstBukti() {
                if (this.buktiCount === 0) {
                    this.addBuktiItem();
                }
            }

            addBuktiItem() {
                const template = document.getElementById('buktiItemTemplate');
                const container = document.getElementById('buktiContainer');

                if (!template || !container) {
                    console.error('Template or container not found');
                    return;
                }

                const clone = template.content.cloneNode(true);

                this.buktiCount++;

                const counter = clone.querySelector('.bukti-counter');
                if (counter) counter.textContent = this.buktiCount;

                const buktiItem = clone.querySelector('.bukti-item');
                if (buktiItem) {
                    buktiItem.setAttribute('data-bukti-index', this.buktiCount);
                }

                container.appendChild(clone);
                this.updateSummary();

                // Focus on new textarea
                const textarea = container.lastElementChild.querySelector('.bukti-text');
                if (textarea) textarea.focus();
            }

            removeBuktiItem(buktiItem) {
                if (!buktiItem) return;

                const items = document.querySelectorAll('.bukti-item');
                if (items.length > 1) {
                    buktiItem.remove();
                    this.reorderCounters();
                    this.updateSummary();
                } else {
                    showAlert('warning', 'Minimal harus ada satu bukti portofolio.');
                }
            }

            reorderCounters() {
                const items = document.querySelectorAll('.bukti-item');
                items.forEach((item, index) => {
                    const counter = item.querySelector('.bukti-counter');
                    if (counter) counter.textContent = index + 1;
                    item.setAttribute('data-bukti-index', index + 1);
                });
                this.buktiCount = items.length;
            }

            updateCharacterCount(textarea) {
                if (!textarea) return;

                const counter = textarea.parentElement.querySelector('.char-count');
                if (counter) {
                    const length = textarea.value.length;
                    counter.textContent = length;

                    if (length > 1800) {
                        counter.style.color = '#dc3545';
                    } else if (length > 1500) {
                        counter.style.color = '#fd7e14';
                    } else {
                        counter.style.color = '#6c757d';
                    }
                }
            }

            updateStatusText(checkbox) {
                if (!checkbox) return;

                const statusText = checkbox.parentElement.querySelector('.status-text');
                if (statusText) {
                    statusText.textContent = checkbox.checked ? 'Aktif' : 'Nonaktif';
                }
            }

            updateSummary() {
                const items = document.querySelectorAll('.bukti-item');
                let totalCount = 0;
                let activeCount = 0;
                let inactiveCount = 0;

                items.forEach(item => {
                    const textarea = item.querySelector('.bukti-text');
                    if (textarea && textarea.value.trim().length >= 5) {
                        totalCount++;
                        const checkbox = item.querySelector('.bukti-status');
                        if (checkbox && checkbox.checked) {
                            activeCount++;
                        } else {
                            inactiveCount++;
                        }
                    }
                });

                const elements = {
                    totalCount: document.getElementById('totalCount'),
                    summaryNewCount: document.getElementById('summaryNewCount'),
                    summaryActiveCount: document.getElementById('summaryActiveCount'),
                    summaryInactiveCount: document.getElementById('summaryInactiveCount'),
                    saveButton: document.getElementById('saveAllBukti')
                };

                if (elements.totalCount) elements.totalCount.textContent = items.length;
                if (elements.summaryNewCount) elements.summaryNewCount.textContent = totalCount;
                if (elements.summaryActiveCount) elements.summaryActiveCount.textContent = activeCount;
                if (elements.summaryInactiveCount) elements.summaryInactiveCount.textContent = inactiveCount;

                if (elements.saveButton) {
                    elements.saveButton.disabled = totalCount === 0;
                }
            }

            resetForm() {
                if (confirm('Apakah Anda yakin ingin mereset form? Semua data yang belum disimpan akan hilang.')) {
                    const container = document.getElementById('buktiContainer');
                    if (container) {
                        container.innerHTML = '';
                        this.buktiCount = 0;
                        this.addFirstBukti();
                    }
                }
            }

            collectFormData() {
                const items = document.querySelectorAll('.bukti-item');
                const data = [];

                items.forEach((item, index) => {
                    const textarea = item.querySelector('.bukti-text');
                    const checkbox = item.querySelector('.bukti-status');

                    if (textarea && checkbox) {
                        const text = textarea.value.trim();

                        if (text.length >= 5) {
                            data.push({
                                bukti_portofolio: text,
                                is_active: checkbox.checked,
                                sort_order: index + 1,
                                dependency_type: 'standalone',
                                dependency_rules: null,
                                group_identifier: null
                            });
                        }
                    }
                });

                return data;
            }

            async handleFormSubmit(e) {
                e.preventDefault();

                const data = this.collectFormData();

                if (data.length === 0) {
                    showAlert('warning',
                        'Harap tambahkan minimal satu bukti portofolio yang valid (minimal 5 karakter).');
                    return;
                }

                for (let item of data) {
                    if (item.bukti_portofolio.length < 5) {
                        showAlert('warning', 'Setiap bukti portofolio harus memiliki minimal 5 karakter.');
                        return;
                    }
                }

                const saveButton = document.getElementById('saveAllBukti');
                if (!saveButton) return;

                const originalText = saveButton.innerHTML;
                saveButton.disabled = true;
                saveButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';

                try {
                    const response = await fetch(
                        `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                bukti_portofolios: data
                            })
                        });

                    const result = await response.json();

                    if (result.success) {
                        showAlert('success', result.message);
                        const modalElement = document.getElementById('buktiPortofolioModal');
                        if (modalElement) {
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) modalInstance.hide();
                        }
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('danger', result.message || 'Terjadi kesalahan saat menyimpan data.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('danger', 'Terjadi kesalahan saat menyimpan data.');
                } finally {
                    saveButton.disabled = false;
                    saveButton.innerHTML = originalText;
                }
            }

            // Group Management Methods
            async loadGroupsData() {
                try {
                    await this.loadAvailableBukti();
                    await this.loadExistingGroups();
                } catch (error) {
                    console.error('Error loading groups data:', error);
                    showAlert('danger', 'Terjadi kesalahan saat memuat data grup.');
                }
            }

            async loadAvailableBukti() {
                const container = document.getElementById('availableBuktiList');
                if (!container) return;

                container.innerHTML =
                    '<div class="text-center text-muted"><i class="bi bi-hourglass-split"></i> Memuat...</div>';

                try {
                    const response = await fetch(
                        `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio/available`
                    );
                    const result = await response.json();

                    if (result.success) {
                        this.availableBukti = [...result.data.standalone, ...result.data.grouped.flatMap(g => g.items)];
                        this.renderAvailableBukti();
                    } else {
                        container.innerHTML =
                            '<div class="text-center text-muted">Tidak ada bukti portofolio tersedia.</div>';
                    }
                } catch (error) {
                    container.innerHTML =
                        '<div class="text-center text-danger">Terjadi kesalahan saat memuat data.</div>';
                    console.error('Error loading available bukti:', error);
                }
            }

            renderAvailableBukti() {
                const container = document.getElementById('availableBuktiList');
                if (!container) return;

                if (this.availableBukti.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted">Belum ada bukti portofolio aktif.</div>';
                    return;
                }

                const template = document.getElementById('buktiCheckboxTemplate');
                if (!template) return;

                container.innerHTML = '';

                this.availableBukti.forEach(bukti => {
                    if (!bukti.group_identifier) { // Only show standalone bukti
                        const clone = template.content.cloneNode(true);
                        const checkbox = clone.querySelector('.bukti-checkbox');
                        const label = clone.querySelector('.bukti-label');

                        if (checkbox && label) {
                            checkbox.value = bukti.id;
                            label.textContent = bukti.bukti_portofolio.length > 80 ?
                                bukti.bukti_portofolio.substring(0, 80) + '...' : bukti.bukti_portofolio;
                        }

                        container.appendChild(clone);
                    }
                });
            }

            async loadExistingGroups() {
                const container = document.getElementById('existingGroupsList');
                if (!container) return;

                container.innerHTML =
                    '<div class="text-center text-muted"><i class="bi bi-hourglass-split"></i> Memuat...</div>';

                try {
                    const response = await fetch(
                        `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio/available`
                    );
                    const result = await response.json();

                    if (result.success && result.data.grouped.length > 0) {
                        this.existingGroups = result.data.grouped;
                        this.renderExistingGroups();
                    } else {
                        container.innerHTML = '<div class="text-center text-muted">Belum ada grup yang dibuat.</div>';
                    }
                } catch (error) {
                    container.innerHTML =
                        '<div class="text-center text-danger">Terjadi kesalahan saat memuat grup.</div>';
                    console.error('Error loading existing groups:', error);
                }
            }

            renderExistingGroups() {
                const container = document.getElementById('existingGroupsList');
                const template = document.getElementById('groupItemTemplate');

                if (!container || !template) return;

                container.innerHTML = '';

                this.existingGroups.forEach(group => {
                    const clone = template.content.cloneNode(true);
                    const title = clone.querySelector('.group-title');
                    const badge = clone.querySelector('.group-type-badge');
                    const items = clone.querySelector('.group-items');
                    const removeBtn = clone.querySelector('.remove-group');

                    // Gunakan group_identifier sebagai nama grup
                    const groupName = group.group_identifier || 'Standalone';
                    if (title) title.textContent = groupName;

                    // Ambil dependency_type dari item pertama, atau default ke 'none'
                    const dependencyType = group.items.length > 0 ? group.items[0].dependency_type : 'none';
                    if (badge) {
                        const typeText = this.getDependencyTypeText(dependencyType);
                        badge.textContent = typeText.label;
                        badge.className = `badge ${typeText.class}`;
                    }

                    if (items) {
                        items.innerHTML = group.items.map(item =>
                            `• ${item.bukti_portofolio.length > 50 ? item.bukti_portofolio.substring(0, 50) + '...' : item.bukti_portofolio}`
                        ).join('<br>');
                    }

                    if (removeBtn) {
                        removeBtn.setAttribute('data-group', groupName);
                        removeBtn.addEventListener('click', () => this.removeGroup(groupName));
                    }

                    container.appendChild(clone);
                });
            }


            getDependencyTypeText(type) {
                switch (type) {
                    case 'required_with':
                        return {
                            label: 'AND - Harus Bersamaan', class: 'bg-success'
                        };
                    case 'optional_with':
                        return {
                            label: 'OR - Dapat Terpisah', class: 'bg-info'
                        };
                    case 'exclusive':
                        return {
                            label: 'EXCLUSIVE - Tidak Bersamaan', class: 'bg-warning'
                        };
                    default:
                        return {
                            label: 'STANDALONE', class: 'bg-secondary'
                        };
                }
            }

            async handleCreateGroup(e) {
                e.preventDefault();

                const groupName = document.getElementById('groupName')?.value.trim();
                const dependencyType = document.getElementById('groupDependencyType')?.value;
                const checkboxes = document.querySelectorAll('.bukti-checkbox:checked');

                if (!groupName) {
                    showAlert('warning', 'Nama grup harus diisi.');
                    return;
                }

                if (checkboxes.length < 2) {
                    showAlert('warning', 'Minimal pilih 2 bukti portofolio untuk membuat grup.');
                    return;
                }

                const buktiIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

                try {
                    const response = await fetch(
                        `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio/manage-groups`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                group_name: groupName,
                                dependency_type: dependencyType,
                                bukti_ids: buktiIds
                            })
                        });

                    const result = await response.json();

                    if (result.success) {
                        showAlert('success', result.message);
                        document.getElementById('createGroupForm').reset();
                        await this.loadGroupsData();
                    } else {
                        showAlert('danger', result.message || 'Terjadi kesalahan saat membuat grup.');
                    }
                } catch (error) {
                    console.error('Error creating group:', error);
                    showAlert('danger', 'Terjadi kesalahan saat membuat grup.');
                }
            }

            async removeGroup(groupName) {
                if (!confirm(`Apakah Anda yakin ingin menghapus grup "${groupName}"?`)) return;

                try {
                    const group = this.existingGroups.find(g => g.group_identifier === groupName);
                    console.log(group);
                    if (!group) return;

                    const buktiIds = group.items.map(item => item.id);

                    const response = await fetch(
                        `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio/remove-from-group`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                bukti_ids: buktiIds
                            })
                        });

                    const result = await response.json();

                    if (result.success) {
                        showAlert('success', result.message);
                        await this.loadGroupsData();
                    } else {
                        showAlert('danger', result.message || 'Terjadi kesalahan saat menghapus grup.');
                    }
                } catch (error) {
                    console.error('Error removing group:', error);
                    showAlert('danger', 'Terjadi kesalahan saat menghapus grup.');
                }
            }

            async loadDependencyData() {
                const container = document.getElementById('dependencyList');
                if (!container) return;

                container.innerHTML =
                    '<div class="text-center text-muted"><i class="bi bi-hourglass-split"></i> Memuat...</div>';

                try {
                    const response = await fetch(
                        `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio`);
                    const result = await response.json();

                    if (result.success) {
                        this.renderDependencyList(result.data);
                        this.updateDependencyStats(result.data);
                    } else {
                        container.innerHTML =
                            '<div class="text-center text-muted">Tidak ada data bukti portofolio.</div>';
                    }
                } catch (error) {
                    container.innerHTML =
                        '<div class="text-center text-danger">Terjadi kesalahan saat memuat data.</div>';
                    console.error('Error loading dependency data:', error);
                }
            }

            renderDependencyList(buktiList) {
                const container = document.getElementById('dependencyList');
                const template = document.getElementById('dependencyItemTemplate');

                if (!container || !template) return;

                container.innerHTML = '';

                buktiList.forEach((bukti, index) => {
                    const clone = template.content.cloneNode(true);

                    const typeBadge = clone.querySelector('.dependency-type-badge');
                    const order = clone.querySelector('.dependency-order');
                    const text = clone.querySelector('.dependency-text');
                    const info = clone.querySelector('.dependency-info');
                    const statusBadge = clone.querySelector('.dependency-status-badge');

                    if (typeBadge) {
                        const typeInfo = this.getDependencyTypeText(bukti.dependency_type);
                        typeBadge.textContent = typeInfo.label;
                        typeBadge.className = `badge me-2 ${typeInfo.class}`;
                    }

                    if (order) order.textContent = `# ${bukti.sort_order || index + 1}`;

                    if (text) {
                        text.textContent = bukti.bukti_portofolio.length > 100 ?
                            bukti.bukti_portofolio.substring(0, 100) + '...' : bukti.bukti_portofolio;
                    }

                    if (info && bukti.dependency_info) {
                        info.textContent = bukti.dependency_info.description || '';
                    }

                    if (statusBadge) {
                        statusBadge.textContent = bukti.is_active ? 'Aktif' : 'Nonaktif';
                        statusBadge.className = `badge ${bukti.is_active ? 'bg-success' : 'bg-secondary'}`;
                    }

                    container.appendChild(clone);
                });
            }

            updateDependencyStats(buktiList) {
                const totalElement = document.getElementById('totalBuktiCount');
                const groupedElement = document.getElementById('groupedBuktiCount');
                const standaloneElement = document.getElementById('standaloneBuktiCount');

                if (!totalElement || !groupedElement || !standaloneElement) return;

                const total = buktiList.length;
                const grouped = buktiList.filter(b => b.group_identifier).length;
                const standalone = total - grouped;

                totalElement.textContent = total;
                groupedElement.textContent = grouped;
                standaloneElement.textContent = standalone;
            }

            async validateAllDependencies() {
                try {
                    const selectedIds = [...document.querySelectorAll('.bukti-checkbox:checked')]
                        .map(cb => cb.value);

                    const response = await fetch(
                        `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio/validate-dependencies`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                selected_bukti_ids: selectedIds,
                            }),
                        }
                    );

                    const result = await response.json();

                    if (result.success) {
                        if (result.data.is_valid) {
                            showAlert('success', 'Semua dependensi valid.');
                        } else {
                            showAlert('danger', 'Ada pelanggaran dependensi.');
                            console.table(result.data.validation_results);
                        }
                    } else {
                        showAlert('danger', result.message || 'Validasi gagal.');
                    }
                } catch (error) {
                    console.error('Error validating dependencies:', error);
                    showAlert('danger', 'Terjadi kesalahan saat validasi dependensi.');
                }
            }

        }

        // Individual bukti management functions
        function editBukti(buktiId, buktiText, isActive) {
            const modalElement = document.getElementById('editBuktiModal');
            const idField = document.getElementById('editBuktiId');
            const textField = document.getElementById('editBuktiText');
            const statusField = document.getElementById('editBuktiStatus');

            if (modalElement && idField && textField && statusField) {
                idField.value = buktiId;
                textField.value = buktiText;
                statusField.checked = isActive;

                updateCharCount('editBuktiText', 'editCharCount');

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        function deleteBukti(buktiId, buktiText) {
            currentDeleteBuktiId = buktiId;

            const previewElement = document.getElementById('deleteBuktiPreview');
            const modalElement = document.getElementById('deleteBuktiModal');

            if (previewElement && modalElement) {
                previewElement.textContent = buktiText.length > 100 ?
                    buktiText.substring(0, 100) + '...' : buktiText;

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        function updateCharCount(textareaId, counterId) {
            const textarea = document.getElementById(textareaId);
            const counter = document.getElementById(counterId);
            if (textarea && counter) {
                counter.textContent = textarea.value.length;
            }
        }

        // Kelompok Kerja functions
        function toggleStatus(kelompokId) {
            if (confirm('Apakah Anda yakin ingin mengubah status kelompok kerja ini?')) {
                fetch(`/admin/schemes/${window.schemeId}/kelompok-kerja/${kelompokId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('danger', 'Terjadi kesalahan saat mengubah status.');
                    });
            }
        }

        function toggleUnitStatus(unitId) {
            if (confirm('Apakah Anda yakin ingin mengubah status unit kompetensi ini dalam kelompok kerja?')) {
                fetch(`/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/toggle-unit-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            unit_id: unitId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('danger', 'Terjadi kesalahan saat mengubah status unit.');
                    });
            }
        }

        function deleteKelompok(kelompokId) {
            const modalElement = document.getElementById('deleteKelompokModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        function removeFromKelompok(unitId) {
            const modalElement = document.getElementById('removeUnitModal');
            const form = document.getElementById('removeUnitForm');

            if (modalElement && form) {
                form.action =
                    `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/remove-unit-kompetensi`;

                const existingInput = form.querySelector('input[name="unit_id"]');
                if (existingInput) existingInput.remove();

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'unit_id';
                input.value = unitId;
                form.appendChild(input);

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        function duplicateKelompok(kelompokId) {
            const modalElement = document.getElementById('duplicateKelompokModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        // Document ready initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF token if not exists
            if (!document.querySelector('meta[name="csrf-token"]')) {
                const meta = document.createElement('meta');
                meta.name = 'csrf-token';
                meta.content = '{{ csrf_token() }}';
                document.getElementsByTagName('head')[0].appendChild(meta);
            }

            // Character count for edit modal
            const editBuktiText = document.getElementById('editBuktiText');
            if (editBuktiText) {
                editBuktiText.addEventListener('input', function() {
                    updateCharCount('editBuktiText', 'editCharCount');
                });
            }

            // Edit bukti form submission
            const editBuktiForm = document.getElementById('editBuktiForm');
            if (editBuktiForm) {
                editBuktiForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const buktiId = document.getElementById('editBuktiId')?.value;
                    const buktiText = document.getElementById('editBuktiText')?.value?.trim();
                    const isActive = document.getElementById('editBuktiStatus')?.checked;

                    if (!buktiText || buktiText.length < 5) {
                        showAlert('warning', 'Deskripsi bukti minimal 5 karakter.');
                        return;
                    }

                    try {
                        const response = await fetch(
                            `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio/${buktiId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    bukti_portofolio: buktiText,
                                    is_active: isActive
                                })
                            });

                        const result = await response.json();

                        if (result.success) {
                            showAlert('success', result.message);
                            const modalElement = document.getElementById('editBuktiModal');
                            if (modalElement) {
                                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                if (modalInstance) modalInstance.hide();
                            }
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', result.message ||
                                'Terjadi kesalahan saat menyimpan perubahan.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showAlert('danger', 'Terjadi kesalahan saat menyimpan perubahan.');
                    }
                });
            }

            // Delete bukti confirmation
            const confirmDeleteBtn = document.getElementById('confirmDeleteBukti');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', async function() {
                    if (!currentDeleteBuktiId) return;

                    try {
                        const response = await fetch(
                            `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/bukti-portofolio/${currentDeleteBuktiId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                        const result = await response.json();

                        if (result.success) {
                            showAlert('success', result.message);
                            const modalElement = document.getElementById('deleteBuktiModal');
                            if (modalElement) {
                                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                if (modalInstance) modalInstance.hide();
                            }
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', result.message ||
                                'Terjadi kesalahan saat menghapus bukti.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showAlert('danger', 'Terjadi kesalahan saat menghapus bukti.');
                    } finally {
                        currentDeleteBuktiId = null;
                    }
                });
            }
        });

        // Initialize Bukti Manager when modal is shown
        const buktiModal = document.getElementById('buktiPortofolioModal');
        if (buktiModal) {
            buktiModal.addEventListener('shown.bs.modal', function() {
                if (window.buktiManager) {
                    window.buktiManager.initialized = false;
                }

                window.buktiManager = new BuktiPortofolioManager();

                setTimeout(() => {
                    if (!window.buktiManager.init()) {
                        console.error('Failed to initialize Bukti Manager - required elements missing');
                        showAlert('danger', 'Terjadi kesalahan saat memuat form. Silakan refresh halaman.');
                    }
                }, 100);
            });
        }
    </script>
@endpush
