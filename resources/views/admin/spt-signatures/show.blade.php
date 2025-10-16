@extends('layouts.admin')

@section('title', 'Detail SPT - ' . ($delegasi->asesi->name ?? 'Delegasi'))

@section('content')
    <div class="container-fluid">
        <div class="main-card">
            <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>Detail Surat Perintah Tugas (SPT)
                    </h4>
                    <p class="text-black mb-0">Informasi lengkap SPT untuk:
                        <strong class="text-primary">{{ $delegasi->asesi->name ?? 'Asesi Tidak Dikenal' }}</strong>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.spt-signatures.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
                    </a>
                    @if ($spt && $spt->status === 'signed')
                        {{-- Already signed --}}
                    @elseif (!$spt || $spt->status === 'pending')
                        <button class="btn btn-success" onclick="openSignModal({{ $delegasi->id }})">
                            <i class="bi bi-pen-fill me-1"></i>Tanda Tangan Sekarang
                        </button>
                    @endif
                </div>
            </div>

            <div class="row m-3">
                {{-- Detail Delegasi Card --}}
                <div class="col-lg-7">
                    <div class="card mb-4 border-primary spt-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Detail Delegasi Asesmen</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $isMandiri = $delegasi->apl01 && $delegasi->apl01->tuk === 'Mandiri';
                            @endphp
                            
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted">Asesi</dt>
                                <dd class="col-sm-8">{{ $delegasi->asesi->name ?? '-' }} (NIK: {{ $delegasi->asesi->id_number ?? '-' }})</dd>

                                <dt class="col-sm-4 text-muted">Skema Sertifikasi</dt>
                                <dd class="col-sm-8">{{ $delegasi->certificationScheme->nama ?? '-' }}</dd>

                                <dt class="col-sm-4 text-muted">Jenis TUK</dt>
                                <dd class="col-sm-8">
                                    @if ($isMandiri)
                                        <span class="badge bg-info">Mandiri</span>
                                    @else
                                        <span class="badge bg-primary">Sewaktu</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4 text-muted">Tgl Pelaksanaan</dt>
                                <dd class="col-sm-8">{{ $delegasi->tanggal_pelaksanaan_asesmen?->format('d F Y') ?? '-' }}</dd>

                                <dt class="col-sm-4 text-muted">Lokasi Assessment</dt>
                                <dd class="col-sm-8">
                                    @if ($isMandiri)
                                        Mandiri
                                    @else
                                        {{ $delegasi->tukRequest?->lokasi_assessment ?? 'Sewaktu, Asesmen Jarak Jauh' }}
                                    @endif
                                </dd>

                                <dt class="col-sm-4 text-muted">Personil Didelegasikan</dt>
                                <dd class="col-sm-8">
                                    <div class="d-flex flex-wrap gap-2">
                                        @if (!$isMandiri && $delegasi->verifikatorTuk)
                                            <span class="personil-badge badge bg-info text-dark">Verifikator: {{ $delegasi->verifikatorTuk?->name ?? 'N/A' }}</span>
                                        @endif
                                        @if ($delegasi->observer)
                                            <span class="personil-badge badge bg-warning text-dark">Observer: {{ $delegasi->observer?->name ?? 'N/A' }}</span>
                                        @endif
                                        @if ($delegasi->asesor)
                                            <span class="personil-badge badge bg-success">Asesor: {{ $delegasi->asesor?->name ?? 'N/A' }}</span>
                                        @endif
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Status Tanda Tangan Card --}}
                <div class="col-lg-5">
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-pen me-2"></i>Status Tanda Tangan SPT</h5>
                        </div>
                        <div class="card-body">
                            @if ($spt)
                                <dl class="row mb-0">
                                    <dt class="col-sm-5 text-muted">Status</dt>
                                    <dd class="col-sm-7">
                                        @if ($spt->status === 'signed')
                                            <span class="badge badge-signed text-white">Selesai Ditandatangani</span>
                                        @else
                                            <span class="badge badge-pending text-dark">Menunggu Tanda Tangan</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-5 text-muted">Tgl. Ditandatangani</dt>
                                    <dd class="col-sm-7">{{ $spt->signed_at?->format('d F Y H:i') ?? '-' }}</dd>

                                    <dt class="col-sm-5 text-muted">Ditandatangani Oleh</dt>
                                    <dd class="col-sm-7">{{ $spt->signedBy?->name ?? 'N/A' }}</dd>

                                    <dt class="col-sm-5 text-muted">Catatan Direktur</dt>
                                    <dd class="col-sm-7 text-wrap small">{{ $spt->notes ?? 'Tidak ada' }}</dd>

                                    <hr class="my-2">

                                    @if (!$isMandiri)
                                        <dt class="col-sm-5 text-muted">No. SPT Verifikator</dt>
                                        <dd class="col-sm-7 text-wrap">{{ $spt->spt_verifikator_number ?? '-' }}</dd>
                                    @endif

                                    <dt class="col-sm-5 text-muted">No. SPT Observer</dt>
                                    <dd class="col-sm-7 text-wrap">{{ $spt->spt_observer_number ?? '-' }}</dd>

                                    <dt class="col-sm-5 text-muted">No. SPT Asesor</dt>
                                    <dd class="col-sm-7 text-wrap">{{ $spt->spt_asesor_number ?? '-' }}</dd>
                                </dl>
                            @else
                                <div class="alert alert-warning mb-0" role="alert">
                                    SPT untuk delegasi ini belum pernah digenerate atau ditandatangani.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preview SPT Section --}}
            <div class="card m-3 border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-eye-fill me-2"></i>Preview SPT
                    </h5>
                </div>
                <div class="card-body">
                    @if ($spt && $spt->status === 'signed')
                        <div class="row">
                            {{-- SPT Verifikator: Hanya tampilkan jika BUKAN Mandiri --}}
                            @if (!$isMandiri && $spt->spt_verifikator_file)
                                <div class="col-md-4 mb-3 text-center">
                                    <h6>SPT Verifikator</h6>
                                    <iframe
                                        src="{{ route('admin.spt-signatures.preview', ['id' => $delegasi->id, 'type' => 'verifikator']) }}"
                                        class="w-100 border rounded" height="300"></iframe>
                                    <a href="javascript:void(0)" onclick="downloadSPT({{ $delegasi->id }}, 'verifikator')" 
                                       class="btn btn-sm btn-primary mt-2">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                </div>
                            @endif

                            {{-- SPT Observer: Selalu tampilkan --}}
                            @if ($spt->spt_observer_file)
                                <div class="col-md-{{ !$isMandiri ? '4' : '6' }} mb-3 text-center">
                                    <h6>SPT Observer</h6>
                                    <iframe
                                        src="{{ route('admin.spt-signatures.preview', ['id' => $delegasi->id, 'type' => 'observer']) }}"
                                        class="w-100 border rounded" height="300"></iframe>
                                    <a href="javascript:void(0)" onclick="downloadSPT({{ $delegasi->id }}, 'observer')" 
                                       class="btn btn-sm btn-info mt-2">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                </div>
                            @endif

                            {{-- SPT Asesor: Selalu tampilkan --}}
                            @if ($spt->spt_asesor_file)
                                <div class="col-md-{{ !$isMandiri ? '4' : '6' }} mb-3 text-center">
                                    <h6>SPT Asesor</h6>
                                    <iframe
                                        src="{{ route('admin.spt-signatures.preview', ['id' => $delegasi->id, 'type' => 'asesor']) }}"
                                        class="w-100 border rounded" height="300"></iframe>
                                    <a href="javascript:void(0)" onclick="downloadSPT({{ $delegasi->id }}, 'asesor')" 
                                       class="btn btn-sm btn-success mt-2">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            SPT belum ditandatangani, tidak ada file untuk ditampilkan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.spt-signatures.partials.sign_modal')

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/4.1.7/signature_pad.umd.min.js"></script>
    <script>
        function downloadSPT(delegasiId, type) {
            const typeName = type === 'verifikator' ? 'Verifikator TUK' : 
                           type === 'observer' ? 'Observer' : 'Asesor';
            
            showToast('info', `Mengunduh SPT ${typeName}...`);
            window.location.href = `/admin/spt-signatures/${delegasiId}/download/${type}`;
        }
        
        // Placeholder function - implement as needed
        function showToast(type, message) {
            console.log(`${type}: ${message}`);
        }
    </script>
@endpush