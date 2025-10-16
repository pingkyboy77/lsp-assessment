{{-- resources/views/admin/monitoring/partials/apl02-review-modal.blade.php --}}
<div class="modal fade" id="reopenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reopenModalTitle">Reopen APL 01</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="reopenModalMessage">Apakah Anda yakin ingin membuka kembali APL 01 ini?</p>
                <p class="text-warning" id="reopenWarningText">
                    <i class="bi bi-exclamation-triangle"></i>
                    Setelah dibuka kembali, status akan berubah menjadi "Open" dan asesi dapat mengedit form ini kembali.
                </p>
                <div class="mb-3">
                    <label class="form-label">Catatan (opsional):</label>
                    <textarea class="form-control" id="reopenNotes" rows="3"
                        placeholder="Berikan catatan mengapa form dibuka kembali..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="confirmReopen()">
                    <i class="bi bi-unlock"></i> Reopen
                </button>
            </div>
        </div>
    </div>
</div>