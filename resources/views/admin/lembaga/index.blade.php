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
                        <i class="bi bi-building-fill me-2"></i>Lembaga Pelatihan
                    </h5>
                    <p class="mb-0 text-muted">Manage and organize training institutions</p>
                </div>
                <div>
                    <a href="{{ route('admin.lembaga.create') }}" class=" btn-primary-custom btn-sm text-light">
                        <i class="bi bi-plus-circle me-2 text-light"></i>Add New Lembaga
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
                    <table class="table table-hover" id="lembagaTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%">
                                    <i class="bi bi-hash"></i>
                                </th>
                                <th>
                                    <i class="bi bi-upc-scan me-2"></i>ID Lembaga
                                </th>
                                <th>
                                    <i class="bi bi-building me-2"></i>Name Lembaga
                                </th>
                                <th>
                                    <i class="bi bi-person-plus me-2"></i>Created By
                                </th>
                                <th>
                                    <i class="bi bi-person-check me-2"></i>Updated By
                                </th>
                                <th style="width: 12%">
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
                $('#lembagaTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.lembaga.index') }}',
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center fw-bold text-muted'
                        },
                        { data: 'id', name: 'id' },
                        { data: 'name', name: 'name' },
                        { data: 'creator', name: 'creator' },
                        { data: 'updater', name: 'updater' },
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
