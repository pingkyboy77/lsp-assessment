@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
@endpush

@section('content')
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

    .review-section {
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        margin-bottom: 1.5rem;
        background: white;
        overflow: hidden;
    }

    .review-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
    }

    .review-body {
        padding: 1.5rem;
    }

    .kelompok-card {
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        margin-bottom: 1.5rem;
        background: white;
        overflow: hidden;
    }

    .kelompok-header {
        background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }

    .nav-tabs-custom {
        border-bottom: 2px solid var(--gray-200);
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: var(--gray-600);
        padding: 1rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .nav-tabs-custom .nav-link:hover {
        color: var(--primary-color);
        background: var(--gray-50);
    }

    .nav-tabs-custom .nav-link.active {
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
        background: white;
    }

    .mapa-option-card {
        border: 2px solid var(--gray-200);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .mapa-option-card:hover {
        border-color: var(--primary-light);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
        transform: translateY(-2px);
    }

    .mapa-option-card.selected {
        border-color: var(--primary-color);
        background: #F5F3FF;
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.2);
    }

    .accordion-button {
        background: var(--gray-50);
        color: var(--gray-900);
        font-weight: 500;
        padding: 1rem 1.25rem;
        min-height: 70px;
        align-items: center;
    }

    .accordion-button:not(.collapsed) {
        background: #EEF2FF;
        color: var(--primary-color);
        box-shadow: none;
    }

    .unit-code-badge {
        background: var(--primary-color);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .unit-title {
        flex: 1;
        min-width: 0;
        padding: 0 1rem;
        line-height: 1.4;
    }

    .unit-title-text {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-weight: 600;
        color: var(--gray-900);
        word-wrap: break-word;
    }

    .mapa-badge-kombinasi {
        background: linear-gradient(135deg, #DBEAFE 0%, #FEF3C7 100%);
        color: #1E40AF;
    }
</style>

<div class="container-fluid p-4">
    <!-- Main Card -->
    <div class="main-card">
        <!-- Card Header Custom -->
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Edit MAPA
                    </h5>
                    <p class="mb-0 text-muted">
                        Asesi: <strong>{{ $mapa->delegasi->asesi->name }}</strong> | 
                        Skema: <strong>{{ $mapa->certificationScheme->code_1 }}</strong> |
                        Nomor: <strong>{{ $mapa->nomor_mapa }}</strong>
                    </p>
                </div>
                <a href="{{ route('asesor.mapa.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body m-3">

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs-custom mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="review-tab" data-bs-toggle="tab"
                        data-bs-target="#review-content" type="button">
                        <i class="bi bi-eye me-2"></i>Review APL 01 & APL 02
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="planning-tab" data-bs-toggle="tab" data-bs-target="#planning-content"
                        type="button">
                        <i class="bi bi-pencil me-2"></i>Edit Perencanaan MAPA
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">

                <!-- TAB 1: REVIEW APL 01 & APL 02 -->
                <div class="tab-pane fade show active" id="review-content">

                    <!-- APL 01 REVIEW - Using Partial -->
                    @include('asesor.mapa.partials.apl01-review', ['delegasi' => $mapa->delegasi])

                    <!-- APL 02 REVIEW - Using Partial -->
                    @include('asesor.mapa.partials.apl02-review', ['delegasi' => $mapa->delegasi, 'kelompokKerjas' => $kelompokKerjas])

                </div>

                <!-- TAB 2: EDIT PERENCANAAN MAPA -->
                <div class="tab-pane fade" id="planning-content">
                    <form id="mapaForm">
                        @csrf
                        @method('PUT')

                        <!-- Info Box -->
                        <div class="info-box-mapa mb-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-lightbulb-fill info-icon"></i>
                                <div class="flex-grow-1">
                                    <h6>Petunjuk Edit MAPA</h6>
                                    <ul class="small mb-0">
                                        <li>Pilih <strong>level MAPA (P0-P{{ $kelompokKerjas->count() }})</strong> yang sesuai</li>
                                        <li><strong>P0</strong> = Semua kelompok menggunakan metode Langsung</li>
                                        <li><strong>P1, P2, P3, dst</strong> = Jumlah kelompok yang menggunakan metode Tidak Langsung</li>
                                        <li>Contoh: P2 = Kelompok 1-2 Tidak Langsung, sisanya Langsung</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- MAPA Options -->
                        @include('asesor.mapa.partials.mapa-options', [
                            'kelompokKerjas' => $kelompokKerjas,
                            'currentPLevel' => $mapa->p_level
                        ])

                        <!-- Catatan Asesor -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header" style="background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                                <h6 class="mb-0 fw-bold" style="color: var(--gray-900);">
                                    <i class="bi bi-sticky me-2"></i>Catatan Asesor (Opsional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" rows="4" name="catatan_asesor"
                                    placeholder="Tulis catatan umum untuk perencanaan asesmen ini..."
                                    style="border-color: var(--gray-300);">{{ $mapa->catatan_asesor }}</textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('asesor.mapa.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </a>
                            <button type="button" class="btn btn-primary" onclick="saveDraft()">
                                <i class="bi bi-save me-1"></i>Simpan Draft
                            </button>
                            <button type="button" class="btn btn-success" onclick="submitMapa()">
                                <i class="bi bi-send me-1"></i>Simpan & Submit
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
@include('asesor.mapa.partials.document-viewer-modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function selectMapa(pLevel, element) {
        document.querySelectorAll('.mapa-option-card').forEach(card => {
            card.classList.remove('selected');
        });
        element.classList.add('selected');
        const radio = element.querySelector('input[type="radio"]');
        radio.checked = true;
        console.log('MAPA Selected: P' + pLevel);
    }

    function saveDraft() {
        submitForm('draft');
    }

    function submitMapa() {
        const selectedPLevel = document.querySelector('input[name="p_level"]:checked');
        
        if (!selectedPLevel) {
            Swal.fire({
                icon: 'warning',
                title: 'MAPA Belum Dipilih',
                text: 'Silakan pilih level MAPA terlebih dahulu',
            });
            return;
        }

        submitForm('submit');
    }

    function submitForm(action) {
        const formData = new FormData(document.getElementById('mapaForm'));
        formData.append('submit_action', action);

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch("{{ route('asesor.mapa.update', $mapa->id) }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
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
                            <p class="mb-1"><strong>MAPA Code:</strong> ${data.data.mapa_code}</p>
                            <p class="mb-0"><strong>Status:</strong> ${data.data.status}</p>
                        `,
                        timer: 3000,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = "{{ route('asesor.mapa.index') }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.error || 'Terjadi kesalahan saat menyimpan'
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

    function viewDocument(url, title) {
        document.getElementById('documentTitle').textContent = title;
        document.getElementById('documentFrame').src = url;
        new bootstrap.Modal(document.getElementById('documentViewerModal')).show();
    }
</script>
@endpush