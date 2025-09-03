@extends('layouts.admin')

@section('content')
    <div class="main-card">
        <!-- Card Header -->
        <div class="card-header-custom">
            @if (session('success'))
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-table me-2"></i>User Management
                    </h5>
                    <p class="mb-0 text-muted">Manage and organize User</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary-custom btn-sm text-light">
                        <i class="bi bi-plus-circle me-2 text-light"></i>Add New User
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-container position-relative">
                <div class="loading-overlay d-none" id="loadingOverlay">
                    <div class="loading-spinner"></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="userTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%">
                                    <i class="bi bi-hash"></i>
                                </th>
                                <th>
                                    <i class="bi bi-person-badge me-2"></i>Name
                                </th>
                                <th>
                                    <i class="bi bi-envelope-at me-2"></i>Email
                                </th>
                                <th>
                                    <i class="bi bi-person-gear me-2"></i>Roles
                                </th>
                                <th>
                                    <i class="bi bi-building me-2"></i>Company
                                </th>
                                <th>
                                    <i class="bi bi-card-text me-2"></i>Id Number
                                </th>
                                <th style="width: 20%">
                                    <i class="bi bi-gear me-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                $('#userTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.users.index') }}',
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold text-muted'
                    },{
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'roles',
                            name: 'roles'
                        },
                        {
                            data: 'lembaga',
                            name: 'lembaga'
                        },
                        {
                            data: 'id_number',
                            name: 'id_number'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            });
        </script>
    @endpush
@endsection