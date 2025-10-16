// ============================================
// UTILITY & HELPER FUNCTIONS
// ============================================

function showToast(type, message) {
    const colors = {
        success: 'bg-success',
        error: 'bg-danger',
        info: 'bg-info',
        warning: 'bg-warning'
    };

    const icons = {
        success: 'bi-check-circle',
        error: 'bi-exclamation-triangle',
        info: 'bi-info-circle',
        warning: 'bi-exclamation-triangle'
    };

    const toastHtml = `
        <div class="toast align-items-center text-white ${colors[type]}" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${icons[type]} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    container.insertAdjacentHTML('beforeend', toastHtml);
    const toast = new bootstrap.Toast(container.lastElementChild);
    toast.show();
}

// ============================================
// TUK MANDIRI VIEW FUNCTIONS
// ============================================

function viewApl01(apl01Id) {
    const modal = new bootstrap.Modal(document.getElementById('viewApl01Modal'));

    $('#viewApl01ModalBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Memuat detail APL01...</p>
        </div>
    `);

    modal.show();

    fetch(`/admin/tuk-requests/apl01/${apl01Id}/view`)
        .then(response => response.text())
        .then(html => $('#viewApl01ModalBody').html(html))
        .catch(error => {
            $('#viewApl01ModalBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading APL01 data: ${error.message}
                </div>
            `);
        });
}

function viewTukMandiri(apl01Id) {
    const modal = new bootstrap.Modal(document.getElementById('viewTukMandiriModal'));

    $('#tukMandiriModalBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Memuat dokumen TUK Mandiri...</p>
        </div>
    `);

    modal.show();

    setTimeout(() => {
        $('#tukMandiriModalBody').html(`
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                        Dokumen TUK Mandiri
                    </h6>
                    <a href="${window.location.origin}/TUK/tukMandiri.pdf" 
                       download="TUK_Mandiri.pdf" 
                       class="btn btn-sm btn-danger">
                        <i class="bi bi-download me-1"></i>Download PDF
                    </a>
                </div>
                
                <div style="height: 600px; border: 1px solid #dee2e6; border-radius: 0.25rem; overflow: hidden;">
                    <embed 
                        src="${window.location.origin}/TUK/tukMandiri.pdf" 
                        type="application/pdf" 
                        width="100%" 
                        height="100%"
                        id="pdfEmbedAdmin"
                    />
                </div>
                
                <div id="pdfFallbackAdmin" style="display: none;" class="text-center p-5">
                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Browser tidak mendukung tampilan PDF</h5>
                    <p class="text-muted">Silakan download dokumen untuk melihatnya</p>
                    <a href="${window.location.origin}/TUK/tukMandiri.pdf" 
                       download="TUK_Mandiri.pdf" 
                       class="btn btn-danger">
                        <i class="bi bi-download me-1"></i>Download PDF
                    </a>
                </div>
            </div>
        `);

        setTimeout(() => {
            const embed = document.getElementById('pdfEmbedAdmin');
            if (!embed || embed.offsetHeight === 0) {
                $('#pdfEmbedAdmin').hide();
                $('#pdfFallbackAdmin').show();
            }
        }, 1000);
    }, 500);
}

// ============================================
// RESCHEDULE FUNCTIONS
// ============================================

function openRescheduleModal(delegasiId, tukRequestId, apl01Id) {
    $('#reschedule_delegasi_id').val(delegasiId);
    $('#reschedule_tuk_request_id').val(tukRequestId || '');
    $('#reschedule_apl01_id').val(apl01Id || '');
    $('#reschedule_reason').val('');

    // Close delegasi modal first
    const delegasiModal = bootstrap.Modal.getInstance(document.getElementById('delegasiPersonilModal'));
    if (delegasiModal) delegasiModal.hide();

    // Open reschedule modal
    const rescheduleModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    rescheduleModal.show();
}

// Setup reschedule form handler (called in main initialization)
function setupRescheduleHandler() {
    $('#rescheduleForm').on('submit', function(e) {
        e.preventDefault();

        const reason = $('#reschedule_reason').val().trim();
        const tukRequestId = $('#reschedule_tuk_request_id').val();
        const apl01Id = $('#reschedule_apl01_id').val();

        if (!reason || reason.length < 10) {
            showToast('error', 'Alasan reschedule minimal 10 karakter');
            return;
        }

        if (!tukRequestId && !apl01Id) {
            showToast('error', 'Data tidak valid');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin melakukan reschedule?\n\nData delegasi dan rekomendasi LSP akan dihapus dan harus diinput ulang.')) {
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

        // Determine URL based on TUK type
        let url;
        if (tukRequestId) {
            url = `/admin/tuk-requests/${tukRequestId}/reschedule`;
        } else if (apl01Id) {
            url = `/admin/tuk-requests/apl01/${apl01Id}/reschedule`;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);

                const rescheduleModal = bootstrap.Modal.getInstance(document.getElementById('rescheduleModal'));
                if (rescheduleModal) rescheduleModal.hide();

                // Refresh tables
                refreshSewaktuTable();
                refreshMandiriTable();
            } else {
                showToast('error', data.error || 'Gagal melakukan reschedule');
            }
        })
        .catch(error => {
            showToast('error', 'Error: ' + error.message);
        })
        .finally(() => {
            submitBtn.prop('disabled', false).html(originalText);
        });
    });
}