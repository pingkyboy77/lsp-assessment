@extends('layouts.admin')

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
    <div class="apl01-container">
        <div class="form-wrapper">
            <!-- Header Section -->
            <div class="form-header">
                <div class="header-content">
                    <div class="scheme-info">
                        <div class="scheme-name">{{ $scheme->nama }}</div>
                        <div class="scheme-code">({{ $scheme->code_1 }})</div>
                    </div>

                    <div class="form-title d-flex align-items-baseline gap-2 justify-content-center">
                        <h1>FR APL 01 -</h1>
                        <h2 class="text-capitalize mb-0">formulir permohonan sertifikasi profesi</h2>
                    </div>

                    <div class="status-badges ">
                        <span class="status-badge number">{{ $existingApl->nomor_apl_01 ?? 'DRAFT' }}</span>
                        <span class="status-badge {{ $existingApl->statusColor ?? 'secondary' }}">
                            {{ $existingApl->statusText ?? 'Draft' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <form
                action="{{ isset($existingApl) && $existingApl->exists ? route('asesi.apl01.update', $existingApl->id) : route('asesi.apl01.store', $scheme->id) }}"
                method="POST" id="apl01Form" enctype="multipart/form-data" class="main-form">
                @csrf
                @if (isset($existingApl) && $existingApl->exists)
                    @method('PUT')
                @endif

                <!-- Hidden field for signature -->
                <input type="hidden" name="tanda_tangan_asesi" id="signature-input"
                    value="{{ old('tanda_tangan_asesi', $existingApl->tanda_tangan_asesi) }}">

                <!-- Progress Indicator -->
                <div class="progress-indicator">
                    <div class="progress-bar" id="progressBar"></div>
                    <div class="progress-text" id="progressText">0% Completed</div>
                </div>

                <!-- Rincian Data Pemohon Sertifikat -->
                <div class="section-header">
                    <div class="section-number">01</div>
                    <h3>Rincian Data Pemohon Sertifikat</h3>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" class="form-input @error('nama_lengkap') error @enderror" name="nama_lengkap"
                            value="{{ old('nama_lengkap', $existingApl->nama_lengkap) }}"
                            placeholder="Masukkan nama lengkap" required>
                        @error('nama_lengkap')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">NIK</label>
                        <input type="text" class="form-input @error('nik') error @enderror" name="nik" maxlength="16"
                            value="{{ old('nik', $existingApl->nik) }}" placeholder="16 digit NIK" required>
                        @error('nik')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Tempat Lahir</label>
                        <input type="text" class="form-input @error('tempat_lahir') error @enderror" name="tempat_lahir"
                            value="{{ old('tempat_lahir', $existingApl->tempat_lahir) }}" placeholder="Kota tempat lahir"
                            required>
                        @error('tempat_lahir')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Tanggal Lahir</label>
                        <input type="date" class="form-input @error('tanggal_lahir') error @enderror"
                            name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $existingApl->tanggal_lahir?->format('Y-m-d')) }}" required>
                        @error('tanggal_lahir')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Jenis Kelamin</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="jenis_kelamin" value="L"
                                    {{ old('jenis_kelamin', $existingApl->jenis_kelamin) == 'L' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Laki-laki</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="jenis_kelamin" value="P"
                                    {{ old('jenis_kelamin', $existingApl->jenis_kelamin) == 'P' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Perempuan</span>
                            </label>
                        </div>
                        @error('jenis_kelamin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Kebangsaan</label>
                        <input type="text" class="form-input @error('kebangsaan') error @enderror" name="kebangsaan"
                            value="{{ old('kebangsaan', $existingApl->kebangsaan ?? 'Indonesia') }}"
                            placeholder="Kebangsaan" required>
                        @error('kebangsaan')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <!-- Data Alamat Rumah -->
                <div class="section-header">
                    <div class="section-number">02</div>
                    <h3>Data Alamat Rumah</h3>
                </div>

                <div class="form-group full-width">
                    <label class="form-label required">Alamat Rumah</label>
                    <textarea class="form-input @error('alamat_rumah') error @enderror" name="alamat_rumah" rows="3"
                        placeholder="Alamat lengkap rumah" required>{{ old('alamat_rumah', $existingApl->alamat_rumah) }}</textarea>
                    @error('alamat_rumah')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">Kota/Kabupaten</label>
                        <select class="form-select @error('kota_rumah') error @enderror" id="kota_rumah"
                            name="kota_rumah" required>
                            <option value="">Pilih Kota/Kabupaten</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" data-province-id="{{ $city->province->id }}"
                                    data-province-name="{{ $city->province->name }}"
                                    {{ old('kota_rumah', $existingApl->kota_rumah) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('kota_rumah')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Provinsi Rumah</label>

                        <input type="hidden" id="provinsi_rumah" name="provinsi_rumah"
                            value="{{ old('provinsi_rumah', $existingApl->provinsi_rumah) }}">
                        <input type="text" id="provinsi_rumah_display" class="form-input"
                            value="{{ old('provinsi_rumah_display', $existingApl->provinceRumah?->name) }}"
                            placeholder="Provinsi akan otomatis terisi" readonly>
                        @error('provinsi_rumah')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" class="form-input @error('kode_pos') error @enderror" name="kode_pos"
                            maxlength="10" placeholder="12345" value="{{ old('kode_pos', $existingApl->kode_pos) }}">
                        @error('kode_pos')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Telepon Rumah</label>
                        <input type="text" class="form-input @error('no_telp_rumah') error @enderror"
                            name="no_telp_rumah" placeholder="021-12345678"
                            value="{{ old('no_telp_rumah', $existingApl->no_telp_rumah) }}">
                        @error('no_telp_rumah')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <!-- Data Kontak -->
                <div class="section-header">
                    <div class="section-number">03</div>
                    <h3>Data Kontak</h3>
                </div>

                <div class="form-grid two-columns">
                    <div class="form-group">
                        <label class="form-label required">No. HP</label>
                        <input type="text" class="form-input @error('no_hp') error @enderror" name="no_hp"
                            maxlength="15" placeholder="08123456789" value="{{ old('no_hp', $existingApl->no_hp) }}"
                            required>
                        @error('no_hp')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" class="form-input @error('email') error @enderror" name="email"
                            placeholder="nama@email.com" value="{{ old('email', $existingApl->email) }}" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <!-- Data Pendidikan -->
                <div class="section-header">
                    <div class="section-number">04</div>
                    <h3>Data Pendidikan</h3>
                </div>

                <div class="form-grid two-columns">
                    <div class="form-group">
                        <label class="form-label required">Pendidikan Terakhir</label>
                        <select class="form-select @error('pendidikan_terakhir') error @enderror"
                            name="pendidikan_terakhir" required>
                            <option value="">Pilih Pendidikan</option>
                            @foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'Sarjana', 'Magister', 'Doktor'] as $edu)
                                <option value="{{ $edu }}"
                                    {{ old('pendidikan_terakhir', $existingApl->pendidikan_terakhir) == $edu ? 'selected' : '' }}>
                                    {{ $edu }}
                                </option>
                            @endforeach
                        </select>
                        @error('pendidikan_terakhir')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Nama Sekolah/Universitas Terakhir</label>
                        <input type="text" class="form-input @error('nama_sekolah_terakhir') error @enderror"
                            name="nama_sekolah_terakhir" placeholder="Nama institusi pendidikan"
                            value="{{ old('nama_sekolah_terakhir', $existingApl->nama_sekolah_terakhir) }}" required>
                        @error('nama_sekolah_terakhir')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <!-- Data Pekerjaan Sekarang -->
                <div class="section-header">
                    <div class="section-number">05</div>
                    <h3>Data Pekerjaan Sekarang</h3>
                </div>

                <div class="subsection">
                    <h4>Informasi Pekerjaan</h4>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label required">Kategori Pekerjaan</label>
                            <select name="kategori_pekerjaan"
                                class="form-select @error('kategori_pekerjaan') error @enderror" required>
                                <option value="">Pilih Kategori Pekerjaan</option>
                                @foreach (\App\Models\Pekerjaan::orderBy('kode')->get() as $pekerjaan)
                                    <option value="{{ $pekerjaan->nama_pekerjaan }}"
                                        {{ old('kategori_pekerjaan', $existingApl->kategori_pekerjaan) == $pekerjaan->nama_pekerjaan ? 'selected' : '' }}>
                                        {{ $pekerjaan->nama_pekerjaan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_pekerjaan')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Nama Tempat Kerja</label>
                            <input type="text" class="form-input @error('nama_tempat_kerja') error @enderror"
                                name="nama_tempat_kerja" placeholder="PT. Nama Perusahaan"
                                value="{{ old('nama_tempat_kerja', $existingApl->nama_tempat_kerja) }}" required>
                            @error('nama_tempat_kerja')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Jabatan</label>
                            <input type="text" class="form-input @error('jabatan') error @enderror" name="jabatan"
                                placeholder="Posisi/jabatan saat ini" value="{{ old('jabatan', $existingApl->jabatan) }}"
                                required>
                            @error('jabatan')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="subsection">
                    <h4>Alamat Kantor</h4>
                    <div class="form-group full-width">
                        <label class="form-label">Alamat Kantor</label>
                        <textarea class="form-input @error('nama_jalan_kantor') error @enderror" name="nama_jalan_kantor" rows="3"
                            placeholder="Alamat lengkap kantor (opsional)">{{ old('nama_jalan_kantor', $existingApl->nama_jalan_kantor) }}</textarea>
                        @error('nama_jalan_kantor')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Kota/Kabupaten Kantor</label>
                            <select class="form-select @error('kota_kantor') error @enderror" id="kota_kantor"
                                name="kota_kantor">
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" data-province-id="{{ $city->province->id }}"
                                        data-province-name="{{ $city->province->name }}">
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kota_kantor')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Provinsi Kantor</label>

                            <input type="hidden" id="provinsi_kantor" name="provinsi_kantor"
                                value="{{ old('provinsi_kantor', $existingApl->provinsi_kantor) }}">
                            <input type="text" id="provinsi_kantor_display" class="form-input"
                                value="{{ old('provinsi_kantor_display', $existingApl->provinceKantor?->name) }}"
                                placeholder="Provinsi akan otomatis terisi" readonly>
                            @error('provinsi_kantor')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="form-group">
                            <label class="form-label">Kode Pos Kantor</label>
                            <input type="text" class="form-input @error('kode_pos_kantor') error @enderror"
                                name="kode_pos_kantor" maxlength="10" placeholder="12345"
                                value="{{ old('kode_pos_kantor', $existingApl->kode_pos_kantor) }}">
                            @error('kode_pos_kantor')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">No. Telepon Kantor</label>
                            <input type="text" class="form-input @error('no_telp_kantor') error @enderror"
                                name="no_telp_kantor" placeholder="021-12345678"
                                value="{{ old('no_telp_kantor', $existingApl->no_telp_kantor) }}">
                            @error('no_telp_kantor')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Daftar Unit Kompetensi yang Dimohon -->
                <div class="section-header">
                    <div class="section-number">06</div>
                    <h3>Daftar Unit Kompetensi yang Dimohon</h3>
                </div>

                @if ($scheme->activeUnitKompetensis && $scheme->activeUnitKompetensis->count() > 0)
                    <div class="competency-table">
                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Kode Unit</th>
                                        <th>Judul Unit Kompetensi</th>
                                        <th class="text-center">Standar Kompetensi Kerja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($scheme->activeUnitKompetensis as $index => $unit)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="code-cell">{{ $unit->kode_unit }}</td>
                                            <td class="title-cell">{{ $unit->judul_unit }}</td>
                                            @if ($index === 0)
                                                <td class="text-center standard-cell"
                                                    rowspan="{{ $scheme->activeUnitKompetensis->count() }}">
                                                    {{ $unit->standar_kompetensi_kerja ?? '-' }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="icon-warning"></i>
                        Belum ada unit kompetensi yang tersedia untuk skema ini.
                    </div>
                @endif

                <hr>

                <!-- Tempat Uji Kompetensi dan Kategori Peserta -->
                <div class="section-header">
                    <div class="section-number">07</div>
                    <h3>Tempat Uji Kompetensi dan Kategori Peserta</h3>
                </div>

                <div class="form-grid two-columns">
                    <div class="form-group">
                        <label class="form-label">TUK (Tempat Uji Kompetensi)</label>
                        <select class="form-select @error('tuk') error @enderror" name="tuk">
                            <option value="">Pilih TUK</option>
                            @foreach (['Sewaktu', 'Tempat Kerja', 'Mandiri'] as $tuk)
                                <option value="{{ $tuk }}"
                                    {{ old('tuk', $existingApl->tuk) == $tuk ? 'selected' : '' }}>
                                    {{ $tuk }}
                                </option>
                            @endforeach
                        </select>
                        @error('tuk')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Kategori Peserta</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="kategori_peserta" value="individu"
                                    {{ old('kategori_peserta', $existingApl->kategori_peserta) == 'individu' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Individu / Mandiri</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="kategori_peserta" value="training_provider"
                                    {{ old('kategori_peserta', $existingApl->kategori_peserta) == 'training_provider' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Training Provider</span>
                            </label>
                        </div>
                        @error('kategori_peserta')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="training_provider_section" class="conditional-section"
                    style="display: {{ old('kategori_peserta', $existingApl->kategori_peserta) == 'training_provider' ? 'block' : 'none' }};">
                    <div class="form-group">
                        <label class="form-label required">Nama Training Provider</label>
                        <select class="form-select @error('training_provider') error @enderror" name="training_provider">
                            <option value="">Pilih Training Provider</option>
                            @foreach ($trainingProviders as $provider)
                                <option value="{{ $provider->id }}"
                                    {{ old('training_provider', $existingApl->training_provider) == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('training_provider')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <hr>

                <!-- Tujuan Asesmen -->
                <div class="section-header">
                    <div class="section-number">08</div>
                    <h3>Tujuan Asesmen</h3>
                </div>

                <div class="form-group">
                    <label class="form-label required">Pilih Tujuan Asesmen</label>
                    <div class="radio-grid">
                        @foreach ([
            'Sertifikasi' => 'Sertifikasi',
            'Sertifikasi Ulang' => 'Sertifikasi Ulang',
            'PKT' => 'Pengakuan Kompetensi Terkini (PKT)',
            'RPL' => 'Rekognisi Pembelajaran Lampau (RPL)',
            'Lainnya' => 'Lainnya',
        ] as $value => $label)
                            <label class="radio-option card-option">
                                <input type="radio" name="tujuan_asesmen_radio" value="{{ $value }}"
                                    {{ old(
                                        'tujuan_asesmen_radio',
                                        in_array($existingApl->tujuan_asesmen, ['Sertifikasi', 'Sertifikasi Ulang', 'PKT', 'RPL'])
                                            ? $existingApl->tujuan_asesmen
                                            : 'Lainnya',
                                    ) == $value
                                        ? 'checked'
                                        : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <div class="option-content">
                                    <span class="option-title">{{ $label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('tujuan_asesmen_radio')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Jika pilih "Lainnya" --}}
                <div id="tujuan_lainnya_section" class="conditional-section"
                    style="display: {{ old(
                        'tujuan_asesmen_radio',
                        in_array($existingApl->tujuan_asesmen, ['Sertifikasi', 'Sertifikasi Ulang', 'PKT', 'RPL'])
                            ? $existingApl->tujuan_asesmen
                            : 'Lainnya',
                    ) == 'Lainnya'
                        ? 'block'
                        : 'none' }};">
                    <div class="form-group">
                        <label class="form-label required">Jelaskan Tujuan Asesmen Lainnya</label>
                        <textarea class="form-input @error('tujuan_asesmen') error @enderror" name="tujuan_asesmen" rows="3"
                            placeholder="Jelaskan tujuan asesmen Anda">{{ old('tujuan_asesmen', !in_array($existingApl->tujuan_asesmen, ['Sertifikasi', 'Sertifikasi Ulang', 'PKT', 'RPL']) ? $existingApl->tujuan_asesmen : '') }}</textarea>
                        @error('tujuan_asesmen')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <!-- Persyaratan Khusus -->
                <!-- Bagian Persyaratan Khusus yang diupdate -->
                @if ($scheme->requirementTemplates && $scheme->requirementTemplates->count() > 0)
                    <div class="section-header">
                        <div class="section-number">09</div>
                        <h3>Persyaratan Khusus</h3>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Pilih Template Persyaratan yang Sesuai</label>
                        <div class="radio-group">
                            @foreach ($scheme->requirementTemplates as $template)
                                <label class="radio-option template-radio-option">
                                    <input class="requirement-template-radio" type="radio"
                                        name="selected_requirement_template" value="{{ $template->id }}"
                                        {{ old('selected_requirement_template', $existingApl->selected_requirement_template_id) == $template->id ? 'checked' : '' }}>
                                    <span class="radio-custom"></span>
                                    <div class="template-content">
                                        <div class="template-title">{{ $template->name }}</div>
                                        @if ($template->activeItems && $template->activeItems->count() > 0)
                                            <div class="template-items-preview">
                                                <strong>Item Persyaratan ({{ $template->activeItems->count() }}
                                                    dokumen):</strong>
                                                <ul class="items-list">
                                                    @foreach ($template->activeItems->take(3) as $item)
                                                        <li>
                                                            {{ $item->document_name }}
                                                            @if ($item->is_required)
                                                                <span class="badge badge-required ms-1">Wajib</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                    @if ($template->activeItems->count() > 3)
                                                        <li class="more-items">... dan
                                                            {{ $template->activeItems->count() - 3 }} dokumen lainnya</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @else
                                            <div class="no-items">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Belum ada item persyaratan
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('selected_requirement_template')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dynamic Template Requirements -->
                    @foreach ($scheme->requirementTemplates as $template)
                        <div id="template_requirements_{{ $template->id }}" class="template-requirements"
                            style="display: {{ old('selected_requirement_template', $existingApl->selected_requirement_template_id) == $template->id ? 'block' : 'none' }};">

                            @if ($template->activeItems && $template->activeItems->count() > 0)
                                <div class="requirements-container">
                                    <div class="requirements-header">
                                        <h5>
                                            <i class="bi bi-folder-check me-2"></i>
                                            {{ $template->name }} - Upload Dokumen
                                        </h5>
                                        @if ($template->requirement_description)
                                            <div class="alert alert-info d-flex align-items-start">
                                                <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                                                <div>{{ $template->requirement_description }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row g-3">
                                        @foreach ($template->activeItems as $item)
                                            @php
                                                $fieldName = "requirement_item_{$item->id}";

                                                // PERBAIKAN: Gunakan getRequirementItemAnswer dengan benar
                                                $existingValue = old($fieldName);
                                                if (!$existingValue) {
                                                    $existingValue =
                                                        $existingApl->getRequirementItemAnswer($item->id) ?? '';
                                                }

                                                $isRequired = $item->is_required;
                                                $maxFileSize = $item->max_file_size ?? 5;
                                                $allowedExtensions =
                                                    $item->allowed_extensions ?? '.pdf,.doc,.docx,.jpg,.jpeg,.png';
                                            @endphp

                                            <div class="col-md-6">
                                                <div class="upload-card border rounded p-3 h-100 position-relative"
                                                    data-item-id="{{ $item->id }}">
                                                    <!-- Header dengan nama dokumen -->
                                                    <div class="d-flex align-items-center mb-3">
                                                        <i class="bi bi-file-earmark text-primary me-2 fs-5"></i>
                                                        <label class="form-label fw-semibold mb-0">
                                                            {{ $item->document_name }}
                                                            @if ($isRequired)
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                    </div>

                                                    @if ($item->description)
                                                        <small class="form-text text-muted mb-2 d-block">
                                                            {{ $item->description }}
                                                        </small>
                                                    @endif

                                                    @if ($item->type === 'file_upload')
                                                        <!-- Show existing file if available -->
                                                        @if ($existingApl->exists && $existingApl->hasRequirementFile($item->id))
                                                            <div
                                                                class="existing-file-display mb-3 p-3 bg-success bg-opacity-10 rounded border">
                                                                @php
                                                                    $fileName = $existingApl->getRequirementFileName(
                                                                        $item->id,
                                                                    );
                                                                    $fileUrl = $existingApl->getRequirementFileUrl(
                                                                        $item->id,
                                                                    );
                                                                    $fileExtension = pathinfo(
                                                                        $fileName,
                                                                        PATHINFO_EXTENSION,
                                                                    );
                                                                @endphp

                                                                <div class="d-flex align-items-start">
                                                                    <div class="file-icon me-3">
                                                                        @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                                                                            <img src="{{ $fileUrl }}"
                                                                                alt="Preview" class="img-thumbnail"
                                                                                style="max-width:80px; max-height:80px;">
                                                                        @elseif (strtolower($fileExtension) === 'pdf')
                                                                            <i
                                                                                class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                                        @else
                                                                            <i
                                                                                class="bi bi-file-earmark text-primary fs-1"></i>
                                                                        @endif
                                                                    </div>
                                                                    <div class="file-info flex-grow-1">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <i
                                                                                class="bi bi-check-circle-fill text-success me-1"></i>
                                                                            <strong class="text-success">File sudah
                                                                                diupload</strong>
                                                                        </div>
                                                                        <small
                                                                            class="text-muted d-block">{{ $fileName }}</small>
                                                                        <div class="mt-2">
                                                                            <a href="{{ $fileUrl }}"
                                                                                target="_blank"
                                                                                class="btn btn-outline-primary btn-sm me-1">
                                                                                <i class="bi bi-eye"></i> Lihat
                                                                            </a>
                                                                            <a href="{{ $fileUrl }}"
                                                                                download="{{ $fileName }}"
                                                                                class="btn btn-outline-success btn-sm me-1">
                                                                                <i class="bi bi-download"></i> Download
                                                                            </a>
                                                                            <button type="button"
                                                                                class="btn btn-outline-warning btn-sm"
                                                                                onclick="replaceExistingFile('{{ $item->id }}')">
                                                                                <i class="bi bi-arrow-repeat"></i> Ganti
                                                                                File
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- PERBAIKAN: File input TANPA required jika ada file existing -->
                                                            <input type="file"
                                                                class="form-control @error($fieldName) is-invalid @enderror"
                                                                name="{{ $fieldName }}"
                                                                accept="{{ $allowedExtensions }}"
                                                                onchange="previewFile(this, '{{ $item->id }}')"
                                                                style="display: none;"
                                                                id="file_input_{{ $item->id }}">

                                                            <!-- Hidden input untuk menandai bahwa file existing ada -->
                                                            <input type="hidden" name="{{ $fieldName }}_existing"
                                                                value="1">
                                                        @else
                                                            <!-- PERBAIKAN: File input DENGAN required jika tidak ada file existing -->
                                                            <input type="file"
                                                                class="form-control @error($fieldName) is-invalid @enderror"
                                                                name="{{ $fieldName }}"
                                                                accept="{{ $allowedExtensions }}"
                                                                {{ $isRequired ? 'required' : '' }}
                                                                onchange="previewFile(this, '{{ $item->id }}')"
                                                                id="file_input_{{ $item->id }}">
                                                        @endif

                                                        <!-- Preview container for new uploads -->
                                                        <div id="preview_{{ $item->id }}"
                                                            class="preview-container mt-3" style="display: none;">
                                                            <div class="preview-content">
                                                                <img id="img_{{ $item->id }}" class="preview-image"
                                                                    style="display: none; max-width: 100px;">
                                                                <div id="pdf_{{ $item->id }}"
                                                                    class="pdf-preview text-center"
                                                                    style="display: none;">
                                                                    <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                                    <p class="small text-muted mt-2">PDF File</p>
                                                                </div>
                                                                <div class="file-info mt-2">
                                                                    <small id="filename_{{ $item->id }}"
                                                                        class="text-muted d-block"></small>
                                                                    <small id="filesize_{{ $item->id }}"
                                                                        class="text-muted"></small>
                                                                </div>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger mt-2"
                                                                    onclick="removePreview('{{ $item->id }}')">
                                                                    <i class="bi bi-trash"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @elseif ($item->type === 'text_input')
                                                        <input type="text"
                                                            class="form-control @error($fieldName) is-invalid @enderror"
                                                            name="{{ $fieldName }}" value="{{ $existingValue }}"
                                                            {{ $isRequired ? 'required' : '' }}
                                                            placeholder="Masukkan {{ strtolower($item->document_name) }}...">
                                                    @elseif ($item->type === 'textarea')
                                                        <textarea class="form-control @error($fieldName) is-invalid @enderror" name="{{ $fieldName }}" rows="3"
                                                            {{ $isRequired ? 'required' : '' }} placeholder="Masukkan {{ strtolower($item->document_name) }}...">{{ $existingValue }}</textarea>
                                                    @elseif ($item->type === 'select')
                                                        <select
                                                            class="form-select @error($fieldName) is-invalid @enderror"
                                                            name="{{ $fieldName }}"
                                                            {{ $isRequired ? 'required' : '' }}>
                                                            <option value="">Pilih
                                                                {{ strtolower($item->document_name) }}...</option>
                                                            @if ($item->options)
                                                                @foreach (json_decode($item->options, true) as $option)
                                                                    <option value="{{ $option }}"
                                                                        {{ $existingValue == $option ? 'selected' : '' }}>
                                                                        {{ $option }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    @elseif ($item->type === 'checkbox')
                                                        @php
                                                            $checkboxValues = is_string($existingValue)
                                                                ? explode(',', $existingValue)
                                                                : (is_array($existingValue)
                                                                    ? $existingValue
                                                                    : []);
                                                        @endphp
                                                        @if ($item->options)
                                                            <div class="checkbox-group">
                                                                @foreach (json_decode($item->options, true) as $option)
                                                                    <label class="checkbox-option">
                                                                        <input type="checkbox"
                                                                            name="{{ $fieldName }}[]"
                                                                            value="{{ $option }}"
                                                                            {{ in_array($option, $checkboxValues) ? 'checked' : '' }}>
                                                                        <span class="checkbox-custom"></span>
                                                                        <span
                                                                            class="checkbox-label">{{ $option }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <label class="checkbox-option">
                                                                <input type="checkbox" name="{{ $fieldName }}"
                                                                    value="1" {{ $existingValue ? 'checked' : '' }}>
                                                                <span class="checkbox-custom"></span>
                                                                <span
                                                                    class="checkbox-label">{{ $item->document_name }}</span>
                                                            </label>
                                                        @endif
                                                    @endif

                                                    @error($fieldName)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror

                                                    <small class="text-muted d-block mt-2">
                                                        @if ($item->type === 'file_upload')
                                                            Format:
                                                            {{ str_replace(',', ', ', strtoupper(str_replace('.', '', $allowedExtensions))) }}
                                                            (Max: {{ $maxFileSize }}MB)
                                                        @endif
                                                        @if ($isRequired)
                                                            <span class="text-danger">*Wajib</span>
                                                        @else
                                                            <span class="text-muted">Opsional</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>
                                        <strong>Template Kosong</strong><br>
                                        Template "{{ $template->name }}" belum memiliki item persyaratan yang aktif.
                                        Silakan hubungi administrator.
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                <hr>

                <!-- Kemampuan Teknis -->
                <div class="section-header">
                    <div class="section-number">10</div>
                    <h3>Kemampuan Teknis</h3>
                </div>

                <div class="form-grid single-column">
                    <div class="form-group">
                        <label class="form-label required">Apakah Anda pernah mengikuti asesmen di LSP
                            sebelumnya?</label>
                        <div class="radio-inline">
                            <label class="radio-option">
                                <input type="radio" name="pernah_asesmen_lsp" value="sudah"
                                    {{ old('pernah_asesmen_lsp', $existingApl->pernah_asesmen_lsp) == 'sudah' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Sudah</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="pernah_asesmen_lsp" value="belum"
                                    {{ old('pernah_asesmen_lsp', $existingApl->pernah_asesmen_lsp) == 'belum' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Belum</span>
                            </label>
                        </div>
                        @error('pernah_asesmen_lsp')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Apakah Anda bisa melakukan <strong>Share
                                Screen</strong>?</label>
                        <div class="radio-inline">
                            <label class="radio-option">
                                <input type="radio" name="bisa_share_screen" value="ya"
                                    {{ old('bisa_share_screen', $existingApl->bisa_share_screen) == 'ya' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Ya</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="bisa_share_screen" value="tidak"
                                    {{ old('bisa_share_screen', $existingApl->bisa_share_screen) == 'tidak' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Tidak</span>
                            </label>
                        </div>
                        @error('bisa_share_screen')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Apakah Anda bisa menggunakan <strong>browser
                                internet</strong>
                            (Chrome, Edge, Firefox)?</label>
                        <div class="radio-inline">
                            <label class="radio-option">
                                <input type="radio" name="bisa_gunakan_browser" value="ya"
                                    {{ old('bisa_gunakan_browser', $existingApl->bisa_gunakan_browser) == 'ya' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Ya</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="bisa_gunakan_browser" value="tidak"
                                    {{ old('bisa_gunakan_browser', $existingApl->bisa_gunakan_browser) == 'tidak' ? 'checked' : '' }}
                                    required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">Tidak</span>
                            </label>
                        </div>
                        @error('bisa_gunakan_browser')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Aplikasi yang Pernah Digunakan</label>
                        @php
                            $currentApps = old(
                                'aplikasi_yang_digunakan',
                                is_string($existingApl->aplikasi_yang_digunakan)
                                    ? explode(', ', $existingApl->aplikasi_yang_digunakan)
                                    : $existingApl->aplikasi_yang_digunakan ?? [],
                            );
                            $currentApps = is_array($currentApps) ? $currentApps : [];
                        @endphp

                        <div class="checkbox-grid two-columns">
                            @foreach ([
            'Microsoft Power Point' => 'Microsoft PowerPoint',
            'Microsoft Excel' => 'Microsoft Excel',
            'Microsoft Word' => 'Microsoft Word',
            'Video Conference (Zoom/Google Meet)' => 'Video Conference',
        ] as $value => $label)
                                <label class="checkbox-option">
                                    <input type="checkbox" name="aplikasi_yang_digunakan[]" value="{{ $value }}"
                                        {{ in_array($value, $currentApps) ? 'checked' : '' }}>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-label">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('aplikasi_yang_digunakan')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <!-- Pernyataan Pemohon -->
                <div class="section-header">
                    <div class="section-number">11</div>
                    <h3>Pernyataan Pemohon</h3>
                </div>

                <div class="declaration-box">
                    <h5>Dengan ini, saya menyatakan bahwa saya telah menuliskan nama lengkap dengan benar. Nama tersebut
                        dapat digunakan untuk pencetakan sertifikat kompetensi apabila saya dinyatakan kompeten dalam proses
                        asesmen ini.</h5>
                </div>

                <div class="agreement-section">
                    <label class="checkbox-option agreement-option">
                        <input type="checkbox" name="pernyataan_benar" value="1"
                            {{ old('pernyataan_benar', $existingApl->pernyataan_benar) ? 'checked' : '' }} required>
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-label">
                            <strong>Saya menyetujui semua pernyataan di atas dan data yang saya berikan adalah
                                benar</strong>
                        </span>
                    </label>
                    @error('pernyataan_benar')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="signature-section col-5">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap TANPA GELAR (untuk sertifikat)</label>
                        <input type="text" class="form-input @error('nama_lengkap_ktp') error @enderror"
                            name="nama_lengkap_ktp"
                            value="{{ old('nama_lengkap_ktp', $existingApl->nama_lengkap_ktp ?? $existingApl->nama_lengkap) }}"
                            placeholder="Nama untuk sertifikat">
                        @error('nama_lengkap_ktp')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">Nama ini akan tercetak pada sertifikat</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Tanda Tangan Digital</label>

                        @if (old('tanda_tangan_asesi', $existingApl->tanda_tangan_asesi))
                            <div class="existing-signature mb-3">
                                <div class="d-flex align-items-center p-3 border rounded bg-success bg-opacity-10">
                                    <div class="signature-preview me-3">
                                        <img src="{{ old('tanda_tangan_asesi', $existingApl->tanda_tangan_asesi) }}"
                                            alt="Tanda tangan tersimpan" class="signature-image border rounded"
                                            style="max-width: 150px; max-height: 80px;">
                                    </div>
                                    <div class="signature-info">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        <strong>Tanda tangan sudah tersimpan</strong>
                                        @if ($existingApl->tanggal_tanda_tangan_asesi)
                                            <small class="d-block text-muted">
                                                {{ $existingApl->tanggal_tanda_tangan_asesi->format('d M Y H:i') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="existing_signature" value="1">
                        @else
                            <!-- Canvas hanya tampil jika belum ada TTD -->
                            <div class="signature-pad-wrapper position-relative border rounded" style="height:200px;">
                                <canvas id="signature-canvas" class="w-100 h-100" style="cursor: crosshair;"></canvas>
                                <div id="signature-placeholder"
                                    class="position-absolute top-50 start-50 translate-middle text-muted small d-flex align-items-center justify-content-center pointer-events-none">
                                    <i class="bi bi-pencil me-1"></i>
                                    <span>Klik dan tahan untuk menggambar tanda tangan</span>
                                </div>
                            </div>

                            <div class="signature-tips text-muted small mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Tips: Gunakan mouse atau jari untuk menggambar tanda tangan
                            </div>

                            <button type="button" id="clear-signature" class="btn btn-outline-secondary btn-sm mt-2">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Clear
                            </button>
                        @endif

                        @error('tanda_tangan_asesi')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <div class="action-buttons">
                        <a href="{{ route('asesi.skema-sertifikasi.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali</span>
                        </a>

                        @if (!isset($existingApl) || $existingApl->isEditable)
                            <div class="primary-actions">
                                <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
                                    <i class="bi bi-save"></i>
                                    <span>Simpan Draft</span>
                                </button>

                                <button type="submit" name="action" value="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i>
                                    <span>Submit APL-01</span>
                                </button>
                            </div>
                        @else
                            <div class="readonly-notice text-muted d-flex align-items-center">
                                <i class="bi bi-lock-fill me-1"></i>
                                <span>APL 01 sudah disubmit dan tidak dapat diedit</span>
                            </div>
                        @endif
                    </div>

                    <div class="form-summary mt-3">
                        <div class="summary-info text-muted">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            <span>Pastikan semua data sudah benar sebelum submit</span>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="loading-overlay" id="loadingModal">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">
                <h4>Memproses formulir...</h4>
                <p>Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/apl01-form.css') }}">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/apl01-form.js') }}"></script>
@endpush
