@extends('layouts.admin')

@section('title', 'Edit Kelompok Kerja - ' . $kelompokKerja->nama_kelompok)

@section('content')
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
                        <i class="bi bi-people-fill me-2"></i>Edit Kelompok Kerja
                    </h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema
                                    Sertifikasi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ Str::limit($scheme->nama, 30) }}</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}">Kelompok Kerja</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompokKerja]) }}">{{ $kelompokKerja->nama_kelompok }}</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() }}"
                        class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="m-3">
            <!-- Current Kelompok Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">{{ $kelompokKerja->nama_kelompok }}</h5>
                            <p class="text-muted mb-0">{{ $scheme->nama }} |
                                <span
                                    class="badge badge-{{ $kelompokKerja->status_color }}">{{ $kelompokKerja->status_text }}</span>
                            </p>
                        </div>
                        <div class="text-muted">
                            <small>
                                {{ $kelompokKerja->active_bukti_count }} Bukti Portofolio
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Form Edit Kelompok Kerja</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.schemes.kelompok-kerja.update', [$scheme, $kelompokKerja]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nama_kelompok">Nama Kelompok Kerja <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_kelompok') is-invalid @enderror"
                                        id="nama_kelompok" name="nama_kelompok"
                                        value="{{ old('nama_kelompok', $kelompokKerja->nama_kelompok) }}"
                                        placeholder="Contoh: Marketing Officer (Tabungan dan Deposito)" required>
                                    @error('nama_kelompok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Nama kelompok kerja harus spesifik dan mudah diidentifikasi
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                            value="1"
                                            {{ old('is_active', $kelompokKerja->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Aktif</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Kelompok kerja yang tidak aktif tidak akan ditampilkan dalam sertifikasi
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi Kelompok Kerja</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4"
                                placeholder="Masukkan deskripsi singkat tentang kelompok kerja ini, ruang lingkup pekerjaan, atau karakteristik khusus...">{{ old('deskripsi', $kelompokKerja->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Deskripsi opsional untuk memberikan penjelasan tambahan tentang kelompok kerja ini
                            </small>
                        </div>

                        @if ($kelompokKerja->bukti_count > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Info:</strong> Kelompok kerja ini memiliki {{ $kelompokKerja->bukti_count }} bukti
                                portofolio.
                                Anda dapat mengelola bukti portofolio di halaman detail setelah menyimpan perubahan ini.
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.schemes.kelompok-kerja.show', [$scheme, $kelompokKerja]) }}"
                                class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash"></i> Hapus Kelompok
                                </button>
                                <button type="submit" class="btn btn-primary ml-2">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus Kelompok Kerja</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus kelompok kerja
                            <strong>{{ $kelompokKerja->nama_kelompok }}</strong>?</p>
                        <p class="text-danger">
                            <strong>Peringatan:</strong> Semua bukti portofolio yang terkait juga akan dihapus secara
                            permanen.
                        </p>
                        @if ($kelompokKerja->bukti_count > 0)
                            <div class="alert alert-info">
                                <small>
                                    <strong>Data yang akan dihapus:</strong><br>
                                    • {{ $kelompokKerja->bukti_count }} Bukti Portofolio
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <form id="deleteForm" method="POST"
                            action="{{ route('admin.schemes.kelompok-kerja.destroy', [$scheme, $kelompokKerja]) }}"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Ya, Hapus Kelompok Kerja
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            $('#deleteModal').modal('show');
        }

        $(document).ready(function() {
            @if (session('success'))
                showAlert('success', '{{ session('success') }}');
            @endif

            @if (session('error'))
                showAlert('danger', '{{ session('error') }}');
            @endif
        });

        function showAlert(type, message) {
            const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;

            $('.container-fluid').prepend(alertHtml);

            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    </script>
@endpush
