@extends('layouts.admin')

@section('content')
    <div class="main-card">
        <div class="card-header-custom">
            <h5 class="mb-1 text-dark fw-bold">
                <i class="bi bi-pencil-square me-2"></i>Edit User
            </h5>
            <p class="mb-0 text-muted">Update roles</p>
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
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="m-3">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" class="form-control" name="name" value="{{ $role->name }}" required>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary-custom me-2">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
