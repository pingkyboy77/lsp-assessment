{{-- resources/views/asesor/mapa/view.blade.php --}}
@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Main Card -->
    <div class="main-card">
        <!-- Card Header -->
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-clipboard-check me-2"></i>Detail MAPA
                    </h5>
                    <p class="mb-0 text-muted">
                        Nomor: <strong>{{ $mapa->nomor_mapa }}</strong> | 
                        Status: <span class="badge bg-{{ $mapa->status_color }}">{{ $mapa->status_text }}</span>
                    </p>
                </div>
                <a href="{{ route('asesor.mapa.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <div class="card-body m-3">
            <!-- Status Actions -->
            @if($mapa->status === 'approved')
                <div class="alert alert-warning mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">MAPA Perlu Validasi</h6>
                                <p class="mb-0">MAPA telah diapprove oleh admin. Silakan lakukan validasi dengan tanda tangan digital.</p>
                            </div>
                        </div>
                        <a href="{{ route('asesor.mapa.validate', $mapa->id) }}" class="btn btn-warning">
                            <i class="bi bi-pen me-1"></i>Validasi Sekarang
                        </a>
                    </div>
                </div>
            @endif

            @if($mapa->status === 'rejected')
                <div class="alert alert-danger mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-x-circle-fill me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">MAPA Ditolak</h6>
                                <p class="mb-2">MAPA ditolak oleh admin. Silakan perbaiki dan submit ulang.</p>
                                @if($mapa->review_notes)
                                    <div class="p-2 bg-white rounded">
                                        <strong>Alasan Penolakan:</strong>
                                        <p class="mb-0 mt-1">{{ $mapa->review_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('asesor.mapa.edit', $mapa->id) }}" class="btn btn-danger">
                            <i class="bi bi-pencil me-1"></i>Edit MAPA
                        </a>
                    </div>
                </div>
            @endif

            {{-- @if($mapa->status === 'validated' && $spt && $spt->is_signed)
                <div class="alert alert-success mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">MAPA Telah Divalidasi & SPT Tersedia</h6>
                                <p class="mb-0">MAPA telah divalidasi dan SPT Asesor sudah tersedia untuk dilihat/download.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.spt-signatures.show', $mapa->delegasi_personil_asesmen_id) }}" 
                           class="btn btn-success" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Lihat SPT Asesor
                        </a>
                    </div>
                </div>
            @endif --}}

            <!-- Asesi Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Informasi Asesi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%;">Nama</td>
                                    <td>{{ $mapa->delegasi->asesi->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Email</td>
                                    <td>{{ $mapa->delegasi->asesi->email }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%;">Skema</td>
                                    <td>{{ $mapa->certificationScheme->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Kode Skema</td>
                                    <td>{{ $mapa->certificationScheme->code_1 }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAPA Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard-data me-2"></i>Detail MAPA</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">MAPA Code</label>
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-primary">{{ $mapa->mapa_code }}</h4>
                            <small class="text-muted">{{ $mapa->getDescription() }}</small>
                        </div>
                    </div>

                    <!-- Kelompok Details -->
                    <label class="form-label fw-bold mb-3">Detail per Kelompok Kerja</label>
                    <div class="row g-2">
                        @foreach($kelompokDetails as $detail)
                            <div class="col-md-6">
                                <div class="kelompok-assignment">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-folder me-2" style="color: var(--primary-color);"></i>
                                        <div class="flex-grow-1">
                                            <small class="fw-semibold d-block">{{ $detail['kelompok']->nama_kelompok }}</small>
                                            <div class="mt-1">
                                                <span class="p-level-badge {{ $detail['p_number'] == 0 ? 'p-level-0' : 'p-level-active' }}">
                                                    P{{ $detail['p_number'] }}
                                                </span>
                                                <small class="text-muted ms-2">{{ $detail['metode_text'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Catatan Asesor -->
                    @if($mapa->catatan_asesor)
                        <div class="mt-4">
                            <label class="form-label fw-bold">Catatan Asesor</label>
                            <div class="p-3 bg-light rounded">
                                {{ $mapa->catatan_asesor }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Signature -->
            @if($mapa->is_signed)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-pen me-2"></i>Tanda Tangan</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $mapa->signature_image) }}" 
                                 alt="Signature" 
                                 style="max-width: 300px; height: auto; border: 1px solid #dee2e6; padding: 10px; background: white;">
                            <div class="mt-3">
                                <p class="mb-1"><strong>{{ $mapa->signedBy->name }}</strong></p>
                                <small class="text-muted">Ditandatangani: {{ $mapa->signed_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Review Info -->
            @if($mapa->reviewed_by)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Informasi Review</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="fw-semibold" style="width: 30%;">Direview oleh</td>
                                <td>{{ $mapa->reviewedBy->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tanggal Review</td>
                                <td>{{ $mapa->reviewed_at ? $mapa->reviewed_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            @if($mapa->review_notes)
                                <tr>
                                    <td class="fw-semibold">Catatan Review</td>
                                    <td>{{ $mapa->review_notes }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection