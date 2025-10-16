@extends('layouts.admin')

@section('title', 'Unit Kompetensi - ' . $scheme->nama)

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .unit-item {
            transition: all 0.2s ease;
        }

        .unit-item:hover {
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

        .kelompok-badge {
            font-size: 0.75rem;
            margin-bottom: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="main-card">
        <!-- Page Header -->


        <div class="card-header-custom">
            @if (session('success'))
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-list-check me-2"></i>Unit Kompetensi
                    </h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 flex-wrap">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema
                                    Sertifikasi</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ Str::limit($scheme->nama, 20) }}</a>
                            </li>
                            <li class="breadcrumb-item">Unit Kompetensi</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() }}"
                        class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                    <a href="{{ route('admin.schemes.unit-kompetensi.create', $scheme) }}"
                        class=" btn-primary-custom btn-sm text-light">
                        <i class="bi bi-plus-lg"></i> Tambah Unit Kompetensi
                    </a>
                </div>
            </div>
        </div>


        <!-- Scheme Info Card -->
        <div class="card m-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title mb-1">{{ $scheme->nama }}</h5>
                        <p class="text-muted mb-2">
                            {{ $scheme->code_1 }} | Jenjang:
                            <span class="badge bg-{{ $scheme->jenjang_color }}">{{ $scheme->jenjang }}</span>
                        </p>
                        <p class="mb-0">
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}"
                                class="btn btn-outline-info">
                                <i class="bi bi-people"></i> Kelompok Kerja
                            </a>
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear"></i> Aksi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.schemes.unit-kompetensi.export', $scheme) }}">
                                        <i class="bi bi-download me-2"></i> Export Data
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="reorderMode()">
                                        <i class="bi bi-arrow-down-up me-2"></i> Ubah Urutan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="card m-3">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput"
                                placeholder="Cari unit kompetensi...">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="statusFilter" id="statusAll" value="all"
                                checked>
                            <label class="btn btn-outline-secondary" for="statusAll">Semua</label>

                            <input type="radio" class="btn-check" name="statusFilter" id="statusActive" value="active">
                            <label class="btn btn-outline-success" for="statusActive">Aktif</label>

                            <input type="radio" class="btn-check" name="statusFilter" id="statusInactive"
                                value="inactive">
                            <label class="btn btn-outline-secondary" for="statusInactive">Nonaktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Units List -->
        <div class="card m-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Daftar Unit Kompetensi ({{ $units->total() }})</h6>
                    <div class="d-none" id="reorder-controls">
                        <button class="btn btn-success btn-sm" onclick="saveReorder()">
                            <i class="bi bi-check2-circle"></i> Simpan Urutan
                        </button>
                        <button class="btn btn-secondary btn-sm ms-2" onclick="cancelReorder()">
                            <i class="bi bi-x-lg"></i> Batal
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($units->count() > 0)
                    <div id="units-container">
                        @foreach ($units as $unit)
                            <div class="unit-item border-bottom p-3" data-id="{{ $unit->id }}"
                                data-status="{{ $unit->is_active ? 'active' : 'inactive' }}"
                                data-search="{{ strtolower($unit->kode_unit . ' ' . $unit->judul_unit) }}">
                                <div class="row align-items-center">
                                    <div class="col-md-1 text-center d-none reorder-handle">
                                        <i class="bi bi-grip-vertical text-muted"></i>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 me-3">{{ $unit->kode_unit }}</h6>
                                            <span class="badge bg-{{ $unit->is_active ? 'success' : 'secondary' }}">
                                                {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </div>
                                        <p class="mb-1 fw-medium">{{ $unit->judul_unit }}</p>

                                        @if ($unit->standar_kompetensi_kerja)
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-hammer"></i>
                                                {{ Str::limit($unit->standar_kompetensi_kerja, 80) }}
                                            </small>
                                        @endif

                                        @if (isset($unit->activeKelompokKerjas) && $unit->activeKelompokKerjas->count() > 0)
                                            <div class="mb-2">
                                                @foreach ($unit->activeKelompokKerjas as $kelompok)
                                                    <span class="badge bg-info me-1 kelompok-badge">
                                                        <i class="bi bi-people"></i> {{ $kelompok->nama_kelompok }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mt-2">
                                            <small class="text-info">
                                                <i class="bi bi-list-task"></i>
                                                {{ $unit->elemenKompetensis->count() }} Elemen Kompetensi
                                                <span class="mx-1">•</span>
                                                <i class="bi bi-check2-circle"></i>
                                                {{ $unit->elemenKompetensis->sum(fn($e) => $e->kriteriaKerjas->count()) }}
                                                Kriteria Kerja
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <div class="btn-group normal-controls">
                                            <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unit]) }}"
                                                class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.schemes.unit-kompetensi.edit', [$scheme, $unit]) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false" title="Aksi Lainnya">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <button class="dropdown-item"
                                                            onclick="toggleStatus({{ $unit->id }})">
                                                            <i
                                                                class="bi bi-toggle-{{ $unit->is_active ? 'on' : 'off' }} me-2"></i>
                                                            {{ $unit->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item"
                                                            onclick="duplicateUnit({{ $unit->id }})">
                                                            <i class="bi bi-files me-2"></i> Duplikasi
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-danger"
                                                            onclick="deleteUnit({{ $unit->id }})">
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

                    <!-- Pagination -->
                    <div class="p-3">
                        {{ $units->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-check fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada Unit Kompetensi</h5>
                        <p class="text-muted">Mulai dengan menambahkan unit kompetensi pertama untuk skema ini.</p>
                        <a href="{{ route('admin.schemes.unit-kompetensi.create', $scheme) }}"
                            class=" btn-primary-custom btn-sm text-light">
                            <i class="bi bi-plus-lg"></i> Tambah Unit Kompetensi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus unit kompetensi ini?</p>
                    <p class="text-danger">
                        <strong>Peringatan:</strong> Semua elemen kompetensi, kriteria kerja, dan relasi dengan kelompok
                        kerja akan ikut terhapus.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        let sortable = null;
        let isReorderMode = false;

        // Function to enter reorder mode
        function reorderMode() {
            isReorderMode = true;

            // Show reorder controls and hide normal controls
            document.getElementById('reorder-controls').classList.remove('d-none');
            document.querySelectorAll('.normal-controls').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.reorder-handle').forEach(el => el.classList.remove('d-none'));

            // Adjust column sizes for reorder mode
            document.querySelectorAll('.unit-item .col-md-8').forEach(el => el.classList.replace('col-md-8', 'col-md-7'));
            document.querySelectorAll('.unit-item .col-md-3').forEach(el => el.classList.replace('col-md-3', 'col-md-4'));

            // Initialize sortable
            const container = document.getElementById('units-container');
            if (container) {
                sortable = new Sortable(container, {
                    handle: '.reorder-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                });
            }
        }

        // Function to cancel reorder
        function cancelReorder() {
            isReorderMode = false;

            // Hide reorder controls and show normal controls
            document.getElementById('reorder-controls').classList.add('d-none');
            document.querySelectorAll('.normal-controls').forEach(el => el.classList.remove('d-none'));
            document.querySelectorAll('.reorder-handle').forEach(el => el.classList.add('d-none'));

            // Restore column sizes
            document.querySelectorAll('.unit-item .col-md-7').forEach(el => el.classList.replace('col-md-7', 'col-md-8'));
            document.querySelectorAll('.unit-item .col-md-4').forEach(el => el.classList.replace('col-md-4', 'col-md-3'));

            // Destroy sortable
            if (sortable) {
                sortable.destroy();
                sortable = null;
            }

            // Refresh page to restore original order
            location.reload();
        }

        // Function to save reorder
        function saveReorder() {
            if (!sortable) return;

            const unitIds = [];
            const items = document.querySelectorAll('.unit-item');
            items.forEach(item => {
                unitIds.push(item.dataset.id);
            });

            // Send AJAX request
            fetch('{{ route('admin.schemes.unit-kompetensi.reorder', $scheme) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        unit_ids: unitIds
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

        function toggleStatus(unitId) {
            fetch(`{{ route('admin.schemes.unit-kompetensi.toggle-status', [$scheme, 'UNIT_ID']) }}`.replace('UNIT_ID',
                    unitId), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => updateUnitStatus(unitId, data.is_active), 500);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Terjadi kesalahan saat mengubah status.');
                });
        }


        // Function to update unit status in UI
        function updateUnitStatus(unitId, isActive) {
            const unitItem = document.querySelector(`[data-id="${unitId}"]`);
            if (unitItem) {
                const badge = unitItem.querySelector('.badge');
                const toggleButton = unitItem.querySelector(`[onclick="toggleStatus(${unitId})"]`);
                const toggleIcon = toggleButton.querySelector('i');

                if (isActive) {
                    badge.className = 'badge bg-success';
                    badge.textContent = 'Aktif';
                    toggleIcon.className = 'bi bi-toggle-on me-2';
                    toggleButton.innerHTML = '<i class="bi bi-toggle-on me-2"></i> Nonaktifkan';
                    unitItem.dataset.status = 'active';
                } else {
                    badge.className = 'badge bg-secondary';
                    badge.textContent = 'Nonaktif';
                    toggleIcon.className = 'bi bi-toggle-off me-2';
                    toggleButton.innerHTML = '<i class="bi bi-toggle-off me-2"></i> Aktifkan';
                    unitItem.dataset.status = 'inactive';
                }
            }
        }

        // Function to duplicate unit
        // Function to duplicate unit
        function duplicateUnit(unitId) {
            window.location.href = `{{ route('admin.schemes.unit-kompetensi.duplicate.form', [$scheme, 'UNIT_ID']) }}`
                .replace('UNIT_ID', unitId);
        }


        // Function to delete unit
        function deleteUnit(unitId) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('admin.schemes.unit-kompetensi.destroy', [$scheme, 'UNIT_ID']) }}`.replace('UNIT_ID',
                unitId);

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Function to show alert
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

            // Auto remove after 5 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
                alertContainer.remove();
            }, 5000);
        }

        // Search and filter functionality
        function initializeFilters() {
            const searchInput = document.getElementById('searchInput');
            const statusFilters = document.querySelectorAll('input[name="statusFilter"]');

            function filterUnits() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusFilter = document.querySelector('input[name="statusFilter"]:checked').value;
                const unitItems = document.querySelectorAll('.unit-item');

                unitItems.forEach(item => {
                    const searchData = item.dataset.search;
                    const status = item.dataset.status;

                    const matchesSearch = searchData.includes(searchTerm);
                    const matchesStatus = statusFilter === 'all' || status === statusFilter;

                    if (matchesSearch && matchesStatus) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterUnits);
            statusFilters.forEach(filter => {
                filter.addEventListener('change', filterUnits);
            });
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF token to meta if not exists
            if (!document.querySelector('meta[name="csrf-token"]')) {
                const meta = document.createElement('meta');
                meta.name = 'csrf-token';
                meta.content = '{{ csrf_token() }}';
                document.getElementsByTagName('head')[0].appendChild(meta);
            }

            // Initialize filters
            initializeFilters();
        });
    </script>
@endpush
