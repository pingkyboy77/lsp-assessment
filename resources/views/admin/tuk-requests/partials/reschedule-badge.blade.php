{{-- resources/views/admin/tuk-requests/partials/reschedule-badge.blade.php --}}
@if($item->is_rescheduled && $item->rescheduled_at)
<div class="reschedule-info mt-2">
    <div class="alert alert-warning alert-sm mb-0" role="alert">
        <div class="d-flex align-items-start">
            <i class="bi bi-calendar-x me-2 mt-1"></i>
            <div class="flex-grow-1">
                <strong class="d-block mb-1">Rescheduled</strong>
                <small class="d-block text-muted mb-1">
                    <i class="bi bi-clock me-1"></i>
                    {{ $item->rescheduled_at->format('d F Y H:i') }}
                </small>
                @if($item->rescheduledBy)
                <small class="d-block text-muted mb-1">
                    <i class="bi bi-person me-1"></i>
                    oleh: {{ $item->rescheduledBy->name }}
                </small>
                @endif
                @if($item->reschedule_reason)
                <small class="d-block text-muted">
                    <i class="bi bi-chat-left-text me-1"></i>
                    {{ Str::limit($item->reschedule_reason, 100) }}
                </small>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .reschedule-info {
        border-left: 3px solid #ffc107;
        padding-left: 0.5rem;
    }
</style>
@endif