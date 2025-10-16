@extends('layouts.admin')

@section('title', 'Detail SPT - Admin')

@section('content')
    <div class="main-card">
        <!-- Header -->
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>Detail Surat Perintah Tugas (SPT)
                </h4>
                <p class="text-black mb-0">Detail lengkap SPT dan informasi delegasi personil asesmen</p>
            </div>
            <div>
                <a href="javascript:history.back()" class="btn btn-light">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        @if ($sptSignature)
            <!-- Status Card -->
            <div class="row mb-4 m-3">
                <div class="col-12">
                    <div class="card {{ $sptSignature->is_signed ? 'border-success' : 'border-warning' }}">
                        <div class="card-header {{ $sptSignature->is_signed ? 'bg-success' : 'bg-warning' }} text-white">
                            <h5 class="mb-0">
                                <i
                                    class="bi {{ $sptSignature->is_signed ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-2"></i>
                                Status SPT:
                                {{ $sptSignature->is_signed ? 'Sudah Ditandatangani' : 'Menunggu Tanda Tangan' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if ($sptSignature->is_signed)
                                    <div class="col-md-4">
                                        <strong>Ditandatangani Oleh:</strong><br>
                                        <span class="text-dark">{{ $sptSignature->signed_by_name }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Tanggal Tanda Tangan:</strong><br>
                                        <span class="text-dark">{{ $sptSignature->formatted_signed_at }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Status Dokumen:</strong><br>
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="bi bi-file-check me-1"></i>Lengkap (3 Dokumen)
                                        </span>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <div class="alert alert-warning mb-0">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            SPT belum ditandatangani. Silakan akses halaman
                                            <a href="{{ route('admin.spt-signatures.index') }}" class="alert-link">
                                                <strong>Tanda Tangan SPT</strong>
                                            </a> untuk menandatangani dokumen ini.
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($sptSignature->notes)
                                <hr>
                                <div>
                                    <strong>Catatan SPT:</strong>
                                    <p class="mb-0 mt-2">{{ $sptSignature->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($sptSignature->is_signed)
                <!-- Download Cards -->
                <div class="row mb-4 m-3">
                    <!-- SPT Verifikator -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-person-check me-2"></i>SPT Verifikator TUK
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <i class="bi bi-file-earmark-pdf text-primary mb-3" style="font-size: 4rem;"></i>
                                <h5 class="card-title">{{ $delegasi->verifikatorTuk->name ?? '-' }}</h5>
                                <p class="text-muted small mb-3">NIK: {{ $delegasi->verifikator_nik ?? '-' }}</p>

                                <a href="javascript:void(0)" onclick="downloadSPT({{ $delegasi->id }}, 'verifikator')"
                                    class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- SPT Observer -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-eye me-2"></i>SPT Observer
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <i class="bi bi-file-earmark-pdf text-info mb-3" style="font-size: 4rem;"></i>
                                <h5 class="card-title">{{ $delegasi->observer->name ?? '-' }}</h5>
                                <p class="text-muted small mb-3">NIK: {{ $delegasi->observer_nik ?? '-' }}</p>

                                <a href="javascript:void(0)" onclick="downloadSPT({{ $delegasi->id }}, 'observer')"
                                    class="btn btn-info btn-lg w-100">
                                    <i class="bi bi-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- SPT Asesor -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-clipboard-check me-2"></i>SPT Asesor
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <i class="bi bi-file-earmark-pdf text-success mb-3" style="font-size: 4rem;"></i>
                                <h5 class="card-title">{{ $delegasi->asesor->name ?? '-' }}</h5>
                                <p class="text-muted small mb-3">MET: {{ $delegasi->asesor_met ?? '-' }}</p>

                                <a href="javascript:void(0)" onclick="downloadSPT({{ $delegasi->id }}, 'asesor')"
                                    class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- SPT Belum Ada -->
            <div class="row mb-4 m-3">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>SPT belum digenerate.</strong> SPT akan otomatis dibuat saat proses penandatanganan di menu
                        Tanda Tangan SPT.
                    </div>
                </div>
            </div>
        @endif

        <!-- Informasi Delegasi -->
        <div class="row m-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informasi Delegasi Personil
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Asesi & Skema -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-person-badge me-2"></i>Informasi Asesi
                                </h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="180"><strong>Nama Lengkap</strong></td>
                                        <td>: {{ $delegasi->asesi->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NIK</strong></td>
                                        <td>: {{ $delegasi->asesi->id_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email</strong></td>
                                        <td>: {{ $delegasi->asesi->email ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info mb-3">
                                    <i class="bi bi-bookmark-fill me-2"></i>Skema Sertifikasi
                                </h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="180"><strong>Nama Skema</strong></td>
                                        <td>: {{ $delegasi->certificationScheme->nama ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <!-- Personil -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3">
                                    <i class="bi bi-people-fill me-2"></i>Personil yang Ditugaskan
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="bi bi-person-check me-1"></i>Verifikator TUK
                                        </h6>
                                        <p class="mb-1"><strong>{{ $delegasi->verifikatorTuk->name ?? '-' }}</strong>
                                        </p>
                                        <small class="text-muted">NIK: {{ $delegasi->verifikator_nik ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-info">
                                            <i class="bi bi-eye me-1"></i>Observer
                                        </h6>
                                        <p class="mb-1"><strong>{{ $delegasi->observer->name ?? '-' }}</strong></p>
                                        <small class="text-muted">NIK: {{ $delegasi->observer_nik ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-success">
                                            <i class="bi bi-clipboard-check me-1"></i>Asesor
                                        </h6>
                                        <p class="mb-1"><strong>{{ $delegasi->asesor->name ?? '-' }}</strong></p>
                                        <small class="text-muted">MET: {{ $delegasi->asesor_met ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Jadwal & Lokasi -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-3">
                                    <i class="bi bi-calendar-event me-2"></i>Jadwal Asesmen
                                </h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="180"><strong>Tanggal Pelaksanaan</strong></td>
                                        <td>: {{ $delegasi->tanggal_pelaksanaan_asesmen->format('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Waktu Mulai</strong></td>
                                        <td>: {{ $delegasi->waktu_mulai }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Ujian</strong></td>
                                        <td>:
                                            <span
                                                class="badge {{ $delegasi->jenis_ujian === 'online' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($delegasi->jenis_ujian) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-warning mb-3">
                                    <i class="bi bi-geo-alt-fill me-2"></i>Informasi Tambahan
                                </h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="180"><strong>Didelegasikan Oleh</strong></td>
                                        <td>: {{ $delegasi->delegatedBy->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Delegasi</strong></td>
                                        <td>: {{ $delegasi->created_at->format('d F Y H:i') }}</td>
                                    </tr>
                                </table>

                                @if ($delegasi->notes)
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <strong class="d-block mb-2">Catatan:</strong>
                                        <p class="mb-0">{{ $delegasi->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function downloadSPT(delegasiId, type) {
                const typeName = type === 'verifikator' ? 'Verifikator TUK' :
                    type === 'observer' ? 'Observer' : 'Asesor';

                showToast('info', `Mengunduh SPT ${typeName}...`);

                window.location.href = `/admin/spt-signatures/${delegasiId}/download/${type}`;
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
@endsection
