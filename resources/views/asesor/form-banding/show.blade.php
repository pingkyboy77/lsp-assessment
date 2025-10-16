{{-- resources/views/asesor/form-banding/show.blade.php --}}
@extends('layouts.admin')

@push('styles')
    <style>
        .document-viewer-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .document-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .document-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: 4px solid #5a67d8;
        }

        .info-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: white;
        }

        .pdf-viewer {
            width: 100%;
            height: 85vh;
            border: none;
            background: #f8f9fa;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .action-buttons {
            background: #f8f9fa;
            padding: 1rem;
            border-top: 2px solid #e9ecef;
        }

        .btn-download {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .alert-info-custom {
            background: linear-gradient(135deg, #e0e7ff 0%, #e5e7eb 100%);
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        {{-- <div class="document-viewer-container"> --}}
            <div class="document-card">
                <div class="document-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="mb-2 fw-bold">
                                <i class="bi bi-file-earmark-text me-2"></i>FR.AK.04 - Form Banding Asesmen
                            </h4>
                            <p class="mb-0 opacity-90">Formulir Permohonan Banding untuk Hasil Asesmen</p>
                        </div>
                        <a href="{{ route('asesor.mapa.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>kembali
                        </a>
                    </div>

                    <div class="row mt-3 g-2">
                        <div class="col-md-6">
                            <div class="info-badge">
                                <small class="d-block opacity-75 mb-1">Assessee Name</small>
                                <strong>{{ $delegasi->asesi->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-badge">
                                <small class="d-block opacity-75 mb-1">Certification Scheme</small>
                                <strong>{{ $delegasi->certificationScheme->nama }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert-info-custom m-3">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill text-primary me-3 fs-4"></i>
                        <div>
                            <h6 class="fw-bold mb-1">About Form Banding</h6>
                            <p class="mb-0 text-muted small">
                                Form ini digunakan oleh Asesi untuk mengajukan permohonan banding terhadap proses asesmen
                                yang telah dilakukan. Asesi dapat mengisi form ini jika merasa ada hal yang perlu
                                ditinjau kembali dari proses asesmen.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="position-relative">
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="text-center">
                            <div class="loading-spinner mb-3"></div>
                            <p class="text-muted">Loading document...</p>
                        </div>
                    </div>

                    <iframe id="pdfViewer" class="pdf-viewer" src="{{ $bandingDocumentUrl }}" 
                            onload="hideLoading()" 
                            onerror="showError()">
                        <p class="p-4 text-center">
                            Your browser does not support PDF viewing. 
                            <a href="{{ $bandingDocumentUrl }}" class="btn btn-primary" download>
                                <i class="bi bi-download me-1"></i>Download PDF
                            </a>
                        </p>
                    </iframe>
                </div>

                <div class="action-buttons">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            <i class="bi bi-file-pdf me-1"></i>
                            Document Type: PDF
                        </div>
                        <div class="d-flex gap-2">
                            {{-- <a href="{{ $bandingDocumentUrl }}" class="btn btn-download" download>
                                <i class="bi bi-download me-2"></i>Download Form
                            </a>
                            <button class="btn btn-outline-secondary" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>
        {{-- </div> --}}
    </div>
@endsection

@push('scripts')
    <script>
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showError() {
            document.getElementById('loadingOverlay').innerHTML = `
                <div class="text-center">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Failed to Load Document</h5>
                    <p class="text-muted">The document could not be loaded. Please try downloading it instead.</p>
                    <a href="{{ $bandingDocumentUrl }}" class="btn btn-primary" download>
                        <i class="bi bi-download me-1"></i>Download Form
                    </a>
                </div>
            `;
        }

        // Hide loading after 10 seconds if not loaded
        setTimeout(function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay.style.display !== 'none') {
                hideLoading();
            }
        }, 10000);
    </script>
@endpush