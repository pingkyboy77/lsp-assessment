{{-- resources/views/admin/tuk-requests/show.blade.php --}}
@php
    $isRecommended = $tukRequest->hasRecommendation();
@endphp

<div class="row">
    <!-- Left Column - TUK Request Details -->
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Informasi Permohonan TUK
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Kode TUK:</strong></div>
                    <div class="col-sm-8">
                        <span class="badge bg-primary fs-6">{{ $tukRequest->kode_tuk }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Tanggal Pengajuan:</strong></div>
                    <div class="col-sm-8">{{ $tukRequest->created_at->format('d F Y, H:i') }} WIB</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Tanggal Assessment:</strong></div>
                    <div class="col-sm-8">
                        <span class="fw-bold">{{ $tukRequest->tanggal_assessment->format('d F Y') }}, </span>
                        @if ($tukRequest->jam_mulai)
                            <span class="fw-bold">{{ $tukRequest->jam_mulai->format('H:i') }} WIB</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Lokasi Assessment:</strong></div>
                    <div class="col-sm-8">
                        <div class="border rounded p-2 bg-light">
                            {{ $tukRequest->lokasi_assessment }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participant Information -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>Informasi Peserta
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Nama Lengkap:</strong></div>
                    <div class="col-sm-8">{{ $tukRequest->apl01->nama_lengkap ?? 'N/A' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Nomor APL-01:</strong></div>
                    <div class="col-sm-8">
                        <span class="badge bg-secondary">{{ $tukRequest->apl01->nomor_apl_01 ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Email:</strong></div>
                    <div class="col-sm-8">
                        @if ($tukRequest->apl01->user->email ?? null)
                            <a href="mailto:{{ $tukRequest->apl01->user->email }}" class="text-primary">
                                {{ $tukRequest->apl01->user->email }}
                            </a>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>No. Telepon:</strong></div>
                    <div class="col-sm-8">{{ $tukRequest->apl01->no_hp ?? 'Tidak tersedia' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Skema Sertifikasi:</strong></div>
                    <div class="col-sm-8">
                        <div class="fw-bold text-primary">
                            {{ $tukRequest->apl01->certificationScheme->nama ?? 'N/A' }}
                        </div>
                        @if ($tukRequest->apl01->certificationScheme->code_1 ?? null)
                            <small class="text-muted">Kode: {{ $tukRequest->apl01->certificationScheme->code_1 }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>Dokumen Pendukung
                </h6>
            </div>
            <div class="card-body">
                @if ($tukRequest->tanda_tangan_peserta_path)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Tanda Tangan:</strong></div>
                        <div class="col-sm-8">
                            @php
                                $signatureUrl = $tukRequest->getSignatureUrl();
                            @endphp
                            @if ($signatureUrl)
                                <div class="border rounded p-3 bg-light">
                                    <img src="{{ asset('storage/' . $tukRequest->tanda_tangan_peserta_path) }}"
                                        alt="Tanda tangan {{ $tukRequest->apl01->nama_lengkap }}"
                                        class="signature-image" style="max-width: 200px; max-height: 100px;">
                                </div>
                            @else
                                <div class="border rounded p-2 bg-light text-muted">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Tanda tangan tidak dapat ditampilkan
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($tukRequest->dokumen_pendukung_path)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Dokumen Pendukung:</strong></div>
                        <div class="col-sm-8">
                            <a href="{{ Storage::url($tukRequest->dokumen_pendukung_path) }}" target="_blank"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Download Dokumen
                            </a>
                        </div>
                    </div>
                @endif

                @if ($tukRequest->apl01->pas_foto_path ?? null)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Pas Foto:</strong></div>
                        <div class="col-sm-8">
                            <a href="{{ Storage::url($tukRequest->apl01->pas_foto_path) }}" target="_blank"
                                class="btn btn-outline-info btn-sm">
                                <i class="bi bi-image me-1"></i>Lihat Pas Foto
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recommendation Section -->
@if (!$isRecommended)
    <!-- Form Buat Rekomendasi (Belum ada rekomendasi) -->
    <div id="recommendationForm" class="mt-4">
        <hr class="my-4">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="bi bi-check-circle me-2"></i>Buat Rekomendasi TUK
                </h6>
            </div>
            <div class="card-body">
                <form id="recommendationFormSubmit" data-tuk-id="{{ $tukRequest->id }}" data-is-edit="false">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_assessment" class="form-label">
                                <strong>Tanggal Assessment</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal_assessment"
                                name="tanggal_assessment"
                                value="{{ $tukRequest->tanggal_assessment?->format('Y-m-d') }}" required>
                            <div class="form-text">Admin dapat mengubah tanggal yang diusulkan peserta</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jam_mulai" class="form-label">
                                <strong>Jam Mulai Assessment</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control" id="jam_mulai" name="jam_mulai"
                                value="{{ $tukRequest->jam_mulai?->format('H:i') }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <strong>Rekomendasi</strong> <span class="text-danger">*</span>
                        </label>

                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="catatan_rekomendasi"
                                        id="rekomendasiSetuju" value="direkomendasikan" required>
                                    <label class="form-check-label fw-semibold text-success" for="rekomendasiSetuju">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Direkomendasikan sebagai calon TUK Sewaktu Jarak Jauh
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="catatan_rekomendasi"
                                        id="rekomendasiTidakSetuju" value="tidak_direkomendasikan" required>
                                    <label class="form-check-label fw-semibold text-danger"
                                        for="rekomendasiTidakSetuju">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Tidak direkomendasikan sebagai calon TUK Sewaktu Jarak Jauh
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Pilih status rekomendasi untuk permohonan TUK ini
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Buat Rekomendasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@else
    <!-- Display Rekomendasi (Sudah ada rekomendasi) - dengan tombol Edit -->
    <div id="recommendationDisplay" class="mt-4">
        <hr class="my-4">
        <div class="card border-success">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span id="cardTitle">Rekomendasi TUK</span>
                </h6>
                <button type="button" class="btn btn-sm btn-warning" id="btnEditRecommendation" onclick="enableEditMode()">
                    <i class="bi bi-pencil me-1"></i>Edit Rekomendasi
                </button>
            </div>
            <div class="card-body">
                <!-- Alert Info (hanya muncul di view mode) -->
                <div class="alert alert-success mb-4" id="viewModeAlert">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Rekomendasi sudah dibuat.</strong> Klik tombol "Edit Rekomendasi" untuk mengubah.
                </div>

                <form id="recommendationFormSubmit" data-tuk-id="{{ $tukRequest->id }}" data-is-edit="true">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Tanggal Assessment:</strong></label>
                            <input type="date" 
                                   class="form-control" 
                                   id="tanggal_assessment"
                                   name="tanggal_assessment"
                                   value="{{ $tukRequest->tanggal_assessment?->format('Y-m-d') }}"
                                   readonly
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Jam Mulai Assessment:</strong></label>
                            <input type="time" 
                                   class="form-control" 
                                   id="jam_mulai"
                                   name="jam_mulai"
                                   value="{{ $tukRequest->jam_mulai?->format('H:i') }}"
                                   readonly
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Status Rekomendasi:</strong></label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="catatan_rekomendasi"
                                           id="rekomendasiSetuju" 
                                           value="direkomendasikan"
                                           {{ $tukRequest->catatan_rekomendasi === 'direkomendasikan' ? 'checked' : '' }}
                                           disabled>
                                    <label class="form-check-label fw-semibold text-success" for="rekomendasiSetuju">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Direkomendasikan sebagai calon TUK Sewaktu Jarak Jauh
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="catatan_rekomendasi"
                                           id="rekomendasiTidakSetuju" 
                                           value="tidak_direkomendasikan"
                                           {{ $tukRequest->catatan_rekomendasi === 'tidak_direkomendasikan' ? 'checked' : '' }}
                                           disabled>
                                    <label class="form-check-label fw-semibold text-danger"
                                        for="rekomendasiTidakSetuju">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Tidak direkomendasikan sebagai calon TUK Sewaktu Jarak Jauh
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Direkomendasi pada:</strong></label>
                            <div class="p-2 bg-light border rounded">
                                {{ $tukRequest->recommended_at?->format('d F Y, H:i') ?? 'N/A' }} WIB
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Direkomendasi oleh:</strong></label>
                            <div class="p-2 bg-light border rounded">
                                {{ $tukRequest->recommendedBy->name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons (hidden by default, muncul saat edit mode) -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end" id="editModeActions" style="display: none !important;">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="cancelEditMode()">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Update Rekomendasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
function enableEditMode() {
    // Change card title
    $('#cardTitle').html('<i class="bi bi-pencil me-2"></i>Edit Rekomendasi TUK');
    
    // Hide view mode alert
    $('#viewModeAlert').hide();
    
    // Hide edit button
    $('#btnEditRecommendation').hide();
    
    // Enable form inputs
    $('#tanggal_assessment, #jam_mulai').prop('readonly', false);
    $('input[name="catatan_rekomendasi"]').prop('disabled', false);
    
    // Show action buttons
    $('#editModeActions').show();
    
    // Add edit mode styling
    $('#tanggal_assessment, #jam_mulai').removeClass('form-control-plaintext').addClass('form-control');
}

function cancelEditMode() {
    // Reload modal to reset form
    location.reload();
}
</script>

<style>
.form-control:read-only,
.form-check-input:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.card-header h6 {
    font-weight: 600;
}

.form-check-input:checked {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.form-check-label {
    cursor: pointer;
}

.alert-success {
    background-color: #d1edff;
    border-color: #bee5eb;
    color: #0c5460;
}
</style>