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

                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-md-6">
                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name') }}" placeholder="Enter full name" required>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" placeholder="Enter email address" required>
                        </div>

                        {{-- ID Number --}}
                        <div class="mb-3">
                            <label for="id_number" class="form-label fw-medium">
                                {{ __('ID Number (NIK / PASPOR / No MET)') }} <span class="text-danger">*</span>
                            </label>
                            <input id="id_number" class="form-control @error('id_number') is-invalid @enderror"
                                type="text" name="id_number" value="{{ old('id_number') }}" required minlength="16"
                                maxlength="16" pattern="\d{16}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                autocomplete="id_number" autofocus>

                            @error('id_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Enter password" required>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="Re-enter password" required>
                        </div>
                    </div>


                    @php
                        $roleDescriptions = [
                            'superadmin' => 'Full system access and management',
                            'admin' => 'Administrative access to manage users and settings',
                            'lembagaPelatihan' => 'Training institution representative',
                            'asesor' => 'Assessment personnel with evaluation rights',
                            'observer' => 'Assessment observer with monitoring access',
                            'asesi' => 'Assessment participant/candidate',
                            'verifikator' => 'Document and process verification personnel',
                        ];
                    @endphp

                    {{-- Right Column --}}
                    <div class="col-md-6">
                        {{-- Roles --}}
                        <div class="mb-3">
                            <label for="roles" class="form-label fw-semibold">
                                Assign Roles <span class="text-danger">*</span>
                            </label>
                            <div class="role-checkbox-container border rounded p-3">
                                @foreach ($roles as $role)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                            value="{{ $role }}" id="role_{{ $role }}"
                                            {{ in_array($role, old('roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role }}">
                                            <span class="fw-semibold">{{ ucfirst($role) }}</span>
                                            <small
                                                class="text-muted d-block">{{ $roleDescriptions[$role] ?? 'User role' }}</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">Select one or more roles for this user</small>
                        </div>

                        {{-- Lembaga Pelatihan Field --}}
                        {{-- [PERBAIKAN] Mengganti id input/select menjadi 'company' untuk konsistensi DB --}}
                        <div class="mb-3" id="companyField"
                            style="{{ in_array('lembagaPelatihan', old('roles', [])) ? '' : 'display:none;' }}">
                            <label for="company" class="form-label fw-semibold">
                                Lembaga Pelatihan <span class="text-danger">*</span>
                            </label>
                            {{-- [PERBAIKAN] Memastikan name input adalah 'company' --}}
                            <select name="company" id="company" class="form-select">
                                <option value="">-- Pilih Lembaga Pelatihan --</option>
                                @foreach ($lembagas as $lp)
                                    <option value="{{ $lp->id }}" {{ old('company') == $lp->id ? 'selected' : '' }}>
                                        {{ $lp->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Required when Lembaga Pelatihan role is selected</small>
                        </div>

                        {{-- Additional Info Card --}}
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i>Role Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="role-info" id="roleInfo">
                                    <p class="text-muted mb-0"><small>Select roles to see their descriptions</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .role-checkbox-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6 !important;
        }

        .form-check {
            padding-left: 1.5em;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-input:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-check-label {
            cursor: pointer;
        }

        .role-info {
            min-height: 60px;
        }

        .role-description {
            display: block;
            margin-top: 4px;
            font-style: italic;
        }

        /* Custom scrollbar for role container */
        .role-checkbox-container::-webkit-scrollbar {
            width: 6px;
        }

        .role-checkbox-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .role-checkbox-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .role-checkbox-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .card-header h6 {
            color: #495057;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Role descriptions
            const roleDescriptions = {
                'superadmin': 'Full system access and management',
                'admin': 'Administrative access to manage users and settings',
                'lembagaPelatihan': 'Training institution representative',
                'asesor': 'Assessment personnel with evaluation rights',
                'observer': 'Assessment observer with monitoring access',
                'asesi': 'Assessment participant/candidate',
                'verifikator': 'Document and process verification personnel'
            };

            // Role field toggle functionality
            function toggleRoleFields() {
                let selectedRoles = [];
                $('input[name="roles[]"]:checked').each(function() {
                    selectedRoles.push($(this).val());
                });

                console.log('Selected roles:', selectedRoles);

                // Show/Hide Company field for lembagaPelatihan role
                // [PERBAIKAN] Memastikan ID yang digunakan di sini adalah '#companyField' dan input adalah '#company'
                if (selectedRoles.includes('lembagaPelatihan')) {
                    $('#companyField').show();
                    $('#company').attr('required', true);
                } else {
                    $('#companyField').hide();
                    $('#company').attr('required', false).val('');
                }

                // Update role info display
                updateRoleInfo(selectedRoles);
            }

            function updateRoleInfo(selectedRoles) {
                let infoHtml = '';

                if (selectedRoles.length === 0) {
                    infoHtml =
                        '<p class="text-muted mb-0"><small>Select roles to see their descriptions</small></p>';
                } else {
                    infoHtml = '<ul class="list-unstyled mb-0">';
                    selectedRoles.forEach(function(role) {
                        let description = roleDescriptions[role] || 'No description available';
                        infoHtml += `<li class="mb-2">
                    <strong class="text-primary">${role.charAt(0).toUpperCase() + role.slice(1)}:</strong>
                    <small class="text-muted d-block">${description}</small>
                </li>`;
                    });
                    infoHtml += '</ul>';
                }

                $('#roleInfo').html(infoHtml);
            }

            // Bind change event to role checkboxes
            $('input[name="roles[]"]').on('change', function() {
                toggleRoleFields();
            });

            // Initialize on page load
            toggleRoleFields();

            // Form validation enhancement
            $('form').on('submit', function(e) {
                let selectedRoles = [];
                $('input[name="roles[]"]:checked').each(function() {
                    selectedRoles.push($(this).val());
                });

                // Check if at least one role is selected
                if (selectedRoles.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one role.');
                    return false;
                }

                // Check if lembagaPelatihan role is selected but no company is selected
                if (selectedRoles.includes('lembagaPelatihan') && !$('#company').val()) {
                    e.preventDefault();
                    alert('Please select a Lembaga Pelatihan for the selected role.');
                    $('#company').focus();
                    return false;
                }
            });

            // Handle old() values on page load (for form validation errors)
            @if (old('roles'))
                setTimeout(function() {
                    toggleRoleFields();
                }, 100);
            @endif
        });
    </script>
@endpush
