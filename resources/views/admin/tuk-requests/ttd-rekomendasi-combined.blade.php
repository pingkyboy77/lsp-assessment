{{-- resources/views/admin/tuk-requests/ttd-rekomendasi-combined.blade.php --}}

<div class="ttd-rekomendasi-container">
    <!-- Alert Header - Compact -->
    <div class="alert alert-success mb-3 py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
            <div>
                <strong class="d-block mb-0">Dokumen APL Sudah Disetujui</strong>
                <small class="text-muted">Silakan berikan rekomendasi LSP dengan tanda tangan digital</small>
            </div>
        </div>
    </div>


    <!-- Navigation Pills - Compact -->
    <ul class="nav nav-pills review-tabs-compact mb-3" id="ttdReviewTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ttd-apl01-tab" data-bs-toggle="pill" data-bs-target="#ttd-apl01-content"
                type="button" role="tab">
                <i class="bi bi-file-earmark-text me-1"></i>APL 01
            </button>
        </li>
        @if ($apl01->apl02)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ttd-apl02-tab" data-bs-toggle="pill" data-bs-target="#ttd-apl02-content"
                    type="button" role="tab">
                    <i class="bi bi-clipboard-check me-1"></i>APL 02
                </button>
            </li>
        @endif
    </ul>

    <!-- Tab Content - Compact with Fixed Height -->
    <div class="tab-content mb-3" id="ttdReviewTabsContent">
        <div class="tab-pane fade show active" id="ttd-apl01-content" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 content-scroll-area">
                    @include('admin.tuk-requests.partials.apl01-content', ['apl' => $apl01])
                    @include('admin.tuk-requests.partials.user-documents-section', ['apl' => $apl01])
                </div>
            </div>
        </div>

        @if ($apl01->apl02)
            <div class="tab-pane fade" id="ttd-apl02-content" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 content-scroll-area">
                        @include('admin.tuk-requests.partials.apl02-content', ['apl02' => $apl01->apl02])
                    </div>
                </div>
            </div>
        @endif

        {{-- <div class="tab-pane fade" id="ttd-documents-content" role="tabpanel">
            <div class="content-scroll-area">
                @include('admin.tuk-requests.partials.user-documents-section', ['apl' => $apl01])
            </div>
        </div> --}}
    </div>

    <!-- Form Rekomendasi LSP - Compact & Clean -->
    <div class="card border-success">
        <div class="card-header bg-success text-white py-2">
            <h6 class="mb-0 fs-6"><i class="bi bi-award me-2"></i>Form Rekomendasi LSP</h6>
        </div>
        <div class="card-body p-3">
            <!-- Rekomendasi Options -->
            <div class="mb-3">
                <label class="form-label fw-semibold small mb-2">
                    Rekomendasi: <span class="text-danger">*</span>
                </label>
                <div class="rekomendasi-options">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="rekomendasi_text" id="statusDiterima"
                            value="Diterima sebagai peserta sertifikasi" required>
                        <label class="form-check-label" for="statusDiterima">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Diterima sebagai peserta sertifikasi
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rekomendasi_text" id="statusTidakDiterima"
                            value="Tidak Diterima sebagai peserta sertifikasi" required>
                        <label class="form-check-label" for="statusTidakDiterima">
                            <i class="bi bi-x-circle text-danger me-1"></i>
                            Tidak Diterima sebagai peserta sertifikasi
                        </label>
                    </div>
                </div>
            </div>

            <!-- Admin Info & Signature in Row -->
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="admin-info-box">
                        <div class="mb-2">
                            <label class="form-label fw-semibold small mb-1">Admin LSP</label>
                            <input type="text" class="form-control form-control-sm" value="{{ Auth::user()->name }}"
                                readonly>
                        </div>
                        <div>
                            <label class="form-label fw-semibold small mb-1">NIK</label>
                            <input type="text" class="form-control form-control-sm"
                                value="{{ Auth::user()->id_number }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="signature-section">
                        <label class="form-label fw-semibold small mb-2 d-block">
                            Tanda Tangan Digital <span class="text-danger">*</span>
                        </label>
                        <div class="signature-wrapper">
                            <canvas id="rekomendasiSignaturePad" class="signature-canvas"></canvas>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>Tanda tangan di area putih
                            </small>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="clearRekomendasiSignature()">
                                <i class="bi bi-eraser me-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Input -->
    <input type="hidden" id="apl01IdForRekomendasi" value="{{ $apl01->id }}">
</div>

<style>
    .ttd-rekomendasi-container {
        max-height: 85vh;
    }

    /* Custom Scrollbar */
    .ttd-rekomendasi-container::-webkit-scrollbar {
        width: 6px;
    }

    .ttd-rekomendasi-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .ttd-rekomendasi-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .ttd-rekomendasi-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Info Items - Compact */
    .info-item {
        display: flex;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        min-width: 80px;
    }

    .info-value {
        color: #212529;
    }

    /* Compact Tabs */
    .review-tabs-compact {
        display: flex;
        gap: 0.5rem;
        flex-wrap: nowrap;
    }

    .review-tabs-compact .nav-item {
        flex: 1;
    }

    .review-tabs-compact .nav-link {
        width: 100%;
        text-align: center;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        font-size: 0.85rem;
        color: #6c757d;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .review-tabs-compact .nav-link.active {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
    }

    .review-tabs-compact .nav-link:hover:not(.active) {
        background: #e9ecef;
        color: #0d6efd;
    }

    .review-tabs-compact .nav-link .badge {
        font-size: 0.65rem;
        padding: 0.2em 0.4em;
    }

    /* Content Scroll Area with Fixed Height */
    .content-scroll-area {
        /* max-height: 500px;
        overflow-y: auto;
        padding-right: 8px; */
    }

    .content-scroll-area::-webkit-scrollbar {
        width: 5px;
    }

    .content-scroll-area::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .content-scroll-area::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }

    .content-scroll-area::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* Rekomendasi Options */
    .rekomendasi-options {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .rekomendasi-options .form-check-label {
        font-size: 0.9rem;
        cursor: pointer;
    }

    /* Admin Info Box */
    .admin-info-box {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        height: 100%;
    }

    /* Signature Section - Matching Validated Page Style */
    .signature-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .signature-wrapper {
        position: relative;
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 0;
        overflow: hidden;
    }

    .signature-canvas {
        display: block;
        width: 100%;
        height: 150px;
        cursor: crosshair;
        touch-action: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .review-tabs-compact .nav-link {
            font-size: 0.75rem;
            padding: 0.4rem 0.5rem;
        }

        .signature-canvas {
            height: 120px;
        }

        .content-scroll-area {
            max-height: 200px;
        }
    }
</style>

<script>
    // Ganti nama agar tidak bentrok
    function openTTDRekomendasiModalCombined(apl01Id) {
        const modal = new bootstrap.Modal(document.getElementById('reviewAplModal'));

        $('#reviewAplModalTitle').html(
            '<i class="bi bi-pencil-square me-2"></i>Tanda Tangan Rekomendasi LSP'
        );

        $('#reviewAplModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted">Memuat form rekomendasi...</p>
            </div>
        `);

        $('#btnApprove, #btnReject').hide();
        $('.modal-footer .btn-group').hide();
        $('#approvalNotesSection, #rejectionNotesSection').hide();
        $('#completionStatusInfo').hide();

        modal.show();

        fetch(`/admin/tuk-requests/${apl01Id}/ttd-rekomendasi-combined`)
            .then(response => response.text())
            .then(html => {
                $('#reviewAplModalBody').html(html);

                $('#completionStatusInfo').hide();
                $('.modal-footer').html(`
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </button>
                            <button type="button" class="btn btn-success" onclick="submitRekomendasiFromModal()">
                                <i class="bi bi-check-circle me-1"></i>Simpan Rekomendasi
                            </button>
                        </div>
                    </div>
                `);

                setTimeout(initRekomendasiSignaturePad, 100);
            })
            .catch(error => {
                $('#reviewAplModalBody').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading form: ${error.message}
                    </div>
                `);
            });
    }
</script>
