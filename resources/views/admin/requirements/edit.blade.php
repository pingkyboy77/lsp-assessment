@extends('layouts.admin')

@section('title', 'Edit Template Persyaratan')

@section('content')
    <div class="main-card">
        <!-- Card Header -->
        <div class="card-header-custom">
            @if (session('success'))
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

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

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Edit Template Persyaratan
                    </h5>
                    <p class="mb-0 text-muted">Perbarui template persyaratan untuk skema sertifikasi</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.requirements.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body m-3">
            <!-- FIXED: Form action menggunakan ID langsung -->
            <form action="{{ route('admin.requirements.update', $template->id) }}" method="POST" id="requirementForm" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-info-circle me-2"></i>Informasi Template
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Nama Template -->
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="bi bi-file-earmark-text me-2"></i>Nama Template
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name"
                                           value="{{ old('name', $template->name) }}" 
                                           placeholder="Contoh: Template Persyaratan Konstruksi"
                                           required>
                                    <div class="form-text">Berikan nama yang menggambarkan grup dokumen ini</div>
                                    <div class="invalid-feedback">
                                        @error('name')
                                            {{ $message }}
                                        @else
                                            Nama template wajib diisi.
                                        @enderror
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="mb-3">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="bi bi-card-text me-2"></i>Deskripsi
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3"
                                              placeholder="Deskripsi template persyaratan...">{{ old('description', $template->description) }}</textarea>
                                    <div class="form-text">Opsional - jelaskan untuk apa template ini digunakan</div>
                                    <div class="invalid-feedback">
                                        @error('description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-gear me-2"></i>Tipe Persyaratan
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Requirement Type -->
                                <div class="mb-3">
                                    <label for="requirement_type" class="form-label fw-semibold">
                                        <i class="bi bi-list-check me-2"></i>Tipe Persyaratan
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('requirement_type') is-invalid @enderror" 
                                            id="requirement_type" 
                                            name="requirement_type"
                                            required>
                                        <option value="">Pilih Tipe</option>
                                        @foreach(\App\Models\RequirementTemplate::REQUIREMENT_TYPES as $key => $label)
                                            <option value="{{ $key }}" 
                                                    {{ old('requirement_type', $template->requirement_type ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('requirement_type')
                                            {{ $message }}
                                        @else
                                            Tipe persyaratan wajib dipilih.
                                        @enderror
                                    </div>
                                </div>

                                <!-- Min Required (for choose_min type) -->
                                <div class="mb-3" id="minRequiredContainer" style="display: none;">
                                    <label for="min_required" class="form-label fw-semibold">
                                        <i class="bi bi-123 me-2"></i>Minimal Dokumen
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('min_required') is-invalid @enderror" 
                                           id="min_required" 
                                           name="min_required" 
                                           value="{{ old('min_required', $template->min_required ?? 1) }}" 
                                           min="1"
                                           placeholder="1">
                                    <div class="form-text">Minimal berapa dokumen yang harus diupload</div>
                                    <div class="invalid-feedback">
                                        @error('min_required')
                                            {{ $message }}
                                        @else
                                            Minimal dokumen wajib diisi untuk tipe ini.
                                        @enderror
                                    </div>
                                </div>

                                <!-- Type Explanation -->
                                <div class="alert alert-info small" id="typeExplanation">
                                    <div id="typeExplanationContent">
                                        Pilih tipe persyaratan terlebih dahulu.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage Warning -->
                @if ($template->certificationSchemes()->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Peringatan:</strong> Template ini sedang digunakan oleh
                                {{ $template->certificationSchemes()->count() }} skema sertifikasi.
                                Perubahan akan mempengaruhi semua skema yang menggunakan template ini.
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Document Items Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-list-check me-2"></i>Daftar Persyaratan
                                </h6>
                                <button type="button" class=" btn-primary-custom btn-sm" id="addRequirement">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Persyaratan
                                </button>
                            </div>
                            <div class="card-body" id="requirementsContainer">
                                @foreach ($template->items->sortBy('sort_order') as $index => $item)
                                    <div class="card mb-3 requirement-item" data-index="{{ $index }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h6 class="card-title mb-0">
                                                    <i class="bi bi-file-earmark me-2"></i>Persyaratan #<span
                                                        class="requirement-number">{{ $index + 1 }}</span>
                                                </h6>
                                                @if ($template->items->count() > 1)
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-requirement">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Nama Dokumen <span class="text-danger">*</span></label>
                                                    <input type="text" name="items[{{ $index }}][document_name]"
                                                        class="form-control" required
                                                        value="{{ old('items.' . $index . '.document_name', $item->document_name) }}"
                                                        placeholder="Contoh: Fotokopi Ijazah">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label fw-semibold">Tipe Input</label>
                                                    <select name="items[{{ $index }}][type]"
                                                        class="form-select requirement-type">
                                                        <option value="file_upload"
                                                            {{ $item->type == 'file_upload' ? 'selected' : '' }}>File Upload
                                                        </option>
                                                        <option value="text_input"
                                                            {{ $item->type == 'text_input' ? 'selected' : '' }}>Text Input</option>
                                                        <option value="number" {{ $item->type == 'number' ? 'selected' : '' }}>
                                                            Number</option>
                                                        <option value="select" {{ $item->type == 'select' ? 'selected' : '' }}>
                                                            Select/Dropdown</option>
                                                        <option value="checkbox" {{ $item->type == 'checkbox' ? 'selected' : '' }}>
                                                            Checkbox</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label fw-semibold">Wajib?</label>
                                                    <select name="items[{{ $index }}][is_required]" class="form-select">
                                                        <option value="1" {{ $item->is_required ? 'selected' : '' }}>Ya
                                                        </option>
                                                        <option value="0" {{ !$item->is_required ? 'selected' : '' }}>Tidak
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label fw-semibold">Deskripsi</label>
                                                    <textarea name="items[{{ $index }}][description]" class="form-control" rows="2"
                                                        placeholder="Deskripsi detail persyaratan...">{{ old('items.' . $index . '.description', $item->description) }}</textarea>
                                                </div>
                                            </div>

                                            <!-- File Upload Options -->
                                            <div class="file-upload-options {{ $item->type != 'file_upload' ? 'd-none' : '' }}">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-semibold">Ukuran Maksimal (KB)</label>
                                                        <input type="number"
                                                            name="items[{{ $index }}][validation_rules][max_size]"
                                                            class="form-control" placeholder="2048"
                                                            value="{{ old('items.' . $index . '.validation_rules.max_size', $item->validation_rules['max_size'] ?? '') }}">
                                                    </div>
                                                    <div class="col-md-8 mb-3">
                                                        <label class="form-label fw-semibold">Tipe File yang Diizinkan</label>
                                                        <input type="text"
                                                            name="items[{{ $index }}][validation_rules][allowed_types]"
                                                            class="form-control" placeholder="pdf,jpg,jpeg,png"
                                                            value="{{ old('items.' . $index . '.validation_rules.allowed_types', isset($item->validation_rules['allowed_types']) ? implode(',', $item->validation_rules['allowed_types']) : '') }}">
                                                        <small class="text-muted">Pisahkan dengan koma. Contoh:
                                                            pdf,jpg,jpeg,png</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Select Options -->
                                            <div class="select-options {{ $item->type != 'select' ? 'd-none' : '' }}">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Pilihan (satu per baris)</label>
                                                    <textarea name="items[{{ $index }}][options_text]" class="form-control" rows="3"
                                                        placeholder="Pilihan 1&#10;Pilihan 2&#10;Pilihan 3">{{ old('items.' . $index . '.options_text', is_array($item->options) ? implode("\n", $item->options) : '') }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Number Options -->
                                            <div class="number-options {{ $item->type != 'number' ? 'd-none' : '' }}">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-semibold">Nilai Minimum</label>
                                                        <input type="number"
                                                            name="items[{{ $index }}][validation_rules][min]"
                                                            class="form-control"
                                                            value="{{ old('items.' . $index . '.validation_rules.min', $item->validation_rules['min'] ?? '') }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-semibold">Nilai Maksimum</label>
                                                        <input type="number"
                                                            name="items[{{ $index }}][validation_rules][max]"
                                                            class="form-control"
                                                            value="{{ old('items.' . $index . '.validation_rules.max', $item->validation_rules['max'] ?? '') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.requirements.index') }}" class="btn btn-outline-secondary d-flex justify-content-center align-items-center">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                    <button type="submit" class=" btn-primary-custom">
                        <i class="bi bi-check-circle me-2"></i>Update Template
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Requirement Item Template (Hidden) -->
    <div id="requirementTemplate" class="d-none">
        <div class="card mb-3 requirement-item" data-index="0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i>Persyaratan #<span class="requirement-number">1</span>
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-requirement">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nama Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="items[0][document_name]" class="form-control" required
                            placeholder="Contoh: Fotokopi Ijazah">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Tipe Input</label>
                        <select name="items[0][type]" class="form-select requirement-type">
                            <option value="file_upload">File Upload</option>
                            <option value="text_input">Text Input</option>
                            <option value="number">Number</option>
                            <option value="select">Select/Dropdown</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Wajib?</label>
                        <select name="items[0][is_required]" class="form-select">
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="items[0][description]" class="form-control" rows="2"
                            placeholder="Deskripsi detail persyaratan..."></textarea>
                    </div>
                </div>

                <!-- File Upload Options -->
                <div class="file-upload-options">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Ukuran Maksimal (KB)</label>
                            <input type="number" name="items[0][validation_rules][max_size]" class="form-control"
                                placeholder="2048">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Tipe File yang Diizinkan</label>
                            <input type="text" name="items[0][validation_rules][allowed_types]" class="form-control"
                                placeholder="pdf,jpg,jpeg,png">
                            <small class="text-muted">Pisahkan dengan koma. Contoh: pdf,jpg,jpeg,png</small>
                        </div>
                    </div>
                </div>

                <!-- Select Options -->
                <div class="select-options d-none">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilihan (satu per baris)</label>
                        <textarea name="items[0][options_text]" class="form-control" rows="3"
                            placeholder="Pilihan 1&#10;Pilihan 2&#10;Pilihan 3"></textarea>
                    </div>
                </div>

                <!-- Number Options -->
                <div class="number-options d-none">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nilai Minimum</label>
                            <input type="number" name="items[0][validation_rules][min]" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nilai Maksimum</label>
                            <input type="number" name="items[0][validation_rules][max]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let requirementIndex = {{ $template->items->count() }};

            // Type explanations
            const typeExplanations = {
                'all_required': 'Semua dokumen dalam template ini WAJIB diupload. Cocok untuk dokumen identitas, ijazah, dll.',
                'choose_one': 'User hanya perlu upload SALAH SATU dokumen dari template ini. Cocok untuk alternatif dokumen seperti NPWP atau SIUP.',
                'choose_min': 'User harus upload minimal beberapa dokumen dari template ini. Cocok untuk referensi kerja, sertifikat pendukung, dll.'
            };

            // Handle requirement type change
            $('#requirement_type').change(function() {
                const type = $(this).val();
                const $minContainer = $('#minRequiredContainer');
                const $explanation = $('#typeExplanationContent');

                if (type === 'choose_min') {
                    $minContainer.show();
                    $('#min_required').attr('required', true);
                } else {
                    $minContainer.hide();
                    $('#min_required').attr('required', false);
                }

                if (type && typeExplanations[type]) {
                    $explanation.html(typeExplanations[type]);
                } else {
                    $explanation.html('Pilih tipe persyaratan terlebih dahulu.');
                }
            });

            // Trigger change on load
            $('#requirement_type').trigger('change');

            // Add new requirement
            $('#addRequirement').click(function() {
                addRequirement();
            });

            // Remove requirement
            $(document).on('click', '.remove-requirement', function() {
                if ($('.requirement-item').length > 1) {
                    $(this).closest('.requirement-item').remove();
                    updateRequirementNumbers();
                } else {
                    alert('Minimal harus ada satu persyaratan');
                }
            });

            // Handle requirement type change
            $(document).on('change', '.requirement-type', function() {
                const type = $(this).val();
                const container = $(this).closest('.requirement-item');

                // Hide all options
                container.find('.file-upload-options, .select-options, .number-options').addClass('d-none');

                // Show relevant options
                if (type === 'file_upload') {
                    container.find('.file-upload-options').removeClass('d-none');
                } else if (type === 'select') {
                    container.find('.select-options').removeClass('d-none');
                } else if (type === 'number') {
                    container.find('.number-options').removeClass('d-none');
                }
            });

            // Form submission
            $('#requirementForm').on('submit', function(e) {
                const itemCount = $('.requirement-item').length;
                const requirementType = $('#requirement_type').val();
                const minRequired = parseInt($('#min_required').val()) || 1;

                if (itemCount === 0) {
                    e.preventDefault();
                    alert('Minimal harus ada 1 dokumen dalam template!');
                    return false;
                }

                if (requirementType === 'choose_min' && minRequired > itemCount) {
                    e.preventDefault();
                    alert('Minimal dokumen tidak boleh lebih besar dari jumlah dokumen yang ada!');
                    return false;
                }

                // Process select options
                $('.requirement-item').each(function() {
                    const optionsText = $(this).find('textarea[name$="[options_text]"]').val();
                    if (optionsText) {
                        const options = optionsText.split('\n').filter(option => option.trim() !==
                            '');
                        $(this).find('input[name$="[options]"]').remove();

                        // Add hidden input for options array
                        options.forEach((option, index) => {
                            $(this).append(
                                `<input type="hidden" name="${$(this).find('textarea[name$="[options_text]"]').attr('name').replace('[options_text]', '[options][' + index + ']')}" value="${option.trim()}">`
                            );
                        });
                    }
                });

                // Process allowed_types for file uploads
                $('.requirement-item').each(function() {
                    const allowedTypesInput = $(this).find(
                        'input[name$="[validation_rules][allowed_types]"]');
                    if (allowedTypesInput.length && allowedTypesInput.val()) {
                        const types = allowedTypesInput.val().split(',').map(type => type.trim())
                            .filter(type => type !== '');
                        const baseName = allowedTypesInput.attr('name').replace('[allowed_types]',
                            '');

                        // Remove existing hidden inputs
                        $(this).find('input[name^="' + baseName + '[allowed_types]"]').not(
                            allowedTypesInput).remove();

                        // Add hidden inputs for array
                        types.forEach((type, index) => {
                            $(this).append(
                                `<input type="hidden" name="${baseName}[allowed_types][${index}]" value="${type}">`
                            );
                        });

                        // Clear the original input to avoid conflicts
                        allowedTypesInput.val('');
                    }
                });
            });

            function addRequirement() {
                const template = $('#requirementTemplate').html();
                const newRequirement = template.replace(/\[0\]/g, '[' + requirementIndex + ']')
                    .replace('data-index="0"', 'data-index="' + requirementIndex + '"');

                $('#requirementsContainer').append(newRequirement);
                requirementIndex++;
                updateRequirementNumbers();
            }

            function updateRequirementNumbers() {
                $('.requirement-item').each(function(index) {
                    $(this).find('.requirement-number').text(index + 1);

                    // Update name attributes
                    $(this).find('input, select, textarea').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                            $(this).attr('name', newName);
                        }
                    });

                    $(this).attr('data-index', index);
                });
            }
        });
    </script>
@endpush