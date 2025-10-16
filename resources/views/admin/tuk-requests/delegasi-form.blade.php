<!-- Enhanced Delegation View/Edit Component -->
<div class="delegation-container">
    <!-- Header dengan Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">
                <i class="bi bi-person-check-fill me-2 text-primary"></i>
                <span id="viewTitle">Detail Delegasi Personil</span>
            </h5>
            <small class="text-muted">
                TUK: <strong>{{ $delegasi->getTukType() }}</strong>
            </small>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnToggleEdit" onclick="toggleEditMode()">
                <i class="bi bi-pencil me-1"></i>Edit
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning" 
                onclick="openRescheduleModal({{ $delegasi->id }}, {{ $delegasi->tuk_request_id ?? 'null' }}, {{ $delegasi->apl01_id ?? 'null' }})">
                <i class="bi bi-calendar-x me-1"></i>Reschedule
            </button>
        </div>
    </div>

    <!-- Form Container (supports both view and edit mode) -->
    <form id="delegasiEditForm">
        @csrf
        <input type="hidden" id="edit_delegasi_id" value="{{ $delegasi->id }}">
        <input type="hidden" id="edit_asesi_id" value="{{ $delegasi->asesi_id }}">
        <input type="hidden" id="edit_certification_scheme_id" value="{{ $delegasi->certification_scheme_id }}">
        <input type="hidden" id="edit_apl01_id" value="{{ $delegasi->apl01_id }}">
        <input type="hidden" id="edit_tuk_request_id" value="{{ $delegasi->tuk_request_id }}">

        <!-- Info Asesi -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Informasi Asesi</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Asesi</label>
                        <p class="view-mode text-muted">{{ $delegasi->asesi->name }}</p>
                        <input type="text" class="form-control edit-mode" value="{{ $delegasi->asesi->name }}" readonly style="display: none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Skema Sertifikasi</label>
                        <p class="view-mode text-muted">{{ $delegasi->certificationScheme->nama }}</p>
                        <input type="text" class="form-control edit-mode" value="{{ $delegasi->certificationScheme->nama }}" readonly style="display: none;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Jenis Ujian -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-laptop me-2"></i>Jenis Ujian</h6>
            </div>
            <div class="card-body">
                <!-- View Mode -->
                <div class="view-mode">
                    <span class="badge {{ $delegasi->jenis_ujian === 'online' ? 'bg-info' : 'bg-secondary' }} fs-6">
                        <i class="bi {{ $delegasi->jenis_ujian === 'online' ? 'bi-laptop' : 'bi-building' }} me-1"></i>
                        {{ $delegasi->jenis_ujian === 'online' ? 'Online' : 'Paperless Offline' }}
                    </span>
                </div>
                <!-- Edit Mode -->
                <div class="edit-mode" style="display: none;">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="edit_jenis_ujian" id="edit_online" value="online" {{ $delegasi->jenis_ujian === 'online' ? 'checked' : '' }}>
                        <label class="form-check-label" for="edit_online">
                            <i class="bi bi-laptop text-info"></i> Online
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="edit_jenis_ujian" id="edit_offline" value="offline" {{ $delegasi->jenis_ujian === 'offline' ? 'checked' : '' }}>
                        <label class="form-check-label" for="edit_offline">
                            <i class="bi bi-building text-secondary"></i> Paperless Offline
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verifikator TUK -->
        <div class="card mb-3">
            <div class="card-header bg-info bg-opacity-10">
                <h6 class="mb-0 text-info">
                    <i class="bi bi-shield-check me-2"></i>Verifikator TUK
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Verifikator TUK</label>
                        <p class="view-mode text-muted">{{ $delegasi->verifikatorTuk->name }}</p>
                        <select class="form-select edit-mode" id="edit_verifikator_tuk_id" style="display: none;">
                            <option value="{{ $delegasi->verifikator_tuk_id }}" selected>{{ $delegasi->verifikatorTuk->name }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">NIK</label>
                        <p class="view-mode text-muted">{{ $delegasi->verifikator_nik }}</p>
                        <input type="text" class="form-control edit-mode" id="edit_verifikator_nik" value="{{ $delegasi->verifikator_nik }}" readonly style="display: none;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal SPT</label>
                        <p class="view-mode text-muted">{{ \Carbon\Carbon::parse($delegasi->verifikator_spt_date)->format('d/m/Y') }}</p>
                        <input type="date" class="form-control edit-mode" id="edit_verifikator_spt_date" value="{{ $delegasi->verifikator_spt_date->format('Y-m-d') }}" style="display: none;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Observer -->
        <div class="card mb-3">
            <div class="card-header bg-warning bg-opacity-10">
                <h6 class="mb-0 text-warning">
                    <i class="bi bi-eye me-2"></i>Observer
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Observer</label>
                        <p class="view-mode text-muted">{{ $delegasi->observer->name }}</p>
                        <select class="form-select edit-mode" id="edit_observer_id" style="display: none;">
                            <option value="{{ $delegasi->observer_id }}" selected>{{ $delegasi->observer->name }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">NIK</label>
                        <p class="view-mode text-muted">{{ $delegasi->observer_nik }}</p>
                        <input type="text" class="form-control edit-mode" id="edit_observer_nik" value="{{ $delegasi->observer_nik }}" readonly style="display: none;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal SPT</label>
                        <p class="view-mode text-muted">{{ \Carbon\Carbon::parse($delegasi->observer_spt_date)->format('d/m/Y') }}</p>
                        <input type="date" class="form-control edit-mode" id="edit_observer_spt_date" value="{{ $delegasi->observer_spt_date->format('Y-m-d') }}" style="display: none;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Asesor -->
        <div class="card mb-3">
            <div class="card-header bg-success bg-opacity-10">
                <h6 class="mb-0 text-success">
                    <i class="bi bi-person-check-fill me-2"></i>Asesor
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asesor</label>
                        <p class="view-mode text-muted">{{ $delegasi->asesor->name }}</p>
                        <select class="form-select edit-mode" id="edit_asesor_id" style="display: none;">
                            <option value="{{ $delegasi->asesor_id }}" selected>{{ $delegasi->asesor->name }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">MET</label>
                        <p class="view-mode text-muted">{{ $delegasi->asesor_met }}</p>
                        <input type="text" class="form-control edit-mode" id="edit_asesor_met" value="{{ $delegasi->asesor_met }}" readonly style="display: none;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal SPT</label>
                        <p class="view-mode text-muted">{{ \Carbon\Carbon::parse($delegasi->asesor_spt_date)->format('d/m/Y') }}</p>
                        <input type="date" class="form-control edit-mode" id="edit_asesor_spt_date" value="{{ $delegasi->asesor_spt_date->format('Y-m-d') }}" style="display: none;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Asesmen -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Jadwal Asesmen</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Pelaksanaan</label>
                        <p class="view-mode text-muted">{{ \Carbon\Carbon::parse($delegasi->tanggal_pelaksanaan_asesmen)->format('d F Y') }}</p>
                        <input type="date" class="form-control edit-mode" id="edit_tanggal_pelaksanaan" value="{{ $delegasi->tanggal_pelaksanaan_asesmen->format('Y-m-d') }}" style="display: none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Waktu Mulai</label>
                        <p class="view-mode text-muted">{{ $delegasi->waktu_mulai }}</p>
                        <input type="time" class="form-control edit-mode" id="edit_waktu_mulai" value="{{ $delegasi->waktu_mulai }}" style="display: none;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan</h6>
            </div>
            <div class="card-body">
                <p class="view-mode text-muted mb-0">{{ $delegasi->notes ?? 'Tidak ada catatan' }}</p>
                <textarea class="form-control edit-mode" id="edit_notes" rows="3" style="display: none;">{{ $delegasi->notes }}</textarea>
            </div>
        </div>

        <!-- Info Metadata -->
        <div class="card mb-3 bg-light">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="bi bi-person-circle me-1"></i>
                            Didelegasikan oleh: <strong>{{ $delegasi->delegatedBy->name ?? 'System' }}</strong>
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <i class="bi bi-clock-history me-1"></i>
                            {{ $delegasi->delegated_at ? $delegasi->delegated_at->format('d F Y H:i') : '-' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle me-1"></i>Tutup
            </button>
            
            <!-- Edit Mode Buttons (hidden by default) -->
            <div class="edit-mode" style="display: none;">
                <button type="button" class="btn btn-outline-secondary me-2" onclick="cancelEditMode()">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-primary" id="btnSaveEdit">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.delegation-container .card {
    transition: all 0.3s ease;
}

.delegation-container .edit-mode {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.delegation-container .form-label {
    margin-bottom: 0.25rem;
}

.delegation-container .view-mode {
    min-height: 24px;
}
</style>