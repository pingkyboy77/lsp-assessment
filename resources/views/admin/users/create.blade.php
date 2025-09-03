{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="main-card">
    <div class="card-header-custom">
        <h5 class="mb-1 text-dark fw-bold">
            <i class="bi bi-plus-circle me-2"></i>Create New User
        </h5>
        <p class="mb-0 text-muted">Add a new user and assign roles</p>
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

        <form action="{{ route('admin.users.store') }}" method="POST" class="m-3">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                    placeholder="Enter full name" required>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                    placeholder="Enter email address" required>
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password"
                    required>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="Re-enter password" required>
            </div>

            {{-- Roles (Multi-select with Select2) --}}
            <div class="mb-3">
                <label for="roles" class="form-label fw-semibold">Assign Roles</label>
                <select name="roles[]" id="roles" class="form-select" multiple required>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ in_array($role, old('roles', [])) ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Search and select multiple roles</small>
            </div>

            {{-- Lembaga Pelatihan (Hidden by default) --}}
            <div class="mb-3" id="companyField" style="{{ in_array('lembagaPelatihan', old('roles', [])) ? '' : 'display:none;' }}">
                <label for="company" class="form-label fw-semibold">
                    Lembaga Pelatihan <span class="text-danger">*</span>
                </label>
                <select name="company" id="company" class="form-select">
                    <option value="">-- Pilih Lembaga --</option>
                    @foreach ($lembagas as $lp)
                        <option value="{{ $lp->id }}" {{ old('company') == $lp->id ? 'selected' : '' }}>
                            {{ $lp->id }} - {{ $lp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- No MET untuk Asesor (Hidden by default) --}}
            <div class="mb-3" id="noMetField" style="{{ in_array('asesor', old('roles', [])) ? '' : 'display:none;' }}">
                <label for="no_met" class="form-label fw-semibold">
                    Nomor MET <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="no_met" name="no_met" value="{{ old('no_met') }}"
                    placeholder="Enter MET Number">
                <small class="form-text text-muted">Required for Assessor role</small>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary-custom me-2">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Save User
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

/* Empty state */
.select2-selection__rendered {
    padding-left: 0 !important;
}

.select2-selection__rendered .select2-selection__placeholder {
    margin-top: 4px !important;
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
                console.log('Roles changed:', $(this).val());
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
            console.log('Showing company field');
            $('#companyField').show();
            $('#company').attr('required', true);
        } else {
            console.log('Hiding company field');
            $('#companyField').hide();
            $('#company').attr('required', false).val('');
        }

        // Show/Hide No MET field for asesor role
        if (selectedRoles.includes('asesor')) {
            console.log('Showing no_met field');
            $('#noMetField').show();
            $('#no_met').attr('required', true);
        } else {
            console.log('Hiding no_met field');
            $('#noMetField').hide();
            $('#no_met').attr('required', false).val('');
        }
    }

    // Initialize Select2 when document is ready
    initializeSelect2();

    // Form validation enhancement
    $('form').on('submit', function(e) {
        let selectedRoles = $('#roles').val() || [];
        
        // Check if lembagaPelatihan role is selected but no company is selected
        if (selectedRoles.includes('lembagaPelatihan') && !$('#company').val()) {
            e.preventDefault();
            alert('Please select a Lembaga Pelatihan for the selected role.');
            $('#company').focus();
            return false;
        }
        
        // Check if asesor role is selected but no MET number is provided
        if (selectedRoles.includes('asesor') && !$('#no_met').val().trim()) {
            e.preventDefault();
            alert('Please enter MET Number for the Assessor role.');
            $('#no_met').focus();
            return false;
        }
    });

    // Handle old() values on page load (for form validation errors)
    @if(old('roles'))
        // If there are old values (form was submitted with errors), ensure fields are shown
        setTimeout(function() {
            toggleRoleFields();
        }, 500);
    @endif
});
</script>
@endpush