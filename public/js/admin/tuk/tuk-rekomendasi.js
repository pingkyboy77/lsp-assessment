// ============================================
// TUK SEWAKTU & REKOMENDASI FUNCTIONS
// ============================================

function openRecommendModal(tukRequestId) {
    const modal = new bootstrap.Modal(document.getElementById('recommendModal'));

    $('#recommendModalBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Memuat detail permohonan TUK...</p>
        </div>
    `);

    modal.show();

    fetch(`/admin/tuk-requests/${tukRequestId}`)
        .then(response => response.text())
        .then(html => {
            $('#recommendModalBody').html(html);
            initRecommendationForm();
        })
        .catch(error => {
            $('#recommendModalBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading data: ${error.message}
                </div>
            `);
        });
}

function initRecommendationForm() {
    const form = document.getElementById('recommendationFormSubmit');
    if (!form) return;

    $(form).off('submit').on('submit', function(e) {
        e.preventDefault();

        const tukRequestId = $(this).data('tuk-id');
        const isEdit = $(this).data('is-edit') === 'true';
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const catatanRekomendasi = $('input[name="catatan_rekomendasi"]:checked').val();

        if (!catatanRekomendasi) {
            showToast('error', 'Silakan pilih status rekomendasi');
            return;
        }

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            tanggal_assessment: $('#tanggal_assessment').val(),
            jam_mulai: $('#jam_mulai').val(),
            catatan_rekomendasi: catatanRekomendasi
        };

        if (isEdit) formData._method = 'PUT';

        fetch(`/admin/tuk-requests/${tukRequestId}/recommend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const message = isEdit ? 'Rekomendasi berhasil diupdate!' : 'Rekomendasi TUK berhasil dibuat!';
                showToast('success', data.message || message);

                const recommendModal = bootstrap.Modal.getInstance(document.getElementById('recommendModal'));
                if (recommendModal) recommendModal.hide();

                refreshSewaktuTable();
            } else {
                if (data.errors) {
                    let errorMessage = 'Validasi gagal:\n';
                    Object.keys(data.errors).forEach(key => {
                        errorMessage += `${key}: ${data.errors[key].join(', ')}\n`;
                    });
                    showToast('error', errorMessage);
                } else {
                    showToast('error', data.error || 'Gagal memproses rekomendasi');
                }
            }
        })
        .catch(error => {
            console.error('Error submitting recommendation:', error);
            showToast('error', 'Error: ' + error.message);
        })
        .finally(() => {
            submitBtn.prop('disabled', false).html(originalText);
        });
    });
}

// ============================================
// TTD REKOMENDASI LSP FUNCTIONS
// ============================================

function initRekomendasiSignaturePad() {
    const canvas = document.getElementById('rekomendasiSignaturePad');
    if (!canvas) return;

    rekomendasiSignaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)',
        minWidth: 1,
        maxWidth: 2.5
    });

    // Resize canvas
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const parentWidth = canvas.parentElement.offsetWidth;

    canvas.width = parentWidth * ratio;
    canvas.height = 200 * ratio;
    canvas.style.width = parentWidth + 'px';
    canvas.style.height = '200px';

    const context = canvas.getContext('2d');
    context.scale(ratio, ratio);
}

function clearRekomendasiSignature() {
    if (rekomendasiSignaturePad) {
        rekomendasiSignaturePad.clear();
    }
}

function openTTDRekomendasiModal(apl01Id) {
    const modal = new bootstrap.Modal(document.getElementById('reviewAplModal'));

    $('#reviewAplModalTitle').html('<i class="bi bi-pencil-square me-2"></i>Tanda Tangan Rekomendasi LSP');

    $('#reviewAplModalBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Memuat form rekomendasi...</p>
        </div>
    `);

    // Hide footer buttons temporarily
    $('#btnApprove, #btnReject').hide();
    $('.modal-footer .btn-group').hide();
    $('#approvalNotesSection, #rejectionNotesSection').hide();
    $('#completionStatusInfo').hide();

    modal.show();

    // Load ONLY the TTD form
    fetch(`/admin/tuk-requests/ttd-rekomendasi-form/${apl01Id}`)
        .then(response => response.text())
        .then(html => {
            $('#reviewAplModalBody').html(html);

            // Show custom footer for rekomendasi
            $('#completionStatusInfo').hide();
            $('.modal-footer').html(`
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-success" onclick="submitRekomendasiFromModal()">
                            <i class="bi bi-check-circle me-1"></i>Simpan Rekomendasi
                        </button>
                    </div>
                </div>
            `);

            // Initialize signature pad
            setTimeout(initRekomendasiSignaturePad, 500);
        })
        .catch(error => {
            $('#reviewAplModalBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading form: ${error.message}
                </div>
            `);
        });
}

function viewRekomendasiLSP(apl01Id) {
    const modal = new bootstrap.Modal(document.getElementById('reviewAplModal'));

    $('#reviewAplModalTitle').html('<i class="bi bi-award me-2"></i>Rekomendasi LSP');

    $('#reviewAplModalBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Memuat rekomendasi LSP...</p>
        </div>
    `);

    modal.show();

    fetch(`/admin/tuk-requests/view-rekomendasi-lsp/${apl01Id}`)
        .then(response => response.text())
        .then(html => {
            $('#reviewAplModalBody').html(html);

            // Hide footer buttons
            $('#btnApprove, #btnReject').hide();
            $('.modal-footer .btn-group').hide();
        })
        .catch(error => {
            $('#reviewAplModalBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading rekomendasi: ${error.message}
                </div>
            `);
        });
}

function closeRekomendasiModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('reviewAplModal'));
    if (modal) modal.hide();
}

function editRekomendasiLSP(apl01Id) {
    // Close current modal
    closeRekomendasiModal();

    // Reopen with edit mode
    setTimeout(() => {
        openTTDRekomendasiModal(apl01Id);
    }, 300);
}

function submitRekomendasiFromModal() {
    const apl01Id = $('#apl01IdForRekomendasi').val();
    submitRekomendasi(apl01Id);
}

function submitRekomendasi(apl01Id) {
    if (!rekomendasiSignaturePad || rekomendasiSignaturePad.isEmpty()) {
        showToast('warning', 'Mohon tanda tangan terlebih dahulu');
        return;
    }

    const rekomendasiText = $('input[name="rekomendasi_text"]:checked').val();

    if (!rekomendasiText) {
        showToast('error', 'Pilih salah satu rekomendasi terlebih dahulu');
        return;
    }

    const signatureData = rekomendasiSignaturePad.toDataURL('image/png');

    const submitBtn = $('.modal-footer .btn-success');
    const originalText = submitBtn.html();

    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    fetch(`/admin/tuk-requests/store-rekomendasi/${apl01Id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({
            rekomendasi_text: rekomendasiText,
            signature_data: signatureData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);

            const modal = bootstrap.Modal.getInstance(document.getElementById('reviewAplModal'));
            if (modal) modal.hide();

            // Refresh tables
            refreshSewaktuTable();
            refreshMandiriTable();
        } else {
            showToast('error', data.error || 'Gagal menyimpan rekomendasi');
        }
    })
    .catch(error => {
        showToast('error', 'Error: ' + error.message);
    })
    .finally(() => {
        submitBtn.prop('disabled', false).html(originalText);
    });
}