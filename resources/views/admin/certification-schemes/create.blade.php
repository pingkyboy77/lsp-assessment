{{-- Form Create/Edit Certification Scheme with Multiple Templates --}}
@extends('layouts.admin')

@section('title', isset($certificationScheme) ? 'Edit Skema Sertifikasi' : 'Tambah Skema Sertifikasi')

@section('content')
    <div class="main-card">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-{{ isset($certificationScheme) ? 'pencil' : 'plus-circle-fill' }} me-2"></i>
                        {{ isset($certificationScheme) ? 'Edit' : 'Tambah' }} Skema Sertifikasi
                    </h5>
                    <p class="mb-0 text-muted">{{ isset($certificationScheme) ? 'Perbarui' : 'Buat' }} skema sertifikasi dengan persyaratan yang fleksibel</p>
                </div>
                <div>
                    <a href="{{ route('admin.certification-schemes.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body m-3">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Terdapat kesalahan pada input:</strong>
                    </div>
                    <ul class="mb-0 ps-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($certificationScheme) ? route('admin.certification-schemes.update', $certificationScheme->id) : route('admin.certification-schemes.store') }}" 
                  method="POST" class="needs-validation" novalidate>
                @csrf
                @if(isset($certificationScheme))
                    @method('PUT')
                @endif

                <!-- Basic Information -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-info-circle me-2"></i>Informasi Dasar
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Nama Skema -->
                                <div class="mb-3">
                                    <label for="nama" class="form-label fw-semibold">
                                        <i class="bi bi-award me-2"></i>Nama Skema
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nama') is-invalid @enderror" 
                                           id="nama" 
                                           name="nama" 
                                           value="{{ old('nama', $certificationScheme->nama ?? '') }}" 
                                           placeholder="Masukkan nama skema sertifikasi"
                                           required>
                                    <div class="invalid-feedback">
                                        @error('nama')
                                            {{ $message }}
                                        @else
                                            Nama skema wajib diisi.
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nama Skema (English) -->
                                <div class="mb-3">
                                    <label for="skema_ing" class="form-label fw-semibold">
                                        <i class="bi bi-translate me-2"></i>Nama Skema (English)
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('skema_ing') is-invalid @enderror" 
                                           id="skema_ing" 
                                           name="skema_ing" 
                                           value="{{ old('skema_ing', $certificationScheme->skema_ing ?? '') }}" 
                                           placeholder="Enter scheme name in English">
                                    <div class="invalid-feedback">
                                        @error('skema_ing')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <!-- Kode Skema -->
                                <div class="mb-3">
                                    <label for="code_1" class="form-label fw-semibold">
                                        <i class="bi bi-upc-scan me-2"></i>Kode Skema
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('code_1') is-invalid @enderror" 
                                           id="code_1" 
                                           name="code_1" 
                                           value="{{ old('code_1', $certificationScheme->code_1 ?? '') }}" 
                                           placeholder="Contoh: SKM-001"
                                           required>
                                    <div class="form-text">Kode unik untuk skema sertifikasi (maksimal 20 karakter)</div>
                                    <div class="invalid-feedback">
                                        @error('code_1')
                                            {{ $message }}
                                        @else
                                            Kode skema wajib diisi dan harus unik.
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-gear me-2"></i>Konfigurasi
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Bidang -->
                                <div class="mb-3">
                                    <label for="code_2" class="form-label fw-semibold">
                                        <i class="bi bi-diagram-3 me-2"></i>Bidang
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('code_2') is-invalid @enderror" 
                                            id="code_2" 
                                            name="code_2" 
                                            required>
                                        <option value="">Pilih Bidang</option>
                                        @foreach($fields as $field)
                                            <option value="{{ $field->code_2 }}" 
                                                    {{ old('code_2', $certificationScheme->code_2 ?? '') == $field->code_2 ? 'selected' : '' }}>
                                                {{ $field->bidang }} ({{ $field->code_2 }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('code_2')
                                            {{ $message }}
                                        @else
                                            Bidang wajib dipilih.
                                        @enderror
                                    </div>
                                </div>

                                <!-- Jenjang -->
                                <div class="mb-3">
                                    <label for="jenjang" class="form-label fw-semibold">
                                        <i class="bi bi-mortarboard me-2"></i>Jenjang
                                    </label>
                                    <select class="form-select @error('jenjang') is-invalid @enderror" 
                                            id="jenjang" 
                                            name="jenjang">
                                        <option value="">Pilih Jenjang</option>
                                        <option value="Madya" {{ old('jenjang', $certificationScheme->jenjang ?? '') == 'Madya' ? 'selected' : '' }}>Madya</option>
                                        <option value="Menengah" {{ old('jenjang', $certificationScheme->jenjang ?? '') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                        <option value="Utama" {{ old('jenjang', $certificationScheme->jenjang ?? '') == 'Utama' ? 'selected' : '' }}>Utama</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('jenjang')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <!-- Fee Tanda Tangan -->
                                <div class="mb-3">
                                    <label for="fee_tanda_tangan" class="form-label fw-semibold">
                                        <i class="bi bi-currency-dollar me-2"></i>Fee Tanda Tangan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control @error('fee_tanda_tangan') is-invalid @enderror" 
                                               id="fee_tanda_tangan" 
                                               name="fee_tanda_tangan" 
                                               value="{{ old('fee_tanda_tangan', $certificationScheme->fee_tanda_tangan ?? '') }}" 
                                               placeholder="0"
                                               min="0"
                                               step="1000">
                                        <div class="invalid-feedback">
                                            @error('fee_tanda_tangan')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-text">Kosongkan jika tidak ada biaya tanda tangan</div>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-toggle-on me-2"></i>Status
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', $certificationScheme->is_active ?? 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <span class="status-text">{{ old('is_active', $certificationScheme->is_active ?? 1) ? 'Aktif' : 'Tidak Aktif' }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirement Templates Selection -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bi bi-collection me-2"></i>Template Persyaratan
                                        <span class="badge bg-secondary ms-2" id="selectedCount">0 Template</span>
                                    </h6>
                                    <div>
                                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#templateModal">
                                            <i class="bi bi-plus me-1"></i>Pilih Template
                                        </button>
                                        <button type="button" class="btn btn-outline-dark btn-sm" id="clearTemplates">
                                            <i class="bi bi-x me-1"></i>Hapus Semua
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="selectedTemplatesContainer">
                                    @if(isset($certificationScheme) && $certificationScheme->requirementTemplates->count() > 0)
                                        @foreach($certificationScheme->requirementTemplates as $index => $template)
                                            <div class="selected-template mb-3 p-3 border rounded bg-light" data-template-id="{{ $template->id }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <i class="bi bi-grip-vertical me-2 text-muted"></i>
                                                            {{ $template->name }}
                                                            <span class="badge bg-{{ $template->requirement_type == 'all_required' ? 'danger' : ($template->requirement_type == 'choose_one' ? 'primary' : 'info') }} ms-2">
                                                                {{ $template->type_display }}
                                                            </span>
                                                        </h6>
                                                        <p class="mb-1 text-muted small">{{ $template->description ?: 'Tidak ada deskripsi' }}</p>
                                                        <p class="mb-0 small text-info">
                                                            <i class="bi bi-files me-1"></i>
                                                            {{ $template->requirement_description }}
                                                        </p>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-outline-info btn-sm preview-template" data-template-id="{{ $template->id }}">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-template">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="requirement_templates[]" value="{{ $template->id }}">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <div id="emptyTemplateState" class="text-center py-4 text-muted" {{ (isset($certificationScheme) && $certificationScheme->requirementTemplates->count() > 0) ? 'style=display:none' : '' }}>
                                    <i class="bi bi-collection display-4"></i>
                                    <p class="mt-2">Belum ada template persyaratan dipilih.</p>
                                    <p class="small text-muted">Template menentukan dokumen apa saja yang harus diupload oleh user.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.certification-schemes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary-custom text-light">
                                <i class="bi bi-check-circle me-2"></i>{{ isset($certificationScheme) ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Template Selection Modal -->
    <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="templateModalLabel">
                        <i class="bi bi-collection me-2"></i>Pilih Template Persyaratan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach($templates as $template)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 template-card" data-template-id="{{ $template->id }}" style="cursor: pointer;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-1">{{ $template->name }}</h6>
                                            <span class="badge bg-{{ $template->requirement_type == 'all_required' ? 'danger' : ($template->requirement_type == 'choose_one' ? 'primary' : 'info') }}">
                                                {{ $template->type_display }}
                                            </span>
                                        </div>
                                        <p class="card-text small text-muted">{{ $template->description ?: 'Tidak ada deskripsi' }}</p>
                                        <div class="d-flex justify-content-between align-items-end">
                                            <small class="text-info">
                                                <i class="bi bi-files me-1"></i>{{ $template->items_count }} dokumen
                                            </small>
                                            <small class="text-muted">
                                                {{ $template->requirement_description }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary" id="addSelectedTemplates">
                        <i class="bi bi-plus me-2"></i>Tambahkan Template
                    </button>
                </div>
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
                    <!-- Preview content will be loaded here -->
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

@push('scripts')
    <script>
        $(document).ready(function() {
            let selectedTemplates = new Set();

            // Initialize selected templates from edit mode
            @if(isset($certificationScheme))
                @foreach($certificationScheme->requirementTemplates as $template)
                    selectedTemplates.add({{ $template->id }});
                @endforeach
            @endif

            // Update selected count
            function updateSelectedCount() {
                const count = selectedTemplates.size;
                $('#selectedCount').text(count + ' Template' + (count > 1 ? 's' : ''));
                
                if (count > 0) {
                    $('#emptyTemplateState').hide();
                } else {
                    $('#emptyTemplateState').show();
                }
            }

            // Toggle status text
            $('#is_active').change(function() {
                const statusText = $(this).is(':checked') ? 'Aktif' : 'Tidak Aktif';
                $('.status-text').text(statusText);
            });

            // Template card selection in modal
            $(document).on('click', '.template-card', function() {
                $(this).toggleClass('border-primary bg-light');
                
                const templateId = $(this).data('template-id');
                if ($(this).hasClass('border-primary')) {
                    selectedTemplates.add(templateId);
                } else {
                    selectedTemplates.delete(templateId);
                }
            });

            // Add selected templates
            $('#addSelectedTemplates').click(function() {
                selectedTemplates.forEach(templateId => {
                    if (!$('.selected-template[data-template-id="' + templateId + '"]').length) {
                        addTemplateToSelection(templateId);
                    }
                });
                
                updateSelectedCount();
                $('#templateModal').modal('hide');
            });

            // Remove template
            $(document).on('click', '.remove-template', function() {
                const templateId = $(this).closest('.selected-template').data('template-id');
                selectedTemplates.delete(templateId);
                $(this).closest('.selected-template').remove();
                updateSelectedCount();
            });

            // Clear all templates
            $('#clearTemplates').click(function() {
                if (confirm('Hapus semua template persyaratan?')) {
                    selectedTemplates.clear();
                    $('#selectedTemplatesContainer').empty();
                    updateSelectedCount();
                }
            });

            // Preview template
            $(document).on('click', '.preview-template', function() {
                const templateId = $(this).data('template-id');
                loadTemplatePreview(templateId);
            });

            function addTemplateToSelection(templateId) {
                // Get template data from modal card
                const $card = $('.template-card[data-template-id="' + templateId + '"]');
                const templateName = $card.find('.card-title').text();
                const templateDesc = $card.find('.card-text').text();
                const templateBadge = $card.find('.badge').attr('class');
                const templateBadgeText = $card.find('.badge').text();
                const templateInfo = $card.find('.text-muted').last().text();

                const templateHtml = `
                    <div class="selected-template mb-3 p-3 border rounded bg-light" data-template-id="${templateId}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <i class="bi bi-grip-vertical me-2 text-muted"></i>
                                    ${templateName}
                                    <span class="${templateBadge} ms-2">${templateBadgeText}</span>
                                </h6>
                                <p class="mb-1 text-muted small">${templateDesc}</p>
                                <p class="mb-0 small text-info">
                                    <i class="bi bi-files me-1"></i>
                                    ${templateInfo}
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-info btn-sm preview-template" data-template-id="${templateId}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-template">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="requirement_templates[]" value="${templateId}">
                    </div>
                `;
                
                $('#selectedTemplatesContainer').append(templateHtml);
            }

            function loadTemplatePreview(templateId) {
                $.ajax({
                    url: '{{ route("admin.requirements.show", ":id") }}'.replace(':id', templateId),
                    type: 'GET',
                    success: function(response) {
                        $('#previewContent').html(response);
                        $('#previewModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error loading template preview:', xhr);
                        alert('Gagal memuat preview template');
                    }
                });
            }

            // Reset modal selection when closed
            $('#templateModal').on('hidden.bs.modal', function() {
                $('.template-card').removeClass('border-primary bg-light');
                selectedTemplates.forEach(templateId => {
                    $('.template-card[data-template-id="' + templateId + '"]').addClass('border-primary bg-light');
                });
            });

            // Initialize count
            updateSelectedCount();

            // Form validation
            $('form').on('submit', function(e) {
                if (selectedTemplates.size === 0) {
                    e.preventDefault();
                    alert('Minimal harus memilih 1 template persyaratan!');
                    return false;
                }
            });
        });
    </script>
@endpush