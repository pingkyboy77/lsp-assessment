@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
    <style>
        .signature-canvas {
            border: 2px solid #dee2e6;
            border-radius: 6px;
            background: white;
            cursor: crosshair;
            max-width: 500px;
            width: 100%;
            height: 200px;
            margin: 0 auto;
            display: block;
        }
        .info-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
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
        .signature-img {
            max-width: 200px;
            height: auto;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="bi bi-pen me-2"></i>Tanda Tangan FR.AK.07</h4>
                <p class="mb-0 opacity-90">Silakan baca dokumen dan berikan tanda tangan Anda</p>
            </div>
            <a href="{{ route('asesi.ak07.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="main-card">
        <!-- Info Dokumen -->
        <div class="info-card mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small text-muted mb-1">Skema Sertifikasi</label>
                    <div class="fw-semibold">{{ $ak07->mapa->certificationScheme->nama }}</div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted mb-1">Asesor</label>
                    <div class="fw-semibold">{{ $ak07->asesor->name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted mb-1">Tanggal Dibuat</label>
                    <div>{{ \Carbon\Carbon::parse($ak07->created_at)->format('d F Y') }}</div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted mb-1">Tanggal TTD Asesor</label>
                    <div>{{ \Carbon\Carbon::parse($ak07->tanggal_ttd_asesor)->format('d F Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Alert Info -->
        <div class="alert alert-info mb-4 m-3">
            <div class="d-flex align-items-start">
                <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                <div>
                    <h6 class="mb-2 fw-bold">Informasi Penting</h6>
                    <ul class="mb-0 small ps-3">
                        <li>Pastikan Anda telah membaca seluruh isi dokumen FR.AK.07 di bawah ini</li>
                        <li>Tanda tangan digital ini memiliki kekuatan hukum yang sama dengan tanda tangan basah</li>
                        <li>Setelah menandatangani, dokumen tidak dapat diubah kecuali dibuka kembali oleh admin</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Potensi Asesi -->
        <div class="detail-section">
            <div class="section-title-small">
                <i class="bi bi-person-check me-2 text-primary"></i>Potensi Asesi
            </div>
            @php
                $potensiAsesiOptions = \App\Models\KelompokKerja::POTENSI_ASESI_OPTIONS;
                $selectedPotensi = is_array($ak07->potensi_asesi) ? $ak07->potensi_asesi : json_decode($ak07->potensi_asesi, true);
            @endphp
            @if(!empty($selectedPotensi))
                @foreach($potensiAsesiOptions as $key => $label)
                    @if(in_array($key, $selectedPotensi))
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

        <!-- Pertanyaan & Jawaban yang diisi Asesor -->
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
                    'Pertimbangan budaya/tradisi/agama?'
                ];
                
                // Decode JSON jika masih string
                $questionsData = is_string($ak07->questions_data) 
                    ? json_decode($ak07->questions_data, true) 
                    : $ak07->questions_data;
            @endphp
            <div class="row">
                @foreach($questions as $i => $question)
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
                                    @if($answer === 'Ya')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>{{ $answer }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-x-circle me-1"></i>{{ $answer }}
                                        </span>
                                    @endif
                                </div>
                                @if($answer === 'Ya' && !empty($keterangan))
                                    <div class="mt-2">
                                        <label class="small text-muted fw-semibold">Keterangan:</label>
                                        <ul class="small mb-0 ps-3">
                                            @foreach($keterangan as $ket)
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
                $instrumenData = $adjustmentsData['instrumen_asesmen'] ?? ['answer' => 'Tidak', 'keterangan' => null];
            @endphp
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card border h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">1. Acuan Pembanding Asesmen</h6>
                            @if($acuanData['answer'] === 'Ya')
                                <span class="badge bg-success mb-2">
                                    <i class="bi bi-check-circle me-1"></i>{{ $acuanData['answer'] }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark border mb-2">
                                    <i class="bi bi-x-circle me-1"></i>{{ $acuanData['answer'] }}
                                </span>
                            @endif
                            @if($acuanData['answer'] === 'Ya' && !empty($acuanData['keterangan']))
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
                            @if($metodeData['answer'] === 'Ya')
                                <span class="badge bg-success mb-2">
                                    <i class="bi bi-check-circle me-1"></i>{{ $metodeData['answer'] }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark border mb-2">
                                    <i class="bi bi-x-circle me-1"></i>{{ $metodeData['answer'] }}
                                </span>
                            @endif
                            @if($metodeData['answer'] === 'Ya' && !empty($metodeData['keterangan']))
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
                            @if($instrumenData['answer'] === 'Ya')
                                <span class="badge bg-success mb-2">
                                    <i class="bi bi-check-circle me-1"></i>{{ $instrumenData['answer'] }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark border mb-2">
                                    <i class="bi bi-x-circle me-1"></i>{{ $instrumenData['answer'] }}
                                </span>
                            @endif
                            @if($instrumenData['answer'] === 'Ya' && !empty($instrumenData['keterangan']))
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

        <!-- Tanda Tangan Asesor -->
        <div class="detail-section">
            <div class="section-title-small">
                <i class="bi bi-pen me-2 text-primary"></i>Tanda Tangan Asesor
            </div>
            <div class="card border">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <strong>{{ $ak07->nama_asesor }}</strong>
                    </div>
                    @if($ak07->asesor_signature)
                        <img src="{{ Storage::url($ak07->asesor_signature) }}" 
                             alt="TTD Asesor" class="signature-img mb-2">
                        <div class="small text-muted">
                            <i class="bi bi-calendar3"></i>
                            {{ \Carbon\Carbon::parse($ak07->tanggal_ttd_asesor)->format('d F Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-4">

        <!-- Form Tanda Tangan Asesi -->
        <form id="signatureForm" method="POST" action="{{ route('asesi.ak07.submit-signature', $ak07->id) }}">
            @csrf
            
            <div class="card border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0 fw-bold text-warning">
                        <i class="bi bi-pen-fill me-2"></i>Tanda Tangan Asesi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Perhatian:</strong> Dengan menandatangani dokumen ini, Anda menyatakan telah membaca dan menyetujui seluruh isi FR.AK.07 di atas.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Asesi</label>
                        <input type="text" class="form-control" 
                               value="{{ auth()->user()->name }}" readonly>
                    </div>
                    <input type="hidden" name="asesi_tanggal_tanda_tangan" value="{{ date('Y-m-d') }}">

                    <div class="mt-4">
                        <label class="form-label fw-semibold text-center d-block mb-3">
                            Silakan tanda tangan di area di bawah ini:
                        </label>
                        <canvas id="signaturePad" class="signature-canvas"></canvas>
                        <input type="hidden" name="asesi_signature" id="signatureData">
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-secondary" id="clearSignature">
                                <i class="bi bi-eraser me-1"></i>Hapus & Tanda Tangan Ulang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4 m-3">
                <a href="{{ route('asesi.ak07.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i>Simpan Tanda Tangan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signaturePad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255,255,255)',
        penColor: 'rgb(0,0,0)'
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    document.getElementById('clearSignature').addEventListener('click', () => {
        signaturePad.clear();
    });

    document.getElementById('signatureForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (signaturePad.isEmpty()) {
            Swal.fire({
                icon: 'warning',
                title: 'Tanda Tangan Diperlukan',
                text: 'Harap tanda tangan terlebih dahulu',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Tanda Tangan',
            html: 'Dengan menandatangani dokumen ini, Anda menyetujui seluruh isi FR.AK.07.<br><br><strong>Apakah Anda yakin?</strong>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#F59E0B',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Tandatangani',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('signatureData').value = signaturePad.toDataURL();
                
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                this.submit();
            }
        });
    });
});
</script>
@endpush