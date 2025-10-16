// public/js/admin/mapa-index.js

let currentMapaId = null;
let selectedMapaIds = new Map(); // Changed from Set to Map to store data
let bulkActionType = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializeCheckboxes();
});

function initializeEventListeners() {
    // Filter button
    document.getElementById('applyFilter')?.addEventListener('click', applyFilters);
    
    // Reset filter button
    document.getElementById('resetFilter')?.addEventListener('click', resetFilters);
    
    // Search on Enter
    document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyFilters();
        }
    });
    
    // Select All checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            const mapaData = {
                id: cb.dataset.id,
                nomor: cb.dataset.nomor,
                asesi: cb.dataset.asesi,
                skema: cb.dataset.skema
            };
            
            if (this.checked) {
                selectedMapaIds.set(cb.dataset.id, mapaData);
            } else {
                selectedMapaIds.delete(cb.dataset.id);
            }
        });
        updateBulkActionBar();
    });
}

function initializeCheckboxes() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const mapaData = {
                id: this.dataset.id,
                nomor: this.dataset.nomor,
                asesi: this.dataset.asesi,
                skema: this.dataset.skema
            };
            
            if (this.checked) {
                selectedMapaIds.set(this.dataset.id, mapaData);
            } else {
                selectedMapaIds.delete(this.dataset.id);
            }
            updateBulkActionBar();
            updateSelectAllState();
        });
    });
}

function updateSelectAllState() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
    
    if (selectAll) {
        selectAll.checked = checkedCount > 0 && checkedCount === checkboxes.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    }
}

function updateBulkActionBar() {
    const bulkBar = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedMapaIds.size > 0) {
        bulkBar.classList.add('show');
        selectedCount.textContent = selectedMapaIds.size;
    } else {
        bulkBar.classList.remove('show');
    }
}

function clearSelection() {
    selectedMapaIds.clear();
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActionBar();
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    showLoading();
    
    fetch(`${window.mapaAdminConfig.indexRoute}?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('tableBody').innerHTML = data.html;
            updatePagination(data);
            updateStats(data.stats);
            
            // Reset selections
            selectedMapaIds.clear();
            updateBulkActionBar();
            
            // Re-initialize checkboxes
            initializeCheckboxes();
            
            // Update URL
            window.history.pushState({}, '', `${window.mapaAdminConfig.indexRoute}?${params.toString()}`);
        }
        hideLoading();
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoading();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data'
        });
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    applyFilters();
}

function updatePagination(data) {
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationLinks = document.getElementById('paginationLinks');
    
    if (data.pagination.total > 0) {
        const firstItem = (data.pagination.current_page - 1) * data.pagination.per_page + 1;
        const lastItem = Math.min(data.pagination.current_page * data.pagination.per_page, data.pagination.total);
        
        paginationInfo.textContent = `Menampilkan ${firstItem} - ${lastItem} dari ${data.pagination.total} data`;
        paginationLinks.innerHTML = data.pagination_html;
    }
}

function updateStats(stats) {
    document.querySelectorAll('.counter-number').forEach(el => {
        const key = el.dataset.target;
        if (stats[key] !== undefined) {
            animateCounter(el, parseInt(stats[key]));
        }
    });
}

function animateCounter(element, target) {
    const duration = 500;
    const start = parseInt(element.textContent) || 0;
    const increment = (target - start) / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= target) || (increment < 0 && current <= target)) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

function showLoading() {
    const tableContainer = document.querySelector('.table-container');
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    `;
    overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    tableContainer.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.remove();
}

// Single Review Modal
function showReviewModal(mapaId) {
    currentMapaId = mapaId;
    
    Swal.fire({
        title: 'Memuat data...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`${window.mapaAdminConfig.indexRoute}/${mapaId}/info`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            const mapa = data.mapa;
            document.getElementById('mapaInfo').innerHTML = `
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Nomor MAPA</small>
                                    <strong>${mapa.nomor_mapa}</strong>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Asesi</small>
                                    <strong>${mapa.asesi_name}</strong>
                                    <br><small class="text-muted">${mapa.asesi_email}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Skema</small>
                                    <strong>${mapa.skema_name}</strong>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Asesor</small>
                                    <strong>${mapa.asesor_name}</strong>
                                    <br><small class="text-muted">Submit: ${mapa.submitted_at}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('reviewNotes').value = '';
            const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
            modal.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data MAPA'
        });
    });
}

function confirmApprove() {
    const notes = document.getElementById('reviewNotes').value;
    
    Swal.fire({
        title: 'Approve MAPA?',
        text: 'Asesor akan dapat melakukan validasi MAPA',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Approve',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            submitReview('approve', currentMapaId, notes);
        }
    });
}

function confirmReject() {
    const notes = document.getElementById('reviewNotes').value;
    
    if (!notes || notes.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Catatan Diperlukan',
            text: 'Silakan tulis alasan penolakan di catatan review',
        });
        return;
    }
    
    Swal.fire({
        title: 'Reject MAPA?',
        text: 'MAPA akan dikembalikan ke asesor untuk diperbaiki',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reject',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            submitReview('reject', currentMapaId, notes);
        }
    });
}

function submitReview(action, mapaId, notes) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
    modal.hide();
    
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const url = action === 'approve' 
        ? window.mapaAdminConfig.approveRoute.replace(':id', mapaId)
        : window.mapaAdminConfig.rejectRoute.replace(':id', mapaId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            review_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: true
            }).then(() => {
                applyFilters();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.error || 'Terjadi kesalahan'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message
        });
    });
}

// Bulk Actions
function bulkApprove() {
    if (selectedMapaIds.size === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada MAPA',
            text: 'Pilih MAPA yang ingin di-approve terlebih dahulu'
        });
        return;
    }
    
    bulkActionType = 'approve';
    showBulkModal('approve');
}

function bulkReject() {
    if (selectedMapaIds.size === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada MAPA',
            text: 'Pilih MAPA yang ingin di-reject terlebih dahulu'
        });
        return;
    }
    
    bulkActionType = 'reject';
    showBulkModal('reject');
}

function showBulkModal(action) {
    const isApprove = action === 'approve';
    
    // Update modal title
    document.getElementById('bulkModalTitle').textContent = isApprove ? 'Bulk Approve MAPA' : 'Bulk Reject MAPA';
    
    // Build list of selected MAPA
    let mapaListHtml = '<div class="mb-3"><strong>Daftar MAPA yang akan di-' + (isApprove ? 'approve' : 'reject') + ':</strong></div>';
    mapaListHtml += '<div class="list-group mb-3" style="max-height: 300px; overflow-y: auto;">';
    
    let counter = 1;
    selectedMapaIds.forEach((mapaData, id) => {
        mapaListHtml += `
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-primary me-2">${counter}</span>
                            <h6 class="mb-0">${mapaData.nomor}</h6>
                        </div>
                        <div class="small text-muted">
                            <i class="bi bi-person me-1"></i>${mapaData.asesi}
                        </div>
                        <div class="small text-muted">
                            <i class="bi bi-award me-1"></i>${mapaData.skema}
                        </div>
                    </div>
                </div>
            </div>
        `;
        counter++;
    });
    
    mapaListHtml += '</div>';
    
    // Update info section
    document.getElementById('bulkInfo').innerHTML = mapaListHtml;
    
    // Update textarea
    document.getElementById('bulkReviewNotes').value = '';
    document.getElementById('bulkReviewNotes').placeholder = isApprove 
        ? 'Tulis catatan untuk semua MAPA (opsional)...'
        : 'Tulis catatan untuk semua MAPA (wajib)...';
    
    // Update button
    const confirmBtn = document.getElementById('bulkConfirmBtn');
    confirmBtn.className = isApprove ? 'btn btn-success' : 'btn btn-danger';
    confirmBtn.innerHTML = isApprove 
        ? '<i class="bi bi-check-circle me-1"></i>Approve Semua'
        : '<i class="bi bi-x-circle me-1"></i>Reject Semua';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('bulkReviewModal'));
    modal.show();
}

function confirmBulkAction() {
    const notes = document.getElementById('bulkReviewNotes').value;
    
    if (bulkActionType === 'reject' && (!notes || notes.trim() === '')) {
        Swal.fire({
            icon: 'warning',
            title: 'Catatan Diperlukan',
            text: 'Silakan tulis alasan penolakan untuk bulk reject',
        });
        return;
    }
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('bulkReviewModal'));
    modal.hide();
    
    const actionText = bulkActionType === 'approve' ? 'approve' : 'reject';
    const actionTitle = bulkActionType === 'approve' ? 'Approve' : 'Reject';
    
    Swal.fire({
        title: `${actionTitle} ${selectedMapaIds.size} MAPA?`,
        text: `Anda akan ${actionText} semua MAPA yang dipilih`,
        icon: bulkActionType === 'approve' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: bulkActionType === 'approve' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Ya, ${actionTitle}`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            submitBulkReview(bulkActionType, notes);
        }
    });
}

function submitBulkReview(action, notes) {
    Swal.fire({
        title: 'Memproses...',
        html: 'Sedang memproses <strong>0</strong> dari <strong>' + selectedMapaIds.size + '</strong> MAPA',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const url = action === 'approve' 
        ? window.mapaAdminConfig.bulkApproveRoute
        : window.mapaAdminConfig.bulkRejectRoute;
    
    // Convert Map keys to array
    const mapaIds = Array.from(selectedMapaIds.keys());
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            mapa_ids: mapaIds,
            review_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                html: `
                    <div class="text-start">
                        <p><strong>Berhasil:</strong> ${data.success_count} MAPA</p>
                        ${data.failed_count > 0 ? `<p class="text-danger"><strong>Gagal:</strong> ${data.failed_count} MAPA</p>` : ''}
                    </div>
                `,
                timer: 3000,
                showConfirmButton: true
            }).then(() => {
                clearSelection();
                applyFilters();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.error || 'Terjadi kesalahan'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message
        });
    });
}