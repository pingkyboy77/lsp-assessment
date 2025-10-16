@extends('layouts.admin')

@section('title', 'Tanda Tangan SPT - Admin')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="main-card">
            <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-pen-fill me-2"></i>Tanda Tangan Surat Perintah Tugas (SPT)
                    </h4>
                    <p class="text-black mb-0">Kelola penandatanganan SPT untuk Verifikator, Observer, dan Asesor</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="refreshTable()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <button class="btn btn-success" id="btnSignBulk" onclick="openBulkSignModal()" disabled>
                        <i class="bi bi-pen me-1"></i>Tanda Tangan Semua (<span id="selectedCount">0</span>)
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4 m-3">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Filter Status</label>
                            <select name="status" class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="pending" selected>Menunggu Tanda Tangan</option>
                                <option value="signed">Sudah Ditandatangani</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Cari</label>
                            <input type="text" name="search" class="form-control" id="searchInput"
                                placeholder="Nama asesi, NIK, skema sertifikasi...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="button" class="btn btn-primary" onclick="refreshTable()">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SPT Table -->
            <div class="card m-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2"></i>Daftar SPT
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                            <i class="bi bi-check-all me-1"></i>Pilih Semua
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                            <i class="bi bi-x me-1"></i>Batal Pilih
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive m-3">
                        <table class="table table-hover mb-0" id="sptTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                                    </th>
                                    <th>Asesi</th>
                                    <th>Skema Sertifikasi</th>
                                    <th>Personil</th>
                                    <th>Status</th>
                                    <th width="120">Tanggal Buat</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tanda Tangan dengan Signature Pad -->
    <div class="modal fade" id="signModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-pen-fill me-2"></i>Tanda Tangan Digital SPT
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Informasi:</strong> Silakan tanda tangan di area yang disediakan. Setelah submit, SPT akan
                        di-generate menggunakan tanda tangan resmi direktur. Signature pad ini hanya untuk verifikasi
                        identitas.
                    </div> --}}

                    <div id="signSummary" class="mb-4">
                        <!-- Will be populated with SPT list -->
                    </div>

                    <!-- Signature Pad Area -->
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bi bi-pencil-square me-2"></i>Area Tanda Tangan untuk Verifikasi
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSignature()">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                        <div class="card-body text-center p-0">
                            <canvas id="signaturePad"
                                style="border: 2px dashed #dee2e6; cursor: crosshair; width: 100%; height: 300px;"></canvas>
                        </div>
                        <div class="card-footer text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            Tanda tangan di sini untuk verifikasi. SPT final akan menggunakan tanda tangan resmi direktur.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Penandatangan</label>
                        <input type="text" class="form-control" id="signerName" value="Haryajid Ramelan" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" class="form-control" value="Direktur LSP Pasar Modal" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="signNotes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmSign">
                        <label class="form-check-label" for="confirmSign">
                            Saya <strong>Haryajid Ramelan</strong> (Direktur LSP Pasar Modal) menyetujui dan menandatangani
                            SPT yang dipilih. SPT akan di-generate dengan tanda tangan resmi.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-success" id="btnConfirmSign" onclick="confirmSign()" disabled>
                        <i class="bi bi-pen-fill me-1"></i>Generate & Tanda Tangan SPT
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .spt-card {
            border-left: 4px solid #0d6efd;
            transition: all 0.3s ease;
        }

        .spt-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .personil-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
            display: inline-block;
        }

        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            color: white;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table tbody tr.selected {
            background-color: #e7f1ff;
        }

        .scheme-name {
            font-size: 0.85rem;
            color: #495057;
            line-height: 1.3;
        }

        .badge-pending {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        }

        .badge-signed {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        #btnSignBulk:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .spt-summary-item {
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            border-left: 3px solid #0d6efd;
        }
    </style>
@endpush

@push('scripts')
    <!-- Signature Pad Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/4.1.7/signature_pad.umd.min.js"></script>

    <script>
        let sptTable;
        let selectedSPTs = [];
        let signaturePad;

        $(document).ready(function() {
            initializeDataTable();
            initializeSignaturePad();

            $('#statusFilter').on('change', function() {
                refreshTable();
            });

            $('#searchInput').on('keyup', debounce(function() {
                refreshTable();
            }, 500));

            $('#confirmSign').on('change', function() {
                updateSubmitButton();
            });

            // Reset and resize signature pad when modal is shown
            $('#signModal').on('shown.bs.modal', function() {
                resizeSignaturePad();
            });
        });

        function initializeSignaturePad() {
            const canvas = document.getElementById('signaturePad');

            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 1,
                maxWidth: 2.5
            });

            // Handle canvas resize
            window.addEventListener('resize', resizeSignaturePad);

            // Update submit button when signature changes
            signaturePad.addEventListener('endStroke', function() {
                updateSubmitButton();
            });
        }

        function resizeSignaturePad() {
            const canvas = document.getElementById('signaturePad');
            const ratio = Math.max(window.devicePixelRatio || 1, 1);

            // Get actual width from parent
            const parentWidth = canvas.parentElement.offsetWidth;

            canvas.width = parentWidth * ratio;
            canvas.height = 300 * ratio;
            canvas.style.width = parentWidth + 'px';
            canvas.style.height = '300px';

            const context = canvas.getContext('2d');
            context.scale(ratio, ratio);

            signaturePad.clear();
        }

        function clearSignature() {
            if (signaturePad) {
                signaturePad.clear();
                updateSubmitButton();
            }
        }

        function updateSubmitButton() {
            const hasSignature = signaturePad && !signaturePad.isEmpty();
            const isConfirmed = $('#confirmSign').is(':checked');

            $('#btnConfirmSign').prop('disabled', !(hasSignature && isConfirmed));
        }

        function initializeDataTable() {
            sptTable = $('#sptTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/admin/spt-signatures/data',
                    data: function(d) {
                        d.status = $('#statusFilter').val();
                        d.search = {
                            value: $('#searchInput').val()
                        };
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTables Error:', error);
                        showToast('error', 'Gagal memuat data SPT');
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const status = row.status_badge.includes('pending') ? 'pending' : 'signed';
                            if (status === 'pending') {
                                return `<input type="checkbox" class="spt-checkbox" value="${data}" onchange="handleCheckboxChange()">`;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'asesi_info',
                        name: 'delegasiPersonil.asesi.name',
                        orderable: true
                    },
                    {
                        data: 'scheme_name',
                        name: 'delegasiPersonil.certificationScheme.nama',
                        orderable: false
                    },
                    {
                        data: 'personil_info',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [5, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    emptyTable: 'Tidak ada data SPT',
                    zeroRecords: 'Tidak ada data yang cocok'
                }
            });
        }

        function refreshTable() {
            if (sptTable) {
                sptTable.ajax.reload();
                deselectAll();
            }
        }

        function handleCheckboxChange() {
            selectedSPTs = [];
            $('.spt-checkbox:checked').each(function() {
                selectedSPTs.push($(this).val());
            });

            $('#selectedCount').text(selectedSPTs.length);
            $('#btnSignBulk').prop('disabled', selectedSPTs.length === 0);
        }

        function toggleSelectAll(checkbox) {
            $('.spt-checkbox').prop('checked', checkbox.checked);
            handleCheckboxChange();
        }

        function selectAll() {
            $('.spt-checkbox').prop('checked', true);
            $('#selectAllCheckbox').prop('checked', true);
            handleCheckboxChange();
        }

        function deselectAll() {
            $('.spt-checkbox').prop('checked', false);
            $('#selectAllCheckbox').prop('checked', false);
            handleCheckboxChange();
        }

        function openBulkSignModal() {
            if (selectedSPTs.length === 0) {
                showToast('warning', 'Pilih minimal 1 SPT untuk ditandatangani');
                return;
            }

            loadSignSummary();
            clearSignature();

            const modal = new bootstrap.Modal(document.getElementById('signModal'));
            modal.show();
        }

        function loadSignSummary() {
    $('#signSummary').html(`
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <p class="text-muted mb-0 mt-2">Memuat ringkasan...</p>
        </div>
    `);

    const promises = selectedSPTs.map(id => 
        fetch(`/admin/spt-signatures/${id}/summary`)
            .then(response => response.json())
    );

    Promise.all(promises)
        .then(results => {
            let html = `
                <h6 class="mb-3">
                    <i class="bi bi-list-check me-2"></i>
                    SPT yang akan ditandatangani (${results.length}):
                </h6>
                <div class="spt-summary-list">
            `;

            results.forEach((data, index) => {
                // Backend mengirim is_mandiri berdasarkan case-insensitive check
                const isMandiri = data.is_mandiri || false;
                const tukType = data.tuk_type || 'Sewaktu';
                
                html += `
                    <div class="spt-summary-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <strong>${index + 1}. ${data.asesi_name}</strong>
                                    ${isMandiri 
                                        ? '<span class="badge bg-info text-dark" style="font-size: 0.7rem;"><i class="bi bi-building me-1"></i>TUK Mandiri</span>' 
                                        : '<span class="badge bg-primary" style="font-size: 0.7rem;"><i class="bi bi-wifi me-1"></i>TUK Sewaktu</span>'
                                    }
                                </div>
                                <div class="text-muted small mb-2">
                                    <i class="bi bi-award me-1"></i>${data.scheme_name}
                                </div>
                                <div class="mt-2">
                                    ${!isMandiri ? `
                                        <span class="personil-badge badge bg-primary">
                                            <i class="bi bi-person-check me-1"></i>Verifikator
                                        </span>
                                        <span class="personil-badge badge bg-info">
                                            <i class="bi bi-eye me-1"></i>Observer
                                        </span>
                                        <span class="personil-badge badge bg-success">
                                            <i class="bi bi-clipboard-check me-1"></i>Asesor
                                        </span>
                                    ` : `
                                        <span class="personil-badge badge bg-info">
                                            <i class="bi bi-eye me-1"></i>Observer
                                        </span>
                                        <span class="personil-badge badge bg-success">
                                            <i class="bi bi-clipboard-check me-1"></i>Asesor
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

            html += '</div>';
            
            // Hitung total SPT
            const totalSPT = results.reduce((total, data) => {
                const isMandiri = data.is_mandiri || false;
                return total + (isMandiri ? 2 : 3); // Mandiri: 2 SPT, Sewaktu: 3 SPT
            }, 0);
            
            // Tambahkan info summary di bawah
            html += `
                <div class="mt-3 mb-0">
                </div>
            `;
            
            $('#signSummary').html(html);
            
            console.log(`📝 ${results.length} delegasi → ${totalSPT} file SPT akan digenerate`);
        })
        .catch(error => {
            $('#signSummary').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error loading summary:</strong> ${error.message}
                </div>
            `);
            console.error('Error loading summary:', error);
        });
}

        function confirmSign() {
            if (!$('#confirmSign').is(':checked')) {
                showToast('warning', 'Mohon centang persetujuan terlebih dahulu');
                return;
            }

            if (signaturePad.isEmpty()) {
                showToast('warning', 'Mohon tanda tangan terlebih dahulu');
                return;
            }

            const notes = $('#signNotes').val().trim();
            const submitBtn = $('#btnConfirmSign');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Menandatangani & Generate SPT...'
            );

            fetch('/admin/spt-signatures/sign-bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        delegasi_ids: selectedSPTs,
                        notes: notes || null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);

                        const modal = bootstrap.Modal.getInstance(document.getElementById('signModal'));
                        if (modal) modal.hide();

                        refreshTable();
                        resetSignModal();
                    } else {
                        showToast('error', data.error || 'Gagal menandatangani SPT');
                    }
                })
                .catch(error => {
                    showToast('error', 'Error: ' + error.message);
                })
                .finally(() => {
                    submitBtn.prop('disabled', false).html(originalText);
                });
        }

        function signSingle(delegasiId) {
            selectedSPTs = [delegasiId];
            openBulkSignModal();
        }

        function downloadSPT(delegasiId, type) {
            const typeName = type === 'verifikator' ? 'Verifikator TUK' :
                type === 'observer' ? 'Observer' : 'Asesor';

            showToast('info', `Mengunduh SPT ${typeName}...`);

            window.location.href = `/admin/spt-signatures/${delegasiId}/download/${type}`;
        }

        function resetSignModal() {
            $('#signNotes').val('');
            $('#confirmSign').prop('checked', false);
            $('#btnConfirmSign').prop('disabled', true);
            $('#signSummary').html('');
            clearSignature();
        }

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

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Clean up on modal close
        $('#signModal').on('hidden.bs.modal', function() {
            resetSignModal();
        });
    </script>
@endpush
