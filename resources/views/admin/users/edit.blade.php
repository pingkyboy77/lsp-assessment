{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="main-card">
        <div class="card-header-custom">
            <h5 class="mb-1 text-dark fw-bold">
                <i class="bi bi-pencil-square me-2"></i>Edit User
            </h5>
            <p class="mb-0 text-muted">Update user details and assigned roles</p>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
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

                <div class="row">
                    {{-- Left Column (User Details) --}}
                    <div class="col-md-6">
                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $user->email) }}" placeholder="Enter email address" required>
                        </div>

                        <!-- ID Number -->
                        <div class="mb-3">
                            <label for="id_number" class="form-label fw-semibold">
                                {{ __('ID Number (NIK / PASPOR / No MET)') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="id_number" name="id_number"
                                class="form-control @error('id_number') is-invalid @enderror"
                                value="{{ old('id_number', $user->id_number ?? '') }}"
                                placeholder="Enter 16-digit ID number" required minlength="16" maxlength="16"
                                pattern="\d{16}" inputmode="numeric"
                                oninput="this.value = this.value.replace(/\D/g, '').slice(0, 16)" autocomplete="off">
                            @error('id_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Enter new password">
                            <small class="form-text text-muted">Leave blank if you don't want to change the password</small>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                Confirm Password
                            </label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="Re-enter new password">
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

                    {{-- Right Column (Roles and Lembaga) --}}
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
                                            {{ in_array($role, old('roles', $userRoles)) ? 'checked' : '' }}>
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
                        {{-- [PERBAIKAN] Mengganti id container menjadi 'companyContainer' dan menggunakan $user->company untuk old value --}}
                        <div class="mb-3" id="companyContainer"
                            style="{{ in_array('lembagaPelatihan', old('roles', $userRoles)) ? '' : 'display:none;' }}">
                            <label for="company" class="form-label fw-semibold">
                                Lembaga Pelatihan <span class="text-danger">*</span>
                            </label>
                            {{-- [PERBAIKAN] Mengganti name dan id input menjadi 'company' --}}
                            <select name="company" id="company" class="form-select">
                                <option value="">-- Pilih Lembaga Pelatihan --</option>
                                @foreach ($lembagas as $lp)
                                    {{-- [PERBAIKAN] Menggunakan $user->company untuk cek selected --}}
                                    <option value="{{ $lp->id }}"
                                        {{ old('company', $user->company) == $lp->id ? 'selected' : '' }}>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update User
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

        .form-check-label {
            cursor: pointer;
        }

        .role-info {
            min-height: 60px;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const roleDescriptions = {
                'superadmin': 'Full system access and management',
                'admin': 'Administrative access to manage users and settings',
                'lembagaPelatihan': 'Training institution representative',
                'asesor': 'Assessment personnel with evaluation rights',
                'observer': 'Assessment observer with monitoring access',
                'asesi': 'Assessment participant/candidate',
                'verifikator': 'Document and process verification personnel'
            };

            function toggleRoleFields() {
                let selectedRoles = [];
                $('input[name="roles[]"]:checked').each(function() {
                    selectedRoles.push($(this).val());
                });

                // [PERBAIKAN] Mengganti '#lembagaContainer' menjadi '#companyContainer' dan '#lembaga_pelatihan_id' menjadi '#company'
                if (selectedRoles.includes('lembagaPelatihan')) {
                    $('#companyContainer').show();
                    $('#company').attr('required', true);
                } else {
                    $('#companyContainer').hide();
                    $('#company').attr('required', false).val('');
                }

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

            $('input[name="roles[]"]').on('change', toggleRoleFields);

            // Initialize on page load
            toggleRoleFields();
        });
    </script>
@endpush
