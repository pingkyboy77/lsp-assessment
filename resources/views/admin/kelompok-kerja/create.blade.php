{{-- resources/views/admin/kelompok-kerja/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Kelompok Kerja - ' . $scheme->nama)

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
                        <i class="bi bi-people-fill me-2"></i>Tambah Kelompok Kerja
                    </h5>
                    <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema Sertifikasi</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ Str::limit($scheme->nama, 30) }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}">Kelompok Kerja</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

    <div class="m-3">

    

    <!-- Scheme Info -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-1">{{ $scheme->nama }}</h5>
            <p class="text-muted mb-0">{{ $scheme->code_1 }} | Jenjang: 
                <span class="badge badge-{{ $scheme->jenjang_color }}">{{ $scheme->jenjang }}</span>
            </p>
        </div>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Form Tambah Kelompok Kerja</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.schemes.kelompok-kerja.store', $scheme) }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="nama_kelompok">Nama Kelompok Kerja <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nama_kelompok') is-invalid @enderror" 
                                   id="nama_kelompok" 
                                   name="nama_kelompok" 
                                   value="{{ old('nama_kelompok') }}"
                                   placeholder="Contoh: Marketing Officer (Tabungan dan Deposito)"
                                   required>
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
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
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
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                              id="deskripsi" 
                              name="deskripsi" 
                              rows="4"
                              placeholder="Masukkan deskripsi singkat tentang kelompok kerja ini, ruang lingkup pekerjaan, atau karakteristik khusus...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Deskripsi opsional untuk memberikan penjelasan tambahan tentang kelompok kerja ini
                    </small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Catatan:</strong> Setelah kelompok kerja dibuat, Anda dapat menambahkan bukti portofolio yang diperlukan untuk kelompok kerja ini.
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Kelompok Kerja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if(session('success'))
        showAlert('success', '{{ session("success") }}');
    @endif
    
    @if(session('error'))
        showAlert('danger', '{{ session("error") }}');
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