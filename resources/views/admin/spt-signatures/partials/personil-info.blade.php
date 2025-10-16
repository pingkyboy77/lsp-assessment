{{-- FILE 2: resources/views/admin/spt-signatures/partials/personil-info.blade.php --}}
<div class="personil-info">
    <div class="d-flex flex-wrap gap-1">
        @if($delegasi->verifikatorTuk)
            <span class="personil-badge badge bg-primary" title="Verifikator TUK">
                <i class="bi bi-person-check me-1"></i>
                {{ Str::limit($delegasi->verifikatorTuk->name, 15) }}
            </span>
        @endif

        @if($delegasi->observer)
            <span class="personil-badge badge bg-info" title="Observer">
                <i class="bi bi-eye me-1"></i>
                {{ Str::limit($delegasi->observer->name, 15) }}
            </span>
        @endif

        @if($delegasi->asesor)
            <span class="personil-badge badge bg-success" title="Asesor">
                <i class="bi bi-clipboard-check me-1"></i>
                {{ Str::limit($delegasi->asesor->name, 15) }}
            </span>
        @endif
    </div>
</div>