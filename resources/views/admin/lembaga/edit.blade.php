{{-- resources/views/admin/lembaga/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="main-card">
    <div class="card-header-custom bg-warning text-dark">
        <h5 class="mb-1 fw-bold">
            <i class="bi bi-pencil-square me-2"></i>Edit Lembaga Pelatihan
        </h5>
        <p class="mb-0 text-muted">Ubah data lembaga pelatihan</p>
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

        <form action="{{ route('admin.lembaga.update', $lembaga->id) }}" method="POST" class="m-3">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nama Lembaga</label>
                <input type="text" id="name" name="name" class="form-control" 
                    value="{{ old('name', $lembaga->name) }}" placeholder="Masukkan nama lembaga" required>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.lembaga.index') }}" class="btn btn-secondary-custom me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-warning text-dark">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
