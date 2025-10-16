<div class="modal fade" id="documentViewerModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentTitle">Document Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="documentFrame" style="width: 100%; height: 80vh; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<style>
    .elemen-card {
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        margin-bottom: 0.75rem;
        background: white;
        transition: all 0.2s;
    }

    .elemen-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .elemen-card-body {
        padding: 1rem;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-kompeten {
        background: #D1FAE5;
        color: #065F46;
    }

    .status-belum-kompeten {
        background: #FEE2E2;
        color: #991B1B;
    }

    .status-belum-dinilai {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .badge-bukti {
        background: #DBEAFE;
        color: #1E40AF;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .portfolio-icon {
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    .portfolio-name {
        flex: 1;
        font-weight: 500;
        color: var(--gray-900);
    }

    .list-group-item {
        border: 1px solid var(--gray-200);
        padding: 0.75rem 1rem;
        background: var(--gray-50);
        color: var(--gray-700);
    }

    .kelompok-assignment {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 0.75rem;
    }

    .p-level-badge {
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .p-level-0 {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .p-level-active {
        background: var(--primary-color);
        color: white;
    }

    .mapa-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .mapa-description {
        color: var(--gray-600);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .mapa-badge {
        padding: 0.4rem 1rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .mapa-badge-langsung {
        background: #D1FAE5;
        color: #065F46;
    }

    .mapa-badge-tidak-langsung {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .document-item h6 {
        color: var(--gray-900);
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .document-item .text-muted {
        color: var(--gray-500) !important;
    }

    .info-table td:last-child {
        color: var(--gray-900);
    }
</style>