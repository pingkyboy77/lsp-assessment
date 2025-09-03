
@extends('layouts.admin')

@section('title', 'Detail Template Persyaratan')

@section('content')
    <div class="main-card">
        <!-- Card Header -->
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-eye me-2"></i>Detail Template Persyaratan
                    </h5>
                    <p class="mb-0 text-muted">{{ $template->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.requirements.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Template Info -->
            <div class="card border-0 bg-light mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-bold">{{ $template->name }}</h6>
                            @if($template->description)
                                <p class="text-muted mb-0">{{ $template->description }}</p>
                            @else
                                <p class="text-muted fst-italic mb-0">Tidak ada deskripsi</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex flex-column align-items-end">
                                <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }} mb-2">
                                    {{ $template->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                                <small class="text-muted">
                                    {{ $template->items->count() }} item persyaratan<br>
                                    Digunakan {{ $template->certificationSchemes->count() }} skema
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements List -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-list-check me-2"></i>Daftar Persyaratan
                    </h6>
                </div>
                <div class="card-body">
                    @if($template->items->count() > 0)
                        <div class="row">
                            @foreach($template->items->sortBy('sort_order') as $index => $item)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-{{ $item->is_required ? 'danger' : 'secondary' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">
                                                <span class="badge bg-light text-dark me-2">{{ $index + 1 }}</span>
                                                {{ $item->document_name }}
                                            </h6>
                                            @if($item->is_required)
                                                <span class="badge bg-danger">Wajib</span>
                                            @else
                                                <span class="badge bg-secondary">Opsional</span>
                                            @endif
                                        </div>
                                        
                                        @if($item->description)
                                            <p class="card-text text-muted small mb-3">{{ $item->description }}</p>
                                        @endif

                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">
                                                    <strong>Tipe Input:</strong><br>
                                                    <i class="bi bi-gear me-1"></i>
                                                    @switch($item->type)
                                                        @case('file_upload')
                                                            Upload File
                                                            @break
                                                        @case('text_input')
                                                            Input Teks
                                                            @break
                                                        @case('number')
                                                            Input Angka
                                                            @break
                                                        @case('select')
                                                            Dropdown
                                                            @break
                                                        @case('checkbox')
                                                            Checkbox
                                                            @break
                                                        @default
                                                            {{ ucfirst($item->type) }}
                                                    @endswitch
                                                </small>
                                            </div>
                                            <div class="col-6">
                                                @if($item->type === 'file_upload' && $item->validation_rules)
                                                    <small class="text-info">
                                                        <strong>Validasi:</strong><br>
                                                        @if(isset($item->validation_rules['allowed_types']))
                                                            <i class="bi bi-file-earmark me-1"></i>
                                                            {{ implode(', ', $item->validation_rules['allowed_types']) }}<br>
                                                        @endif
                                                        @if(isset($item->validation_rules['max_size']))
                                                            <i class="bi bi-hdd me-1"></i>
                                                            Max: {{ number_format($item->validation_rules['max_size']/1024, 0) }}KB
                                                        @endif
                                                    </small>
                                                @elseif($item->type === 'select' && $item->options)
                                                    <small class="text-success">
                                                        <strong>Pilihan:</strong><br>
                                                        @foreach($item->options as $option)
                                                            <span class="badge bg-light text-dark me-1 mb-1">{{ $option }}</span>
                                                        @endforeach
                                                    </small>
                                                @elseif($item->type === 'number' && $item->validation_rules)
                                                    <small class="text-warning">
                                                        <strong>Range:</strong><br>
                                                        @if(isset($item->validation_rules['min']))
                                                            Min: {{ $item->validation_rules['min'] }}<br>
                                                        @endif
                                                        @if(isset($item->validation_rules['max']))
                                                            Max: {{ $item->validation_rules['max'] }}
                                                        @endif
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Total: {{ $template->items->count() }} item persyaratan
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <small class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        {{ $template->items->where('is_required', true)->count() }} wajib
                                    </small>
                                    <span class="mx-2">|</span>
                                    <small class="text-secondary">
                                        <i class="bi bi-dash-circle me-1"></i>
                                        {{ $template->items->where('is_required', false)->count() }} opsional
                                    </small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <h5 class="mt-3 text-muted">Belum Ada Item Persyaratan</h5>
                            <p class="text-muted">Template ini belum memiliki item persyaratan</p>
                            {{-- <a href="{{ route('admin.requirements.edit', $template) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Item Persyaratan
                            </a> --}}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Usage Information -->
            @if($template->certificationSchemes->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-diagram-3 me-2"></i>Digunakan oleh Skema Sertifikasi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($template->certificationSchemes as $scheme)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-award me-2 text-primary"></i>
                                <div>
                                    <strong>{{ $scheme->code_1 }}</strong> - {{ $scheme->nama }}<br>
                                    <small class="text-muted">{{ $scheme->field->bidang ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

{{-- Untuk AJAX Preview (partial view) --}}
@if(request()->ajax())
    @if($template->items->count() > 0)
        <div class="row">
            @foreach($template->items->sortBy('sort_order') as $index => $item)
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-{{ $item->is_required ? 'danger' : 'secondary' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">
                                <span class="badge bg-light text-dark me-2">{{ $index + 1 }}</span>
                                {{ $item->document_name }}
                            </h6>
                            @if($item->is_required)
                                <span class="badge bg-danger">Wajib</span>
                            @else
                                <span class="badge bg-secondary">Opsional</span>
                            @endif
                        </div>
                        
                        @if($item->description)
                            <p class="card-text text-muted small mb-2">{{ $item->description }}</p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-gear me-1"></i>
                                @switch($item->type)
                                    @case('file_upload')
                                        Upload File
                                        @break
                                    @case('text_input')
                                        Input Teks
                                        @break
                                    @case('number')
                                        Input Angka
                                        @break
                                    @case('select')
                                        Dropdown
                                        @break
                                    @case('checkbox')
                                        Checkbox
                                        @break
                                    @default
                                        {{ ucfirst($item->type) }}
                                @endswitch
                            </small>
                            
                            @if($item->type === 'file_upload' && $item->validation_rules)
                                <small class="text-info">
                                    @if(isset($item->validation_rules['allowed_types']))
                                        {{ implode(', ', $item->validation_rules['allowed_types']) }}
                                    @endif
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-3 text-muted text-center">
            <small>
                Total: {{ $template->items->count() }} item 
                ({{ $template->items->where('is_required', true)->count() }} wajib, 
                {{ $template->items->where('is_required', false)->count() }} opsional)
            </small>
        </div>
    @else
        <div class="text-center py-3">
            <i class="bi bi-inbox display-6 text-muted"></i>
            <p class="text-muted mt-2 mb-0">Template ini belum memiliki item persyaratan</p>
        </div>
    @endif
@endif