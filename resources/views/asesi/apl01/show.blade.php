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
                        <div class="scheme-name">{{ $apl->certificationScheme->nama }}</div>
                        <div class="scheme-code">({{ $apl->certificationScheme->code_1 }})</div>
                    </div>

                    <div class="form-title d-flex align-items-baseline gap-2 justify-content-center">
                        <h1>FR APL 01 -</h1>
                        <h2 class="text-capitalize mb-0">formulir permohonan sertifikasi profesi</h2>
                    </div>

                    <div class="status-badges">
                        <span class="status-badge number">{{ $apl->nomor_apl_01 ?? 'DRAFT' }}</span>
                        <span class="status-badge {{ $apl->statusColor ?? 'secondary' }}">
                            {{ $apl->statusText ?? 'Draft' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-form readonly-form">

                <!-- Rincian Data Pemohon Sertifikat -->
                <div class="section-header">
                    <div class="section-number">01</div>
                    <h3>Rincian Data Pemohon Sertifikat</h3>
                </div>

                <div class="form-grid">
                    <div class="form-group readonly">
                        <label class="form-label">Nama Lengkap</label>
                        <div class="form-value">{{ $apl->nama_lengkap }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">NIK</label>
                        <div class="form-value">{{ $apl->nik }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Tempat Lahir</label>
                        <div class="form-value">{{ $apl->tempat_lahir }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Tanggal Lahir</label>
                        <div class="form-value">{{ $apl->tanggal_lahir?->format('d F Y') }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="form-value">
                            <span
                                class="badge badge-light">{{ $apl->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Kebangsaan</label>
                        <div class="form-value">{{ $apl->kebangsaan }}</div>
                    </div>
                </div>

                <hr>

                <!-- Data Alamat Rumah -->
                <div class="section-header">
                    <div class="section-number">02</div>
                    <h3>Data Alamat Rumah</h3>
                </div>

                <div class="form-group full-width readonly">
                    <label class="form-label">Alamat Rumah</label>
                    <div class="form-value textarea-value">{{ $apl->alamat_rumah }}</div>
                </div>

                <div class="form-grid">
                    <div class="form-group readonly">
                        <label class="form-label">Kota/Kabupaten</label>
                        <div class="form-value">{{ $apl->kota_rumah }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Provinsi</label>
                        <div class="form-value">{{ $apl->provinsi_rumah }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Kode Pos</label>
                        <div class="form-value">{{ $apl->kode_pos ?: '-' }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">No. Telepon Rumah</label>
                        <div class="form-value">{{ $apl->no_telp_rumah ?: '-' }}</div>
                    </div>
                </div>

                <hr>

                <!-- Data Kontak -->
                <div class="section-header">
                    <div class="section-number">03</div>
                    <h3>Data Kontak</h3>
                </div>

                <div class="form-grid two-columns">
                    <div class="form-group readonly">
                        <label class="form-label">No. HP</label>
                        <div class="form-value">{{ $apl->no_hp }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Email</label>
                        <div class="form-value">{{ $apl->email }}</div>
                    </div>
                </div>

                <hr>

                <!-- Data Pendidikan -->
                <div class="section-header">
                    <div class="section-number">04</div>
                    <h3>Data Pendidikan</h3>
                </div>

                <div class="form-grid two-columns">
                    <div class="form-group readonly">
                        <label class="form-label">Pendidikan Terakhir</label>
                        <div class="form-value">
                            <span class="badge badge-info">{{ $apl->pendidikan_terakhir }}</span>
                        </div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Nama Sekolah/Universitas Terakhir</label>
                        <div class="form-value">{{ $apl->nama_sekolah_terakhir }}</div>
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
                        <div class="form-group full-width readonly">
                            <label class="form-label">Kategori Pekerjaan</label>
                            <div class="form-value">{{ $apl->kategori_pekerjaan }}</div>
                        </div>

                        <div class="form-group readonly">
                            <label class="form-label">Nama Tempat Kerja</label>
                            <div class="form-value">{{ $apl->nama_tempat_kerja }}</div>
                        </div>

                        <div class="form-group readonly">
                            <label class="form-label">Jabatan</label>
                            <div class="form-value">{{ $apl->jabatan }}</div>
                        </div>
                    </div>
                </div>

                @if ($apl->nama_jalan_kantor || $apl->kota_kantor || $apl->provinsi_kantor)
                    <div class="subsection">
                        <h4>Alamat Kantor</h4>
                        @if ($apl->nama_jalan_kantor)
                            <div class="form-group full-width readonly">
                                <label class="form-label">Alamat Kantor</label>
                                <div class="form-value textarea-value">{{ $apl->nama_jalan_kantor }}</div>
                            </div>
                        @endif

                        <div class="form-grid">
                            @if ($apl->kota_kantor)
                                <div class="form-group readonly">
                                    <label class="form-label">Kota/Kabupaten Kantor</label>
                                    <div class="form-value">{{ $apl->kota_kantor }}</div>
                                </div>
                            @endif

                            @if ($apl->provinsi_kantor)
                                <div class="form-group readonly">
                                    <label class="form-label">Provinsi Kantor</label>
                                    <div class="form-value">{{ $apl->provinsi_kantor }}</div>
                                </div>
                            @endif

                            @if ($apl->kode_pos_kantor)
                                <div class="form-group readonly">
                                    <label class="form-label">Kode Pos Kantor</label>
                                    <div class="form-value">{{ $apl->kode_pos_kantor }}</div>
                                </div>
                            @endif

                            @if ($apl->no_telp_kantor)
                                <div class="form-group readonly">
                                    <label class="form-label">No. Telepon Kantor</label>
                                    <div class="form-value">{{ $apl->no_telp_kantor }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <hr>

                <!-- Unit Kompetensi yang Dipilih -->
                <div class="section-header">
                    <div class="section-number">06</div>
                    <h3>Daftar Unit Kompetensi yang Dimohon</h3>
                </div>

                @if ($apl->certificationScheme->activeUnitKompetensis && $apl->certificationScheme->activeUnitKompetensis->count() > 0)
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
                                    @foreach ($apl->certificationScheme->activeUnitKompetensis as $index => $unit)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="code-cell">{{ $unit->kode_unit }}</td>
                                            <td class="title-cell">{{ $unit->judul_unit }}</td>
                                            @if ($index === 0)
                                                <td class="text-center standard-cell"
                                                    rowspan="{{ $apl->certificationScheme->activeUnitKompetensis->count() }}">
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
                    <div class="form-group readonly">
                        <label class="form-label">TUK (Tempat Uji Kompetensi)</label>
                        <div class="form-value">
                            @if ($apl->tuk)
                                <span class="badge badge-primary">{{ $apl->tuk }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Kategori Peserta</label>
                        <div class="form-value">
                            <span class="badge badge-secondary">
                                {{ $apl->kategori_peserta == 'individu' ? 'Individu / Mandiri' : 'Training Provider' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if ($apl->kategori_peserta == 'training_provider' && $apl->training_provider)
                    <div class="form-group readonly">
                        <label class="form-label">Nama Training Provider</label>
                        <div class="form-value">{{ $apl->training_provider }}</div>
                    </div>
                @endif

                <hr>

                <!-- Tujuan Asesmen -->
                <div class="section-header">
                    <div class="section-number">08</div>
                    <h3>Tujuan Asesmen</h3>
                </div>

                <div class="form-group readonly">
                    <label class="form-label">Tujuan Asesmen</label>
                    <div class="form-value">
                        <span class="badge badge-info">{{ $apl->tujuan_asesmen }}</span>
                    </div>
                </div>

                <hr>

                <!-- Persyaratan Khusus -->
                @if ($apl->requirement_answers && count($apl->requirement_answers) > 0)
                    <div class="section-header">
                        <div class="section-number">09</div>
                        <h3>Persyaratan Khusus</h3>
                    </div>

                    <div class="requirements-files">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>{{ count($apl->requirement_answers) }} dokumen</strong> telah diupload untuk
                            persyaratan khusus.
                        </div>

                        <div class="row g-3">
                            @foreach ($apl->requirement_answers as $itemId => $filePath)
                                @if ($filePath && Storage::disk('public')->exists($filePath))
                                    @php
                                        $requirementItem = \App\Models\RequirementItem::find($itemId);
                                        $documentName = $requirementItem
                                            ? $requirementItem->document_name
                                            : "Dokumen $loop->iteration";

                                        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                                        $fileSize = Storage::disk('public')->size($filePath);
                                        $fileSizeFormatted =
                                            $fileSize > 1024 * 1024
                                                ? round($fileSize / (1024 * 1024), 2) . ' MB'
                                                : round($fileSize / 1024, 2) . ' KB';
                                    @endphp

                                    <div class="col-md-4">
                                        <div class="upload-card border rounded p-3 h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                                    <i class="bi bi-image text-success me-2 fs-5"></i>
                                                @elseif(strtolower($fileExtension) === 'pdf')
                                                    <i class="bi bi-file-earmark-pdf text-danger me-2 fs-5"></i>
                                                @elseif(in_array(strtolower($fileExtension), ['doc', 'docx']))
                                                    <i class="bi bi-file-earmark-word text-primary me-2 fs-5"></i>
                                                @else
                                                    <i class="bi bi-file-earmark text-secondary me-2 fs-5"></i>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold">{{ $documentName }}</div>
                                                    <div class="text-muted small">{{ strtoupper($fileExtension) }} •
                                                        {{ $fileSizeFormatted }}</div>
                                                </div>
                                            </div>

                                            <div class="file-info mb-3">
                                                <div class="file-name text-truncate" title="{{ $documentName }}">
                                                    {{ $documentName }}
                                                </div>
                                            </div>

                                            <div class="file-status mb-3">
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle-fill"></i> Terupload
                                                </span>
                                            </div>

                                            <div class="file-actions d-flex gap-2">
                                                <a href="{{ Storage::url($filePath) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary flex-grow-1">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                                <a href="{{ Storage::url($filePath) }}" download
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>

                    <hr>
                @endif

                <!-- Kemampuan Teknis -->
                <div class="section-header">
                    <div class="section-number">10</div>
                    <h3>Kemampuan Teknis</h3>
                </div>

                <div class="form-grid single-column">
                    <div class="form-group readonly">
                        <label class="form-label">Apakah Anda pernah mengikuti asesmen di LSP sebelumnya?</label>
                        <div class="form-value">
                            @if ($apl->pernah_asesmen_lsp)
                                <h6>{{ $apl->pernah_asesmen_lsp }}</h6>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Apakah Anda bisa melakukan Share Screen?</label>
                        <div class="form-value">
                            @if ($apl->bisa_share_screen)
                                <h6>{{ $apl->pernah_asesmen_lsp }}</h6>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Apakah Anda bisa menggunakan browser internet?</label>
                        <div class="form-value">
                            @if ($apl->bisa_gunakan_browser)
                                <h6>{{ $apl->bisa_gunakan_browser }}</h6>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Aplikasi yang Pernah Digunakan</label>
                        <div class="form-value">
                            @if ($apl->aplikasi_yang_digunakan && count($apl->aplikasi_yang_digunakan) > 0)
                                <div class="app-badges">
                                    @foreach ($apl->aplikasi_yang_digunakan as $app)
                                        <span class="badge badge-light me-1 mb-1">{{ $app }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">Tidak ada aplikasi yang dipilih</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Pernyataan Pemohon -->
                <div class="section-header">
                    <div class="section-number">11</div>
                    <h3>Pernyataan Pemohon</h3>
                </div>

                <div class="declaration-box readonly">
                    <h5>Dengan ini, saya menyatakan bahwa saya telah menuliskan nama lengkap dengan benar. Nama tersebut
                        dapat digunakan untuk pencetakan sertifikat kompetensi apabila saya dinyatakan kompeten dalam proses
                        asesmen ini.</h5>
                </div>

                <div class="form-group readonly">
                    <label class="form-label">Persetujuan Pernyataan</label>
                    <div class="form-value">
                        @if ($apl->pernyataan_benar)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle-fill"></i> Disetujui
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-x-circle-fill"></i> Belum Disetujui
                            </span>
                        @endif
                    </div>
                </div>

                <div class="signature-section readonly">
                    <div class="form-group readonly">
                        <label class="form-label">Nama Lengkap TANPA GELAR (untuk sertifikat)</label>
                        <div class="form-value">{{ $apl->nama_lengkap_ktp ?? $apl->nama_lengkap }}</div>
                    </div>

                    <div class="form-group readonly">
                        <label class="form-label">Tanda Tangan Digital</label>
                        <div class="form-value">
                            @if ($apl->tanda_tangan_asesi)
                                <div class="signature-display">
                                    <div class="signature-container border rounded p-3 bg-light">
                                        <img src="{{ asset('storage/' . $apl->tanda_tangan_asesi) }}"
     alt="Tanda tangan {{ $apl->nama_lengkap }}"
     class="signature-image"
     style="max-width: 200px; max-height: 100px;">

                                    </div>
                                    @if ($apl->tanggal_tanda_tangan_asesi)
                                        <div class="signature-date text-muted mt-2">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            Ditandatangani pada:
                                            {{ $apl->tanggal_tanda_tangan_asesi->format('d F Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">Belum ditandatangani</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submission Info -->
                @if ($apl->submitted_at)
                    <div class="submission-info mt-4 p-3 bg-primary bg-opacity-10 rounded">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-send-check-fill text-primary me-2"></i>
                            <div>
                                <strong>Formulir telah disubmit</strong>
                                <div class="text-muted small">
                                    {{ $apl->submitted_at->format('d F Y, H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="form-actions">
                    <div class="action-buttons">
                        <a href="{{ route('asesi.skema-sertifikasi.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Daftar Skema</span>
                        </a>

                        @if ($apl->isEditable)
                            <a href="{{ route('asesi.apl01.edit', $apl->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit APL-01</span>
                            </a>
                        @endif

                        {{-- @if ($apl->status == 'submitted')
                            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                <i class="bi bi-printer"></i>
                                <span>Cetak</span>
                            </button>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk menampilkan response text -->
    <div class="modal fade" id="responseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalTitle">Detail Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="responseModalContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/apl01-form.css') }}">
    <style>
        /* Additional styles for readonly view */
        .readonly-form .form-group.readonly {
            margin-bottom: 1.5rem;
        }

        .readonly-form .form-value {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
            color: #495057;
            font-weight: 500;
        }

        .readonly-form .form-value.textarea-value {
            align-items: flex-start;
            white-space: pre-wrap;
            min-height: 4rem;
            padding-top: 1rem;
        }

        .readonly-form .badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }

        .readonly-form .badge-light {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .readonly-form .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .readonly-form .badge-primary {
            background-color: #cfe2ff;
            color: #084298;
        }

        .readonly-form .badge-secondary {
            background-color: #e2e3e5;
            color: #41464b;
        }

        .readonly-form .app-badges .badge {
            display: inline-block;
            margin-right: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .selected-template-info {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
        }

        .selected-template-info .template-name {
            display: flex;
            align-items: center;
            font-size: 1.1rem;
        }

        .selected-template-info .template-description {
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .signature-display .signature-container {
            display: inline-block;
            min-width: 200px;
        }

        .signature-display .signature-image {
            display: block;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }

        .signature-display .signature-date {
            font-size: 0.875rem;
        }

        .submission-info {
            border: 1px solid rgba(13, 110, 253, 0.2) !important;
        }

        /* Print styles */
        @media print {

            .form-actions,
            .btn {
                display: none !important;
            }

            .readonly-form .form-value {
                border: 1px solid #000;
                background: transparent;
            }

            .badge {
                border: 1px solid #000 !important;
                background: transparent !important;
                color: #000 !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function showResponseModal(title, content) {
            document.getElementById('responseModalTitle').textContent = title;
            document.getElementById('responseModalContent').innerHTML = `<p class="mb-0">${content}</p>`;

            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(document.getElementById('responseModal'));
                modal.show();
            }
        }

        // Initialize progress bars animation
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.transition = 'width 0.8s ease-in-out';
                    bar.style.width = width;
                }, 100);
            });
        });
    </script>
@endpush
