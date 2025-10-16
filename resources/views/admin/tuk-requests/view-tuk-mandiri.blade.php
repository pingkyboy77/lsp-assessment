{{-- resources/views/admin/tuk-requests/view-tuk-mandiri.blade.php --}}
@extends('layouts.admin')

@section('title', 'TUK Mandiri - ' . $apl01->nama_lengkap)

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
                    <p class="text-muted mb-0">{{ $apl01->nama_lengkap }} - {{ $apl01->nomor_apl_01 }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.tuk-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Participant Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h6 class="text-muted mb-2">Peserta</h6>
                                <p class="mb-0 fw-bold">{{ $apl01->nama_lengkap }}</p>
                                <small class="text-muted">{{ $apl01->user->email ?? 'N/A' }}</small>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-2">NIK</h6>
                                <p class="mb-0">{{ $apl01->nik ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted mb-2">Skema Sertifikasi</h6>
                                <p class="mb-0">{{ $apl01->certificationScheme->nama ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-muted mb-2">Jenis TUK</h6>
                                <span class="badge bg-info">TUK Mandiri</span>
                            </div>
                        </div>
                        
                        @if($apl01->nama_tempat_kerja)
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Perusahaan</h6>
                                <p class="mb-0">{{ $apl01->nama_tempat_kerja }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Jabatan</h6>
                                <p class="mb-0">{{ $apl01->jabatan ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF Viewer Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                Dokumen TUK Mandiri
                            </h5>
                            <a href="{{ asset('TUK MANDIRI/tukMandiri.pdf') }}" 
                               download="TUK_Mandiri.pdf" 
                               class="btn btn-sm btn-danger">
                                <i class="bi bi-download me-1"></i>Download PDF
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="pdfViewerContainer" style="height: 800px; position: relative;">
                            <!-- PDF Embed -->
                            <embed 
                                src="{{ asset('TUK MANDIRI/tukMandiri.pdf') }}" 
                                type="application/pdf" 
                                width="100%" 
                                height="100%"
                                id="pdfEmbed"
                            />
                            
                            <!-- Fallback for browsers that don't support embed -->
                            <div id="pdfFallback" style="display: none;" class="text-center p-5">
                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                                <h5 class="mt-3">Browser tidak mendukung tampilan PDF</h5>
                                <p class="text-muted">Silakan download dokumen untuk melihatnya</p>
                                <a href="{{ asset('TUK MANDIRI/tukMandiri.pdf') }}" 
                                   download="TUK_Mandiri.pdf" 
                                   class="btn btn-danger">
                                    <i class="bi bi-download me-1"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-3">Tindakan Selanjutnya</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="delegasiAsesor({{ $apl01->id }})">
                                <i class="bi bi-person-check me-1"></i>Delegasi Asesor
                            </button>
                            <a href="{{ route('admin.tuk-requests.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
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

    function delegasiAsesor(apl01Id) {
        if (!confirm('Apakah Anda yakin ingin melakukan delegasi asesor untuk APL01 TUK Mandiri ini?')) {
            return;
        }

        fetch(`/admin/tuk-requests/apl01/${apl01Id}/delegasi-asesor`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
                setTimeout(() => {
                    window.location.href = '{{ route("admin.tuk-requests.index") }}';
                }, 1500);
            } else {
                showToast('error', data.error || 'Gagal melakukan delegasi asesor');
            }
        })
        .catch(error => {
            showToast('error', 'Error: ' + error.message);
        });
    }

    function showToast(type, message) {
        const colors = {
            success: 'bg-success',
            error: 'bg-danger',
            info: 'bg-info',
            warning: 'bg-warning'
        };

        const icons = {
            success: 'bi-check-circle',
            error: 'bi-exclamation-triangle',
            info: 'bi-info-circle',
            warning: 'bi-exclamation-triangle'
        };

        const toastHtml = `
            <div class="toast align-items-center text-white ${colors[type]}" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icons[type]} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        container.insertAdjacentHTML('beforeend', toastHtml);
        const toast = new bootstrap.Toast(container.lastElementChild);
        toast.show();
    }
</script>
@endpush
@extends('layouts.admin')

@section('title', 'TUK Mandiri - ' . $apl01->nama_lengkap)

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
                    <p class="text-muted mb-0">{{ $apl01->nama_lengkap }} - {{ $apl01->nomor_apl_01 }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.tuk-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Participant Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h6 class="text-muted mb-2">Peserta</h6>
                                <p class="mb-0 fw-bold">{{ $apl01->nama_lengkap }}</p>
                                <small class="text-muted">{{ $apl01->user->email ?? 'N/A' }}</small>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted mb-2">NIK</h6>
                                <p class="mb-0">{{ $apl01->nik ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted mb-2">Skema Sertifikasi</h6>
                                <p class="mb-0">{{ $apl01->certificationScheme->nama ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-muted mb-2">Jenis TUK</h6>
                                <span class="badge bg-info">TUK Mandiri</span>
                            </div>
                        </div>
                        
                        @if($apl01->nama_tempat_kerja)
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Perusahaan</h6>
                                <p class="mb-0">{{ $apl01->nama_tempat_kerja }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Jabatan</h6>
                                <p class="mb-0">{{ $apl01->jabatan ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF Viewer Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                Dokumen TUK Mandiri
                            </h5>
                            <a href="{{ asset('TUK MANDIRI/tukMandiri.pdf') }}" 
                               download="TUK_Mandiri.pdf" 
                               class="btn btn-sm btn-danger">
                                <i class="bi bi-download me-1"></i>Download PDF
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="pdfViewerContainer" style="height: 800px; position: relative;">
                            <!-- PDF Embed -->
                            <embed 
                                src="{{ asset('TUK MANDIRI/tukMandiri.pdf') }}" 
                                type="application/pdf" 
                                width="100%" 
                                height="100%"
                                id="pdfEmbed"
                            />
                            
                            <!-- Fallback for browsers that don't support embed -->
                            <div id="pdfFallback" style="display: none;" class="text-center p-5">
                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                                <h5 class="mt-3">Browser tidak mendukung tampilan PDF</h5>
                                <p class="text-muted">Silakan download dokumen untuk melihatnya</p>
                                <a href="{{ asset('TUK MANDIRI/tukMandiri.pdf') }}" 
                                   download="TUK_Mandiri.pdf" 
                                   class="btn btn-danger">
                                    <i class="bi bi-download me-1"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-3">Tindakan Selanjutnya</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" onclick="viewApl01Details({{ $apl01->id }})">
                                <i class="bi bi-eye me-1"></i>Lihat Detail APL01
                            </button>
                            <button type="button" class="btn btn-success" onclick="delegasiAsesor({{ $apl01->id }})">
                                <i class="bi bi-person-check me-1"></i>Delegasi Asesor
                            </button>
                            <a href="{{ route('admin.tuk-requests.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
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

    function viewApl01Details(apl01Id) {
        // Open APL01 detail in new tab or modal
        window.open(`/admin/tuk-requests/apl01/${apl01Id}/view`, '_blank');
    }

    function delegasiAsesor(apl01Id) {
        if (!confirm('Apakah Anda yakin ingin melakukan delegasi asesor untuk APL01 ini?')) {
            return;
        }

        fetch(`/admin/tuk-requests/apl01/${apl01Id}/delegasi-asesor`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
                setTimeout(() => {
                    window.location.href = '{{ route("admin.tuk-requests.index") }}';
                }, 1500);
            } else {
                showToast('error', data.error || 'Gagal melakukan delegasi asesor');
            }
        })
        .catch(error => {
            showToast('error', 'Error: ' + error.message);
        });
    }

    function showToast(type, message) {
        const colors = {
            success: 'bg-success',
            error: 'bg-danger',
            info: 'bg-info',
            warning: 'bg-warning'
        };

        const icons = {
            success: 'bi-check-circle',
            error: 'bi-exclamation-triangle',
            info: 'bi-info-circle',
            warning: 'bi-exclamation-triangle'
        };

        const toastHtml = `
            <div class="toast align-items-center text-white ${colors[type]}" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icons[type]} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        container.insertAdjacentHTML('beforeend', toastHtml);
        const toast = new bootstrap.Toast(container.lastElementChild);
        toast.show();
    }
</script>
@endpush