{{-- resources/views/admin/unit-kompetensi/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Unit Kompetensi - ' . $unitKompetensi->kode_unit)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

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
                        <i class="bi bi-list-check me-2"></i>Edit Unit Kompetensi
                    </h5>
                    <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema Sertifikasi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ $scheme->nama }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schemes.unit-kompetensi.index', $scheme) }}">Unit Kompetensi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi]) }}">{{ $unitKompetensi->kode_unit }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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

    <!-- Page Header -->
    {{-- <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Edit Unit Kompetensi</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema Sertifikasi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.show', $scheme) }}">{{ $scheme->nama }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schemes.unit-kompetensi.index', $scheme) }}">Unit Kompetensi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi]) }}">{{ $unitKompetensi->kode_unit }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div> --}}

    <div class="card-body m-3">
        <!-- Scheme Info -->
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-2"></i>
                <div>
                    <strong>Skema Sertifikasi:</strong> {{ $scheme->nama }} 
                    <span class="badge bg-{{ $scheme->jenjang_color }} ms-2">{{ $scheme->jenjang }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.schemes.unit-kompetensi.update', [$scheme, $unitKompetensi]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Basic Information Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Informasi Dasar</h6>
                        </div>
                        <div class="card-body">
                            <!-- Kode Unit -->
                            <div class="mb-3">
                                <label for="kode_unit" class="form-label">Kode Unit <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control @error('kode_unit') is-invalid @enderror" 
                                    id="kode_unit" 
                                    name="kode_unit" 
                                    value="{{ old('kode_unit', $unitKompetensi->kode_unit) }}"
                                    placeholder="Contoh: TIK.PR02.001.01"
                                    required
                                >
                                @error('kode_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Judul Unit -->
                            <div class="mb-3">
                                <label for="judul_unit" class="form-label">Judul Unit <span class="text-danger">*</span></label>
                                <textarea 
                                    class="form-control @error('judul_unit') is-invalid @enderror" 
                                    id="judul_unit" 
                                    name="judul_unit" 
                                    rows="3"
                                    placeholder="Masukkan judul unit kompetensi"
                                    required
                                >{{ old('judul_unit', $unitKompetensi->judul_unit) }}</textarea>
                                @error('judul_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Standar Kompetensi Kerja -->
                            <div class="mb-3">
                                <label for="standar_kompetensi_kerja" class="form-label">Standar Kompetensi Kerja</label>
                                <textarea 
                                    class="form-control @error('standar_kompetensi_kerja') is-invalid @enderror" 
                                    id="standar_kompetensi_kerja" 
                                    name="standar_kompetensi_kerja" 
                                    rows="4"
                                    placeholder="Masukkan standar kompetensi kerja (opsional)"
                                >{{ old('standar_kompetensi_kerja', $unitKompetensi->standar_kompetensi_kerja) }}</textarea>
                                @error('standar_kompetensi_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Kelompok Kerja Card -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-people me-2"></i>Kelompok Kerja
                            </h6>
                            <small class="text-muted">
                                {{ $unitKompetensi->kelompokKerjas->count() }} kelompok saat ini
                            </small>
                        </div>
                        <div class="card-body">
                            @if($kelompokKerjas->count() > 0)
                                <!-- Current Kelompok Kerja -->
                                @if($unitKompetensi->kelompokKerjas->count() > 0)
                                    <div class="alert alert-light mb-3">
                                        <h6 class="alert-heading mb-2">
                                            <i class="bi bi-bookmark-check"></i> Kelompok Kerja Saat Ini:
                                        </h6>
                                        <div>
                                            @foreach($unitKompetensi->kelompokKerjas as $kelompok)
                                                <span class="badge bg-secondary me-1">
                                                    <i class="bi bi-people"></i> {{ $kelompok->nama_kelompok }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="kelompok_kerja_ids" class="form-label">Pilih Kelompok Kerja</label>
                                    <select 
                                        class="form-select @error('kelompok_kerja_ids') is-invalid @enderror" 
                                        id="kelompok_kerja_ids" 
                                        name="kelompok_kerja_ids[]" 
                                        multiple
                                        data-placeholder="Pilih kelompok kerja untuk unit ini..."
                                    >
                                        @foreach($kelompokKerjas as $kelompok)
                                            <option 
                                                value="{{ $kelompok->id }}" 
                                                {{ in_array($kelompok->id, old('kelompok_kerja_ids', $selectedKelompokKerjas ?? [])) ? 'selected' : '' }}
                                            >
                                                {{ $kelompok->nama_kelompok }}
                                                @if($kelompok->deskripsi)
                                                    - {{ Str::limit($kelompok->deskripsi, 50) }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i> 
                                        Unit kompetensi dapat dimasukkan ke dalam beberapa kelompok kerja.
                                        Kosongkan untuk menghapus dari semua kelompok.
                                    </div>
                                    @error('kelompok_kerja_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preview Selected Kelompok -->
                                <div id="selected-kelompok-preview" class="d-none">
                                    <h6 class="mb-2">Kelompok Kerja yang Akan Dipilih:</h6>
                                    <div id="selected-kelompok-badges"></div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Belum ada kelompok kerja!</strong><br>
                                    Anda perlu membuat kelompok kerja terlebih dahulu.
                                    <a href="{{ route('admin.schemes.kelompok-kerja.create', $scheme) }}" class="btn btn-sm btn-warning mt-2">
                                        <i class="bi bi-plus"></i> Buat Kelompok Kerja
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Statistics Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-bar-chart me-2"></i>Statistik Unit
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-1">{{ $unitKompetensi->elemenKompetensis->count() }}</h4>
                                        <small class="text-muted">Elemen Kompetensi</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border-end">
                                        <h4 class="text-success mb-1">{{ $unitKompetensi->elemenKompetensis->sum(fn($e) => $e->kriteriaKerjas->count()) }}</h4>
                                        <small class="text-muted">Kriteria Kerja</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="text-info mb-1">{{ $unitKompetensi->kelompokKerjas->count() }}</h4>
                                    <small class="text-muted">Kelompok Kerja</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Status & Actions Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Status & Pengaturan</h6>
                        </div>
                        <div class="card-body">
                            <!-- Status -->
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        id="is_active" 
                                        name="is_active" 
                                        value="1"
                                        {{ old('is_active', $unitKompetensi->is_active) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="is_active">
                                        Unit Aktif
                                    </label>
                                </div>
                                <small class="text-muted">Unit yang nonaktif tidak akan ditampilkan dalam penilaian.</small>
                            </div>

                            <!-- Timestamps -->
                            <div class="border-top pt-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-calendar-plus"></i>
                                    <strong>Dibuat:</strong><br>
                                    {{ $unitKompetensi->created_at->format('d M Y, H:i') }}
                                </small>
                                @if($unitKompetensi->updated_at != $unitKompetensi->created_at)
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar-check"></i>
                                        <strong>Diperbarui:</strong><br>
                                        {{ $unitKompetensi->updated_at->format('d M Y, H:i') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Aksi Cepat</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>
                                {{-- <button type="button" class="btn btn-outline-info btn-sm" onclick="duplicateUnit()">
                                    <i class="bi bi-files"></i> Duplikasi Unit
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unitKompetensi]) }}" class="btn btn-secondary me-2">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Perbarui Unit Kompetensi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for kelompok kerja
    $('#kelompok_kerja_ids').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih kelompok kerja untuk unit ini...',
        allowClear: true,
        width: '100%'
    });

    // Preview selected kelompok kerja
    $('#kelompok_kerja_ids').on('change', function() {
        const selectedOptions = $(this).find('option:selected');
        const previewContainer = $('#selected-kelompok-preview');
        const badgesContainer = $('#selected-kelompok-badges');

        if (selectedOptions.length > 0) {
            let badges = '';
            selectedOptions.each(function() {
                const text = $(this).text().split(' - ')[0]; // Remove description part
                badges += `<span class="badge bg-success me-1 mb-1">
                    <i class="bi bi-people"></i> ${text}
                </span>`;
            });
            badgesContainer.html(badges);
            previewContainer.removeClass('d-none');
        } else {
            previewContainer.addClass('d-none');
        }
    });

    // Trigger preview on page load if there are selected items
    if ($('#kelompok_kerja_ids').val() && $('#kelompok_kerja_ids').val().length > 0) {
        $('#kelompok_kerja_ids').trigger('change');
    }
});


</script>
@endpush