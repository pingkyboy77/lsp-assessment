{{-- resources/views/admin/unit-kompetensi/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Unit Kompetensi - ' . $unit->kode_unit)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .document-item {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .document-item:hover {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-color: #5a5c69;
        }

        .document-item.required {
            border-left: 4px solid #dc3545;
        }

        .document-item.optional {
            border-left: 4px solid #ffc107;
        }

        .document-item.inactive {
            opacity: 0.6;
            background-color: #f8f9fa;
        }

        .sortable-handle {
            cursor: move;
            color: #858796;
        }

        .sortable-handle:hover {
            color: #5a5c69;
        }

        .requirement-badge {
            font-size: 0.75rem;
        }

        .template-item {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            transition: all 0.2s ease;
        }

        .template-item:hover {
            border-color: #5a5c69;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .template-item.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
    </style>
    <style>
        .kelompok-badge {
            transition: all 0.2s ease;
        }

        .kelompok-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            border-left: 4px solid;
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

        .section-divider {
            border-top: 2px solid #e9ecef;
            margin: 2rem 0;
        }

        .card-body>.row {
            margin-left: 0;
            margin-right: 0;
        }

        .card-body>.row>[class*="col-"] {
            padding-left: 15px;
            padding-right: 15px;
        }

        .elemen-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .elemen-card:hover {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .kriteria-item {
            background: #f8f9fc;
            border-radius: 0.25rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-left: 3px solid #e3e6f0;
            transition: all 0.2s ease;
        }

        .kriteria-item:hover {
            border-left-color: #5a5c69;
            background: #f2f3f8;
        }

        .add-form {
            background: #f8f9fc;
            border: 2px dashed #e3e6f0;
            border-radius: 0.35rem;
            padding: 1.5rem;
            margin: 1rem 0;
            transition: all 0.2s ease;
        }

        .add-form:hover {
            border-color: #5a5c69;
            background: #f2f3f8;
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .btn-group-actions {
            gap: 0.25rem;
        }

        .sortable-handle {
            cursor: move;
            color: #858796;
        }

        .sortable-handle:hover {
            color: #5a5c69;
        }

        .collapse-content {
            border-top: 1px solid #e3e6f0;
        }

        /* Responsive fixes */
        @media (max-width: 768px) {
            .breadcrumb-item {
                font-size: 0.85rem;
            }

            .btn-group .btn {
                font-size: 0.85rem;
                padding: 0.25rem 0.5rem;
            }

            .stat-card h2 {
                font-size: 1.5rem;
            }

            .add-form {
                padding: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="">
        <div class="main-card">
            <!-- Page Header -->
            <div class="card-header-custom mb-4">
                @if (session('success'))
                    <div class="alert-success-custom">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 text-dark fw-bold">
                            <i class="bi bi-list-check me-2"></i>Detail Unit Kompetensi
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
                                        href="{{ route('admin.schemes.unit-kompetensi.index', $scheme) }}">Unit
                                        Kompetensi</a></li>
                                <li class="breadcrumb-item active">{{ $unit->kode_unit }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.schemes.unit-kompetensi.edit', [$scheme, $unit]) }}"
                            class="btn btn-outline-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="{{ url()->previous() }}"
                            class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>

                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Unit Information -->
                <div class="row mb-4">
                    <div class="col-lg-8 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-clipboard-check me-2"></i>Informasi Unit
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Kode Unit:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-primary fs-6">{{ $unit->kode_unit }}</span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Judul Unit:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $unit->judul_unit }}
                                    </div>
                                </div>

                                @if ($unit->standar_kompetensi_kerja)
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Standar Kompetensi:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="text-muted">
                                                {{ $unit->standar_kompetensi_kerja }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-sm-4">
                                        <strong>Status:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-{{ $unit->is_active ? 'success' : 'secondary' }}">
                                            <i class="bi bi-{{ $unit->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                            {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
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
                                        <h2 class="text-primary mb-1">{{ $unit->elemenKompetensis->count() }}</h2>
                                        <p class="text-muted mb-0">Elemen Kompetensi</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="card stat-card success">
                                    <div class="card-body text-center">
                                        <h2 class="text-success mb-1">
                                            {{ $unit->elemenKompetensis->sum(fn($e) => $e->kriteriaKerjas->count()) }}</h2>
                                        <p class="text-muted mb-0">Kriteria Kerja</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card stat-card info">
                                    <div class="card-body text-center">
                                        <h2 class="text-info mb-1">{{ $unit->activeKelompokKerjas->count() }}</h2>
                                        <p class="text-muted mb-0">Kelompok Kerja</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kelompok Kerja Section (unchanged) -->
                @if ($unit->activeKelompokKerjas->count() > 0)
                    <div class="card m-3">
                        <div
                            class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                            <h6 class="mb-0">
                                <i class="bi bi-people me-2"></i>Kelompok Kerja
                                <span class="badge bg-info ms-2">{{ $unit->activeKelompokKerjas->count() }}</span>
                            </h6>
                            <div class="btn-group">
                                <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-gear"></i> Kelola di Kelompok Kerja
                                </a>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}">
                                            <i class="bi bi-people me-2"></i> Lihat Semua Kelompok Kerja
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <button class="dropdown-item" onclick="showKelompokKerjaStats()">
                                            <i class="bi bi-bar-chart me-2"></i> Statistik
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($unit->activeKelompokKerjas->take(6) as $kelompok)
                                    <div class="col-lg-6 mb-3">
                                        <div class="card border-info kelompok-badge h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1 me-2">
                                                        <h6 class="card-title mb-1">
                                                            <i class="bi bi-people text-info"></i>
                                                            {{ $kelompok->nama_kelompok }}
                                                            @if ($kelompok->pivot && $kelompok->pivot->is_active)
                                                                <span class="badge bg-success ms-2">Aktif</span>
                                                            @else
                                                                <span class="badge bg-secondary ms-2">Nonaktif</span>
                                                            @endif
                                                        </h6>
                                                        @if ($kelompok->deskripsi)
                                                            <p class="card-text text-muted small mb-2">
                                                                {{ Str::limit($kelompok->deskripsi, 80) }}
                                                            </p>
                                                        @endif
                                                        <div
                                                            class="d-flex flex-column flex-sm-row align-items-start text-muted small gap-2">
                                                            <span>
                                                                <i class="bi bi-sort-numeric-up"></i>
                                                                Urutan:
                                                                {{ $kelompok->pivot ? $kelompok->pivot->sort_order : '-' }}
                                                            </span>
                                                            <span>
                                                                <i class="bi bi-calendar-plus"></i>
                                                                {{ $kelompok->pivot ? $kelompok->pivot->created_at->format('d M Y') : '-' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column gap-1">
                                                        <a href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompok]) }}"
                                                            class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($unit->activeKelompokKerjas->count() > 6)
                                    <div class="col-12 text-center">
                                        <div class="alert alert-light border">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Dan {{ $unit->activeKelompokKerjas->count() - 6 }} kelompok kerja lainnya.
                                            <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}"
                                                class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="bi bi-eye"></i> Lihat Semua
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-3 pt-3 border-top">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Total: {{ $unit->kelompokKerjas->count() }} kelompok kerja
                                        ({{ $unit->activeKelompokKerjas->count() }} aktif)
                                    </small>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}"
                                            class="btn btn-outline-primary">
                                            <i class="bi bi-gear me-1"></i> Kelola Mapping
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mx-3">
                        <div
                            class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Kelompok Kerja</strong><br>
                                    Unit kompetensi ini belum dimasukkan ke dalam kelompok kerja manapun.
                                </div>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-plus"></i> Kelola di Kelompok Kerja
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="section-divider"></div>

                <!-- Elemen Kompetensi Section -->
                <div class="card m-3">
                    <div
                        class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                        <h6 class="mb-0">
                            <i class="bi bi-list-task me-2"></i>Elemen Kompetensi & Kriteria Kerja
                            <span class="badge bg-primary ms-2">{{ $unit->elemenKompetensis->count() }}</span>
                        </h6>
                        <button class="btn btn-sm btn-primary" onclick="showAddElemenForm()">
                            <i class="bi bi-plus"></i> Tambah Elemen
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Add Elemen Form -->
                        <div class="add-form" id="addElemenForm" style="display: none;">
                            <form onsubmit="submitElemenForm(event)">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="bi bi-plus-circle me-2"></i>Tambah Elemen Kompetensi
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="hideAddElemenForm()">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="kode_elemen"
                                                name="kode_elemen" placeholder="Kode Elemen" required maxlength="50">
                                            <label for="kode_elemen">Kode Elemen</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="judul_elemen"
                                                name="judul_elemen" placeholder="Judul Elemen" required maxlength="1000">
                                            <label for="judul_elemen">Judul Elemen</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active_elemen"
                                                name="is_active" checked>
                                            <label class="form-check-label" for="is_active_elemen">
                                                Aktif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-8 text-end">
                                        <button type="button" class="btn btn-outline-secondary me-2"
                                            onclick="hideAddElemenForm()">
                                            <i class="bi bi-x-circle"></i> Batal
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Simpan Elemen
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Elemen List -->
                        @if ($unit->elemenKompetensis->count() > 0)
                            <div id="elemenList">
                                @foreach ($unit->elemenKompetensis as $index => $elemen)
                                    <div class="elemen-card" data-elemen-id="{{ $elemen->id }}">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center flex-grow-1">
                                                    <i class="bi bi-grip-vertical sortable-handle me-2"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <span
                                                                class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                                            <span class="elemen-kode">{{ $elemen->kode_elemen }}</span>
                                                            <span
                                                                class="badge bg-{{ $elemen->is_active ? 'success' : 'secondary' }} ms-2">
                                                                {{ $elemen->is_active ? 'Aktif' : 'Nonaktif' }}
                                                            </span>
                                                        </h6>
                                                        <div class="elemen-judul">{{ $elemen->judul_elemen }}</div>
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary"
                                                        onclick="toggleKriteriaSection({{ $elemen->id }})">
                                                        <i class="bi bi-list-ul"></i>
                                                        Kriteria ({{ $elemen->kriteriaKerjas->count() }})
                                                    </button>
                                                    <button class="btn btn-outline-warning"
                                                        onclick="editElemen({{ $elemen->id }})">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger"
                                                        onclick="deleteElemen({{ $elemen->id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Form (Hidden by default) -->
                                        <div class="collapse-content" id="editElemenForm_{{ $elemen->id }}"
                                            style="display: none;">
                                            <form onsubmit="updateElemen(event, {{ $elemen->id }})">
                                                <div class="p-3 bg-light">
                                                    <h6 class="mb-3">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Elemen Kompetensi
                                                    </h6>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control"
                                                                    name="kode_elemen" value="{{ $elemen->kode_elemen }}"
                                                                    placeholder="Kode Elemen" required maxlength="50">
                                                                <label>Kode Elemen</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control"
                                                                    name="judul_elemen"
                                                                    value="{{ $elemen->judul_elemen }}"
                                                                    placeholder="Judul Elemen" required maxlength="1000">
                                                                <label>Judul Elemen</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="is_active"
                                                                {{ $elemen->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label">Aktif</label>
                                                        </div>
                                                        <div>
                                                            <button type="button" class="btn btn-outline-secondary me-2"
                                                                onclick="cancelEditElemen({{ $elemen->id }})">
                                                                <i class="bi bi-x-circle"></i> Batal
                                                            </button>
                                                            <button type="submit" class="btn btn-warning">
                                                                <i class="bi bi-check-circle"></i> Update
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Kriteria Section -->
                                        <div class="collapse-content" id="kriteriaSection_{{ $elemen->id }}"
                                            style="display: none;">
                                            <div class="p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-check-square me-2"></i>Kriteria Kerja
                                                        <span
                                                            class="badge bg-success ms-2">{{ $elemen->kriteriaKerjas->count() }}</span>
                                                    </h6>
                                                    <button class="btn btn-sm btn-success"
                                                        onclick="showAddKriteriaForm({{ $elemen->id }})">
                                                        <i class="bi bi-plus"></i> Tambah Kriteria
                                                    </button>
                                                </div>

                                                <!-- Add Kriteria Form -->
                                                <div class="add-form" id="addKriteriaForm_{{ $elemen->id }}"
                                                    style="display: none;">
                                                    <form onsubmit="submitKriteriaForm(event, {{ $elemen->id }})">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <h6 class="mb-0">
                                                                <i class="bi bi-plus-square me-2"></i>Tambah Kriteria Kerja
                                                            </h6>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                onclick="hideAddKriteriaForm({{ $elemen->id }})">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control"
                                                                        name="kode_kriteria" placeholder="Kode Kriteria"
                                                                        required maxlength="50">
                                                                    <label>Kode Kriteria</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <div class="form-floating">
                                                                    <input class="form-control" name="uraian_kriteria"
                                                                        placeholder="Uraian Kriteria"
                                                                        style="height: 80px;" required maxlength="2000">
                                                                    <label>Uraian Kriteria</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mt-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="is_active" checked>
                                                                <label class="form-check-label">Aktif</label>
                                                            </div>
                                                            <div>
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary me-2"
                                                                    onclick="hideAddKriteriaForm({{ $elemen->id }})">
                                                                    <i class="bi bi-x-circle"></i> Batal
                                                                </button>
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="bi bi-check-circle"></i> Simpan Kriteria
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                                <!-- Kriteria List -->
                                                <div class="kriteria-list" id="kriteriaList_{{ $elemen->id }}">
                                                    @foreach ($elemen->kriteriaKerjas as $kriteria)
                                                        <div class="kriteria-item"
                                                            data-kriteria-id="{{ $kriteria->id }}">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="flex-grow-1 me-3">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i
                                                                            class="bi bi-grip-vertical sortable-handle me-2"></i>
                                                                        <span
                                                                            class="badge bg-light text-dark me-2 kriteria-kode">{{ $kriteria->kode_kriteria }}</span>
                                                                        <span
                                                                            class="badge bg-{{ $kriteria->is_active ? 'success' : 'secondary' }}">
                                                                            {{ $kriteria->is_active ? 'Aktif' : 'Nonaktif' }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="kriteria-uraian">
                                                                        {{ $kriteria->uraian_kriteria }}</div>
                                                                </div>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button class="btn btn-outline-warning"
                                                                        onclick="editKriteria({{ $kriteria->id }}, {{ $elemen->id }})">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                    <button class="btn btn-outline-danger"
                                                                        onclick="deleteKriteria({{ $kriteria->id }}, {{ $elemen->id }})">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <!-- Edit Kriteria Form (Hidden by default) -->
                                                            <div id="editKriteriaForm_{{ $kriteria->id }}"
                                                                style="display: none;">
                                                                <form
                                                                    onsubmit="updateKriteria(event, {{ $kriteria->id }}, {{ $elemen->id }})">
                                                                    <div class="mt-3 p-3 bg-white border rounded">
                                                                        <h6 class="mb-3">
                                                                            <i class="bi bi-pencil-square me-2"></i>Edit
                                                                            Kriteria Kerja
                                                                        </h6>

                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                <div class="form-floating mb-3">
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="kode_kriteria"
                                                                                        value="{{ $kriteria->kode_kriteria }}"
                                                                                        placeholder="Kode Kriteria"
                                                                                        required maxlength="50">
                                                                                    <label>Kode Kriteria</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <div class="form-floating mb-3">
                                                                                    <textarea class="form-control" name="uraian_kriteria" placeholder="Uraian Kriteria" style="height: 80px;" required
                                                                                        maxlength="2000">{{ $kriteria->uraian_kriteria }}</textarea>
                                                                                    <label>Uraian Kriteria</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox" name="is_active"
                                                                                    {{ $kriteria->is_active ? 'checked' : '' }}>
                                                                                <label
                                                                                    class="form-check-label">Aktif</label>
                                                                            </div>
                                                                            <div>
                                                                                <button type="button"
                                                                                    class="btn btn-outline-secondary me-2"
                                                                                    onclick="cancelEditKriteria({{ $kriteria->id }})">
                                                                                    <i class="bi bi-x-circle"></i> Batal
                                                                                </button>
                                                                                <button type="submit"
                                                                                    class="btn btn-warning">
                                                                                    <i class="bi bi-check-circle"></i>
                                                                                    Update
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @if ($elemen->kriteriaKerjas->count() == 0)
                                                        <div class="text-center py-4 text-muted">
                                                            <i class="bi bi-info-circle fs-1 mb-3"></i>
                                                            <h6>Belum ada Kriteria Kerja</h6>
                                                            <p class="small">Tambahkan kriteria kerja untuk elemen ini.
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-list-task fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada Elemen Kompetensi</h5>
                                <p class="text-muted">Mulai dengan menambahkan elemen kompetensi untuk unit ini.</p>
                                <button class="btn btn-primary" onclick="showAddElemenForm()">
                                    <i class="bi bi-plus-lg"></i> Tambah Elemen Kompetensi
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Metadata -->
                <div class="row mt-4 mb-3">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-info-circle me-2"></i>Informasi Tambahan
                                </h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Dibuat:</small>
                                        <strong>{{ $unit->created_at->format('d M Y, H:i') }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Diperbarui:</small>
                                        <strong>{{ $unit->updated_at->format('d M Y, H:i') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-gear me-2"></i>Aksi Cepat
                                </h6>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.schemes.unit-kompetensi.edit', [$scheme, $unit]) }}"
                                        class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit Unit
                                    </a>
                                    <button onclick="showAddElemenForm()" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-plus-circle"></i> Tambah Elemen
                                    </button>
                                    <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}"
                                        class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-people"></i> Kelola di Kelompok Kerja
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Statistik Kelompok Kerja -->
    <div class="modal fade" id="kelompokKerjaStatsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-bar-chart me-2"></i>Statistik Kelompok Kerja
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h3 class="text-primary mb-1">{{ $unit->kelompokKerjas->count() }}</h3>
                                    <p class="text-muted mb-0">Total Kelompok</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h3 class="text-success mb-1">{{ $unit->activeKelompokKerjas->count() }}</h3>
                                    <p class="text-muted mb-0">Aktif</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-secondary">
                                <div class="card-body">
                                    <h3 class="text-secondary mb-1">
                                        {{ $unit->kelompokKerjas->count() - $unit->activeKelompokKerjas->count() }}</h3>
                                    <p class="text-muted mb-0">Nonaktif</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($unit->kelompokKerjas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kelompok Kerja</th>
                                        <th>Status</th>
                                        <th>Urutan</th>
                                        <th>Ditambahkan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unit->kelompokKerjas()->withPivot(['sort_order', 'is_active', 'created_at'])->orderBy('kelompok_kerja_unit_kompetensi.sort_order')->get() as $kelompok)
                                        <tr>
                                            <td>{{ $kelompok->nama_kelompok }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $kelompok->pivot->is_active ? 'success' : 'secondary' }}">
                                                    {{ $kelompok->pivot->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td>{{ $kelompok->pivot->sort_order }}</td>
                                            <td>{{ $kelompok->pivot->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}" class="btn btn-primary">
                        <i class="bi bi-gear me-1"></i> Kelola di Kelompok Kerja
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Unit Modal -->
    <div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus unit kompetensi <strong>{{ $unit->kode_unit }}</strong>?</p>
                    <p class="text-danger">
                        <strong>Peringatan:</strong> Semua elemen kompetensi, kriteria kerja, dan relasi dengan kelompok
                        kerja akan ikut terhapus.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteUnitForm" method="POST"
                        action="{{ route('admin.schemes.unit-kompetensi.destroy', [$scheme, $unit]) }}"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus Unit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Elemen Modal -->
    <div class="modal fade" id="deleteElemenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Elemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus elemen kompetensi ini?</p>
                    <p class="text-danger">
                        <strong>Peringatan:</strong> Semua kriteria kerja dalam elemen ini akan ikut terhapus.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteElemenForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus Elemen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Kriteria Modal -->
    <div class="modal fade" id="deleteKriteriaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kriteria kerja ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteKriteriaForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus Kriteria</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Portfolio Requirements Section - Simplified Version -->

    <!-- Portfolio Requirements Section - Fixed -->
    <div class="card mt-3">
        <div
            class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h6 class="mb-0">
                <i class="bi bi-file-text me-2"></i>Dokumen Portofolio Unit Ini
                <span
                    class="badge bg-primary ms-2">{{ $unit->portfolioFiles ? $unit->portfolioFiles->count() : 0 }}</span>
                <span
                    class="badge bg-danger ms-1">{{ $unit->portfolioFiles ? $unit->portfolioFiles->where('is_required', true)->count() : 0 }}
                    Wajib</span>
                <span
                    class="badge bg-warning ms-1">{{ $unit->portfolioFiles ? $unit->portfolioFiles->where('is_required', false)->count() : 0 }}
                    Opsional</span>
            </h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-success" onclick="showAddDocumentForm()">
                    <i class="bi bi-plus"></i> Tambah Dokumen
                </button>
                @php
                    // Fix untuk error whereHas - gunakan filter biasa
                    $otherUnitsWithPortfolio = $scheme->unitKompetensis->filter(function ($otherUnit) use ($unit) {
                        return $otherUnit->id !== $unit->id &&
                            $otherUnit->portfolioFiles &&
                            $otherUnit->portfolioFiles->count() > 0;
                    });
                @endphp

                @if ($otherUnitsWithPortfolio->count() > 0)
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-copy"></i> Duplikasi
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <h6 class="dropdown-header">Salin dari Unit Lain:</h6>
                            </li>
                            @foreach ($otherUnitsWithPortfolio as $otherUnit)
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="duplicateFromUnit({{ $otherUnit->id }}, '{{ $otherUnit->kode_unit }}')">
                                        <i class="bi bi-files me-2"></i>
                                        {{ $otherUnit->kode_unit }}
                                        <span
                                            class="badge bg-light text-dark ms-2">{{ $otherUnit->portfolioFiles ? $otherUnit->portfolioFiles->count() : 0 }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-body">
            <!-- Add Document Form -->
            <div class="add-form" id="addDocumentForm" style="display: none;">
                <form id="portfolioDocumentForm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Dokumen Portofolio
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideAddDocumentForm()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="document_name" name="document_name"
                                    placeholder="Nama Dokumen" required maxlength="255">
                                <label for="document_name">Nama Dokumen *</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="document_requirement" name="is_required" required>
                                    <option value="1" selected>Wajib</option>
                                    <option value="0">Opsional</option>
                                </select>
                                <label for="document_requirement">Status *</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="document_description" name="document_description" placeholder="Deskripsi Dokumen"
                                    style="height: 80px;" maxlength="500"></textarea>
                                <label for="document_description">Deskripsi Dokumen</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check mt-3">
                            <!-- hidden default supaya nilai tetap terkirim -->
                            <input type="hidden" name="is_active" value="0">

                            <!-- checkbox utama (default ON) -->
                            <input class="form-check-input" type="checkbox" id="document_active" name="is_active"
                                value="1" {{ old('is_active', $field->is_active ?? 1) ? 'checked' : '' }}>

                            <label class="form-check-label" for="document_active">Dokumen Aktif</label>
                        </div>

                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2"
                                onclick="hideAddDocumentForm()">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Simpan Dokumen
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            @if ($unit->portfolioFiles && $unit->portfolioFiles->count() > 0)
                <!-- Portfolio Documents List -->
                <div id="documentList" class="mt-3">
                    @foreach ($unit->portfolioFiles->sortBy('sort_order') as $document)
                        <div class="document-item {{ $document->is_required ? 'required' : 'optional' }} {{ !$document->is_active ? 'inactive' : '' }}"
                            data-document-id="{{ $document->id }}">
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-start flex-grow-1">
                                        <i class="bi bi-grip-vertical sortable-handle me-3 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <span
                                                    class="badge bg-light text-dark me-2">{{ $document->sort_order }}</span>
                                                <h6 class="mb-0 me-2">{{ $document->document_name }}</h6>
                                                <span
                                                    class="badge bg-{{ $document->is_required ? 'danger' : 'warning' }} me-2">
                                                    {{ $document->is_required ? 'Wajib' : 'Opsional' }}
                                                </span>
                                                <span
                                                    class="badge bg-{{ $document->is_active ? 'success' : 'secondary' }}">
                                                    <i
                                                        class="bi bi-{{ $document->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                                    {{ $document->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>

                                            @if ($document->document_description)
                                                <p class="text-muted mb-2 small">{{ $document->document_description }}
                                                </p>
                                            @endif

                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="bi bi-calendar-plus me-1"></i>
                                                <span>{{ $document->created_at->format('d M Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning btn-sm"
                                            onclick="editDocument({{ $document->id }}, '{{ addslashes($document->document_name) }}', '{{ addslashes($document->document_description ?? '') }}', {{ $document->is_required ? 'true' : 'false' }}, {{ $document->is_active ? 'true' : 'false' }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-outline-{{ $document->is_active ? 'secondary' : 'success' }} btn-sm"
                                            onclick="toggleDocumentStatus({{ $document->id }})">
                                            <i class="bi bi-{{ $document->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm"
                                            onclick="deleteDocument({{ $document->id }}, '{{ addslashes($document->document_name) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Portfolio Summary -->
                <div class="mt-4 p-3 bg-light rounded">
                    <div class="row text-center">
                        <div class="col-3">
                            <h4 class="text-primary mb-0">{{ $unit->portfolioFiles->count() }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-danger mb-0">{{ $unit->portfolioFiles->where('is_required', true)->count() }}
                            </h4>
                            <small class="text-muted">Wajib</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-warning mb-0">
                                {{ $unit->portfolioFiles->where('is_required', false)->count() }}</h4>
                            <small class="text-muted">Opsional</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-success mb-0">{{ $unit->portfolioFiles->where('is_active', true)->count() }}
                            </h4>
                            <small class="text-muted">Aktif</small>
                        </div>
                    </div>
                </div>
            @else
                <!-- HANDLE KOSONG: Tampilan ketika belum ada portfolio files -->
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Belum ada Dokumen Portofolio</h5>
                    <p class="text-muted mb-4">Unit <strong>{{ $unit->kode_unit }}</strong> belum memiliki daftar dokumen
                        portofolio yang diperlukan untuk penilaian kompetensi.</p>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <button type="button" class="btn btn-primary" onclick="showAddDocumentForm()">
                            <i class="bi bi-plus me-2"></i>Tambah Dokumen Pertama
                        </button>
                    </div>

                    @if ($otherUnitsWithPortfolio->count() > 0)
                        <div class="mt-3">
                            <p class="text-muted small mb-2">Atau salin dari unit lain dalam skema ini:</p>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                @foreach ($otherUnitsWithPortfolio as $otherUnit)
                                    <button class="btn btn-outline-info btn-sm"
                                        onclick="duplicateFromUnit({{ $otherUnit->id }}, '{{ $otherUnit->kode_unit }}')">
                                        <i class="bi bi-copy me-1"></i>
                                        {{ $otherUnit->kode_unit }}
                                        <span
                                            class="badge bg-light text-dark ms-1">{{ $otherUnit->portfolioFiles->count() }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Info:</strong> Unit lain dalam skema ini juga belum memiliki dokumen portofolio.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Document Modal -->
    <div class="modal fade" id="editDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Dokumen Portofolio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editDocumentForm">
                        <input type="hidden" id="editDocumentId">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editDocumentName" required maxlength="255">
                            <label>Nama Dokumen *</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="editDocumentDescription" style="height: 80px;" maxlength="500"></textarea>
                            <label>Deskripsi Dokumen</label>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="editDocumentRequirement">
                                        <option value="1">Wajib</option>
                                        <option value="0">Opsional</option>
                                    </select>
                                    <label>Status Dokumen *</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" id="editDocumentActive">
                                    <label class="form-check-label">Dokumen Aktif</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="editDocumentForm" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Document Modal -->
    <div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus dokumen portofolio ini?</p>
                    <div id="deleteDocumentInfo" class="p-3 bg-light rounded">
                        <!-- Document info will be populated here -->
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteDocument">
                        <i class="bi bi-trash me-2"></i>Hapus Dokumen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Duplicate Documents Modal -->
    <div class="modal fade" id="duplicateDocumentsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-copy me-2"></i>Duplikasi Dokumen Portofolio
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="duplicateContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmDuplicate">
                        <i class="bi bi-copy me-2"></i>Duplikasi Dokumen Terpilih
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Fixed JavaScript for handling form submissions
        let currentElemenId = null;
        let currentKriteriaId = null;

        // Unit functions
        function toggleStatus(unitId) {
            fetch(`{{ route('admin.schemes.unit-kompetensi.toggle-status', [$scheme, 'UNIT_ID']) }}`.replace('UNIT_ID',
                    unitId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Terjadi kesalahan saat mengubah status.');
                });
        }

        function deleteUnit(unitId) {
            const modal = new bootstrap.Modal(document.getElementById('deleteUnitModal'));
            modal.show();
        }

        // Elemen functions
        function showAddElemenForm() {
            document.getElementById('addElemenForm').style.display = 'block';
            document.getElementById('addElemenForm').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function hideAddElemenForm() {
            document.getElementById('addElemenForm').style.display = 'none';
            // Clear form
            document.getElementById('addElemenForm').querySelector('form').reset();
        }

        function submitElemenForm(event) {
            event.preventDefault();
            const form = event.target;

            // Create FormData to properly handle checkboxes
            const formData = new FormData(form);

            // Handle checkbox value properly
            const isActiveCheckbox = form.querySelector('input[name="is_active"]');
            if (isActiveCheckbox) {
                formData.set('is_active', isActiveCheckbox.checked ? '1' : '0');
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;

            fetch(`{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.store', [$scheme, $unit]) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Gagal menambahkan elemen');
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message || 'Gagal menambahkan elemen');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', error.message || 'Terjadi kesalahan saat menambahkan elemen');

                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        function editElemen(elemenId) {
            // Hide other edit forms
            document.querySelectorAll('[id^="editElemenForm_"]').forEach(form => {
                form.style.display = 'none';
            });

            const editForm = document.getElementById(`editElemenForm_${elemenId}`);
            editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
        }

        function cancelEditElemen(elemenId) {
            document.getElementById(`editElemenForm_${elemenId}`).style.display = 'none';
        }

        function updateElemen(event, elemenId) {
            event.preventDefault();
            const form = event.target;

            // Create FormData to properly handle all form data including checkboxes
            const formData = new FormData(form);

            // Handle checkbox value properly
            const isActiveCheckbox = form.querySelector('input[name="is_active"]');
            if (isActiveCheckbox) {
                formData.set('is_active', isActiveCheckbox.checked ? '1' : '0');
            }

            // Add method spoofing for Laravel
            formData.append('_method', 'PUT');

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengupdate...';
            submitBtn.disabled = true;

            fetch(`{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.update', [$scheme, $unit, 'ELEMEN_ID']) }}`
                    .replace('ELEMEN_ID', elemenId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Gagal memperbarui elemen');
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message || 'Gagal memperbarui elemen');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', error.message || 'Terjadi kesalahan saat memperbarui elemen');

                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        function deleteElemen(elemenId) {
            currentElemenId = elemenId;
            const modal = new bootstrap.Modal(document.getElementById('deleteElemenModal'));
            const form = document.getElementById('deleteElemenForm');
            form.action =
                `{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.destroy', [$scheme, $unit, 'ELEMEN_ID']) }}`
                .replace('ELEMEN_ID', elemenId);
            modal.show();
        }

        function toggleKriteriaSection(elemenId) {
            const section = document.getElementById(`kriteriaSection_${elemenId}`);
            section.style.display = section.style.display === 'none' ? 'block' : 'none';

            if (section.style.display === 'block') {
                section.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        }

        // Kriteria functions
        function showAddKriteriaForm(elemenId) {
            document.getElementById(`addKriteriaForm_${elemenId}`).style.display = 'block';
        }

        function hideAddKriteriaForm(elemenId) {
            document.getElementById(`addKriteriaForm_${elemenId}`).style.display = 'none';
            // Clear form
            document.getElementById(`addKriteriaForm_${elemenId}`).querySelector('form').reset();
        }

        function submitKriteriaForm(event, elemenId) {
            event.preventDefault();
            const form = event.target;

            // Create FormData to properly handle all form data including checkboxes
            const formData = new FormData(form);

            // Handle checkbox value properly
            const isActiveCheckbox = form.querySelector('input[name="is_active"]');
            if (isActiveCheckbox) {
                formData.set('is_active', isActiveCheckbox.checked ? '1' : '0');
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;

            fetch(`{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.kriteria-kerja.store', [$scheme, $unit, 'ELEMEN_ID']) }}`
                    .replace('ELEMEN_ID', elemenId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Gagal menambahkan kriteria');
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message || 'Gagal menambahkan kriteria');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', error.message || 'Terjadi kesalahan saat menambahkan kriteria');

                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        function editKriteria(kriteriaId, elemenId) {
            // Hide other edit forms
            document.querySelectorAll('[id^="editKriteriaForm_"]').forEach(form => {
                form.style.display = 'none';
            });

            const editForm = document.getElementById(`editKriteriaForm_${kriteriaId}`);
            editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
        }

        function cancelEditKriteria(kriteriaId) {
            document.getElementById(`editKriteriaForm_${kriteriaId}`).style.display = 'none';
        }

        function updateKriteria(event, kriteriaId, elemenId) {
            event.preventDefault();
            const form = event.target;

            // Create FormData to properly handle all form data including checkboxes
            const formData = new FormData(form);

            // Handle checkbox value properly
            const isActiveCheckbox = form.querySelector('input[name="is_active"]');
            if (isActiveCheckbox) {
                formData.set('is_active', isActiveCheckbox.checked ? '1' : '0');
            }

            // Add method spoofing for Laravel
            formData.append('_method', 'PUT');

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengupdate...';
            submitBtn.disabled = true;

            fetch(`{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.kriteria-kerja.update', [$scheme, $unit, 'ELEMEN_ID', 'KRITERIA_ID']) }}`
                    .replace('ELEMEN_ID', elemenId)
                    .replace('KRITERIA_ID', kriteriaId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Gagal memperbarui kriteria');
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message || 'Gagal memperbarui kriteria');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', error.message || 'Terjadi kesalahan saat memperbarui kriteria');

                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        function deleteKriteria(kriteriaId, elemenId) {
            currentKriteriaId = kriteriaId;
            currentElemenId = elemenId;
            const modal = new bootstrap.Modal(document.getElementById('deleteKriteriaModal'));
            const form = document.getElementById('deleteKriteriaForm');
            form.action =
                `{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.kriteria-kerja.destroy', [$scheme, $unit, 'ELEMEN_ID', 'KRITERIA_ID']) }}`
                .replace('ELEMEN_ID', elemenId)
                .replace('KRITERIA_ID', kriteriaId);
            modal.show();
        }

        function showKelompokKerjaStats() {
            const modal = new bootstrap.Modal(document.getElementById('kelompokKerjaStatsModal'));
            modal.show();
        }

        function showAlert(type, message) {
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
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

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure CSRF token is available
            if (!document.querySelector('meta[name="csrf-token"]')) {
                const meta = document.createElement('meta');
                meta.name = 'csrf-token';
                meta.content = '{{ csrf_token() }}';
                document.getElementsByTagName('head')[0].appendChild(meta);
            }

            // Debug: Log when forms are submitted
            document.addEventListener('submit', function(e) {
                console.log('Form submitted:', e.target.action, e.target.method);
            });
        });
    </script>

    <script>
        // Portfolio Document Management - Simplified Version
        let currentDeleteDocumentId = null;
        let currentSourceUnitId = null;

        // Show/Hide Add Document Form
        function showAddDocumentForm() {
            document.getElementById('addDocumentForm').style.display = 'block';
            document.getElementById('addDocumentForm').scrollIntoView({
                behavior: 'smooth'
            });
            document.getElementById('document_name').focus();
        }

        function hideAddDocumentForm() {
            document.getElementById('addDocumentForm').style.display = 'none';
            document.getElementById('portfolioDocumentForm').reset();
        }

        // Submit Add Document Form
        document.getElementById('portfolioDocumentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(
                    `/admin/schemes/{{ $scheme->id }}/unit-kompetensi/{{ $unit->id }}/portfolio-files`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message);
                    hideAddDocumentForm();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', result.message || 'Gagal menambahkan dokumen');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Terjadi kesalahan saat menambahkan dokumen');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        // Edit Document
        function editDocument(documentId, name, description, isRequired, isActive) {
            document.getElementById('editDocumentId').value = documentId;
            document.getElementById('editDocumentName').value = name;
            document.getElementById('editDocumentDescription').value = description || '';
            document.getElementById('editDocumentRequirement').value = isRequired ? '1' : '0';
            document.getElementById('editDocumentActive').checked = isActive;

            const modal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
            modal.show();
        }

        // Submit Edit Document Form
        document.getElementById('editDocumentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const documentId = document.getElementById('editDocumentId').value;
            const formData = {
                document_name: document.getElementById('editDocumentName').value,
                document_description: document.getElementById('editDocumentDescription').value,
                is_required: document.getElementById('editDocumentRequirement').value === '1',
                is_active: document.getElementById('editDocumentActive').checked
            };

            try {
                const response = await fetch(
                    `/admin/schemes/{{ $scheme->id }}/unit-kompetensi/{{ $unit->id }}/portfolio-files/${documentId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editDocumentModal'));
                    modal.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', result.message || 'Gagal mengupdate dokumen');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Terjadi kesalahan saat mengupdate dokumen');
            }
        });

        // Delete Document
        function deleteDocument(documentId, documentName) {
            currentDeleteDocumentId = documentId;

            document.getElementById('deleteDocumentInfo').innerHTML = `
        <h6 class="mb-1">Dokumen yang akan dihapus:</h6>
        <p class="mb-0"><strong>${documentName}</strong></p>
    `;

            const modal = new bootstrap.Modal(document.getElementById('deleteDocumentModal'));
            modal.show();
        }

        // Confirm Delete Document
        document.getElementById('confirmDeleteDocument').addEventListener('click', async function() {
            if (!currentDeleteDocumentId) return;

            try {
                const response = await fetch(
                    `/admin/schemes/{{ $scheme->id }}/unit-kompetensi/{{ $unit->id }}/portfolio-files/${currentDeleteDocumentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteDocumentModal'));
                    modal.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', result.message || 'Gagal menghapus dokumen');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Terjadi kesalahan saat menghapus dokumen');
            } finally {
                currentDeleteDocumentId = null;
            }
        });

        // Toggle Document Status
        function toggleDocumentStatus(documentId) {
            if (confirm('Apakah Anda yakin ingin mengubah status dokumen ini?')) {
                fetch(`/admin/schemes/{{ $scheme->id }}/unit-kompetensi/{{ $unit->id }}/portfolio-files/${documentId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                        showAlert('danger', 'Terjadi kesalahan saat mengubah status dokumen');
                    });
            }
        }

        // Duplicate from Unit
        async function duplicateFromUnit(sourceUnitId, sourceUnitCode) {
            currentSourceUnitId = sourceUnitId;

            try {
                const response = await fetch(
                    `/admin/schemes/{{ $scheme->id }}/unit-kompetensi/${sourceUnitId}/portfolio-files`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                const result = await response.json();

                if (result.success && result.data.length > 0) {
                    let content = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Duplikasi dokumen portofolio dari <strong>${sourceUnitCode}</strong> ke unit ini.
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Pilih dokumen yang akan diduplikasi:</label>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="selectAllDocs" checked>
                    <label class="form-check-label fw-bold" for="selectAllDocs">
                        Pilih Semua (${result.data.length} dokumen)
                    </label>
                </div>
                
                <div class="document-list" style="max-height: 300px; overflow-y: auto;">
            `;

                    result.data.forEach((doc, index) => {
                        content += `
                    <div class="form-check mb-2 p-2 border rounded">
                        <input class="form-check-input doc-checkbox" type="checkbox" 
                               value="${doc.id}" id="doc_${doc.id}" checked>
                        <label class="form-check-label w-100" for="doc_${doc.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>${doc.document_name}</strong>
                                    ${doc.document_description ? `<br><small class="text-muted">${doc.document_description}</small>` : ''}
                                </div>
                                <div class="ms-2">
                                    <span class="badge bg-${doc.is_required ? 'danger' : 'warning'} me-1">
                                        ${doc.is_required ? 'Wajib' : 'Opsional'}
                                    </span>
                                    <span class="badge bg-${doc.is_active ? 'success' : 'secondary'}">
                                        ${doc.is_active ? 'Aktif' : 'Nonaktif'}
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>
                `;
                    });

                    content += '</div>';

                    document.getElementById('duplicateContent').innerHTML = content;

                    // Handle select all checkbox
                    document.getElementById('selectAllDocs').addEventListener('change', function() {
                        const checkboxes = document.querySelectorAll('.doc-checkbox');
                        checkboxes.forEach(cb => cb.checked = this.checked);
                    });

                    const modal = new bootstrap.Modal(document.getElementById('duplicateDocumentsModal'));
                    modal.show();
                } else {
                    showAlert('warning', 'Unit tersebut tidak memiliki dokumen portofolio');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Terjadi kesalahan saat memuat data dokumen');
            }
        }

        // Confirm Duplicate
        document.getElementById('confirmDuplicate').addEventListener('click', async function() {
            const selectedDocs = [];
            const checkboxes = document.querySelectorAll('.doc-checkbox:checked');

            if (checkboxes.length === 0) {
                showAlert('warning', 'Pilih minimal satu dokumen untuk diduplikasi');
                return;
            }

            checkboxes.forEach(cb => {
                selectedDocs.push(parseInt(cb.value));
            });

            const confirmBtn = this;
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menduplikasi...';
            confirmBtn.disabled = true;

            try {
                const response = await fetch(
                    `/admin/schemes/{{ $scheme->id }}/unit-kompetensi/{{ $unit->id }}/portfolio-files/duplicate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            source_unit_id: currentSourceUnitId,
                            document_ids: selectedDocs
                        })
                    });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById(
                        'duplicateDocumentsModal'));
                    modal.hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', result.message || 'Gagal menduplikasi dokumen');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Terjadi kesalahan saat menduplikasi dokumen');
            } finally {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        });

        // Utility function for showing alerts
        function showAlert(type, message) {
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
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
    </script>
@endpush
