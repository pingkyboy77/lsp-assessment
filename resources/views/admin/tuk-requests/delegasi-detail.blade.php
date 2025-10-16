<!-- Modal Delegasi Personil (Form untuk Create/Edit) -->
<div class="modal fade" id="delegasiPersonilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-check-fill me-2"></i>Delegasi Personil Asesmen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="delegasiPersonilForm">
                    @csrf
                    
                    <!-- Hidden Fields -->
                    <input type="hidden" id="asesi_id" name="asesi_id">
                    <input type="hidden" id="certification_scheme_id" name="certification_scheme_id">
                    <input type="hidden" id="apl01_id" name="apl01_id">
                    <input type="hidden" id="tuk_request_id" name="tuk_request_id">

                    <!-- Info Asesi -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-person me-2"></i>Informasi Asesi</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nama Asesi</label>
                                        <input type="text" class="form-control" id="asesi_nama" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Skema Sertifikasi</label>
                                        <input type="text" class="form-control" id="skema_nama" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Ujian -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-laptop me-2"></i>Jenis Ujian</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_ujian" id="online" value="online" checked>
                                <label class="form-check-label" for="online">
                                    <i class="bi bi-laptop text-info"></i> Online
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_ujian" id="offline" value="offline">
                                <label class="form-check-label" for="offline">
                                    <i class="bi bi-building text-secondary"></i> Paperless Offline
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Verifikator TUK -->
                    <div class="card mb-4">
                        <div class="card-header bg-info bg-opacity-10">
                            <h6 class="mb-0 text-info">
                                <i class="bi bi-shield-check me-2"></i>Verifikator TUK
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Verifikator TUK <span class="text-danger">*</span></label>
                                        <select class="form-select" id="verifikator_tuk_id" name="verifikator_tuk_id" required>
                                            <option value="">-- Pilih Verifikator --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="verifikator_nik" name="verifikator_nik" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Tanggal SPT 
                                            <span class="badge bg-success">
                                                <i class="bi bi-magic me-1"></i>Auto Sync
                                            </span>
                                        </label>
                                        <input type="date" class="form-control" id="verifikator_spt_date" name="verifikator_spt_date" required>
                                        <small class="text-muted">Auto dari tanggal pelaksanaan (editable)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observer -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h6 class="mb-0 text-warning">
                                <i class="bi bi-eye me-2"></i>Observer
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Observer <span class="text-danger">*</span></label>
                                        <select class="form-select" id="observer_id" name="observer_id" required>
                                            <option value="">-- Pilih Observer --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="observer_nik" name="observer_nik" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Tanggal SPT 
                                            <span class="badge bg-success">
                                                <i class="bi bi-magic me-1"></i>Auto Sync
                                            </span>
                                        </label>
                                        <input type="date" class="form-control" id="observer_spt_date" name="observer_spt_date" required>
                                        <small class="text-muted">Auto dari tanggal pelaksanaan (editable)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asesor -->
                    <div class="card mb-4">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0 text-success">
                                <i class="bi bi-person-check-fill me-2"></i>Asesor
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Asesor <span class="text-danger">*</span></label>
                                        <select class="form-select" id="asesor_id" name="asesor_id" required>
                                            <option value="">-- Pilih Asesor --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">MET <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="asesor_met" name="asesor_met" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Tanggal SPT 
                                            <span class="badge bg-success">
                                                <i class="bi bi-magic me-1"></i>Auto Sync
                                            </span>
                                        </label>
                                        <input type="date" class="form-control" id="asesor_spt_date" name="asesor_spt_date" required>
                                        <small class="text-muted">Auto dari tanggal pelaksanaan (editable)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Asesmen -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Jadwal Asesmen</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3" id="autoFillInfo">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Auto-Fill (TUK Sewaktu):</strong> 
                                <ul class="mb-0 mt-2" style="font-size: 0.9rem;">
                                    <li>Waktu mulai otomatis dari jam mulai TUK (bisa diedit)</li>
                                    <li>Tanggal SPT otomatis dari tanggal pelaksanaan (bisa diedit)</li>
                                    <li>Mengubah tanggal pelaksanaan akan otomatis update semua tanggal SPT</li>
                                </ul>
                            </div>
                            <div class="alert alert-warning mb-3" id="manualFillInfo" style="display: none;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>TUK Mandiri - Input Manual:</strong> 
                                <ul class="mb-0 mt-2" style="font-size: 0.9rem;">
                                    <li>Silakan isi <strong>Tanggal Pelaksanaan</strong> dan <strong>Waktu Mulai</strong></li>
                                    <li>Tanggal SPT akan otomatis menyesuaikan dengan tanggal pelaksanaan</li>
                                </ul>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_pelaksanaan" name="tanggal_pelaksanaan_asesmen" required>
                                        <small class="text-muted">Mengubah tanggal ini akan otomatis update semua tanggal SPT</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Waktu Mulai 
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Auto dari TUK
                                            </span>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" required>
                                        <small class="text-muted">Otomatis dari jam mulai TUK (bisa diedit)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan (Opsional)</h6>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitDelegasiBtn">
                            <i class="bi bi-save me-1"></i>Simpan Delegasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>