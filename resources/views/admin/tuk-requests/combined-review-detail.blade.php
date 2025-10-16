{{-- resources/views/admin/tuk-requests/combined-review-detail.blade.php --}}

<div class="combined-review-container">
    <!-- Navigation Pills -->
    <div class="review-tabs-wrapper mb-4">
        <ul class="nav nav-pills review-tabs" id="reviewTabs" role="tablist">
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link active fw-semibold w-100 py-3" id="apl01-tab" data-bs-toggle="pill"
                    data-bs-target="#apl01-content" type="button" role="tab">
                    <i class="bi bi-file-earmark-text me-2"></i>APL 01
                </button>
            </li>
            @if ($apl01->apl02)
                <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link fw-semibold w-100 py-3" id="apl02-tab" data-bs-toggle="pill"
                        data-bs-target="#apl02-content" type="button" role="tab">
                        <i class="bi bi-clipboard-check me-2"></i>APL 02
                    </button>
                </li>
            @endif
        </ul>
    </div>
    <!-- Tab Content -->
    <div class="tab-content" id="reviewTabsContent">
        <!-- APL 01 Content -->
        <div class="tab-pane fade show active" id="apl01-content" role="tabpanel">
            @include('admin.tuk-requests.partials.apl01-content', ['apl' => $apl01])
            @include('admin.tuk-requests.partials.user-documents-section', ['apl' => $apl01])
        </div>

        <!-- APL 02 Content -->
        @if ($apl01->apl02)
            <div class="tab-pane fade" id="apl02-content" role="tabpanel">
                @include('admin.tuk-requests.partials.apl02-content', ['apl02' => $apl01->apl02])
            </div>
        @endif
    </div>

    <!-- Hidden data for JS -->
    <input type="hidden" id="apl01Id" value="{{ $apl01->id }}">
    <input type="hidden" id="apl02Id" value="{{ $apl01->apl02->id ?? '' }}">
    <input type="hidden" id="apl01Status" value="{{ $completionStatus['apl01']['status'] }}">
    <input type="hidden" id="apl02Status" value="{{ $completionStatus['apl02']['status'] ?? '' }}">
</div>

<style>
    .combined-review-container {
        max-height: 65vh;
    }

    .review-tabs {
        display: flex;
        gap: 0.5rem;
    }

    .review-tabs .nav-item {
        flex: 1;
    }

    .review-tabs .nav-link {
        width: 100%;
        text-align: center;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-weight: 500;
        color: #6c757d;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        font-size: 0.9rem;
    }

    .review-tabs .nav-link.active {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        border: none;
    }

    .review-tabs .nav-link:hover:not(.active) {
        background: #e9ecef;
        color: #0d6efd;
    }

    .review-tabs .nav-link i {
        font-size: 1rem;
    }

    .review-tabs .nav-link .badge {
        font-size: 0.7rem;
        padding: 0.25em 0.5em;
    }
</style>
