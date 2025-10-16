{{-- resources/views/admin/monitoring/partials/apl02-review-modal.blade.php --}}
<div class="modal fade" id="apl02ReviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clipboard2-check me-2"></i>Review APL 02 - Self Assessment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="apl02ModalBody" style="max-height: 80vh; overflow-y: auto;">
                {{-- Content will be loaded here --}}
            </div>
            <div class="modal-footer">
                <div class="flex-fill">
                    <textarea class="form-control" id="apl02ReviewNotes" placeholder="Catatan review (opsional)..." rows="3"></textarea>
                </div>
                <div class="d-flex flex-column gap-2 ms-3">
                    <button type="button" class="btn btn-success" onclick="processApl02Review('approve')">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" onclick="processApl02Review('reject')">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>