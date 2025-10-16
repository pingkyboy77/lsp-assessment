{{-- resources/views/asesi/form-kerahasiaan/sign.blade.php --}}
@extends('layouts.admin')

@push('styles')
    <style>
        .signature-pad {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: crosshair;
            background: white;
        }

        .main-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .card-header-custom {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: 4px solid #047857;
        }

        .info-card {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 1rem;
        }

        .info-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .statement-box {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .signature-section {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
        }

        .asesor-signature {
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }

        .asesor-signature img {
            max-height: 150px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background: white;
            padding: 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <div class="main-card">
            <div class="card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold">
                            <i class="bi bi-shield-lock me-2"></i>Tandatangani Formulir Kerahasiaan
                        </h5>
                        <p class="mb-0 opacity-90">FR.AK.01 - Formulir Persetujuan Asesmen dan Kerahasiaan</p>
                    </div>
                    <a href="{{ route('asesi.ak07.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="card-body m-3">
                {{-- Assessment Information --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Informasi Asesmen
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Nama Asesi</label>
                                <div class="fw-semibold">{{ $formKerahasiaan->nama_asesi }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Nama Asesor</label>
                                <div class="fw-semibold">{{ $formKerahasiaan->nama_asesor }}</div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-muted small">Skema Sertifikasi</label>
                                <div class="fw-semibold">{{ $formKerahasiaan->skema_sertifikasi }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Tanggal Asesmen</label>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($formKerahasiaan->tanggal_asesmen)->format('d F Y') }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Jam Mulai</label>
                                <div class="fw-semibold">{{ $formKerahasiaan->jam_mulai }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assessor Signature --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-person-check me-2"></i>Tanda Tangan Asesor
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-2">Tanda Tangan</label>
                                <div class="asesor-signature">
                                    <img src="{{ $formKerahasiaan->ttd_asesor }}" alt="Tanda Tangan Asesor"
                                        class="img-fluid">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Tanggal</label>
                                <div class="fw-semibold text-success">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ \Carbon\Carbon::parse($formKerahasiaan->tanggal_ttd_asesor)->format('d F Y, H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assessee Sign Form --}}
                <form id="signAsesiForm">
                    @csrf

                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-person-badge me-2"></i>Pernyataan Asesi
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="statement-box mb-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-shield-check me-2"></i>Pernyataan Kerahasiaan
                                </h6>
                                <p class="text-dark mb-0 lh-lg text-capitalize" style="text-align: justify;">
                                    Bahwa saya telah mendapatkan penjelasan terkait hak dan prosedur banding asesmen dari asesor. Dan Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Tanda Tangan Anda <span class="text-danger">*</span>
                                </label>
                                <div class="signature-section">
                                    <canvas id="signaturePadAsesi" class="signature-pad" width="600" height="200">
                                    </canvas>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="clearSignature()">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Hapus Tanda Tangan
                                    </button>
                                </div>
                                <input type="hidden" name="ttd_asesi" id="ttdAsesi" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal</label>
                                <input type="text" class="form-control" value="{{ now()->format('d F Y') }}" readonly>
                                <small class="text-muted">Otomatis terisi dengan tanggal hari ini</small>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-success border-0 shadow-sm mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Konfirmasi Tanda Tangan</h6>
                                        <p class="mb-0 small">
                                            Dengan menandatangani formulir ini, Anda menyetujui persyaratan kerahasiaan dan
                                            proses konsultasi akan dinyatakan selesai.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('asesi.ak07.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Submit Form
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const canvas = document.getElementById('signaturePadAsesi');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });

        function clearSignature() {
            signaturePad.clear();
        }

        document.getElementById('signAsesiForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda Tangan Diperlukan',
                    text: 'Silakan berikan tanda tangan Anda'
                });
                return;
            }

            const signatureData = signaturePad.toDataURL();

            Swal.fire({
                title: 'Konfirmasi Tanda Tangan',
                text: 'Apakah Anda yakin ingin menandatangani formulir kerahasiaan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tandatangani',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitSignature(signatureData);
                }
            });
        });

        function submitSignature(signatureData) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('asesi.form-kerahasiaan.store-signature', $formKerahasiaan->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ttd_asesi: signatureData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: 'Terjadi kesalahan: ' + error.message
                    });
                });
        }
    </script>
@endpush