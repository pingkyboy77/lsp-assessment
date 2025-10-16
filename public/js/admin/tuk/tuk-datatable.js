// ============================================
// DATATABLE FUNCTIONS - WITH MANUAL FILTER
// ============================================

function initializeSewaktuTable() {
    sewaktuTable = $('#sewaktuTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/tuk-requests/sewaktu-data',
            data: function(d) {
                d.filter = $('#sewaktuFilter').val();
                d.search = $('#sewaktuSearch').val();
                d.date_from = $('#sewaktuDateFrom').val();
                d.date_to = $('#sewaktuDateTo').val();
            },
            error: function(xhr, error, code) {
                console.error('DataTables AJAX Error:', error);
                showToast('error', 'Gagal memuat data TUK Sewaktu. Status: ' + xhr.status);
            }
        },
        columns: [
            { data: 'kode_tuk', name: 'kode_tuk', orderable: true, searchable: true },
            { data: 'participant_info', name: 'apl01.nama_lengkap', orderable: true, searchable: true },
            { data: 'scheme_name', name: 'certificationScheme.nama', orderable: false, searchable: true },
            { data: 'assessment_info', name: 'tanggal_assessment', orderable: true, searchable: false },
            { data: 'lokasi_info', name: 'lokasi_assessment', orderable: false, searchable: true },
            { data: 'status_badge', name: 'recommended_at', orderable: true, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        searching: false,
    });
}

function initializeMandiriTable() {
    mandiriTable = $('#mandiriTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/tuk-requests/mandiri-data',
            data: function(d) {
                d.search = $('#mandiriSearch').val();
                d.date_from = $('#mandiriDateFrom').val();
                d.date_to = $('#mandiriDateTo').val();
            },
            error: function(xhr, error, code) {
                console.error('DataTables AJAX Error:', error);
                showToast('error', 'Gagal memuat data TUK Mandiri. Status: ' + xhr.status);
            }
        },
        columns: [
            { data: 'nomor_apl_01', name: 'nomor_apl_01', orderable: true, searchable: true },
            { data: 'participant_info', name: 'nama_lengkap', orderable: true, searchable: true },
            { data: 'scheme_name', name: 'certificationScheme.nama', orderable: false, searchable: true },
            { data: 'company_info', name: 'nama_tempat_kerja', orderable: true, searchable: true },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        searching: false,
    });
}

function refreshSewaktuTable() {
    if (sewaktuTable) {
        sewaktuTable.ajax.reload();
        showToast('info', 'Filter TUK Sewaktu diterapkan');
    }
}

function refreshMandiriTable() {
    if (mandiriTable) {
        mandiriTable.ajax.reload();
        showToast('info', 'Filter TUK Mandiri diterapkan');
    }
}

function refreshAllTables() {
    if (sewaktuTable) sewaktuTable.ajax.reload();
    if (mandiriTable) mandiriTable.ajax.reload();
    showToast('info', 'Semua tabel telah di-refresh');
}

// ============================================
// RESET FILTER FUNCTIONS
// ============================================

function resetSewaktuFilter() {
    // Reset all filter inputs
    $('#sewaktuDateFrom').val('');
    $('#sewaktuDateTo').val('');
    $('#sewaktuFilter').val('');
    $('#sewaktuSearch').val('');
    
    // Reload table with cleared filters
    if (sewaktuTable) {
        sewaktuTable.ajax.reload();
        showToast('success', 'Filter TUK Sewaktu direset');
    }
}

function resetMandiriFilter() {
    // Reset all filter inputs
    $('#mandiriDateFrom').val('');
    $('#mandiriDateTo').val('');
    $('#mandiriSearch').val('');
    
    // Reload table with cleared filters
    if (mandiriTable) {
        mandiriTable.ajax.reload();
        showToast('success', 'Filter TUK Mandiri direset');
    }
}

// ============================================
// ENTER KEY SUPPORT FOR SEARCH
// ============================================

// Allow Enter key to trigger filter on search inputs
$('#sewaktuSearch').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
        e.preventDefault();
        refreshSewaktuTable();
    }
});

$('#mandiriSearch').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
        e.preventDefault();
        refreshMandiriTable();
    }
});