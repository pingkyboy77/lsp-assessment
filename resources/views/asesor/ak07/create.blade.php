@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
    <style>
        /* Simple & Clean Design */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .page-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .page-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.95;
            font-size: 0.9rem;
        }

        .main-form-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .section-divider {
            border-top: 2px solid #f1f3f5;
            margin: 2rem 0;
            position: relative;
        }

        .section-divider::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .section-title i {
            font-size: 1.2rem;
            margin-right: 0.5rem;
            color: #667eea;
        }

        .info-row {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .question-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
        }

        .question-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #212529;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: start;
        }

        .question-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .form-check {
            padding: 0.5rem;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .form-check:hover {
            background: white;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 0.15rem;
        }

        .form-check-label {
            font-size: 0.875rem;
            margin-left: 0.5rem;
        }

        .keterangan-box {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            border: 1px dashed #dee2e6;
            margin-top: 0.75rem;
            display: none;
        }

        .keterangan-box.show {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .signature-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .signature-canvas {
            border: 2px solid #dee2e6;
            border-radius: 6px;
            background: white;
            cursor: crosshair;
            max-width: 400px;
            width: 100%;
            height: 180px;
            margin: 0 auto;
            display: block;
        }

        .btn-sm {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }

        .hasil-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
        }

        /* Enhanced Potensi Asesi Styles */
        .potensi-checkbox-container {
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .main-form-card {
                padding: 1.5rem;
            }

            .section-title {
                font-size: 1rem;
            }

            .question-item {
                padding: 0.75rem;
            }

            .signature-canvas {
                height: 150px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Card -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4><i class="bi bi-clipboard2-check me-2"></i>FR.AK.07 - Ceklis Penyesuaian Yang Wajar dan Beralasan
                    </h4>
                </div>
                <a href="{{ route('asesor.mapa.view', $mapa->id) }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="main-form-card">
            <form id="ak07Form" method="POST" action="{{ route('asesor.ak07.store', $mapa->id) }}">
                @csrf

                <!-- Info MAPA -->
                <div class="section-title">
                    <i class="bi bi-info-circle"></i>
                    <span>Informasi MAPA</span>
                </div>
                <div class="info-row">
                    <div class="row g-3 w-100">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Nama Asesi</label>
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $mapa->delegasi->asesi->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Skema Sertifikasi</label>
                            <textarea class="form-control form-control-sm" style="height: auto; resize: none; white-space: pre-wrap;" readonly>{{ $mapa->certificationScheme->nama }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Enhanced Potensi Asesi Section -->
                <div class="section-title">
                    <i class="bi bi-person-check"></i>
                    <span>Potensi Asesi <span class="text-danger">*</span></span>
                </div>

                <div class="alert alert-info mb-3">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-2 fw-bold">Potensi Asesi Dipilih Otomatis</h6>
                            <p class="mb-0 small">
                                Berdasarkan <strong>MAPA P{{ $mapa->p_level }}</strong>, potensi asesi berikut telah dipilih otomatis 
                                dari <strong>Kelompok Kerja terkait</strong> dan <strong class="text-danger">tidak dapat diubah</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        @php
                            $potensiAsesiOptions = \App\Models\KelompokKerja::POTENSI_ASESI_OPTIONS;
                            // Jika tidak ada yang auto-selected, maka default pilih P1
                            $selectedPotensi = !empty($autoSelectedPotensiAsesi) ? $autoSelectedPotensiAsesi : ['p1'];
                        @endphp
                        
                        @foreach($potensiAsesiOptions as $key => $label)
                            @php
                                $isSelected = in_array($key, $selectedPotensi);
                            @endphp
                            <div class="form-check mb-3 p-3 border rounded potensi-checkbox-container {{ $isSelected ? 'bg-success bg-opacity-10 border-success' : 'bg-light' }}"
                                 id="potensi-{{ $key }}-container">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="{{ $key }}" 
                                       name="potensi_asesi_display[]"
                                       value="{{ $key }}"
                                       {{ $isSelected ? 'checked' : '' }} 
                                       disabled>
                                <label class="form-check-label d-flex align-items-start {{ $isSelected ? '' : 'text-muted' }}" for="{{ $key }}">
                                    <span class="flex-grow-1">{{ $label }}</span>
                                    @if($isSelected)
                                        <span class="badge bg-success ms-2">
                                            <i class="bi bi-check-circle me-1"></i>Terpilih
                                        </span>
                                    @endif
                                </label>
                                @if($isSelected)
                                    <input type="hidden" name="potensi_asesi[]" value="{{ $key }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Pertanyaan Karakteristik -->
                <div class="section-title">
                    <i class="bi bi-list-check"></i>
                    <span>Persyaratan Modifikasi dan Kontekstualisasi</span>
                </div>

                @php
                    $questions = [
                        [
                            'text' => 'Keterbatasan asesi terhadap persyaratan bahasa, literasi, numerasi?',
                            'items' => [
                                'Dukungan pembaca/penerjemah',
                                'Asesmen verbal dengan gambar',
                                'Ceklis observasi/demonstrasi',
                                'Daftar instruksi terstruktur',
                                'Lainnya',
                            ],
                        ],
                        [
                            'text' => 'Penyediaan dukungan pembaca, penerjemah, pelayan, penulis?',
                            'items' => [
                                'Pertanyaan lisan dengan visual',
                                'Pertanyaan wawancara dengan visual',
                                'Lainnya',
                            ],
                        ],
                        [
                            'text' => 'Penggunaan teknologi adaptif atau peralatan khusus?',
                            'items' => [
                                'Ceklis observasi/demonstrasi',
                                'Pertanyaan lisan',
                                'Pertanyaan tertulis',
                                'Pertanyaan wawancara',
                                'Daftar instruksi',
                                'Ceklis verifikasi portofolio',
                                'Dukungan operator komputer',
                                'Lainnya',
                            ],
                        ],
                        [
                            'text' => 'Pelaksanaan asesmen fleksibel (keletihan/pengobatan)?',
                            'items' => [
                                'Juru tulis',
                                'Kameramen perekam',
                                'Waktu lebih panjang',
                                'Waktu lebih pendek',
                                'Instruksi spesifik proyek',
                                'Lainnya',
                            ],
                        ],
                        [
                            'text' => 'Penyediaan peralatan asesmen (braille, audio/video-tape)?',
                            'items' => ['Pertanyaan lisan', 'Pertanyaan wawancara', 'Lainnya'],
                        ],
                        [
                            'text' => 'Penyesuaian tempat fisik/lingkungan asesmen?',
                            'items' => [
                                'Pertanyaan lisan',
                                'Pertanyaan tulis',
                                'Pertanyaan wawancara',
                                'Ceklis verifikasi portofolio',
                                'Ceklis reviu produk',
                                'Daftar instruksi',
                                'Lainnya',
                            ],
                        ],
                        [
                            'text' => 'Pertimbangan umur/usia lanjut/gender asesi?',
                            'items' => [
                                'Studi kasus/instruksi terstruktur',
                                'Instrumen huruf normal',
                                'Asesor gender sama',
                                'Instrumen sama semua gender',
                                'Lainnya',
                            ],
                        ],
                        [
                            'text' => 'Pertimbangan budaya/tradisi/agama?',
                            'items' => [
                                'Studi kasus/instruksi terstruktur',
                                'Asesor tanpa pertimbangan budaya',
                                'Instrumen sama untuk semua',
                                'Lainnya',
                            ],
                        ],
                    ];
                @endphp

                <div class="row">
                    @foreach ($questions as $i => $q)
                        <div class="col-md-6 mb-3">
                            <div class="question-item">
                                <div class="question-label">
                                    <span class="question-number">{{ $i + 1 }}</span>
                                    <span>{{ $q['text'] }}</span>
                                </div>

                                <div class="d-flex gap-3 mb-2 justify-content-end">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="q{{ $i + 1 }}_answer"
                                            id="q{{ $i + 1 }}_ya" value="Ya" required>
                                        <label class="form-check-label" for="q{{ $i + 1 }}_ya">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="q{{ $i + 1 }}_answer"
                                            id="q{{ $i + 1 }}_tidak" value="Tidak">
                                        <label class="form-check-label" for="q{{ $i + 1 }}_tidak">Tidak</label>
                                    </div>
                                </div>

                                <div class="keterangan-box" id="keterangan_{{ $i + 1 }}">
                                    <label class="form-label small fw-semibold mb-2">Keterangan <span
                                            class="text-danger">*</span></label>
                                    @foreach ($q['items'] as $j => $item)
                                        <div class="form-check">
                                            <input class="form-check-input keterangan-check" type="checkbox"
                                                id="q{{ $i + 1 }}_k{{ $j }}"
                                                name="q{{ $i + 1 }}_keterangan[]" value="{{ $item }}"
                                                data-question="{{ $i + 1 }}">
                                            <label class="form-check-label"
                                                for="q{{ $i + 1 }}_k{{ $j }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="section-divider"></div>

                <!-- Hasil Penyesuaian -->
                <div class="section-title">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Hasil Penyesuaian yang Disepakati</span>
                </div>

                <div class="row">
                    @foreach ([['name' => 'acuan_pembahasan', 'label' => '1. Acuan Pembanding Asesmen', 'textarea' => 'tulisan_acuan_pembahasan'], ['name' => 'metode_asesmen', 'label' => '2. Metode Asesmen', 'textarea' => 'tulisan_metode_asesmen'], ['name' => 'instrumen_asesmen', 'label' => '3. Instrumen Asesmen', 'textarea' => 'tulisan_instrumen_asesmen']] as $item)
                        <div class="col-md-6 mb-3">
                            <div class="hasil-item">
                                <label class="fw-semibold mb-2">{{ $item['label'] }}</label>
                                <div class="d-flex gap-3 mb-2 justify-content-end">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input hasil-radio" type="radio"
                                            name="{{ $item['name'] }}" id="{{ $item['name'] }}_ya" value="Ya"
                                            required data-target="{{ $item['textarea'] }}">
                                        <label class="form-check-label" for="{{ $item['name'] }}_ya">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input hasil-radio" type="radio"
                                            name="{{ $item['name'] }}" id="{{ $item['name'] }}_tidak" value="Tidak"
                                            data-target="{{ $item['textarea'] }}">
                                        <label class="form-check-label" for="{{ $item['name'] }}_tidak">Tidak</label>
                                    </div>
                                </div>
                                <textarea class="form-control form-control-sm hasil-textarea" id="{{ $item['textarea'] }}"
                                    name="{{ $item['textarea'] }}" rows="2" placeholder="Keterangan..." style="display:none"></textarea>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Nama Asesor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" name="nama_asesor"
                        value="{{ Auth::user()->name }}" readonly>
                </div>

                <div class="section-divider"></div>

                <!-- Tanda Tangan Asesor -->
                <div class="section-title">
                    <i class="bi bi-pen"></i>
                    <span>Tanda Tangan Asesor</span>
                </div>
                <input type="hidden" id="tanggal_ttd_asesor" name="tanggal_ttd_asesor">
                <div class="signature-box mb-4">
                    <label class="form-label fw-semibold mb-3 text-center d-block">Silakan tanda tangan di bawah
                        ini:</label>
                    <canvas id="asesorSignaturePad" class="signature-canvas"></canvas>
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-3" id="clearAsesorSignature">
                            <i class="bi bi-eraser me-1"></i>Hapus & Tanda Tangan Ulang
                        </button>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Tanda Tangan Asesi - Only show if role is asesi -->
                @if(auth()->user()->hasRole('asesi'))
                <div class="section-title" id="asesi-signature-section">
                    <i class="bi bi-pen-fill"></i>
                    <span>Tanda Tangan Asesi</span>
                </div>
                <div class="alert alert-info alert-sm mb-3">
                    <i class="bi bi-info-circle me-2"></i>Setelah menyimpan, Asesi akan diminta menandatangani.
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label small">Nama Asesi</label>
                        <input type="text" class="form-control form-control-sm"
                            value="{{ $mapa->delegasi->asesi->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Tanggal Tanda Tangan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="asesi_tanggal_tanda_tangan"
                            required>
                    </div>
                </div>
                <div class="signature-box mb-4">
                    <label class="form-label fw-semibold mb-3 text-center d-block">Tanda tangan Asesi:</label>
                    <canvas id="asesiSignaturePad" class="signature-canvas"></canvas>
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-3" id="clearAsesiSignature">
                            <i class="bi bi-eraser me-1"></i>Hapus & Tanda Tangan Ulang
                        </button>
                    </div>
                </div>

                <div class="section-divider"></div>
                @endif

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-end pt-4 mt-4 border-top" style="position: sticky; bottom: 0; background: white; padding: 1rem; margin: -1rem -2rem 0 -2rem; border-radius: 0 0 12px 12px; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 10;">
                    <a href="{{ route('asesor.mapa.view', $mapa->id) }}" class="btn btn-outline-secondary btn-md">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-success btn-md">
                        <i class="bi bi-save me-2"></i>Simpan FR.AK.07
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
            // Set tanggal otomatis
            document.getElementById('tanggal_ttd_asesor').value = new Date().toISOString().split('T')[0];

            // Show success/error messages from session
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#10B981',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#EF4444',
                    showConfirmButton: true
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: '<ul class="text-start">' + 
                        '@foreach($errors->all() as $error)' +
                        '<li>{{ $error }}</li>' +
                        '@endforeach' +
                        '</ul>',
                    confirmButtonColor: '#EF4444',
                    showConfirmButton: true
                });
            @endif

            // Toggle keterangan untuk pertanyaan
            document.querySelectorAll('input[type="radio"][name^="q"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const num = this.name.match(/\d+/)[0];
                    const keteranganBox = document.getElementById('keterangan_' + num);
                    if (keteranganBox) {
                        if (this.value === 'Ya') {
                            keteranganBox.classList.add('show');
                            keteranganBox.querySelectorAll('.keterangan-check').forEach(chk => chk
                                .required = true);
                        } else {
                            keteranganBox.classList.remove('show');
                            keteranganBox.querySelectorAll('.keterangan-check').forEach(chk => {
                                chk.checked = false;
                                chk.required = false;
                            });
                        }
                    }
                });
            });

            // Toggle textarea untuk hasil penyesuaian
            document.querySelectorAll('.hasil-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const textarea = document.getElementById(this.dataset.target);
                    if (this.value === 'Ya') {
                        textarea.style.display = 'block';
                        textarea.required = true;
                    } else {
                        textarea.style.display = 'none';
                        textarea.required = false;
                        textarea.value = '';
                    }
                });
            });

            // Signature Pads
            function initSignaturePad(canvasId, clearBtnId) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return null;

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

                document.getElementById(clearBtnId)?.addEventListener('click', () => signaturePad.clear());
                return signaturePad;
            }

            const asesorPad = initSignaturePad('asesorSignaturePad', 'clearAsesorSignature');
            const asesiPad = initSignaturePad('asesiSignaturePad', 'clearAsesiSignature');

            // Validasi form
            document.getElementById('ak07Form').addEventListener('submit', function(e) {
                e.preventDefault();
                const role = "{{ auth()->user()->getRoleNames()->first() }}";
                const form = this;

                // Validasi semua radio button pertanyaan sudah dijawab
                let allQuestionsAnswered = true;
                let unansweredQuestions = [];
                
                for (let i = 1; i <= 8; i++) {
                    const answered = document.querySelector(`input[name="q${i}_answer"]:checked`);
                    if (!answered) {
                        allQuestionsAnswered = false;
                        unansweredQuestions.push(i);
                    }
                    
                    // Jika jawaban "Ya", cek apakah ada keterangan yang dipilih
                    if (answered && answered.value === 'Ya') {
                        const keteranganChecked = document.querySelectorAll(`input[name="q${i}_keterangan[]"]:checked`).length;
                        if (keteranganChecked === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Keterangan Diperlukan',
                                text: `Pertanyaan ${i}: Harap pilih minimal satu keterangan karena Anda menjawab "Ya"`,
                                confirmButtonColor: '#F59E0B',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                    }
                }

                if (!allQuestionsAnswered) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pertanyaan Belum Lengkap',
                        html: `Harap jawab semua pertanyaan. Pertanyaan yang belum dijawab: <strong>${unansweredQuestions.join(', ')}</strong>`,
                        confirmButtonColor: '#F59E0B',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Validasi Hasil Penyesuaian
                const hasilFields = ['acuan_pembahasan', 'metode_asesmen', 'instrumen_asesmen'];
                let hasilComplete = true;
                let missingHasil = [];

                hasilFields.forEach(field => {
                    const answered = document.querySelector(`input[name="${field}"]:checked`);
                    if (!answered) {
                        hasilComplete = false;
                        missingHasil.push(field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
                    } else if (answered.value === 'Ya') {
                        const textarea = document.querySelector(`textarea[name="tulisan_${field}"]`);
                        if (!textarea.value.trim()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Keterangan Diperlukan',
                                text: `Harap isi keterangan untuk: ${field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}`,
                                confirmButtonColor: '#F59E0B',
                                confirmButtonText: 'OK'
                            });
                            hasilComplete = false;
                            return false;
                        }
                    }
                });

                if (!hasilComplete) {
                    if (missingHasil.length > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hasil Penyesuaian Belum Lengkap',
                            html: `Harap lengkapi bagian: <strong>${missingHasil.join(', ')}</strong>`,
                            confirmButtonColor: '#F59E0B',
                            confirmButtonText: 'OK'
                        });
                    }
                    return false;
                }

                // Validasi tanda tangan asesor (wajib untuk semua role)
                if (asesorPad && asesorPad.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanda Tangan Diperlukan',
                        text: 'Harap tanda tangan Asesor terlebih dahulu.',
                        confirmButtonColor: '#F59E0B',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Validasi tanda tangan asesi (hanya jika role asesi)
                if (role === 'asesi' && asesiPad && asesiPad.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanda Tangan Diperlukan',
                        text: 'Harap tanda tangan Asesi terlebih dahulu.',
                        confirmButtonColor: '#F59E0B',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Konfirmasi sebelum submit
                Swal.fire({
                    title: 'Konfirmasi Penyimpanan',
                    text: 'Apakah Anda yakin ingin menyimpan FR.AK.07 ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Simpan signature ke hidden input
                        if (asesorPad && !asesorPad.isEmpty()) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'asesor_signature';
                            input.value = asesorPad.toDataURL();
                            form.appendChild(input);
                        }

                        if (asesiPad && !asesiPad.isEmpty()) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'asesi_signature';
                            input.value = asesiPad.toDataURL();
                            form.appendChild(input);
                        }

                        // Show loading
                        Swal.fire({
                            title: 'Menyimpan Data...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        form.submit();
                    }
                });

                return false;
            });
        });
    </script>
@endpush