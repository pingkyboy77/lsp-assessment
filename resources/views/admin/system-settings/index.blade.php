@extends('layouts.admin')

@section('title', 'System Settings')

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
                        <i class="bi bi-gear me-2"></i>System Settings
                    </h5>
                    <p class="mb-0 text-muted">Manage system configuration and settings</p>
                </div>
                <div>
                    <a href="{{ route('admin.system-settings.create') }}" class="btn btn-primary-custom text-light">
                        <i class="bi bi-plus-circle me-2 text-light"></i>Add New Setting
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
                <table class="table table-hover table-bordered" id="settingsTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 5%">
                                <i class="bi bi-hash"></i>
                            </th>
                            <th>
                                <i class="bi bi-key me-2"></i>Key
                            </th>
                            <th>
                                <i class="bi bi-file-text me-2"></i>Value
                            </th>
                            <th style="width: 10%">
                                <i class="bi bi-tag me-2"></i>Type
                            </th>
                            <th style="width: 12%">
                                <i class="bi bi-folder me-2"></i>Group
                            </th>
                            <th style="width: 10%">
                                <i class="bi bi-toggle-on me-2"></i>Status
                            </th>
                            <th style="width: 15%">
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
            $('#settingsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('admin.system-settings.index') }}',
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold text-muted'
                    },
                    {
                        data: 'key',
                        name: 'key',
                        render: function(data, type, row) {
                            return '<code class="bg-light px-2 py-1 rounded">' + data + '</code>';
                        }
                    },
                    {
                        data: 'formatted_value',
                        name: 'value',
                        className: 'text-wrap'
                    },
                    {
                        data: 'type_badge',
                        name: 'type',
                        className: 'text-center'
                    },
                    {
                        data: 'group',
                        name: 'group',
                        render: function(data, type, row) {
                            return data ? '<span class="badge bg-secondary">' + data + '</span>' : '-';
                        }
                    },
                    {
                        data: 'status_badge',
                        name: 'is_active',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [[1, 'asc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
            });
        });

        function deleteSetting(id) {
            if (confirm('Are you sure you want to delete this setting?')) {
                $.ajax({
                    url: '{{ route("admin.system-settings.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#settingsTable').DataTable().ajax.reload();
                            
                            // Show success message
                            $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                '<i class="bi bi-check-circle-fill me-2"></i>' + response.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                                '</div>').prependTo('.main-card .card-header-custom');
                        }
                    },
                    error: function() {
                        alert('Error deleting setting');
                    }
                });
            }
        }
    </script>
@endpush