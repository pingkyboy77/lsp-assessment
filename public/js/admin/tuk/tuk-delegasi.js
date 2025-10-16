// ============================================
// DELEGASI PERSONIL FUNCTIONS
// ============================================

async function loadUsersByRole() {
    try {
        const response = await fetch('/admin/users/by-roles', {
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        const data = await response.json();

        if (data.success) {
            verifikatorList = data.verifikator || [];
            observerList = data.observer || [];
            asesorList = data.asesor || [];
        }
    } catch (error) {
        console.error('Error loading users by role:', error);
    }
}

function openDelegasiModalSewaktu(tukRequestId) {
    fetch(`/admin/tuk-requests/${tukRequestId}/delegasi-data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateDelegasiForm(data.data, 'sewaktu');
                const modal = new bootstrap.Modal(document.getElementById('delegasiPersonilModal'));
                modal.show();
            } else {
                showToast('error', 'Gagal memuat data');
            }
        })
        .catch(error => showToast('error', 'Error: ' + error.message));
}

function openDelegasiModalMandiri(apl01Id) {
    fetch(`/admin/tuk-requests/apl01/${apl01Id}/delegasi-data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateDelegasiForm(data.data, 'mandiri');
                const modal = new bootstrap.Modal(document.getElementById('delegasiPersonilModal'));
                modal.show();
            } else {
                showToast('error', 'Gagal memuat data');
            }
        })
        .catch(error => {
            console.error('Error loading delegasi data:', error);
            showToast('error', 'Error: ' + error.message);
        });
}

function populateDelegasiForm(data, type) {
    resetDelegasiModalToForm();
    $('#delegasiPersonilForm')[0].reset();

    $('#asesi_id').val(data.asesi_id);
    $('#asesi_nama').val(data.asesi_nama);
    $('#certification_scheme_id').val(data.certification_scheme_id);
    $('#skema_nama').val(data.skema_nama);
    $('#apl01_id').val(data.apl01_id || '');

    if (type === 'sewaktu') {
        $('#tuk_request_id').val(data.tuk_request_id || '');
    } else if (type === 'mandiri') {
        $('#tuk_request_id').val('');
    }

    $('#delegasiPersonilForm').removeData('is-edit').removeData('delegasi-id');

    populateVerifikatorDropdown();
    populateObserverDropdown();
    populateAsesorDropdown();

    let tanggalAssessment, waktuMulai;

    if (type === 'mandiri') {
        tanggalAssessment = '';
        waktuMulai = '08:00';
        $('#autoFillInfo').hide();
        $('#manualFillInfo').show();

        if (!data.existing_delegation) {
            showToast('info', 'TUK Mandiri: Silakan isi tanggal dan waktu pelaksanaan secara manual');
        }
    } else {
        tanggalAssessment = data.tanggal_assessment || new Date().toISOString().split('T')[0];
        waktuMulai = data.jam_mulai || '08:00';
        $('#autoFillInfo').show();
        $('#manualFillInfo').hide();
    }

    $('#tanggal_pelaksanaan').val(tanggalAssessment);
    $('#waktu_mulai').val(waktuMulai);

    if (tanggalAssessment) {
        $('#verifikator_spt_date, #observer_spt_date, #asesor_spt_date').val(tanggalAssessment);
    } else {
        $('#verifikator_spt_date, #observer_spt_date, #asesor_spt_date').val('');
    }

    if (data.existing_delegation) {
        const ed = data.existing_delegation;
        setTimeout(() => {
            $('#verifikator_tuk_id').val(ed.verifikator_tuk_id);
            $('#verifikator_nik').val(ed.verifikator_nik);
            $('#verifikator_spt_date').val(ed.verifikator_spt_date);
            $('#observer_id').val(ed.observer_id);
            $('#observer_nik').val(ed.observer_nik);
            $('#observer_spt_date').val(ed.observer_spt_date);
            $('#asesor_id').val(ed.asesor_id);
            $('#asesor_met').val(ed.asesor_met);
            $('#asesor_spt_date').val(ed.asesor_spt_date);
            $('#tanggal_pelaksanaan').val(ed.tanggal_pelaksanaan_asesmen);
            $('#waktu_mulai').val(ed.waktu_mulai);
            $(`input[name="jenis_ujian"][value="${ed.jenis_ujian}"]`).prop('checked', true);
            $('#notes').val(ed.notes);
            $('#delegasiPersonilForm').data('is-edit', true).data('delegasi-id', ed.id);
        }, 300);
    }
}

function resetDelegasiModalToForm() {
    const modalBody = $('#delegasiPersonilModal .modal-body');
    if (modalBody.find('#delegasiPersonilForm').length === 0) {
        location.reload();
    }
}

function populateVerifikatorDropdown() {
    const select = $('#verifikator_tuk_id');
    select.empty().append('<option value="">-- Pilih Verifikator --</option>');
    verifikatorList.forEach(user => {
        select.append(`<option value="${user.id}" data-nik="${user.id_number}">${user.name}</option>`);
    });
}

function populateObserverDropdown() {
    const select = $('#observer_id');
    select.empty().append('<option value="">-- Pilih Observer --</option>');
    observerList.forEach(user => {
        select.append(`<option value="${user.id}" data-nik="${user.id_number}">${user.name}</option>`);
    });
}

function populateAsesorDropdown() {
    const select = $('#asesor_id');
    select.empty().append('<option value="">-- Pilih Asesor --</option>');
    asesorList.forEach(user => {
        select.append(`<option value="${user.id}" data-met="${user.id_number}">${user.name}</option>`);
    });
}

function setupDelegasiFormHandlers() {
    $(document).on('change', '#verifikator_tuk_id', function() {
        const nik = $(this).find('option:selected').data('nik');
        $('#verifikator_nik').val(nik || '');
    });

    $(document).on('change', '#observer_id', function() {
        const nik = $(this).find('option:selected').data('nik');
        $('#observer_nik').val(nik || '');
    });

    $(document).on('change', '#asesor_id', function() {
        const met = $(this).find('option:selected').data('met');
        $('#asesor_met').val(met || '');
    });

    // Auto-sync tanggal SPT
    $(document).on('change', '#tanggal_pelaksanaan', function() {
        const tanggalPelaksanaan = $(this).val();
        if (tanggalPelaksanaan) {
            $('#verifikator_spt_date, #observer_spt_date, #asesor_spt_date').val(tanggalPelaksanaan);
            showToast('info', 'Tanggal SPT otomatis disesuaikan dengan tanggal pelaksanaan');
        }
    });

    // Warning jika manual edit
    let sptManuallyEdited = false;
    $(document).on('change', '#verifikator_spt_date, #observer_spt_date, #asesor_spt_date', function() {
        const tanggalPelaksanaan = $('#tanggal_pelaksanaan').val();
        const sptDate = $(this).val();

        if (tanggalPelaksanaan && sptDate !== tanggalPelaksanaan && !sptManuallyEdited) {
            const fieldName = $(this).attr('id').replace('_spt_date', '').replace('_', ' ');
            showToast('warning', `Tanggal SPT ${fieldName} diubah manual. Pastikan tanggal sudah benar.`);
            sptManuallyEdited = true;
        }
    });

    $('#delegasiPersonilModal').on('show.bs.modal', function() {
        sptManuallyEdited = false;
    });

    // Form submission
    $(document).on('submit', '#delegasiPersonilForm', function(e) {
        e.preventDefault();

        const isEdit = $(this).data('is-edit') === true;
        const delegasiId = $(this).data('delegasi-id');
        const submitBtn = $('#submitDelegasiBtn');
        const originalText = submitBtn.html();

        if (!validateDelegasiForm()) return;

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        const apl01Value = $('#apl01_id').val();
        const tukRequestValue = $('#tuk_request_id').val();

        const formData = {
            asesi_id: $('#asesi_id').val(),
            certification_scheme_id: $('#certification_scheme_id').val(),
            apl01_id: apl01Value ? apl01Value : null,
            tuk_request_id: tukRequestValue ? tukRequestValue : null,
            jenis_ujian: $('input[name="jenis_ujian"]:checked').val(),
            verifikator_tuk_id: $('#verifikator_tuk_id').val(),
            verifikator_nik: $('#verifikator_nik').val(),
            verifikator_spt_date: $('#verifikator_spt_date').val(),
            observer_id: $('#observer_id').val(),
            observer_nik: $('#observer_nik').val(),
            observer_spt_date: $('#observer_spt_date').val(),
            asesor_id: $('#asesor_id').val(),
            asesor_met: $('#asesor_met').val(),
            asesor_spt_date: $('#asesor_spt_date').val(),
            tanggal_pelaksanaan_asesmen: $('#tanggal_pelaksanaan').val(),
            waktu_mulai: $('#waktu_mulai').val(),
            notes: $('#notes').val()
        };

        if (isEdit) formData._method = 'PUT';

        const url = isEdit ? `/admin/delegasi-personil/${delegasiId}` : '/admin/delegasi-personil/store';

        fetch(url, {
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
                showToast('success', data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('delegasiPersonilModal'));
                if (modal) modal.hide();

                if (formData.tuk_request_id) {
                    refreshSewaktuTable();
                } else if (formData.apl01_id) {
                    refreshMandiriTable();
                }
            } else {
                showToast('error', data.error || 'Gagal menyimpan delegasi');
            }
        })
        .catch(error => showToast('error', 'Error: ' + error.message))
        .finally(() => submitBtn.prop('disabled', false).html(originalText));
    });
}

function validateDelegasiForm() {
    const required = [
        { id: '#verifikator_tuk_id', label: 'Verifikator TUK' },
        { id: '#verifikator_spt_date', label: 'Tanggal SPT Verifikator' },
        { id: '#observer_id', label: 'Observer' },
        { id: '#observer_spt_date', label: 'Tanggal SPT Observer' },
        { id: '#asesor_id', label: 'Asesor' },
        { id: '#asesor_spt_date', label: 'Tanggal SPT Asesor' },
        { id: '#tanggal_pelaksanaan', label: 'Tanggal Pelaksanaan' },
        { id: '#waktu_mulai', label: 'Waktu Mulai' }
    ];

    for (let field of required) {
        if (!$(field.id).val()) {
            showToast('error', `${field.label} harus diisi`);
            $(field.id).focus();
            return false;
        }
    }
    return true;
}

// ============================================
// VIEW/EDIT MODE FUNCTIONS
// ============================================

let isEditMode = false;
let originalDropdownData = { verifikator: [], observer: [], asesor: [] };

function toggleEditMode() {
    isEditMode = !isEditMode;

    if (isEditMode) {
        $('.view-mode').hide();
        $('.edit-mode').show();
        $('#viewTitle').text('Edit Delegasi Personil');
        $('#btnToggleEdit').html('<i class="bi bi-eye me-1"></i>Lihat').removeClass('btn-outline-primary').addClass('btn-outline-secondary');

        if (originalDropdownData.verifikator.length === 0) {
            loadDropdownsForEdit();
        }

        showToast('info', 'Mode Edit aktif. Silakan ubah data yang diperlukan.');
    } else {
        $('.edit-mode').hide();
        $('.view-mode').show();
        $('#viewTitle').text('Detail Delegasi Personil');
        $('#btnToggleEdit').html('<i class="bi bi-pencil me-1"></i>Edit').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    }
}

function cancelEditMode() {
    if (confirm('Batalkan perubahan? Data yang telah diubah tidak akan disimpan.')) {
        isEditMode = false;
        $('.edit-mode').hide();
        $('.view-mode').show();
        $('#viewTitle').text('Detail Delegasi Personil');
        $('#btnToggleEdit').html('<i class="bi bi-pencil me-1"></i>Edit').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
        showToast('info', 'Perubahan dibatalkan');
    }
}

async function loadDropdownsForEdit() {
    try {
        if (typeof verifikatorList !== 'undefined' && verifikatorList.length > 0) {
            populateEditDropdown('#edit_verifikator_tuk_id', verifikatorList, 'verifikator');
            populateEditDropdown('#edit_observer_id', observerList, 'observer');
            populateEditDropdown('#edit_asesor_id', asesorList, 'asesor');
        } else {
            const response = await fetch('/admin/users/by-roles', {
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            const data = await response.json();

            if (data.success) {
                originalDropdownData.verifikator = data.verifikator || [];
                originalDropdownData.observer = data.observer || [];
                originalDropdownData.asesor = data.asesor || [];

                populateEditDropdown('#edit_verifikator_tuk_id', originalDropdownData.verifikator, 'verifikator');
                populateEditDropdown('#edit_observer_id', originalDropdownData.observer, 'observer');
                populateEditDropdown('#edit_asesor_id', originalDropdownData.asesor, 'asesor');
            }
        }
    } catch (error) {
        console.error('Error loading dropdowns:', error);
        showToast('error', 'Gagal memuat data dropdown');
    }
}

function populateEditDropdown(selector, users, type) {
    const $select = $(selector);
    const currentValue = $select.find('option:selected').val();
    $select.empty();

    users.forEach(user => {
        const selected = user.id == currentValue ? 'selected' : '';
        let dataAttr = type === 'asesor' ? `data-met="${user.id_number}"` : `data-nik="${user.id_number}"`;
        $select.append(`<option value="${user.id}" ${dataAttr} ${selected}>${user.name}</option>`);
    });
}

function submitEditDelegasi() {
    const delegasiId = $('#edit_delegasi_id').val();
    const submitBtn = $('#btnSaveEdit');
    const originalText = submitBtn.html();

    if (!$('#edit_verifikator_tuk_id').val() || !$('#edit_observer_id').val() || !$('#edit_asesor_id').val()) {
        showToast('error', 'Verifikator, Observer, dan Asesor harus dipilih');
        return;
    }

    if (!$('#edit_tanggal_pelaksanaan').val() || !$('#edit_waktu_mulai').val()) {
        showToast('error', 'Tanggal dan waktu pelaksanaan harus diisi');
        return;
    }

    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    const formData = {
        jenis_ujian: $('input[name="edit_jenis_ujian"]:checked').val(),
        verifikator_tuk_id: $('#edit_verifikator_tuk_id').val(),
        verifikator_nik: $('#edit_verifikator_nik').val(),
        verifikator_spt_date: $('#edit_verifikator_spt_date').val(),
        observer_id: $('#edit_observer_id').val(),
        observer_nik: $('#edit_observer_nik').val(),
        observer_spt_date: $('#edit_observer_spt_date').val(),
        asesor_id: $('#edit_asesor_id').val(),
        asesor_met: $('#edit_asesor_met').val(),
        asesor_spt_date: $('#edit_asesor_spt_date').val(),
        tanggal_pelaksanaan_asesmen: $('#edit_tanggal_pelaksanaan').val(),
        waktu_mulai: $('#edit_waktu_mulai').val(),
        notes: $('#edit_notes').val(),
        _method: 'PUT'
    };

    fetch(`/admin/delegasi-personil/${delegasiId}`, {
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
            showToast('success', data.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('delegasiPersonilModal'));
            if (modal) modal.hide();
            refreshSewaktuTable();
            refreshMandiriTable();
        } else {
            showToast('error', data.error || 'Gagal menyimpan perubahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error: ' + error.message);
    })
    .finally(() => {
        submitBtn.prop('disabled', false).html(originalText);
    });
}

function confirmDeleteDelegasi(delegasiId) {
    if (!confirm('Apakah Anda yakin ingin menghapus delegasi ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
        return;
    }

    fetch(`/admin/delegasi-personil/${delegasiId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('delegasiPersonilModal'));
            if (modal) modal.hide();
            refreshSewaktuTable();
            refreshMandiriTable();
        } else {
            showToast('error', data.error || 'Gagal menghapus delegasi');
        }
    })
    .catch(error => {
        showToast('error', 'Error: ' + error.message);
    });
}

function viewDelegasi(delegasiId) {
    const modal = new bootstrap.Modal(document.getElementById('delegasiPersonilModal'));

    $('#delegasiPersonilModal .modal-body').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Memuat detail delegasi...</p>
        </div>
    `);

    modal.show();
    isEditMode = false;

    fetch(`/admin/delegasi-personil/${delegasiId}/view`)
        .then(response => response.text())
        .then(html => {
            $('#delegasiPersonilModal .modal-body').html(html);
        })
        .catch(error => {
            $('#delegasiPersonilModal .modal-body').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading delegasi: ${error.message}
                </div>
            `);
        });
}

// Wrapper functions
function delegasiTuk(tukRequestId) {
    openDelegasiModalSewaktu(tukRequestId);
}

function delegasiAsesor(apl01Id) {
    openDelegasiModalMandiri(apl01Id);
}

// Setup Edit Mode Handlers
$(document).ready(function() {
    $(document).on('change', '#edit_verifikator_tuk_id', function() {
        const nik = $(this).find('option:selected').data('nik');
        $('#edit_verifikator_nik').val(nik || '');
    });

    $(document).on('change', '#edit_observer_id', function() {
        const nik = $(this).find('option:selected').data('nik');
        $('#edit_observer_nik').val(nik || '');
    });

    $(document).on('change', '#edit_asesor_id', function() {
        const met = $(this).find('option:selected').data('met');
        $('#edit_asesor_met').val(met || '');
    });

    $(document).on('change', '#edit_tanggal_pelaksanaan', function() {
        const tanggal = $(this).val();
        if (tanggal && isEditMode) {
            $('#edit_verifikator_spt_date, #edit_observer_spt_date, #edit_asesor_spt_date').val(tanggal);
            showToast('info', 'Tanggal SPT otomatis disesuaikan');
        }
    });

    $(document).on('submit', '#delegasiEditForm', function(e) {
        e.preventDefault();
        submitEditDelegasi();
    });
});