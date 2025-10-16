/**
 * Unified APL Monitoring System - Lembaga Pelatihan Version
 * WITH APPROVE/REJECT/REOPEN FUNCTIONALITY
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

    bindEventHandlers() {
        $('#aplTypeSelector').on('change', (e) => {
            this.handleAplTypeChange($(e.target).val());
        });

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
                .scheme-name { font-size: 0.85rem; color: #495057; line-height: 1.3; }
                .date-info { min-width: 100px; text-align: center; }
                .status-center { text-align: center; }
                .element-assessment { border-radius: 12px; padding: 1.5rem; background: rgba(0,0,0,0.02); }
                .assessment-status { padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
                .status-draft { background-color: #6c757d; color: white; }
                .status-submitted { background-color: #0dcaf0; color: white; }
                .status-approved { background-color: #198754; color: white; }
                .status-rejected { background-color: #dc3545; color: white; }
                .status-open { background-color: #fd7e14; color: white; }
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

    handleAplTypeChange(aplType) {
        this.currentAplType = aplType;

        if (!aplType) {
            this.resetInterface();
            return;
        }

        this.showLoading();
        this.showInterface();

        setTimeout(() => this.initializeDataTable(aplType), 200);
    }

    resetInterface() {
        if (this.currentDataTable) {
            this.currentDataTable.destroy();
            this.currentDataTable = null;
        }
        $('#filtersSection, #tableSection').addClass('hidden');
    }

    showInterface() {
        $('#filtersSection, #tableSection').removeClass('hidden');
    }

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
                    url: '/lembaga-pelatihan/monitoring/apl/data',
                    type: 'GET',
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
                order: [[0, 'desc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    loadingRecords: "Memuat data...",
                    zeroRecords: "Tidak ada data yang sesuai",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                },
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
    data: null, 
    className: 'scheme-info',
    render: (row) => {
        const schemeName = row.certification_scheme_nama || 'N/A';
        return `<div class="scheme-name" style="word-wrap: break-word; white-space: normal; max-width: 280px;">${schemeName}</div>`;
    }
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
    data: 'certification_scheme', 
    className: 'scheme-info',
    render: (data) => {
        const schemeName = data || 'N/A';
        return `<div class="scheme-name" style="word-wrap: break-word; white-space: normal; max-width: 280px;">${schemeName}</div>`;
    }
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
                <button class="btn btn-sm btn-outline-success btn-action" 
                        onclick="aplMonitoring.openShowModal('apl01', ${row.id})" 
                        title="Lihat Detail">
                    <i class="bi bi-eye"></i>
                </button>
            `;
        } else {
            actions += '<span class="text-muted small">-</span>';
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
            `;
        }
        return '<span class="text-muted small">-</span>';
    }

    // ==================== REVIEW MODAL (FOR SUBMITTED) ====================
    
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
            const endpoint = `/lembaga-pelatihan/monitoring/${aplType}/${aplId}/review`;
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

    // ==================== SHOW MODAL (FOR APPROVED) ====================
    
    async openShowModal(aplType, aplId) {
        this.currentAplId = aplId;
        this.isShowOnlyMode = true;
        const modalId = aplType === 'apl01' ? 'apl01ReviewModal' : 'apl02ReviewModal';
        const modalBodyId = aplType === 'apl01' ? 'apl01ModalBody' : 'apl02ModalBody';
        
        const modal = new bootstrap.Modal(document.getElementById(modalId));

        $(`#${modalId}`).addClass('modal-show-only');

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
            const endpoint = `/lembaga-pelatihan/monitoring/${aplType}/${aplId}/review`;
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

    // TAMBAHKAN INI - Dokumen Administrasi (User Documents)
    if (data.user_documents && data.user_documents.length > 0) {
        documentsHtml += this.buildUserDocumentsSection('Dokumen Administrasi', data.user_documents, 'person-badge');
    }

    // TAMBAHKAN INI - Dokumen Persyaratan (Requirement Documents)
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
                Detail Data APL 01 (Sudah Disetujui)
            </div>
        `);
    }

    renderApl02ReviewData(data) {
        const headerSection = this.buildApl02HeaderSection(data);
        const assessmentSection = this.buildApl02AssessmentSection(data);

        $('#apl02ModalBody').html(headerSection + assessmentSection);
    }

    renderApl02ShowData(data) {
        const showModeIndicator = `
            <div class="show-mode-indicator">
                <i class="bi bi-eye me-2"></i>
                Detail Data APL 02 (Sudah Disetujui)
            </div>
        `;

        const headerSection = this.buildApl02HeaderSection(data, true);
        const assessmentSection = this.buildApl02AssessmentSection(data, true);

        $('#apl02ModalBody').html(showModeIndicator + headerSection + assessmentSection);
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
                            <div class="fw-semibold">${doc.item_name}</div>
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
                            <div class="fw-semibold">${doc.file_type_text || doc.document_type}</div>
                            <small class="text-muted">${doc.file_size_formatted || doc.file_extension}</small>
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
        
        return `
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informasi Peserta</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr><td class="fw-semibold" style="width: 40%;">No. APL 02:</td><td>${apl02.nomor_apl_02 || 'DRAFT'}</td></tr>
                                <tr><td class="fw-semibold">Nama:</td><td>${participant.nama_lengkap}</td></tr>
                                <tr><td class="fw-semibold">Email:</td><td>${participant.email}</td></tr>
                                <tr><td class="fw-semibold">Status:</td><td><span class="assessment-status status-${apl02.status}">${apl02.status_text}</span></td></tr>
                                <tr><td class="fw-semibold">Skema:</td><td>${certification_scheme.nama}</td></tr>
                                ${apl02.submitted_at ? `<tr><td class="fw-semibold">Submitted:</td><td>${apl02.submitted_at}</td></tr>` : ''}
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

        let html = `
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clipboard2-check me-2"></i>Detail Self Assessment</h6>
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
                const elementClass = isKompeten ? 'border-success' : 'border-danger';

                html += `
                    <div class="element-assessment border rounded p-3 mb-3 ${elementClass}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="mb-3">${element.judul_elemen}</h6>
                                <div class="criteria-section">
                                    <h6 class="fw-semibold text-black mb-2">Kriteria Unjuk Kerja:</h6>
                                    <ol class="ps-3 small">
                `;

                element.criteria.forEach(criteria => {
                    html += `<li class="mb-1">${criteria.uraian_kriteria}</li>`;
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
                                ${this.buildEvidenceSection(element.evidences)}
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

    buildEvidenceSection(evidences) {
        if (!evidences || evidences.length === 0) {
            return '<div class="text-muted small"><i class="bi bi-info-circle me-1"></i>Tidak ada dokumen</div>';
        }

        let html = '';
        evidences.forEach(evidence => {
            const hasFile = evidence.uploaded_evidence !== null;
            html += `
                <div class="evidence-item border rounded p-2 mb-2 ${hasFile ? 'border-success bg-success bg-opacity-10' : 'border-warning bg-warning bg-opacity-10'}">
                    <div class="d-flex align-items-start">
                        <div class="flex-grow-1">
                            <div class="fw-semibold small text-primary mb-1">${evidence.document_name}</div>
                            ${hasFile ? `
                                <div class="small text-success">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    Sudah Upload
                                </div>
                            ` : `
                                <div class="small text-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    Belum Upload
                                </div>
                            `}
                        </div>
                        ${hasFile ? `
                            <button type="button" class="btn btn-outline-primary btn-sm ms-2" 
                                    onclick="aplMonitoring.previewEvidence('${evidence.uploaded_evidence.id}')"
                                    title="Preview">
                                <i class="bi bi-eye"></i>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        return html;
    }

    previewEvidence(evidenceId) {
        const previewUrl = `/lembaga-pelatihan/monitoring/apl02/evidence/${evidenceId}/preview`;
        window.open(previewUrl, '_blank');
    }

    // ==================== PROCESS REVIEW ====================
    
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
            const endpoint = `/lembaga-pelatihan/monitoring/apl/${aplType}/${this.currentAplId}/${action}`;
            
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

    // ==================== REOPEN MODAL ====================
    
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
            const endpoint = `/lembaga-pelatihan/monitoring/apl/${this.currentReopenType}/${this.currentAplId}/reopen`;
            
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
                document.getElementById('reopenNotes').value = '';
            } else {
                throw new Error(result.message || 'Failed to reopen');
            }
        } catch (error) {
            this.showToast('error', 'Gagal membuka kembali: ' + error.message);
        }
    }

    // ==================== UTILITY METHODS ====================

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
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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

// ==================== GLOBAL INITIALIZATION ====================

let aplMonitoring;

$(document).ready(function() {
    aplMonitoring = new UnifiedAplMonitoring();
});

// ==================== GLOBAL FUNCTIONS ====================

function openApl01ReviewModal(aplId) {
    aplMonitoring.openReviewModal('apl01', aplId);
}

function openApl02ReviewModal(aplId) {
    aplMonitoring.openReviewModal('apl02', aplId);
}

function processApl01Review(action) {
    aplMonitoring.processReview('apl01', action);
}

function processApl02Review(action) {
    aplMonitoring.processReview('apl02', action);
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

// ==================== EVENT HANDLERS ====================

$(document).on('hidden.bs.modal', '.modal', function() {
    const modalId = $(this).attr('id');
    if (modalId === 'apl01ReviewModal') {
        $('#apl01ReviewNotes').val('');
        $(this).removeClass('modal-show-only');
    } else if (modalId === 'apl02ReviewModal') {
        $('#apl02ReviewNotes').val('');
        $(this).removeClass('modal-show-only');
    } else if (modalId === 'reopenModal') {
        $('#reopenNotes').val('');
    }
});

document.addEventListener('visibilitychange', function() {
    if (!document.hidden && aplMonitoring && aplMonitoring.currentDataTable) {
        aplMonitoring.currentDataTable.ajax.reload(null, false);
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