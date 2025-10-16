@extends('layouts.admin')

@section('title', 'Edit Halaman')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Halaman
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-eye"></i> Lihat
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.pages.update', $page) }}" id="pageForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <h6 class="border-bottom pb-2 mb-3">Informasi Dasar</h6>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nama Halaman <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $page->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="route_name" class="form-label">Route Name <span class="text-danger">*</span></label>
                                        <input type="text" name="route_name" id="route_name" class="form-control @error('route_name') is-invalid @enderror" 
                                               value="{{ old('route_name', $page->route_name) }}" required>
                                        @error('route_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Contoh: admin.users.index</div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="icon" class="form-label">Icon</label>
                                        <div class="input-group">
                                            <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror" 
                                                   value="{{ old('icon', $page->icon) }}" placeholder="bi bi-house">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#iconModal">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                        @error('icon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Bootstrap Icons class</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="group" class="form-label">Group</label>
                                        <div class="input-group">
                                            <input type="text" name="group" id="group" class="form-control @error('group') is-invalid @enderror" 
                                                   value="{{ old('group', $page->group) }}" list="groupList">
                                            <datalist id="groupList">
                                                <option value="main">Main</option>
                                                <option value="Admin Management">Admin Management</option>
                                                <option value="System & Monitoring">System & Monitoring</option>
                                                <option value="Asesi Menu">Asesi Menu</option>
                                                @foreach($groups as $existingGroup)
                                                    <option value="{{ $existingGroup }}">{{ $existingGroup }}</option>
                                                @endforeach
                                            </datalist>
                                        </div>
                                        @error('group')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Kosongkan untuk main group</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                              rows="3">{{ old('description', $page->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="sort_order" class="form-label">Urutan</label>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                               value="{{ old('sort_order', $page->sort_order) }}" min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="parent_route" class="form-label">Parent Route</label>
                                        <input type="text" name="parent_route" id="parent_route" class="form-control @error('parent_route') is-invalid @enderror" 
                                               value="{{ old('parent_route', $page->parent_route) }}">
                                        @error('parent_route')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Route induk (opsional)</div>
                                    </div>
                                </div>

                                <!-- Role Access -->
                                <h6 class="border-bottom pb-2 mb-3 mt-4">Akses Role</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Role yang Diizinkan</label>
                                    <div class="row">
                                        @foreach($roles as $role)
                                            <div class="col-md-4 col-sm-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="allowed_roles[]" 
                                                           value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                           {{ in_array($role->name, old('allowed_roles', $page->allowed_roles ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="form-text">Kosongkan untuk mengizinkan semua role</div>
                                    @error('allowed_roles')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Settings Sidebar -->
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">Pengaturan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                                       value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Status Aktif
                                                </label>
                                            </div>
                                            <small class="text-muted">Halaman dapat diakses</small>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_sidebar_menu" id="is_sidebar_menu" 
                                                       value="1" {{ old('is_sidebar_menu', $page->is_sidebar_menu) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_sidebar_menu">
                                                    Tampil di Sidebar
                                                </label>
                                            </div>
                                            <small class="text-muted">Halaman muncul di menu sidebar</small>
                                        </div>

                                        <hr>

                                        <!-- Preview Icon -->
                                        <div class="mb-3">
                                            <label class="form-label small">Preview Icon:</label>
                                            <div class="p-2 border rounded text-center">
                                                <i id="iconPreview" class="{{ $page->icon ?: 'bi bi-question' }} fs-3"></i>
                                            </div>
                                        </div>

                                        <!-- Page Info -->
                                        <div class="mb-3">
                                            <label class="form-label small">Informasi:</label>
                                            <div class="small text-muted">
                                                <div>Dibuat: {{ $page->created_at->format('d/m/Y H:i') }}</div>
                                                <div>Diupdate: {{ $page->updated_at->format('d/m/Y H:i') }}</div>
                                                <div>Slug: <code>{{ $page->slug }}</code></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        <button type="submit" class="btn btn-primary mb-2 w-100">
                                            <i class="bi bi-check-lg"></i> Update Halaman
                                        </button>
                                        
                                        <div class="btn-group w-100">
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-clockwise"></i> Reset
                                            </button>
                                            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-x"></i> Batal
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Icon Selection Modal -->
<div class="modal fade" id="iconModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <input type="text" id="iconSearch" class="form-control" placeholder="Cari icon...">
                    </div>
                </div>
                <div class="row" id="iconGrid">
                    <!-- Common Bootstrap Icons -->
                    @php
                        $commonIcons = [
                            'bi bi-speedometer2', 'bi bi-house', 'bi bi-people', 'bi bi-person',
                            'bi bi-gear', 'bi bi-file-earmark', 'bi bi-folder', 'bi bi-archive',
                            'bi bi-award', 'bi bi-bar-chart', 'bi bi-bell', 'bi bi-bookmark',
                            'bi bi-briefcase', 'bi bi-building', 'bi bi-calendar', 'bi bi-camera',
                            'bi bi-cart', 'bi bi-chat', 'bi bi-clipboard', 'bi bi-clock',
                            'bi bi-cloud', 'bi bi-cpu', 'bi bi-credit-card', 'bi bi-database',
                            'bi bi-diagram-3', 'bi bi-envelope', 'bi bi-eye', 'bi bi-globe',
                            'bi bi-graph-up', 'bi bi-heart', 'bi bi-image', 'bi bi-info-circle',
                            'bi bi-key', 'bi bi-layers', 'bi bi-layout-text-sidebar', 'bi bi-list',
                            'bi bi-lock', 'bi bi-map', 'bi bi-megaphone', 'bi bi-music-note',
                            'bi bi-newspaper', 'bi bi-palette', 'bi bi-person-badge', 'bi bi-phone',
                            'bi bi-pie-chart', 'bi bi-play', 'bi bi-plus', 'bi bi-printer',
                            'bi bi-question-circle', 'bi bi-search', 'bi bi-server', 'bi bi-shield',
                            'bi bi-star', 'bi bi-table', 'bi bi-tag', 'bi bi-tools',
                            'bi bi-trash', 'bi bi-upload', 'bi bi-user-check', 'bi bi-wallet',
                            'bi bi-wifi', 'bi bi-x-circle', 'bi bi-youtube', 'bi bi-zoom-in'
                        ];
                    @endphp
                    
                    @foreach($commonIcons as $icon)
                        <div class="col-2 mb-3 text-center">
                            <button type="button" class="btn btn-outline-secondary icon-btn w-100" data-icon="{{ $icon }}">
                                <i class="{{ $icon }} fs-4"></i>
                                <div class="small mt-1">{{ str_replace('bi bi-', '', $icon) }}</div>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto generate slug from name
    const nameInput = document.getElementById('name');
    const routeInput = document.getElementById('route_name');
    
    // Icon preview
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('iconPreview');
    
    iconInput.addEventListener('input', function() {
        iconPreview.className = this.value || 'bi bi-question fs-3';
    });

    // Icon modal functionality
    const iconModal = document.getElementById('iconModal');
    const iconButtons = document.querySelectorAll('.icon-btn');
    const iconSearch = document.getElementById('iconSearch');

    iconButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedIcon = this.dataset.icon;
            iconInput.value = selectedIcon;
            iconPreview.className = selectedIcon + ' fs-3';
            bootstrap.Modal.getInstance(iconModal).hide();
        });
    });

    // Icon search functionality
    iconSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        iconButtons.forEach(btn => {
            const iconName = btn.dataset.icon.toLowerCase();
            btn.parentElement.style.display = iconName.includes(searchTerm) ? 'block' : 'none';
        });
    });

    // Form validation
    document.getElementById('pageForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const routeName = document.getElementById('route_name').value.trim();
        
        if (!name || !routeName) {
            e.preventDefault();
            alert('Nama dan Route Name wajib diisi');
            return false;
        }
        
        // Validate route name format
        if (!/^[a-z0-9\-_.]+$/.test(routeName)) {
            e.preventDefault();
            alert('Route Name hanya boleh berisi huruf kecil, angka, titik, garis bawah, dan strip');
            return false;
        }
    });

    // Reset form
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        setTimeout(() => {
            iconPreview.className = '{{ $page->icon ?: "bi bi-question" }} fs-3';
        }, 100);
    });
});
</script>
@endpush

@push('styles')
<style>
.icon-btn {
    aspect-ratio: 1;
    padding: 0.5rem 0.25rem;
    font-size: 0.75rem;
}

.icon-btn:hover {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.form-check-input:checked {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
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

.bg-light .card-header {
    background-color: #e9ecef !important;
}

#iconPreview {
    color: var(--bs-primary);
}

.modal-body {
    max-height: 60vh;
    overflow-y: auto;
}
</style>
@endpush
@endsection