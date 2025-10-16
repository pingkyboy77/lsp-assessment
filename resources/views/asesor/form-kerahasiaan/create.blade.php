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

        .statement-box {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
            border-left: 4px solid #0284c7;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
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

        .signature-pad {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: crosshair;
            background: white;
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

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #667eea;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .editable-section {
            background: #fffbeb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #f59e0b;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <div class="form-view-card">
            {{-- Header --}}
            <div class="form-header">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h4 class="mb-2 fw-bold">
                            <i class="bi bi-shield-lock me-2"></i>FR.AK.01 - Formulir Persetujuan Asesmen dan Kerahasiaan
                        </h4>
                        <p class="mb-0 opacity-90">Formulir Persetujuan Asesmen dan Kerahasiaan</p>
                    </div>
                    <a href="{{ route('asesor.mapa.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form id="formKerahasiaanForm">
                @csrf

                <div class="p-4">
                    {{-- Assessment Information --}}
                    <h5 class="section-title">
                        <i class="bi bi-info-circle me-2"></i>Informasi Asesmen
                    </h5>
                    <div class="info-section">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="bi bi-person me-2 text-primary"></i>Nama Asesi
                            </div>
                            <div class="info-value">{{ $delegasi->asesi->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="bi bi-person-badge me-2 text-success"></i>Nama Asesor
                            </div>
                            <div class="info-value">{{ $delegasi->asesor->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="bi bi-award me-2 text-warning"></i>Skema Sertifikasi
                            </div>
                            <div class="info-value">{{ $delegasi->certificationScheme->nama }}</div>
                        </div>
                    </div>

                    {{-- Editable Schedule --}}
                    <h5 class="section-title">
                        <i class="bi bi-calendar-event me-2"></i>Jadwal Asesmen
                    </h5>
                    <div class="editable-section">
                        {{-- <div class="alert alert-warning border-0 mb-3">
                            <i class="bi bi-pencil-square me-2"></i>
                            <strong>Bagian ini dapat diedit.</strong> Perubahan akan memperbarui jadwal di Delegasi dan Permintaan TUK.
                        </div> --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-date me-1"></i>Tanggal Asesmen 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal_asesmen" class="form-control"
                                    value="{{ $delegasi->tanggal_pelaksanaan_asesmen?->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-clock me-1"></i>Jam Mulai 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="jam_mulai" class="form-control" value="{{ $jamMulai }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    {{-- Assessor Statement --}}
                    <h5 class="section-title">
                        <i class="bi bi-chat-quote me-2"></i>Pernyataan Asesor
                    </h5>
                    <div class="statement-box">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="bi bi-shield-check me-2"></i>Perjanjian Kerahasiaan
                        </h6>
                        <p class="text-dark mb-0 lh-lg" style="text-align: justify;">
                            Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai
                            Asesor dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang
                            berwenang sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
                        </p>
                    </div>

                    {{-- Signature Section --}}
                    <h5 class="section-title">
                        <i class="bi bi-pen me-2"></i>Tanda Tangan Asesor
                    </h5>
                    <div class="signature-display mb-3">
                        <canvas id="signaturePadAsesor" class="signature-pad" width="600" height="200"></canvas>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Bersihkan Tanda Tangan
                        </button>
                    </div>
                    <input type="hidden" name="ttd_asesor" id="ttdAsesor" required>

                    {{-- Current Date Display --}}
                    <div class="info-section">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="bi bi-calendar-check me-2 text-success"></i>Tanggal Penandatanganan
                            </div>
                            <div class="info-value">
                                {{ now()->format('d F Y') }}
                                <small class="text-muted ms-2">(Otomatis terisi dengan tanggal hari ini)</small>
                            </div>
                        </div>
                    </div>

                    {{-- Information Alert --}}
                    {{-- <div class="alert alert-info border-0 shadow-sm mt-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Informasi Penting</h6>
                                <p class="mb-0 small">
                                    Setelah Anda menandatangani formulir ini, formulir akan dikirim ke Asesi untuk ditandatangani.
                                    Proses asesmen dapat dilanjutkan setelah kedua belah pihak menandatangani formulir kerahasiaan ini.
                                </p>
                            </div>
                        </div>
                    </div> --}}
                </div>

                {{-- Action Bar --}}
                <div class="action-bar">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('asesor.mapa.index') }}" class="btn btn-outline-secondary btn-action">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-success btn-action">
                            <i class="bi bi-save me-1"></i>Submit Form
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const canvas = document.getElementById('signaturePadAsesor');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });

        function clearSignature() {
            signaturePad.clear();
        }

        document.getElementById('formKerahasiaanForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda Tangan Diperlukan',
                    text: 'Silakan berikan tanda tangan Anda terlebih dahulu'
                });
                return;
            }

            const signatureData = signaturePad.toDataURL();
            document.getElementById('ttdAsesor').value = signatureData;

            const formData = new FormData(this);
            const data = {
                tanggal_asesmen: formData.get('tanggal_asesmen'),
                jam_mulai: formData.get('jam_mulai'),
                ttd_asesor: signatureData
            };

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('asesor.form-kerahasiaan.store', $delegasi->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
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
                        title: 'Error!',
                        text: 'Terjadi kesalahan: ' + error.message
                    });
                });
        });
    </script>
@endpush