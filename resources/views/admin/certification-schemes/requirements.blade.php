@extends('layouts.admin')

@section('title', 'Kelola Persyaratan - ' . $certificationScheme->nama)

@section('content')
    <div class="main-card">
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
                        <i class="bi bi-list-check me-2"></i>Kelola Persyaratan
                    </h5>
                    <p class="mb-0 text-muted">
                        <strong>{{ $certificationScheme->code_1 }} - {{ $certificationScheme->nama }}</strong>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body m-3">
            <!-- Scheme Information -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-info-circle me-2"></i>Informasi Skema
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Kode Skema:</strong>
                                        <span class="badge bg-primary">{{ $certificationScheme->code_1 }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Bidang:</strong>
                                        {{ $certificationScheme->field ? $certificationScheme->field->bidang : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Jenjang:</strong>
                                        @if ($certificationScheme->jenjang)
                                            <span
                                                class="badge bg-{{ $certificationScheme->jenjang_color }}">{{ $certificationScheme->jenjang }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                    <p class="mb-2">
                                        <strong>Status:</strong>
                                        <span
                                            class="badge bg-{{ $certificationScheme->is_active ? 'success' : 'secondary' }}">
                                            {{ $certificationScheme->status_text }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-graph-up me-2"></i>Statistik Persyaratan
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="text-primary mb-1">{{ $certificationScheme->requirement_templates_count }}
                                    </h4>
                                    <small class="text-muted">Template</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info mb-1">{{ $certificationScheme->total_required_documents }}</h4>
                                    <small class="text-muted">Total Dokumen</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Single Template (Backward Compatibility) -->
            @if ($certificationScheme->requirementTemplate)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-secondary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bi bi-file-text me-2"></i>Template Utama (Legacy)
                                    </h6>
                                    <span class="badge bg-light text-dark">Backward Compatibility</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2">
                                            {{ $certificationScheme->requirementTemplate->name }}
                                            <span
                                                class="badge bg-{{ $certificationScheme->requirementTemplate->requirement_type == 'all_required' ? 'danger' : ($certificationScheme->requirementTemplate->requirement_type == 'choose_one' ? 'primary' : 'info') }} ms-2">
                                                {{ $certificationScheme->requirementTemplate->type_display }}
                                            </span>
                                        </h6>
                                        <p class="text-muted mb-2">
                                            {{ $certificationScheme->requirementTemplate->description ?: 'Tidak ada deskripsi' }}
                                        </p>
                                        <p class="text-info small mb-0">
                                            <i class="bi bi-info-circle me-1"></i>
                                            {{ $certificationScheme->requirementTemplate->requirement_description }}
                                        </p>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-info btn-sm preview-single-template"
                                            data-template-id="{{ $certificationScheme->requirementTemplate->id }}">
                                            <i class="bi bi-eye me-1"></i>Preview
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Multiple Templates -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-collection me-2"></i>Template Persyaratan
                                    <span
                                        class="badge bg-light text-dark ms-2">{{ $certificationScheme->requirementTemplates->count() }}
                                        Template</span>
                                </h6>
                                <div class="btn-group">
                                    @if ($certificationScheme->hasRequirements())
                                        <button type="button" class="btn btn-outline-light btn-sm" id="resetTemplatesBtn">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Reset Semua
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#manageTemplatesModal">
                                        <i class="bi bi-gear me-1"></i>Kelola Template
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Konfirmasi Reset -->
                        <div class="modal fade" id="resetConfirmModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title">
                                            <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Reset
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <strong>Peringatan!</strong> Tindakan ini akan menghapus SEMUA template
                                            persyaratan yang sudah dipilih untuk skema sertifikasi ini.
                                        </div>
                                        <p>Apakah Anda yakin ingin mereset semua template persyaratan?</p>
                                        <ul class="text-muted small">
                                            <li>Template utama (legacy) akan direset</li>
                                            <li>Semua template multiple akan dihapus</li>
                                            <li>Data registrasi yang sudah ada tidak akan terpengaruh</li>
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="bi bi-x me-1"></i>Batal
                                        </button>
                                        <form method="POST"
                                            action="{{ route('admin.certification-schemes.reset-requirements', $certificationScheme) }}"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-warning">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Ya, Reset Semua
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($certificationScheme->requirementTemplates->count() > 0)
                                <div id="templatesContainer">
                                    @foreach ($certificationScheme->requirementTemplates as $index => $template)
                                        <div class="template-item mb-3 p-3 border rounded bg-light">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                                        <h6 class="mb-0 me-2">{{ $template->name }}</h6>
                                                        <span
                                                            class="badge bg-{{ $template->requirement_type == 'all_required' ? 'danger' : ($template->requirement_type == 'choose_one' ? 'primary' : 'info') }}">
                                                            {{ $template->type_display }}
                                                        </span>
                                                        @if ($template->pivot->is_active)
                                                            <span class="badge bg-success ms-2">Aktif</span>
                                                        @else
                                                            <span class="badge bg-secondary ms-2">Nonaktif</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-muted mb-2 small">
                                                        {{ $template->description ?: 'Tidak ada deskripsi' }}</p>
                                                    <div class="d-flex align-items-center text-info small">
                                                        <i class="bi bi-files me-1"></i>
                                                        <span
                                                            class="me-3">{{ $template->requirement_description }}</span>
                                                        <i class="bi bi-clock me-1"></i>
                                                        <span>Urutan: {{ $template->pivot->sort_order }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column gap-2">
                                                    <button type="button"
                                                        class="btn btn-outline-info btn-sm preview-template"
                                                        data-template-id="{{ $template->id }}">
                                                        <i class="bi bi-eye me-1"></i>Detail
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Template Requirements Preview -->
                                            @if ($template->activeItems->count() > 0)
                                                <div class="mt-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <small class="fw-semibold text-secondary">Dokumen yang
                                                            Diperlukan:</small>
                                                        <button type="button"
                                                            class="btn btn-outline-secondary btn-xs toggle-requirements"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#requirements-{{ $template->id }}">
                                                            <i class="bi bi-chevron-down me-1"></i>Lihat Dokumen
                                                        </button>
                                                    </div>
                                                    <div class="collapse" id="requirements-{{ $template->id }}">
                                                        <div class="bg-white p-3 rounded border">
                                                            <div class="row">
                                                                @foreach ($template->activeItems->chunk(2) as $chunk)
                                                                    @foreach ($chunk as $item)
                                                                        <div class="col-md-6 mb-2">
                                                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                                                <i
                                                                                    class="bi bi-file-earmark-text me-2 text-primary"></i>
                                                                                <div class="d-flex">
                                                                                    
                                                                                    @if ($item->description)
                                                                                        <small
                                                                                            class="text-muted d-flex">{{ $item->description }}</small>
                                                                                    @endif
                                                                                </div>
                                                                                <div
                                                                                        class="d-flex justify-content-between align-items-center">
                                                                                        <span
                                                                                            class="small fw-semibold">{{ $item->name }}</span>
                                                                                        @if ($item->is_required)
                                                                                            <span
                                                                                                class="badge bg-danger badge-sm">Wajib</span>
                                                                                        @else
                                                                                            <span
                                                                                                class="badge bg-secondary badge-sm">Opsional</span>
                                                                                        @endif
                                                                                    </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-collection display-4"></i>
                                    <p class="mt-3">Belum ada template persyaratan yang dipilih.</p>
                                    <p class="small">Klik "Kelola Template" untuk menambahkan template persyaratan.</p>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#manageTemplatesModal">
                                        <i class="bi bi-plus me-1"></i>Tambah Template
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            @if ($certificationScheme->hasRequirements())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-check-circle me-2"></i>Ringkasan Persyaratan
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h5 class="text-primary">
                                            {{ $certificationScheme->getAllActiveTemplatesAttribute()->count() }}</h5>
                                        <small class="text-muted">Total Template Aktif</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="text-info">{{ $certificationScheme->total_required_documents }}</h5>
                                        <small class="text-muted">Minimum Dokumen Dibutuhkan</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="text-warning">
                                            {{ $certificationScheme->getAllActiveTemplatesAttribute()->sum(function ($t) {return $t->activeItems->count();}) }}
                                        </h5>
                                        <small class="text-muted">Total Opsi Dokumen</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="text-success">
                                            {{ $certificationScheme->getAllActiveTemplatesAttribute()->where('requirement_type', 'all_required')->count() }}
                                        </h5>
                                        <small class="text-muted">Template Wajib Lengkap</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Manage Templates Modal -->
    <div class="modal fade" id="manageTemplatesModal" tabindex="-1" aria-labelledby="manageTemplatesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST"
                    action="{{ route('admin.certification-schemes.update-requirements', $certificationScheme) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="manageTemplatesModalLabel">
                            <i class="bi bi-gear me-2"></i>Kelola Template Persyaratan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Single Template Selection (Backward Compatibility) -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-file-text me-2"></i>Template Utama (Legacy - Backward Compatibility)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="requirement_template_id" class="form-label">Template Utama</label>
                                    <select class="form-select" id="requirement_template_id"
                                        name="requirement_template_id">
                                        <option value="">Tidak Ada Template Utama</option>
                                        @foreach ($templates as $template)
                                            <option value="{{ $template->id }}"
                                                {{ $certificationScheme->requirement_template_id == $template->id ? 'selected' : '' }}>
                                                {{ $template->name }} ({{ $template->type_display }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Template ini untuk backward compatibility. Gunakan "Template
                                        Multiple" di bawah untuk fitur terbaru.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Multiple Templates Selection -->
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-collection me-2"></i>Template Persyaratan (Multiple)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($templates as $template)
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="card h-100 template-selection-card {{ $certificationScheme->requirementTemplates->contains($template->id) ? 'border-primary bg-light' : '' }}"
                                                data-template-id="{{ $template->id }}">
                                                <div class="card-body">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="requirement_templates[]" value="{{ $template->id }}"
                                                            id="template_{{ $template->id }}"
                                                            {{ $certificationScheme->requirementTemplates->contains($template->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-semibold"
                                                            for="template_{{ $template->id }}">
                                                            {{ $template->name }}
                                                        </label>
                                                    </div>
                                                    <span
                                                        class="badge bg-{{ $template->requirement_type == 'all_required' ? 'danger' : ($template->requirement_type == 'choose_one' ? 'primary' : 'info') }} mb-2">
                                                        {{ $template->type_display }}
                                                    </span>
                                                    <p class="card-text small text-muted">
                                                        {{ $template->description ?: 'Tidak ada deskripsi' }}</p>
                                                    <div class="d-flex justify-content-between align-items-end">
                                                        <small class="text-info">
                                                            <i
                                                                class="bi bi-files me-1"></i>{{ $template->activeItems->count() }}
                                                            dokumen
                                                        </small>
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-sm preview-template-modal"
                                                            data-template-id="{{ $template->id }}">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Template Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="bi bi-eye me-2"></i>Preview Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat preview template...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .template-selection-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .template-selection-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .template-selection-card.border-primary {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .toggle-requirements .bi-chevron-down {
            transition: transform 0.3s ease;
        }

        .toggle-requirements[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }

        .badge-xs {
            font-size: 0.65em;
            padding: 0.25em 0.4em;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle template card selection in manage modal
            $(document).on('click', '.template-selection-card', function(e) {
                if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('.btn') || $(e.target)
                    .closest('.btn').length > 0) {
                    return;
                }

                const checkbox = $(this).find('input[type="checkbox"]');
                const isChecked = checkbox.prop('checked');

                checkbox.prop('checked', !isChecked);
                $(this).toggleClass('border-primary bg-light', !isChecked);
            });

            // Handle checkbox changes
            $(document).on('change', '.template-selection-card input[type="checkbox"]', function() {
                const isChecked = $(this).prop('checked');
                $(this).closest('.template-selection-card').toggleClass('border-primary bg-light',
                    isChecked);
            });

            // Preview template functionality
            $(document).on('click', '.preview-template, .preview-template-modal, .preview-single-template',
                function(e) {
                    e.stopPropagation();
                    const templateId = $(this).data('template-id');
                    loadTemplatePreview(templateId);
                });

            // Toggle requirements collapse
            $(document).on('click', '.toggle-requirements', function() {
                const icon = $(this).find('.bi-chevron-down');
                setTimeout(() => {
                    const isExpanded = $(this).attr('aria-expanded') === 'true';
                    icon.css('transform', isExpanded ? 'rotate(180deg)' : 'rotate(0deg)');
                }, 100);
            });

            function loadTemplatePreview(templateId) {
                $('#previewContent').html(`
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat preview template...</p>
                    </div>
                `);

                $('#previewModal').modal('show');

                $.ajax({
                    url: '{{ route('admin.certification-schemes.get-template-details', ':id') }}'.replace(
                        ':id', templateId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const template = response.template;
                            let previewHtml = `
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">${template.name}</h6>
                                            <span class="badge bg-${template.requirement_type == 'all_required' ? 'danger' : (template.requirement_type == 'choose_one' ? 'primary' : 'info')}">
                                                ${template.type_display}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        ${template.description ? `<p class="text-muted">${template.description}</p>` : ''}
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Requirement Type:</strong> ${template.requirement_description}
                                        </div>
                                        
                                        <h6 class="mt-3 mb-3">
                                            <i class="bi bi-files me-2"></i>Dokumen yang Diperlukan (${template.items_count}):
                                        </h6>
                            `;

                            if (template.items && template.items.length > 0) {
                                previewHtml += '<div class="row">';
                                template.items.forEach((item, index) => {
                                    const colClass = template.items.length > 4 ? 'col-md-6' :
                                        'col-12';
                                    previewHtml += `
                                        <div class="${colClass} mb-3">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                                            ${item.document_name}
                                                        </h6>
                                                        ${item.is_required ? '<span class="badge bg-danger">Wajib</span>' : '<span class="badge bg-secondary">Opsional</span>'}
                                                    </div>
                                                    ${item.description ? `<p class="mb-0 small text-muted">${item.description}</p>` : '<p class="mb-0 small text-muted">Tidak ada deskripsi</p>'}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                                previewHtml += '</div>';
                            } else {
                                previewHtml += `
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        Template ini belum memiliki item dokumen.
                                    </div>
                                `;
                            }

                            // Add usage instructions based on requirement type
                            previewHtml += `
                                <div class="mt-4">
                                    <h6 class="mb-2">
                                        <i class="bi bi-lightbulb me-2"></i>Cara Kerja Template:
                                    </h6>
                            `;

                            switch (template.requirement_type) {
                                case 'all_required':
                                    previewHtml += `
                                        <div class="alert alert-danger">
                                            <strong>Semua Dokumen Wajib:</strong> User harus mengupload SEMUA ${template.items_count} dokumen yang tercantum di atas.
                                        </div>
                                    `;
                                    break;
                                case 'choose_one':
                                    previewHtml += `
                                        <div class="alert alert-primary">
                                            <strong>Pilih Salah Satu:</strong> User hanya perlu mengupload 1 dokumen dari ${template.items_count} pilihan yang tersedia.
                                        </div>
                                    `;
                                    break;
                                case 'choose_min':
                                    previewHtml += `
                                        <div class="alert alert-info">
                                            <strong>Pilih Minimal:</strong> User harus mengupload minimal beberapa dokumen dari ${template.items_count} pilihan yang tersedia.
                                        </div>
                                    `;
                                    break;
                            }

                            previewHtml += `
                                    </div>
                                </div>
                            </div>
                            `;

                            $('#previewContent').html(previewHtml);
                        } else {
                            $('#previewContent').html(`
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    ${response.message || 'Gagal memuat preview template.'}
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memuat preview template.';
                        if (xhr.status === 404) {
                            errorMessage = 'Template tidak ditemukan.';
                        }

                        $('#previewContent').html(`
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                ${errorMessage}
                            </div>
                        `);
                    }
                });
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-dismiss success alerts
            setTimeout(function() {
                $('.alert-success-custom').fadeOut();
            }, 5000);

            // Form validation before submit
            $('#manageTemplatesModal form').on('submit', function(e) {
                const hasBackwardTemplate = $('#requirement_template_id').val();
                const hasMultipleTemplates = $('input[name="requirement_templates[]"]:checked').length > 0;

                if (!hasBackwardTemplate && !hasMultipleTemplates) {
                    e.preventDefault();
                    alert('Silakan pilih minimal 1 template persyaratan!');
                    return false;
                }

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...');

                // Re-enable button after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.prop('disabled', false).html(originalText);
                }, 10000);

                return true;
            });

            // Template search functionality
            let searchTimeout;
            $('#manageTemplatesModal').on('shown.bs.modal', function() {
                if (!$('#templateSearch').length) {
                    const searchHtml = `
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="templateSearch" placeholder="Cari template berdasarkan nama atau deskripsi...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="templateTypeFilter">
                                    <option value="">Semua Tipe</option>
                                    <option value="all_required">Semua Wajib</option>
                                    <option value="choose_one">Pilih Satu</option>
                                    <option value="choose_min">Pilih Minimal</option>
                                </select>
                            </div>
                        </div>
                    `;
                    $('#manageTemplatesModal .card-body .row').before(searchHtml);

                    // Bind search events
                    $('#templateSearch').on('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(filterTemplates, 300);
                    });

                    $('#templateTypeFilter').on('change', filterTemplates);
                }
            });

            function filterTemplates() {
                const searchTerm = $('#templateSearch').val().toLowerCase();
                const typeFilter = $('#templateTypeFilter').val();
                let visibleCount = 0;

                $('.template-selection-card').each(function() {
                    const $card = $(this);
                    const $container = $card.closest('.col-lg-4');
                    const title = $card.find('.form-check-label').text().toLowerCase();
                    const description = $card.find('.card-text').text().toLowerCase();
                    const badgeText = $card.find('.badge').text().toLowerCase();

                    let showCard = true;

                    // Text search
                    if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm)) {
                        showCard = false;
                    }

                    // Type filter
                    if (typeFilter) {
                        const typeMatch = typeFilter === 'all_required' && badgeText.includes('semua') ||
                            typeFilter === 'choose_one' && badgeText.includes('pilih') && badgeText
                            .includes('satu') ||
                            typeFilter === 'choose_min' && badgeText.includes('minimal');
                        if (!typeMatch) {
                            showCard = false;
                        }
                    }

                    $container.toggle(showCard);
                    if (showCard) visibleCount++;
                });

                // Show/hide no results message
                if (visibleCount === 0 && !$('#noResultsMessage').length) {
                    $('#manageTemplatesModal .card-body .row').after(`
                        <div id="noResultsMessage" class="text-center py-4 text-muted">
                            <i class="bi bi-search display-4"></i>
                            <p class="mt-2">Tidak ditemukan template yang sesuai dengan filter.</p>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="$('#templateSearch').val(''); $('#templateTypeFilter').val(''); filterTemplates();">
                                <i class="bi bi-x me-1"></i>Reset Filter
                            </button>
                        </div>
                    `);
                } else if (visibleCount > 0) {
                    $('#noResultsMessage').remove();
                }
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Handle reset button click
            $('#resetTemplatesBtn').on('click', function() {
                $('#resetConfirmModal').modal('show');
            });

            // Handle reset form submission
            $('#resetConfirmModal form').on('submit', function(e) {
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true)
                    .html('<i class="bi bi-hourglass-split me-1"></i>Mereset...');

                // Re-enable after timeout as fallback
                setTimeout(() => {
                    submitBtn.prop('disabled', false).html(originalText);
                }, 10000);
            });
        });
    </script>
@endpush
