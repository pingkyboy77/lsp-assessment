@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
    <style>
        .status-badge {
            padding: 0.4rem 0.85rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }

        .status-waiting {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #92400E;
            border: 1px solid #FCD34D;
        }

        .status-completed {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            color: #065F46;
            border: 1px solid #6EE7B7;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <div class="page-header">
            <div>
                <h4><i class="bi bi-clipboard2-check me-2"></i>Proses Konsultasi</h4>
                <p class="mb-0 opacity-90">Tinjau Dokumen Konsultasi APL-01 dan APL-02</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="main-card">
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold" style="color: #2c5282;">
                    <i class="bi bi-list-ul me-2"></i>Daftar Konsultasi
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Skema Sertifikasi</th>
                            <th width="12%">Tanggal Dibuat</th>
                            <th width="12%">Tanggal TTD Asesor</th>
                            <th width="10%">Status AK07</th>
                            <th width="10%">Status Form</th>
                            <th width="26%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ak07List as $index => $ak07)
                            <tr>
                                <td class="text-center">{{ $ak07List->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $ak07->mapa->certificationScheme->nama }}
                                    </div>
                                    <small class="text-muted">
                                        Asesor: {{ $ak07->asesor->name }}
                                    </small>
                                </td>
                                <td>
                                    <small><i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($ak07->created_at)->format('d M Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <small><i class="bi bi-pen me-1"></i>
                                        {{ \Carbon\Carbon::parse($ak07->tanggal_ttd_asesor)->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    @if ($ak07->status === 'waiting_asesi')
                                        <span class="status-badge status-waiting">
                                            <i class="bi bi-hourglass-split me-1"></i>Perlu TTD
                                        </span>
                                    @elseif($ak07->status === 'completed')
                                        <span class="status-badge status-completed">
                                            <i class="bi bi-check-all me-1"></i>Selesai
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $delegasi = $ak07->mapa->delegasi;
                                        $formKerahasiaan = $delegasi->formKerahasiaan;
                                    @endphp
                                    
                                    @if($formKerahasiaan)
                                        @if($formKerahasiaan->status === 'waiting_asesi')
                                            <span class="status-badge status-waiting">
                                                <i class="bi bi-hourglass me-1"></i>Menunggu
                                            </span>
                                        @elseif($formKerahasiaan->status === 'completed')
                                            <span class="status-badge status-completed">
                                                <i class="bi bi-check-circle me-1"></i>Selesai
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        {{-- AK07 Actions --}}
                                        @if ($ak07->status === 'waiting_asesi')
                                            <a href="{{ route('asesi.ak07.sign', $ak07->id) }}"
                                                class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-pen-fill me-1"></i>TTD AK07
                                            </a>
                                        @endif

                                        {{-- Form Kerahasiaan Actions --}}
                                        @if($ak07->status === 'completed' && $ak07->final_recommendation === 'continue')
                                            @if($formKerahasiaan)
                                                @if($formKerahasiaan->status === 'waiting_asesi')
                                                    <a href="{{ route('asesi.form-kerahasiaan.sign', $formKerahasiaan->id) }}"
                                                        class="btn btn-sm btn-outline-warning">
                                                        <i class="bi bi-pen me-1"></i>TTD Form
                                                    </a>
                                                @endif
                                            @endif
                                        @endif

                                        {{-- Show "Selesai" badge when both are completed --}}
                                        @if($ak07->status === 'completed' && $formKerahasiaan && $formKerahasiaan->status === 'completed')
                                            <span class="status-badge status-completed">
                                                <i class="bi bi-check-circle-fill me-1"></i>Semua Selesai
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        <p>Belum ada dokumen FR.AK.07 yang tersedia</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($ak07List->hasPages())
                <div class="p-3 border-top">
                    {{ $ak07List->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection