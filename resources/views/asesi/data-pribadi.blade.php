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
                                {{-- <div class="feature-icon bg-primary bg-gradient text-white rounded-3 me-3">
                                    <i class="bi bi-person-fill"></i>
                                </div> --}}
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
                            <label for="nik" class="form-label fw-semibold">Nomor Identitas (KTP / Paspor) <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nik" id="nik"
                                value="{{ old('nik', $profile->nik ?? '') }}"
                                class="form-control @error('nik') is-invalid @enderror" maxlength="16" required>
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
                                value="{{ old('email', $profile->email ?? '') }}"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="pendidikan_terakhir" class="form-label fw-semibold">Pendidikan Terakhir <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="pendidikan_terakhir" id="pendidikan_terakhir"
                                value="{{ old('pendidikan_terakhir', $profile->pendidikan_terakhir ?? '') }}"
                                class="form-control @error('pendidikan_terakhir') is-invalid @enderror" required>
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
                                <option value="Jakarta"
                                    {{ old('kota_rumah', $profile->kota_rumah ?? '') == 'Jakarta' ? 'selected' : '' }}>
                                    Jakarta
                                </option>
                                <option value="Surabaya"
                                    {{ old('kota_rumah', $profile->kota_rumah ?? '') == 'Surabaya' ? 'selected' : '' }}>
                                    Surabaya</option>
                                <option value="Bandung"
                                    {{ old('kota_rumah', $profile->kota_rumah ?? '') == 'Bandung' ? 'selected' : '' }}>
                                    Bandung
                                </option>
                                <option value="Medan"
                                    {{ old('kota_rumah', $profile->kota_rumah ?? '') == 'Medan' ? 'selected' : '' }}>Medan
                                </option>
                                <option value="Semarang"
                                    {{ old('kota_rumah', $profile->kota_rumah ?? '') == 'Semarang' ? 'selected' : '' }}>
                                    Semarang</option>
                            </select>
                            @error('kota_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Provinsi Rumah --}}
                        <div class="col-md-6">
                            <label for="provinsi_rumah" class="form-label fw-semibold">Provinsi <span
                                    class="text-danger">*</span></label>
                            <select name="provinsi_rumah" id="provinsi_rumah"
                                class="form-select @error('provinsi_rumah') is-invalid @enderror" required>
                                <option value="">Provinsi</option>
                                <option value="DKI Jakarta"
                                    {{ old('provinsi_rumah', $profile->provinsi_rumah ?? '') == 'DKI Jakarta' ? 'selected' : '' }}>
                                    DKI Jakarta</option>
                                <option value="Jawa Barat"
                                    {{ old('provinsi_rumah', $profile->provinsi_rumah ?? '') == 'Jawa Barat' ? 'selected' : '' }}>
                                    Jawa Barat</option>
                                <option value="Jawa Timur"
                                    {{ old('provinsi_rumah', $profile->provinsi_rumah ?? '') == 'Jawa Timur' ? 'selected' : '' }}>
                                    Jawa Timur</option>
                                <option value="Jawa Tengah"
                                    {{ old('provinsi_rumah', $profile->provinsi_rumah ?? '') == 'Jawa Tengah' ? 'selected' : '' }}>
                                    Jawa Tengah</option>
                                <option value="Sumatera Utara"
                                    {{ old('provinsi_rumah', $profile->provinsi_rumah ?? '') == 'Sumatera Utara' ? 'selected' : '' }}>
                                    Sumatera Utara</option>
                            </select>
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
                                <option value="Pendidikan"
                                    {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == 'Pendidikan' ? 'selected' : '' }}>
                                    Pendidikan</option>
                                <option value="Teknologi Informasi"
                                    {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == 'Teknologi Informasi' ? 'selected' : '' }}>
                                    Teknologi Informasi</option>
                                <option value="Kesehatan"
                                    {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == 'Kesehatan' ? 'selected' : '' }}>
                                    Kesehatan</option>
                                <option value="Keuangan"
                                    {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == 'Keuangan' ? 'selected' : '' }}>
                                    Keuangan</option>
                                <option value="Pemerintahan"
                                    {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == 'Pemerintahan' ? 'selected' : '' }}>
                                    Pemerintahan</option>
                                <option value="Swasta"
                                    {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == 'Swasta' ? 'selected' : '' }}>
                                    Swasta</option>
                                <option value="BUMN"
                                    {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == 'BUMN' ? 'selected' : '' }}>
                                    BUMN</option>
                                <option value="Wiraswasta"
                                    {{ old('kategori_pekerjaan', $profile->kategori_pekerjaan ?? '') == 'Wiraswasta' ? 'selected' : '' }}>
                                    Wiraswasta</option>
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
                        {{-- Nama Jalan --}}
                        <div class="col-12">
                            <label for="nama_jalan_kantor" class="form-label fw-semibold">Alamat Kntor <span
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
                            <input type="text" name="kota_kantor" id="kota_kantor"
                                value="{{ old('kota_kantor', $profile->kota_kantor ?? '') }}"
                                class="form-control @error('kota_kantor') is-invalid @enderror" required>
                            @error('kota_kantor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Provinsi Kantor --}}
                        <div class="col-md-6">
                            <label for="provinsi_kantor" class="form-label fw-semibold">Provinsi <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="provinsi_kantor" id="provinsi_kantor"
                                value="{{ old('provinsi_kantor', $profile->provinsi_kantor ?? '') }}"
                                class="form-control @error('provinsi_kantor') is-invalid @enderror" required>
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

            {{-- DOCUMENTS SECTION WITH PREVIEW --}}
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
                                        <label class="form-label fw-semibold mb-0 fw-semibold">{{ $doc['label'] }} <span
                                                class="text-danger">*</span></label>
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
                                                style="display: none;">
                                            <div id="pdf_{{ $key }}" class="pdf-preview"
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
                                        $existingDoc = $documents->where('jenis_dokumen', $key)->first();
                                    @endphp

                                    @if ($existingDoc)
                                        <div class="existing-doc mt-3 p-2 bg-success bg-opacity-10 rounded">
                                            <small class="text-success d-block">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                File sudah diupload
                                            </small>
                                            <div class="d-flex gap-1 mt-2">
                                                <a href="{{ route('asesi.data-pribadi.download', $existingDoc->id) }}"
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="deleteDocument({{ $existingDoc->id }})">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    <small class="text-muted d-block mt-2">
                                        Format: PDF, JPG, PNG (Max: 2MB)
                                    </small>
                                </div>
                            </div>
                        @endforeach
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
    <style>
        .feature-icon {
            width: 4rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            /* transform: translateY(-2px); */
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .upload-card {
            border: 2px dashed #dee2e6 !important;
            transition: all 0.3s ease;
            min-height: 220px;
        }

        .upload-card:hover {
            border-color: #0d6efd !important;
            background-color: #f8f9ff;
        }

        .upload-card.has-file {
            border-color: #198754 !important;
            border-style: solid !important;
            background-color: #f8fff9;
        }

        .preview-container {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
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
        }

        .btn-group-sm .btn {
            font-size: 0.75rem;
            padding: 4px 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .upload-card {
                min-height: 200px;
            }

            .preview-image {
                max-height: 100px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // File Preview Function
        function previewFile(input, type) {
            const file = input.files[0];
            const previewContainer = document.getElementById(`preview_${type}`);
            const uploadCard = input.closest('.upload-card');

            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
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

                // Show preview container
                previewContainer.style.display = 'block';
                uploadCard.classList.add('has-file');

                // Update file info
                document.getElementById(`filename_${type}`).textContent = file.name;
                document.getElementById(`filesize_${type}`).textContent = formatFileSize(file.size);

                if (file.type.startsWith('image/')) {
                    // Show image preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.getElementById(`img_${type}`);
                        img.src = e.target.result;
                        img.style.display = 'block';
                        document.getElementById(`pdf_${type}`).style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    // Show PDF preview
                    document.getElementById(`img_${type}`).style.display = 'none';
                    document.getElementById(`pdf_${type}`).style.display = 'block';
                }
            }
        }

        // Remove Preview Function
        function removePreview(type) {
            const input = document.querySelector(`input[name="documents[${type}]"]`);
            const previewContainer = document.getElementById(`preview_${type}`);
            const uploadCard = input.closest('.upload-card');

            input.value = '';
            previewContainer.style.display = 'none';
            uploadCard.classList.remove('has-file');

            // Clear preview content
            document.getElementById(`img_${type}`).src = '';
            document.getElementById(`img_${type}`).style.display = 'none';
            document.getElementById(`pdf_${type}`).style.display = 'none';
        }

        // Format File Size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        // Delete Document Function
        function deleteDocument(documentId) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/data-pribadi/document/${documentId}`;

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Auto format NIK input
        document.getElementById('nik').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 16) {
                value = value.substring(0, 16);
            }
            e.target.value = value;
        });

        // Auto format phone numbers
        ['no_telp_rumah', 'no_hp', 'no_telp_kantor'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^\d\-\+\(\)\s]/g, '');
                    e.target.value = value;
                });
            }
        });

        // Auto format postal code
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

        // Form submission loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
            submitBtn.disabled = true;
        });
    </script>
@endpush
