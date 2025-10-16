@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .signature-img {
            max-width: 300px;
            height: auto;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }

        .data-row {
            padding: 0.75rem;
            border-bottom: 1px solid #e9ecef;
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .detail-section {
            background: white;
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
        }

        .section-title-small {
            font-size: 0.95rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .info-row {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4><i class="bi bi-eye me-2"></i>Detail FR.AK.07</h4>
                    <p class="mb-0 opacity-90">Ceklis Penyesuaian Yang Wajar dan Beralasan</p>
                </div>
                <a href="{{ route('asesor.mapa.index', $ak07->mapa_id) }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke MAPA
                </a>
            </div>
        </div>

        <div class="main-card">
            <!-- Info MAPA -->
            <div class="detail-section">
                <div class="section-title-small">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Informasi MAPA
                </div>
                <div class="info-row">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="data-row">
                                <label class="small text-muted mb-1">Nama Asesi</label>
                                <div class="fw-semibold">{{ $ak07->mapa->delegasi->asesi->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-row">
                                <label class="small text-muted mb-1">Skema Sertifikasi</label>
                                <div class="fw-semibold">{{ $ak07->mapa->certificationScheme->nama }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-row">
                                <label class="small text-muted mb-1">Nama Asesor</label>
                                <div class="fw-semibold">{{ $ak07->asesor->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-row">
                                <label class="small text-muted mb-1">Tanggal Dibuat</label>
                                <div>{{ \Carbon\Carbon::parse($ak07->created_at)->format('d F Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Badge -->
            @if ($ak07->status === 'completed')
                <div class="alert alert-success mb-4 m-3">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Status:</strong> Dokumen telah lengkap dan ditandatangani oleh kedua belah pihak
                </div>
            @elseif($ak07->status === 'waiting_asesi')
                <div class="alert alert-warning mb-4 m-3">
                    <i class="bi bi-hourglass-split me-2"></i>
                    <strong>Status:</strong> Menunggu tanda tangan Asesi
                </div>
            @elseif($ak07->status === 'draft')
                <div class="alert alert-secondary mb-4">
                    <i class="bi bi-pencil-square me-2"></i>
                    <strong>Status:</strong> Draft
                </div>
            @endif

            <!-- Potensi Asesi -->
            <div class="detail-section">
                <div class="section-title-small">
                    <i class="bi bi-person-check me-2 text-primary"></i>Potensi Asesi
                </div>
                @php
                    $potensiAsesiOptions = \App\Models\KelompokKerja::POTENSI_ASESI_OPTIONS;
                    $selectedPotensi = is_array($ak07->potensi_asesi)
                        ? $ak07->potensi_asesi
                        : json_decode($ak07->potensi_asesi, true);
                @endphp
                @if (!empty($selectedPotensi))
                    @foreach ($potensiAsesiOptions as $key => $label)
                        @if (in_array($key, $selectedPotensi))
                            <div class="alert alert-success alert-sm mb-2">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ $label }}
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="alert alert-secondary">
                        <i class="bi bi-info-circle me-2"></i>Tidak ada potensi asesi yang dipilih
                    </div>
                @endif
            </div>

            <!-- Pertanyaan & Jawaban -->
            <div class="detail-section">
                <div class="section-title-small">
                    <i class="bi bi-list-check me-2 text-primary"></i>Persyaratan Modifikasi dan Kontekstualisasi
                </div>
                @php
                    $questions = [
                        'Keterbatasan asesi terhadap persyaratan bahasa, literasi, numerasi?',
                        'Penyediaan dukungan pembaca, penerjemah, pelayan, penulis?',
                        'Penggunaan teknologi adaptif atau peralatan khusus?',
                        'Pelaksanaan asesmen fleksibel (keletihan/pengobatan)?',
                        'Penyediaan peralatan asesmen (braille, audio/video-tape)?',
                        'Penyesuaian tempat fisik/lingkungan asesmen?',
                        'Pertimbangan umur/usia lanjut/gender asesi?',
                        'Pertimbangan budaya/tradisi/agama?',
                    ];

                    // Decode JSON jika masih string
                    $questionsData = is_string($ak07->questions_data)
                        ? json_decode($ak07->questions_data, true)
                        : $ak07->questions_data;
                @endphp
                <div class="row">
                    @foreach ($questions as $i => $question)
                        @php
                            $qKey = 'q' . ($i + 1);
                            $qData = $questionsData[$qKey] ?? ['answer' => 'Tidak', 'keterangan' => []];
                            $answer = $qData['answer'] ?? 'Tidak';
                            $keterangan = $qData['keterangan'] ?? [];
                        @endphp
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-2" style="font-size: 0.9rem;">
                                        <span class="badge bg-primary me-2">{{ $i + 1 }}</span>
                                        {{ $question }}
                                    </h6>
                                    <div class="mb-2">
                                        @if ($answer === 'Ya')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>{{ $answer }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-x-circle me-1"></i>{{ $answer }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($answer === 'Ya' && !empty($keterangan))
                                        <div class="mt-2">
                                            <label class="small text-muted fw-semibold">Keterangan:</label>
                                            <ul class="small mb-0 ps-3">
                                                @foreach ($keterangan as $ket)
                                                    <li>{{ $ket }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Hasil Penyesuaian -->
            <div class="detail-section">
                <div class="section-title-small">
                    <i class="bi bi-clipboard-data me-2 text-primary"></i>Hasil Penyesuaian yang Disepakati
                </div>
                @php
                    // Decode adjustments_data JSON
                    $adjustmentsData = is_string($ak07->adjustments_data)
                        ? json_decode($ak07->adjustments_data, true)
                        : $ak07->adjustments_data;

                    $acuanData = $adjustmentsData['acuan_pembahasan'] ?? ['answer' => 'Tidak', 'keterangan' => null];
                    $metodeData = $adjustmentsData['metode_asesmen'] ?? ['answer' => 'Tidak', 'keterangan' => null];
                    $instrumenData = $adjustmentsData['instrumen_asesmen'] ?? [
                        'answer' => 'Tidak',
                        'keterangan' => null,
                    ];
                @endphp
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">1. Acuan Pembanding Asesmen</h6>
                                @if ($acuanData['answer'] === 'Ya')
                                    <span class="badge bg-success mb-2">
                                        <i class="bi bi-check-circle me-1"></i>{{ $acuanData['answer'] }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border mb-2">
                                        <i class="bi bi-x-circle me-1"></i>{{ $acuanData['answer'] }}
                                    </span>
                                @endif
                                @if ($acuanData['answer'] === 'Ya' && !empty($acuanData['keterangan']))
                                    <div class="mt-2 small">
                                        <label class="text-muted fw-semibold">Keterangan:</label>
                                        <p class="mb-0">{{ $acuanData['keterangan'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">2. Metode Asesmen</h6>
                                @if ($metodeData['answer'] === 'Ya')
                                    <span class="badge bg-success mb-2">
                                        <i class="bi bi-check-circle me-1"></i>{{ $metodeData['answer'] }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border mb-2">
                                        <i class="bi bi-x-circle me-1"></i>{{ $metodeData['answer'] }}
                                    </span>
                                @endif
                                @if ($metodeData['answer'] === 'Ya' && !empty($metodeData['keterangan']))
                                    <div class="mt-2 small">
                                        <label class="text-muted fw-semibold">Keterangan:</label>
                                        <p class="mb-0">{{ $metodeData['keterangan'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">3. Instrumen Asesmen</h6>
                                @if ($instrumenData['answer'] === 'Ya')
                                    <span class="badge bg-success mb-2">
                                        <i class="bi bi-check-circle me-1"></i>{{ $instrumenData['answer'] }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border mb-2">
                                        <i class="bi bi-x-circle me-1"></i>{{ $instrumenData['answer'] }}
                                    </span>
                                @endif
                                @if ($instrumenData['answer'] === 'Ya' && !empty($instrumenData['keterangan']))
                                    <div class="mt-2 small">
                                        <label class="text-muted fw-semibold">Keterangan:</label>
                                        <p class="mb-0">{{ $instrumenData['keterangan'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tanda Tangan -->
            <div class="detail-section">
                <div class="section-title-small">
                    <i class="bi bi-pen me-2 text-primary"></i>Tanda Tangan
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-person-badge me-2"></i>Asesor
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <strong>{{ $ak07->asesor->name ?? 'N/A' }}</strong>
                                </div>
                                @if ($ak07->asesor_signature)
                                    <div class="mt-3">
                                        <img src="{{ Storage::url($ak07->asesor_signature) }}" alt="Tanda Tangan Asesor"
                                            class="signature-img">
                                    </div>
                                    <div class="mt-3 small text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($ak07->asesor_signed_at)->format('d F Y') }}
                                    </div>
                                @else
                                    <div class="alert alert-secondary mt-2">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Belum ada tanda tangan
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-person-check me-2"></i>Asesi
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <strong>{{ $ak07->mapa->delegasi->asesi->name }}</strong>
                                </div>
                                @if ($ak07->asesi_signature)
                                    <div class="mt-3">
                                        <img src="{{ Storage::url($ak07->asesi_signature) }}" alt="Tanda Tangan Asesi"
                                            class="signature-img">
                                    </div>
                                    <div class="mt-3 small text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($ak07->asesi_signed_at)->format('d F Y') }}
                                    </div>
                                @else
                                    <div class="alert alert-warning mt-2">
                                        <i class="bi bi-hourglass-split me-2"></i>
                                        Menunggu tanda tangan Asesi
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            {{-- <div class="d-flex gap-2 justify-content-between mt-4 pt-3 border-top">
                <a href="{{ route('asesor.mapa.view', $ak07->mapa_id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <div class="d-flex gap-2">
                    @if($ak07->status !== 'completed')
                        <button type="button" class="btn btn-warning" onclick="window.location.href='{{ route('asesor.ak07.edit', $ak07->id) }}'">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </button>
                    @endif
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Cetak
                    </button>
                </div>
            </div> --}}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Print styles
    window.addEventListener('beforeprint', function() {
        document.querySelector('.page-header')?.classList.add('d-print-none');
        document.querySelectorAll('.btn')?.forEach(btn => btn.classList.add('d-print-none'));
    });
</script>
@endpush