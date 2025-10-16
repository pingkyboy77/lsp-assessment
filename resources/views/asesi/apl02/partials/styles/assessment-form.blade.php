/* resources/views/asesi/apl02/partials/styles/assessment-form.blade.php */
<style>
    /* Main card styling */
    .main-card {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Custom checkbox styling for evidence selection */
    .custom-check {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid #e9ecef;
    }

    .custom-check:hover {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }

    .custom-check input:checked + .flex-grow-1,
    .custom-check:has(input:checked) {
        border-color: #0d6efd;
        background-color: #e7f3ff;
    }

    /* Evidence status badges */
    .evidence-status .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    /* Portfolio item styling */
    .portfolio-item {
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .portfolio-item:hover {
        border-color: #0d6efd !important;
        background: #f8f9fa;
    }

    /* File info styling */
    .selected-file-info {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 0.25rem;
        padding: 0.5rem;
        margin-top: 0.5rem;
    }

    .signature-display {
            max-width: 200px;
            max-height: 100px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background: white;
        }

    /* Signature pad styling */
    .signature-pad-wrapper {
        background: #fff;
        border: 2px dashed #dee2e6;
        transition: border-color 0.3s ease;
    }

    .signature-pad-wrapper:hover {
        border-color: #0d6efd;
    }

    .signature-pad-wrapper.active {
        border-color: #0d6efd;
        border-style: solid;
    }

    #signature-placeholder {
        pointer-events: none;
        user-select: none;
    }

    #signature-placeholder.hidden {
        display: none !important;
    }

    .signature-image {
        max-width: 100%;
        height: auto;
    }

    /* Assessment form styling */
    .element-assessment {
        background: #fafafa;
        transition: all 0.3s ease;
    }

    .element-assessment:hover {
        background: #f5f5f5;
    }

    .criteria-item {
        padding: 0.25rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .criteria-item:last-child {
        border-bottom: none;
    }

    /* Card header custom styling */
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 0.375rem 0.375rem 0 0;
    }

    /* Alert styling */
    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Button enhancements */
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
    }

    /* Loading modal styling */
    #loadingModal .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* File preview modal styling */
    #filePreviewModal .modal-body {
        max-height: 80vh;
        overflow-y: auto;
    }

    #filePreviewContent img {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        transition: transform 0.2s ease;
    }

    #filePreviewContent img:hover {
        transform: scale(1.02);
    }

    #filePreviewContent iframe {
        width: 100%;
        height: 600px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    /* File type icons */
    .file-icon-large {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .file-info-grid {
        background: #f8f9fa;
        border-radius: 0.375rem;
        padding: 1rem;
        margin: 1rem 0;
    }

    .file-info-grid .row {
        margin-bottom: 0.5rem;
    }

    .file-info-grid .row:last-child {
        margin-bottom: 0;
    }

    /* Upload progress styling */
    .upload-progress {
        display: none;
        margin-top: 0.5rem;
    }

    .upload-progress.show {
        display: block;
    }

    .upload-progress .progress {
        height: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .upload-progress-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* Individual upload button styling */
    .upload-evidence-individual {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        transition: all 0.3s ease;
    }

    .upload-evidence-individual:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    /* File selection feedback */
    .file-selected {
        border-color: #198754 !important;
        background-color: #f8fff9 !important;
    }

    .file-selected + .selected-file-info {
        background: #d1e7dd;
        border-color: #badbcc;
    }

    /* Delete confirmation modal */
    #deleteConfirmModal .modal-body {
        padding: 2rem;
    }

    #deleteConfirmModal .display-1 {
        color: #ffc107;
    }

    /* Responsive adjustments for preview modal */
    @media (max-width: 768px) {
        #filePreviewModal .modal-dialog {
            margin: 0.5rem;
        }
        
        #filePreviewModal .modal-body {
            padding: 1rem;
        }
        
        .file-info-grid {
            font-size: 0.875rem;
        }
        
        .file-icon-large {
            font-size: 3rem;
        }
    }

    /* Loading states */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 0.375rem;
    }

    .loading-overlay.show {
        display: flex;
    }

    .loading-spinner {
        width: 2rem;
        height: 2rem;
        border: 0.25rem solid #f3f3f3;
        border-top: 0.25rem solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Evidence status improvements */
    .evidence-status-new {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border: none;
        font-weight: 600;
    }

    .evidence-status-uploaded {
        background: linear-gradient(45deg, #17a2b8, #138496);
        color: white;
        border: none;
        font-weight: 600;
    }

    /* Button improvements */
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
        border-radius: 0.25rem;
        margin: 0 1px;
    }

    .btn-group-sm .btn:first-child {
        margin-left: 0;
    }

    .btn-group-sm .btn:last-child {
        margin-right: 0;
    }

    /* Hover effects for evidence items */
    .existing-evidence {
        transition: all 0.3s ease;
    }

    .existing-evidence:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .existing-evidence:hover .btn-group-sm .btn {
        transform: scale(1.05);
    }

    /* Unit evidence section */
    .unit-evidence-section {
        background: #f8f9fa;
        border-radius: 0.375rem;
        padding: 1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .main-card {
            margin: 0 1rem;
        }
        
        .card {
            margin: 0.5rem 0 !important;
        }
        
        .signature-pad-wrapper {
            height: 150px !important;
        }
        
        .portfolio-item {
            margin-bottom: 1rem;
        }
        
        .btn-group-sm .btn {
            padding: 0.375rem 0.75rem;
            margin-bottom: 0.25rem;
        }
    }

    /* Custom scrollbar */
    .evidence-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .evidence-list::-webkit-scrollbar {
        width: 6px;
    }

    .evidence-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .evidence-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .evidence-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Animation for alerts */
    .alert {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Status indicators */
    .status-indicator {
        position: relative;
    }

    .status-indicator::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border: 2px solid transparent;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }

    .status-indicator.completed::before {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }

    /* Progress indication */
    .progress-mini {
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }

    .progress-mini .progress-bar {
        transition: width 0.6s ease;
    }

    /* Form validation styling */
    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    /* Success states */
    .is-valid {
        border-color: #198754;
    }

    .valid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #198754;
    }
</style>