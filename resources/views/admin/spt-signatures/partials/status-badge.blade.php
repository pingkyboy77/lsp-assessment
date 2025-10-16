{{-- FILE 3: resources/views/admin/spt-signatures/partials/status-badge.blade.php --}}
@if($spt && $spt->is_signed)
    <span class="badge badge-signed px-3 py-2">
        <i class="bi bi-check-circle-fill me-1"></i>
        Sudah Ditandatangani
    </span>
    <div class="mt-1">
        <small class="text-muted">
            <i class="bi bi-person me-1"></i>{{ $spt->signed_by_name }}
        </small>
        <br>
        <small class="text-muted">
            <i class="bi bi-calendar-event me-1"></i>{{ $spt->formatted_signed_at }}
        </small>
    </div>
@else
    <span class="badge badge-pending px-3 py-2">
        <i class="bi bi-clock-history me-1"></i>
        Menunggu Tanda Tangan
    </span>
    <div class="mt-1">
        <small class="text-muted">
            <i class="bi bi-calendar-event me-1"></i>{{ $delegasi->created_at->format('d/m/Y H:i') }}
        </small>
    </div>
@endif
