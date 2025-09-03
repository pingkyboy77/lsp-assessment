{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="main-card">
    <div class="card-header-custom">
        <h5 class="mb-1 text-dark fw-bold">
            <i class="bi bi-pencil-square me-2"></i>Edit User
        </h5>
        <p class="mb-0 text-muted">Update user details and roles</p>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert-danger-custom mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li><i class="bi bi-exclamation-triangle me-1"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="m-3">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
            </div>

            {{-- Id Number untuk Asesor --}}
            <div class="mb-3" id="id_number" >
                <label for="id_number" class="form-label fw-semibold">
                    ID Number (NIK / PASPOR / No MET) <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="id_number" name="id_number"
                    value="{{ old('id_number', $user->id_number) }}" placeholder="Enter ID Number">
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email', $user->email) }}" placeholder="Enter email address" required>
            </div>

            {{-- Password (opsional) --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password
                    <small class="text-muted">(Leave blank to keep current)</small>
                </label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Enter new password">
            </div>

            {{-- Confirm Password --}}
            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="Re-enter new password">
            </div>

            {{-- Roles (Multi-select with Select2) --}}
            <div class="mb-3">
                <label for="roles" class="form-label fw-semibold">Assign Roles</label>
                <select name="roles[]" id="roles" class="form-select" multiple required>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ in_array($role, old('roles', $userRole)) ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Search and select multiple roles</small>
            </div>

            {{-- Lembaga Pelatihan --}}
            <div class="mb-3" id="companyField" 
                style="{{ in_array('lembagaPelatihan', old('roles', $userRole)) ? '' : 'display:none;' }}">
                <label for="company" class="form-label fw-semibold">
                    Lembaga Pelatihan <span class="text-danger">*</span>
                </label>
                <select name="company" id="company" class="form-select">
                    <option value="">-- Pilih Lembaga --</option>
                    @foreach ($lembagas as $lp)
                        <option value="{{ $lp->id }}" {{ old('company', $user->company) == $lp->id ? 'selected' : '' }}>
                            {{ $lp->id }} - {{ $lp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            

            {{-- Buttons --}}
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary-custom me-2">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
{{-- Include Select2 CSS --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />

<style>
/* Custom Select2 styling to match your form */
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    min-height: 38px !important;
    padding: 6px 12px !important;
    font-size: 1rem;
    background-color: #fff !important;
}

.select2-container--default .select2-selection--multiple:focus-within {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    outline: 0 !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #0d6efd !important;
    border: 1px solid #0d6efd !important;
    color: white !important;
    border-radius: 0.25rem !important;
    padding: 2px 8px !important;
    font-size: 0.875rem;
    margin-top: 2px !important;
    margin-right: 5px !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: white !important;
    margin-right: 6px !important;
    margin-left: 6px !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #ffc107 !important;
}

.select2-dropdown {
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    border-top: none !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #0d6efd !important;
    color: white !important;
}

.select2-container--default .select2-results__option--selected {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

.select2-container {
    width: 100% !important;
}

.select2-selection__placeholder {
    color: #6c757d !important;
    font-style: italic;
}

.select2-search--inline .select2-search__field {
    margin-top: 0 !important;
    padding: 4px 0 !important;
    font-size: 1rem !important;
}

/* Loading state */
.select2-results__option[role=status] {
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
{{-- Include Select2 JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Wait for Select2 to be fully loaded
    function initializeSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            console.log('Select2 loaded successfully');
            
            // Initialize Select2 for roles
            $('#roles').select2({
                placeholder: 'Search and select roles...',
                allowClear: false,
                closeOnSelect: false,
                tags: false,
                width: '100%',
                theme: 'default',
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(state) {
                    if (!state.id) return state.text;
                    return $('<span>' + state.text + '</span>');
                },
                templateSelection: function(state) {
                    if (!state.id) return state.text;
                    return $('<span>' + state.text + '</span>');
                }
            });

            // Initialize role field toggle functionality
            toggleRoleFields();
            
            // Bind change event
            $('#roles').on('change', function(e) {
                toggleRoleFields();
            });
            
        } else {
            console.error('Select2 not loaded yet, retrying in 100ms...');
            setTimeout(initializeSelect2, 100);
        }
    }

    function toggleRoleFields() {
        let selectedRoles = $('#roles').val() || [];
        console.log('Selected roles:', selectedRoles);
        
        // Show/Hide Company field for lembagaPelatihan role
        if (selectedRoles.includes('lembagaPelatihan')) {
            $('#companyField').show();
            $('#company').attr('required', true);
        } else {
            $('#companyField').hide();
            $('#company').attr('required', false).val('');
        }
    
    }

    // Initialize Select2 when document is ready
    initializeSelect2();
});
</script>
@endpush