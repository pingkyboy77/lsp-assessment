{{-- resources/views/admin/tuk-requests/partials/apl01-content.blade.php --}}

<div class="review-section">
    <div class="review-header">
        <i class="bi bi-file-text me-2"></i>APL 01 - Data Asesi & Requirements
    </div>
    <div class="review-body">

        <!-- Status Info -->
        @if($apl->notes)
        <div class="alert alert-warning mb-4">
            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Catatan Review</h6>
            <p class="mb-0">{{ $apl->notes }}</p>
            @if($apl->reviewed_at)
            <hr>
            <small class="text-muted">
                Review oleh: {{ $apl->reviewer->name ?? 'N/A' }} - {{ $apl->reviewed_at->format('d M Y H:i') }}
            </small>
            @endif
        </div>
        @endif

        <!-- Rincian Data Pemohon Sertifikat -->
        <div class="section-header-small">
            <h6 class="fw-bold mb-3" style="color: var(--gray-900);">
                <span class="section-number-small">01</span>
                Rincian Data Pemohon Sertifikat
            </h6>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="info-table w-100">
                    <tr>
                        <td>Nama Lengkap</td>
                        <td>{{ $apl->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>{{ $apl->nik }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>{{ $apl->tempat_lahir }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="info-table w-100">
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>{{ $apl->tanggal_lahir?->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>
                            <span class="badge badge-light">
                                {{ $apl->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Kebangsaan</td>
                        <td>{{ $apl->kebangsaan ?? 'Indonesia' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Data Alamat Rumah -->
        <div class="section-header-small">
            <h6 class="fw-bold mb-3 mt-4" style="color: var(--gray-900);">
                <span class="section-number-small">02</span>
                Data Alamat Rumah
            </h6>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="info-table w-100">
                    <tr>
                        <td>Alamat Rumah</td>
                        <td>{{ $apl->alamat_rumah }}</td>
                    </tr>
                    <tr>
                        <td>Kota/Kabupaten</td>
                        <td>{{ $apl->kotaRumah->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Provinsi</td>
                        <td>{{ $apl->provinsiRumah->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="info-table w-100">
                    <tr>
                        <td>Kode Pos</td>
                        <td>{{ $apl->kode_pos ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. Telepon Rumah</td>
                        <td>{{ $apl->no_telp_rumah ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Data Kontak & Pendidikan -->
        <div class="row mb-4">
            <!-- Data Kontak -->
            <div class="col-md-6">
                <div class="section-header-small">
                    <h6 class="fw-bold mb-3" style="color: var(--gray-900);">
                        <span class="section-number-small">03</span>
                        Data Kontak
                    </h6>
                </div>

                <table class="info-table w-100">
                    <tr>
                        <td>No. HP</td>
                        <td>{{ $apl->no_hp }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ $apl->user->email }}</td>
                    </tr>
                </table>
            </div>

            <!-- Data Pendidikan -->
            <div class="col-md-6">
                <div class="section-header-small">
                    <h6 class="fw-bold mb-3" style="color: var(--gray-900);">
                        <span class="section-number-small">04</span>
                        Data Pendidikan
                    </h6>
                </div>

                <table class="info-table w-100">
                    <tr>
                        <td>Pendidikan Terakhir</td>
                        <td>
                            <span class="badge badge-info">{{ $apl->pendidikan_terakhir }}</span>
                        </td>
                    </tr>
                    @if($apl->nama_sekolah_terakhir)
                    <tr>
                        <td>Nama Sekolah/Universitas</td>
                        <td>{{ $apl->nama_sekolah_terakhir }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Data Pekerjaan Sekarang -->
        <div class="section-header-small">
            <h6 class="fw-bold mb-3 mt-4" style="color: var(--gray-900);">
                <span class="section-number-small">05</span>
                Data Pekerjaan Sekarang
            </h6>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="info-table w-100">
                    <tr>
                        <td>Kategori Pekerjaan</td>
                        <td>{{ $apl->kategori_pekerjaan }}</td>
                    </tr>
                    <tr>
                        <td>Nama Tempat Kerja</td>
                        <td>{{ $apl->nama_tempat_kerja }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>{{ $apl->jabatan }}</td>
                    </tr>
                    @if($apl->nama_jalan_kantor)
                    <tr>
                        <td>Alamat Kantor</td>
                        <td>{{ $apl->nama_jalan_kantor }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <table class="info-table w-100">
                    @if($apl->kotaKantor)
                    <tr>
                        <td>Kota/Kabupaten</td>
                        <td>{{ $apl->kotaKantor->name }}</td>
                    </tr>
                    @endif
                    @if($apl->provinsiKantor)
                    <tr>
                        <td>Provinsi Kantor</td>
                        <td>{{ $apl->provinsiKantor->name }}</td>
                    </tr>
                    @endif
                    @if($apl->negara_kantor)
                    <tr>
                        <td>Negara</td>
                        <td>{{ $apl->negara_kantor }}</td>
                    </tr>
                    @endif
                    @if($apl->no_telp_kantor)
                    <tr>
                        <td>No. Telepon Kantor</td>
                        <td>{{ $apl->no_telp_kantor }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Unit Kompetensi yang Dipilih -->
        <div class="section-header-small">
            <h6 class="fw-bold mb-3 mt-4" style="color: var(--gray-900);">
                <span class="section-number-small">06</span>
                Daftar Unit Kompetensi yang Dimohon
            </h6>
        </div>

        @if($apl->certificationScheme->activeUnitKompetensis && $apl->certificationScheme->activeUnitKompetensis->count() > 0)
            <div class="table-responsive mb-4">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 15%;">Kode Unit</th>
                            <th style="width: 55%;">Judul Unit Kompetensi</th>
                            <th class="text-center" style="width: 25%;">Standar Kompetensi Kerja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apl->certificationScheme->activeUnitKompetensis as $index => $unit)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="code-cell">{{ $unit->kode_unit }}</td>
                            <td class="title-cell">{{ $unit->judul_unit }}</td>
                            @if($index === 0)
                            <td class="text-center standard-cell" rowspan="{{ $apl->certificationScheme->activeUnitKompetensis->count() }}">
                                {{ $unit->standar_kompetensi_kerja ?? '-' }}
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-light border">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada unit kompetensi yang tersedia untuk skema ini.
            </div>
        @endif

        <!-- TUK & Tujuan Asesmen -->
        <div class="row mb-4">
            <!-- Tempat Uji Kompetensi -->
            <div class="col-md-6">
                <div class="section-header-small">
                    <h6 class="fw-bold mb-3" style="color: var(--gray-900);">
                        <span class="section-number-small">07</span>
                        Tempat Uji Kompetensi
                    </h6>
                </div>

                <table class="info-table w-100">
                    <tr>
                        <td>TUK</td>
                        <td>
                            @if($apl->tuk)
                                <span class="badge badge-primary">{{ $apl->tuk }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tujuan Asesmen -->
            <div class="col-md-6">
                <div class="section-header-small">
                    <h6 class="fw-bold mb-3" style="color: var(--gray-900);">
                        <span class="section-number-small">08</span>
                        Tujuan Asesmen
                    </h6>
                </div>

                <table class="info-table w-100">
                    <tr>
                        <td>Tujuan Asesmen</td>
                        <td>
                            <span class="badge badge-info">{{ $apl->tujuan_asesmen ?? '-' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dokumen Persyaratan -->
        @if($apl->selected_requirement_template_id && $apl->selectedRequirementTemplate)
        <div class="section-header-small">
            <h6 class="fw-bold mb-3 mt-4" style="color: var(--gray-900);">
                <span class="section-number-small">09</span>
                Dokumen Persyaratan
            </h6>
        </div>

        <div class="requirements-files mb-4">
            @if($apl->selectedRequirementTemplate->activeItems && $apl->selectedRequirementTemplate->activeItems->count() > 0)
                @php
                    $uploadedFiles = 0;
                    foreach($apl->selectedRequirementTemplate->activeItems as $item) {
                        if($item->type === 'file_upload' && $apl->hasRequirementFile($item->id)) {
                            $uploadedFiles++;
                        }
                    }
                @endphp
                
                @if($uploadedFiles > 0)
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>{{ $uploadedFiles }} dokumen</strong> telah diupload untuk persyaratan khusus.
                </div>

                <div class="row g-3">
                    @foreach($apl->selectedRequirementTemplate->activeItems as $item)
                        @if($item->type === 'file_upload' && $apl->hasRequirementFile($item->id))
                            @php
                                $fileUrl = $apl->getRequirementFileUrl($item->id);
                                $fileName = $apl->getRequirementFileName($item->id);
                                $fileSize = $apl->getRequirementFileSizeFormattedSafe($item->id);
                                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                            @endphp

                            <div class="col-md-4">
                                <div class="upload-card border rounded p-3 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                            <i class="bi bi-image text-success me-2 fs-5"></i>
                                        @elseif(strtolower($fileExtension) === 'pdf')
                                            <i class="bi bi-file-earmark-pdf text-danger me-2 fs-5"></i>
                                        @elseif(in_array(strtolower($fileExtension), ['doc', 'docx']))
                                            <i class="bi bi-file-earmark-word text-primary me-2 fs-5"></i>
                                        @else
                                            <i class="bi bi-file-earmark text-secondary me-2 fs-5"></i>
                                        @endif
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">{{ $item->document_name }}</div>
                                            <div class="text-muted small">{{ strtoupper($fileExtension) }} • {{ $fileSize }}</div>
                                        </div>
                                    </div>

                                    <div class="file-status mb-3">
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill"></i> Terupload
                                        </span>
                                    </div>

                                    <div class="file-actions d-flex gap-2">
                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary flex-grow-1">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                        <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @else
                <div class="alert alert-light border">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada dokumen yang diupload
                </div>
                @endif
            @else
                <div class="alert alert-light border">
                    <i class="bi bi-info-circle me-2"></i>
                    Tidak ada item persyaratan
                </div>
            @endif
        </div>
        @endif

        <!-- Pernyataan Pemohon -->
        <div class="section-header-small">
            <h6 class="fw-bold mb-3 mt-4" style="color: var(--gray-900);">
                <span class="section-number-small">10</span>
                Pernyataan Pemohon
            </h6>
        </div>

        <div class="declaration-box-small mb-3">
            <p class="mb-0">Dengan ini, saya menyatakan bahwa saya telah menuliskan nama lengkap dengan benar. 
            Nama tersebut dapat digunakan untuk pencetakan sertifikat kompetensi apabila saya dinyatakan 
            kompeten dalam proses asesmen ini.</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="info-table w-100">
                    <tr>
                        <td>Persetujuan</td>
                        <td>
                            @if($apl->pernyataan_benar)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill"></i> Disetujui
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-circle-fill"></i> Belum Disetujui
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Nama Lengkap</td>
                        <td>{{ $apl->nama_lengkap }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="signature-box">
                    <h6 class="fw-semibold mb-3">Tanda Tangan Digital Asesi</h6>
                    @php
                        $asesiSignature = $apl->getSignatureInfo('asesi');
                    @endphp
                    @if($asesiSignature['exists'] && $asesiSignature['path'])
                        <div class="signature-display">
                            @if($asesiSignature['is_base64'])
                                <img src="{{ $asesiSignature['path'] }}" 
                                     alt="Tanda tangan {{ $apl->nama_lengkap }}" 
                                     class="signature-image">
                            @else
                                <img src="{{ asset('storage/' . $asesiSignature['path']) }}" 
                                     alt="Tanda tangan {{ $apl->nama_lengkap }}" 
                                     class="signature-image">
                            @endif
                            @if($asesiSignature['formatted_date'])
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Ditandatangani pada: {{ $asesiSignature['formatted_date'] }}
                                </div>
                            @endif
                        </div>
                    @else
                        <span class="text-muted">Belum ditandatangani</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="signature-box">
                    <h6 class="fw-semibold mb-3">Tanda Tangan Digital Asesor</h6>
                    @php
                        $asesorSignature = $apl->getSignatureInfo('asesor');
                    @endphp
                    @if($asesorSignature['exists'] && $asesorSignature['path'])
                        <div class="signature-display">
                            @if($asesorSignature['is_base64'])
                                <img src="{{ $asesorSignature['path'] }}" 
                                     alt="Tanda tangan Asesor" 
                                     class="signature-image">
                            @else
                                <img src="{{ asset('storage/' . $asesorSignature['path']) }}" 
                                     alt="Tanda tangan Asesor" 
                                     class="signature-image">
                            @endif
                            @if($apl->nama_asesor)
                                <p class="small fw-semibold mt-2 mb-0">{{ $apl->nama_asesor }}</p>
                            @endif
                            @if($asesorSignature['formatted_date'])
                                <div class="text-muted small">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Ditandatangani pada: {{ $asesorSignature['formatted_date'] }}
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
        @if($apl->submitted_at)
        <div class="submission-info-small">
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

    </div>
</div>

<style>
    :root {
        --primary-color: #4f46e5;
        --primary-light: #818cf8;
        --gray-900: #111827;
        --gray-700: #374151;
        --gray-100: #f3f4f6;
    }

    .review-section {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .review-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .review-body {
        padding: 1.5rem;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .info-table td:first-child {
        font-weight: 600;
        color: var(--gray-700);
        width: 140px;
    }

    .info-table td:last-child {
        color: var(--gray-900);
    }

    .section-header-small {
        margin-bottom: 1rem;
    }

    .section-number-small {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-right: 0.75rem;
    }

    .badge-light {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .badge-info {
        background-color: #d1ecf1;
        color: #0c5460;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .badge-primary {
        background-color: #cfe2ff;
        color: #084298;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .modern-table thead {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
    }

    .modern-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .modern-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .modern-table .code-cell {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: var(--primary-color);
    }

    .modern-table .title-cell {
        color: var(--gray-900);
    }

    .modern-table .standard-cell {
        vertical-align: middle;
    }

    .declaration-box-small {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-left: 4px solid var(--primary-color);
        border-radius: 6px;
        padding: 1rem;
    }

    .declaration-box-small p {
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .upload-card {
        background: white;
        transition: all 0.3s ease;
    }

    .upload-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    .signature-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        height: 100%;
    }

    .signature-display {
        display: inline-block;
    }

    .signature-image {
        max-width: 200px;
        max-height: 100px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .submission-info-small {
        background: rgba(13, 110, 253, 0.1);
        border: 1px solid rgba(13, 110, 253, 0.2);
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>