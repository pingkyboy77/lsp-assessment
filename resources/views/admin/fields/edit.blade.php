@extends('layouts.admin')

@section('title', 'Edit Bidang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="main-card">
                    <div class="card-header-custom mb-4">
                        @if (session('success'))
                            <div class="alert-success-custom">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 text-dark fw-bold">
                                    <i class="bi bi-diagram-3-fill me-2"></i>Edit Bidang Sertifikasi
                                </h5>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ url()->previous() }}"
                                    class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                                    <i class="bi bi-arrow-left me-2"></i> Kembali
                                </a>

                            </div>
                        </div>
                    </div>

                    <div class="card-body m-3">
                        <form action="{{ route('admin.fields.update', $field) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="kode_bidang" class="form-label">Kode Bidang <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('kode_bidang') is-invalid @enderror" id="kode_bidang"
                                            name="kode_bidang" value="{{ old('kode_bidang', $field->kode_bidang) }}"
                                            required>
                                        @error('kode_bidang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Kode akan otomatis diubah ke huruf kapital</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="code_2" class="form-label">Code 2 <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('code_2') is-invalid @enderror"
                                            id="code_2" name="code_2" value="{{ old('code_2', $field->code_2) }}"
                                            maxlength="10" required>
                                        @error('code_2')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Maksimal 10 karakter, akan otomatis diubah ke huruf kapital
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="bidang" class="form-label">Nama Bidang <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('bidang') is-invalid @enderror"
                                            id="bidang" name="bidang" value="{{ old('bidang', $field->bidang) }}"
                                            required>
                                        @error('bidang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="bidang_ing" class="form-label">Nama Bidang (English)</label>
                                        <input type="text" class="form-control @error('bidang_ing') is-invalid @enderror"
                                            id="bidang_ing" name="bidang_ing"
                                            value="{{ old('bidang_ing', $field->bidang_ing) }}">
                                        @error('bidang_ing')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="kbbli_bidang" class="form-label">KBBLI Bidang</label>
                                        <input type="text"
                                            class="form-control @error('kbbli_bidang') is-invalid @enderror"
                                            id="kbbli_bidang" name="kbbli_bidang"
                                            value="{{ old('kbbli_bidang', $field->kbbli_bidang) }}">
                                        @error('kbbli_bidang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="kode_web" class="form-label">Kode Web</label>
                                        <input type="text" class="form-control @error('kode_web') is-invalid @enderror"
                                            id="kode_web" name="kode_web" value="{{ old('kode_web', $field->kode_web) }}">
                                        @error('kode_web')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Kode akan otomatis diubah ke huruf kapital</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="4">{{ old('description', $field->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active"
                                                name="is_active" value="1"
                                                {{ old('is_active', $field->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Status Aktif
                                            </label>
                                        </div>
                                        @if ($field->certificationSchemes->count() > 0)
                                            <div class="form-text text-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Bidang ini memiliki {{ $field->certificationSchemes->count() }} skema
                                                sertifikasi
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Dibuat:</strong> {{ $field->created_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Terakhir diupdate:</strong>
                                                {{ $field->updated_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </div> --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary-custom me-2">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-warning text-dark">
                                    <i class="bi bi-save"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto uppercase for specific fields
            $('#kode_bidang, #code_2, #kode_web').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });

            // Form validation
            $('form').on('submit', function(e) {
                var isValid = true;

                // Required field validation
                $('input[required]').each(function() {
                    if ($(this).val().trim() === '') {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Mohon lengkapi semua field yang wajib diisi.');
                }
            });
        });
    </script>
@endpush
