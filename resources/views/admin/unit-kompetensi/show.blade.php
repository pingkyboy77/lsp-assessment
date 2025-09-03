{{-- resources/views/admin/unit-kompetensi/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Unit Kompetensi - ' . $unit->kode_unit)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .kelompok-badge {
            transition: all 0.2s ease;
        }
        .kelompok-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        
        .card-body > .row {
            margin-left: 0;
            margin-right: 0;
        }
        
        .card-body > .row > [class*="col-"] {
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema Sertifikasi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ Str::limit($scheme->nama, 20) }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.schemes.unit-kompetensi.index', $scheme) }}">Unit Kompetensi</a></li>
                            <li class="breadcrumb-item active">{{ $unit->kode_unit }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.schemes.unit-kompetensi.edit', [$scheme, $unit]) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
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

                            @if($unit->standar_kompetensi_kerja)
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
                                    <h2 class="text-success mb-1">{{ $unit->elemenKompetensis->sum(fn($e) => $e->kriteriaKerjas->count()) }}</h2>
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
            @if($unit->activeKelompokKerjas->count() > 0)
            <div class="card m-3">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <h6 class="mb-0">
                        <i class="bi bi-people me-2"></i>Kelompok Kerja
                        <span class="badge bg-info ms-2">{{ $unit->activeKelompokKerjas->count() }}</span>
                    </h6>
                    <div class="btn-group">
                        <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-gear"></i> Kelola di Kelompok Kerja
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}">
                                    <i class="bi bi-people me-2"></i> Lihat Semua Kelompok Kerja
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
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
                        @foreach($unit->activeKelompokKerjas->take(6) as $kelompok)
                            <div class="col-lg-6 mb-3">
                                <div class="card border-info kelompok-badge h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1 me-2">
                                                <h6 class="card-title mb-1">
                                                    <i class="bi bi-people text-info"></i>
                                                    {{ $kelompok->nama_kelompok }}
                                                    @if($kelompok->pivot && $kelompok->pivot->is_active)
                                                        <span class="badge bg-success ms-2">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary ms-2">Nonaktif</span>
                                                    @endif
                                                </h6>
                                                @if($kelompok->deskripsi)
                                                    <p class="card-text text-muted small mb-2">
                                                        {{ Str::limit($kelompok->deskripsi, 80) }}
                                                    </p>
                                                @endif
                                                <div class="d-flex flex-column flex-sm-row align-items-start text-muted small gap-2">
                                                    <span>
                                                        <i class="bi bi-sort-numeric-up"></i> 
                                                        Urutan: {{ $kelompok->pivot ? $kelompok->pivot->sort_order : '-' }}
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
                        
                        @if($unit->activeKelompokKerjas->count() > 6)
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
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
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
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
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
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
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
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideAddElemenForm()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="kode_elemen" name="kode_elemen" placeholder="Kode Elemen" required maxlength="50">
                                        <label for="kode_elemen">Kode Elemen</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="judul_elemen" name="judul_elemen" placeholder="Judul Elemen" required maxlength="1000">
                                        <label for="judul_elemen">Judul Elemen</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active_elemen" name="is_active" checked>
                                        <label class="form-check-label" for="is_active_elemen">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-8 text-end">
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="hideAddElemenForm()">
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
                    @if($unit->elemenKompetensis->count() > 0)
                        <div id="elemenList">
                            @foreach($unit->elemenKompetensis as $index => $elemen)
                                <div class="elemen-card" data-elemen-id="{{ $elemen->id }}">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <i class="bi bi-grip-vertical sortable-handle me-2"></i>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                                        <span class="elemen-kode">{{ $elemen->kode_elemen }}</span>
                                                        <span class="badge bg-{{ $elemen->is_active ? 'success' : 'secondary' }} ms-2">
                                                            {{ $elemen->is_active ? 'Aktif' : 'Nonaktif' }}
                                                        </span>
                                                    </h6>
                                                    <div class="elemen-judul">{{ $elemen->judul_elemen }}</div>
                                                </div>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="toggleKriteriaSection({{ $elemen->id }})">
                                                    <i class="bi bi-list-ul"></i>
                                                    Kriteria ({{ $elemen->kriteriaKerjas->count() }})
                                                </button>
                                                <button class="btn btn-outline-warning" onclick="editElemen({{ $elemen->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteElemen({{ $elemen->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Edit Form (Hidden by default) -->
                                    <div class="collapse-content" id="editElemenForm_{{ $elemen->id }}" style="display: none;">
                                        <form onsubmit="updateElemen(event, {{ $elemen->id }})">
                                            <div class="p-3 bg-light">
                                                <h6 class="mb-3">
                                                    <i class="bi bi-pencil-square me-2"></i>Edit Elemen Kompetensi
                                                </h6>
                                                
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control" name="kode_elemen" value="{{ $elemen->kode_elemen }}" placeholder="Kode Elemen" required maxlength="50">
                                                            <label>Kode Elemen</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control" name="judul_elemen" value="{{ $elemen->judul_elemen }}" placeholder="Judul Elemen" required maxlength="1000">
                                                            <label>Judul Elemen</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_active" {{ $elemen->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label">Aktif</label>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="cancelEditElemen({{ $elemen->id }})">
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
                                    <div class="collapse-content" id="kriteriaSection_{{ $elemen->id }}" style="display: none;">
                                        <div class="p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-check-square me-2"></i>Kriteria Kerja
                                                    <span class="badge bg-success ms-2">{{ $elemen->kriteriaKerjas->count() }}</span>
                                                </h6>
                                                <button class="btn btn-sm btn-success" onclick="showAddKriteriaForm({{ $elemen->id }})">
                                                    <i class="bi bi-plus"></i> Tambah Kriteria
                                                </button>
                                            </div>

                                            <!-- Add Kriteria Form -->
                                            <div class="add-form" id="addKriteriaForm_{{ $elemen->id }}" style="display: none;">
                                                <form onsubmit="submitKriteriaForm(event, {{ $elemen->id }})">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-plus-square me-2"></i>Tambah Kriteria Kerja
                                                        </h6>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideAddKriteriaForm({{ $elemen->id }})">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control" name="kode_kriteria" placeholder="Kode Kriteria" required maxlength="50">
                                                                <label>Kode Kriteria</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="form-floating">
                                                                <input class="form-control" name="uraian_kriteria" placeholder="Uraian Kriteria" style="height: 80px;" required maxlength="2000">   
                                                                <label>Uraian Kriteria</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                                                            <label class="form-check-label">Aktif</label>
                                                        </div>
                                                        <div>
                                                            <button type="button" class="btn btn-outline-secondary me-2" onclick="hideAddKriteriaForm({{ $elemen->id }})">
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
                                                @foreach($elemen->kriteriaKerjas as $kriteria)
                                                    <div class="kriteria-item" data-kriteria-id="{{ $kriteria->id }}">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="flex-grow-1 me-3">
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <i class="bi bi-grip-vertical sortable-handle me-2"></i>
                                                                    <span class="badge bg-light text-dark me-2 kriteria-kode">{{ $kriteria->kode_kriteria }}</span>
                                                                    <span class="badge bg-{{ $kriteria->is_active ? 'success' : 'secondary' }}">
                                                                        {{ $kriteria->is_active ? 'Aktif' : 'Nonaktif' }}
                                                                    </span>
                                                                </div>
                                                                <div class="kriteria-uraian">{{ $kriteria->uraian_kriteria }}</div>
                                                            </div>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class="btn btn-outline-warning" onclick="editKriteria({{ $kriteria->id }}, {{ $elemen->id }})">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button class="btn btn-outline-danger" onclick="deleteKriteria({{ $kriteria->id }}, {{ $elemen->id }})">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Edit Kriteria Form (Hidden by default) -->
                                                        <div id="editKriteriaForm_{{ $kriteria->id }}" style="display: none;">
                                                            <form onsubmit="updateKriteria(event, {{ $kriteria->id }}, {{ $elemen->id }})">
                                                                <div class="mt-3 p-3 bg-white border rounded">
                                                                    <h6 class="mb-3">
                                                                        <i class="bi bi-pencil-square me-2"></i>Edit Kriteria Kerja
                                                                    </h6>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <div class="form-floating mb-3">
                                                                                <input type="text" class="form-control" name="kode_kriteria" value="{{ $kriteria->kode_kriteria }}" placeholder="Kode Kriteria" required maxlength="50">
                                                                                <label>Kode Kriteria</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <div class="form-floating mb-3">
                                                                                <textarea class="form-control" name="uraian_kriteria" placeholder="Uraian Kriteria" style="height: 80px;" required maxlength="2000">{{ $kriteria->uraian_kriteria }}</textarea>
                                                                                <label>Uraian Kriteria</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" name="is_active" {{ $kriteria->is_active ? 'checked' : '' }}>
                                                                            <label class="form-check-label">Aktif</label>
                                                                        </div>
                                                                        <div>
                                                                            <button type="button" class="btn btn-outline-secondary me-2" onclick="cancelEditKriteria({{ $kriteria->id }})">
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
                                                    </div>
                                                @endforeach
                                                
                                                @if($elemen->kriteriaKerjas->count() == 0)
                                                    <div class="text-center py-4 text-muted">
                                                        <i class="bi bi-info-circle fs-1 mb-3"></i>
                                                        <h6>Belum ada Kriteria Kerja</h6>
                                                        <p class="small">Tambahkan kriteria kerja untuk elemen ini.</p>
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
                                <a href="{{ route('admin.schemes.unit-kompetensi.edit', [$scheme, $unit]) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit Unit
                                </a>
                                <button onclick="showAddElemenForm()" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Tambah Elemen
                                </button>
                                <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}" class="btn btn-outline-info btn-sm">
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
                                <h3 class="text-secondary mb-1">{{ $unit->kelompokKerjas->count() - $unit->activeKelompokKerjas->count() }}</h3>
                                <p class="text-muted mb-0">Nonaktif</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($unit->kelompokKerjas->count() > 0)
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
                            @foreach($unit->kelompokKerjas()->withPivot(['sort_order', 'is_active', 'created_at'])->orderBy('kelompok_kerja_unit_kompetensi.sort_order')->get() as $kelompok)
                            <tr>
                                <td>{{ $kelompok->nama_kelompok }}</td>
                                <td>
                                    <span class="badge bg-{{ $kelompok->pivot->is_active ? 'success' : 'secondary' }}">
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
                <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}" 
                   class="btn btn-primary">
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
                    <strong>Peringatan:</strong> Semua elemen kompetensi, kriteria kerja, dan relasi dengan kelompok kerja akan ikut terhapus.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteUnitForm" method="POST" action="{{ route('admin.schemes.unit-kompetensi.destroy', [$scheme, $unit]) }}" style="display: inline;">
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
@endsection

@push('scripts')
<script>
// Fixed JavaScript for handling form submissions
let currentElemenId = null;
let currentKriteriaId = null;

// Unit functions
function toggleStatus(unitId) {
    fetch(`{{ route("admin.schemes.unit-kompetensi.toggle-status", [$scheme, "UNIT_ID"]) }}`.replace('UNIT_ID', unitId), {
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
    document.getElementById('addElemenForm').scrollIntoView({ behavior: 'smooth' });
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
    
    fetch(`{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.update', [$scheme, $unit, 'ELEMEN_ID']) }}`.replace('ELEMEN_ID', elemenId), {
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
    form.action = `{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.destroy', [$scheme, $unit, 'ELEMEN_ID']) }}`.replace('ELEMEN_ID', elemenId);
    modal.show();
}

function toggleKriteriaSection(elemenId) {
    const section = document.getElementById(`kriteriaSection_${elemenId}`);
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
    
    if (section.style.display === 'block') {
        section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
    
    fetch(`{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.kriteria-kerja.store', [$scheme, $unit, 'ELEMEN_ID']) }}`.replace('ELEMEN_ID', elemenId), {
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
    form.action = `{{ route('admin.schemes.unit-kompetensi.elemen-kompetensi.kriteria-kerja.destroy', [$scheme, $unit, 'ELEMEN_ID', 'KRITERIA_ID']) }}`
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
@endpush