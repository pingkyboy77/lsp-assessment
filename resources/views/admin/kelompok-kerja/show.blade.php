{{-- resources/views/admin/kelompok-kerja/show.blade.php --}}
@extends('layouts.admin')

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
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema Sertifikasi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ Str::limit($scheme->nama, 20) }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}">Kelompok Kerja</a></li>
                            <li class="breadcrumb-item active">{{ Str::limit($kelompokKerja->nama_kelompok, 20) }}</li>
                        </ol>
                    </nav>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.schemes.kelompok-kerja.edit', [$scheme, $kelompokKerja]) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <div class="dropdown">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> Action
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.schemes.kelompok-kerja.manage-unit-kompetensi', [$scheme, $kelompokKerja]) }}">
                                    <i class="bi bi-clipboard-check me-2"></i> Kelola Unit Kompetensi
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
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
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item text-danger" onclick="deleteKelompok({{ $kelompokKerja->id }})">
                                    <i class="bi bi-trash me-2"></i> Hapus
                                </button>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
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
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nama Kelompok:</strong><br>
                                    {{ $kelompokKerja->nama_kelompok }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-{{ $kelompokKerja->is_active ? 'success' : 'secondary' }}">
                                        <i class="bi bi-{{ $kelompokKerja->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                        {{ $kelompokKerja->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                            
                            @if ($kelompokKerja->deskripsi)
                                <div class="mt-3">
                                    <strong>Deskripsi:</strong><br>
                                    <p class="text-muted">{{ $kelompokKerja->deskripsi }}</p>
                                </div>
                            @endif

                            <div class="mt-3">
                                <strong>Skema Sertifikasi:</strong><br>
                                <a href="{{ route('admin.certification-schemes.show', $scheme) }}" class="text-decoration-none">
                                    {{ $scheme->nama }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="card text-center bg-primary text-white">
                                <div class="card-body">
                                    <i class="bi bi-clipboard-check display-6 mb-2"></i>
                                    <h2 class="mb-1">{{ $kelompokKerja->unitKompetensis->count() }}</h2>
                                    <p class="mb-0">Total Unit Kompetensi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="card text-center bg-success text-white">
                                <div class="card-body">
                                    <i class="bi bi-check-circle display-6 mb-2"></i>
                                    <h2 class="mb-1">{{ $kelompokKerja->unitKompetensis->where('pivot.is_active', true)->count() }}</h2>
                                    <p class="mb-0">Unit Aktif</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card text-center bg-warning text-white">
                                <div class="card-body">
                                    <i class="bi bi-layers display-6 mb-2"></i>
                                    <h2 class="mb-1">{{ $kelompokKerja->unitKompetensis->sum(fn($unit) => $unit->elemenKompetensis->count()) }}</h2>
                                    <p class="mb-0">Total Elemen</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Unit Kompetensi Section -->
            <div class="card mb-4">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <h6 class="mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>Unit Kompetensi dalam Kelompok Kerja
                    </h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.schemes.kelompok-kerja.manage-unit-kompetensi', [$scheme, $kelompokKerja]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-gear"></i> Kelola Unit Kompetensi
                        </a>
                    </div>
                </div>

                @if ($kelompokKerja->unitKompetensis->count() > 0)
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-units" type="button" role="tab">
                                    Semua ({{ $kelompokKerja->unitKompetensis->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-units" type="button" role="tab">
                                    Aktif ({{ $kelompokKerja->unitKompetensis->where('pivot.is_active', true)->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive-units" type="button" role="tab">
                                    Nonaktif ({{ $kelompokKerja->unitKompetensis->where('pivot.is_active', false)->count() }})
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="all-units" role="tabpanel">
                                <div class="row">
                                    @forelse ($kelompokKerja->unitKompetensis->sortBy('pivot.sort_order') as $unit)
                                        <div class="col-lg-6 mb-3">
                                            <div class="card {{ !$unit->pivot->is_active ? 'opacity-75' : '' }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-1">{{ $unit->judul_unit }}</h6>
                                                        <div class="d-flex gap-1">
                                                            <span class="badge bg-{{ $unit->pivot->is_active ? 'success' : 'secondary' }}">
                                                                {{ $unit->pivot->is_active ? 'Aktif' : 'Nonaktif' }}
                                                            </span>
                                                            <span class="badge bg-light text-dark">
                                                                #{{ $unit->pivot->sort_order }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="text-muted small mb-2">{{ $unit->kode_unit }}</p>
                                                    <div class="d-flex justify-content-between text-muted small mb-2">
                                                        <span>Elemen: {{ $unit->elemenKompetensis->count() }}</span>
                                                    </div>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unit]) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i> Detail
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="text-center py-4">
                                                <i class="bi bi-info-circle text-muted"></i>
                                                <p class="text-muted mt-2">Belum ada unit kompetensi dalam kelompok kerja ini.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="tab-pane fade" id="active-units" role="tabpanel">
                                <div class="row">
                                    @forelse($kelompokKerja->unitKompetensis->where('pivot.is_active', true)->sortBy('pivot.sort_order') as $unit)
                                        <div class="col-lg-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-1">{{ $unit->judul_unit }}</h6>
                                                        <span class="badge bg-light text-dark">
                                                            #{{ $unit->pivot->sort_order }}
                                                        </span>
                                                    </div>
                                                    <p class="text-muted small mb-2">{{ $unit->kode_unit }}</p>
                                                    <div class="d-flex justify-content-between text-muted small mb-2">
                                                        <span>Elemen: {{ $unit->elemenKompetensis->count() }}</span>
                                                    </div>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unit]) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i> Detail
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-warning" onclick="toggleUnitStatus({{ $unit->id }})">
                                                            <i class="bi bi-toggle-off"></i> Nonaktifkan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="text-center py-4">
                                                <i class="bi bi-info-circle text-muted"></i>
                                                <p class="text-muted mt-2">Belum ada unit kompetensi aktif dalam kelompok kerja ini.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="tab-pane fade" id="inactive-units" role="tabpanel">
                                <div class="row">
                                    @forelse($kelompokKerja->unitKompetensis->where('pivot.is_active', false)->sortBy('pivot.sort_order') as $unit)
                                        <div class="col-lg-6 mb-3">
                                            <div class="card opacity-75">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-1">{{ $unit->judul_unit }}</h6>
                                                        <span class="badge bg-light text-dark">
                                                            #{{ $unit->pivot->sort_order }}
                                                        </span>
                                                    </div>
                                                    <p class="text-muted small mb-2">{{ $unit->kode_unit }}</p>
                                                    <div class="d-flex justify-content-between text-muted small mb-2">
                                                        <span>Elemen: {{ $unit->elemenKompetensis->count() }}</span>
                                                    </div>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unit]) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i> Detail
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-warning" onclick="toggleUnitStatus({{ $unit->id }})">
                                                            <i class="bi bi-toggle-on"></i> Aktifkan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="text-center py-4">
                                                <i class="bi bi-check-circle text-success"></i>
                                                <p class="text-muted mt-2">Semua unit kompetensi dalam kelompok kerja ini sudah aktif!</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
                        <h5>Belum ada Unit Kompetensi</h5>
                        <p>Kelompok kerja ini belum memiliki unit kompetensi. Mulai dengan menambahkan unit kompetensi.</p>
                        <a href="{{ route('admin.schemes.kelompok-kerja.manage-unit-kompetensi', [$scheme, $kelompokKerja]) }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-2"></i>Tambah Unit Kompetensi
                        </a>
                    </div>
                @endif
            </div>

            <!-- Metadata and Actions -->
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Informasi Tambahan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-2"><strong>Dibuat:</strong><br>{{ $kelompokKerja->created_at->format('d M Y, H:i') }}</p>
                                    <p class="mb-2"><strong>Diperbarui:</strong><br>{{ $kelompokKerja->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-2"><strong>Urutan:</strong><br>#{{ $kelompokKerja->sort_order }}</p>
                                    @if ($kelompokKerja->updated_at != $kelompokKerja->created_at)
                                        <p class="mb-2"><strong>Terakhir diubah:</strong><br>{{ $kelompokKerja->updated_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-bar-chart me-2"></i>Statistik
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-2">
                                    <div class="border-end">
                                        <h5 class="mb-0 text-primary">{{ $kelompokKerja->unitKompetensis->count() }}</h5>
                                        <small class="text-muted">Total Units</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <h5 class="mb-0 text-success">{{ $kelompokKerja->unitKompetensis->where('pivot.is_active', true)->count() }}</h5>
                                    <small class="text-muted">Active Units</small>
                                </div>
                                <div class="col-12">
                                    <h5 class="mb-0 text-info">{{ $kelompokKerja->unitKompetensis->sum(fn($unit) => $unit->elemenKompetensis->count()) }}</h5>
                                    <small class="text-muted">Elements</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Duplicate Kelompok Modal -->
    <div class="modal fade" id="duplicateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-copy me-2"></i>Duplikasi Kelompok Kerja
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="duplicateForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="duplicateName" class="form-label">Nama Kelompok Kerja Baru *</label>
                            <input type="text" class="form-control" id="duplicateName" name="nama_kelompok" required maxlength="200">
                            <div class="form-text">Maksimal 200 karakter</div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="copyUnits" name="copy_units" checked>
                            <label class="form-check-label" for="copyUnits">
                                Salin semua unit kompetensi yang terdaftar
                            </label>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Kelompok kerja hasil duplikasi akan dibuat dalam status nonaktif.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-copy me-2"></i>Duplikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-trash me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kelompok kerja ini?</p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Semua relasi dengan unit kompetensi akan ikut terhapus dan tidak dapat dikembalikan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-2"></i>Hapus Kelompok Kerja
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.schemeId = {{ $scheme->id }};
        window.kelompokId = {{ $kelompokKerja->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // Kelompok Kerja management functions
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
                            showAlert('error', data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('error', 'Terjadi kesalahan saat mengubah status.');
                    });
            }
        }

        function duplicateKelompok(kelompokId) {
            document.getElementById('duplicateName').value = '{{ $kelompokKerja->nama_kelompok }} (Copy)';
            const modal = new bootstrap.Modal(document.getElementById('duplicateModal'));
            modal.show();
        }

        function deleteKelompok(kelompokId) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Unit management functions
        function toggleUnitStatus(unitId) {
            if (confirm('Apakah Anda yakin ingin mengubah status unit ini dalam kelompok kerja?')) {
                fetch(`/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/unit-kompetensi/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        unit_kompetensi_id: unitId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('error', data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat mengubah status unit.');
                });
            }
        }

        function removeUnit(unitId, unitTitle) {
            if (confirm(`Apakah Anda yakin ingin menghapus "${unitTitle}" dari kelompok kerja ini?`)) {
                fetch(`/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/unit-kompetensi/remove`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        unit_kompetensi_id: unitId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('error', data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat menghapus unit.');
                });
            }
        }

        // Event handlers
        document.getElementById('duplicateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menduplikasi...';
            
            fetch(`/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}/duplicate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('duplicateModal'));
                    modal.hide();
                    setTimeout(() => {
                        window.location.href = `/admin/schemes/${window.schemeId}/kelompok-kerja/${data.data.id}`;
                    }, 1000);
                } else {
                    showAlert('error', data.message || 'Terjadi kesalahan saat menduplikasi');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat menduplikasi kelompok kerja.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/schemes/${window.schemeId}/kelompok-kerja/${window.kelompokId}`;
            
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = csrfToken;
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfField);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        });

        // Utility functions
        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
            
            const alertHtml = `
                <div class="${alertClass} alert alert-dismissible fade show" role="alert">
                    <i class="bi ${icon} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Insert at the top of the card body
            const cardBody = document.querySelector('.card-body');
            cardBody.insertAdjacentHTML('afterbegin', alertHtml);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                const alert = cardBody.querySelector('.alert');
                if (alert) {
                    const alertInstance = new bootstrap.Alert(alert);
                    alertInstance.close();
                }
            }, 5000);
        }
    </script>

    @endpush