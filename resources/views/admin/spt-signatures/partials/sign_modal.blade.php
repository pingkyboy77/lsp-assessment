<div class="modal fade" id="signModal" tabindex="-1" aria-labelledby="signModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="signModalTitle">Tanda Tangan SPT</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="signForm" onsubmit="event.preventDefault(); confirmSign();">
                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        Anda akan melakukan <strong>Generate SPT</strong> (Surat Perintah Tugas) dan <strong>menandatanganinya</strong> sebagai Direktur.
                    </div>
                    
                    {{-- Peringatan Re-Sign --}}
                    <div id="resignWarning" class="alert alert-warning d-none" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>PERINGATAN!</strong> Tindakan ini akan <strong>mengganti</strong> semua SPT yang sudah ada sebelumnya dengan nomor dan file yang baru.
                    </div>

                    <h6 class="mt-3">
                        <i class="bi bi-list-check me-2"></i>SPT yang akan ditandatangani (<span id="sptCount">0</span>):
                    </h6>
                    <div id="signSummary" class="border rounded mb-4" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mb-0 mt-2 small">Memuat data ringkasan...</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="signNotes" class="form-label">
                            <i class="bi bi-pencil-square me-1"></i>Catatan Tambahan (Opsional)
                        </label>
                        <textarea class="form-control" id="signNotes" rows="3" maxlength="1000" 
                            placeholder="Masukkan catatan jika ada, maksimal 1000 karakter..."></textarea>
                        <small class="text-muted">Karakter tersisa: <span id="notesCounter">1000</span></small>
                    </div>

                    {{-- Info Tanda Tangan --}}
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="bi bi-pen me-2"></i>Tanda Tangan Elektronik Direktur
                            </h6>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-shield-check text-success" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1">Tanda tangan akan menggunakan gambar tanda tangan direktur yang sudah tersimpan di server:</p>
                                    <code class="small">assets/signatures/direktur_signature.png</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <button type="submit" id="btnConfirmSign" class="btn btn-primary">
                        <i class="bi bi-pen-fill me-1"></i>Generate & Tanda Tangan SPT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .spt-summary-card {
        transition: all 0.3s ease;
        border-left: 4px solid #0d6efd;
    }
    
    .spt-summary-card:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .tuk-badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 600;
    }
    
    .spt-type-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<script>
    // Character counter untuk notes
    document.addEventListener('DOMContentLoaded', function() {
        const notesTextarea = document.getElementById('signNotes');
        const notesCounter = document.getElementById('notesCounter');
        
        if (notesTextarea && notesCounter) {
            notesTextarea.addEventListener('input', function() {
                const remaining = 1000 - this.value.length;
                notesCounter.textContent = remaining;
                
                if (remaining < 100) {
                    notesCounter.classList.add('text-warning');
                } else {
                    notesCounter.classList.remove('text-warning');
                }
                
                if (remaining < 0) {
                    notesCounter.classList.add('text-danger');
                } else {
                    notesCounter.classList.remove('text-danger');
                }
            });
        }
    });

    // Function untuk load summary dengan info TUK
    function loadSignSummary() {
        $('#signSummary').html(`
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mb-0 mt-2 small">Memuat ringkasan...</p>
            </div>
        `);

        const promises = selectedSPTs.map(id => 
            fetch(`/admin/spt-signatures/${id}/summary`)
                .then(response => response.json())
        );

        Promise.all(promises)
            .then(results => {
                let html = '';
                
                results.forEach((data, index) => {
                    // Backend mengirim is_mandiri berdasarkan case-insensitive check
                    const isMandiri = data.is_mandiri || false;
                    const tukType = data.tuk_type || 'Sewaktu';
                    
                    html += `
                        <div class="spt-summary-card p-3 mb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <strong class="text-primary">${index + 1}. ${data.asesi_name}</strong>
                                        ${isMandiri 
                                            ? '<span class="badge bg-info tuk-badge"><i class="bi bi-building me-1"></i>TUK Mandiri</span>' 
                                            : '<span class="badge bg-primary tuk-badge"><i class="bi bi-wifi me-1"></i>TUK Sewaktu</span>'
                                        }
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="bi bi-award me-1"></i>${data.scheme_name}
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        ${!isMandiri ? `
                                            <span class="badge bg-primary spt-type-badge">
                                                <i class="bi bi-person-check me-1"></i>SPT Verifikator
                                            </span>
                                            <span class="badge bg-info spt-type-badge">
                                                <i class="bi bi-eye me-1"></i>SPT Observer
                                            </span>
                                            <span class="badge bg-success spt-type-badge">
                                                <i class="bi bi-clipboard-check me-1"></i>SPT Asesor
                                            </span>
                                        ` : `
                                            <span class="badge bg-info spt-type-badge">
                                                <i class="bi bi-eye me-1"></i>SPT Observer
                                            </span>
                                            <span class="badge bg-success spt-type-badge">
                                                <i class="bi bi-clipboard-check me-1"></i>SPT Asesor
                                            </span>
                                        `}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-calendar-event me-1"></i>${data.formatted_date}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#signSummary').html(html);
                $('#sptCount').text(results.length);
                
                // Hitung total SPT yang akan digenerate
                const totalSPT = results.reduce((total, data) => {
                    const isMandiri = data.is_mandiri || false;
                    return total + (isMandiri ? 2 : 3); // Mandiri: 2 SPT, Sewaktu: 3 SPT
                }, 0);
                
                console.log(`Total ${results.length} delegasi akan menghasilkan ${totalSPT} file SPT`);
            })
            .catch(error => {
                $('#signSummary').html(`
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> ${error.message}
                    </div>
                `);
            });
    }
</script>