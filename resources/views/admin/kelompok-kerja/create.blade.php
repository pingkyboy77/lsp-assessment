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
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="p_level">P Level <span class="text-danger">*</span></label>
                                <select class="form-control @error('p_level') is-invalid @enderror" 
                                        id="p_level" 
                                        name="p_level" 
                                        required>
                                    <option value="">-- Pilih P Level --</option>
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" 
                                                {{ old('p_level') == $i ? 'selected' : '' }}
                                                {{ in_array($i, $usedPLevels) ? 'disabled' : '' }}>
                                            P{{ $i }} {{ in_array($i, $usedPLevels) ? '(Sudah digunakan)' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('p_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle"></i> P Level harus unik untuk setiap kelompok kerja. Angka sinkron dengan MAPA.
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
                                  placeholder="Masukkan deskripsi singkat tentang kelompok kerja ini...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Potensi Asesi <span class="text-danger">*</span></label>
                        <div class="card">
                            <div class="card-body">
                                @foreach($potensiAsesiOptions as $key => $label)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input @error('potensi_asesi') is-invalid @enderror" 
                                               type="checkbox" 
                                               id="{{ $key }}" 
                                               name="potensi_asesi[]"
                                               value="{{ $key }}"
                                               {{ in_array($key, old('potensi_asesi', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $key }}">
                                            <strong>{{ strtoupper($key) }}:</strong> {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                                @error('potensi_asesi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle"></i> Pilih minimal satu potensi asesi yang sesuai dengan kelompok kerja ini
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Catatan:</strong> 
                        <ul class="mb-0 mt-2">
                            <li>P Level harus unik dan akan digunakan untuk sinkronisasi dengan MAPA</li>
                            <li>Potensi Asesi yang dipilih akan otomatis ditampilkan saat membuat MAPA dengan P Level yang sama</li>
                            <li>Setelah kelompok kerja dibuat, Anda dapat menambahkan unit kompetensi</li>
                        </ul>
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