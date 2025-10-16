{{-- resources/views/admin/unit-kompetensi/partials/portfolio-document-item.blade.php --}}
<div class="document-item {{ $document->is_required ? 'required' : 'optional' }} {{ !$document->is_active ? 'inactive' : '' }}" 
     data-document-id="{{ $document->id }}">
    <div class="p-3">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-start flex-grow-1">
                <i class="bi bi-grip-vertical sortable-handle me-3 mt-1"></i>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-light text-dark me-2">{{ $document->sort_order }}</span>
                        <h6 class="mb-0 me-2">{{ $document->document_name }}</h6>
                        <span class="badge requirement-badge bg-{{ $document->is_required ? 'danger' : 'warning' }} me-2">
                            {{ $document->requirement_text }}
                        </span>
                        <span class="badge bg-{{ $document->status_color }}">
                            <i class="bi bi-{{ $document->is_active ? 'check-circle' : 'x-circle' }}"></i>
                            {{ $document->status_text }}
                        </span>
                    </div>
                    
                    @if($document->document_description)
                        <p class="text-muted mb-2 small">{{ $document->document_description }}</p>
                    @endif
                    
                    <div class="d-flex align-items-center text-muted small">
                        <i class="bi bi-calendar-plus me-1"></i>
                        <span class="me-3">{{ $document->created_at->format('d M Y') }}</span>
                        @if($document->updated_at != $document->created_at)
                            <i class="bi bi-pencil me-1"></i>
                            <span>Diperbarui {{ $document->updated_at->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button class="dropdown-item" 
                                onclick="editDocument({{ $document->id }}, '{{ addslashes($document->document_name) }}', '{{ addslashes($document->document_description ?? '') }}', {{ $document->is_required ? 'true' : 'false' }}, {{ $document->is_active ? 'true' : 'false' }})">
                            <i class="bi bi-pencil me-2"></i>Edit Dokumen
                        </button>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button class="dropdown-item" onclick="toggleDocumentStatus({{ $document->id }})">
                            <i class="bi bi-{{ $document->is_active ? 'eye-slash' : 'eye' }} me-2"></i>
                            {{ $document->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" onclick="toggleDocumentRequirement({{ $document->id }})">
                            <i class="bi bi-{{ $document->is_required ? 'question-circle' : 'exclamation-circle' }} me-2"></i>
                            Ubah ke {{ $document->is_required ? 'Opsional' : 'Wajib' }}
                        </button>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button class="dropdown-item text-danger" 
                                onclick="deleteDocument({{ $document->id }}, '{{ addslashes($document->document_name) }}')">
                            <i class="bi bi-trash me-2"></i>Hapus Dokumen
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Quick Info -->
        <div class="row mt-2">
            <div class="col-sm-6">
                <small class="text-muted">
                    <i class="bi bi-{{ $document->requirement_icon }} me-1"></i>
                    Dokumen {{ $document->requirement_text }}
                </small>
            </div>
            <div class="col-sm-6 text-end">
                <small class="text-muted">
                    ID: {{ $document->id }}
                </small>
            </div>
        </div>
    </div>
</div>