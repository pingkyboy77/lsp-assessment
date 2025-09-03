@extends('layouts.admin')

@section('title', 'Edit APL 01 - ' . $apl->nomor_apl_01)

@section('content')
    @if (session('success'))
        <div class="alert-success-custom">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="main-card">
        <!-- Header Section -->
        <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Edit APL 01</h2>
                <p class="text-muted mb-0">{{ $apl->nomor_apl_01 }} - {{ $apl->nama_lengkap }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.apl01.show', $apl) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                @if (in_array($apl->status, ['submitted', 'approved', 'rejected', 'review', 'reviewed']))
                    <button class="btn btn-warning" onclick="reopenApl({{ $apl->id }})">
                        <i class="bi bi-unlock"></i> Reopen
                    </button>
                @endif
                <button type="button" class="btn btn-success" onclick="document.getElementById('aplForm').submit()">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- Status Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 bg-{{ $apl->statusColor ?? 'secondary' }} bg-opacity-10">
                    <div class="card-body text-center">
                        <span class="badge bg-{{ $apl->statusColor ?? 'secondary' }} fs-6 px-3 py-2">
                            {{ $apl->statusText ?? ucfirst($apl->status) }}
                        </span>
                        <div class="mt-2">
                            <small class="text-muted">
                                Status saat ini -
                                @if ($apl->status === 'open')
                                    Form dapat diedit oleh asesi
                                @elseif($apl->status === 'draft')
                                    Belum disubmit oleh asesi
                                @elseif($apl->status === 'submitted')
                                    Menunggu review
                                @elseif($apl->status === 'approved')
                                    Telah disetujui
                                @elseif($apl->status === 'rejected')
                                    Ditolak - perlu perbaikan
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form id="aplForm" method="POST" action="{{ route('admin.apl01.update', $apl) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Section 1: Data Pemohon -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">01</span>
                        Rincian Data Pemohon Sertifikat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                name="nama_lengkap" value="{{ old('nama_lengkap', $apl->nama_lengkap) }}" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik"
                                value="{{ old('nik', $apl->nik) }}" maxlength="16" required>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                name="tempat_lahir" value="{{ old('tempat_lahir', $apl->tempat_lahir) }}" required>
                            @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $apl->tanggal_lahir?->format('Y-m-d')) }}" required>
                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin"
                                required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L"
                                    {{ old('jenis_kelamin', $apl->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P"
                                    {{ old('jenis_kelamin', $apl->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kebangsaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kebangsaan') is-invalid @enderror"
                                name="kebangsaan" value="{{ old('kebangsaan', $apl->kebangsaan) }}" required>
                            @error('kebangsaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('no_hp') is-invalid @enderror" name="no_hp"
                                value="{{ old('no_hp', $apl->no_hp) }}" required>
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email', $apl->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon Rumah</label>
                            <input type="tel" class="form-control @error('no_telp_rumah') is-invalid @enderror"
                                name="no_telp_rumah" value="{{ old('no_telp_rumah', $apl->no_telp_rumah) }}">
                            @error('no_telp_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                            <select class="form-select @error('pendidikan_terakhir') error @enderror"
                            name="pendidikan_terakhir" required>
                            <option value="">Pilih Pendidikan</option>
                            @foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor'] as $edu)
                                <option value="{{ $edu }}"
                                    {{ old('pendidikan_terakhir', $apl->pendidikan_terakhir) == $edu ? 'selected' : '' }}>
                                    {{ $edu }}
                                </option>
                            @endforeach
                        </select>
                        @error('pendidikan_terakhir')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Sekolah/Institusi</label>
                            <input type="text"
                                class="form-control @error('nama_sekolah_terakhir') is-invalid @enderror"
                                name="nama_sekolah_terakhir"
                                value="{{ old('nama_sekolah_terakhir', $apl->nama_sekolah_terakhir) }}">
                            @error('nama_sekolah_terakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Alamat Rumah -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">02</span>
                        Data Alamat Rumah
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alamat_rumah') is-invalid @enderror" name="alamat_rumah" rows="3"
                                required>{{ old('alamat_rumah', $apl->alamat_rumah) }}</textarea>
                            @error('alamat_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                            <select class="form-select @error('provinsi_rumah') is-invalid @enderror"
                                name="provinsi_rumah" id="provinsi_rumah" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}"
                                        {{ old('provinsi_rumah', $apl->provinsi_rumah) == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('provinsi_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                            <select class="form-select @error('kota_rumah') is-invalid @enderror" name="kota_rumah"
                                id="kota_rumah" required>
                                <option value="">Pilih Kota/Kabupaten</option>
                                @if ($apl->kotaRumah)
                                    <option value="{{ $apl->kota_rumah }}" selected>{{ $apl->kotaRumah->name }}</option>
                                @endif
                            </select>
                            @error('kota_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" class="form-control @error('kode_pos') is-invalid @enderror"
                                name="kode_pos" value="{{ old('kode_pos', $apl->kode_pos) }}" maxlength="5">
                            @error('kode_pos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Data Pekerjaan -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">03</span>
                        Data Pekerjaan Sekarang
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Tempat Kerja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_tempat_kerja') is-invalid @enderror"
                                name="nama_tempat_kerja" value="{{ old('nama_tempat_kerja', $apl->nama_tempat_kerja) }}"
                                required>
                            @error('nama_tempat_kerja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                name="jabatan" value="{{ old('jabatan', $apl->jabatan) }}" required>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori Pekerjaan <span class="text-danger">*</span></label>
                            <select class="form-select @error('kategori_pekerjaan') is-invalid @enderror"
                                name="kategori_pekerjaan" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Karyawan"
                                    {{ old('kategori_pekerjaan', $apl->kategori_pekerjaan) == 'Karyawan' ? 'selected' : '' }}>
                                    Karyawan</option>
                                <option value="Wiraswasta"
                                    {{ old('kategori_pekerjaan', $apl->kategori_pekerjaan) == 'Wiraswasta' ? 'selected' : '' }}>
                                    Wiraswasta</option>
                                <option value="PNS"
                                    {{ old('kategori_pekerjaan', $apl->kategori_pekerjaan) == 'PNS' ? 'selected' : '' }}>
                                    PNS</option>
                                <option value="TNI/POLRI"
                                    {{ old('kategori_pekerjaan', $apl->kategori_pekerjaan) == 'TNI/POLRI' ? 'selected' : '' }}>
                                    TNI/POLRI</option>
                                <option value="Lainnya"
                                    {{ old('kategori_pekerjaan', $apl->kategori_pekerjaan) == 'Lainnya' ? 'selected' : '' }}>
                                    Lainnya</option>
                            </select>
                            @error('kategori_pekerjaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="isiAlamatKantor"
                            {{ $apl->nama_jalan_kantor ? 'checked' : '' }}>
                        <label class="form-check-label" for="isiAlamatKantor">
                            Isi alamat kantor (opsional)
                        </label>
                    </div>

                    <div id="alamatKantorSection" class="mt-3"
                        style="{{ $apl->nama_jalan_kantor ? 'display: block;' : 'display: none;' }}">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Alamat Kantor</label>
                                <textarea class="form-control @error('nama_jalan_kantor') is-invalid @enderror" name="nama_jalan_kantor"
                                    rows="3">{{ old('nama_jalan_kantor', $apl->nama_jalan_kantor) }}</textarea>
                                @error('nama_jalan_kantor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Provinsi Kantor</label>
                                <select class="form-select @error('provinsi_kantor') is-invalid @enderror"
                                    name="provinsi_kantor" id="provinsi_kantor">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}"
                                            {{ old('provinsi_kantor', $apl->provinsi_kantor) == $province->id ? 'selected' : '' }}>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provinsi_kantor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kota Kantor</label>
                                <select class="form-select @error('kota_kantor') is-invalid @enderror" name="kota_kantor"
                                    id="kota_kantor">
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    @if ($apl->kotaKantor)
                                        <option value="{{ $apl->kota_kantor }}" selected>{{ $apl->kotaKantor->name }}
                                        </option>
                                    @endif
                                </select>
                                @error('kota_kantor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Pos Kantor</label>
                                <input type="text" class="form-control @error('kode_pos_kantor') is-invalid @enderror"
                                    name="kode_pos_kantor" value="{{ old('kode_pos_kantor', $apl->kode_pos_kantor) }}"
                                    maxlength="5">
                                @error('kode_pos_kantor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon Kantor</label>
                                <input type="tel" class="form-control @error('no_telp_kantor') is-invalid @enderror"
                                    name="no_telp_kantor" value="{{ old('no_telp_kantor', $apl->no_telp_kantor) }}">
                                @error('no_telp_kantor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('admin.apl01.show', $apl) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Reopen Confirmation Modal -->
    <div class="modal fade" id="reopenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reopen APL 01</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membuka kembali APL 01 ini?</p>
                    <p class="text-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Setelah dibuka kembali, status akan berubah menjadi "Open" dan asesi dapat mengedit form ini
                        kembali.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional):</label>
                        <textarea class="form-control" id="reopenNotes" rows="3"
                            placeholder="Berikan catatan mengapa form dibuka kembali..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" onclick="confirmReopen()">
                        <i class="bi bi-unlock"></i> Reopen
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-header {
            border-bottom: 2px solid #dee2e6;
        }

        .badge.fs-6 {
            font-size: 0.9rem !important;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .text-danger {
            font-size: 0.8rem;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
        }

        #alamatKantorSection {
            border-left: 3px solid #007bff;
            padding-left: 1rem;
            background-color: #f8f9fa;
            border-radius: 0 8px 8px 0;
            padding: 1rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Toggle alamat kantor section
        document.getElementById('isiAlamatKantor').addEventListener('change', function() {
            const section = document.getElementById('alamatKantorSection');
            if (this.checked) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
                // Clear form values when hidden
                section.querySelectorAll('input, select, textarea').forEach(field => {
                    field.value = '';
                });
            }
        });

        // Province and City handling
        document.getElementById('provinsi_rumah').addEventListener('change', function() {
            loadCities(this.value, 'kota_rumah');
        });

        document.getElementById('provinsi_kantor').addEventListener('change', function() {
            loadCities(this.value, 'kota_kantor');
        });

        async function loadCities(provinceId, targetSelectId) {
            const citySelect = document.getElementById(targetSelectId);

            // Clear existing options
            citySelect.innerHTML = '<option value="">Memuat kota...</option>';

            if (!provinceId) {
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                return;
            }

            try {
                const response = await fetch(`{{ route('asesi.regions.cities', ':provinceId') }}`.replace(
                    ':provinceId', provinceId));
                const cities = await response.json();

                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.id;
                    option.textContent = city.name;
                    citySelect.appendChild(option);
                });

            } catch (error) {
                console.error('Error loading cities:', error);
                citySelect.innerHTML = '<option value="">Error loading cities</option>';
            }
        }

        // Reopen functionality
        let currentAplId = {{ $apl->id }};

        function reopenApl(aplId) {
            currentAplId = aplId;
            const modal = new bootstrap.Modal(document.getElementById('reopenModal'));
            modal.show();
        }

        async function confirmReopen() {
            const notes = document.getElementById('reopenNotes').value;
            const modal = bootstrap.Modal.getInstance(document.getElementById('reopenModal'));

            try {
                const response = await fetch(`/admin/apl01/${currentAplId}/reopen`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        notes: notes
                    })
                });

                const result = await response.json();

                if (result.success) {
                    modal.hide();
                    showToast('success', result.message);

                    // Reload page to show updated status
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Failed to reopen APL');
                }
            } catch (error) {
                console.error('Reopen error:', error);
                showToast('error', 'Gagal membuka kembali APL: ' + error.message);
            }
        }

        function showToast(type, message) {
            // Create toast element
            const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

            // Add to toast container
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '1056';
                document.body.appendChild(container);
            }

            container.insertAdjacentHTML('beforeend', toastHtml);

            // Show toast
            const toastElement = container.lastElementChild;
            const toast = new bootstrap.Toast(toastElement, {
                delay: 5000
            });
            toast.show();

            // Remove from DOM after hide
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        // Form validation
        document.getElementById('aplForm').addEventListener('submit', function(e) {
            // Basic validation can be added here if needed
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                showToast('error', 'Mohon lengkapi semua field yang wajib diisi');
                return;
            }
        });
@endpush