@extends('layouts.admin')

@section('title', 'Edit Profil Pengguna')

@section('content')

    <!-- Main Content Card -->
    <div class="main-card">
        <!-- Card Header -->
        <div class="card-header-custom">
            @if(session('error'))
                <div class="alert-danger-custom">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profil Pengguna
                    </h5>
                    <p class="mb-0 text-muted">{{ $profile->nama_lengkap }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.monitoring-profile.show', $profile->id) }}" class="btn btn-info-custom me-2">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                    <a href="{{ route('admin.monitoring-profile.index') }}" class="btn btn-secondary-custom">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <div class="content-container m-3">
            <form action="{{ route('admin.monitoring-profile.update', $profile->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <!-- Data Personal -->
                    <div class="col-lg-6">
                        <div class="form-card">
                            <div class="form-card-header">
                                <h6 class="mb-0"><i class="bi bi-person-fill me-2"></i>Data Personal</h6>
                            </div>
                            <div class="form-card-body">
                                <div class="form-group mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                           id="nama_lengkap" name="nama_lengkap" 
                                           value="{{ old('nama_lengkap', $profile->nama_lengkap) }}" required>
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control @error('nik') is-invalid @enderror" 
                                           id="nik" name="nik" value="{{ old('nik', $profile->nik) }}">
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                                           id="tempat_lahir" name="tempat_lahir" 
                                           value="{{ old('tempat_lahir', $profile->tempat_lahir) }}">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                           id="tanggal_lahir" name="tanggal_lahir" 
                                           value="{{ old('tanggal_lahir', $profile->tanggal_lahir) }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                                            id="jenis_kelamin" name="jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin', $profile->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $profile->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="kebangsaan" class="form-label">Kebangsaan</label>
                                    <input type="text" class="form-control @error('kebangsaan') is-invalid @enderror" 
                                           id="kebangsaan" name="kebangsaan" 
                                           value="{{ old('kebangsaan', $profile->kebangsaan) }}">
                                    @error('kebangsaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                                           id="no_hp" name="no_hp" 
                                           value="{{ old('no_hp', $profile->no_hp) }}">
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email', $profile->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Rumah -->
                    <div class="col-lg-6">
                        <div class="form-card">
                            <div class="form-card-header">
                                <h6 class="mb-0"><i class="bi bi-house-fill me-2"></i>Alamat Rumah</h6>
                            </div>
                            <div class="form-card-body">
                                <div class="form-group mb-3">
                                    <label for="alamat_rumah" class="form-label">Alamat</label>
                                    <textarea class="form-control @error('alamat_rumah') is-invalid @enderror" 
                                              id="alamat_rumah" name="alamat_rumah" rows="3">{{ old('alamat_rumah', $profile->alamat_rumah) }}</textarea>
                                    @error('alamat_rumah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="kota_rumah" class="form-label">Kota</label>
                                    <input type="text" class="form-control @error('kota_rumah') is-invalid @enderror" 
                                           id="kota_rumah" name="kota_rumah" 
                                           value="{{ old('kota_rumah', $profile->kota_rumah) }}">
                                    @error('kota_rumah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="provinsi_rumah" class="form-label">Provinsi</label>
                                    <input type="text" class="form-control @error('provinsi_rumah') is-invalid @enderror" 
                                           id="provinsi_rumah" name="provinsi_rumah" 
                                           value="{{ old('provinsi_rumah', $profile->provinsi_rumah) }}">
                                    @error('provinsi_rumah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="kode_pos" class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" 
                                           id="kode_pos" name="kode_pos" 
                                           value="{{ old('kode_pos', $profile->kode_pos) }}">
                                    @error('kode_pos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label for="no_telp_rumah" class="form-label">Telp. Rumah</label>
                                    <input type="text" class="form-control @error('no_telp_rumah') is-invalid @enderror" 
                                           id="no_telp_rumah" name="no_telp_rumah" 
                                           value="{{ old('no_telp_rumah', $profile->no_telp_rumah) }}">
                                    @error('no_telp_rumah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pendidikan -->
                    <div class="col-lg-6">
                        <div class="form-card">
                            <div class="form-card-header">
                                <h6 class="mb-0"><i class="bi bi-mortarboard-fill me-2"></i>Pendidikan</h6>
                            </div>
                            <div class="form-card-body">
                                <div class="form-group mb-3">
                                    <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
                                    <input type="text" class="form-control @error('pendidikan_terakhir') is-invalid @enderror" 
                                           id="pendidikan_terakhir" name="pendidikan_terakhir" 
                                           value="{{ old('pendidikan_terakhir', $profile->pendidikan_terakhir) }}">
                                    @error('pendidikan_terakhir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label for="nama_sekolah_terakhir" class="form-label">Nama Sekolah/Universitas</label>
                                    <input type="text" class="form-control @error('nama_sekolah_terakhir') is-invalid @enderror" 
                                           id="nama_sekolah_terakhir" name="nama_sekolah_terakhir" 
                                           value="{{ old('nama_sekolah_terakhir', $profile->nama_sekolah_terakhir) }}">
                                    @error('nama_sekolah_terakhir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pekerjaan -->
                    <div class="col-lg-6">
                        <div class="form-card">
                            <div class="form-card-header">
                                <h6 class="mb-0"><i class="bi bi-briefcase-fill me-2"></i>Pekerjaan</h6>
                            </div>
                            <div class="form-card-body">
                                <div class="form-group mb-3">
                                    <label for="jabatan" class="form-label">Jabatan</label>
                                    <input type="text" class="form-control @error('jabatan') is-invalid @enderror" 
                                           id="jabatan" name="jabatan" 
                                           value="{{ old('jabatan', $profile->jabatan) }}">
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="nama_tempat_kerja" class="form-label">Nama Tempat Kerja</label>
                                    <input type="text" class="form-control @error('nama_tempat_kerja') is-invalid @enderror" 
                                           id="nama_tempat_kerja" name="nama_tempat_kerja" 
                                           value="{{ old('nama_tempat_kerja', $profile->nama_tempat_kerja) }}">
                                    @error('nama_tempat_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label for="kategori_pekerjaan" class="form-label">Kategori Pekerjaan</label>
                                    <input type="text" class="form-control @error('kategori_pekerjaan') is-invalid @enderror" 
                                           id="kategori_pekerjaan" name="kategori_pekerjaan" 
                                           value="{{ old('kategori_pekerjaan', $profile->kategori_pekerjaan) }}">
                                    @error('kategori_pekerjaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Kantor -->
                    <div class="col-lg-12">
                        <div class="form-card">
                            <div class="form-card-header">
                                <h6 class="mb-0"><i class="bi bi-building me-2"></i>Alamat Kantor</h6>
                            </div>
                            <div class="form-card-body">
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="nama_jalan_kantor" class="form-label">Nama Jalan</label>
                                            <input type="text" class="form-control @error('nama_jalan_kantor') is-invalid @enderror" 
                                                   id="nama_jalan_kantor" name="nama_jalan_kantor" 
                                                   value="{{ old('nama_jalan_kantor', $profile->nama_jalan_kantor) }}">
                                            @error('nama_jalan_kantor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="kota_kantor" class="form-label">Kota</label>
                                            <input type="text" class="form-control @error('kota_kantor') is-invalid @enderror" 
                                                   id="kota_kantor" name="kota_kantor" 
                                                   value="{{ old('kota_kantor', $profile->kota_kantor) }}">
                                            @error('kota_kantor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-0">
                                            <label for="provinsi_kantor" class="form-label">Provinsi</label>
                                            <input type="text" class="form-control @error('provinsi_kantor') is-invalid @enderror" 
                                                   id="provinsi_kantor" name="provinsi_kantor" 
                                                   value="{{ old('provinsi_kantor', $profile->provinsi_kantor) }}">
                                            @error('provinsi_kantor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="kode_pos_kantor" class="form-label">Kode Pos</label>
                                            <input type="text" class="form-control @error('kode_pos_kantor') is-invalid @enderror" 
                                                   id="kode_pos_kantor" name="kode_pos_kantor" 
                                                   value="{{ old('kode_pos_kantor', $profile->kode_pos_kantor) }}">
                                            @error('kode_pos_kantor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="negara_kantor" class="form-label">Negara</label>
                                            <input type="text" class="form-control @error('negara_kantor') is-invalid @enderror" 
                                                   id="negara_kantor" name="negara_kantor" 
                                                   value="{{ old('negara_kantor', $profile->negara_kantor) }}">
                                            @error('negara_kantor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-0">
                                            <label for="no_telp_kantor" class="form-label">Telp. Kantor</label>
                                            <input type="text" class="form-control @error('no_telp_kantor') is-invalid @enderror" 
                                                   id="no_telp_kantor" name="no_telp_kantor" 
                                                   value="{{ old('no_telp_kantor', $profile->no_telp_kantor) }}">
                                            @error('no_telp_kantor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen yang Sudah Ada -->
                    @if ($documents->count() > 0)
                    <div class="document-table">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Jenis Dokumen</th>
                                    <th>Tanggal Upload</th>
                                    <th>Ukuran File</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $document->jenis_dokumen)) }}</td>
                                        <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if ($document->file_exists)
                                                <span class="file-size">{{ $document->file_size_kb }} KB</span>
                                            @else
                                                <span class="file-size">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->file_exists)
                                                <span class="file-status exists">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="file-status missing">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                    Tidak ditemukan
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->file_exists)
                                                <a href="{{ $document->file_url }}" target="_blank"
                                                    class="btn-doc-action btn-view-doc">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                                <a href="{{ $document->file_url }}" download
                                                    class="btn-doc-action btn-download-doc">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            @else
                                                <span class="text-danger small">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                    File tidak tersedia
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="no-documents">
                        <i class="bi bi-file-earmark-upload"></i>
                        <p><strong>Belum ada dokumen yang diupload</strong></p>
                        <small>Pengguna belum mengupload dokumen apapun</small>
                    </div>
                @endif
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-actions text-center">
                            <button type="submit" class="btn btn-primary-custom me-2 text-light">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.monitoring-profile.show', $profile->id) }}" class="btn btn-secondary-custom">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
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
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert-danger-custom').fadeOut();
    }, 5000);
});
</script>
@endpush