@extends('layouts.admin')

@section('title', 'TUK Mandiri - Panduan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="main-card">
        <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="bi bi-house-check me-2"></i>TUK Mandiri - Panduan dan Informasi
                </h4>
                <p class="text-muted mb-0">{{ $apl01->certificationScheme->nama ?? 'Skema Sertifikasi' }}</p>
            </div>
            <a href="{{ route('asesi.apl01.show', $apl01->id) }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke APL 01
            </a>
        </div>

        <!-- APL01 Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Informasi APL 01</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Nomor APL 01:</strong><br>
                                {{ $apl01->nomor_apl_01 }}
                            </div>
                            <div class="col-md-3">
                                <strong>Nama Peserta:</strong><br>
                                {{ $apl01->nama_lengkap }}
                            </div>
                            <div class="col-md-3">
                                <strong>Skema Sertifikasi:</strong><br>
                                {{ $apl01->certificationScheme->nama ?? '-' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Jenis TUK:</strong><br>
                                <span class="badge bg-success">TUK MANDIRI</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TUK Mandiri Content -->
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-file-pdf me-2"></i>Panduan TUK Mandiri
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <!-- PDF Embed -->
                        <div id="pdfViewer" class="w-100" style="height: 800px;">
                            <!-- Placeholder PDF - replace with actual PDF URL when available -->
                            <embed src="{{ asset('pdfs/tuk-mandiri-panduan.pdf') }}" 
                                   type="application/pdf" 
                                   width="100%" 
                                   height="100%"
                                   id="pdfEmbed">
                            
                            <!-- Fallback if PDF not found -->
                            <div id="pdfFallback" class="d-none p-5 text-center">
                                <div class="mb-4">
                                    <i class="bi bi-file-pdf text-danger" style="font-size: 4rem;"></i>
                                </div>
                                <h5>Panduan TUK Mandiri</h5>
                                <p class="text-muted">Panduan PDF belum tersedia. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                                <hr class="my-4">
                                
                                <!-- Temporary hardcoded content -->
                                <div class="text-start">
                                    <h6 class="text-primary">Informasi TUK Mandiri:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            <strong>TUK Mandiri</strong> adalah tempat uji kompetensi yang diselenggarakan di tempat kerja atau fasilitas milik peserta sendiri
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            Peserta bertanggung jawab menyediakan fasilitas dan peralatan yang diperlukan
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            Assessment dilakukan sesuai dengan standar yang ditetapkan LSP-PM
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            Tidak memerlukan form permohonan TUK Sewaktu
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Panduan ini berisi informasi lengkap tentang persyaratan dan prosedur TUK Mandiri
                            </small>
                            <div>
                                <button class="btn btn-outline-primary btn-sm" onclick="printPDF()">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
                                <a href="{{ asset('pdfs/tuk-mandiri-panduan.pdf') }}" 
                                   class="btn btn-outline-success btn-sm" 
                                   download="TUK_Mandiri_Panduan.pdf">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Status TUK
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Jenis TUK:</strong><br>
                            <span class="badge bg-success">TUK MANDIRI</span>
                        </div>

                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle me-2"></i>TUK Mandiri Aktif</h6>
                            <p class="mb-0 small">
                                Anda dapat langsung melanjutkan ke proses selanjutnya tanpa perlu mengisi form permohonan TUK.
                            </p>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <a href="{{ route('asesi.apl01.show', $apl01->id) }}" 
                               class="btn btn-primary">
                                <i class="bi bi-arrow-right me-2"></i>Lanjutkan ke APL 01
                            </a>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="bi bi-lightbulb me-2"></i>Informasi</h6>
                            <ul class="mb-0 small">
                                <li>TUK Mandiri tidak memerlukan permohonan khusus</li>
                                <li>Pastikan fasilitas memenuhi persyaratan</li>
                                <li>Assessment akan dilakukan di tempat Anda</li>
                                <li>Jadwal akan dikoordinasikan langsung</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-list-check me-2"></i>Langkah Selanjutnya
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="badge bg-success rounded-pill me-3">1</div>
                            <div>
                                <strong>Siapkan Fasilitas</strong>
                                <p class="mb-0 small text-muted">Pastikan tempat dan peralatan sesuai panduan</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <div class="badge bg-secondary rounded-pill me-3">2</div>
                            <div>
                                <strong>Tunggu Penjadwalan</strong>
                                <p class="mb-0 small text-muted">Admin akan menghubungi untuk koordinasi jadwal</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="badge bg-secondary rounded-pill me-3">3</div>
                            <div>
                                <strong>Pelaksanaan Assessment</strong>
                                <p class="mb-0 small text-muted">Assessment dilakukan di tempat Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card-header-custom {
    padding: 1.5rem;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border-radius: 10px 10px 0 0;
}

.main-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
    padding: 0;
}

#pdfViewer {
    border: 1px solid #dee2e6;
}

.badge.rounded-pill {
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Check if PDF loads successfully
    const pdfEmbed = document.getElementById('pdfEmbed');
    const pdfFallback = document.getElementById('pdfFallback');
    
    pdfEmbed.onerror = function() {
        pdfEmbed.style.display = 'none';
        pdfFallback.classList.remove('d-none');
    };
    
    // If PDF is already broken, show fallback
    setTimeout(function() {
        if (pdfEmbed.offsetHeight === 0) {
            pdfEmbed.style.display = 'none';
            pdfFallback.classList.remove('d-none');
        }
    }, 1000);
});

function printPDF() {
    const pdfEmbed = document.getElementById('pdfEmbed');
    if (pdfEmbed && pdfEmbed.style.display !== 'none') {
        pdfEmbed.focus();
        pdfEmbed.contentWindow.print();
    } else {
        window.print();
    }
}
</script>
@endpush