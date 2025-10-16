{{-- resources/views/lembaga-pelatihan/monitoring/partials/reopen-modal.blade.php --}}
<div class="modal fade" id="reopenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reopenModalTitle">Reopen APL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="reopenModalMessage"></p>
                <p class="text-warning" id="reopenWarningText"></p>
                <div class="mb-3">
                    <label class="form-label">Catatan (opsional):</label>
                    <textarea class="form-control" id="reopenNotes" rows="3" placeholder="Berikan catatan..."></textarea>
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