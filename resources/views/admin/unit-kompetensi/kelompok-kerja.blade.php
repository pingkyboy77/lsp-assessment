{{-- resources/views/admin/kelompok-kerja/manage-unit-kompetensi.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kelola Unit Kompetensi - ' . $kelompokKerja->nama_kelompok)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <style>
        .unit-item {
            transition: all 0.3s ease;
            cursor: grab;
            border: 2px solid transparent;
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .unit-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            border-color: #0d6efd;
        }

        .unit-item.dragging {
            opacity: 0.5;
            transform: rotate(5deg);
            cursor: grabbing;
        }

        .unit-item .drag-handle {
            color: #6c757d;
            cursor: grab;
            margin-right: 10px;
        }

        .unit-item.dragging .drag-handle {
            cursor: grabbing;
        }

        .drop-zone {
            min-height: 300px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
            position: relative;
        }

        .drop-zone.drag-over {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }

        .drop-zone.assigned {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .drop-zone.available {
            border-color: #198754;
            background-color: rgba(25, 135, 84, 0.05);
        }

        .drop-zone-placeholder {
            text-align: center;
            color: #6c757d;
            margin-top: 100px;
        }

        .drop-zone:not(:empty) .drop-zone-placeholder {
            display: none;
        }

        .panel-header {
            border-bottom: 3px solid;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .panel-header.available {
            border-bottom-color: #198754;
        }

        .panel-header.assigned {
            border-bottom-color: #dc3545;
        }

        .action-buttons {
            position: sticky;
            bottom: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            border-top: 3px solid #0d6efd;
        }

        .unit-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .unit-item.available {
            border-left: 4px solid #198754;
        }

        .unit-item.assigned {
            border-left: 4px solid #dc3545;
        }

        .move-button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.8rem;
        }

        .move-to-assigned {
            background-color: #dc3545;
            color: white;
        }

        .move-to-available {
            background-color: #198754;
            color: white;
        }

        .move-button:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        .unit-code {
            font-weight: 600;
            color: #0d6efd;
        }

        @media (max-width: 768px) {
            .management-container {
                grid-template-columns: 1fr !important;
            }
            
            .unit-item {
                padding: 10px;
            }
        }

        .search-box {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@section('content')
<div class="card">
    <!-- Page Header -->
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Kelola Unit Kompetensi</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema Sertifikasi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ $scheme->nama }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}">Kelompok Kerja</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompokKerja]) }}">{{ $kelompokKerja->nama_kelompok }}</a></li>
                        <li class="breadcrumb-item active">Kelola Unit Kompetensi</li>
                    </ol>
                </nav>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompokKerja]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Kelompok Kerja Information -->
        <div class="card mb-4" style="background: linear-gradient(135deg, #74b9ff, #0984e3); color: white;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">{{ $kelompokKerja->nama_kelompok }}</h4>
                        <p class="mb-0 opacity-75">{{ $kelompokKerja->deskripsi ?? 'Kelola unit kompetensi dalam kelompok kerja ini' }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex justify-content-end gap-3">
                            <div class="text-center">
                                <div class="fs-4 fw-bold">{{ $assignedUnits->count() }}</div>
                                <small class="opacity-75">Unit Terpasang</small>
                            </div>
                            <div class="text-center">
                                <div class="fs-4 fw-bold">{{ $availableUnits->count() }}</div>
                                <small class="opacity-75">Unit Tersedia</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Interface -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header panel-header available">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle text-success me-2"></i>
                            Unit Kompetensi Tersedia
                            <span class="badge bg-success ms-2" id="available-count">
                                {{ $availableUnits->count() }}
                            </span>
                        </h5>
                        <small class="text-muted">Drag ke sebelah kanan untuk menambahkan ke kelompok kerja</small>
                    </div>
                    <div class="card-body p-0">
                        <!-- Search Box for Available Units -->
                        <div class="search-box">
                            <input type="text" 
                                   id="search-available" 
                                   class="form-control form-control-sm" 
                                   placeholder="Cari unit kompetensi tersedia..."
                                   onkeyup="filterUnits('available')">
                        </div>

                        <div class="drop-zone available" id="available-zone">
                            <div class="drop-zone-placeholder">
                                <i class="bi bi-clipboard-check fs-1 text-muted"></i>
                                <p class="mb-0">Unit kompetensi tersedia akan muncul di sini</p>
                            </div>
                            
                            @foreach($availableUnits as $unit)
                                <div class="unit-item available" data-id="{{ $unit->id }}" data-search="{{ strtolower($unit->kode_unit . ' ' . $unit->judul_unit) }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <i class="bi bi-grip-vertical drag-handle"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <span class="unit-code">{{ $unit->kode_unit }}</span>
                                                </h6>
                                                <p class="mb-1 small">{{ Str::limit($unit->judul_unit, 80) }}</p>
                                                <div class="unit-meta">
                                                    <i class="bi bi-layers"></i> {{ $unit->elemenKompetensis->count() }} elemen
                                                    <span class="ms-3">
                                                        <i class="bi bi-check2-square"></i> {{ $unit->elemenKompetensis->sum(fn($e) => $e->kriteriaKerjas->count()) }} kriteria
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="move-button move-to-assigned" 
                                                onclick="moveToAssigned({{ $unit->id }})"
                                                title="Tambahkan ke kelompok kerja">
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header panel-header assigned">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle text-danger me-2"></i>
                            Unit dalam Kelompok Kerja
                            <span class="badge bg-danger ms-2" id="assigned-count">
                                {{ $assignedUnits->count() }}
                            </span>
                        </h5>
                        <small class="text-muted">Drag untuk mengatur urutan atau pindah ke kiri untuk melepas</small>
                    </div>
                    <div class="card-body p-0">
                        <!-- Search Box for Assigned Units -->
                        <div class="search-box">
                            <input type="text" 
                                   id="search-assigned" 
                                   class="form-control form-control-sm" 
                                   placeholder="Cari unit dalam kelompok kerja..."
                                   onkeyup="filterUnits('assigned')">
                        </div>

                        <div class="drop-zone assigned" id="assigned-zone">
                            <div class="drop-zone-placeholder">
                                <i class="bi bi-check2-square fs-1 text-muted"></i>
                                <p class="mb-0">Unit kompetensi dalam kelompok kerja akan muncul di sini</p>
                            </div>

                            @foreach($assignedUnits->sortBy('pivot.sort_order') as $unit)
                                <div class="unit-item assigned" data-id="{{ $unit->id }}" data-search="{{ strtolower($unit->kode_unit . ' ' . $unit->judul_unit) }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <i class="bi bi-grip-vertical drag-handle"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <span class="unit-code">{{ $unit->kode_unit }}</span>
                                                    @if($unit->pivot && $unit->pivot->is_active)
                                                        <span class="badge bg-success ms-2">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary ms-2">Nonaktif</span>
                                                    @endif
                                                </h6>
                                                <p class="mb-1 small">{{ Str::limit($unit->judul_unit, 70) }}</p>
                                                <div class="unit-meta">
                                                    <i class="bi bi-sort-numeric-up"></i> 
                                                    Urutan: {{ $unit->pivot ? $unit->pivot->sort_order : 'N/A' }}
                                                    <span class="ms-3">
                                                        <i class="bi bi-layers"></i> {{ $unit->elemenKompetensis->count() }} elemen
                                                    </span>
                                                    <div class="mt-1">
                                                        <i class="bi bi-calendar-plus"></i> 
                                                        Ditambahkan: {{ $unit->pivot ? $unit->pivot->created_at->format('d M Y') : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unit]) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Lihat detail unit">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="toggleStatus({{ $unit->id }})"
                                                    title="Toggle status">
                                                <i class="bi bi-toggle-{{ $unit->pivot && $unit->pivot->is_active ? 'on' : 'off' }}"></i>
                                            </button>
                                            <button type="button" class="move-button move-to-available" 
                                                    onclick="moveToAvailable({{ $unit->id }})"
                                                    title="Lepas dari kelompok kerja">
                                                <i class="bi bi-arrow-left"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Perubahan akan tersimpan otomatis
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="saveAll()" id="save-btn">
                        <i class="bi bi-check-lg me-1"></i> Simpan Semua Perubahan
                    </button>
                    <a href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompokKerja]) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100" 
     style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="bg-white p-4 rounded shadow">
            <div class="text-center">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>Menyimpan perubahan...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let pendingChanges = [];
let sortableAvailable, sortableAssigned;

document.addEventListener('DOMContentLoaded', function() {
    initializeSortable();
    
    // Add CSRF token if not exists
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
});

function initializeSortable() {
    const availableZone = document.getElementById('available-zone');
    const assignedZone = document.getElementById('assigned-zone');

    sortableAvailable = Sortable.create(availableZone, {
        group: 'unit-kompetensi',
        animation: 150,
        handle: '.drag-handle',
        filter: '.search-box',
        onStart: function(evt) {
            evt.item.classList.add('dragging');
        },
        onEnd: function(evt) {
            evt.item.classList.remove('dragging');
        },
        onAdd: function(evt) {
            const item = evt.item;
            const unitId = item.getAttribute('data-id');
            
            // Update classes
            item.classList.remove('assigned');
            item.classList.add('available');
            
            // Update button
            const buttonContainer = item.querySelector('.d-flex:last-child');
            buttonContainer.innerHTML = `
                <button type="button" class="move-button move-to-assigned" 
                        onclick="moveToAssigned(${unitId})"
                        title="Tambahkan ke kelompok kerja">
                    <i class="bi bi-arrow-right"></i>
                </button>
            `;
            
            // Remove status badge and pivot info
            const badges = item.querySelectorAll('.badge');
            badges.forEach(badge => {
                if (badge.textContent === 'Aktif' || badge.textContent === 'Nonaktif') {
                    badge.remove();
                }
            });
            
            // Update meta info
            const metaDiv = item.querySelector('.unit-meta');
            const unitCode = item.querySelector('.unit-code').textContent;
            const elementsCount = metaDiv.textContent.match(/(\d+) elemen/)[1];
            
            // Get criteria count from original data or calculate
            let criteriaCount = 0;
            const criteriaMatch = metaDiv.textContent.match(/(\d+) kriteria/);
            if (criteriaMatch) {
                criteriaCount = criteriaMatch[1];
            }
            
            metaDiv.innerHTML = `
                <i class="bi bi-layers"></i> ${elementsCount} elemen
                <span class="ms-3">
                    <i class="bi bi-check2-square"></i> ${criteriaCount} kriteria
                </span>
            `;
            
            // Add to pending changes
            addToPendingChanges({
                action: 'remove',
                unit_id: unitId
            });
            
            updateCounts();
        }
    });

    sortableAssigned = Sortable.create(assignedZone, {
        group: 'unit-kompetensi',
        animation: 150,
        handle: '.drag-handle',
        filter: '.search-box',
        onStart: function(evt) {
            evt.item.classList.add('dragging');
        },
        onEnd: function(evt) {
            evt.item.classList.remove('dragging');
            
            // Update sort order for all items in assigned zone
            const items = assignedZone.querySelectorAll('.unit-item');
            items.forEach((item, index) => {
                const unitId = item.getAttribute('data-id');
                addToPendingChanges({
                    action: 'update_sort',
                    unit_id: unitId,
                    sort_order: index + 1
                });
                
                // Update UI
                const metaDiv = item.querySelector('.unit-meta');
                const metaHTML = metaDiv.innerHTML;
                metaDiv.innerHTML = metaHTML.replace(/Urutan: \d+/, `Urutan: ${index + 1}`);
            });
        },
        onAdd: function(evt) {
            const item = evt.item;
            const unitId = item.getAttribute('data-id');
            
            // Update classes
            item.classList.remove('available');
            item.classList.add('assigned');
            
            // Update button and add controls
            const buttonContainer = item.querySelector('.d-flex:last-child');
            buttonContainer.innerHTML = `
                <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, 'UNIT_ID']) }}".replace('UNIT_ID', ${unitId})
                   class="btn btn-sm btn-outline-info" 
                   title="Lihat detail unit">
                    <i class="bi bi-eye"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-warning" 
                        onclick="toggleStatus(${unitId})"
                        title="Toggle status">
                    <i class="bi bi-toggle-on"></i>
                </button>
                <button type="button" class="move-button move-to-available" 
                        onclick="moveToAvailable(${unitId})"
                        title="Lepas dari kelompok kerja">
                    <i class="bi bi-arrow-left"></i>
                </button>
            `;
            
            // Add status badge
            const titleElement = item.querySelector('h6');
            titleElement.innerHTML += ' <span class="badge bg-success ms-2">Aktif</span>';
            
            // Update meta info
            const metaDiv = item.querySelector('.unit-meta');
            const elementsCount = metaDiv.textContent.match(/(\d+) elemen/)[1];
            let criteriaCount = 0;
            const criteriaMatch = metaDiv.textContent.match(/(\d+) kriteria/);
            if (criteriaMatch) {
                criteriaCount = criteriaMatch[1];
            }
            
            metaDiv.innerHTML = `
                <i class="bi bi-sort-numeric-up"></i> 
                Urutan: ${evt.newIndex + 1}
                <span class="ms-3">
                    <i class="bi bi-layers"></i> ${elementsCount} elemen
                </span>
                <div class="mt-1">
                    <i class="bi bi-calendar-plus"></i> 
                    Ditambahkan: Baru
                </div>
            `;
            
            // Add to pending changes
            addToPendingChanges({
                action: 'add',
                unit_id: unitId,
                sort_order: evt.newIndex + 1
            });
            
            updateCounts();
        }
    });
}

function moveToAssigned(unitId) {
    const item = document.querySelector(`.unit-item[data-id="${unitId}"]`);
    const assignedZone = document.getElementById('assigned-zone');
    
    assignedZone.appendChild(item);
    
    // Trigger the onAdd event manually
    sortableAssigned.option("onAdd")({ item: item, newIndex: assignedZone.querySelectorAll('.unit-item').length - 1 });
}

function moveToAvailable(unitId) {
    const item = document.querySelector(`.unit-item[data-id="${unitId}"]`);
    const availableZone = document.getElementById('available-zone');
    
    availableZone.appendChild(item);
    
    // Trigger the onAdd event manually
    sortableAvailable.option("onAdd")({ item: item });
}

function toggleStatus(unitId) {
    const item = document.querySelector(`.unit-item[data-id="${unitId}"]`);
    const statusBtn = item.querySelector('.btn-outline-warning i');
    const badge = item.querySelector('.badge');
    
    const isCurrentlyActive = statusBtn.classList.contains('bi-toggle-on');
    
    // Toggle UI
    if (isCurrentlyActive) {
        statusBtn.classList.remove('bi-toggle-on');
        statusBtn.classList.add('bi-toggle-off');
        badge.classList.remove('bg-success');
        badge.classList.add('bg-secondary');
        badge.textContent = 'Nonaktif';
    } else {
        statusBtn.classList.remove('bi-toggle-off');
        statusBtn.classList.add('bi-toggle-on');
        badge.classList.remove('bg-secondary');
        badge.classList.add('bg-success');
        badge.textContent = 'Aktif';
    }
    
    // Add to pending changes
    addToPendingChanges({
        action: 'toggle_status',
        unit_id: unitId,
        is_active: !isCurrentlyActive
    });
}

function filterUnits(zone) {
    const searchInput = document.getElementById(`search-${zone}`);
    const searchTerm = searchInput.value.toLowerCase();
    const items = document.querySelectorAll(`#${zone}-zone .unit-item`);
    
    items.forEach(item => {
        const searchText = item.getAttribute('data-search');
        if (searchText.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function addToPendingChanges(change) {
    // Remove any existing changes for this unit_id with the same action
    pendingChanges = pendingChanges.filter(c => 
        !(c.unit_id === change.unit_id && c.action === change.action)
    );
    
    pendingChanges.push(change);
    
    // Update save button state
    const saveBtn = document.getElementById('save-btn');
    saveBtn.classList.remove('btn-success');
    saveBtn.classList.add('btn-warning');
    saveBtn.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Ada Perubahan - Klik untuk Simpan';
}

function updateCounts() {
    const availableCount = document.getElementById('available-zone').querySelectorAll('.unit-item').length;
    const assignedCount = document.getElementById('assigned-zone').querySelectorAll('.unit-item').length;
    
    document.getElementById('available-count').textContent = availableCount;
    document.getElementById('assigned-count').textContent = assignedCount;
}

function saveAll() {
    if (pendingChanges.length === 0) {
        showAlert('info', 'Tidak ada perubahan untuk disimpan.');
        return;
    }
    
    const loadingOverlay = document.getElementById('loading-overlay');
    loadingOverlay.classList.remove('d-none');
    
    // Collect assigned unit IDs in order
    const assignedItems = document.querySelectorAll('#assigned-zone .unit-item');
    const unitKompetensiIds = Array.from(assignedItems).map(item => 
        parseInt(item.getAttribute('data-id'))
    );
    
    fetch('{{ route("admin.schemes.kelompok-kerja.update-unit-kompetensi", [$scheme, $kelompokKerja]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            unit_kompetensi_ids: unitKompetensiIds,
            changes: pendingChanges
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingOverlay.classList.add('d-none');
        
        if (data.success) {
            showAlert('success', data.message);
            pendingChanges = [];
            
            // Reset save button
            const saveBtn = document.getElementById('save-btn');
            saveBtn.classList.remove('btn-warning');
            saveBtn.classList.add('btn-success');
            saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Tersimpan';
            
            setTimeout(() => {
                saveBtn.classList.remove('btn-success');
                saveBtn.classList.add('btn-success');
                saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Semua Perubahan';
            }, 2000);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        loadingOverlay.classList.add('d-none');
        console.error('Error:', error);
        showAlert('danger', 'Terjadi kesalahan saat menyimpan perubahan.');
    });
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
</script>
@endpush