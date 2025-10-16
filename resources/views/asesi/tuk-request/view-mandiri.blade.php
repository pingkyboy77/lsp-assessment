{{-- resources/views/asesi/tuk/view-mandiri.blade.php --}}
@extends('layouts.admin')

@section('title', 'TUK Mandiri - Dokumen')

@section('content')
    <div class="container-fluid">
        <div class="main-card">
            <!-- Header -->
            <div class="card-header-custom mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Dokumen TUK Mandiri
                        </h4>
                        <p class="text-muted mb-0">Informasi mengenai TUK Mandiri untuk sertifikasi Anda</p>
                    </div>
                    <div>
                        <a href="{{ route('asesi.inbox.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-info mb-4 m-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-info-circle me-3 fs-4"></i>
                    <div>
                        <h6 class="mb-2">Tentang TUK Mandiri</h6>
                        <p class="mb-0">
                            TUK Mandiri adalah Tempat Uji Kompetensi yang berada di lokasi perusahaan/institusi Anda
                            sendiri.
                            Silakan pelajari dokumen berikut untuk memahami persyaratan dan prosedur TUK Mandiri.
                        </p>
                    </div>
                </div>
            </div>

            <!-- PDF Viewer Card -->
            <div class="row m-3">
                <div class="col-12">
                    <div class="card shadow-sm">

                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                    Dokumen TUK Mandiri
                                </h5>
                                <div class="btn-group">
                                    <a href="{{ asset('TUK/tukMandiri.pdf') }}" download="TUK_Mandiri.pdf"
                                        class="btn btn-sm btn-danger">
                                        <i class="bi bi-download me-1"></i>Download PDF
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="toggleFullscreen()">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="pdfViewerContainer" style="height: 800px; position: relative;">
                                <!-- PDF Embed -->
                                <embed src="{{ asset('TUK/tukMandiri.pdf') }}" type="application/pdf" width="100%"
                                    height="100%" id="pdfEmbed" />

                                <!-- Fallback for browsers that don't support embed -->
                                <div id="pdfFallback" style="display: none;" class="text-center p-5">
                                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                                    <h5 class="mt-3">Browser tidak mendukung tampilan PDF</h5>
                                    <p class="text-muted">Silakan download dokumen untuk melihatnya</p>
                                    <a href="{{ asset('TUK/tukMandiri.pdf') }}" download="TUK_Mandiri.pdf"
                                        class="btn btn-danger">
                                        <i class="bi bi-download me-1"></i>Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps Card -->
            <div class="row mt-4 m-3">
                <div class="col-12">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-lightbulb me-2"></i>Langkah Selanjutnya
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <strong>1</strong>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Pelajari Dokumen</h6>
                                            <p class="text-muted small mb-0">
                                                Baca dan pahami persyaratan TUK Mandiri yang tercantum dalam dokumen.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <strong>2</strong>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Persiapkan Lokasi</h6>
                                            <p class="text-muted small mb-0">
                                                Pastikan lokasi TUK Mandiri Anda memenuhi persyaratan yang ditetapkan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <strong>3</strong>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Tunggu Konfirmasi</h6>
                                            <p class="text-muted small mb-0">
                                                Admin akan menghubungi Anda untuk proses selanjutnya.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <strong>4</strong>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Siap Assessment</h6>
                                            <p class="text-muted small mb-0">
                                                Setelah dikonfirmasi, Anda siap untuk menjalani assessment.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('asesi.inbox.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
                                </a>
                                <a href="{{ asset('TUK/tukMandiri.pdf') }}" download="TUK_Mandiri.pdf"
                                    class="btn btn-danger">
                                    <i class="bi bi-download me-1"></i>Download Dokumen
                                </a>
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
        #pdfViewerContainer {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            overflow: hidden;
        }

        #pdfViewerContainer.fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            height: 100vh !important;
            border-radius: 0;
        }

        embed {
            border: none;
        }

        .card-header.bg-light {
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Check if PDF embed is supported
        $(document).ready(function() {
            setTimeout(function() {
                const embed = document.getElementById('pdfEmbed');
                if (!embed || embed.offsetHeight === 0) {
                    $('#pdfEmbed').hide();
                    $('#pdfFallback').show();
                }
            }, 1000);
        });

        function toggleFullscreen() {
            const container = document.getElementById('pdfViewerContainer');
            container.classList.toggle('fullscreen');

            // Change icon
            const btn = event.currentTarget;
            const icon = btn.querySelector('i');
            if (container.classList.contains('fullscreen')) {
                icon.classList.remove('bi-arrows-fullscreen');
                icon.classList.add('bi-fullscreen-exit');
            } else {
                icon.classList.remove('bi-fullscreen-exit');
                icon.classList.add('bi-arrows-fullscreen');
            }
        }

        // ESC key to exit fullscreen
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const container = document.getElementById('pdfViewerContainer');
                if (container.classList.contains('fullscreen')) {
                    container.classList.remove('fullscreen');
                    const icon = document.querySelector('[onclick="toggleFullscreen()"] i');
                    if (icon) {
                        icon.classList.remove('bi-fullscreen-exit');
                        icon.classList.add('bi-arrows-fullscreen');
                    }
                }
            }
        });
    </script>
@endpush
