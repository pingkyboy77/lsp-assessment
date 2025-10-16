@extends('layouts.admin')

@push('styles')
    <style>
        .form-view-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .form-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 2rem;
            border-bottom: 4px solid #047857;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-waiting {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #92400E;
            border: 2px solid #FCD34D;
        }

        .status-completed {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            color: #065F46;
            border: 2px solid #6EE7B7;
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }

        .info-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #4b5563;
            min-width: 200px;
        }

        .info-value {
            color: #1f2937;
            flex: 1;
        }

        .signature-display {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-display img {
            max-width: 100%;
            max-height: 180px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background: white;
        }

        .signature-placeholder {
            color: #9ca3af;
            font-style: italic;
        }

        .statement-box {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
            border-left: 4px solid #0284c7;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .statement-box.asesi {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #3b82f6;
        }

        .action-bar {
            background: #f8f9fa;
            padding: 1.5rem;
            border-top: 2px solid #e9ecef;
            border-radius: 0 0 12px 12px;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .timeline-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #667eea;
        }

        .breadcrumb-custom {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <!-- Breadcrumb -->
        <div class="breadcrumb-custom">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.mapa.index') }}">
                            <i class="bi bi-house-door"></i> MAPA
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.mapa.show', $mapa->id) }}">
                            {{ $mapa->nomor_mapa }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Form Kerahasiaan</li>
                </ol>
            </nav>
        </div>

        <div class="form-view-card">
            {{-- Header --}}
            <div class="form-header">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h4 class="mb-2 fw-bold">
                            <i class="bi bi-shield-lock me-2"></i>FR.AK.01 - Formulir Persetujuan Asesmen dan Kerahasiaan
                        </h4>
                        <p class="mb-3 opacity-90">Formulir Persetujuan Asesmen dan Kerahasiaan</p>
                        <div>
                            @if ($formKerahasiaan->status === 'waiting_asesi')
                                <span class="status-indicator status-waiting">
                                    <i class="bi bi-hourglass-split me-2"></i>Menunggu Tanda Tangan Asesi
                                </span>
                            @elseif($formKerahasiaan->status === 'completed')
                                <span class="status-indicator status-completed">
                                    <i class="bi bi-check-circle-fill me-2"></i>Selesai
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.mapa.index', $mapa->id) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="p-4">
                {{-- MAPA Reference --}}
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">Referensi MAPA</h6>
                            <p class="mb-0 small">
                                Nomor MAPA: <strong>{{ $mapa->nomor_mapa }}</strong> | 
                                MAPA Code: <strong>{{ $mapa->mapa_code }}</strong>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Assessment Information --}}
                <h5 class="section-title">
                    <i class="bi bi-info-circle me-2"></i>Informasi Asesmen
                </h5>
                <div class="info-section">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-person me-2 text-primary"></i>Nama Asesi
                        </div>
                        <div class="info-value">{{ $formKerahasiaan->nama_asesi }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-person-badge me-2 text-success"></i>Nama Asesor
                        </div>
                        <div class="info-value">{{ $formKerahasiaan->nama_asesor }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-award me-2 text-warning"></i>Skema Sertifikasi
                        </div>
                        <div class="info-value">{{ $formKerahasiaan->skema_sertifikasi }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-calendar-event me-2 text-info"></i>Tanggal Asesmen
                        </div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($formKerahasiaan->tanggal_asesmen)->format('d F Y') }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-clock me-2 text-danger"></i>Jam Mulai
                        </div>
                        <div class="info-value">{{ $formKerahasiaan->jam_mulai }}</div>
                    </div>
                </div>

                {{-- Assessor Statement --}}
                <h5 class="section-title">
                    <i class="bi bi-chat-quote me-2"></i>Pernyataan Asesor
                </h5>
                <div class="statement-box">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-shield-check me-2"></i>Pernyataan Kerahasiaan
                    </h6>
                    <p class="text-dark mb-0 lh-lg" style="text-align: justify;">
                        Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai
                        Asesor dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang
                        berwenang sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
                    </p>
                </div>

                {{-- Assessee Statement --}}
                <h5 class="section-title">
                    <i class="bi bi-chat-quote me-2"></i>Pernyataan Asesi
                </h5>
                <div class="statement-box asesi">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-shield-check me-2"></i>Pernyataan Kerahasiaan
                    </h6>
                    <p class="text-dark mb-0 lh-lg text-capitalize" style="text-align: justify;">
                        Bahwa saya telah mendapatkan penjelasan terkait hak dan prosedur banding asesmen dari asesor. Dan Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.
                    </p>
                </div>

                {{-- Signatures Section --}}
                <div class="row g-4 mb-4">
                    {{-- Assessor Signature --}}
                    <div class="col-md-6">
                        <h5 class="section-title">
                            <i class="bi bi-pen me-2"></i>Tanda Tangan Asesor
                        </h5>
                        <div class="signature-display">
                            @if ($formKerahasiaan->ttd_asesor)
                                <img src="{{ $formKerahasiaan->ttd_asesor }}" alt="Tanda Tangan Asesor">
                            @else
                                <div class="signature-placeholder">
                                    <i class="bi bi-pen fs-1 mb-2 d-block"></i>
                                    Tanda tangan tidak tersedia
                                </div>
                            @endif
                        </div>
                        @if ($formKerahasiaan->tanggal_ttd_asesor)
                            <div class="text-center mt-3">
                                <div class="timeline-badge">
                                    <i class="bi bi-calendar-check text-success"></i>
                                    <span>Ditandatangani pada:
                                        {{ \Carbon\Carbon::parse($formKerahasiaan->tanggal_ttd_asesor)->format('d F Y, H:i') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Assessee Signature --}}
                    <div class="col-md-6">
                        <h5 class="section-title">
                            <i class="bi bi-pen me-2"></i>Tanda Tangan Asesi
                        </h5>
                        <div class="signature-display">
                            @if ($formKerahasiaan->ttd_asesi)
                                <img src="{{ $formKerahasiaan->ttd_asesi }}" alt="Tanda Tangan Asesi">
                            @else
                                <div class="signature-placeholder">
                                    <i class="bi bi-hourglass-split fs-1 mb-2 d-block text-warning"></i>
                                    Menunggu tanda tangan asesi
                                </div>
                            @endif
                        </div>
                        @if ($formKerahasiaan->tanggal_ttd_asesi)
                            <div class="text-center mt-3">
                                <div class="timeline-badge">
                                    <i class="bi bi-calendar-check text-success"></i>
                                    <span>Ditandatangani pada:
                                        {{ \Carbon\Carbon::parse($formKerahasiaan->tanggal_ttd_asesi)->format('d F Y, H:i') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Status Timeline --}}
                @if ($formKerahasiaan->status === 'completed')
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Formulir Selesai</h6>
                                <p class="mb-0 small">
                                    Formulir kerahasiaan ini telah ditandatangani oleh asesor dan asesi. Proses asesmen dapat dilanjutkan.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hourglass-split fs-3 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Menunggu Asesi</h6>
                                <p class="mb-0 small">
                                    Formulir telah ditandatangani oleh asesor dan saat ini menunggu tanda tangan asesi untuk menyelesaikan perjanjian kerahasiaan.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Action Bar --}}
            <div class="action-bar">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Terakhir diperbarui:
                        {{ $formKerahasiaan->updated_at->format('d F Y, H:i') }}
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.mapa.show', $mapa->id) }}" class="btn btn-outline-secondary btn-action">
                            <i class="bi bi-arrow-left me-1"></i>Kembali ke MAPA
                        </a>
                        <button class="btn btn-primary btn-action" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i>Cetak Formulir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Print styling
        window.addEventListener('beforeprint', function() {
            document.querySelectorAll('.btn, .breadcrumb-custom')?.forEach(el => el.classList.add('d-print-none'));
        });
    </script>
@endpush