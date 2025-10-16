{{-- resources/views/asesor/mapa/validate.blade.php --}}
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
            transition: all 0.3s;
            cursor: pointer;
        }

        .mapa-option-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }

        .mapa-option-card.selected {
            border-color: var(--success-color);
            background: #ECFDF5;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.15);
        }

        .mapa-badge-kombinasi {
            background: linear-gradient(135deg, #DBEAFE 0%, #FEF3C7 100%);
            color: #1E40AF;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <div class="main-card">
            <div class="card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 text-dark fw-bold">
                            <i class="bi bi-pen me-2"></i>Review & Edit MAPA
                        </h5>
                        <p class="mb-0 text-muted">
                            Asesi: <strong>{{ $mapa->delegasi->asesi->name }}</strong> |
                            Nomor: <strong>{{ $mapa->nomor_mapa }}</strong>
                        </p>
                    </div>
                    <a href="{{ route('asesor.mapa.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="card-body m-3">
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-2">MAPA Telah Diapprove Admin</h6>
                            <p class="mb-2">MAPA Anda telah direview dan diapprove oleh admin. Anda dapat mengubah 
                                pilihan P-Level jika diperlukan sebelum melanjutkan ke AK.07.</p>
                            @if ($mapa->review_notes)
                                <div class="mt-2 p-2 bg-white rounded">
                                    <strong>Catatan dari Admin:</strong>
                                    <p class="mb-0 mt-1">{{ $mapa->review_notes }}</p>
                                </div>
                            @endif
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Catatan:</strong> Validasi MAPA dengan tanda tangan digital akan dilakukan 
                                    bersamaan dengan pemberian Rekomendasi Final setelah AK.07 selesai.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs-custom mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="review-tab" data-bs-toggle="tab"
                            data-bs-target="#review-content" type="button">
                            <i class="bi bi-eye me-2"></i>Review APL 01 & APL 02
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mapa-tab" data-bs-toggle="tab"
                            data-bs-target="#mapa-content" type="button">
                            <i class="bi bi-file-earmark-check me-2"></i>Edit Perencanaan MAPA
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="review-content">
                        @include('asesor.mapa.partials.apl01-review', ['delegasi' => $mapa->delegasi])
                        @include('asesor.mapa.partials.apl02-review', [
                            'delegasi' => $mapa->delegasi,
                            'kelompokKerjas' => $kelompokKerjas,
                        ])
                    </div>

                    <div class="tab-pane fade" id="mapa-content">
                        <form id="editMapaForm">
                            @csrf
                            @method('PUT')

                            <div class="alert alert-warning mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i>
                                    <div>
                                        <h6 class="mb-2 fw-bold">Perhatian!</h6>
                                        <p class="mb-0">
                                            Anda dapat mengubah pilihan P-Level MAPA jika diperlukan. Perubahan ini 
                                            akan mempengaruhi perencanaan asesmen Anda. Pastikan pilihan sudah sesuai 
                                            sebelum melanjutkan ke FR.AK.07.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header"
                                    style="background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                                    <h6 class="mb-0 fw-bold" style="color: var(--gray-900);">
                                        <i class="bi bi-info-circle me-2"></i>Informasi MAPA Saat Ini
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">MAPA Code</small>
                                            <strong class="text-primary">{{ $mapa->mapa_code }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Status</small>
                                            <span class="badge bg-success">{{ ucfirst($mapa->status) }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">P-Level Saat Ini</small>
                                            <strong class="text-primary fs-5">P{{ $mapa->p_level }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Reviewed By</small>
                                            <strong>{{ $mapa->reviewedBy->name ?? '-' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('asesor.mapa.partials.mapa-options', [
                                'kelompokKerjas' => $kelompokKerjas,
                                'currentPLevel' => $mapa->p_level,
                                'readOnly' => false,
                            ])

                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header"
                                    style="background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                                    <h6 class="mb-0 fw-bold" style="color: var(--gray-900);">
                                        <i class="bi bi-sticky me-2"></i>Catatan Asesor (Opsional)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <textarea class="form-control" rows="4" name="catatan_asesor"
                                        placeholder="Tambahkan atau perbarui catatan untuk perencanaan asesmen ini (opsional)..."
                                        style="border-color: var(--gray-300);">{{ $mapa->catatan_asesor }}</textarea>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="bi bi-info-circle me-1"></i>Catatan ini akan tersimpan bersama MAPA
                                    </small>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Perubahan akan disimpan tanpa tanda tangan. Validasi dengan tanda tangan 
                                        akan dilakukan di tahap Rekomendasi Final.
                                    </div>

                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('asesor.mapa.index') }}"
                                            class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-1"></i>Batal
                                        </a>
                                        <button type="button" class="btn btn-primary" onclick="saveMapaChanges()">
                                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('asesor.mapa.partials.document-viewer-modal')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function viewDocument(url, title) {
            document.getElementById('documentTitle').textContent = title;
            document.getElementById('documentFrame').src = url;
            new bootstrap.Modal(document.getElementById('documentViewerModal')).show();
        }

        function saveMapaChanges() {
            const selectedPLevel = document.querySelector('input[name="p_level"]:checked');
            
            if (!selectedPLevel) {
                Swal.fire({
                    icon: 'warning',
                    title: 'P-Level Belum Dipilih',
                    text: 'Silakan pilih P-Level MAPA terlebih dahulu',
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Perubahan',
                html: `
                    <p class="mb-3">Anda akan menyimpan perubahan MAPA dengan pilihan:</p>
                    <div style="text-align: center; background: #f3f4f6; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <strong style="font-size: 1.5rem; color: #4F46E5;">P${selectedPLevel.value}</strong>
                    </div>
                    <p class="text-muted small">Perubahan ini tidak memerlukan tanda tangan. Validasi akan dilakukan di Rekomendasi Final.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    processMapaUpdate();
                }
            });
        }

        function processMapaUpdate() {
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(document.getElementById('editMapaForm'));
            const data = {
                p_level: document.querySelector('input[name="p_level"]:checked').value,
                catatan_asesor: formData.get('catatan_asesor'),
                _method: 'PUT'
            };

            fetch('{{ route("asesor.mapa.update-validated", $mapa->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
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
                                P-Level: <strong>P${data.data.p_level}</strong>
                            </p>
                        `,
                        timer: 2000,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.reload();
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