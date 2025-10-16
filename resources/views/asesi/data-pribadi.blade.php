@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('asesi.data-pribadi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- HEADER SECTION --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h4 class="mb-1 text-dark">Profile</h4>
                                    <p class="mb-0 text-muted">Lengkapi data profil dan dokumen pendukung Anda</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PERSONAL DATA SECTION --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 text-dark">
                        <i class="bi bi-person-badge me-2"></i>
                        Data Pribadi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Nama Lengkap --}}
                        <div class="col-md-12">
                            <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap"
                                value="{{ old('nama_lengkap', $profile->nama_lengkap ?? '') }}"
                                class="form-control @error('nama_lengkap') is-invalid @enderror" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NIK --}}
                        <div class="col-md-12">
                            <label for="nik" class="form-label fw-semibold">
                                Nomor Identitas (KTP / Paspor) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nik" id="nik"
                                value="{{ old('nik', $userProfile->nik ?? (auth()->user()->id_number ?? '')) }}"
                                class="form-control @error('nik') is-invalid @enderror" maxlength="16" required>


                            <div id="nikWarning" class="text-danger small mt-1" style="display:none;">
                                Nomor identitas harus tepat 16 digit.
                            </div>

                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tempat Lahir --}}
                        <div class="col-md-6">
                            <label for="tempat_lahir" class="form-label fw-semibold">Tempat Lahir <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir"
                                value="{{ old('tempat_lahir', $profile->tempat_lahir ?? '') }}"
                                class="form-control @error('tempat_lahir') is-invalid @enderror" required>
                            @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label fw-semibold">
                                Tanggal Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                value="{{ old('tanggal_lahir', isset($profile->tanggal_lahir) ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('Y-m-d') : '') }}"
                                class="form-control @error('tanggal_lahir') is-invalid @enderror" required>
                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="col-md-6">
                            <label for="jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin <span
                                    class="text-danger">*</span></label>
                            <select name="jenis_kelamin" id="jenis_kelamin"
                                class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L"
                                    {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="P"
                                    {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kebangsaan --}}
                        <div class="col-md-6">
                            <label for="kebangsaan" class="form-label fw-semibold">Kebangsaan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="kebangsaan" id="kebangsaan"
                                value="{{ old('kebangsaan', $profile->kebangsaan ?? 'Indonesia') }}"
                                class="form-control @error('kebangsaan') is-invalid @enderror" required>
                            @error('kebangsaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- No Telepon Rumah --}}
                        <div class="col-md-6">
                            <label for="no_telp_rumah" class="form-label fw-semibold">No. Telepon Rumah <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="no_telp_rumah" id="no_telp_rumah"
                                value="{{ old('no_telp_rumah', $profile->no_telp_rumah ?? '') }}"
                                class="form-control @error('no_telp_rumah') is-invalid @enderror" required>
                            @error('no_telp_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- No HP --}}
                        <div class="col-md-6">
                            <label for="no_hp" class="form-label fw-semibold">No. HP <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="no_hp"
                                value="{{ old('no_hp', $profile->no_hp ?? '') }}"
                                class="form-control @error('no_hp') is-invalid @enderror" required>
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-12">
                            <label for="email" class="form-label fw-semibold">Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', auth()->user()->email ?? '') }}" class="form-control" readonly>
                        </div>

                        {{-- Pendidikan Terakhir --}}
                        <div class="col-md-6">
                            <label for="pendidikan_terakhir" class="form-label fw-semibold">Pendidikan Terakhir <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('pendidikan_terakhir') is-invalid @enderror"
                                name="pendidikan_terakhir" id="pendidikan_terakhir" required>
                                <option value="">Pilih Pendidikan</option>
                                @foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor'] as $edu)
                                    <option value="{{ $edu }}"
                                        {{ old('pendidikan_terakhir', $profile->pendidikan_terakhir ?? '') == $edu ? 'selected' : '' }}>
                                        {{ $edu }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pendidikan_terakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Sekolah Terakhir --}}
                        <div class="col-md-6">
                            <label for="nama_sekolah_terakhir" class="form-label fw-semibold">Nama Sekolah Terakhir <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_sekolah_terakhir" id="nama_sekolah_terakhir"
                                value="{{ old('nama_sekolah_terakhir', $profile->nama_sekolah_terakhir ?? '') }}"
                                class="form-control @error('nama_sekolah_terakhir') is-invalid @enderror" required>
                            @error('nama_sekolah_terakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        {{-- Alamat Rumah --}}
                        <div class="col-md-12">
                            <label for="alamat_rumah" class="form-label fw-semibold">Alamat Rumah <span
                                    class="text-danger">*</span></label>
                            <textarea name="alamat_rumah" id="alamat_rumah" rows="3"
                                class="form-control @error('alamat_rumah') is-invalid @enderror" required>{{ old('alamat_rumah', $profile->alamat_rumah ?? '') }}</textarea>
                            @error('alamat_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kota Rumah --}}
                        <div class="col-md-6">
                            <label for="kota_rumah" class="form-label fw-semibold">Kota <span
                                    class="text-danger">*</span></label>
                            <select name="kota_rumah" id="kota_rumah"
                                class="form-select @error('kota_rumah') is-invalid @enderror" required>
                                <option value="">Pilih Kota</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" data-province-id="{{ $city->province->id }}"
                                        data-province-name="{{ $city->province->name }}"
                                        {{ old('kota_rumah', $profile->kota_rumah ?? '') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kota_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Provinsi Rumah --}}
                        <div class="col-md-6">
                            <label for="provinsi_rumah" class="form-label fw-semibold">Provinsi <span
                                    class="text-danger">*</span></label>
                            <input type="hidden" id="provinsi_rumah" name="provinsi_rumah"
                                value="{{ old('provinsi_rumah', $profile->provinsi_rumah ?? '') }}">
                            <input type="text" id="provinsi_rumah_display" class="form-control"
                                value="{{ old('provinsi_rumah_display', $profile->provinceRumah?->name ?? '') }}"
                                placeholder="Provinsi akan otomatis terisi" readonly>
                            @error('provinsi_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kode Pos --}}
                        <div class="col-md-4">
                            <label for="kode_pos" class="form-label fw-semibold">Kode Pos <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="kode_pos" id="kode_pos"
                                value="{{ old('kode_pos', $profile->kode_pos ?? '') }}"
                                class="form-control @error('kode_pos') is-invalid @enderror" maxlength="5" required>
                            @error('kode_pos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- WORK INFORMATION SECTION --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 text-dark">
                        <i class="bi bi-briefcase me-2"></i>
                        Informasi Pekerjaan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Nama Institusi/Perusahaan --}}
                        <div class="col-12">
                            <label for="nama_tempat_kerja" class="form-label fw-semibold">Nama Institusi / Perusahaan
                                <span class="text-danger">*</span></label>
                            <input type="text" name="nama_tempat_kerja" id="nama_tempat_kerja"
                                value="{{ old('nama_tempat_kerja', $profile->nama_tempat_kerja ?? '') }}"
                                class="form-control @error('nama_tempat_kerja') is-invalid @enderror" required>
                            @error('nama_tempat_kerja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori Pekerjaan --}}
                        <div class="col-12">
                            <label for="kategori_pekerjaan" class="form-label fw-semibold">Kategori Pekerjaan <span
                                    class="text-danger">*</span></label>
                            <select name="kategori_pekerjaan" id="kategori_pekerjaan"
                                class="form-select @error('kategori_pekerjaan') is-invalid @enderror" required>
                                <option value="">Pilih Kategori Pekerjaan</option>
                                @foreach (\App\Models\Pekerjaan::orderBy('kode')->get() as $pekerjaan)
                                    <option value="{{ $pekerjaan->nama_pekerjaan }}"
                                        {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == $pekerjaan->nama_pekerjaan ? 'selected' : '' }}>
                                        {{ $pekerjaan->nama_pekerjaan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_pekerjaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jabatan --}}
                        <div class="col-12">
                            <label for="jabatan" class="form-label fw-semibold">Jabatan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="jabatan" id="jabatan"
                                value="{{ old('jabatan', $profile->jabatan ?? '') }}"
                                class="form-control @error('jabatan') is-invalid @enderror" required>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alamat Kantor --}}
                        <div class="col-12">
                            <label for="nama_jalan_kantor" class="form-label fw-semibold">Alamat Kantor <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_jalan_kantor" id="nama_jalan_kantor"
                                value="{{ old('nama_jalan_kantor', $profile->nama_jalan_kantor ?? '') }}"
                                class="form-control @error('nama_jalan_kantor') is-invalid @enderror" required>
                            @error('nama_jalan_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kota Kantor --}}
                        <div class="col-md-6">
                            <label for="kota_kantor" class="form-label fw-semibold">Kota <span
                                    class="text-danger">*</span></label>
                            <select name="kota_kantor" id="kota_kantor"
                                class="form-select @error('kota_kantor') is-invalid @enderror" required>
                                <option value="">Pilih Kota</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" data-province-id="{{ $city->province->id }}"
                                        data-province-name="{{ $city->province->name }}"
                                        {{ old('kota_kantor', $profile->kota_kantor ?? '') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kota_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Provinsi Kantor --}}
                        <div class="col-md-6">
                            <label for="provinsi_kantor" class="form-label fw-semibold">Provinsi <span
                                    class="text-danger">*</span></label>
                            <input type="hidden" id="provinsi_kantor" name="provinsi_kantor"
                                value="{{ old('provinsi_kantor', $profile->provinsi_kantor ?? '') }}">
                            <input type="text" id="provinsi_kantor_display" class="form-control"
                                value="{{ old('provinsi_kantor_display', $profile->provinceKantor?->name ?? '') }}"
                                placeholder="Provinsi akan otomatis terisi" readonly>
                            @error('provinsi_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kode Pos Kantor --}}
                        <div class="col-md-4">
                            <label for="kode_pos_kantor" class="form-label fw-semibold">Kode pos <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="kode_pos_kantor" id="kode_pos_kantor"
                                value="{{ old('kode_pos_kantor', $profile->kode_pos_kantor ?? '') }}"
                                class="form-control @error('kode_pos_kantor') is-invalid @enderror" maxlength="5"
                                required>
                            @error('kode_pos_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Negara Kantor --}}
                        <div class="col-md-8">
                            <label for="negara_kantor" class="form-label fw-semibold">Negara <span
                                    class="text-danger">*</span></label>
                            <select name="negara_kantor" id="negara_kantor"
                                class="form-select @error('negara_kantor') is-invalid @enderror" required>
                                <option value="Indonesia"
                                    {{ old('negara_kantor', $profile->negara_kantor ?? 'Indonesia') == 'Indonesia' ? 'selected' : '' }}>
                                    Indonesia</option>
                                <option value="Malaysia"
                                    {{ old('negara_kantor', $profile->negara_kantor ?? '') == 'Malaysia' ? 'selected' : '' }}>
                                    Malaysia</option>
                                <option value="Singapore"
                                    {{ old('negara_kantor', $profile->negara_kantor ?? '') == 'Singapore' ? 'selected' : '' }}>
                                    Singapore</option>
                                <option value="Thailand"
                                    {{ old('negara_kantor', $profile->negara_kantor ?? '') == 'Thailand' ? 'selected' : '' }}>
                                    Thailand</option>
                            </select>
                            @error('negara_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- No Telepon Kantor --}}
                        <div class="col-12">
                            <label for="no_telp_kantor" class="form-label fw-semibold">No. Telepon Kantor <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="no_telp_kantor" id="no_telp_kantor"
                                value="{{ old('no_telp_kantor', $profile->no_telp_kantor ?? '') }}"
                                class="form-control @error('no_telp_kantor') is-invalid @enderror" required>
                            @error('no_telp_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- DOCUMENTS SECTION --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 text-dark">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>
                        Dokumen Pendukung
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $jenisDokumen = [
                            'ktp' => ['label' => 'KTP', 'icon' => 'bi-card-text'],
                            'ijazah' => ['label' => 'Ijazah', 'icon' => 'bi-mortarboard'],
                            'pas_foto' => ['label' => 'Pas Foto', 'icon' => 'bi-camera'],
                        ];
                    @endphp

                    <div class="row g-3">
                        @foreach ($jenisDokumen as $key => $doc)
                            <div class="col-md-4">
                                <div class="upload-card border rounded p-3 h-100 position-relative">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi {{ $doc['icon'] }} text-primary me-2 fs-5"></i>
                                        <label class="form-label fw-semibold mb-0 fw-semibold">{{ $doc['label'] }}
                                            <span class="text-danger">*</span>
                                        </label>
                                    </div>

                                    {{-- File Input --}}
                                    <input type="file" name="documents[{{ $key }}]"
                                        class="form-control @error('documents.' . $key) is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="previewFile(this, '{{ $key }}')">

                                    @error('documents.' . $key)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    {{-- Preview Container --}}
                                    <div id="preview_{{ $key }}" class="preview-container mt-3"
                                        style="display: none;">
                                        <div class="preview-content">
                                            <img id="img_{{ $key }}" class="preview-image"
                                                style="display: none; max-width: 100%; height: 150px; object-fit: cover; border-radius: 5px;">
                                            <div id="pdf_{{ $key }}" class="pdf-preview text-center"
                                                style="display: none;">
                                                <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                <p class="small text-muted mt-2">PDF File</p>
                                            </div>
                                            <div class="file-info mt-2">
                                                <small id="filename_{{ $key }}"
                                                    class="text-muted d-block"></small>
                                                <small id="filesize_{{ $key }}" class="text-muted"></small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                onclick="removePreview('{{ $key }}')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Existing Document --}}
                                    @php
                                        $existingDoc = $documents->where('document_type', $key)->first();
                                    @endphp

                                    @if ($existingDoc)
                                        <div class="existing-doc mt-3 p-2 bg-success bg-opacity-10 rounded">
                                            <small class="text-success d-block">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                File sudah diupload
                                            </small>
                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                <small class="text-muted flex-grow-1">
                                                    {{ $existingDoc->original_name ?? 'File uploaded' }}
                                                    <br>
                                                    <span
                                                        class="badge bg-secondary">{{ $existingDoc->file_size_formatted ?? 'Unknown size' }}</span>
                                                </small>
                                            </div>
                                            <div class="d-flex gap-1 mt-2">
                                                @if ($existingDoc->file_exists)
                                                    <a href="{{ route('asesi.data-pribadi.download', $existingDoc->id) }}"
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                    @if ($existingDoc->isImage())
                                                        <button type="button" class="btn btn-outline-info btn-sm"
                                                            onclick="showImageModal('{{ $existingDoc->file_url }}', '{{ $existingDoc->original_name }}')">
                                                            <i class="bi bi-eye"></i> Lihat
                                                        </button>
                                                    @elseif($existingDoc->mime_type === 'application/pdf')
                                                        <button type="button" class="btn btn-outline-info btn-sm"
                                                            onclick="showPdfModal('{{ $existingDoc->file_url }}', '{{ $existingDoc->original_name }}')">
                                                            <i class="bi bi-file-pdf"></i> Preview PDF
                                                        </button>
                                                    @endif
                                                @else
                                                    <small class="text-warning">
                                                        <i class="bi bi-exclamation-triangle"></i> File tidak ditemukan
                                                    </small>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="deleteDocument({{ $existingDoc->id }}, '{{ $doc['label'] }}')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    <small class="text-muted d-block mt-2">
                                        Format: PDF, JPG, PNG (Max: 5MB)
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Image Preview Modal --}}
                    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="imageModalTitle">Preview Gambar</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="modalPreviewImage" src="" alt="Preview" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PDF Preview Modal --}}
                    <div class="modal fade" id="pdfPreviewModal" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="pdfModalTitle">Preview PDF</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <iframe id="pdfViewer" src="" width="100%" height="700px"
                                        frameborder="0">
                                        <p>Browser Anda tidak mendukung iframe.
                                            <a id="pdfDownloadLink" href="" target="_blank">Klik di sini untuk
                                                membuka PDF</a>
                                        </p>
                                    </iframe>
                                </div>
                                <div class="modal-footer">
                                    <a id="pdfDownloadBtn" href="" target="_blank" class="btn btn-success">
                                        <i class="bi bi-download"></i> Download PDF
                                    </a>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUBMIT BUTTON --}}
            <div class="row">
                <div class="col-12">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-success btn-md px-4">
                            <i class="bi bi-save me-2"></i>
                            Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Delete Document Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus dokumen ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <style>
        /* ==========================================================================
                           FORM CONTROLS & LAYOUT
                           ========================================================================== */
        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .card {
            transition: all 0.3s ease;
        }

        /* ==========================================================================
                           SELECT2 CUSTOM STYLING
                           ========================================================================== */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: calc(1.5em + 0.75rem + 2px);
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            color: #212529;
            line-height: 1.5;
            text-transform: uppercase;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.75rem);
            right: 0.75rem;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            text-transform: uppercase;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #0d6efd;
            color: #fff;
            text-transform: uppercase;
        }

        /* Error state for Select2 */
        .is-invalid+.select2-container--bootstrap-5 .select2-selection {
            border-color: #dc3545;
        }

        .is-invalid+.select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .is-invalid+.select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }

        /* ==========================================================================
                           UPLOAD CARD STYLING
                           ========================================================================== */
        .upload-card {
            border: 2px dashed #dee2e6 !important;
            transition: all 0.3s ease;
            min-height: 220px;
            cursor: pointer;
        }

        .upload-card:hover {
            border-color: #0d6efd !important;
            background-color: #f8f9ff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .upload-card.has-file {
            border-color: #198754 !important;
            border-style: solid !important;
            background-color: #f8fff9;
        }

        .upload-card.border-primary {
            border-width: 2px !important;
            transform: scale(1.02);
        }

        /* ==========================================================================
                           FILE PREVIEW STYLING
                           ========================================================================== */
        .preview-container {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .preview-image {
            max-width: 100%;
            max-height: 120px;
            object-fit: contain;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .pdf-preview {
            text-align: center;
            padding: 20px;
        }

        .file-info {
            font-size: 0.875rem;
            margin-top: 10px;
        }

        .existing-doc {
            border: 1px solid rgba(25, 135, 84, 0.2) !important;
            transition: opacity 0.3s ease;
        }

        /* ==========================================================================
                           TEXT TRANSFORMATION RULES
                           ========================================================================== */
        /* Default uppercase for most text inputs */
        input[type="text"],
        input[type="tel"],
        textarea,
        select {
            text-transform: uppercase;
        }

        /* Keep email inputs lowercase */
        input[type="email"],
        input[type="email"].form-control {
            text-transform: none !important;
        }

        input[type="email"]::placeholder {
            text-transform: none;
        }

        /* Readonly fields remain uppercase */
        input[readonly] {
            text-transform: uppercase;
        }

        /* Placeholder styling */
        .form-control::placeholder,
        .form-select option[value=""]::after {
            text-transform: none;
            opacity: 0.7;
        }

        /* Override for specific fields that should remain normal case */
        .normal-case {
            text-transform: none !important;
        }

        /* ==========================================================================
                           RESPONSIVE DESIGN
                           ========================================================================== */
        @media (max-width: 768px) {
            .upload-card {
                min-height: 200px;
            }

            .preview-image {
                max-height: 100px;
            }
        }

        /* ==========================================================================
                           UTILITY CLASSES
                           ========================================================================== */
        .btn-group-sm .btn {
            font-size: 0.75rem;
            padding: 4px 8px;
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        /**
         * ==========================================================================
         * MAIN INITIALIZATION
         * ==========================================================================
         */
        $(document).ready(function() {
            initializeSelect2();
            initializeCityProvinceHandlers();
            initializeFormHandlers();
            initializeUppercaseInputs();
            initializeDragAndDrop();
        });

        /**
         * ==========================================================================
         * SELECT2 INITIALIZATION
         * ==========================================================================
         */
        function initializeSelect2() {
            const select2Config = {
                theme: 'bootstrap-5',
                allowClear: true,
                width: '100%'
            };

            $('#kota_rumah').select2({
                ...select2Config,
                placeholder: 'Pilih Kota'
            });

            $('#kota_kantor').select2({
                ...select2Config,
                placeholder: 'Pilih Kota'
            });

            $('#kategori_pekerjaan').select2({
                ...select2Config,
                placeholder: 'Pilih Kategori Pekerjaan'
            });

            $('#pendidikan_terakhir').select2({
                ...select2Config,
                placeholder: 'Pilih Pendidikan'
            });
        }

        /**
         * ==========================================================================
         * CITY/PROVINCE HANDLERS
         * ==========================================================================
         */
        function initializeCityProvinceHandlers() {
            // Home address city/province handler
            $('#kota_rumah').on('change', function() {
                updateProvinceField(this, 'rumah');
            });

            // Office address city/province handler
            $('#kota_kantor').on('change', function() {
                updateProvinceField(this, 'kantor');
            });

            // Initialize province fields on page load
            updateProvinceOnLoad('kota_rumah', 'rumah');
            updateProvinceOnLoad('kota_kantor', 'kantor');
        }

        function updateProvinceField(citySelect, type) {
            const selectedOption = citySelect.options[citySelect.selectedIndex];
            const provinceId = selectedOption.getAttribute('data-province-id');
            const provinceName = selectedOption.getAttribute('data-province-name');

            if (provinceId && provinceName) {
                document.getElementById(`provinsi_${type}`).value = provinceId;
                document.getElementById(`provinsi_${type}_display`).value = provinceName.toUpperCase();
            } else {
                document.getElementById(`provinsi_${type}`).value = '';
                document.getElementById(`provinsi_${type}_display`).value = '';
            }
        }

        function updateProvinceOnLoad(cityId, type) {
            const citySelect = document.getElementById(cityId);
            if (citySelect && citySelect.value) {
                updateProvinceField(citySelect, type);
            }
        }

        /**
         * ==========================================================================
         * FORM HANDLERS
         * ==========================================================================
         */
        function initializeFormHandlers() {
            initializeInputFormatters();
            initializeFormSubmissionHandler();

            // Global functions for file handling
            window.previewFile = previewFile;
            window.removePreview = removePreview;
            window.deleteDocument = deleteDocument;
            window.showImageModal = showImageModal;
            window.formatFileSize = formatFileSize;
        }

        function initializeInputFormatters() {
            // NIK formatter - digits only, max 16 characters
            const nikInput = document.getElementById('nik');
            if (nikInput) {
                nikInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 16) {
                        value = value.substring(0, 16);
                    }
                    e.target.value = value;
                });
            }

            // Phone number formatters
            ['no_telp_rumah', 'no_hp', 'no_telp_kantor'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', function(e) {
                        // Allow digits, hyphens, plus, parentheses, and spaces
                        let value = e.target.value.replace(/[^\d\-\+\(\)\s]/g, '');
                        e.target.value = value;
                    });
                }
            });

            // Postal code formatters
            ['kode_pos', 'kode_pos_kantor'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length > 5) {
                            value = value.substring(0, 5);
                        }
                        e.target.value = value;
                    });
                }
            });
        }

        function initializeFormSubmissionHandler() {
            const form = document.querySelector('form[action*="data-pribadi.store"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                        submitBtn.disabled = true;

                        // Re-enable after timeout to prevent permanent lock
                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Data';
                                submitBtn.disabled = false;
                            }
                        }, 10000);
                    }
                });
            }
        }

        /**
         * ==========================================================================
         * FILE HANDLING FUNCTIONS
         * ==========================================================================
         */
        function previewFile(input, type) {
            const file = input.files[0];
            const previewContainer = document.getElementById(`preview_${type}`);
            const uploadCard = input.closest('.upload-card');

            if (!file) return;

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                input.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung! Gunakan PDF, JPG, atau PNG.');
                input.value = '';
                return;
            }

            // Show preview
            previewContainer.style.display = 'block';
            uploadCard.classList.add('has-file');

            // Update file info
            document.getElementById(`filename_${type}`).textContent = file.name;
            document.getElementById(`filesize_${type}`).textContent = formatFileSize(file.size);

            if (file.type.startsWith('image/')) {
                showImagePreview(file, type);
            } else if (file.type === 'application/pdf') {
                showPdfPreview(type);
            }

            // Fade existing document info
            const existingDoc = uploadCard.querySelector('.existing-doc');
            if (existingDoc) {
                existingDoc.style.opacity = '0.5';
                existingDoc.style.transition = 'opacity 0.3s ease';
            }
        }

        function showImagePreview(file, type) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(`img_${type}`);
                img.src = e.target.result;
                img.style.display = 'block';
                document.getElementById(`pdf_${type}`).style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        function showPdfPreview(type) {
            document.getElementById(`img_${type}`).style.display = 'none';
            document.getElementById(`pdf_${type}`).style.display = 'block';
        }

        function removePreview(type) {
            const input = document.querySelector(`input[name="documents[${type}]"]`);
            const previewContainer = document.getElementById(`preview_${type}`);
            const uploadCard = input.closest('.upload-card');

            // Clear input and hide preview
            input.value = '';
            previewContainer.style.display = 'none';
            uploadCard.classList.remove('has-file');

            // Clear preview elements
            const img = document.getElementById(`img_${type}`);
            const pdf = document.getElementById(`pdf_${type}`);

            if (img) {
                img.src = '';
                img.style.display = 'none';
            }
            if (pdf) {
                pdf.style.display = 'none';
            }

            // Restore existing document info
            const existingDoc = uploadCard.querySelector('.existing-doc');
            if (existingDoc) {
                existingDoc.style.opacity = '1';
                existingDoc.style.transition = 'opacity 0.3s ease';
            }
        }

        function deleteDocument(documentId, docLabel) {
            if (!confirm(`Apakah Anda yakin ingin menghapus dokumen ${docLabel || 'ini'}?`)) {
                return;
            }

            // Create and submit delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/asesi/data-pribadi/document/${documentId}/delete`;
            form.style.display = 'none';

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;

            if (csrfToken) {
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = csrfToken;
                form.appendChild(csrfField);
            }

            // Add DELETE method
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }

        function showImageModal(imageUrl, imageName) {
            const modal = document.getElementById('imagePreviewModal');
            if (!modal) return;

            const modalInstance = new bootstrap.Modal(modal);
            const modalImage = document.getElementById('modalPreviewImage');
            const modalTitle = document.getElementById('imageModalTitle');

            if (modalImage) modalImage.src = imageUrl;
            if (modalTitle) modalTitle.textContent = imageName || 'Preview Gambar';

            modalInstance.show();
        }

        function showPdfModal(pdfUrl, fileName) {
            const modal = document.getElementById('pdfPreviewModal');
            if (!modal) return;

            const modalInstance = new bootstrap.Modal(modal);
            const pdfViewer = document.getElementById('pdfViewer');
            const modalTitle = document.getElementById('pdfModalTitle');
            const downloadBtn = document.getElementById('pdfDownloadBtn');
            const downloadLink = document.getElementById('pdfDownloadLink');

            if (pdfViewer) pdfViewer.src = pdfUrl;
            if (modalTitle) modalTitle.textContent = fileName || 'Preview PDF';
            if (downloadBtn) downloadBtn.href = pdfUrl;
            if (downloadLink) downloadLink.href = pdfUrl;

            modalInstance.show();

            // Clear iframe src when modal closes to stop loading
            modal.addEventListener('hidden.bs.modal', function() {
                if (pdfViewer) pdfViewer.src = 'about:blank';
            }, {
                once: true
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        /**
         * ==========================================================================
         * DRAG AND DROP FUNCTIONALITY
         * ==========================================================================
         */
        function initializeDragAndDrop() {
            const uploadCards = document.querySelectorAll('.upload-card');

            uploadCards.forEach(card => {
                const input = card.querySelector('input[type="file"]');
                if (!input) return;

                // Make card clickable - simple approach
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('button, a, .btn, input[type="file"]')) {
                        e.preventDefault();
                        e.stopPropagation();
                        input.click();
                    }
                });

                // Drag and drop handlers
                card.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    card.classList.add('border-primary', 'bg-light');
                });

                card.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    card.classList.remove('border-primary', 'bg-light');
                });

                card.addEventListener('drop', function(e) {
                    e.preventDefault();
                    card.classList.remove('border-primary', 'bg-light');

                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        input.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });
            });
        }

        /**
         * ==========================================================================
         * TEXT CASE TRANSFORMATION
         * ==========================================================================
         */
        function initializeUppercaseInputs() {
            // Define inputs that should be uppercase
            const uppercaseSelectors = [
                'input[name="nama_lengkap"]',
                'input[name="nik"]',
                'input[name="tempat_lahir"]',
                'input[name="kebangsaan"]',
                'input[name="no_telp_rumah"]',
                'input[name="no_hp"]',
                'input[name="nama_sekolah_terakhir"]',
                'input[name="nama_tempat_kerja"]',
                'input[name="jabatan"]',
                'input[name="nama_jalan_kantor"]',
                'input[name="no_telp_kantor"]',
                'textarea[name="alamat_rumah"]'
            ];

            uppercaseSelectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(element => {
                    setupUppercaseInput(element);
                });
            });

            // Special handling for email (lowercase)
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput) {
                setupLowercaseInput(emailInput);
            }
        }

        function setupUppercaseInput(element) {
            // Convert existing value
            if (element.value && element.value.trim() !== '') {
                element.value = element.value.toUpperCase();
            }

            // Handle input events
            element.addEventListener('input', function() {
                const cursorPosition = this.selectionStart;
                const oldValue = this.value;
                const newValue = oldValue.toUpperCase();

                if (oldValue !== newValue) {
                    this.value = newValue;
                    this.setSelectionRange(cursorPosition, cursorPosition);
                }
            });

            // Handle paste events
            element.addEventListener('paste', function() {
                setTimeout(() => {
                    const cursorPosition = this.selectionStart;
                    this.value = this.value.toUpperCase();
                    this.setSelectionRange(cursorPosition, cursorPosition);
                }, 1);
            });
        }

        function setupLowercaseInput(element) {
            // Handle input events
            element.addEventListener('input', function() {
                const cursorPosition = this.selectionStart;
                const oldValue = this.value;
                const newValue = oldValue.toLowerCase();

                if (oldValue !== newValue) {
                    this.value = newValue;
                    this.setSelectionRange(cursorPosition, cursorPosition);
                }
            });

            // Handle form submission
            const form = element.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    element.value = element.value.toLowerCase();
                });
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const nikInput = document.getElementById('nik');
            const nikWarning = document.getElementById('nikWarning');

            nikInput.addEventListener('input', function() {
                // Hapus karakter non-digit
                this.value = this.value.replace(/\D/g, '');

                // Tampilkan warning jika panjangnya bukan 16
                if (this.value.length > 0 && this.value.length !== 16) {
                    nikWarning.style.display = 'block';
                } else {
                    nikWarning.style.display = 'none';
                }
            });

            // Validasi sebelum submit
            nikInput.form.addEventListener('submit', function(e) {
                if (nikInput.value.length !== 16) {
                    e.preventDefault();
                    nikWarning.style.display = 'block';
                    nikInput.focus();
                }
            });
        });
    </script>
@endpush
