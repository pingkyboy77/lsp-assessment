{{-- resources/views/partials/modals/submit-confirmation.blade.php --}}
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Submit APL 02</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>Pastikan sebelum submit:</h6>
                    <ul class="mb-0">
                        <li>Semua elemen kompetensi telah dinilai</li>
                        <li>Bukti minimal telah diupload</li>
                        <li>Tanda tangan digital telah dibuat</li>
                    </ul>
                </div>
                <p>Setelah disubmit, APL 02 akan masuk ke proses review dan tidak dapat diedit lagi sampai dikembalikan atau disetujui.</p>
                <div id="submitValidation"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmSubmit">
                    <i class="bi bi-send me-1"></i>Ya, Submit APL 02
                </button>
            </div>
        </div>
    </div>
</div>