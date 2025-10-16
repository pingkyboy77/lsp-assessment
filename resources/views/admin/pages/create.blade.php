@extends('layouts.admin')

@section('title', 'Tambah Halaman')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle me-2"></i>
                            Tambah Halaman Baru
                        </h5>
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.pages.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Informasi Dasar</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Halaman <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Route Name <span class="text-danger">*</span></label>
                                            <input type="text" name="route_name" class="form-control @error('route_name') is-invalid @enderror" 
                                                   value="{{ old('route_name') }}" placeholder="admin.pages.index" required>
                                            <div class="form-text">Format: prefix.controller.action (contoh: admin.users.index)</div>
                                            @error('route_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Icon</label>
                                            <div class="input-group">
                                                <input type="text" name="icon" id="iconInput" class="form-control @error('icon') is-invalid @enderror" 
                                                       value="{{ old('icon') }}" placeholder="bi bi-house">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#iconModal">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                            </div>
                                            <div class="form-text">
                                                Bootstrap Icons class (contoh: bi bi-house)
                                                <span id="iconPreview" class="ms-2"></span>
                                            </div>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                                      rows="3">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Pengaturan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Group</label>
                                            <input type="text" name="group" class="form-control @error('group') is-invalid @enderror" 
                                                   value="{{ old('group') }}" list="groupOptions">
                                            <datalist id="groupOptions">
                                                @foreach($groups as $group)
                                                    <option value="{{ $group }}">
                                                @endforeach
                                            </datalist>
                                            <div class="form-text">Grup menu untuk pengelompokan</div>
                                            @error('group')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Urutan</label>
                                            <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   value="{{ old('sort_order', 0) }}" min="0">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Parent Route</label>
                                            <input type="text" name="parent_route" class="form-control @error('parent_route') is-invalid @enderror" 
                                                   value="{{ old('parent_route') }}" placeholder="admin.users.*">
                                            <div class="form-text">Untuk submenu (opsional)</div>
                                            @error('parent_route')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <hr>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" 
                                                       id="isActive" {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="isActive">
                                                    Aktif
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_sidebar_menu" 
                                                       id="isSidebarMenu" {{ old('is_sidebar_menu', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="isSidebarMenu">
                                                    Tampilkan di Sidebar
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permissions -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Permission</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <label class="form-label">Role yang Diizinkan</label>
                                        </div>
                                        @foreach($roles as $role)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="allowed_roles[]" 
                                                       value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                       {{ in_array($role->name, old('allowed_roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    {{ ucfirst($role->name) }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="form-text mt-2">
                                            Jika tidak ada yang dipilih, semua role dapat mengakses.
                                        </div>
                                        @error('allowed_roles')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Halaman
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Icon Picker Modal -->
<div class="modal fade" id="iconModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Icon Bootstrap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Search Box -->
                <div class="mb-3">
                    <input type="text" class="form-control" id="iconSearch" placeholder="Cari icon...">
                </div>
                
                <!-- Icon Grid -->
                <div class="row g-2" id="iconList" style="max-height: 500px; overflow-y: auto;">
                    <!-- General -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-house" data-name="house">
                        <i class="bi bi-house fs-3"></i><br>
                        <small>house</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-house-door" data-name="house-door">
                        <i class="bi bi-house-door fs-3"></i><br>
                        <small>house-door</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-speedometer" data-name="speedometer">
                        <i class="bi bi-speedometer fs-3"></i><br>
                        <small>speedometer</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-speedometer2" data-name="speedometer2">
                        <i class="bi bi-speedometer2 fs-3"></i><br>
                        <small>speedometer2</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-grid" data-name="grid">
                        <i class="bi bi-grid fs-3"></i><br>
                        <small>grid</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-grid-3x3" data-name="grid-3x3">
                        <i class="bi bi-grid-3x3 fs-3"></i><br>
                        <small>grid-3x3</small>
                    </div>
                    
                    <!-- People & User -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-people" data-name="people">
                        <i class="bi bi-people fs-3"></i><br>
                        <small>people</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-person" data-name="person">
                        <i class="bi bi-person fs-3"></i><br>
                        <small>person</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-person-circle" data-name="person-circle">
                        <i class="bi bi-person-circle fs-3"></i><br>
                        <small>person-circle</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-person-badge" data-name="person-badge">
                        <i class="bi bi-person-badge fs-3"></i><br>
                        <small>person-badge</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-person-check" data-name="person-check">
                        <i class="bi bi-person-check fs-3"></i><br>
                        <small>person-check</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-person-plus" data-name="person-plus">
                        <i class="bi bi-person-plus fs-3"></i><br>
                        <small>person-plus</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-person-gear" data-name="person-gear">
                        <i class="bi bi-person-gear fs-3"></i><br>
                        <small>person-gear</small>
                    </div>
                    
                    <!-- Files & Documents -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-file" data-name="file">
                        <i class="bi bi-file fs-3"></i><br>
                        <small>file</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-file-text" data-name="file-text">
                        <i class="bi bi-file-text fs-3"></i><br>
                        <small>file-text</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-file-earmark" data-name="file-earmark">
                        <i class="bi bi-file-earmark fs-3"></i><br>
                        <small>file-earmark</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-file-earmark-text" data-name="file-earmark-text">
                        <i class="bi bi-file-earmark-text fs-3"></i><br>
                        <small>file-earmark-text</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-file-pdf" data-name="file-pdf">
                        <i class="bi bi-file-pdf fs-3"></i><br>
                        <small>file-pdf</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-file-word" data-name="file-word">
                        <i class="bi bi-file-word fs-3"></i><br>
                        <small>file-word</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-file-excel" data-name="file-excel">
                        <i class="bi bi-file-excel fs-3"></i><br>
                        <small>file-excel</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-files" data-name="files">
                        <i class="bi bi-files fs-3"></i><br>
                        <small>files</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-folder" data-name="folder">
                        <i class="bi bi-folder fs-3"></i><br>
                        <small>folder</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-folder-open" data-name="folder-open">
                        <i class="bi bi-folder-open fs-3"></i><br>
                        <small>folder-open</small>
                    </div>
                    
                    <!-- Building & Business -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-building" data-name="building">
                        <i class="bi bi-building fs-3"></i><br>
                        <small>building</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-shop" data-name="shop">
                        <i class="bi bi-shop fs-3"></i><br>
                        <small>shop</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-briefcase" data-name="briefcase">
                        <i class="bi bi-briefcase fs-3"></i><br>
                        <small>briefcase</small>
                    </div>
                    
                    <!-- Education -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-mortarboard" data-name="mortarboard">
                        <i class="bi bi-mortarboard fs-3"></i><br>
                        <small>mortarboard</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-book" data-name="book">
                        <i class="bi bi-book fs-3"></i><br>
                        <small>book</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-journal" data-name="journal">
                        <i class="bi bi-journal fs-3"></i><br>
                        <small>journal</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-journal-text" data-name="journal-text">
                        <i class="bi bi-journal-text fs-3"></i><br>
                        <small>journal-text</small>
                    </div>
                    
                    <!-- Clipboard & Tasks -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-clipboard" data-name="clipboard">
                        <i class="bi bi-clipboard fs-3"></i><br>
                        <small>clipboard</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-clipboard-check" data-name="clipboard-check">
                        <i class="bi bi-clipboard-check fs-3"></i><br>
                        <small>clipboard-check</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-clipboard-data" data-name="clipboard-data">
                        <i class="bi bi-clipboard-data fs-3"></i><br>
                        <small>clipboard-data</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-list-check" data-name="list-check">
                        <i class="bi bi-list-check fs-3"></i><br>
                        <small>list-check</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-check-circle" data-name="check-circle">
                        <i class="bi bi-check-circle fs-3"></i><br>
                        <small>check-circle</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-check-square" data-name="check-square">
                        <i class="bi bi-check-square fs-3"></i><br>
                        <small>check-square</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-check2" data-name="check2">
                        <i class="bi bi-check2 fs-3"></i><br>
                        <small>check2</small>
                    </div>
                    
                    <!-- Charts & Analytics -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-graph-up" data-name="graph-up">
                        <i class="bi bi-graph-up fs-3"></i><br>
                        <small>graph-up</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-bar-chart" data-name="bar-chart">
                        <i class="bi bi-bar-chart fs-3"></i><br>
                        <small>bar-chart</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-pie-chart" data-name="pie-chart">
                        <i class="bi bi-pie-chart fs-3"></i><br>
                        <small>pie-chart</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-diagram-3" data-name="diagram-3">
                        <i class="bi bi-diagram-3 fs-3"></i><br>
                        <small>diagram-3</small>
                    </div>
                    
                    <!-- Settings & Tools -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-gear" data-name="gear">
                        <i class="bi bi-gear fs-3"></i><br>
                        <small>gear</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-tools" data-name="tools">
                        <i class="bi bi-tools fs-3"></i><br>
                        <small>tools</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-wrench" data-name="wrench">
                        <i class="bi bi-wrench fs-3"></i><br>
                        <small>wrench</small>
                    </div>
                    
                    <!-- Awards & Badges -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-award" data-name="award">
                        <i class="bi bi-award fs-3"></i><br>
                        <small>award</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-trophy" data-name="trophy">
                        <i class="bi bi-trophy fs-3"></i><br>
                        <small>trophy</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-patch-check" data-name="patch-check">
                        <i class="bi bi-patch-check fs-3"></i><br>
                        <small>patch-check</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-shield" data-name="shield">
                        <i class="bi bi-shield fs-3"></i><br>
                        <small>shield</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-shield-check" data-name="shield-check">
                        <i class="bi bi-shield-check fs-3"></i><br>
                        <small>shield-check</small>
                    </div>
                    
                    <!-- Communication -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-envelope" data-name="envelope">
                        <i class="bi bi-envelope fs-3"></i><br>
                        <small>envelope</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-chat" data-name="chat">
                        <i class="bi bi-chat fs-3"></i><br>
                        <small>chat</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-telephone" data-name="telephone">
                        <i class="bi bi-telephone fs-3"></i><br>
                        <small>telephone</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-bell" data-name="bell">
                        <i class="bi bi-bell fs-3"></i><br>
                        <small>bell</small>
                    </div>
                    
                    <!-- Calendar & Time -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-calendar" data-name="calendar">
                        <i class="bi bi-calendar fs-3"></i><br>
                        <small>calendar</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-calendar-event" data-name="calendar-event">
                        <i class="bi bi-calendar-event fs-3"></i><br>
                        <small>calendar-event</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-clock" data-name="clock">
                        <i class="bi bi-clock fs-3"></i><br>
                        <small>clock</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-clock-history" data-name="clock-history">
                        <i class="bi bi-clock-history fs-3"></i><br>
                        <small>clock-history</small>
                    </div>
                    
                    <!-- Location -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-geo-alt" data-name="geo-alt">
                        <i class="bi bi-geo-alt fs-3"></i><br>
                        <small>geo-alt</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-map" data-name="map">
                        <i class="bi bi-map fs-3"></i><br>
                        <small>map</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-pin-map" data-name="pin-map">
                        <i class="bi bi-pin-map fs-3"></i><br>
                        <small>pin-map</small>
                    </div>
                    
                    <!-- Archive & Storage -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-archive" data-name="archive">
                        <i class="bi bi-archive fs-3"></i><br>
                        <small>archive</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-box" data-name="box">
                        <i class="bi bi-box fs-3"></i><br>
                        <small>box</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-inbox" data-name="inbox">
                        <i class="bi bi-inbox fs-3"></i><br>
                        <small>inbox</small>
                    </div>
                    
                    <!-- Layout -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-layout-text-sidebar" data-name="layout-text-sidebar">
                        <i class="bi bi-layout-text-sidebar fs-3"></i><br>
                        <small>sidebar</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-layout-three-columns" data-name="layout-three-columns">
                        <i class="bi bi-layout-three-columns fs-3"></i><br>
                        <small>columns</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-window" data-name="window">
                        <i class="bi bi-window fs-3"></i><br>
                        <small>window</small>
                    </div>
                    
                    <!-- Media -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-image" data-name="image">
                        <i class="bi bi-image fs-3"></i><br>
                        <small>image</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-camera" data-name="camera">
                        <i class="bi bi-camera fs-3"></i><br>
                        <small>camera</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-film" data-name="film">
                        <i class="bi bi-film fs-3"></i><br>
                        <small>film</small>
                    </div>
                    
                    <!-- Actions -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-plus-circle" data-name="plus-circle">
                        <i class="bi bi-plus-circle fs-3"></i><br>
                        <small>plus-circle</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-pencil" data-name="pencil">
                        <i class="bi bi-pencil fs-3"></i><br>
                        <small>pencil</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-trash" data-name="trash">
                        <i class="bi bi-trash fs-3"></i><br>
                        <small>trash</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-eye" data-name="eye">
                        <i class="bi bi-eye fs-3"></i><br>
                        <small>eye</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-download" data-name="download">
                        <i class="bi bi-download fs-3"></i><br>
                        <small>download</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-upload" data-name="upload">
                        <i class="bi bi-upload fs-3"></i><br>
                        <small>upload</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-search" data-name="search">
                        <i class="bi bi-search fs-3"></i><br>
                        <small>search</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-filter" data-name="filter">
                        <i class="bi bi-filter fs-3"></i><br>
                        <small>filter</small>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-arrow-right" data-name="arrow-right">
                        <i class="bi bi-arrow-right fs-3"></i><br>
                        <small>arrow-right</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-arrow-left" data-name="arrow-left">
                        <i class="bi bi-arrow-left fs-3"></i><br>
                        <small>arrow-left</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-chevron-right" data-name="chevron-right">
                        <i class="bi bi-chevron-right fs-3"></i><br>
                        <small>chevron-right</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-list" data-name="list">
                        <i class="bi bi-list fs-3"></i><br>
                        <small>list</small>
                    </div>
                    
                    <!-- Status -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-exclamation-triangle" data-name="exclamation-triangle">
                        <i class="bi bi-exclamation-triangle fs-3"></i><br>
                        <small>warning</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-info-circle" data-name="info-circle">
                        <i class="bi bi-info-circle fs-3"></i><br>
                        <small>info-circle</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-x-circle" data-name="x-circle">
                        <i class="bi bi-x-circle fs-3"></i><br>
                        <small>x-circle</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-question-circle" data-name="question-circle">
                        <i class="bi bi-question-circle fs-3"></i><br>
                        <small>question</small>
                    </div>
                    
                    <!-- Finance -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-cash" data-name="cash">
                        <i class="bi bi-cash fs-3"></i><br>
                        <small>cash</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-credit-card" data-name="credit-card">
                        <i class="bi bi-credit-card fs-3"></i><br>
                        <small>credit-card</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-wallet" data-name="wallet">
                        <i class="bi bi-wallet fs-3"></i><br>
                        <small>wallet</small>
                    </div>
                    
                    <!-- Security -->
                    <div class="col-2 text-center icon-item" data-icon="bi bi-lock" data-name="lock">
                        <i class="bi bi-lock fs-3"></i><br>
                        <small>lock</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-unlock" data-name="unlock">
                        <i class="bi bi-unlock fs-3"></i><br>
                        <small>unlock</small>
                    </div>
                    <div class="col-2 text-center icon-item" data-icon="bi bi-key" data-name="key">
                        <i class="bi bi-key fs-3"></i><br>
                        <small>key</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@push('styles')
<style>
    .icon-item {
        padding: 15px 5px;
        cursor: pointer;
        border-radius: 8px;
        transition: background-color 0.2s;
    }
    .icon-item:hover {
        background-color: #f8f9fa;
    }
    .icon-item.selected {
        background-color: #e7f3ff;
        border: 2px solid #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Icon preview
    function updateIconPreview() {
        var icon = $('#iconInput').val();
        if (icon) {
            $('#iconPreview').html(`<i class="${icon}"></i>`);
        } else {
            $('#iconPreview').html('');
        }
    }

    $('#iconInput').on('input', updateIconPreview);
    updateIconPreview();

    // Icon picker
    $('.icon-item').click(function() {
        $('.icon-item').removeClass('selected');
        $(this).addClass('selected');
        var icon = $(this).data('icon');
        $('#iconInput').val(icon);
        updateIconPreview();
        $('#iconModal').modal('hide');
    });

    // Auto-generate slug from name
    $('input[name="name"]').on('input', function() {
        var name = $(this).val();
        // Auto-suggest route name based on name
        if (name && !$('input[name="route_name"]').val()) {
            var slug = name.toLowerCase()
                          .replace(/[^a-z0-9\s]/g, '')
                          .replace(/\s+/g, '.');
            $('input[name="route_name"]').val('admin.' + slug + '.index');
        }
    });

    // Form validation
    // $('form').submit(function(e) {
    //     var routeName = $('input[name="route_name"]').val();
    //     if (!routeName.match(/^[a-z0-9\.]+$/)) {
    //         e.preventDefault();
    //         alert('Route name hanya boleh mengandung huruf kecil, angka, dan titik.');
    //         $('input[name="route_name"]').focus();
    //         return false;
    //     }
    // });
});
</script>

<script>
// Icon search functionality
document.getElementById('iconSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const iconItems = document.querySelectorAll('.icon-item');
    
    iconItems.forEach(item => {
        const iconName = item.getAttribute('data-name').toLowerCase();
        const iconClass = item.getAttribute('data-icon').toLowerCase();
        
        if (iconName.includes(searchTerm) || iconClass.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

// Icon selection
document.querySelectorAll('.icon-item').forEach(item => {
    item.addEventListener('click', function() {
        // Remove previous selection
        document.querySelectorAll('.icon-item').forEach(i => i.classList.remove('selected'));
        
        // Add selection to clicked item
        this.classList.add('selected');
        
        // Get icon class
        const iconClass = this.getAttribute('data-icon');
        
        // You can customize this based on your needs
        // For example, set it to a hidden input or trigger a callback
        if (window.onIconSelected) {
            window.onIconSelected(iconClass);
        }
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('iconModal'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>
@endpush