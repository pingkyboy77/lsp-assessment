{{-- resources/views/admin/mapa/partials/table-rows.blade.php --}}
@forelse ($mapaList as $index => $mapa)
    <tr>
        <td class="text-center">
            @if ($mapa->status === 'submitted' || $mapa->status === 'approved')
                <input type="checkbox" class="mapa-checkbox item-checkbox" data-id="{{ $mapa->id }}"
                    data-nomor="{{ $mapa->nomor_mapa }}" data-asesi="{{ $mapa->delegasi->asesi->name ?? 'N/A' }}"
                    data-skema="{{ $mapa->certificationScheme->nama ?? 'N/A' }}">
            @else
                <span class="text-muted" title="Status {{ $mapa->status }} tidak dapat diseleksi">
                    <i class="bi bi-dash-circle"></i>
                </span>
            @endif
        </td>
        <td class="text-center">{{ $mapaList->firstItem() + $index }}</td>
        <td>
            <div class="fw-semibold" style="color: var(--gray-900);">
                {{ $mapa->nomor_mapa }}
            </div>
            <small class="text-muted">{{ $mapa->mapa_code }}</small>
        </td>
        <td>
            <div class="fw-semibold" style="color: var(--gray-900); font-size: 0.9rem;">
                {{ Str::limit($mapa->certificationScheme->nama ?? 'N/A', 30) }}
            </div>
            <small class="text-muted">{{ $mapa->certificationScheme->code_1 ?? '-' }}</small>
        </td>
        <td>
            <div class="fw-semibold">{{ $mapa->delegasi->asesi->name ?? 'N/A' }}</div>
            <small class="text-muted">{{ $mapa->delegasi->asesi->email ?? 'N/A' }}</small>
        </td>
        <td>
            <div class="fw-semibold" style="color: var(--gray-900);">
                {{ $mapa->asesor->name ?? 'N/A' }}
            </div>
            <small class="text-muted">
                <i class="bi bi-clock me-1"></i>{{ $mapa->submitted_at ? $mapa->submitted_at->format('d/m/Y') : '-' }}
            </small>
        </td>
        <td>
            @if ($mapa->status === 'submitted')
                <span class="status-badge status-submitted">
                    <i class="bi bi-send me-1"></i>Submitted
                </span>
            @elseif($mapa->status === 'approved')
                <span class="status-badge"
                    style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #FCD34D;">
                    <i class="bi bi-check-circle me-1"></i>Approved
                </span>
            @elseif($mapa->status === 'validated')
                <span class="status-badge status-approved">
                    <i class="bi bi-check-all me-1"></i>Validated
                </span>
            @elseif($mapa->status === 'rejected')
                <span class="status-badge status-rejected">
                    <i class="bi bi-x-circle-fill me-1"></i>Rejected
                </span>
            @else
                <span class="status-badge status-draft">
                    <i class="bi bi-file-earmark me-1"></i>{{ ucfirst($mapa->status) }}
                </span>
            @endif
        </td>
        <td>
            <div class="d-flex gap-1 justify-content-end">
                {{-- Review Button --}}
                <button onclick="showReviewModal({{ $mapa->id }})" 
                    class="btn btn-sm btn-view btn-outline-primary"
                    title="Review MAPA" 
                    @if (!$mapa->canBeReviewed()) disabled @endif>
                    <i class="bi bi-eye"></i>
                </button>

                {{-- Detail Button --}}
                <a href="{{ route('admin.mapa.show', $mapa->id) }}" 
                    class="btn btn-sm btn-outline-success"
                    title="Detail MAPA">
                    <i class="bi bi-file-text"></i>
                </a>

                {{-- View AK07 Button --}}
                @if($mapa->ak07)
                <a href="{{ route('admin.mapa.view-ak07', $mapa->id) }}" 
                    class="btn btn-sm btn-outline-info"
                    title="View AK07"
                    target="_blank">
                    <i class="bi bi-clipboard-check"></i>
                </a>
                @endif

                {{-- Unlock AK07 Button --}}
                @if($mapa->ak07 && $mapa->ak07->status === 'completed')
                <button onclick="unlockAk07FromIndex({{ $mapa->id }})" 
                    class="btn btn-sm btn-outline-warning"
                    title="Unlock AK07">
                    <i class="bi bi-unlock"></i>
                </button>
                @endif

                {{-- View Form Kerahasiaan Button --}}
                @if($mapa->delegasi && $mapa->delegasi->formKerahasiaan)
                <a href="{{ route('admin.mapa.view-form-kerahasiaan', $mapa->id) }}" 
                    class="btn btn-sm btn-outline-secondary"
                    title="View Form Kerahasiaan"
                    target="_blank">
                    <i class="bi bi-shield-lock"></i>
                </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color: var(--gray-700);">Tidak Ada Data</h5>
                <p class="text-muted mb-3">
                    Tidak ada MAPA yang sesuai dengan filter
                </p>
            </div>
        </td>
    </tr>
@endforelse