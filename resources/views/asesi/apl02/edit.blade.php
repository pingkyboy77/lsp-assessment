{{-- resources/views/asesi/apl02/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit APL 02 - Self Assessment')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.css" rel="stylesheet">
    @include('asesi.apl02.partials.styles.assessment-form')
@endpush

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
                <h4 class="m-0">APL 02 - SELF ASSESSMENT</h4>
                <small class="text-light">{{ $apl02->certificationScheme->nama }}</small>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-{{ $apl02->status_color ?? 'secondary' }} fs-6">{{ $apl02->status_text }}</span>
            </div>
        </div>

        <!-- Assessment Form -->
        <form id="assessmentForm" method="POST" action="{{ route('asesi.apl02.update', $apl02) }}"
            enctype="multipart/form-data">
            @csrf

            <!-- Assessment Sections by Unit -->
            @foreach ($assessmentData as $index => $unitData)
                <div class="card mb-4 m-3" data-unit-id="{{ $unitData['unit']->id }}">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-book me-2"></i>
                            {{ $unitData['unit']->kode_unit }} - {{ ucwords(strtolower($unitData['unit']->judul_unit)) }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach ($unitData['elements'] as $elementData)
                            <div class="element-assessment mb-4 p-3 border rounded bg-white shadow-sm"
                                data-element-id="{{ $elementData['element']->id }}"
                                data-unit-id="{{ $unitData['unit']->id }}">

                                <div class="row g-3">
                                    {{-- LEFT: Elemen + Kriteria (col-md-6) --}}
                                    <div class="col-md-5">
                                        {{-- judul elemen --}}
                                        <h5 class="fw-bold mb-3"
                                            style="text-align: justify; font-size: 1rem; line-height:1.4;">
                                            {{ $elementData['element']->judul_elemen }}
                                        </h5>

                                        {{-- kriteria --}}
                                        <div class="criteria-list ms-1">
                                            <h6 class="fw-semibold text-secondary mb-2" style="font-size: 0.8rem;">Kriteria
                                                Unjuk Kerja:</h6>
                                            <ol class="ps-3"
                                                style="text-align: justify; line-height: 1.6; font-size: 0.8rem;">
                                                @foreach ($elementData['criterias'] as $criteria)
                                                    <li class="mb-2">
                                                        {{ $criteria->uraian_kriteria }}
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>

                                    {{-- MIDDLE: Penilaian Diri (col-md-3) --}}
                                    <div class="col-md-3 border-start ps-4 d-flex flex-column justify-content-start">
                                        <label class="form-label fw-bold mb-3">Penilaian Diri</label>

                                        @php
                                            $saved = old(
                                                'assessment.' . $elementData['element']->id,
                                                optional($elementData['assessment'])->assessment_result,
                                            );
                                        @endphp

                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="assessment[{{ $elementData['element']->id }}]"
                                                    id="kompeten_{{ $elementData['element']->id }}" value="kompeten"
                                                    {{ $saved === 'kompeten' ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-success ms-2"
                                                    for="kompeten_{{ $elementData['element']->id }}"
                                                    style="font-size: 1rem;">
                                                    Kompeten
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="assessment[{{ $elementData['element']->id }}]"
                                                    id="belum_kompeten_{{ $elementData['element']->id }}"
                                                    value="belum_kompeten"
                                                    {{ $saved === 'belum_kompeten' ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-danger ms-2"
                                                    for="belum_kompeten_{{ $elementData['element']->id }}"
                                                    style="font-size: 1rem;">
                                                    Belum Kompeten
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- RIGHT: Evidence Portfolio (col-md-3) --}}
                                    <div class="col-md-4 border-start ps-4">
                                        @if ($unitData['portfolio_files']->count() > 0)
                                            <div class="element-evidence-section"
                                                data-element-id="{{ $elementData['element']->id }}">
                                                <h6 class="text-info mb-3" style="font-size: 0.95rem;">
                                                    <i class="bi bi-paperclip me-1"></i>
                                                    Bukti Portfolio:
                                                </h6>
                                                <div class="evidence-list">
                                                    @foreach ($unitData['portfolio_files'] as $portfolioFile)
                                                        @php
                                                            // Check if this portfolio file has uploaded evidence
                                                            $hasEvidence = isset($existingEvidence[$portfolioFile->id]);
                                                        @endphp
                                                        <label
                                                            class="custom-check d-block mb-2 p-2 border rounded {{ $hasEvidence ? 'border-success bg-success bg-opacity-10' : '' }}">
                                                            <div class="d-flex align-items-center">
                                                                <input type="checkbox"
                                                                    class="form-check-input evidence-checkbox me-2"
                                                                    id="evidence_portfolio_{{ $portfolioFile->id }}"
                                                                    data-portfolio-id="{{ $portfolioFile->id }}"
                                                                    data-element-id="{{ $elementData['element']->id }}"
                                                                    data-unit-id="{{ $unitData['unit']->id }}"
                                                                    data-document-name="{{ $portfolioFile->document_name }}"
                                                                    {{ $hasEvidence ? 'checked' : '' }}>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold" style="font-size: 0.85rem;">
                                                                        {{ $portfolioFile->document_name }}</div>
                                                                    @if ($hasEvidence)
                                                                        <small class="text-success">
                                                                            <i class="bi bi-check-circle me-1"></i>Sudah
                                                                            Upload:
                                                                            {{ $existingEvidence[$portfolioFile->id]['file_name'] }}
                                                                        </small>
                                                                    @else
                                                                        <small class="text-muted">
                                                                            <i class="bi bi-circle me-1"></i>Belum Upload
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Selected Evidence Upload Section -->
            <div class="card mb-4 m-3" id="selectedEvidenceSection" style="display: none;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-upload me-2"></i>
                        UPLOAD BUKTI YANG DIPILIH
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Cara kerja:</strong> Pilih file untuk setiap dokumen yang diperlukan, kemudian tekan tombol
                        "Simpan & Submit" untuk mengupload semua file sekaligus dan mengirim assessment.
                    </div>
                    <div class="row" id="selectedEvidenceList">
                        <!-- Selected evidence items will be populated here by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Digital Signature Section -->
            <div class="card mb-4 m-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-pen me-2"></i>
                        TANDA TANGAN DIGITAL
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            {{-- Check for existing signature using correct field name --}}
                            @if ($apl02->tanda_tangan_asesi)
                                <div class="existing-signature mb-3">
                                    <div class="d-flex align-items-center p-3 border rounded bg-success bg-opacity-10">
                                        <div class="signature-preview me-3">
                                            <img src="{{ Storage::url($apl02->tanda_tangan_asesi) }}"
                                                alt="Tanda tangan asesi" class="signature-display">
                                        </div>

                                        <div class="signature-info flex-grow-1">
                                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                                            <strong>Tanda tangan sudah tersimpan</strong>
                                            @if ($apl02->tanggal_tanda_tangan_asesi)
                                                <small class="d-block text-muted">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $apl02->tanggal_tanda_tangan_asesi->format('d M Y H:i') }}
                                                </small>
                                            @endif
                                            @if ($apl02->ip_tanda_tangan_asesi)
                                                <small class="d-block text-muted">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    IP: {{ $apl02->ip_tanda_tangan_asesi }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Error fallback - outside the main container --}}
                                    <div class="alert alert-warning mt-2" style="display: none;">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        <strong>Tanda tangan tidak dapat ditampilkan</strong>
                                        <br>
                                        <small class="text-muted">Path: {{ $apl02->tanda_tangan_asesi }}</small>
                                        <br>
                                        <small class="text-muted">Full URL: {{ $apl02->getSignatureUrl() }}</small>
                                    </div>

                                    {{-- Button to clear existing signature --}}
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-sm"
                                            id="clearExistingSignature">
                                            <i class="bi bi-trash me-1"></i>Hapus & Buat Ulang
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="existing_signature" value="1">
                            @else
                                <div class="form-group">
                                    <label class="form-label required">Tanda Tangan Digital</label>
                                    <div class="signature-pad-wrapper position-relative border rounded"
                                        style="height:200px;">
                                        <canvas id="signature-canvas" class="w-100 h-100"
                                            style="cursor: crosshair;"></canvas>
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

                                    <button type="button" id="clear-signature"
                                        class="btn btn-outline-secondary btn-sm mt-2">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear
                                    </button>

                                    @error('signature')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            {{-- Hidden signature pad for re-signing --}}
                            <div id="new-signature-pad" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label required">Tanda Tangan Digital Baru</label>
                                    <div class="signature-pad-wrapper position-relative border rounded"
                                        style="height:200px;">
                                        <canvas id="new-signature-canvas" class="w-100 h-100"
                                            style="cursor: crosshair;"></canvas>
                                        <div id="new-signature-placeholder"
                                            class="position-absolute top-50 start-50 translate-middle text-muted small d-flex align-items-center justify-content-center pointer-events-none">
                                            <i class="bi bi-pencil me-1"></i>
                                            <span>Klik dan tahan untuk menggambar tanda tangan baru</span>
                                        </div>
                                    </div>

                                    <div class="signature-tips text-muted small mt-1">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Tips: Gunakan mouse atau jari untuk menggambar tanda tangan
                                    </div>

                                    <div class="mt-2">
                                        <button type="button" id="clear-new-signature"
                                            class="btn btn-outline-secondary btn-sm me-2">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Clear
                                        </button>
                                        <button type="button" id="cancel-new-signature"
                                            class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-x me-1"></i> Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle me-1"></i> Petunjuk Tanda Tangan Digital:</h6>
                                <ul class="mb-0 small">
                                    <li>Gunakan mouse atau layar sentuh untuk menggambar tanda tangan</li>
                                    <li>Pastikan tanda tangan jelas dan mudah dibaca</li>
                                    <li>Klik tombol "Clear" jika ingin mengulangi</li>
                                    <li>Tanda tangan akan disimpan otomatis saat submit</li>
                                    <li>Tanda tangan disimpan di folder: signatures/[nama_asesi]/</li>
                                </ul>
                            </div>

                            @if ($apl02->tanda_tangan_asesi)
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>Tanda tangan sudah tersedia.</strong>
                                    Anda dapat menghapus dan membuat ulang jika diperlukan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mb-4 m-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('asesi.apl02.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" onclick="saveAssessmentWithFiles()" class="btn btn-primary">
                                Simpan & Submit Assessment
                            </button>

                            {{-- <a href="{{ route('asesi.apl02.preview', $apl02) }}" class="btn btn-outline-info"
                                target="_blank">
                                <i class="bi bi-eye me-1"></i>Preview
                            </a> --}}
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Setelah submit, Anda tidak dapat mengubah assessment ini lagi.
                            Pastikan semua data sudah benar sebelum submit.
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modals remain the same -->
    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0">Menyimpan data dan mengupload file...</p>
                    <small class="text-muted">Mohon tunggu, jangan tutup halaman ini</small>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="filePreviewContent" class="text-center">
                        <!-- File preview content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" class="btn btn-primary" id="downloadFileBtn" target="_blank">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-conteant">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="bi bi-exclamation-triangle display-1 text-warning mb-3"></i>
                        <h5>Yakin ingin menghapus bukti ini?</h5>
                        <p class="text-muted">File <span id="deleteFileName"></span> akan dihapus permanen dan tidak dapat
                            dikembalikan.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    @include('asesi.apl02.partials.scripts.assessment-form', ['apl02' => $apl02])
@endpush
