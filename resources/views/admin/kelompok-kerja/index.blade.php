{{-- resources/views/admin/kelompok-kerja/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kelompok Kerja - ' . $scheme->nama)

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.css">
    <style>
        .kelompok-card {
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .kelompok-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            /* border-color: #0d6efd; */
        }

        .kelompok-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .action-dropdown {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .kelompok-card:hover .action-dropdown {
            opacity: 1;
        }

        .search-filters {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .filter-chip {
            display: inline-block;
            padding: 5px 12px;
            margin: 2px;
            background: #e9ecef;
            border-radius: 15px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-chip.active {
            background: #0d6efd;
            color: white;
        }

        .filter-chip:hover {
            background: #0d6efd;
            color: white;
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

        /* Reorder Mode Styles */
        .kelompok-item {
            transition: all 0.2s ease;
        }
        .kelompok-item:hover {
            background-color: #f8f9fa;
        }
        .sortable-ghost {
            opacity: 0.5;
        }
        .sortable-drag {
            transform: rotate(5deg);
        }
        .reorder-handle {
            cursor: grab;
        }
        .reorder-handle:active {
            cursor: grabbing;
        }

        /* List View Styles */
        .list-view .kelompok-card {
            margin-bottom: 15px;
        }

        .view-toggle {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 3px;
        }

        .view-toggle .btn {
            border: none;
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: 5px;
        }

        .view-toggle .btn.active {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-people-fill me-2"></i>Kelompok Kerja
                    </h5>
                    <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema Sertifikasi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ Str::limit($scheme->nama, 30) }}</a></li>
                        <li class="breadcrumb-item active">Kelompok Kerja</li>
                    </ol>
                </nav>
                </div>
                
                <div class="d-flex gap-2">
                    {{-- <a href="{{ route('admin.schemes.kelompok-kerja.edit', [$scheme, $kelompokKerja]) }}"
                        class="btn btn-outline-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a> --}}
                    <a href="{{ route('admin.schemes.kelompok-kerja.create', $scheme) }}" class="btn btn-primary-custom text-light">
                    <i class="bi bi-plus"></i> Tambah Kelompok Kerja
                </a>
                    {{-- <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-gear"></i> Action
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button> --}}
                    <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.schemes.kelompok-kerja.create', $scheme) }}">
                            <i class="bi bi-plus me-2"></i> Tambah Kelompok Kerja
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button class="dropdown-item" onclick="reorderMode()">
                            <i class="bi bi-list me-2"></i> Ubah Urutan
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" onclick="exportData()">
                            <i class="bi bi-download me-2"></i> Export Data
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" onclick="showBulkActions()">
                            <i class="bi bi-gear me-2"></i> Aksi Massal
                        </button>
                    </li>
                </ul>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    

    <div class="card-body m-3">
        <!-- Scheme Information -->
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle me-3 fs-4"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">{{ $scheme->nama }}</h5>
                    <p class="mb-0">
                        {{ $scheme->code_1 ?? 'Kode tidak tersedia' }} | 
                        <span class="badge bg-{{ $scheme->jenjang_color ?? 'secondary' }}">{{ $scheme->jenjang ?? 'Jenjang tidak tersedia' }}</span>
                    </p>
                    @if(isset($scheme->kelompok_kerja_count) || isset($scheme->total_bukti_portofolio_count))
                    <small class="text-muted">
                        <strong>Total:</strong> 
                        {{ $scheme->kelompok_kerja_count ?? $kelompoks->total() }} Kelompok Kerja
                        @if(isset($scheme->total_bukti_portofolio_count))
                            , {{ $scheme->total_bukti_portofolio_count }} Bukti Portofolio
                        @endif
                    </small>
                    @endif
                </div>
                <div class="text-end">
                    <div class="fw-bold text-primary fs-4">{{ $kelompoks->total() }}</div>
                    <small class="text-muted">Total Kelompok Kerja</small>
                </div>
            </div>
        </div>

        <!-- Reorder Controls (Hidden by default) -->
        <div class="alert alert-warning d-none" id="reorder-controls">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-list me-2"></i>
                    <strong>Mode Pengurutan:</strong> Drag kelompok kerja untuk mengubah urutan
                </div>
                <div class="btn-group">
                    <button class="btn btn-success btn-sm" onclick="saveReorder()">
                        <i class="bi bi-save me-1"></i> Simpan Urutan
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="cancelReorder()">
                        <i class="bi bi-x me-1"></i> Batal
                    </button>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               id="search-input"
                               placeholder="Cari kelompok kerja..."
                               onkeyup="filterKelompokKerja()">
                    </div>
                </div>
                <div class="col-md-5 mt-3 mt-md-0">
                    <div class="d-flex flex-wrap">
                        <span class="filter-chip active" data-filter="all" onclick="setFilter('all', this)">
                            Semua ({{ $kelompoks->total() }})
                        </span>
                        <span class="filter-chip" data-filter="active" onclick="setFilter('active', this)">
                            Aktif ({{ $kelompoks->where('is_active', true)->count() }})
                        </span>
                        <span class="filter-chip" data-filter="inactive" onclick="setFilter('inactive', this)">
                            Nonaktif ({{ $kelompoks->where('is_active', false)->count() }})
                        </span>
                    </div>
                </div>
                <div class="col-md-3 mt-3 mt-md-0">
                    <div class="d-flex justify-content-md-end gap-2">
                        <div class="view-toggle">
                            <button class="btn btn-sm active" id="grid-view-btn" onclick="setView('grid')">
                                <i class="bi bi-grid"></i>
                            </button>
                            <button class="btn btn-sm" id="list-view-btn" onclick="setView('list')">
                                <i class="bi bi-list-ul"></i>
                            </button>
                        </div>
                        <a href="{{ route('admin.schemes.unit-kompetensi.index', $scheme) }}" class="btn btn-outline-info btn-sm d-flex justify-content-center align-items-center gap-2">
                            <i class="bi bi-clipboard-check"></i> Unit Kompetensi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelompok Kerja Content -->
        @if($kelompoks->count() > 0)
            <!-- Grid View -->
            <div class="row" id="kelompok-grid">
                @foreach($kelompoks as $kelompok)
                    <div class="col-lg-6 col-xl-4 mb-4 kelompok-item" 
                         data-id="{{ $kelompok->id }}"
                         data-name="{{ strtolower($kelompok->nama_kelompok) }}"
                         data-status="{{ $kelompok->is_active ? 'active' : 'inactive' }}">
                        <div class="card kelompok-card h-100">
                            <!-- Reorder Handle (Hidden by default) -->
                            <div class="reorder-handle d-none p-2 text-center bg-light">
                                <i class="bi bi-grip-vertical text-muted"></i>
                            </div>

                            <div class="kelompok-header p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">{{ $kelompok->nama_kelompok }}</h5>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-{{ $kelompok->is_active ? 'success' : 'secondary' }}">
                                                {{ $kelompok->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                            @if(isset($kelompok->active_bukti_count))
                                                <span class="badge bg-light text-dark">
                                                    {{ $kelompok->active_bukti_count }} Bukti
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="dropdown action-dropdown normal-controls">
                                        <button class="btn btn-sm btn-light dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown" 
                                                aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompok]) }}">
                                                    <i class="bi bi-eye me-2"></i> Lihat Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.schemes.kelompok-kerja.edit', [$scheme, $kelompok]) }}">
                                                    <i class="bi bi-pencil me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.schemes.kelompok-kerja.manage-unit-kompetensi', [$scheme, $kelompok]) }}">
                                                    <i class="bi bi-clipboard-check me-2"></i> Mapping Unit Kompetensi
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item" onclick="toggleStatus({{ $kelompok->id }})">
                                                    <i class="bi bi-{{ $kelompok->is_active ? 'toggle-off' : 'toggle-on' }} me-2"></i>
                                                    {{ $kelompok->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteKelompok({{ $kelompok->id }})">
                                                    <i class="bi bi-trash me-2"></i> Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                @if($kelompok->deskripsi)
                                    <p class="text-muted mb-3">{{ Str::limit($kelompok->deskripsi, 100) }}</p>
                                @endif

                                <!-- Statistics -->
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $kelompok->unitKompetensis->count() ?? 0 }}</div>
                                            <div class="stat-label">Unit Kompetensi</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <div class="stat-number">
                                                {{ $kelompok->buktiPortofolios->count() ?? 0 }}
                                            </div>
                                            <div class="stat-label">Bukti Portofolio</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Meta Information -->
                                <div class="border-top pt-3">
                                    <div class="d-flex justify-content-between text-muted small">
                                        <span>
                                            <i class="bi bi-calendar-plus"></i>
                                            {{ $kelompok->created_at->format('d M Y') }}
                                        </span>
                                        <span>
                                            <i class="bi bi-clock"></i>
                                            {{ $kelompok->updated_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent normal-controls">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompok]) }}" 
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('admin.schemes.kelompok-kerja.manage-unit-kompetensi', [$scheme, $kelompok]) }}" 
                                       class="btn btn-primary btn-sm flex-fill">
                                        <i class="bi bi-clipboard-check"></i> Mapping Unit kompetensi
                                    </a>
                                    <a href="{{ route('admin.schemes.kelompok-kerja.edit', [$scheme, $kelompok]) }}" 
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List View (Hidden by default) -->
            <div class="d-none list-view" id="kelompok-list">
                <div id="kelompoks-container">
                    @foreach($kelompoks as $kelompok)
                        <div class="kelompok-item border-bottom p-3" data-id="{{ $kelompok->id }}"
                             data-name="{{ strtolower($kelompok->nama_kelompok) }}"
                             data-status="{{ $kelompok->is_active ? 'active' : 'inactive' }}">
                            <div class="row align-items-center">
                                <div class="col-md-1 text-center d-none reorder-handle">
                                    <i class="bi bi-grip-vertical text-muted"></i>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="mb-0 me-3">{{ $kelompok->nama_kelompok }}</h6>
                                        <span class="badge bg-{{ $kelompok->is_active ? 'success' : 'secondary' }}">
                                            {{ $kelompok->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                    @if($kelompok->deskripsi)
                                        <p class="mb-1 text-muted">{{ Str::limit($kelompok->deskripsi, 150) }}</p>
                                    @endif
                                    <div class="mt-2">
                                        <small class="text-info me-3">
                                            <i class="bi bi-clipboard-check"></i> {{ $kelompok->unitKompetensis->count() ?? 0 }} Unit Kompetensi
                                        </small>
                                        @if(isset($kelompok->active_bukti_count))
                                        <small class="text-info">
                                            <i class="bi bi-folder-open"></i> {{ $kelompok->active_bukti_count }} Bukti Portofolio
                                        </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="btn-group normal-controls">
                                        <a href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompok]) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.schemes.kelompok-kerja.edit', [$scheme, $kelompok]) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin.schemes.kelompok-kerja.manage-unit-kompetensi', [$scheme, $kelompok]) }}" 
                                           class="btn btn-sm btn-primary" title="Kelola Unit Kompetensi">
                                            <i class="bi bi-clipboard-check"></i>
                                        </a>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    data-bs-toggle="dropdown" title="Aksi Lainnya">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item" onclick="toggleStatus({{ $kelompok->id }})">
                                                        <i class="bi bi-{{ $kelompok->is_active ? 'toggle-off' : 'toggle-on' }} me-2"></i>
                                                        {{ $kelompok->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="deleteKelompok({{ $kelompok->id }})">
                                                        <i class="bi bi-trash me-2"></i> Hapus
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $kelompoks->links() }}
            </div>

            <!-- No Results State -->
            <div class="empty-state d-none" id="no-results">
                <i class="bi bi-search"></i>
                <h5>Tidak ada kelompok kerja yang ditemukan</h5>
                <p>Coba ubah filter atau kata kunci pencarian Anda.</p>
                <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                </button>
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <i class="bi bi-people"></i>
                <h5>Belum ada Kelompok Kerja</h5>
                <p>Mulai dengan membuat kelompok kerja pertama untuk skema sertifikasi ini.</p>
                <a href="{{ route('admin.schemes.kelompok-kerja.create', $scheme) }}" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i> Tambah Kelompok Kerja
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Kelompok Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kelompok kerja ini?</p>
                <p class="text-danger">
                    <strong>Peringatan:</strong> Semua relasi dengan unit kompetensi dan bukti portofolio akan ikut terhapus.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Kelompok Kerja</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aksi Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Pilih aksi yang ingin diterapkan ke semua kelompok kerja:</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-success" onclick="bulkToggleStatus(true)">
                        <i class="bi bi-toggle-on me-2"></i> Aktifkan Semua
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="bulkToggleStatus(false)">
                        <i class="bi bi-toggle-off me-2"></i> Nonaktifkan Semua
                    </button>
                    <hr>
                    <button type="button" class="btn btn-outline-primary" onclick="exportData()">
                        <i class="bi bi-download me-2"></i> Export Data
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
let sortable;
let originalOrder = [];
let currentFilter = 'all';
let currentView = 'grid';

// Reorder Functions
function reorderMode() {
    if (currentView === 'grid') {
        alert('Mode pengurutan hanya tersedia di tampilan list. Silakan ubah ke tampilan list terlebih dahulu.');
        return;
    }
    
    $('#reorder-controls').removeClass('d-none');
    $('.normal-controls').addClass('d-none');
    $('.reorder-handle').removeClass('d-none');
    
    originalOrder = [];
    $('.kelompok-item').each(function() {
        originalOrder.push($(this).data('id'));
    });
    
    const container = document.getElementById('kelompoks-container');
    sortable = new Sortable(container, {
        handle: '.reorder-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag'
    });
}

function cancelReorder() {
    $('#reorder-controls').addClass('d-none');
    $('.normal-controls').removeClass('d-none');
    $('.reorder-handle').addClass('d-none');
    
    const container = $('#kelompoks-container');
    originalOrder.forEach(id => {
        container.append($(`.kelompok-item[data-id="${id}"]`));
    });
    
    if (sortable) {
        sortable.destroy();
        sortable = null;
    }
}

function saveReorder() {
    const kelompokIds = [];
    $('#kelompoks-container .kelompok-item').each(function() {
        kelompokIds.push($(this).data('id'));
    });
    
    fetch('{{ route("admin.schemes.kelompok-kerja.reorder", $scheme) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            kelompok_ids: kelompokIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            cancelReorder();
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Terjadi kesalahan saat menyimpan urutan.');
    });
}

// Filter Functions
function filterKelompokKerja() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const items = document.querySelectorAll('.kelompok-item');
    let visibleCount = 0;

    items.forEach(item => {
        const name = item.getAttribute('data-name');
        const status = item.getAttribute('data-status');
        
        let matchesSearch = name.includes(searchTerm);
        let matchesFilter = true;
        
        switch(currentFilter) {
            case 'active':
                matchesFilter = status === 'active';
                break;
            case 'inactive':
                matchesFilter = status === 'inactive';
                break;
            case 'all':
            default:
                matchesFilter = true;
                break;
        }
        
        if (matchesSearch && matchesFilter) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show/hide no results state
    const noResults = document.getElementById('no-results');
    const grid = document.getElementById('kelompok-grid');
    const list = document.getElementById('kelompok-list');
    
    if (visibleCount === 0 && (searchTerm || currentFilter !== 'all')) {
        noResults.classList.remove('d-none');
        if (grid) grid.classList.add('d-none');
        if (list) list.classList.add('d-none');
    } else {
        noResults.classList.add('d-none');
        if (currentView === 'grid' && grid) grid.classList.remove('d-none');
        if (currentView === 'list' && list) list.classList.remove('d-none');
    }
}

function setFilter(filter, element) {
    currentFilter = filter;
    
    // Update active filter chip
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.classList.remove('active');
    });
    element.classList.add('active');
    
    filterKelompokKerja();
}

function clearFilters() {
    document.getElementById('search-input').value = '';
    currentFilter = 'all';
    
    // Reset active filter chip
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.classList.remove('active');
    });
    document.querySelector('.filter-chip[data-filter="all"]').classList.add('active');
    
    filterKelompokKerja();
}

// View Toggle Functions
function setView(view) {
    currentView = view;
    const gridView = document.getElementById('kelompok-grid');
    const listView = document.getElementById('kelompok-list');
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');
    
    if (view === 'grid') {
        gridView.classList.remove('d-none');
        listView.classList.add('d-none');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        
        // Cancel reorder mode if active
        if (sortable) {
            cancelReorder();
        }
    } else {
        gridView.classList.add('d-none');
        listView.classList.remove('d-none');
        gridBtn.classList.remove('active');
        listBtn.classList.add('active');
    }
    
    // Re-apply filters
    filterKelompokKerja();
}

// CRUD Functions
function toggleStatus(kelompokId) {
    if (confirm('Apakah Anda yakin ingin mengubah status kelompok kerja ini?')) {
        fetch(`{{ route("admin.schemes.kelompok-kerja.index", $scheme) }}/${kelompokId}/toggle-status`, {
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
}

function deleteKelompok(kelompokId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `{{ route("admin.schemes.kelompok-kerja.index", $scheme) }}/${kelompokId}`;
    modal.show();
}

function showBulkActions() {
    const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
    modal.show();
}

function bulkToggleStatus(isActive) {
    if (confirm(`Apakah Anda yakin ingin ${isActive ? 'mengaktifkan' : 'menonaktifkan'} semua kelompok kerja?`)) {
        fetch('{{ route("admin.schemes.kelompok-kerja.bulk-toggle-status", $scheme) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                is_active: isActive
            })
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
}

function exportData() {
    window.location.href = '{{ route("admin.schemes.kelompok-kerja.export", $scheme) }}';
}

// Utility Functions
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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token if not exists
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
    
    // Show session messages
    @if(session('success'))
        showAlert('success', '{{ session("success") }}');
    @endif
    
    @if(session('error'))
        showAlert('danger', '{{ session("error") }}');
    @endif
});
</script>
@endpush