// ============================================
// REVIEW APL FUNCTIONS (APL01 & APL02) - FIXED
// ============================================

function openReviewApl01Modal(apl01Id) {
    currentReviewType = 'apl01';
    currentReviewId = apl01Id;
    $('#reviewAplModalTitle').html('<i class="bi bi-file-earmark-check me-2"></i>Review APL 01 - Formulir Pendaftaran');
    openReviewModal(`/admin/apl-review/apl01/${apl01Id}/review-detail`);
}

function openReviewApl02Modal(apl02Id) {
    currentReviewType = 'apl02';
    currentReviewId = apl02Id;
    $('#reviewAplModalTitle').html('<i class="bi bi-file-earmark-check me-2"></i>Review APL 02 - Asesmen Mandiri');
    openReviewModal(`/admin/apl-review/apl02/${apl02Id}/review-detail`);
}

function openReviewModal(url) {
    const modal = new bootstrap.Modal(document.getElementById('reviewAplModal'));
    resetReviewModalUI();

    $('#reviewAplModalBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Memuat detail dokumen...</p>
        </div>
    `);

    modal.show();

    fetch(url)
        .then(response => response.text())
        .then(html => {
            $('#reviewAplModalBody').html(html);
            updateCompletionBadges();
        })
        .catch(error => {
            $('#reviewAplModalBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading data: ${error.message}
                </div>
            `);
        });
}

function resetReviewModalUI() {
    $('#approvalNotesSection').hide();
    $('#rejectionNotesSection').hide();
    $('#approvalNotes').val('');
    $('#rejectionNotes').val('');
    $('#btnApprove').prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Approve');
    $('#btnReject').prop('disabled', false).html('<i class="bi bi-x-circle me-1"></i>Reject');
    $('#viewOnlyAlert').remove();
    $('.modal-footer .btn-group').show();
}

function updateCompletionBadges() {
    const docType = currentReviewType === 'apl01' ? 'APL 01' : 'APL 02';
    $('#completionBadges').html(`
        <span class="badge bg-info">
            <i class="bi bi-file-earmark me-1"></i>${docType}
        </span>
        <span class="badge bg-secondary" id="statusBadge">Menunggu Review</span>
    `);
}

// ============================================
// 🔥 FIXED APPROVE HANDLER
// ============================================
function handleApprove() {
    console.log('handleApprove called', {currentReviewType, currentReviewId}); // Debug
    
    if (!currentReviewId || !currentReviewType) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Data review tidak valid'
        });
        return;
    }

    // Jika notes section masih hidden, tampilkan dulu
    if ($('#approvalNotesSection').is(':hidden')) {
        $('#rejectionNotesSection').hide();
        $('#approvalNotesSection').slideDown();
        $('#approvalNotes').focus();
        $('#btnApprove').html('<i class="bi bi-check-circle me-1"></i>Konfirmasi Persetujuan');
        return;
    }

    // Ambil notes
    const notes = $('#approvalNotes').val().trim();

    // Konfirmasi dengan SweetAlert2
    Swal.fire({
        title: 'Konfirmasi Persetujuan',
        html: `
            <div class="text-center">
                <p class="mb-2">Anda akan menyetujui dokumen ini ? </strong></p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Approve',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            processApproval(notes);
        }
    });
}

// ============================================
// 🔥 PROCESS APPROVAL TO BACKEND - COMBINED
// ============================================
function processApproval(notes) {
    const submitBtn = $('#btnApprove');
    const originalText = submitBtn.html();

    // Disable buttons
    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
    $('#btnReject').prop('disabled', true);

    // 🔥 GUNAKAN ENDPOINT COMBINED - Approve APL01 & APL02 sekaligus
    const url = `/admin/tuk-requests/approve-combined/${currentReviewId}`;

    console.log('Sending combined approval to:', url); // Debug

    // Kirim request
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({ 
            notes: notes || null,
            approval_notes: notes || null
        })
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug
        
        if (data.success) {
            // Build detailed message
            let detailHtml = '<div class="text-start">';
            
            
            if (data.completed_at) {
                detailHtml += `<small class="text-muted"><i class="bi bi-clock me-1"></i>Completed: ${data.completed_at}</small>`;
            }
            
            detailHtml += '</div>';
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Di Approve!',
                html: detailHtml,
                confirmButtonColor: '#198754'
            }).then(() => {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('reviewAplModal'));
                if (modal) modal.hide();
                
                // Refresh tables
                refreshSewaktuTable();
                refreshMandiriTable();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Gagal menyetujui dokumen'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error); // Debug
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan: ' + error.message
        });
    })
    .finally(() => {
        submitBtn.prop('disabled', false).html(originalText);
        $('#btnReject').prop('disabled', false);
    });
}

// ============================================
// 🔥 FIXED REJECT HANDLER
// ============================================
function handleReject() {
    console.log('handleReject called', {currentReviewType, currentReviewId}); // Debug
    
    if (!currentReviewId || !currentReviewType) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Data review tidak valid'
        });
        return;
    }

    // Jika notes section masih hidden, tampilkan dulu
    if ($('#rejectionNotesSection').is(':hidden')) {
        $('#approvalNotesSection').hide();
        $('#rejectionNotesSection').slideDown();
        $('#rejectionNotes').focus();
        $('#btnReject').html('<i class="bi bi-x-circle me-1"></i>Konfirmasi Penolakan');
        return;
    }

    const notes = $('#rejectionNotes').val().trim();
    
    // Validasi
    if (!notes) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Alasan penolakan harus diisi'
        });
        $('#rejectionNotes').focus();
        return;
    }

    if (notes.length < 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Alasan penolakan minimal 10 karakter'
        });
        $('#rejectionNotes').focus();
        return;
    }

    // Konfirmasi
    Swal.fire({
        title: 'Konfirmasi Penolakan',
        html: `
            <div class="text-start">
                <p class="mb-2">Anda akan menolak dokumen <strong>${currentReviewType.toUpperCase()}</strong>:</p>
                <div class="alert alert-warning mb-0">
                    <strong>Alasan:</strong><br>
                    ${notes}
                </div>
                <p class="mt-3 mb-0 text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Dokumen akan dibuka kembali untuk diperbaiki
                </p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-x-circle me-1"></i> Ya, Tolak',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            processRejection(notes);
        }
    });
}

// ============================================
// 🔥 PROCESS REJECTION TO BACKEND - COMBINED
// ============================================
function processRejection(notes) {
    const submitBtn = $('#btnReject');
    const originalText = submitBtn.html();

    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
    $('#btnApprove').prop('disabled', true);

    // 🔥 GUNAKAN ENDPOINT COMBINED - Reject APL01 & APL02 sekaligus
    const url = `/admin/tuk-requests/reject-combined/${currentReviewId}`;

    console.log('Sending combined rejection to:', url); // Debug

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({ 
            notes: notes,
            rejection_notes: notes,
            reject_which: 'both'  // 🔥 Tolak keduanya (APL01 & APL02)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Build detailed message
            let detailHtml = '<div class="text-start">';
            detailHtml += `<div class="mb-3">${data.message}</div>`;
            
            if (data.data) {
                detailHtml += '<div class="alert alert-warning mb-2">';
                detailHtml += '<strong>⚠️ Dokumen yang Ditolak:</strong><br>';
                
                if (data.data.apl01 && data.data.apl01.rejected) {
                    detailHtml += `<small>❌ APL01: ${data.data.apl01.status}</small><br>`;
                }
                
                if (data.data.apl02 && data.data.apl02.rejected) {
                    detailHtml += `<small>❌ APL02: ${data.data.apl02.status}</small><br>`;
                }
                
                detailHtml += '</div>';
            }
            
            detailHtml += '</div>';
            
            Swal.fire({
                icon: 'info',
                title: 'Dokumen Ditolak',
                html: detailHtml,
                confirmButtonColor: '#0d6efd'
            }).then(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('reviewAplModal'));
                if (modal) modal.hide();
                refreshSewaktuTable();
                refreshMandiriTable();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Gagal menolak dokumen'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error); // Debug
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan: ' + error.message
        });
    })
    .finally(() => {
        submitBtn.prop('disabled', false).html(originalText);
        $('#btnApprove').prop('disabled', false);
    });
}

// ============================================
// COMBINED REVIEW MODAL (APL01 & APL02)
// ============================================
function openCombinedReviewModal(apl01Id, isViewMode = false) {
    const modal = new bootstrap.Modal(document.getElementById('reviewAplModal'));
    const titleText = isViewMode 
        ? '<i class="bi bi-eye me-2"></i>Lihat Dokumen APL 01 & APL 02'
        : '<i class="bi bi-file-earmark-check me-2"></i>Review Dokumen APL 01 & APL 02';

    $('#reviewAplModalTitle').html(titleText);
    resetReviewModalUI();

    // 🔥 Rebuild modal footer dengan event handler
    $('.modal-footer').html(`
        <div class="w-100">
            <div class="mb-3" id="approvalNotesSection" style="display: none;">
                <label class="form-label fw-semibold">Catatan Persetujuan (Opsional)</label>
                <textarea class="form-control" id="approvalNotes" rows="2"></textarea>
            </div>
            <div class="mb-3" id="rejectionNotesSection" style="display: none;">
                <div class="alert alert-warning mb-2">
                    <i class="bi bi-info-circle me-2"></i>Dokumen akan dibuka kembali untuk diperbaiki
                </div>
                <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                <textarea class="form-control" id="rejectionNotes" rows="3" required></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Tutup
                </button>
                <div class="btn-group">
                    <button type="button" class="btn btn-danger" id="btnReject">
                        <i class="bi bi-x-circle me-1"></i>Reject
                    </button>
                    <button type="button" class="btn btn-success" id="btnApprove">
                        <i class="bi bi-check-circle me-1"></i>Approve
                    </button>
                </div>
            </div>
        </div>
    `);

    // 🔥 RE-BIND EVENT HANDLERS SETELAH HTML DI-GENERATE
    setTimeout(() => {
        $('#btnApprove').off('click').on('click', function() {
            console.log('Approve button clicked'); // Debug
            handleApprove();
        });

        $('#btnReject').off('click').on('click', function() {
            console.log('Reject button clicked'); // Debug
            handleReject();
        });
    }, 100);

    $('#reviewAplModalBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Memuat detail dokumen...</p>
        </div>
    `);

    modal.show();

    fetch(`/admin/tuk-requests/combined-review/${apl01Id}`)
        .then(response => response.text())
        .then(html => {
            $('#reviewAplModalBody').html(html);
            
            // Set current review data untuk combined modal
            currentReviewType = 'apl01'; // Default ke apl01
            currentReviewId = apl01Id;
            
            const apl01Status = $('#apl01Status').val();
            const apl02Status = $('#apl02Status').val();
            const hasApl02 = $('#apl02Id').val() !== '';
            updateCombinedCompletionBadges(apl01Status, apl02Status, hasApl02);

            if (isViewMode) {
                $('#btnApprove, #btnReject').hide();
                $('#approvalNotesSection, #rejectionNotesSection').hide();
                $('#completionStatusInfo').after(`
                    <div class="alert alert-success mb-3" id="viewOnlyAlert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Dokumen sudah disetujui.</strong> Mode tampilan saja (view only).
                    </div>
                `);
                $('.modal-footer .btn-group').hide();
            } else {
                $('#btnApprove, #btnReject').show();
                $('#viewOnlyAlert').remove();
                $('.modal-footer .btn-group').show();
            }
        })
        .catch(error => {
            $('#reviewAplModalBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading data: ${error.message}
                </div>
            `);
        });
}

function updateCombinedCompletionBadges(apl01Status, apl02Status, hasApl02) {
    let badgesHtml = '';

    // Badge APL01
    if (apl01Status === 'approved') {
        badgesHtml += '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>APL 01: Approved</span>';
    } else if (apl01Status === 'open') {
        badgesHtml += '<span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>APL 01: Re-opened</span>';
    } else {
        badgesHtml += '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>APL 01: Pending</span>';
    }

    // Badge APL02
    if (hasApl02) {
        if (apl02Status === 'approved') {
            badgesHtml += '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>APL 02: Approved</span>';
        } else if (apl02Status === 'open') {
            badgesHtml += '<span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>APL 02: Re-opened</span>';
        } else {
            badgesHtml += '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>APL 02: Pending</span>';
        }
    } else {
        badgesHtml += '<span class="badge bg-light text-dark"><i class="bi bi-info-circle me-1"></i>APL 02: Belum dibuat</span>';
    }

    $('#completionBadges').html(badgesHtml);
}

// Helper functions (tetap ada untuk backward compatibility)
function approveDocument(type, id, notes) {
    const url = type === 'apl01'
        ? `/admin/apl-review/apl01/${id}/approve`
        : `/admin/apl-review/apl02/${id}/approve`;

    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({ notes: notes || null })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Gagal menyetujui dokumen');
        }
        return data;
    });
}

function rejectDocument(type, id, notes) {
    const url = type === 'apl01'
        ? `/admin/apl-review/apl01/${id}/reject`
        : `/admin/apl-review/apl02/${id}/reject`;

    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Gagal menolak dokumen');
        }
        return data;
    });
}