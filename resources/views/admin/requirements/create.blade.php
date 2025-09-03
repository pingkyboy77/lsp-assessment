@extends('layouts.admin')

@section('title', 'Tambah Template Persyaratan')

@section('content')
    <div class="main-card">
        <div class="card-header-custom">
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
                        <i class="bi bi-plus-circle-fill me-2"></i>Tambah Template Persyaratan
                    </h5>
                    <p class="mb-0 text-muted">Buat template persyaratan untuk skema sertifikasi</p>
                </div>
                <div>
                    <a href="{{ route('admin.requirements.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body m-3">
            <form action="{{ route('admin.requirements.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

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
                                           value="{{ old('name') }}" 
                                           placeholder="Contoh: Identitas Diri, Pendidikan"
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
                                              placeholder="Deskripsi singkat tentang template ini...">{{ old('description') }}</textarea>
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
                                            <option value="{{ $key }}" {{ old('requirement_type') == $key ? 'selected' : '' }}>
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
                                           value="{{ old('min_required', 1) }}" 
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

                <!-- Document Items -->
                <div class="row my-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-list-check me-2"></i>Daftar Persyaratan
                                </h6>
                                <button type="button" class="btn btn-primary-custom btn-sm" id="addItem">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Persyaratan
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="itemsContainer">
                                    @if(old('items'))
                                        @foreach(old('items') as $index => $item)
                                            <div class="card mb-3 requirement-item">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h6 class="card-title mb-0">
                                                            <i class="bi bi-file-earmark me-2"></i>
                                                            <span class="item-number">Persyaratan #{{ $index + 1 }}</span>
                                                        </h6>
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Nama Dokumen <span class="text-danger">*</span></label>
                                                                <input type="text" 
                                                                       class="form-control" 
                                                                       name="items[{{ $index }}][document_name]" 
                                                                       value="{{ $item['document_name'] }}"
                                                                       placeholder="Contoh: KTP, Ijazah, Sertifikat"
                                                                       required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Tipe Input <span class="text-danger">*</span></label>
                                                                <select class="form-select item-type" name="items[{{ $index }}][type]" required>
                                                                    <option value="">Pilih Tipe</option>
                                                                    @foreach(\App\Models\RequirementItem::TYPES as $key => $label)
                                                                        <option value="{{ $key }}" {{ $item['type'] == $key ? 'selected' : '' }}>
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Deskripsi</label>
                                                                <textarea class="form-control" 
                                                                          name="items[{{ $index }}][description]" 
                                                                          rows="2"
                                                                          placeholder="Deskripsi atau petunjuk untuk item ini...">{{ $item['description'] ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="options-container {{ in_array($item['type'], ['select', 'checkbox']) ? '' : 'd-none' }}">
                                                        <label class="form-label fw-semibold">Opsi (pisahkan dengan koma)</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="items[{{ $index }}][options]" 
                                                               value="{{ $item['options'] ?? '' }}"
                                                               placeholder="Contoh: Ya, Tidak">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <div id="emptyState" class="text-center py-4 text-muted" {{ old('items') ? 'style=display:none' : '' }}>
                                    <i class="bi bi-inbox display-4"></i>
                                    <p class="mt-2">Belum ada dokumen. Klik "Tambah Persyaratan" untuk memulai.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row ">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.requirements.index') }}" class="btn btn-outline-secondary d-flex justify-content-center align-items-center">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary-custom text-light">
                                <i class="bi bi-check-circle me-2"></i>Simpan Template
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Item Template (Hidden) -->
    <div id="itemTemplate" class="d-none">
        <div class="card mb-3 requirement-item">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i>
                        <span class="item-number">Persyaratan #1</span>
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Dokumen <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   name="items[INDEX][document_name]" 
                                   placeholder="Contoh: KTP, Ijazah, Sertifikat"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Input <span class="text-danger">*</span></label>
                            <select class="form-select item-type" name="items[INDEX][type]" required>
                                <option value="">Pilih Tipe</option>
                                @foreach(\App\Models\RequirementItem::TYPES as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" 
                                      name="items[INDEX][description]" 
                                      rows="2"
                                      placeholder="Deskripsi atau petunjuk untuk item ini..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="options-container d-none">
                    <label class="form-label fw-semibold">Opsi (pisahkan dengan koma)</label>
                    <input type="text" 
                           class="form-control" 
                           name="items[INDEX][options]" 
                           placeholder="Contoh: Ya, Tidak">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let itemIndex = {{ old('items') ? count(old('items')) : 0 }};

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

            // Add item function
            $('#addItem').click(function() {
                addNewItem();
                updateEmptyState();
            });

            // Remove item function
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.requirement-item').remove();
                updateItemNumbers();
                updateEmptyState();
            });

            // Handle type change for showing/hiding options
            $(document).on('change', '.item-type', function() {
                const $container = $(this).closest('.requirement-item');
                const $optionsContainer = $container.find('.options-container');
                
                if ($(this).val() === 'select' || $(this).val() === 'checkbox') {
                    $optionsContainer.removeClass('d-none');
                } else {
                    $optionsContainer.addClass('d-none');
                }
            });

            function addNewItem() {
                let template = $('#itemTemplate').html();
                template = template.replaceAll('INDEX', itemIndex);
                
                $('#itemsContainer').append(template);
                itemIndex++;
                updateItemNumbers();
            }

            function updateItemNumbers() {
                $('.requirement-item').each(function(index) {
                    $(this).find('.item-number').text('Persyaratan #' + (index + 1));
                    // Update name attributes
                    $(this).find('input, select, textarea').each(function() {
                        const name = $(this).attr('name');
                        if (name && name.includes('[') && name.includes(']')) {
                            const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                            $(this).attr('name', newName);
                        }
                    });
                });
            }

            function updateEmptyState() {
                if ($('.requirement-item').length === 0) {
                    $('#emptyState').show();
                } else {
                    $('#emptyState').hide();
                }
            }

            // Form validation
            $('form').on('submit', function(e) {
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
            });

            // Initialize empty state
            updateEmptyState();
        });
    </script>
@endpush