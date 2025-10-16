@extends('layouts.admin')

@section('title', 'Detail Halaman - ' . $page->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-eye me-2"></i>
                        Detail Halaman
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @if(!in_array($page->route_name, ['admin.dashboard', 'asesi.dashboard', 'admin.pages.index']))
                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Yakin ingin menghapus halaman ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Main Information -->
                        <div class="col-md-8">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        @if($page->icon)
                                            <div class="me-3">
                                                <i class="{{ $page->icon }} fs-1 text-primary"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="mb-1">{{ $page->name }}</h3>
                                            <div class="text-muted">
                                                <code>{{ $page->route_name }}</code>
                                                @if($page->group)
                                                    • Group: <span class="badge bg-secondary ms-1">{{ $page->group }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($page->description)
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">Deskripsi</h6>
                                    <p class="text-muted">{{ $page->description }}</p>
                                </div>
                            @endif

                            <!-- Basic Info -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Informasi Dasar</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td class="text-muted" width="120">Nama:</td>
                                                <td class="fw-medium">{{ $page->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Slug:</td>
                                                <td><code>{{ $page->slug }}</code></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Route:</td>
                                                <td><code>{{ $page->route_name }}</code></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Icon:</td>
                                                <td>
                                                    @if($page->icon)
                                                        <i class="{{ $page->icon }} me-2"></i>
                                                        <code>{{ $page->icon }}</code>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td class="text-muted" width="120">Group:</td>
                                                <td>
                                                    @if($page->group)
                                                        <span class="badge bg-secondary">{{ $page->group }}</span>
                                                    @else
                                                        <span class="badge bg-primary">Main</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Urutan:</td>
                                                <td><span class="badge bg-light text-dark">{{ $page->sort_order }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Parent Route:</td>
                                                <td>
                                                    @if($page->parent_route)
                                                        <code>{{ $page->parent_route }}</code>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Role Access -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Akses Role</h6>
                                @if($page->allowed_roles && count($page->allowed_roles) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($page->allowed_roles as $role)
                                            <span class="badge bg-primary">{{ $role }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Tidak ada pembatasan role - Semua user dapat mengakses
                                    </div>
                                @endif
                            </div>

                            <!-- Route Test -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">Test Route</h6>
                                <div class="d-flex align-items-center gap-2">
                                    @if(Route::has($page->route_name))
                                        <span class="badge bg-success">✓ Route tersedia</span>
                                        <a href="{{ route($page->route_name) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-box-arrow-up-right"></i> Buka Halaman
                                        </a>
                                    @else
                                        <span class="badge bg-danger">✗ Route tidak ditemukan</span>
                                        <small class="text-muted">Route belum didefinisikan di sistem</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Info -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Status & Pengaturan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Status:</label>
                                        <div>
                                            @if($page->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Tampil di Sidebar:</label>
                                        <div>
                                            @if($page->is_sidebar_menu)
                                                <span class="badge bg-success">Ya</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak</span>
                                            @endif
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Dibuat:</label>
                                        <div class="small">
                                            {{ $page->created_at->format('d F Y, H:i') }}
                                            <br>
                                            <span class="text-muted">({{ $page->created_at->diffForHumans() }})</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Terakhir Diupdate:</label>
                                        <div class="small">
                                            {{ $page->updated_at->format('d F Y, H:i') }}
                                            <br>
                                            <span class="text-muted">({{ $page->updated_at->diffForHumans() }})</span>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <hr>
                                    <div class="d-grid gap-2">
                                        <form method="POST" action="{{ route('admin.pages.toggle-status', $page) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $page->is_active ? 'warning' : 'success' }} w-100">
                                                @if($page->is_active)
                                                    <i class="bi bi-pause"></i> Nonaktifkan
                                                @else
                                                    <i class="bi bi-play"></i> Aktifkan
                                                @endif
                                            </button>
                                        </form>
                                        
                                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-warning w-100">
                                            <i class="bi bi-pencil"></i> Edit Halaman
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- System Protection Notice -->
                            @if(in_array($page->route_name, ['admin.dashboard', 'asesi.dashboard', 'admin.pages.index']))
                                <div class="card mt-3 border-warning">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center text-warning">
                                            <i class="bi bi-shield-exclamation fs-5 me-2"></i>
                                            <div>
                                                <strong>Halaman Sistem</strong>
                                                <small class="d-block">Halaman ini dilindungi dan tidak dapat dihapus</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Preview -->
                            @if($page->icon)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Preview Menu</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="p-3 border rounded">
                                            <div class="d-flex align-items-center justify-content-start">
                                                <i class="{{ $page->icon }} me-2"></i>
                                                <span>{{ $page->name }}</span>
                                            </div>
                                        </div>
                                        <small class="text-muted mt-2 d-block">Preview tampilan di sidebar</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.table-borderless td {
    border: none;
    padding: 0.375rem 0;
}

.border-bottom {
    border-bottom: 2px solid #e9ecef !important;
}

code {
    background-color: #f8f9fa;
    color: #d63384;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.85em;
}

.badge {
    font-size: 0.75rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.bg-light .card-header {
    background-color: #e9ecef !important;
}

.alert {
    font-size: 0.9rem;
}
</style>
@endpush
@endsection