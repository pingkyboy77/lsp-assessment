@extends('layouts.admin')

@section('title', 'Role Management')


@section('content')

    <!-- Main Content Card -->
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
                        <i class="bi bi-table me-2"></i>Role Management
                    </h5>
                    <p class="mb-0 text-muted">Manage and organize system roles</p>
                </div>
                <div>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary-custom text-light">
                        <i class="bi bi-plus-circle me-2 text-light"></i>Add New Role
                    </a>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container position-relative">
            <div class="loading-overlay d-none" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="roleTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 5%">
                                <i class="bi bi-hash"></i>
                            </th>
                            <th>
                                <i class="bi bi-person-badge me-2"></i>Role Name
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
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#roleTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('admin.roles.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold text-muted'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });
        });
    </script>
@endpush
