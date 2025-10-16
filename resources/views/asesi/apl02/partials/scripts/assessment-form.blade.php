{{-- resources/views/asesi/apl02/partials/scripts/assessment-form.blade.php --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global Variables
        let signaturePad = null;
        let hasUnsavedChanges = false;
        let existingEvidence = @json($existingEvidence ?? []);
        let selectedFiles = {}; // Store selected files for batch upload
        let elementDocuments = {}; // Store selected documents per element: { elementId: [portfolioIds] }

        console.log('Existing evidence data:', existingEvidence);

        // Initialize Components
        initializeSignaturePad();
        initializeEventListeners();
        initializeElementDocuments(); // Initialize existing element documents

        // Initialize evidence display
        setTimeout(() => {
            updateSelectedEvidence();
        }, 100);

        /* ===================== ELEMENT DOCUMENTS INITIALIZATION ===================== */

        function initializeElementDocuments() {
            // Load existing element documents from server data
            @php
                $existingAssessmentsData = $assessmentData->flatMap(function ($unitData) {
                    return $unitData['elements']->map(function ($elementData) {
                        return [
                            'element_id' => $elementData['element']->id,
                            'assessment' => $elementData['assessment']
                                ? [
                                    'notes' => $elementData['assessment']->notes,
                                ]
                                : null,
                        ];
                    });
                });
            @endphp

            const existingAssessments = @json($existingAssessmentsData);

            existingAssessments.forEach(assessment => {
                if (assessment.assessment && assessment.assessment.notes) {
                    try {
                        let notes = assessment.assessment.notes;

                        // Handle both string and already parsed JSON
                        if (typeof notes === 'string') {
                            notes = JSON.parse(notes);
                        }

                        if (Array.isArray(notes) && notes.length > 0) {
                            const portfolioIds = notes.map(doc => doc.portfolioId || doc.portfolio_id)
                                .filter(Boolean);
                            if (portfolioIds.length > 0) {
                                elementDocuments[assessment.element_id] = portfolioIds;

                                // Check corresponding checkboxes
                                portfolioIds.forEach(portfolioId => {
                                    const checkbox = document.querySelector(
                                        `input.evidence-checkbox[data-element-id="${assessment.element_id}"][data-portfolio-id="${portfolioId}"]`
                                    );
                                    if (checkbox) {
                                        checkbox.checked = true;
                                    }
                                });
                            }
                        }
                    } catch (e) {
                        console.warn('Failed to parse element assessment notes:', e, assessment
                            .assessment.notes);
                    }
                }
            });

            console.log('Initialized element documents:', elementDocuments);
        }

        /* ===================== SIGNATURE PAD INITIALIZATION ===================== */

        function initializeSignaturePad() {
            const canvas = document.getElementById("signature-canvas");
            const newCanvas = document.getElementById("new-signature-canvas");
            const placeholder = document.getElementById("signature-placeholder");
            const newPlaceholder = document.getElementById("new-signature-placeholder");
            const clearBtn = document.getElementById("clear-signature");
            const clearNewBtn = document.getElementById("clear-new-signature");
            const clearExistingBtn = document.getElementById("clearExistingSignature");
            const cancelNewBtn = document.getElementById("cancel-new-signature");

            if (typeof SignaturePad === "undefined") {
                console.log('SignaturePad library not loaded');
                return;
            }

            // Initialize main signature pad if canvas exists
            if (canvas) {
                signaturePad = initSingleSignaturePad(canvas, placeholder, clearBtn);
            }

            // Initialize new signature pad if canvas exists
            let newSignaturePad = null;
            if (newCanvas) {
                newSignaturePad = initSingleSignaturePad(newCanvas, newPlaceholder, clearNewBtn);
            }

            // Handle clear existing signature button
            if (clearExistingBtn) {
                clearExistingBtn.addEventListener('click', function() {
                    const existingSignatureDiv = document.querySelector('.existing-signature');
                    const newSignaturePadDiv = document.getElementById('new-signature-pad');
                    const existingSignatureInput = document.querySelector(
                        'input[name="existing_signature"]');

                    if (existingSignatureDiv) existingSignatureDiv.style.display = 'none';
                    if (newSignaturePadDiv) newSignaturePadDiv.style.display = 'block';
                    if (existingSignatureInput) existingSignatureInput.remove();

                    if (!newSignaturePad && newCanvas) {
                        newSignaturePad = initSingleSignaturePad(newCanvas, newPlaceholder,
                            clearNewBtn);
                        signaturePad = newSignaturePad;
                    }
                });
            }

            // Handle cancel new signature
            if (cancelNewBtn) {
                cancelNewBtn.addEventListener('click', function() {
                    const existingSignatureDiv = document.querySelector('.existing-signature');
                    const newSignaturePadDiv = document.getElementById('new-signature-pad');

                    if (existingSignatureDiv) existingSignatureDiv.style.display = 'block';
                    if (newSignaturePadDiv) newSignaturePadDiv.style.display = 'none';

                    if (newSignaturePad) {
                        newSignaturePad.clear();
                    }

                    signaturePad = null;

                    if (!document.querySelector('input[name="existing_signature"]')) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'existing_signature';
                        hiddenInput.value = '1';
                        document.getElementById('assessmentForm').appendChild(hiddenInput);
                    }
                });
            }
        }

        function initSingleSignaturePad(canvas, placeholder, clearButton) {
            try {
                const container = canvas.parentElement;
                const rect = container.getBoundingClientRect();
                const ratio = Math.max(window.devicePixelRatio || 1, 1);

                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;

                const ctx = canvas.getContext("2d");
                ctx.scale(ratio, ratio);

                canvas.style.width = rect.width + "px";
                canvas.style.height = rect.height + "px";

                function resetCanvasContext() {
                    const ctx = canvas.getContext("2d");
                    ctx.fillStyle = "rgba(255, 255, 255, 1)";
                    ctx.strokeStyle = "rgba(0, 0, 0, 1)";
                    ctx.lineWidth = 2;
                    ctx.lineCap = "round";
                    ctx.lineJoin = "round";
                    ctx.fillRect(0, 0, rect.width, rect.height);
                }

                resetCanvasContext();

                const pad = new SignaturePad(canvas, {
                    backgroundColor: "rgba(255, 255, 255, 1)",
                    penColor: "rgba(0, 0, 0, 1)",
                    minWidth: 2,
                    maxWidth: 4,
                    throttle: 16,
                    minPointDistance: 2,
                    onBegin: function() {
                        if (placeholder) placeholder.classList.add("d-none");
                    },
                    onEnd: function() {
                        if (!pad.isEmpty() && placeholder) {
                            placeholder.classList.add("d-none");
                        }
                    },
                });

                const originalClear = pad.clear;
                pad.clear = function() {
                    originalClear.call(this);
                    resetCanvasContext();
                    this._ctx.strokeStyle = "rgba(0, 0, 0, 1)";
                    this._ctx.fillStyle = "rgba(0, 0, 0, 1)";
                    if (placeholder) placeholder.classList.remove("d-none");
                };

                if (clearButton) {
                    clearButton.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        pad.clear();
                    });
                }

                return pad;
            } catch (error) {
                console.error('Signature pad initialization failed:', error);
                return null;
            }
        }

        /* ===================== EVENT LISTENERS INITIALIZATION ===================== */

        function initializeEventListeners() {
            // Track changes
            document.addEventListener('input', function(e) {
                if (e.target.matches('input[type="radio"][name*="assessment"]')) {
                    hasUnsavedChanges = true;
                }
            });

            // Assessment radio buttons and evidence checkboxes
            document.addEventListener('change', function(e) {
                if (e.target.matches('input[type="radio"][name*="assessment"]')) {
                    hasUnsavedChanges = true;
                }

                // Evidence checkboxes - FIXED: Now properly handles per-element selection
                if (e.target.matches('.evidence-checkbox')) {
                    handleEvidenceSelection(e.target);
                    updateSelectedEvidence();
                }

                // File input changes
                if (e.target.matches('.evidence-file')) {
                    handleFileSelection(e.target);
                }
            });

            // Button event listeners
            const saveButton = document.getElementById('saveAssessment');
            if (saveButton) {
                saveButton.addEventListener('click', handleSaveAssessment);
            }

            // Dynamic event delegation
            document.addEventListener('click', function(e) {
                if (e.target.matches('.delete-evidence')) {
                    const evidenceId = e.target.dataset.evidenceId;
                    deleteEvidence(evidenceId);
                }

                if (e.target.matches('.preview-evidence')) {
                    const evidenceId = e.target.dataset.evidenceId;
                    previewEvidence(evidenceId);
                }

                if (e.target.matches('.download-evidence')) {
                    e.preventDefault();
                    const url = e.target.dataset.url;
                    downloadFile(url);
                }

                if (e.target.matches('.preview-selected-file')) {
                    const portfolioId = e.target.dataset.portfolioId;
                    previewSelectedFile(portfolioId);
                }

                if (e.target.matches('.remove-selected-file')) {
                    const portfolioId = e.target.dataset.portfolioId;
                    removeSelectedFile(portfolioId);
                }
            });

            // Warn about unsaved changes
            window.addEventListener('beforeunload', function(e) {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }

        /* ===================== EVENT HANDLERS ===================== */

        function handleSaveAssessment(e) {
            e.preventDefault();
            saveAssessmentWithFiles();
        }

        // FIXED: Now properly tracks which documents are selected for each element
        function handleEvidenceSelection(checkbox) {
            const elementId = checkbox.dataset.elementId;
            const portfolioId = checkbox.dataset.portfolioId;

            // Initialize element documents array if doesn't exist
            if (!elementDocuments[elementId]) {
                elementDocuments[elementId] = [];
            }

            if (checkbox.checked) {
                // Add portfolio to element if not already present
                if (!elementDocuments[elementId].includes(portfolioId)) {
                    elementDocuments[elementId].push(portfolioId);
                }
            } else {
                // Remove portfolio from element
                elementDocuments[elementId] = elementDocuments[elementId].filter(id => id !== portfolioId);

                // Clean up empty arrays
                if (elementDocuments[elementId].length === 0) {
                    delete elementDocuments[elementId];
                }
            }

            console.log('Updated element documents:', elementDocuments);
            hasUnsavedChanges = true;
        }

        function handleFileSelection(fileInput) {
            const portfolioId = fileInput.dataset.portfolioId;
            const files = fileInput.files;

            if (files.length > 0) {
                selectedFiles[portfolioId] = files[0];
                updateFileDisplay(portfolioId, files[0]);
            } else {
                delete selectedFiles[portfolioId];
                updateFileDisplay(portfolioId, null);
            }
        }

        /* ===================== EVIDENCE MANAGEMENT ===================== */

        function updateSelectedEvidence() {
            const selectedCheckboxes = document.querySelectorAll('.evidence-checkbox:checked');
            const selectedSection = document.getElementById('selectedEvidenceSection');
            const selectedList = document.getElementById('selectedEvidenceList');

            if (!selectedSection || !selectedList) {
                return;
            }

            if (selectedCheckboxes.length === 0) {
                selectedSection.style.display = 'none';
                return;
            }

            selectedSection.style.display = 'block';

            // Group by document name to avoid duplicates in upload section (like original version)
            const uniqueEvidence = {};
            selectedCheckboxes.forEach(checkbox => {
                const documentName = checkbox.dataset.documentName;
                const portfolioId = checkbox.dataset.portfolioId;
                const elementId = checkbox.dataset.elementId;

                // Use document name as key to ensure uniqueness like original version
                if (!uniqueEvidence[documentName]) {
                    uniqueEvidence[documentName] = {
                        portfolioId: portfolioId,
                        elementId: elementId,
                        documentName: documentName
                    };
                }
            });

            // Generate HTML for unique evidence
            let html = '';
            Object.values(uniqueEvidence).forEach(evidence => {
                // Check existing file by document name instead of portfolio ID
                const existingFile = Object.values(existingEvidence).find(e => e.document_name ===
                    evidence.documentName);
                html += generateEvidenceItemHtml(evidence, existingFile);
            });

            selectedList.innerHTML = html;
        }

        function generateEvidenceItemHtml(evidence, existingFile) {
            return `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="portfolio-item border rounded p-3">
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${evidence.documentName}</h6>
                            <small class="text-muted">Portfolio ID: ${evidence.portfolioId}</small>
                        </div>
                        <div class="text-end">
                            ${existingFile ? 
                                '<i class="bi bi-check-circle-fill text-success fs-4" title="Sudah diupload"></i>' : 
                                '<i class="bi bi-circle text-muted fs-4" title="Belum diupload"></i>'
                            }
                        </div>
                    </div>
                    
                    <div class="evidence-upload" data-portfolio-id="${evidence.portfolioId}" data-element-id="${evidence.elementId}">
                        ${existingFile ? 
                            generateExistingEvidenceHtml(existingFile) : 
                            generateUploadFormHtml(evidence.portfolioId)
                        }
                    </div>
                </div>
            </div>
        `;
        }

        function generateExistingEvidenceHtml(evidence) {
            return `
            <div class="existing-evidence">
                <div class="d-flex align-items-center p-2 bg-light rounded mb-2">
                    <i class="bi bi-file-earmark me-2"></i>
                    <div class="flex-grow-1">
                        <small class="fw-bold">${evidence.file_name}</small>
                        <br>
                        <small class="text-muted">${evidence.file_size_formatted}</small>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-sm download-evidence"
                                data-url="${evidence.download_url}"
                                title="Download">
                            <i class="bi bi-download"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm preview-evidence"
                                data-evidence-id="${evidence.id}"
                                data-file-type="${evidence.file_type}"
                                data-preview-url="${evidence.preview_url || evidence.download_url}"
                                title="Preview">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete-evidence"
                                data-evidence-id="${evidence.id}"
                                title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        }

        function generateUploadFormHtml(portfolioId) {
            return `
            <div class="upload-form">
                <div class="mb-2">
                    <input type="file" class="form-control form-control-sm evidence-file" 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
                           data-portfolio-id="${portfolioId}">
                </div>
                <div class="selected-file-info" id="file-info-${portfolioId}" style="display: none;">
                    <div class="d-flex align-items-center p-2 bg-light rounded mb-2">
                        <i class="bi bi-file-earmark me-2"></i>
                        <div class="flex-grow-1">
                            <small class="fw-bold file-name"></small>
                            <br>
                            <small class="text-muted file-size"></small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-info btn-sm preview-selected-file"
                                    data-portfolio-id="${portfolioId}"
                                    title="Preview">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-selected-file"
                                    data-portfolio-id="${portfolioId}"
                                    title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <small class="text-muted">File akan diupload saat tombol "Simpan & Submit" ditekan</small>
            </div>
        `;
        }

        function updateFileDisplay(portfolioId, file) {
            const fileInfo = document.getElementById(`file-info-${portfolioId}`);

            if (fileInfo) {
                if (file) {
                    fileInfo.style.display = 'block';
                    const fileNameElement = fileInfo.querySelector('.file-name');
                    const fileSizeElement = fileInfo.querySelector('.file-size');
                    if (fileNameElement) fileNameElement.textContent = file.name;
                    if (fileSizeElement) fileSizeElement.textContent = ` (${formatFileSize(file.size)})`;
                } else {
                    fileInfo.style.display = 'none';
                }
            }
        }

        /* ===================== MAIN FUNCTION - FORM SUBMIT VERSION ===================== */

        function saveAssessmentWithFiles() {
            // Kumpulkan data assessment
            const assessments = collectAssessmentDataWithDocuments();
            if (assessments.length === 0) {
                showAlert('warning', 'Belum ada assessment yang dilakukan');
                return;
            }

            // Cek tanda tangan
            const hasExistingSignature = document.querySelector('input[name="existing_signature"]');
            if (!hasExistingSignature && signaturePad && signaturePad.isEmpty()) {
                showAlert('warning', 'Tanda tangan digital diperlukan untuk submit assessment');
                return;
            }

            // Cari form yang ada atau buat baru
            let form = document.querySelector('#assessmentForm');
            if (!form) {
                // Buat form baru jika belum ada
                form = document.createElement('form');
                form.id = 'assessmentForm';
                form.method = 'POST';
                form.action = '{{ route("asesi.apl02.update", $apl02) }}';
                form.enctype = 'multipart/form-data';
                document.body.appendChild(form);
            }

            // Pastikan form punya method dan action yang benar
            form.method = 'POST';
            form.action = '{{ route("asesi.apl02.update", $apl02) }}';
            form.enctype = 'multipart/form-data';

            // Hapus input lama untuk mencegah duplikasi
            form.querySelectorAll('input[name="assessments"], input[name="signature"], input[name^="evidence_files"]').forEach(input => input.remove());

            // CSRF Token
            let csrfInput = form.querySelector('input[name="_token"]');
            if (!csrfInput) {
                csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrfInput);
            }

            // Assessment data
            const assessmentInput = document.createElement('input');
            assessmentInput.type = 'hidden';
            assessmentInput.name = 'assessments';
            assessmentInput.value = JSON.stringify(assessments);
            form.appendChild(assessmentInput);

            // Tanda tangan (kalau ada yang baru)
            if (!hasExistingSignature && signaturePad && !signaturePad.isEmpty()) {
                const signatureInput = document.createElement('input');
                signatureInput.type = 'hidden';
                signatureInput.name = 'signature';
                signatureInput.value = signaturePad.toDataURL();
                form.appendChild(signatureInput);
            }

            // Evidence files - buat input file untuk setiap file yang dipilih
            Object.keys(selectedFiles).forEach(portfolioId => {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = `evidence_files[${portfolioId}]`;
                fileInput.style.display = 'none';
                
                // Transfer file ke input menggunakan DataTransfer API
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(selectedFiles[portfolioId]);
                fileInput.files = dataTransfer.files;
                
                form.appendChild(fileInput);
            });

            console.log('Form data yang akan disubmit:', {
                assessments: assessments,
                selectedFiles: Object.keys(selectedFiles),
                hasExistingSignature: !!hasExistingSignature
            });

            // Show loading
            showLoading(true, 'Menyimpan assessment...');
            
            // Set flag
            hasUnsavedChanges = false;
            
            // Submit form - Flash message akan muncul setelah redirect!
            form.submit();
        }

        /* ===================== HELPER FUNCTIONS ===================== */

        function collectAssessmentDataWithDocuments() {
            const assessments = [];
            const checkedRadios = document.querySelectorAll('input[type="radio"][name*="assessment"]:checked');

            console.log('Found checked radios:', checkedRadios.length);
            console.log('Element documents to save:', elementDocuments);

            checkedRadios.forEach(radio => {
                const nameMatch = radio.name.match(/assessment\[(\d+)\]/);
                if (!nameMatch) {
                    console.log('Could not extract element ID from name:', radio.name);
                    return;
                }

                const elementId = nameMatch[1];
                const value = radio.value;

                const elementContainer = radio.closest('.element-assessment');
                if (!elementContainer) {
                    console.log('Could not find element container for radio:', radio.name);
                    return;
                }

                const unitId = elementContainer.dataset.unitId;

                // Get selected documents for this element
                const selectedDocuments = elementDocuments[elementId] || [];
                const documentsData = selectedDocuments.map(portfolioId => {
                    const checkbox = document.querySelector(
                        `input.evidence-checkbox[data-element-id="${elementId}"][data-portfolio-id="${portfolioId}"]`
                    );
                    return {
                        portfolioId: portfolioId,
                        documentName: checkbox ? checkbox.dataset.documentName :
                            'Unknown Document'
                    };
                });

                assessments.push({
                    elemen_id: elementId,
                    unit_id: unitId,
                    result: value,
                    notes: documentsData.length > 0 ? JSON.stringify(documentsData) :
                        null // Convert to JSON string
                });
            });

            console.log('Final assessments array with documents:', assessments);
            return assessments;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        /* ===================== EVIDENCE MANAGEMENT FUNCTIONS ===================== */

        function deleteEvidence(evidenceId) {
            const evidence = Object.values(existingEvidence).find(e => e.id == evidenceId);
            if (!evidence) {
                showAlert('warning', 'File tidak ditemukan');
                return;
            }

            if (confirm(`Hapus file "${evidence.file_name}"?`)) {
                showLoading(true, 'Menghapus file...');

                const deleteUrl = `{{ route('asesi.apl02.delete-evidence', [$apl02, ':evidenceId']) }}`.replace(
                    ':evidenceId', evidenceId);

                fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        showLoading(false);

                        if (data.success) {
                            showAlert('success', data.message);

                            // Remove from existing evidence
                            Object.keys(existingEvidence).forEach(key => {
                                if (existingEvidence[key].id == evidenceId) {
                                    delete existingEvidence[key];
                                }
                            });

                            // Update UI
                            updateSelectedEvidence();
                        } else {
                            showAlert('danger', data.error || 'Gagal menghapus bukti');
                        }
                    })
                    .catch(error => {
                        showLoading(false);
                        console.error('Error:', error);
                        showAlert('danger', 'Terjadi kesalahan saat menghapus');
                    });
            }
        }

        function previewEvidence(evidenceId) {
            const evidence = Object.values(existingEvidence).find(e => e.id == evidenceId);
            if (!evidence) {
                showAlert('warning', 'File tidak ditemukan');
                return;
            }

            const fileType = evidence.file_type?.toLowerCase();

            if (['pdf', 'jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                const previewUrl = evidence.preview_url || evidence.download_url;
                window.open(previewUrl, '_blank', 'width=800,height=600,scrollbars=yes');
            } else {
                showAlert('info', 'File tidak dapat dipreview. Silakan download untuk melihat isi file.');
            }
        }

        function downloadFile(url, filename = null) {
            const link = document.createElement('a');
            link.href = url;
            if (filename) {
                link.download = filename;
            }
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function previewSelectedFile(portfolioId) {
            const file = selectedFiles[portfolioId];
            if (!file) {
                showAlert('warning', 'File tidak ditemukan');
                return;
            }

            const fileType = file.type.split('/')[1]?.toLowerCase() || file.name.split('.').pop()
                ?.toLowerCase();

            if (['pdf'].includes(fileType) || file.type.startsWith('image/')) {
                const objectUrl = URL.createObjectURL(file);
                window.open(objectUrl, '_blank', 'width=800,height=600,scrollbars=yes');

                setTimeout(() => {
                    URL.revokeObjectURL(objectUrl);
                }, 30000);
            } else {
                showAlert('info', 'File tidak dapat dipreview');
            }
        }

        function removeSelectedFile(portfolioId) {
            delete selectedFiles[portfolioId];

            const container = document.querySelector(`.evidence-upload[data-portfolio-id="${portfolioId}"]`);
            const fileInput = container?.querySelector('.evidence-file');
            if (fileInput) {
                fileInput.value = '';
            }

            updateFileDisplay(portfolioId, null);
            showAlert('success', 'File berhasil dihapus dari daftar upload');
        }

        /* ===================== UI HELPER FUNCTIONS ===================== */

        function showLoading(show, message = 'Menyimpan data...') {
            const modal = document.getElementById('loadingModal');
            if (!modal) {
                if (show) {
                    const overlay = document.createElement('div');
                    overlay.id = 'simple-loading-overlay';
                    overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    color: white;
                    font-size: 18px;
                `;
                    overlay.innerHTML =
                        `<div><div class="spinner-border" role="status"></div><div class="mt-2">${message}</div></div>`;
                    document.body.appendChild(overlay);
                } else {
                    const overlay = document.getElementById('simple-loading-overlay');
                    if (overlay) {
                        overlay.remove();
                    }
                }
                return;
            }

            const loadingMessage = modal.querySelector('p');
            if (loadingMessage) {
                loadingMessage.textContent = message;
            }

            if (show) {
                let modalInstance = bootstrap.Modal.getInstance(modal);
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(modal, {
                        backdrop: 'static',
                        keyboard: false
                    });
                }
                modalInstance.show();
            } else {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }

                setTimeout(() => {
                    document.body.classList.remove('modal-open');
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                    modal.style.display = 'none';
                }, 300);
            }
        }

        function showAlert(type, message) {
            const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${getAlertIcon(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

            const container = document.querySelector('.main-card') || document.body;
            container.insertAdjacentHTML('afterbegin', alertHtml);

            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    const alertInstance = bootstrap.Alert.getInstance(alert);
                    if (alertInstance) {
                        alertInstance.close();
                    } else {
                        alert.remove();
                    }
                }
            }, 5000);
        }

        function getAlertIcon(type) {
            const icons = {
                'success': 'check-circle',
                'danger': 'exclamation-triangle',
                'warning': 'exclamation-triangle',
                'info': 'info-circle'
            };
            return icons[type] || 'info-circle';
        }

        // ✅ EXPOSE FUNCTION TO GLOBAL SCOPE FOR ONCLICK
        window.saveAssessmentWithFiles = saveAssessmentWithFiles;
    });
</script>