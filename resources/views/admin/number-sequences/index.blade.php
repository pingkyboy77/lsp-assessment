@extends('layouts.admin')
@section('title', 'Number Sequences')

@section('content')
    <div class="main-card">
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
                    <p class="mb-0 text-muted">Manage automatic number generation for various documents</p>
                </div>
                <div>
                    <a href="{{ route('admin.number-sequences.create') }}" class="btn btn-primary-custom text-light">
                        <i class="bi bi-plus-circle me-2 text-light"></i>Add New Sequence
                    </a>
                </div>
            </div>
        </div>

        <div class="table-container position-relative">
            <div class="loading-overlay d-none" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="sequencesTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 5%"><i class="bi bi-hash"></i></th>
                            <th><i class="bi bi-key me-2"></i>Sequence Key</th>
                            <th><i class="bi bi-type me-2"></i>Name</th>
                            <th><i class="bi bi-tags me-2"></i>Format Info</th>
                            <th><i class="bi bi-eye me-2"></i>Current Preview</th>
                            <th style="width: 8%"><i class="bi bi-toggle-on me-2"></i>Status</th>
                            <th style="width: 15%"><i class="bi bi-gear me-2"></i>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Test Modal -->
    <div class="modal fade" id="testModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Number Generation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="testResults"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#sequencesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('admin.number-sequences.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold text-muted'
                    },
                    {
                        data: 'sequence_key',
                        name: 'sequence_key',
                        render: function(data) {
                            return '<code class="bg-light px-2 py-1 rounded">' + data + '</code>';
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'sequence_info',
                        name: 'sequence_info',
                        className: 'text-center'
                    },
                    {
                        data: 'current_preview',
                        name: 'current_preview',
                        className: 'text-center'
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
                order: [
                    [2, 'asc']
                ],
                pageLength: 10
            });
        });

        function testSequence(id) {
            $.ajax({
                url: '{{ route('admin.number-sequences.test', ':id') }}'.replace(':id', id),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        let html = '<h6>Preview Numbers:</h6><ul class="list-group">';
                        response.previews.forEach(function(preview) {
                            html += '<li class="list-group-item"><code>' + preview + '</code></li>';
                        });
                        html += '</ul>';
                        html += '<div class="mt-3"><small class="text-muted">Current Counter: ' + response
                            .current_number + '</small></div>';

                        $('#testResults').html(html);
                        $('#testModal').modal('show');
                    }
                }
            });
        }

        function deleteSequence(id) {
            if (confirm('Are you sure you want to delete this number sequence?')) {
                $.ajax({
                    url: '{{ route('admin.number-sequences.destroy', ':id') }}'.replace(':id', id),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#sequencesTable').DataTable().ajax.reload();

                            $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                '<i class="bi bi-check-circle-fill me-2"></i>' + response.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                                '</div>').prependTo('.main-card .card-header-custom');
                        }
                    }
                });
            }
        }
    </script>
@endpush
