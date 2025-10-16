@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mapa-shared-styles.css') }}">
<style>
    .action-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .action-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .action-card .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .unlock-btn {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .unlock-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        color: white;
    }
    
    .view-form-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .view-form-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Main Card -->
    <div class="main-card">
        <!-- Card Header -->
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-clipboard-check me-2"></i>Review MAPA
                    </h5>
                    <p class="mb-0 text-muted">
                        Nomor: <strong>{{ $mapa->nomor_mapa }}</strong> | 
                        Status: <span class="badge bg-{{ $mapa->status_color }}">{{ $mapa->status_text }}</span>
                    </p>
                </div>
                <a href="{{ route('admin.mapa.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <div class="card-body m-3">
            <!-- Additional Actions for AK07 & Form Kerahasiaan -->
            @if($mapa->ak07)
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="action-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-wrapper bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-eye-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">FR.AK.07</h6>
                                <small class="text-muted">Status: {{ ucfirst($mapa->ak07->status) }}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.mapa.view-ak07', $mapa->id) }}"
                               class="btn btn-sm btn-info flex-fill" target="_blank">
                                <i class="bi bi-eye me-1"></i>View AK07
                            </a>
                            @if($mapa->ak07->status === 'completed')
                            <button onclick="unlockAk07({{ $mapa->id }})" 
                                    class="btn btn-sm unlock-btn flex-fill">
                                <i class="bi bi-unlock me-1"></i>Unlock AK07
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                @if($mapa->delegasi && $mapa->delegasi->formKerahasiaan)
                <div class="col-md-6">
                    <div class="action-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-wrapper bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Form Kerahasiaan</h6>
                                <small class="text-muted">Status: {{ ucfirst($mapa->delegasi->formKerahasiaan->status) }}</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.mapa.view-form-kerahasiaan', $mapa->id) }}" 
                           class="btn btn-sm view-form-btn w-100" target="_blank">
                            <i class="bi bi-file-text me-1"></i>View Form Kerahasiaan
                        </a>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Asesi & Asesor Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Informasi Asesi</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%;">Nama</td>
                                    <td>{{ $mapa->delegasi->asesi->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Email</td>
                                    <td>{{ $mapa->delegasi->asesi->email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Skema</td>
                                    <td>{{ $mapa->certificationScheme->nama }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>Informasi Asesor</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%;">Nama</td>
                                    <td>{{ $mapa->asesor->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tanggal Submit</td>
                                    <td>{{ $mapa->submitted_at ? $mapa->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">TTD Asesor</td>
                                    <td>
                                        @if($mapa->is_signed)
                                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> Sudah TTD</span>
                                        @else
                                            <span class="text-muted">Belum TTD</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAPA Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard-data me-2"></i>Detail MAPA</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">MAPA Code</label>
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-primary">{{ $mapa->mapa_code }}</h4>
                            <small class="text-muted">{{ $mapa->getDescription() }}</small>
                        </div>
                    </div>

                    <!-- Kelompok Details -->
                    <label class="form-label fw-bold mb-3">Detail per Kelompok Kerja</label>
                    @foreach($kelompokDetails as $detail)
                        <div class="kelompok-assignment mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-folder me-2" style="color: var(--primary-color);"></i>
                                <div class="flex-grow-1">
                                    <strong>{{ $detail['kelompok']->nama_kelompok }}</strong>
                                    <div class="mt-1">
                                        <span class="p-level-badge {{ $detail['p_number'] == 0 ? 'p-level-0' : 'p-level-active' }}">
                                            P{{ $detail['p_number'] }}
                                        </span>
                                        <small class="text-muted ms-2">{{ $detail['metode_text'] }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Catatan Asesor -->
                    @if($mapa->catatan_asesor)
                        <div class="mt-4">
                            <label class="form-label fw-bold">Catatan Asesor</label>
                            <div class="p-3 bg-light rounded">
                                {{ $mapa->catatan_asesor }}
                            </div>
                        </div>
                    @endif

                    <!-- Signature -->
                    @if($mapa->is_signed)
                        <div class="mt-4">
                            <label class="form-label fw-bold">Tanda Tangan Asesor</label>
                            <div class="p-3 border rounded text-center" style="background: #f8f9fa;">
                                <img src="{{ asset('storage/' . $mapa->signature_image) }}" 
                                     alt="Signature" 
                                     style="max-width: 300px; height: auto; border: 1px solid #dee2e6;">
                                <div class="mt-2 small text-muted">
                                    Ditandatangani: {{ $mapa->signed_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Review Section -->
            @if($mapa->canBeReviewed())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0 fw-bold text-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>Perlu Review
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="reviewForm">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Catatan Review (Opsional)</label>
                                <textarea class="form-control" name="review_notes" rows="4"
                                    placeholder="Tulis catatan untuk asesor..."></textarea>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-danger" onclick="rejectMapa()">
                                    <i class="bi bi-x-circle me-1"></i>Reject
                                </button>
                                <button type="button" class="btn btn-success" onclick="approveMapa()">
                                    <i class="bi bi-check-circle me-1"></i>Approve
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Review History -->
            @if($mapa->reviewed_by)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Review</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="fw-semibold" style="width: 30%;">Direview oleh</td>
                                <td>{{ $mapa->reviewedBy->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tanggal Review</td>
                                <td>{{ $mapa->reviewed_at ? $mapa->reviewed_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            @if($mapa->review_notes)
                                <tr>
                                    <td class="fw-semibold">Catatan Review</td>
                                    <td>{{ $mapa->review_notes }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function approveMapa() {
        const notes = document.querySelector('[name="review_notes"]').value;

        Swal.fire({
            title: 'Approve MAPA?',
            text: 'Asesor akan dapat melakukan validasi MAPA',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Approve',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitReview('approve', notes);
            }
        });
    }

    function rejectMapa() {
        const notes = document.querySelector('[name="review_notes"]').value;

        if (!notes || notes.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Catatan Diperlukan',
                text: 'Silakan tulis alasan penolakan di catatan review',
            });
            return;
        }

        Swal.fire({
            title: 'Reject MAPA?',
            text: 'MAPA akan dikembalikan ke asesor untuk diperbaiki',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reject',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitReview('reject', notes);
            }
        });
    }

    function submitReview(action, notes) {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const url = action === 'approve' 
            ? '{{ route("admin.mapa.approve", $mapa->id) }}'
            : '{{ route("admin.mapa.reject", $mapa->id) }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                review_notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = '{{ route("admin.mapa.index") }}';
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

    function unlockAk07(mapaId) {
        Swal.fire({
            title: 'Unlock AK07?',
            html: `
                <div class="text-start">
                    <p class="mb-2">Tindakan ini akan:</p>
                    <ul class="small text-muted">
                        <li>Menghapus tanda tangan Asesi di AK07</li>
                        <li>Menghapus Form Kerahasiaan</li>
                        <li>Menghapus Final Recommendation</li>
                        <li>Mereset status AK07 ke Draft</li>
                        <li>Asesor dapat mengisi ulang AK07</li>
                    </ul>
                    <p class="mt-2 mb-0 text-danger small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Unlock',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'text-start'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/admin/mapa/${mapaId}/unlock-ak07`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
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
        });
    }
</script>
@endpush