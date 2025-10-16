/**
 * Unified APL Monitoring System
 * Handles both APL 01 and APL 02 monitoring with clean, organized code
 * Version: No Bulk Actions
 */

class UnifiedAplMonitoring {
    constructor() {
        this.currentDataTable = null;
        this.currentAplType = '';
        this.currentAplId = null;
        this.currentReopenType = '';
        this.isShowOnlyMode = false;
        
        this.init();
    }

    init() {
        this.bindEventHandlers();
        this.addCustomStyles();
    }

    // ==================== INITIALIZATION ====================

    bindEventHandlers() {
        // APL Type Selector
        $('#aplTypeSelector').on('change', (e) => {
            this.handleAplTypeChange($(e.target).val());
        });

        // Filter handlers
        $('#date_from, #date_to, #status_filter').on('change', () => {
            if (this.currentDataTable) {
                this.currentDataTable.ajax.reload();
            }
        });
    }

    addCustomStyles() {
        const styles = `
            <style id="apl-monitoring-styles">
                .btn-action { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
                .assessment-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.25rem; }
                .evidence-info { font-size: 0.75rem; color: #6c757d; }
                .participant-info { min-width: 200px; }
                .participant-name { font-weight: 600; color: #212529; }
                .participant-email { font-size: 0.8rem; color: #6c757d; }
                .scheme-info { min-width: 200px; max-width: 300px; word-wrap: break-word; white-space: normal; }
                .scheme-name { font-size: 0.85rem; color: #495057; line-height: 1.3; word-wrap: break-word; white-space: normal; overflow-wrap: break-word; }
                .date-info { min-width: 100px; text-align: center; }
                .status-center { text-align: center; }
                .progress-circle { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; color: white !important; }
                .element-assessment { border-radius: 12px; padding: 1.5rem; background: rgba(0,0,0,0.02); }
                .evidence-file { border: 1px solid #e9ecef; border-radius: 8px; padding: 0.75rem; background: #f8f9fa; transition: all 0.2s ease; }
                .evidence-file:hover { background: #e9ecef; border-color: #007bff; }
                .assessment-status { padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
                .status-draft { background-color: #6c757d; color: white; }
                .status-submitted { background-color: #0dcaf0; color: white; }
                .status-approved { background-color: #198754; color: white; }
                .status-rejected { background-color: #dc3545; color: white; }
                .status-open { background-color: #fd7e14; color: white; }
                .status-returned { background-color: #ffc107; color: black; }
                .document-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; background: #fafafa; transition: all 0.3s ease; }
                .document-card:hover { background: #f0f0f0; border-color: #007bff; transform: translateY(-2px); }
                .file-icon { font-size: 2rem; margin-right: 0.5rem; }
                .modal-show-only .modal-footer .btn-review-action { display: none !important; }
                .show-mode-indicator { 
                    background: linear-gradient(135deg, #198754, #20c997); 
                    color: white; 
                    padding: 0.5rem 1rem; 
                    border-radius: 0.375rem; 
                    margin-bottom: 1rem;
                    text-align: center;
                    font-weight: 600;
                }
            </style>
        `;
        
        if (!document.getElementById('apl-monitoring-styles')) {
            $('head').append(styles);
        }
    }

    // ==================== APL TYPE HANDLING ====================
    
    handleAplTypeChange(aplType) {
        this.currentAplType = aplType;

        if (!aplType) {
            this.resetInterface();
            return;
        }

        this.showLoading();
        this.loadStatistics(aplType);
        this.showInterface();

        setTimeout(() => this.initializeDataTable(aplType), 200);
    }

    resetInterface() {
        if (this.currentDataTable) {
            this.currentDataTable.destroy();
            this.currentDataTable = null;
        }
        $('#statsContainer, #filtersSection, #tableSection').addClass('hidden');
    }

    showInterface() {
        $('#statsContainer, #filtersSection, #tableSection').removeClass('hidden');
    }

    // ==================== STATISTICS ====================
    
    async loadStatistics(aplType) {
        try {
            const response = await fetch(`/admin/monitoring/unified-apl/statistics?type=${aplType}`);
            const stats = await response.json();

            const statsHtml = this.buildStatisticsHTML(stats, aplType);
            $('#statsContainer').html(statsHtml);
        } catch (error) {
            console.error('Failed to load statistics:', error);
            this.showToast('error', 'Failed to load statistics');
        }
    }

    buildStatisticsHTML(stats, aplType) {
        const statItems = [
            { key: 'total', label: `Total ${aplType.toUpperCase()}`, color: 'primary', icon: 'file-earmark-text' },
            { key: 'draft', label: 'Draft', color: 'secondary', icon: 'pencil-square' },
            { key: 'submitted', label: 'Submitted', color: 'info', icon: 'send' },
            { key: 'approved', label: 'Approved', color: 'success', icon: 'check-circle' },
            { key: 'rejected', label: 'Rejected', color: 'danger', icon: 'x-circle' }
        ];

        if (aplType === 'apl01') {
            statItems.push({ key: 'open', label: 'Re Open', color: 'warning', icon: 'unlock' });
        } else {
            statItems.push({ key: 'open', label: 'Re Open', color: 'warning', icon: 'arrow-return-left' });
        }

        let html = '<div class="row">';
        
        statItems.forEach(item => {
            html += `
                <div class="col-md-2">
                    <div class="stat-card card bg-${item.color} bg-opacity-10 text-center p-3">
                        <i class="bi bi-${item.icon} text-${item.color} fs-1"></i>
                        <h4 class="text-${item.color} mb-0">${stats[item.key] || 0}</h4>
                        <small class="text-muted">${item.label}</small>
                    </div>
                </div>
            `;
        });

        html += `
            <div class="col-md-2">
                <div class="text-center">
                    <button class="btn btn-outline-primary btn-lg" onclick="aplMonitoring.exportData('${aplType}')">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                </div>
            </div>
        </div>
        `;

        return html;
    }

    // ==================== DATA TABLE ====================
    
    initializeDataTable(aplType) {
        const tableTitle = aplType === 'apl01' ? 'Data APL 01 - Formulir Permohonan' : 'Data APL 02 - Self Assessment';
        $('#tableTitle').text(tableTitle);

        if (this.currentDataTable) {
            this.currentDataTable.destroy();
            this.currentDataTable = null;
            $('#aplDataTable').empty();
        }

        const { headers, columns } = this.getTableConfiguration(aplType);

        $('#aplDataTable').html(`
            <thead class="table-dark">
                <tr>${headers}</tr>
            </thead>
            <tbody></tbody>
        `);

        $('#aplDataTable').show();

        try {
            this.currentDataTable = $('#aplDataTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                destroy: true,
                responsive: true,
                ajax: {
                    url: '/admin/monitoring/unified-apl/data',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: (d) => {
                        d.apl_type = aplType;
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                        d.status = $('#status_filter').val();
                    },
                    error: (xhr, error, code) => {
                        console.error('DataTable AJAX Error:', error, code);
                        this.hideLoading();
                        this.showToast('error', `Failed to load data: ${error}`);
                    }
                },
                columns: columns,
                order: [[aplType === 'apl01' ? 0 : 0, 'desc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                drawCallback: () => {
                    this.hideLoading();
                },
                initComplete: () => {
                    this.hideLoading();
                    console.log('DataTable initialized for:', aplType);
                }
            });
        } catch (error) {
            console.error('DataTable initialization error:', error);
            this.hideLoading();
        }
    }

    getTableConfiguration(aplType) {
        if (aplType === 'apl01') {
            return {
                headers: `
                    <th><i class="bi bi-person-badge me-1"></i>No. APL / Peserta</th>
                    <th><i class="bi bi-award me-1"></i>Skema Sertifikasi</th>
                    <th class="text-center" width="120"><i class="bi bi-calendar-check me-1"></i>Submitted</th>
                    <th class="text-center" width="100"><i class="bi bi-flag me-1"></i>Status</th>
                    <th><i class="bi bi-person-check me-1"></i>Reviewer</th>
                    <th class="text-center" width="160"><i class="bi bi-gear me-1"></i>Aksi</th>
                `,
                columns: this.getApl01Columns()
            };
        } else {
            return {
                headers: `
                    <th><i class="bi bi-person-badge me-1"></i>No. APL / Peserta</th>
                    <th><i class="bi bi-award me-1"></i>Skema Sertifikasi</th>
                    <th class="text-center" width="180"><i class="bi bi-clipboard-check me-1"></i>Assessment Result</th>
                    <th class="text-center" width="120"><i class="bi bi-calendar-check me-1"></i>Submitted</th>
                    <th class="text-center" width="100"><i class="bi bi-flag me-1"></i>Status</th>
                    <th class="text-center" width="160"><i class="bi bi-gear me-1"></i>Aksi</th>
                `,
                columns: this.getApl02Columns()
            };
        }
    }

    getApl01Columns() {
        return [
            {
                data: null, className: 'participant-info',
                render: (row) => `
                    <div>
                        <div class="participant-name">${row.nomor_apl_01 || 'DRAFT'}</div>
                        <div class="participant-name">${row.nama_lengkap}</div>
                        <div class="participant-email">${row.email}</div>
                    </div>
                `
            },
            {
                data: null, className: 'scheme-info',
                render: (row) => `<div class="scheme-name" style="word-wrap: break-word; white-space: normal;">${row.certification_scheme_nama || 'N/A'}</div>`
            },
            {
                data: 'submitted_at', className: 'date-info',
                render: (data) => data ? this.formatDateTime(data) : '<span class="text-muted">-</span>'
            },
            {
                data: 'status', className: 'status-center',
                render: (data) => {
                    const statusMap = {
                        'draft': 'Draft', 'submitted': 'Submitted', 'approved': 'Approved',
                        'rejected': 'Rejected', 'open': 'Re open'
                    };
                    return `<span class="assessment-status status-${data}">${statusMap[data] || data}</span>`;
                }
            },
            {
                data: 'reviewer_name',
                render: (data) => data ? `<small class="text-muted">${data}</small>` : '<span class="text-muted">-</span>'
            },
            {
                data: null, orderable: false, className: 'text-center',
                render: (row) => this.getApl01ActionButtons(row)
            }
        ];
    }

    getApl02Columns() {
        return [
            {
                data: null, className: 'participant-info',
                render: (row) => `
                    <div>
                        <div class="participant-name">${row.nomor_apl_02 || 'DRAFT'}</div>
                        <div class="participant-name">${row.nama_lengkap}</div>
                        <div class="participant-email">${row.email}</div>
                    </div>
                `
            },
            {
                data: 'certification_scheme', className: 'scheme-info',
                render: (data) => `<div class="scheme-name" style="word-wrap: break-word; white-space: normal;">${data || 'N/A'}</div>`
            },
            {
                data: null, className: 'text-center',
                render: (row) => `
                    <div class="assessment-badges">
                        <span class="badge bg-success">${row.kompeten_count || 0} Kompeten</span>
                        <span class="badge bg-danger">${row.belum_kompeten_count || 0} Belum</span>
                    </div>
                    <div class="evidence-info">${row.evidence_count || 0} Bukti Portfolio</div>
                `
            },
            {
                data: 'submitted_at', className: 'date-info',
                render: (data) => data ? this.formatDateTime(data) : '<span class="text-muted">-</span>'
            },
            {
                data: 'status', className: 'status-center',
                render: (data) => {
                    const statusMap = {
                        'draft': 'Draft', 'submitted': 'Submitted', 'approved': 'Approved',
                        'rejected': 'Rejected', 'returned': 'Returned', 'open': 'Re open'
                    };
                    return `<span class="assessment-status status-${data}">${statusMap[data] || data}</span>`;
                }
            },
            {
                data: null, orderable: false, className: 'text-center',
                render: (row) => this.getApl02ActionButtons(row)
            }
        ];
    }

    getApl01ActionButtons(row) {
        let actions = '<div class="d-flex gap-1">';

        if (row.status === 'submitted') {
            actions += `
                <button class="btn btn-sm btn-outline-info btn-action" 
                        onclick="aplMonitoring.openReviewModal('apl01', ${row.id})" 
                        title="Review">
                    <i class="bi bi-clipboard-check"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning btn-action" 
                        onclick="aplMonitoring.openReopenModal('apl01', ${row.id})" 
                        title="Re Open">
                    <i class="bi bi-unlock"></i>
                </button>
            `;
        } else if (row.status === 'approved') {
            actions += `
                <a href="/admin/apl01/${row.id}" 
                   class="btn btn-sm btn-outline-primary" 
                   title="Lihat Detail">
                    <i class="bi bi-eye"></i>
                </a>
                <button class="btn btn-sm btn-outline-warning btn-action" 
                        onclick="aplMonitoring.openReopenModal('apl01', ${row.id})" 
                        title="Re Open">
                    <i class="bi bi-unlock"></i>
                </button>
            `;
        }

        actions += '</div>';
        return actions;
    }

    getApl02ActionButtons(row) {
        if (row.status === 'submitted') {
            return `
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-info btn-action" 
                            onclick="aplMonitoring.openReviewModal('apl02', ${row.id})" 
                            title="Review">
                        <i class="bi bi-clipboard2-check"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning btn-action" 
                            onclick="aplMonitoring.openReopenModal('apl02', ${row.id})" 
                            title="Reopen">
                        <i class="bi bi-unlock"></i>
                    </button>
                </div>
            `;
        } else if (row.status === 'approved') {
            return `
                <button class="btn btn-sm btn-outline-success btn-action" 
                        onclick="aplMonitoring.openShowModal('apl02', ${row.id})" 
                        title="Lihat Detail">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning btn-action" 
                            onclick="aplMonitoring.openReopenModal('apl02', ${row.id})" 
                            title="Reopen">
                        <i class="bi bi-unlock"></i>
                    </button>
            `;
        }
        return '<span class="text-muted small">-</span>';
    }

    // ==================== REVIEW MODALS ====================
    
    async openReviewModal(aplType, aplId) {
        this.currentAplId = aplId;
        this.isShowOnlyMode = false;
        const modalId = aplType === 'apl01' ? 'apl01ReviewModal' : 'apl02ReviewModal';
        const modalBodyId = aplType === 'apl01' ? 'apl01ModalBody' : 'apl02ModalBody';
        
        const modal = new bootstrap.Modal(document.getElementById(modalId));

        $(`#${modalId}`).removeClass('modal-show-only');

        $(`#${modalBodyId}`).html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat data review...</p>
            </div>
        `);

        modal.show();

        try {
            const endpoint = `/admin/monitoring/unified-apl/${aplType}/${aplId}/review-data`;
            const response = await fetch(endpoint);
            const result = await response.json();

            if (result.success) {
                if (aplType === 'apl01') {
                    this.renderApl01ReviewData(result.data);
                } else {
                    this.renderApl02ReviewData(result.data);
                }
            } else {
                throw new Error(result.message || 'Failed to load review data');
            }
        } catch (error) {
            $(`#${modalBodyId}`).html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error: ${error.message}
                </div>
            `);
        }
    }

    async openShowModal(aplType, aplId) {
        this.currentAplId = aplId;
        this.isShowOnlyMode = true;
        const modalId = aplType === 'apl01' ? 'apl01ReviewModal' : 'apl02ReviewModal';
        const modalBodyId = aplType === 'apl01' ? 'apl01ModalBody' : 'apl02ModalBody';
        
        const modal = new bootstrap.Modal(document.getElementById(modalId));

        $(`#${modalId}`).addClass('modal-show-only');

        if (aplType === 'apl02') {
            $(`#${modalId} .modal-title`).html('<i class="bi bi-eye me-2"></i>Detail APL 02 - Self Assessment');
        }

        $(`#${modalBodyId}`).html(`
            <div class="text-center py-5">
                <div class="spinner-border text-success mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat detail data...</p>
            </div>
        `);

        modal.show();

        try {
            const endpoint = `/admin/monitoring/unified-apl/${aplType}/${aplId}/review-data`;
            const response = await fetch(endpoint);
            const result = await response.json();

            if (result.success) {
                if (aplType === 'apl01') {
                    this.renderApl01ShowData(result.data);
                } else {
                    this.renderApl02ShowData(result.data);
                }
            } else {
                throw new Error(result.message || 'Failed to load data');
            }
        } catch (error) {
            $(`#${modalBodyId}`).html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error: ${error.message}
                </div>
            `);
        }
    }

    renderApl01ReviewData(data) {
        const apl = data.apl;
        let documentsHtml = '';

        if (data.user_documents && data.user_documents.length > 0) {
            documentsHtml += this.buildUserDocumentsSection('Dokumen Administrasi', data.user_documents, 'person-badge');
        }

        if (data.requirement_documents && data.requirement_documents.length > 0) {
            documentsHtml += this.buildDocumentsSection('Dokumen Persyaratan', data.requirement_documents, 'file-earmark-check');
        }

        $('#apl01ModalBody').html(`
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-person-circle me-2"></i>Informasi Peserta</h6>
                            ${this.buildParticipantInfo(apl)}
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-files me-2"></i>Dokumen Peserta</h6>
                            ${documentsHtml || '<p class="text-muted">Tidak ada dokumen yang diupload.</p>'}
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    renderApl01ShowData(data) {
        this.renderApl01ReviewData(data);
        
        $('#apl01ModalBody').prepend(`
            <div class="show-mode-indicator">
                <i class="bi bi-eye me-2"></i>
                Mode Tampilan - Data APL 01 yang sudah disetujui
            </div>
        `);
    }

    renderApl02ReviewData(data) {
        const headerSection = this.buildApl02HeaderSection(data);
        const assessmentSection = this.buildApl02AssessmentSection(data);
        const reviewNotesSection = this.buildApl02ReviewNotesSection(data.apl02);

        $('#apl02ModalBody').html(headerSection + assessmentSection + reviewNotesSection);
    }

    renderApl02ShowData(data) {
        const showModeIndicator = `
            <div class="show-mode-indicator">
                <i class="bi bi-eye me-2"></i>
                Mode Tampilan - Data APL 02 yang sudah disetujui
            </div>
        `;

        const headerSection = this.buildApl02HeaderSection(data, true);
        const assessmentSection = this.buildApl02AssessmentSection(data, true);

        $('#apl02ModalBody').html(showModeIndicator + headerSection + assessmentSection);
        
        setTimeout(() => {
            const modal = $('#apl02ReviewModal');
            
            modal.find('.btn-success, .btn-danger, .btn-primary, .btn-warning').remove();
            modal.find('textarea, input, select, .form-control').remove();
            modal.find('button[onclick*="Review"], button[onclick*="Approve"], button[onclick*="Reject"]').remove();
            
            modal.find(':contains("Catatan review")').each(function() {
                if ($(this).text().includes("Catatan review")) {
                    $(this).remove();
                }
            });
            
            modal.find('.modal-footer').html(`
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Tutup
                </button>
            `).show();
            
            modal.find('.modal-dialog').css('max-height', '90vh');
            modal.find('.modal-body').css({
                'max-height': '70vh',
                'overflow-y': 'auto'
            });
        }, 50);
    }

    buildDocumentsSection(title, documents, icon) {
        let html = `
            <div class="mb-4">
                <h6 class="border-bottom pb-2"><i class="bi bi-${icon} me-2"></i>${title}</h6>
                <div class="row g-3">
        `;

        documents.forEach(doc => {
            const iconClass = this.getFileIcon(doc.file_extension);
            html += `
                <div class="col-md-6">
                    <div class="document-card">
                        <div class="d-flex align-items-center">
                            <i class="${iconClass} file-icon me-2"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${doc.jenis_dokumen || doc.item_name}</div>
                                <small class="text-muted">${doc.file_extension ? doc.file_extension.toUpperCase() : 'FILE'}</small>
                            </div>
                            <div class="ms-2">
                                ${doc.file_exists && doc.file_url ? 
                                    `<a href="${doc.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Preview
                                    </a>` : 
                                    '<span class="badge bg-danger">File Tidak Ada</span>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div></div>';
        return html;
    }

    buildUserDocumentsSection(title, documents, icon) {
        let html = `
            <div class="mb-4">
                <h6 class="border-bottom pb-2"><i class="bi bi-${icon} me-2"></i>${title}</h6>
                <div class="row g-3">
        `;

        documents.forEach(doc => {
            const iconClass = this.getFileIcon(doc.file_extension);
            html += `
                <div class="col-md-6">
                    <div class="document-card">
                        <div class="d-flex align-items-center">
                            <i class="${iconClass} file-icon me-2"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${doc.document_type || doc.original_name}</div>
                                <small class="text-muted">${doc.file_extension ? doc.file_extension.toUpperCase() : 'FILE'}</small>
                            </div>
                            <div class="ms-2">
                                ${doc.file_exists && doc.file_url ? 
                                    `<a href="${doc.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Preview
                                    </a>` : 
                                    '<span class="badge bg-danger">File Tidak Ada</span>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div></div>';
        return html;
    }

    buildParticipantInfo(apl) {
        return `
            <table class="table table-sm table-borderless">
                <tr><td class="fw-semibold">No. APL:</td><td>${apl.nomor_apl_01 || 'DRAFT'}</td></tr>
                <tr><td class="fw-semibold">Nama:</td><td>${apl.nama_lengkap}</td></tr>
                <tr><td class="fw-semibold">Email:</td><td>${apl.email}</td></tr>
                <tr><td class="fw-semibold">No. HP:</td><td>${apl.no_hp || '-'}</td></tr>
                <tr><td class="fw-semibold">Status:</td><td><span class="badge bg-info">${apl.status_text}</span></td></tr>
                <tr><td class="fw-semibold">Submitted:</td><td>${apl.submitted_at || '-'}</td></tr>
                <tr><td class="fw-semibold">Skema:</td><td>${apl.certification_scheme || '-'}</td></tr>
                <tr><td class="fw-semibold">TUK:</td><td>${apl.tuk || '-'}</td></tr>
            </table>
        `;
    }

    buildApl02HeaderSection(data, isShowMode = false) {
        const { apl02, participant, certification_scheme } = data;
        
        const headerClass = isShowMode ? 'bg-success' : 'bg-primary';
        const headerIcon = isShowMode ? 'bi-eye' : 'bi-person-circle';
        
        return `
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header ${headerClass} text-white">
                            <h6 class="mb-0"><i class="bi ${headerIcon} me-2"></i>Informasi Peserta</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr><td class="fw-semibold" style="width: 40%;">No. APL 02:</td><td>${apl02.nomor_apl_02 || 'DRAFT'}</td></tr>
                                <tr><td class="fw-semibold">Nama:</td><td>${participant.nama_lengkap}</td></tr>
                                <tr><td class="fw-semibold">Email:</td><td>${participant.email}</td></tr>
                                <tr><td class="fw-semibold">Status:</td><td><span class="assessment-status status-${apl02.status}">${apl02.status_text}</span></td></tr>
                                <tr><td class="fw-semibold">Skema:</td><td>${certification_scheme.nama}</td></tr>
                                ${apl02.submitted_at ? `<tr><td class="fw-semibold">Submitted:</td><td>${apl02.submitted_at}</td></tr>` : ''}
                                ${apl02.approved_at ? `<tr><td class="fw-semibold">Approved:</td><td>${apl02.approved_at}</td></tr>` : ''}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    buildApl02AssessmentSection(data, isShowMode = false) {
        if (!data.assessment_units || data.assessment_units.length === 0) {
            return '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Data assessment tidak tersedia.</div>';
        }

        const sectionTitle = isShowMode ? 'Detail Self Assessment (Final)' : 'Detail Self Assessment';
        const cardClass = isShowMode ? 'border-success' : '';

        let html = `
            <div class="card ${cardClass}">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clipboard2-check me-2"></i>${sectionTitle}</h6>
                </div>
                <div class="card-body">
        `;

        data.assessment_units.forEach((unit, unitIndex) => {
            html += `
                <div class="unit-section mb-4 ${unitIndex > 0 ? 'border-top pt-4' : ''}">
                    <h5 class="text-black mb-3">
                        <i class="bi bi-book me-2"></i>${unit.kode_unit} - ${unit.judul_unit}
                    </h5>
            `;

            unit.elements.forEach(element => {
                const isKompeten = element.assessment_result === 'kompeten';
                const isBelumKompeten = element.assessment_result === 'belum_kompeten';

                const elementClass = isShowMode ? 
                    (isKompeten ? 'border-success' : 
                     isBelumKompeten ? 'border-danger' : 'border-secondary') :
                    (isKompeten ? 'border-success' : 
                     isBelumKompeten ? 'border-danger' : 'border-secondary');

                html += `
                    <div class="element-assessment border rounded p-3 mb-3 ${elementClass}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <h6 class="mb-3" style="text-align: justify; line-height: 1.5;">${element.judul_elemen}</h6>
                                </div>
                                <div class="criteria-section">
                                    <h6 class="fw-semibold text-black mb-2">Kriteria Unjuk Kerja:</h6>
                                    <ol class="ps-3 small" style="line-height: 1.4;">
                `;

                element.criteria.forEach(criteria => {
                    html += `<li class="mb-1"><p class="fw-semibold text-black mb-2">${criteria.uraian_kriteria}</p></li>`;
                });

                html += `
                                    </ol>
                                </div>
                            </div>
                            
                            <div class="col-md-3 border-start ps-3">
                                <h6 class="fw-semibold mb-3">Penilaian Diri:</h6>
                                <div class="mb-2">
                                    ${isKompeten 
                                        ? '<span class="text-success fw-semibold">Kompeten</span>' 
                                        : '<span class="text-danger fw-semibold">Belum Kompeten</span>'}
                                </div>
                            </div>
                            
                            <div class="col-md-3 border-start ps-3">
                                <h6 class="fw-semibold mb-3">Dokumen Portfolio:</h6>
                                ${this.buildEvidenceSection(element.evidences, isShowMode)}
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
        });

        html += '</div></div>';
        return html;
    }

    buildEvidenceSection(evidences, isShowMode = false) {
        if (!evidences || evidences.length === 0) {
            return '<div class="text-muted small text-center"><i class="bi bi-info-circle me-1"></i>Tidak ada dokumen dipilih</div>';
        }

        let html = '';
        evidences.forEach(evidence => {
            const hasFile = evidence.uploaded_evidence !== null && evidence.uploaded_evidence.file_name;
            const fileClass = hasFile ? 
                (isShowMode ? 'border-warning bg-warning bg-opacity-10' : 'border-success bg-success bg-opacity-10') : 
                'border-warning bg-warning bg-opacity-10';

            html += `
                <div class="evidence-item border rounded p-2 mb-2 ${fileClass}">
                    <div class="d-flex align-items-start">
                        <div class="file-icon me-2 mt-1">
                            ${hasFile ? this.getFileIconHtml(evidence.uploaded_evidence.file_type || evidence.uploaded_evidence.mime_type) : '<i class="bi bi-file-earmark-plus text-muted"></i>'}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small text-primary mb-1">${evidence.document_name}</div>
                            ${hasFile ? `
                                <div class="small text-success mb-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    <span class="fw-medium">${evidence.uploaded_evidence.file_name}</span>
                                    ${isShowMode ? '<span class="badge bg-success ms-1">Verified</span>' : ''}
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-file-earmark me-1"></i>
                                    ${this.formatFileSize(evidence.uploaded_evidence.file_size)}
                                    ${evidence.uploaded_evidence.created_at ? 
                                        `• Upload: ${this.formatDate(evidence.uploaded_evidence.created_at)}` : 
                                        ''
                                    }
                                </div>
                                ${evidence.uploaded_evidence.description ? `
                                    <div class="small text-info mt-1">
                                        <i class="bi bi-info-circle me-1"></i>
                                        ${evidence.uploaded_evidence.description}
                                    </div>
                                ` : ''}
                            ` : `
                                <div class="small text-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    Belum diupload
                                </div>
                                ${evidence.is_required ? `
                                    <div class="small text-danger">
                                        <i class="bi bi-asterisk me-1"></i>
                                        Dokumen wajib
                                    </div>
                                ` : ''}
                            `}
                        </div>
                        ${hasFile ? `
                            <div class="ms-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        onclick="aplMonitoring.previewEvidence('${evidence.uploaded_evidence.id}', '${evidence.uploaded_evidence.file_name}')"
                                        title="Preview ${evidence.uploaded_evidence.file_name}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        return html;
    }

    previewEvidence(evidenceId, fileName) {
        const previewUrl = `/admin/monitoring/unified-apl/apl02/evidence/${evidenceId}/preview`;
        
        const newWindow = window.open(previewUrl, '_blank');
        
        if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
            this.showToast('warning', 'Popup diblokir oleh browser. Silakan izinkan popup dan coba lagi.');
            console.warn('Popup blocked for URL:', previewUrl);
        }
    }

    formatFileSize(bytes) {
        if (!bytes) return '0 bytes';
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        } else {
            return bytes + ' bytes';
        }
    }

    formatDate(dateString) {
        if (!dateString) return '';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        } catch (error) {
            return dateString;
        }
    }

    buildApl02ReviewNotesSection(apl02) {
        if (!apl02.reviewer_notes && !apl02.catatan_asesor) {
            return '';
        }

        return `
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan Review</h6>
                </div>
                <div class="card-body">
                    ${apl02.reviewer_notes ? `
                        <div class="mb-3">
                            <h6 class="fw-bold text-primary">Catatan Reviewer:</h6>
                            <div class="p-3 bg-light rounded">${apl02.reviewer_notes}</div>
                        </div>
                    ` : ''}
                    ${apl02.catatan_asesor ? `
                        <div class="mb-3">
                            <h6 class="fw-bold text-success">Catatan Asesor:</h6>
                            <div class="p-3 bg-light rounded">${apl02.catatan_asesor}</div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    async processReview(aplType, action) {
        const notesId = aplType === 'apl01' ? 'apl01ReviewNotes' : 'apl02ReviewNotes';
        const notes = document.getElementById(notesId).value;
        
        if (action === 'reject' && !notes.trim()) {
            this.showToast('error', 'Catatan wajib diisi untuk aksi reject.');
            return;
        }

        const button = event.target;
        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

        try {
            const endpoint = `/admin/monitoring/unified-apl/${aplType}/${this.currentAplId}/${action}`;
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ notes: notes })
            });

            const result = await response.json();

            if (result.success) {
                const modalId = aplType === 'apl01' ? 'apl01ReviewModal' : 'apl02ReviewModal';
                bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                this.showToast('success', result.message);
                
                if (this.currentDataTable) this.currentDataTable.ajax.reload();
                this.loadStatistics(this.currentAplType);
            } else {
                throw new Error(result.message || 'Action failed');
            }
        } catch (error) {
            this.showToast('error', error.message);
        } finally {
            button.disabled = false;
            button.innerHTML = originalContent;
        }
    }

    openReopenModal(aplType, aplId) {
        this.currentAplId = aplId;
        this.currentReopenType = aplType;

        const titles = {
            apl01: 'Reopen APL 01',
            apl02: 'Reopen APL 02'
        };

        const messages = {
            apl01: 'Apakah Anda yakin ingin membuka kembali APL 01 ini?',
            apl02: 'Apakah Anda yakin ingin membuka kembali APL 02 ini?'
        };

        const warnings = {
            apl01: 'Setelah dibuka kembali, status akan berubah menjadi "Open" dan asesi dapat mengedit form ini kembali.',
            apl02: 'Setelah dibuka kembali, status akan berubah menjadi "Open" dan asesi dapat merevisi assessment ini kembali.'
        };

        document.getElementById('reopenModalTitle').textContent = titles[aplType];
        document.getElementById('reopenModalMessage').textContent = messages[aplType];
        document.getElementById('reopenWarningText').textContent = warnings[aplType];

        const modal = new bootstrap.Modal(document.getElementById('reopenModal'));
        modal.show();
    }

    async confirmReopen() {
        const notes = document.getElementById('reopenNotes').value;
        const modal = bootstrap.Modal.getInstance(document.getElementById('reopenModal'));

        try {
            const endpoint = `/admin/monitoring/unified-apl/${this.currentReopenType}/${this.currentAplId}/reopen`;
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ notes: notes })
            });

            const result = await response.json();

            if (result.success) {
                modal.hide();
                this.showToast('success', result.message);
                if (this.currentDataTable) this.currentDataTable.ajax.reload();
                this.loadStatistics(this.currentAplType);
                document.getElementById('reopenNotes').value = '';
            } else {
                throw new Error(result.message || 'Failed to reopen');
            }
        } catch (error) {
            this.showToast('error', 'Gagal membuka kembali: ' + error.message);
        }
    }

    showLoading() {
        $('.data-loading').show();
        $('#aplDataTable').hide();
    }

    hideLoading() {
        $('.data-loading').hide();
        $('#aplDataTable').show();
    }

    formatDateTime(dateString) {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return `
                <div>
                    <div>${date.toLocaleDateString('id-ID')}</div>
                    <small class="text-muted">${date.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'})}</small>
                </div>
            `;
        } catch (error) {
            return dateString;
        }
    }

    getFileIcon(extension) {
        if (!extension) return 'bi bi-file-earmark text-secondary';

        const ext = extension.toLowerCase().replace('.', '');
        const iconMap = {
            'pdf': 'bi bi-file-earmark-pdf text-danger',
            'jpg': 'bi bi-file-earmark-image text-success',
            'jpeg': 'bi bi-file-earmark-image text-success',
            'png': 'bi bi-file-earmark-image text-success',
            'gif': 'bi bi-file-earmark-image text-success',
            'doc': 'bi bi-file-earmark-word text-primary',
            'docx': 'bi bi-file-earmark-word text-primary',
            'xls': 'bi bi-file-earmark-excel text-success',
            'xlsx': 'bi bi-file-earmark-excel text-success'
        };

        return iconMap[ext] || 'bi bi-file-earmark text-secondary';
    }

    getFileIconHtml(fileType) {
        if (!fileType) return '<i class="bi bi-file-earmark text-secondary"></i>';

        const type = fileType.toLowerCase();
        
        if (type.includes('pdf') || type === 'pdf') {
            return '<i class="bi bi-file-earmark-pdf text-danger"></i>';
        } else if (type.includes('word') || type.includes('doc') || type === 'doc' || type === 'docx') {
            return '<i class="bi bi-file-earmark-word text-primary"></i>';
        } else if (type.includes('image') || ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].some(ext => type.includes(ext))) {
            return '<i class="bi bi-file-earmark-image text-success"></i>';
        } else if (type.includes('excel') || type.includes('spreadsheet') || ['xls', 'xlsx'].some(ext => type.includes(ext))) {
            return '<i class="bi bi-file-earmark-excel text-success"></i>';
        } else if (type.includes('powerpoint') || type.includes('presentation') || ['ppt', 'pptx'].some(ext => type.includes(ext))) {
            return '<i class="bi bi-file-earmark-ppt text-warning"></i>';
        } else if (type.includes('video') || ['mp4', 'avi', 'mov', 'wmv', 'flv'].some(ext => type.includes(ext))) {
            return '<i class="bi bi-file-earmark-play text-info"></i>';
        } else if (type.includes('audio') || ['mp3', 'wav', 'ogg', 'm4a'].some(ext => type.includes(ext))) {
            return '<i class="bi bi-file-earmark-music text-purple"></i>';
        } else if (type.includes('zip') || type.includes('rar') || type.includes('7z') || type.includes('tar')) {
            return '<i class="bi bi-file-earmark-zip text-warning"></i>';
        } else if (type.includes('text') || type === 'txt') {
            return '<i class="bi bi-file-earmark-text text-secondary"></i>';
        }

        return '<i class="bi bi-file-earmark text-secondary"></i>';
    }

    exportData(aplType) {
        const routes = {
            apl01: '/admin/apl01/export',
            apl02: '/admin/apl02/export'
        };

        this.showToast('info', 'Export sedang diproses...');

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routes[aplType];

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = $('meta[name="csrf-token"]').attr('content');
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    resetFilters() {
        const today = new Date().toISOString().split('T')[0];
        $('#date_from').val(today);
        $('#date_to').val(today);
        $('#status_filter').val('').trigger('change');
        
        if (this.currentDataTable) {
            this.currentDataTable.ajax.reload();
        }
    }

    refreshTable() {
        if (this.currentDataTable) {
            this.currentDataTable.ajax.reload();
        }
        this.loadStatistics(this.currentAplType);
    }

    showToast(type, message) {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const typeConfig = {
            success: { bg: 'bg-success', icon: 'bi-check-circle' },
            error: { bg: 'bg-danger', icon: 'bi-exclamation-triangle' },
            warning: { bg: 'bg-warning', icon: 'bi-exclamation-triangle' },
            info: { bg: 'bg-info', icon: 'bi-info-circle' }
        };

        const config = typeConfig[type] || typeConfig.info;

        const toastHtml = `
            <div class="toast align-items-center text-white ${config.bg}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${config.icon} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = container.lastElementChild;
        const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
}

let aplMonitoring;

$(document).ready(function() {
    aplMonitoring = new UnifiedAplMonitoring();
});

function openApl01ReviewModal(aplId) {
    aplMonitoring.openReviewModal('apl01', aplId);
}

function openApl02ReviewModal(aplId) {
    aplMonitoring.openReviewModal('apl02', aplId);
}

function openApl02ShowModal(aplId) {
    aplMonitoring.openShowModal('apl02', aplId);
}

function processApl01Review(action) {
    aplMonitoring.processReview('apl01', action);
}

function processApl02Review(action) {
    aplMonitoring.processReview('apl02', action);
}

function reopenApl(aplId) {
    aplMonitoring.openReopenModal('apl01', aplId);
}

function reopenApl02(aplId) {
    aplMonitoring.openReopenModal('apl02', aplId);
}

function confirmReopen() {
    aplMonitoring.confirmReopen();
}

function resetFilters() {
    aplMonitoring.resetFilters();
}

function refreshTable() {
    aplMonitoring.refreshTable();
}

function exportData(aplType) {
    aplMonitoring.exportData(aplType);
}

$(document).on('hidden.bs.modal', '.modal', function() {
    const modalId = $(this).attr('id');
    if (modalId === 'apl01ReviewModal') {
        $('#apl01ReviewNotes').val('');
        $(this).removeClass('modal-show-only');
        $(this).find('.modal-title').html('<i class="bi bi-clipboard-check me-2"></i>Review APL 01');
    } else if (modalId === 'apl02ReviewModal') {
        $('#apl02ReviewNotes').val('');
        $(this).removeClass('modal-show-only');
        $(this).find('.modal-title').html('<i class="bi bi-clipboard2-check me-2"></i>Review APL 02');
    } else if (modalId === 'reopenModal') {
        $('#reopenNotes').val('');
    }
});

document.addEventListener('visibilitychange', function() {
    if (!document.hidden && aplMonitoring && aplMonitoring.currentDataTable) {
        aplMonitoring.currentDataTable.ajax.reload(null, false);
    }
});

window.addEventListener('popstate', function(event) {
    if (aplMonitoring && aplMonitoring.currentAplType) {
        aplMonitoring.loadStatistics(aplMonitoring.currentAplType);
        if (aplMonitoring.currentDataTable) {
            aplMonitoring.currentDataTable.ajax.reload();
        }
    }
});

$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        // ESC handling without bulk selection
    }
    
    if (e.ctrlKey && e.key === 'r' && aplMonitoring && aplMonitoring.currentDataTable) {
        e.preventDefault();
        aplMonitoring.refreshTable();
    }
});

$(document).ready(function() {
    $('[title]').tooltip({
        placement: 'auto',
        trigger: 'hover focus'
    });
    
    $(document).on('draw.dt', '#aplDataTable', function() {
        $('[title]').tooltip('dispose').tooltip({
            placement: 'auto',
            trigger: 'hover focus'
        });
    });
});

$(window).on('beforeunload', function() {
    if (aplMonitoring && aplMonitoring.currentDataTable) {
        aplMonitoring.currentDataTable.destroy();
    }
    
    aplMonitoring = null;
    
    $(document).off('keydown');
    $(window).off('popstate');
    document.removeEventListener('visibilitychange');
});

window.addEventListener('error', function(e) {
    console.error('Unhandled JavaScript error:', e.error);
    
    if (aplMonitoring) {
        aplMonitoring.showToast('error', 'Terjadi kesalahan sistem. Silakan refresh halaman jika diperlukan.');
    }
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
    
    if (aplMonitoring) {
        aplMonitoring.showToast('warning', 'Operasi gagal dijalankan. Silakan coba lagi.');
    }
    
    e.preventDefault();
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { UnifiedAplMonitoring };
}