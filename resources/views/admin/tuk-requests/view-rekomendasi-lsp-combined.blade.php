{{-- resources/views/admin/tuk-requests/view-rekomendasi-lsp-combined.blade.php --}}

<div class="ttd-rekomendasi-view-container">
    <!-- Alert - Compact -->
    <div class="alert alert-success mb-3 py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-patch-check-fill me-2" style="font-size: 1.5rem;"></i>
            <div>
                <strong class="d-block mb-0">Rekomendasi LSP Sudah Ditandatangani</strong>
                <small class="text-muted">
                    Oleh: <strong>{{ $apl01->rekomendasiLsp->admin->name }}</strong> 
                    • {{ $apl01->rekomendasiLsp->formatted_tanggal_ttd }}
                </small>
            </div>
        </div>
    </div>

    <!-- Navigation Pills - Compact -->
    <ul class="nav nav-pills review-tabs-compact mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="view-apl01-tab" data-bs-toggle="pill" 
                    data-bs-target="#view-apl01-content" type="button" role="tab">
                <i class="bi bi-file-earmark-text me-1"></i>APL 01
            </button>
        </li>
        @if($apl01->apl02)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="view-apl02-tab" data-bs-toggle="pill" 
                        data-bs-target="#view-apl02-content" type="button" role="tab">
                    <i class="bi bi-clipboard-check me-1"></i>APL 02
                </button>
            </li>
        @endif
    </ul>

    <!-- Tab Content - Compact with Fixed Height -->
    <div class="tab-content mb-3">
        <div class="tab-pane fade show active" id="view-apl01-content" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 content-scroll-area">
                    @include('admin.tuk-requests.partials.apl01-content', ['apl' => $apl01])
                    @include('admin.tuk-requests.partials.user-documents-section', ['apl' => $apl01])
                </div>
            </div>
        </div>

        @if($apl01->apl02)
            <div class="tab-pane fade" id="view-apl02-content" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 content-scroll-area">
                        @include('admin.tuk-requests.partials.apl02-content', ['apl02' => $apl01->apl02])
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Rekomendasi LSP Result - Clean Layout -->
    <div class="card border-success">
        <div class="card-header bg-success text-white py-2">
            <h6 class="mb-0 fs-6"><i class="bi bi-award me-2"></i>Rekomendasi LSP</h6>
        </div>
        <div class="card-body p-3">
            <!-- Rekomendasi Result -->
            <div class="mb-3">
                <label class="form-label fw-semibold small mb-2">Keputusan Rekomendasi:</label>
                <div class="rekomendasi-result">
                    @if($apl01->rekomendasiLsp->rekomendasi_text)
                        @if(str_contains($apl01->rekomendasiLsp->rekomendasi_text, 'Diterima'))
                            <div class="alert alert-success mb-0 py-2">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>{{ $apl01->rekomendasiLsp->rekomendasi_text }}</strong>
                            </div>
                        @else
                            <div class="alert alert-danger mb-0 py-2">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                <strong>{{ $apl01->rekomendasiLsp->rekomendasi_text }}</strong>
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Tidak ada catatan rekomendasi</p>
                    @endif
                </div>
            </div>

            <!-- Admin Info & Signature in Row -->
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="admin-info-box">
                        <div class="mb-2">
                            <label class="form-label fw-semibold small mb-1">Admin LSP</label>
                            <div class="info-display">{{ $apl01->rekomendasiLsp->admin->name }}</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold small mb-1">NIK Admin</label>
                            <div class="info-display">{{ $apl01->rekomendasiLsp->admin_nik }}</div>
                        </div>
                        <div>
                            <label class="form-label fw-semibold small mb-1">Tanggal TTD</label>
                            <div class="info-display">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $apl01->rekomendasiLsp->formatted_tanggal_ttd }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-7">
                    <div class="signature-display-section">
                        <label class="form-label fw-semibold small mb-2 d-block">
                            Tanda Tangan Digital
                        </label>
                        <div class="signature-display-wrapper">
                            @if($apl01->rekomendasiLsp->ttd_admin_path)
                                <img src="{{ asset('storage/' . $apl01->rekomendasiLsp->ttd_admin_path) }}" 
                                     alt="Tanda Tangan Admin" 
                                     class="signature-image">
                            @else
                                <div class="no-signature">
                                    <i class="bi bi-exclamation-circle text-muted"></i>
                                    <p class="text-muted mb-0 mt-2">Tanda tangan tidak tersedia</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Button -->
            <div class="mt-3 pt-3 border-top d-flex justify-content-end">
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="editRekomendasiLSP({{ $apl01->id }})">
                    <i class="bi bi-pencil me-1"></i>Edit Rekomendasi
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .ttd-rekomendasi-view-container {
        max-height: 85vh;
    }

    /* Custom Scrollbar */
    .ttd-rekomendasi-view-container::-webkit-scrollbar {
        width: 6px;
    }

    .ttd-rekomendasi-view-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .ttd-rekomendasi-view-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .ttd-rekomendasi-view-container::-webkit-scrollbar-thumb:hover {
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

    /* Content Scroll Area */
    .content-scroll-area {
        /* max-height: 700px;
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

    /* Rekomendasi Result */
    .rekomendasi-result .alert {
        font-size: 0.9rem;
    }

    /* Admin Info Box */
    .admin-info-box {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        height: 100%;
    }

    .info-display {
        background: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        font-size: 0.875rem;
        color: #212529;
    }

    /* Signature Display Section - Matching Validated Page */
    .signature-display-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .signature-display-wrapper {
        position: relative;
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .signature-image {
        max-width: 100%;
        max-height: 120px;
        object-fit: contain;
        display: block;
    }

    .no-signature {
        text-align: center;
        padding: 2rem 1rem;
    }

    .no-signature i {
        font-size: 2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .review-tabs-compact .nav-link {
            font-size: 0.75rem;
            padding: 0.4rem 0.5rem;
        }
        
        .signature-display-wrapper {
            min-height: 120px;
        }
        
        .content-scroll-area {
            max-height: 200px;
        }
    }
</style>