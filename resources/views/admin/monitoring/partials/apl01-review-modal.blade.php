{{-- resources/views/admin/monitoring/partials/apl01-review-modal.blade.php --}}
<div class="modal fade" id="apl01ReviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clipboard-check me-2"></i>Review APL 01
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="apl01ModalBody">
                {{-- Content will be loaded here --}}
            </div>
            <div class="modal-footer">
                <div class="flex-fill">
                    <textarea class="form-control" id="apl01ReviewNotes" placeholder="Catatan review (opsional)..." rows="3"></textarea>
                </div>
                <div class="d-flex flex-column gap-2 ms-3">
                    <button type="button" class="btn btn-success" onclick="processApl01Review('approve')">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" onclick="processApl01Review('reject')">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>