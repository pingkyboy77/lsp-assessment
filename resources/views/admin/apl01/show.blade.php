@extends('layouts.admin')

@section('title', 'Detail APL 01 - ' . $apl->nomor_apl_01)

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
            <h2 class="mb-1">Detail APL 01</h2>
            <p class="text-muted mb-0">{{ $apl->nomor_apl_01 }} - {{ $apl->nama_lengkap }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.apl01.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.apl01.edit', $apl) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            @if(in_array($apl->status, ['submitted', 'review', 'reviewed']))
            <button class="btn btn-success" onclick="openReviewModal({{ $apl->id }})">
                <i class="bi bi-clipboard-check"></i> Review
            </button>
            @endif
            <button class="btn btn-outline-info" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Status and Info Cards -->
    <div class="row m-3">
        <div class="col-md-4">
            <div class="card border-0 bg-{{ $apl->statusColor ?? 'secondary' }} bg-opacity-10">
                <div class="card-body text-center">
                    <span class="badge bg-{{ $apl->statusColor ?? 'secondary' }} fs-6 px-3 py-2">
                        {{ $apl->statusText ?? ucfirst($apl->status) }}
                    </span>
                    <div class="mt-2">
                        <small class="text-muted">Status APL</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <div class="text-warning fs-6 fw-bold">
                        {{ $apl->submitted_at ? $apl->submitted_at->format('d/m/Y') : '-' }}
                    </div>
                    <small class="text-muted">Tanggal Submit</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-secondary bg-opacity-10">
                <div class="card-body text-center">
                    <div class="text-secondary fs-6 fw-bold">
                        {{ $apl->reviewer ? $apl->reviewer->name : '-' }}
                    </div>
                    <small class="text-muted">Reviewer</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Review History -->
    @if($apl->reviewed_at && $apl->notes)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="bi bi-chat-left-text"></i> Catatan Review</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="mb-2">{{ $apl->notes }}</p>
                        <small class="text-muted">
                            <i class="bi bi-person"></i> {{ $apl->reviewer->name ?? 'Unknown' }} • 
                            <i class="bi bi-clock"></i> {{ $apl->reviewed_at->format('d F Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Form Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Form Header -->
                    <div class="text-center mb-4">
                        <h3 class="text-primary">FR APL 01 - FORMULIR PERMOHONAN SERTIFIKASI PROFESI</h3>
                        @if($apl->certificationScheme)
                        <h5 class="text-secondary">{{ $apl->certificationScheme->nama }}</h5>
                        <p class="text-muted">{{ $apl->certificationScheme->jenjang }}</p>
                        @endif
                    </div>

                    <!-- Section 1: Data Pemohon -->
                    <div class="mb-5">
                        <h5 class="d-flex justify-content-start align-items-center">
                            <span class="badge bg-secondary me-2">01</span>
                            Rincian Data Pemohon Sertifikat
                        </h5>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%" class="fw-semibold">Nama Lengkap:</td>
                                        <td>{{ $apl->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">NIK:</td>
                                        <td>{{ $apl->nik }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Tempat Lahir:</td>
                                        <td>{{ $apl->tempat_lahir }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Tanggal Lahir:</td>
                                        <td>{{ $apl->tanggal_lahir?->format('d F Y') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Jenis Kelamin:</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $apl->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Kebangsaan:</td>
                                        <td>{{ $apl->kebangsaan }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%" class="fw-semibold">No. HP:</td>
                                        <td>{{ $apl->no_hp }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Email:</td>
                                        <td>{{ $apl->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">No. Telepon Rumah:</td>
                                        <td>{{ $apl->no_telp_rumah ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Pendidikan Terakhir:</td>
                                        <td>{{ $apl->pendidikan_terakhir }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Nama Sekolah:</td>
                                        <td>{{ $apl->nama_sekolah_terakhir }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Alamat Rumah -->
                    <div class="mb-5">
                        <h5 class="d-flex justify-content-start align-items-center">
                            <span class="badge bg-secondary me-2">02</span>
                            Data Alamat Rumah
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="20%" class="fw-semibold">Alamat:</td>
                                        <td>{{ $apl->alamat_rumah }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Kota/Kabupaten:</td>
                                        <td>{{ $apl->kotaRumah->name ?? $apl->kota_rumah ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Provinsi:</td>
                                        <td>{{ $apl->provinsiRumah->name ?? $apl->provinsi_rumah ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Kode Pos:</td>
                                        <td>{{ $apl->kode_pos ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Data Pekerjaan -->
                    <div class="mb-5">
                        <h5 class="d-flex justify-content-start align-items-center">
                            <span class="badge bg-secondary me-2">03</span>
                            Data Pekerjaan Sekarang
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%" class="fw-semibold">Nama Tempat Kerja:</td>
                                        <td>{{ $apl->nama_tempat_kerja }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Jabatan:</td>
                                        <td>{{ $apl->jabatan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Kategori Pekerjaan:</td>
                                        <td>{{ $apl->kategori_pekerjaan }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if($apl->nama_jalan_kantor)
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%" class="fw-semibold">Alamat Kantor:</td>
                                        <td>{{ $apl->nama_jalan_kantor }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Kota Kantor:</td>
                                        <td>{{ $apl->kotaKantor->name ?? $apl->kota_kantor ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Provinsi Kantor:</td>
                                        <td>{{ $apl->provinsiKantor->name ?? $apl->provinsi_kantor ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Kode Pos Kantor:</td>
                                        <td>{{ $apl->kode_pos_kantor ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">No. Telepon Kantor:</td>
                                        <td>{{ $apl->no_telp_kantor ?: '-' }}</td>
                                    </tr>
                                </table>
                                @else
                                <p class="text-muted">Data alamat kantor tidak diisi</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Data Asesmen -->
                    <div class="mb-5">
                        <h5 class="d-flex justify-content-start align-items-center">
                            <span class="badge bg-secondary me-2">04</span>
                            Data Asesmen
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%" class="fw-semibold">Tujuan Asesmen:</td>
                                        <td>{{ $apl->tujuan_asesmen }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">TUK:</td>
                                        <td>{{ $apl->tuk ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Kategori Peserta:</td>
                                        <td>
                                            <span class="badge bg-info">{{ $apl->kategori_peserta === 'individu' ? 'Individu / Mandiri' : 'Training Provider' }}</span>
                                        </td>
                                    </tr>
                                    @if($apl->lembagaPelatihan)
                                    <tr>
                                        <td class="fw-semibold">Lembaga Pelatihan:</td>
                                        <td>{{ $apl->lembagaPelatihan->name }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if($apl->aplikasi_yang_digunakan)
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%" class="fw-semibold">Aplikasi yang Digunakan:</td>
                                        <td>{{ is_array($apl->aplikasi_yang_digunakan) ? implode(', ', $apl->aplikasi_yang_digunakan) : $apl->aplikasi_yang_digunakan }}</td>
                                    </tr>
                                    @if($apl->bisa_share_screen)
                                    <tr>
                                        <td class="fw-semibold">Bisa Share Screen:</td>
                                        <td>{{ $apl->bisa_share_screen }}</td>
                                    </tr>
                                    @endif
                                    @if($apl->bisa_gunakan_browser)
                                    <tr>
                                        <td class="fw-semibold">Bisa Gunakan Browser:</td>
                                        <td>{{ $apl->bisa_gunakan_browser }}</td>
                                    </tr>
                                    @endif
                                </table>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Dokumen Persyaratan -->
                    @if($apl->requirement_answers && count($apl->requirement_answers) > 0)
                    <div class="mb-5">
                        <h5 class="d-flex justify-content-start align-items-center">
                            <span class="badge bg-secondary me-2">05</span>
                            Dokumen Persyaratan
                        </h5>
                        <hr>
                        <div class="row g-3">
                            @foreach($apl->requirement_answers as $itemId => $filePath)
                                @if($filePath && Storage::disk('public')->exists($filePath))
                                    @php
                                        $requirementItem = \App\Models\RequirementItem::find($itemId);
                                        $documentName = $requirementItem ? $requirementItem->document_name : "Dokumen $loop->iteration";
                                        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                                        $fileSize = Storage::disk('public')->size($filePath);
                                        $fileSizeFormatted = $fileSize > 1024 * 1024 
                                            ? round($fileSize / (1024 * 1024), 2) . ' MB' 
                                            : round($fileSize / 1024, 2) . ' KB';
                                    @endphp

                                    <div class="col-md-4">
                                        <div class="document-card h-100">
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
                                                    <div class="fw-semibold">{{ $documentName }}</div>
                                                    <div class="text-muted small">{{ strtoupper($fileExtension) }} • {{ $fileSizeFormatted }}</div>
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
                    @endif

                    <!-- Section 6: Dokumen Pengguna -->
                    @php
                        $userDocuments = \App\Models\UserDocument::where('user_id', $apl->user_id)->get();
                    @endphp
                    @if($userDocuments && $userDocuments->count() > 0)
                    <div class="mb-5">
                        <h5 class="d-flex justify-content-start align-items-center">
                            <span class="badge bg-secondary me-2">06</span>
                            Dokumen Pengguna
                        </h5>
                        <hr>
                        <div class="row g-3">
                            @foreach($userDocuments as $document)
                                @if($document->file_exists)
                                    @php
                                        $fileExtension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                                    @endphp

                                    <div class="col-md-4">
                                        <div class="document-card h-100">
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
                                                    <div class="fw-semibold">{{ $document->jenis_dokumen }}</div>
                                                    <div class="text-muted small">{{ strtoupper($fileExtension) }} • {{ $document->file_size_kb }} KB</div>
                                                </div>
                                            </div>

                                            <div class="file-status mb-3">
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle-fill"></i> Terupload
                                                </span>
                                            </div>

                                            <div class="file-actions d-flex gap-2">
                                                <a href="{{ $document->file_url }}" target="_blank" 
                                                   class="btn btn-sm btn-outline-primary flex-grow-1">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                                <a href="{{ $document->file_url }}" download 
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
                    @endif

                    <!-- Section 7: Digital Signature -->
                    @if($apl->tanda_tangan_asesi || $apl->tanda_tangan_asesor)
                    <div class="mb-5">
                        <h5 class="d-flex justify-content-start align-items-center">
                            <span class="badge bg-secondary me-2">07</span>
                            Tanda Tangan Digital
                        </h5>
                        <hr>
                        <div class="row">
                            @if($apl->tanda_tangan_asesi)
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Tanda Tangan Asesi</h6>
                                        <div class="signature-box mb-3">
                                            <img src="{{ $apl->tanda_tangan_asesi }}" alt="Tanda Tangan Asesi" class="img-fluid border" style="max-height: 150px;">
                                        </div>
                                        <small class="text-muted">
                                            Ditandatangani pada: {{ $apl->tanggal_tanda_tangan_asesi?->format('d F Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($apl->tanda_tangan_asesor)
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Tanda Tangan Asesor</h6>
                                        <div class="signature-box mb-3">
                                            <img src="{{ $apl->tanda_tangan_asesor }}" alt="Tanda Tangan Asesor" class="img-fluid border" style="max-height: 150px;">
                                        </div>
                                        <p class="mb-1"><strong>{{ $apl->nama_asesor }}</strong></p>
                                        <small class="text-muted">
                                            Ditandatangani pada: {{ $apl->tanggal_tanda_tangan_asesor?->format('d F Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Section 8: Additional Information -->
                    @if($apl->pernah_asesmen_lsp || $apl->nama_lengkap_ktp || $apl->pernah_aplikasi)
                    <div class="mb-5">
                        <h5 class="d-flex justify-content-start align-items-center">
                            <span class="badge bg-secondary me-2">08</span>
                            Pernyataan
                        </h5>
                        <hr>
                        <!-- Section 9: Pernyataan -->
                    @if($apl->pernyataan_benar)
                    <div class="mb-4">
                        
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                <div>
                                    <strong>Pernyataan Kebenaran Data</strong>
                                    <p class="mb-0">Saya menyatakan bahwa semua informasi yang telah saya berikan adalah benar dan dapat dipertanggungjawabkan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                        <table class="table table-borderless">
                            @if($apl->pernah_asesmen_lsp)
                            <tr>
                                <td width="25%" class="fw-semibold">Pernah Asesmen LSP:</td>
                                <td>{{ $apl->pernah_asesmen_lsp }}</td>
                            </tr>
                            @endif
                            @if($apl->pernah_aplikasi)
                            <tr>
                                <td class="fw-semibold">Pernah Aplikasi:</td>
                                <td>{{ $apl->pernah_aplikasi }}</td>
                            </tr>
                            @endif
                            @if($apl->nama_lengkap_ktp)
                            <tr>
                                <td class="fw-semibold">Nama Sesuai KTP:</td>
                                <td>{{ $apl->nama_lengkap_ktp }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @endif

                    

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review APL 01</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reviewModalBody">
                <!-- Content will be loaded here -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="flex-fill">
                    <textarea class="form-control" id="reviewNotes" placeholder="Catatan review (opsional untuk approve, wajib untuk reject)..." rows="3"></textarea>
                </div>
                <div class="d-flex flex-column gap-2 ms-3">
                    <button type="button" class="btn btn-success" onclick="processReview('approve')">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" onclick="processReview('reject')">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                    <button type="button" class="btn btn-warning" onclick="processReview('set_review')">
                        <i class="bi bi-eye"></i> Set Review
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="processReview('return')">
                        <i class="bi bi-arrow-return-left"></i> Return for Revision
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Print styles */
@media print {
    .btn, .form-actions, .modal, .d-flex.justify-content-between {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #000 !important;
    }
    
    .badge {
        border: 1px solid #000 !important;
        background: transparent !important;
        color: #000 !important;
    }
    
    .table td, .table th {
        border: 1px solid #000 !important;
    }
}

.signature-box {
    min-height: 120px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
}

.signature-box img {
    border-radius: 4px;
}

.document-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    background: #fafafa;
    transition: all 0.3s ease;
}

.document-card:hover {
    background: #f0f0f0;
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.15);
}

.file-icon {
    font-size: 2rem;
    margin-right: 0.5rem;
}

.toast-container {
    z-index: 1055;
}

.table-borderless td {
    padding: 0.5rem 0.75rem;
    vertical-align: top;
}

.badge.fs-6 {
    font-size: 0.9rem !important;
}

.border-bottom {
    border-bottom: 2px solid #dee2e6 !important;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
let currentAplId = {{ $apl->id }};

async function openReviewModal(aplId) {
    currentAplId = aplId;
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    const modalBody = document.getElementById('reviewModalBody');
    
    // Show loading
    modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat data review...</p>
        </div>
    `;
    
    modal.show();
    
    try {
        const response = await fetch(`{{ url('admin/apl01') }}/${aplId}/review-data`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            renderReviewData(result.data);
        } else {
            throw new Error(result.message || 'Failed to load review data');
        }
    } catch (error) {
        console.error('Review modal error:', error);
        modalBody.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                Error: ${error.message}
                <br><small class="text-muted mt-2 d-block">Silakan refresh halaman dan coba lagi.</small>
            </div>
        `;
    }
}

function renderReviewData(data) {
    const modalBody = document.getElementById('reviewModalBody');
    const apl = data.apl;
    
    let documentsHtml = '';
    
    // User Documents
    if (data.user_documents && data.user_documents.length > 0) {
        documentsHtml += `
            <div class="mb-4">
                <h6 class="border-bottom pb-2"><i class="bi bi-person-badge"></i> Dokumen Pengguna</h6>
                <div class="row g-3">
        `;
        
        data.user_documents.forEach(doc => {
            const iconClass = getFileIcon(doc.file_extension);
            documentsHtml += `
                <div class="col-md-6">
                    <div class="document-card">
                        <div class="d-flex align-items-center">
                            <i class="${iconClass} file-icon"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${escapeHtml(doc.jenis_dokumen)}</div>
                                <small class="text-muted">
                                    ${doc.file_extension.toUpperCase()} • ${doc.file_size_kb} KB
                                </small>
                            </div>
                            <div class="ms-2">
                                ${doc.file_exists ? 
                                    `<a href="${escapeHtml(doc.file_url)}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>` : 
                                    '<span class="badge bg-danger">File Tidak Ada</span>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        documentsHtml += `
                </div>
            </div>
        `;
    }
    
    // Requirement Documents
    if (data.requirement_documents && data.requirement_documents.length > 0) {
        documentsHtml += `
            <div class="mb-4">
                <h6 class="border-bottom pb-2"><i class="bi bi-file-earmark-check"></i> Dokumen Persyaratan</h6>
                <div class="row g-3">
        `;
        
        data.requirement_documents.forEach(doc => {
            const iconClass = getFileIcon(doc.file_extension);
            const fileSize = (doc.file_size / 1024).toFixed(2);
            
            documentsHtml += `
                <div class="col-md-6">
                    <div class="document-card">
                        <div class="d-flex align-items-center">
                            <i class="${iconClass} file-icon"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${escapeHtml(doc.item_name)}</div>
                                <small class="text-muted">
                                    ${doc.file_extension.toUpperCase()} • ${fileSize} KB
                                </small>
                            </div>
                            <div class="ms-2">
                                <a href="${escapeHtml(doc.file_url)}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        documentsHtml += `
                </div>
            </div>
        `;
    }
    
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-person-circle"></i> Informasi Peserta</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-semibold">No. APL:</td>
                                <td>${escapeHtml(apl.nomor_apl_01 || 'DRAFT')}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Nama:</td>
                                <td>${escapeHtml(apl.nama_lengkap)}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Email:</td>
                                <td>${escapeHtml(apl.email)}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">No. HP:</td>
                                <td>${escapeHtml(apl.no_hp)}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Status:</td>
                                <td><span class="badge bg-info">${escapeHtml(apl.status_text)}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Submitted:</td>
                                <td>${apl.submitted_at || '-'}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Skema:</td>
                                <td>${escapeHtml(apl.certification_scheme || '-')}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-files"></i> Dokumen</h6>
                        ${documentsHtml || '<p class="text-muted">Tidak ada dokumen yang diupload.</p>'}
                    </div>
                </div>
            </div>
        </div>
    `;
}

function getFileIcon(extension) {
    const ext = extension.toLowerCase();
    switch(ext) {
        case 'pdf': return 'bi bi-file-earmark-pdf text-danger';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif': return 'bi bi-file-earmark-image text-success';
        case 'doc':
        case 'docx': return 'bi bi-file-earmark-word text-primary';
        case 'xls':
        case 'xlsx': return 'bi bi-file-earmark-excel text-success';
        default: return 'bi bi-file-earmark text-secondary';
    }
}

async function processReview(action) {
    const notes = document.getElementById('reviewNotes').value;
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Validate required notes for reject and return actions
    if ((action === 'reject' || action === 'return') && !notes.trim()) {
        alert('Catatan wajib diisi untuk aksi ini.');
        return;
    }
    
    // Show loading
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    try {
        let endpoint = '';
        switch(action) {
            case 'approve':
                endpoint = `{{ url('admin/apl01') }}/${currentAplId}/approve`;
                break;
            case 'reject':
                endpoint = `{{ url('admin/apl01') }}/${currentAplId}/reject`;
                break;
            case 'set_review':
                endpoint = `{{ url('admin/apl01') }}/${currentAplId}/set-review`;
                break;
            case 'return':
                endpoint = `{{ url('admin/apl01') }}/${currentAplId}/return-revision`;
                break;
        }
        
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notes: notes })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
            
            // Show success message
            showToast('success', result.message);
            
            // Reload page to reflect changes
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(result.message || 'Action failed');
        }
    } catch (error) {
        console.error('Review action error:', error);
        showToast('error', 'Gagal memproses review: ' + error.message);
    } finally {
        button.disabled = false;
        button.innerHTML = originalContent;
    }
}

function showToast(type, message) {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast element
    const toastId = 'toast_' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${escapeHtml(message)}
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
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { 
        delay: type === 'success' ? 3000 : 5000 
    });
    toast.show();
    
    // Remove from DOM after hide
    toastElement.addEventListener('hidden.bs.toast', () => {
        if (toastElement && toastElement.parentNode) {
            toastElement.parentNode.removeChild(toastElement);
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Handle modal cleanup on close
document.getElementById('reviewModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('reviewNotes').value = '';
    document.getElementById('reviewModalBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
});
</script>
@endpush