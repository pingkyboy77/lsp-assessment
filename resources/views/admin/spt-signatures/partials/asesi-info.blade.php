{{-- @dd($delegasi->apl01); --}}

{{-- FILE 1: resources/views/admin/spt-signatures/partials/asesi-info.blade.php --}}
<div class="asesi-info">
    <div class="fw-bold text-dark mb-1">
        {{ $delegasi->apl01->nama_lengkap ?? '-' }}
    </div>
    <small class="text-muted">
        <i class="bi bi-card-text me-1"></i>NIK: {{ $delegasi->asesi->id_number ?? '-' }}
    </small>
</div>