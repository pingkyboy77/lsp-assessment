{{-- Form Create/Edit Certification Scheme --}}
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
                    <p class="mb-0 text-muted">{{ isset($certificationScheme) ? 'Perbarui' : 'Buat' }} skema sertifikasi baru</p>
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

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <a href="{{ route('admin.certification-schemes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class=" btn-primary-custom text-light">
                                <i class="bi bi-check-circle me-2"></i>{{ isset($certificationScheme) ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle status text
            $('#is_active').change(function() {
                const statusText = $(this).is(':checked') ? 'Aktif' : 'Tidak Aktif';
                $('.status-text').text(statusText);
            });

            // Form validation
            $('form').on('submit', function(e) {
                // Basic form validation - can add more rules here if needed
                const nama = $('#nama').val().trim();
                const code_1 = $('#code_1').val().trim();
                const code_2 = $('#code_2').val();
                
                if (!nama) {
                    e.preventDefault();
                    alert('Nama skema wajib diisi!');
                    $('#nama').focus();
                    return false;
                }
                
                if (!code_1) {
                    e.preventDefault();
                    alert('Kode skema wajib diisi!');
                    $('#code_1').focus();
                    return false;
                }
                
                if (!code_2) {
                    e.preventDefault();
                    alert('Bidang wajib dipilih!');
                    $('#code_2').focus();
                    return false;
                }
            });
        });
    </script>
@endpush