{{-- resources/views/asesor/ak07/final-recommendation.blade.php --}}
@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-light: #6366F1;
            --primary-dark: #4338CA;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        .recommendation-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .review-card {
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            background: white;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .review-card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
        }

        .review-card-body {
            padding: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--gray-600);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1rem;
            color: var(--gray-900);
            font-weight: 500;
        }

        .tabs-wrapper {
            margin-bottom: 2rem;
        }

        .nav-tabs-custom {
            border-bottom: 2px solid var(--gray-200);
            display: flex;
            gap: 0;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            background: none;
            color: var(--gray-600);
            padding: 1rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
        }

        .nav-tabs-custom .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--primary-color);
        }

        .nav-tabs-custom .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .recommendation-section {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            background: var(--gray-50);
        }

        .recommendation-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
        }

        .radio-option {
            display: flex;
            align-items: flex-start;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .radio-option:hover {
            border-color: var(--primary-color);
            background: var(--gray-50);
        }

        .radio-option input[type="radio"] {
            margin-top: 0.25rem;
            margin-right: 1rem;
            cursor: pointer;
            accent-color: var(--primary-color);
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .radio-option.selected {
            border-color: var(--primary-color);
            background: rgba(79, 70, 229, 0.05);
        }

        .radio-content {
            flex: 1;
        }

        .radio-label {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .radio-description {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-label .required {
            color: var(--danger-color);
        }

        textarea.form-control {
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-family: inherit;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 120px;
        }

        textarea.form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .signature-pad-container {
            border: 2px dashed var(--gray-300);
            border-radius: 8px;
            background: white;
            padding: 1.5rem;
            text-align: center;
        }

        .signature-canvas {
            border: 2px solid var(--gray-200);
            border-radius: 6px;
            background: white;
            cursor: crosshair;
            width: 100%;
            max-width: 400px;
            height: 150px;
            display: block;
            margin: 1rem auto;
        }

        .signature-hint {
            color: var(--gray-600);
            font-size: 0.85rem;
            margin: 1rem 0;
        }

        .btn-clear-signature {
            background: white;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-clear-signature:hover {
            background: var(--gray-50);
            border-color: var(--gray-500);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
        }

        .btn-primary-action:hover {
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary-action {
            background: white;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }

        .btn-secondary-action:hover {
            background: var(--gray-50);
            border-color: var(--gray-500);
        }

        .alert-info-custom {
            background: rgba(79, 70, 229, 0.05);
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            border-radius: 8px;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
        }

        .alert-warning-custom {
            background: rgba(245, 158, 11, 0.05);
            border-left: 4px solid var(--warning-color);
            padding: 1rem;
            border-radius: 8px;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <div class="recommendation-container">
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="bi bi-clipboard-check me-2"></i>Rekomendasi Final Asesmen
                        </h4>
                        <p class="mb-0 opacity-90">Berikan rekomendasi final untuk asesi berdasarkan hasil AK.07</p>
                    </div>
                    <a href="{{ route('asesor.mapa.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Info Summary -->
            <div class="review-card">
                <div class="review-card-header">
                    <i class="bi bi-info-circle me-2"></i>Ringkasan Asesmen
                </div>
                <div class="review-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Asesi</div>
                            <div class="info-value">{{ $delegasi->asesi->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Skema Sertifikasi</div>
                            <div class="info-value">{{ $delegasi->certificationScheme->nama }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Nomor MAPA</div>
                            <div class="info-value">{{ $mapa->nomor_mapa }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">P-Level</div>
                            <div class="info-value">P{{ $mapa->p_level }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Asesmen</div>
                            <div class="info-value">
                                {{ $delegasi->tanggal_pelaksanaan_asesmen?->format('d M Y') ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert-info-custom">
                                <strong><i class="bi bi-lightning-fill me-2"></i>Bukti Asesmen</strong>
                                <div class="mt-2 small">
                                    <div>
                                        <i class="bi bi-check-circle-fill me-1" style="color: var(--success-color);"></i>
                                        Total Elemen:
                                        <strong>{{ $assessmentSummary['assessment_evidences']['total_elements'] }}</strong>
                                    </div>
                                    <div>
                                        <i class="bi bi-check-circle-fill me-1" style="color: var(--success-color);"></i>
                                        Total Bukti:
                                        <strong>{{ $assessmentSummary['assessment_evidences']['total_evidences'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert-info-custom">
                                <strong><i class="bi bi-file-earmark-check-fill me-2"></i>Status AK.07</strong>
                                <div class="mt-2 small">
                                    <span class="badge bg-success" style="font-size: 0.9rem;">
                                        <i class="bi bi-check-all me-1"></i>{{ ucfirst($ak07->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs for APL 01 & APL 02 Review -->
            <div class="tabs-wrapper">
                <ul class="nav-tabs-custom">
                    <li>
                        <button type="button" class="nav-link active" onclick="switchTab('apl01-tab')">
                            <i class="bi bi-file-earmark me-2"></i>APL 01
                        </button>
                    </li>
                    <li>
                        <button type="button" class="nav-link" onclick="switchTab('apl02-tab')">
                            <i class="bi bi-file-earmark me-2"></i>APL 02
                        </button>
                    </li>
                    <li>
                        <button type="button" class="nav-link" onclick="switchTab('rekomendasi-tab')">
                            <i class="bi bi-star-fill me-2"></i>Rekomendasi Final
                        </button>
                    </li>
                </ul>
            </div>

            <!-- APL 01 Tab -->
            <div id="apl01-tab" class="tab-content active">
                @include('asesor.mapa.partials.apl01-review', ['delegasi' => $delegasi])
            </div>

            <!-- APL 02 Tab -->
            <div id="apl02-tab" class="tab-content">
                @include('asesor.mapa.partials.apl02-review', [
                    'delegasi' => $delegasi,
                    'kelompokKerjas' => $kelompokKerjas ?? [],
                ])
            </div>

            <!-- Rekomendasi Final Tab -->
            <div id="rekomendasi-tab" class="tab-content">
                <form id="finalRecommendationForm">
                    @csrf

                    <div class="recommendation-section">
                        <div class="recommendation-title">
                            <i class="bi bi-star-fill me-2" style="color: var(--warning-color);"></i>
                            Rekomendasi Asesor untuk Asesi
                        </div>

                        <div class="alert-warning-custom">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Pilihan rekomendasi Anda sangat penting untuk menentukan langkah selanjutnya.
                            Pastikan keputusan didasarkan pada bukti asesmen yang telah dikumpulkan.
                        </div>

                        <!-- Option 1: Asesmen Dilanjutkan -->
                        <div class="radio-option" onclick="selectRecommendation('continue')">
                            <input type="radio" name="recommendation" value="continue" id="rec_continue">
                            <div class="radio-content">
                                <div class="radio-label">
                                    <i class="bi bi-check-circle-fill me-2" style="color: var(--success-color);"></i>
                                    Asesmen Dapat Dilanjutkan
                                </div>
                                <div class="radio-description">
                                    Asesi telah menunjukkan kompetensi yang sesuai dan siap untuk dilanjutkan ke tahap
                                    berikutnya.
                                    Sertifikat akan diterbitkan sesuai hasil asesmen.
                                </div>
                            </div>
                        </div>

                        <!-- Option 2: Asesmen Tidak Dilanjutkan -->
                        <div class="radio-option" onclick="selectRecommendation('not_continue')">
                            <input type="radio" name="recommendation" value="not_continue" id="rec_not_continue">
                            <div class="radio-content">
                                <div class="radio-label">
                                    <i class="bi bi-x-circle-fill me-2" style="color: var(--danger-color);"></i>
                                    Asesmen Tidak Dilanjutkan (Reschedule)
                                </div>
                                <div class="radio-description">
                                    Asesi perlu perbaikan lebih lanjut. Asesmen akan direset dan perlu dilakukan review
                                    ulang.
                                    Semua data terkait akan dihapus dan status APL dikembalikan untuk penilaian ulang.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="review-card">
                        <div class="review-card-header">
                            <i class="bi bi-sticky me-2"></i>Catatan Rekomendasi
                        </div>
                        <div class="review-card-body">
                            <div class="form-group">
                                <label class="form-label">
                                    Catatan Asesor 
                                </label>
                                <textarea name="recommendation_notes" class="form-control"
                                    placeholder="Jelaskan alasan rekomendasi Anda. Misalnya: Asesi sudah menunjukkan semua kompetensi yang diperlukan dengan bukti yang lengkap, atau Asesi masih memerlukan perbaikan dalam area X dan Y..."
                                    ></textarea>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Catatan ini akan tersimpan dalam rekam jejak asesmen dan dapat dilihat oleh admin.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Signature Section -->
                    <div class="review-card">
                        <div class="review-card-header">
                            <i class="bi bi-pen me-2"></i>Validasi MAPA & Tanda Tangan Digital
                        </div>
                        <div class="review-card-body">
                            <div class="alert-info-custom mb-4">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Informasi Penting:</strong> Dengan menandatangani, Anda mengkonfirmasi bahwa:
                                <ul class="mt-2 mb-0">
                                    <li>MAPA telah direview dan sesuai dengan perencanaan asesmen</li>
                                    <li>Rekomendasi final telah dipertimbangkan dengan matang</li>
                                    <li>Data asesmen dapat dilanjutkan ke tahap berikutnya</li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Tanda Tangan Validasi MAPA & Rekomendasi <span class="required">*</span>
                                </label>
                                <div class="signature-pad-container">
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-pencil me-1"></i>
                                        Tanda Tangan Digital untuk Validasi MAPA & Rekomendasi Final
                                    </p>
                                    <canvas id="finalRecommendationSignature" class="signature-canvas"></canvas>
                                    <div class="signature-hint">
                                        Tanda tangan di area putih menggunakan mouse atau touchscreen
                                    </div>
                                    <button type="button" class="btn-clear-signature" onclick="clearFinalSignature()">
                                        <i class="bi bi-eraser me-1"></i>Hapus Tanda Tangan
                                    </button>
                                </div>
                                <input type="hidden" name="signature" id="signatureInput">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('asesor.mapa.index') }}" class="btn-action btn-secondary-action">
                            <i class="bi bi-x-circle"></i>Batal
                        </a>
                        <button type="button" class="btn btn-success shadow-sm fw-semibold px-3"
                            onclick="submitFinalRecommendation()">
                            <i class="bi bi-save me-1"></i> Simpan Rekomendasi
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Initialize Signature Pad
            const canvasFinal = document.getElementById('finalRecommendationSignature');
            const signaturePadFinal = new SignaturePad(canvasFinal, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 1,
                maxWidth: 1
            });

            function resizeCanvasFinal() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const canvas = canvasFinal;
                const data = signaturePadFinal.toData();

                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);

                signaturePadFinal.clear();
                signaturePadFinal.fromData(data);
            }

            window.addEventListener('load', () => {
                resizeCanvasFinal();
            });

            window.addEventListener('resize', resizeCanvasFinal);

            function clearFinalSignature() {
                signaturePadFinal.clear();
            }

            function switchTab(tabId) {
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });

                // Remove active from all nav links
                document.querySelectorAll('.nav-tabs-custom .nav-link').forEach(link => {
                    link.classList.remove('active');
                });

                // Show selected tab
                document.getElementById(tabId).classList.add('active');

                // Add active to clicked nav link
                event.target.classList.add('active');

                // Resize canvas if showing rekomendasi tab
                if (tabId === 'rekomendasi-tab') {
                    setTimeout(resizeCanvasFinal, 100);
                }
            }

            function selectRecommendation(value) {
                document.getElementById('rec_' + value).checked = true;

                document.querySelectorAll('.radio-option').forEach(option => {
                    option.classList.remove('selected');
                });

                event.currentTarget.classList.add('selected');
            }

            function submitFinalRecommendation() {
                const recommendation = document.querySelector('input[name="recommendation"]:checked');
                const notes = document.querySelector('textarea[name="recommendation_notes"]').value.trim();

                // Validation
                if (!recommendation) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Rekomendasi Diperlukan',
                        text: 'Silakan pilih rekomendasi terlebih dahulu',
                    });
                    return;
                }


                if (signaturePadFinal.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanda Tangan Diperlukan',
                        text: 'Silakan tanda tangan terlebih dahulu',
                    });
                    return;
                }

                const recommendationType = recommendation.value === 'continue' ?
                    'Dilanjutkan' :
                    'Tidak Dilanjutkan (Reschedule)';

                Swal.fire({
                    title: 'Konfirmasi Rekomendasi',
                    html: `
                        <p class="mb-3">Anda akan menyimpan rekomendasi dengan pilihan:</p>
                        <div style="text-align: left; background: #f3f4f6; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                            <strong style="font-size: 1.1rem;">Rekomendasi: ${recommendationType}</strong>
                            <hr style="margin: 0.75rem 0;">
                            <p style="margin-bottom: 0; color: #666;">${notes.substring(0, 100)}${notes.length > 100 ? '...' : ''}</p>
                        </div>
                        <p class="text-muted small">Tindakan ini akan juga memvalidasi MAPA dan tidak dapat dibatalkan setelah disimpan.</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processFinalRecommendation(recommendation.value, notes);
                    }
                });
            }

            function processFinalRecommendation(recommendation, notes) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const signatureData = signaturePadFinal.toDataURL();

                fetch('{{ route('asesor.ak07.final-recommendation.store', $ak07->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            recommendation: recommendation,
                            recommendation_notes: notes,
                            signature: signatureData
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: `
                                <p>${data.message}</p>
                                <hr>
                                <p class="small text-muted mb-0">
                                    Status: <strong>${data.data.status || data.data.recommendation}</strong>
                                </p>
                            `,
                                timer: 3000,
                                showConfirmButton: true
                            }).then(() => {
                                window.location.href = '{{ route('asesor.mapa.index') }}';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.error || 'Terjadi kesalahan'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan: ' + error.message
                        });
                    });
            }
        </script>
    @endpush
@endsection
