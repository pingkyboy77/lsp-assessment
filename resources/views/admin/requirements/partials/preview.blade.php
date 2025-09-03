{{-- resources/views/admin/requirements/partials/preview.blade.php --}}

<!-- Template Header Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="text-primary fw-bold mb-1">{{ $template->name }}</h5>
                @if($template->description)
                    <p class="text-muted mb-0">{{ $template->description }}</p>
                @else
                    <p class="text-muted fst-italic mb-0">Tidak ada deskripsi</p>
                @endif
            </div>
            <div class="text-end">
                <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }} mb-2">
                    {{ $template->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
                <br>
                <small class="text-muted">
                    {{ $template->items->count() }} item persyaratan
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Requirements List -->
@if($template->items->count() > 0)
    <div class="row g-3">
        @foreach($template->items->sortBy('sort_order') as $index => $item)
        <div class="col-12">
            <div class="card border-start border-{{ $item->is_required ? 'danger' : 'secondary' }} border-3">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-light text-dark me-2 fs-6">{{ $index + 1 }}</span>
                            <h6 class="card-title mb-0">{{ $item->document_name }}</h6>
                        </div>
                        <div class="d-flex gap-1">
                            @if($item->is_required)
                                <span class="badge bg-danger">Wajib</span>
                            @else
                                <span class="badge bg-secondary">Opsional</span>
                            @endif
                            <span class="badge bg-info">
                                @switch($item->type)
                                    @case('file_upload')
                                        <i class="bi bi-cloud-upload me-1"></i>Upload File
                                        @break
                                    @case('text_input')
                                        <i class="bi bi-input-cursor me-1"></i>Input Teks
                                        @break
                                    @case('number')
                                        <i class="bi bi-123 me-1"></i>Input Angka
                                        @break
                                    @case('select')
                                        <i class="bi bi-list me-1"></i>Dropdown
                                        @break
                                    @case('checkbox')
                                        <i class="bi bi-check-square me-1"></i>Checkbox
                                        @break
                                    @default
                                        {{ ucfirst($item->type) }}
                                @endswitch
                            </span>
                        </div>
                    </div>
                    
                    @if($item->description)
                        <p class="card-text text-muted small mb-2">{{ $item->description }}</p>
                    @endif

                    <!-- Type Specific Info -->
                    <div class="row">
                        @if($item->type === 'file_upload' && $item->validation_rules)
                            <div class="col-md-12">
                                <small class="text-info">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Validasi File:</strong>
                                    @if(isset($item->validation_rules['allowed_types']))
                                        Tipe: {{ implode(', ', $item->validation_rules['allowed_types']) }}
                                    @endif
                                    @if(isset($item->validation_rules['max_size']))
                                        @if(isset($item->validation_rules['allowed_types'])) | @endif
                                        Max: {{ number_format($item->validation_rules['max_size']/1024, 0) }}KB
                                    @endif
                                </small>
                            </div>
                        @elseif($item->type === 'select' && $item->options)
                            <div class="col-md-12">
                                <small class="text-success">
                                    <i class="bi bi-list me-1"></i>
                                    <strong>Pilihan:</strong>
                                </small>
                                <div class="mt-1">
                                    @foreach($item->options as $option)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $option }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($item->type === 'number' && $item->validation_rules)
                            <div class="col-md-12">
                                <small class="text-warning">
                                    <i class="bi bi-rulers me-1"></i>
                                    <strong>Range Nilai:</strong>
                                    @if(isset($item->validation_rules['min']))
                                        Min: {{ $item->validation_rules['min'] }}
                                    @endif
                                    @if(isset($item->validation_rules['max']))
                                        @if(isset($item->validation_rules['min'])) | @endif
                                        Max: {{ $item->validation_rules['max'] }}
                                    @endif
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="mt-4 pt-3 border-top">
        <div class="row text-center">
            <div class="col-4">
                <div class="text-primary">
                    <h4 class="mb-1">{{ $template->items->count() }}</h4>
                    <small class="text-muted">Total Item</small>
                </div>
            </div>
            <div class="col-4">
                <div class="text-danger">
                    <h4 class="mb-1">{{ $template->items->where('is_required', true)->count() }}</h4>
                    <small class="text-muted">Wajib</small>
                </div>
            </div>
            <div class="col-4">
                <div class="text-secondary">
                    <h4 class="mb-1">{{ $template->items->where('is_required', false)->count() }}</h4>
                    <small class="text-muted">Opsional</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Info -->
    @if($template->certificationSchemes && $template->certificationSchemes->count() > 0)
        <div class="mt-4 pt-3 border-top">
            <h6 class="text-success mb-2">
                <i class="bi bi-diagram-3 me-2"></i>Digunakan oleh Skema Sertifikasi:
            </h6>
            <div class="row">
                @foreach($template->certificationSchemes->take(4) as $scheme)
                <div class="col-md-6 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-award me-2 text-primary"></i>
                        <div>
                            <small class="fw-bold">{{ $scheme->code_1 ?? 'N/A' }}</small><br>
                            <small class="text-muted">{{ Str::limit($scheme->nama ?? 'N/A', 40) }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($template->certificationSchemes->count() > 4)
                <small class="text-muted">
                    <i class="bi bi-three-dots me-1"></i>
                    dan {{ $template->certificationSchemes->count() - 4 }} skema lainnya
                </small>
            @endif
        </div>
    @endif
@else
    <div class="text-center py-5">
        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
        <h5 class="text-muted">Belum Ada Item Persyaratan</h5>
        <p class="text-muted mb-0">Template ini belum memiliki item persyaratan yang dikonfigurasi.</p>
    </div>
@endif