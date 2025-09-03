@extends('layouts.admin')

@section('content')
<div class="main-card">
    <div class="card-header-custom">
        <h5 class="mb-1 text-dark fw-bold">
            <i class="bi bi-plus-circle me-2"></i>Tambah Lembaga Pelatihan
        </h5>
        <p class="mb-0 text-muted">Tambah lembaga pelatihan baru</p>
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

        <form action="{{ route('admin.lembaga.store') }}" method="POST" class="m-3">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nama Lembaga</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Masukkan nama lembaga" required>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.lembaga.index') }}" class="btn btn-secondary-custom me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
