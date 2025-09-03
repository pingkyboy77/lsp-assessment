@extends('layouts.admin')

@section('title', 'Duplikasi Unit Kompetensi')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endpush



@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

    <div class="main-card">
        <div class="card-header-custom">
            <h5>Duplikasi Unit Kompetensi</h5>
        </div>

        <div class="card-body m-3">
            <form method="POST"
                action="{{ route('admin.schemes.unit-kompetensi.duplicate.store', [$scheme->id, $unitKompetensi->id]) }}">
                @csrf
                <div class="mb-3">
                    <label for="kode_unit" class="form-label">Kode Unit Baru</label>
                    <input type="text" name="kode_unit" id="kode_unit"
                        class="form-control @error('kode_unit') is-invalid @enderror" value="{{ old('kode_unit') }}"
                        required>
                    @error('kode_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="scheme_id" class="form-label">Duplikasi ke Skema</label>
                    <select name="scheme_id" id="scheme_id" class="form-select @error('scheme_id') is-invalid @enderror"
                        required style="white-space: normal; line-height: 1.4;">
                        @foreach ($schemes as $s)
                            <option value="{{ $s->id }}"
                                {{ old('scheme_id', $scheme->id) == $s->id ? 'selected' : '' }}>
                                [{{ $s->code_1 }}] - {{ $s->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('scheme_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>



                <button type="submit" class="btn btn-primary">Simpan Duplikasi</button>
                <a href="{{ route('admin.schemes.unit-kompetensi.index', $scheme->id) }}"
                    class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#scheme_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Skema',
                allowClear: true
            });
        });
    </script>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            })
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}'
            })
        @endif
    </script>
@endpush
