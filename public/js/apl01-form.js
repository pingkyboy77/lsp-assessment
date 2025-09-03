/**
 * APL-01 Form JavaScript - Complete & Optimized
 * Compatible with Laravel Blade template
 */

(function () {
    "use strict";

    // Global state
    let signaturePad = null;
    let formValidationState = {
        initialized: false,
        requiredFields: new Set(),
        completedFields: new Set(),
    };

    // Dependency checker
    function checkDependencies() {
        const deps = {
            jquery: typeof $ !== "undefined",
            select2: false,
            signaturePad: typeof SignaturePad !== "undefined",
        };

        if (deps.jquery) {
            try {
                deps.select2 =
                    typeof $.fn.select2 !== "undefined" &&
                    $.fn.select2.defaults !== undefined;
            } catch (e) {
                deps.select2 = false;
            }
        }

        return deps;
    }

    // Safe initialization with retry mechanism
    function initializeWithRetry(callback, maxRetries = 3, delay = 200) {
        let attempts = 0;

        function attempt() {
            attempts++;
            try {
                if (callback()) {
                    return true;
                }
            } catch (error) {
                console.warn(
                    `Initialization attempt ${attempts} failed:`,
                    error
                );
            }

            if (attempts < maxRetries) {
                setTimeout(attempt, delay * attempts);
            } else {
                console.error(
                    `Failed to initialize after ${maxRetries} attempts`
                );
            }
        }

        attempt();
    }

    // Main initialization
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Initializing APL-01 Form...");

        setTimeout(() => {
            const deps = checkDependencies();
            console.log("Dependencies:", deps);

            // Core modules (no dependencies required)
            initializeForm();
            initializeConditionalSections();
            initializeFileUploads();
            initializeValidation();
            initializeProgressTracking();

            // jQuery dependent modules
            if (deps.jquery) {
                if (deps.select2) {
                    initializeSelect2();
                    initializeCityAutoFill();
                } else {
                    // Retry Select2 initialization
                    initializeWithRetry(
                        () => {
                            const currentDeps = checkDependencies();
                            if (currentDeps.select2) {
                                initializeSelect2();
                                initializeCityAutoFill();
                                return true;
                            }
                            return false;
                        },
                        5,
                        300
                    );
                }
            } else {
                console.warn(
                    "jQuery not available - Select2 features disabled"
                );
            }

            // SignaturePad
            if (deps.signaturePad) {
                initializeSignaturePad();
            } else {
                console.warn("SignaturePad not available");
            }

            console.log("APL-01 Form initialization complete");
        }, 100);
    });

    // =====================================
    // FORM INITIALIZATION
    // =====================================

    function initializeForm() {
        const form = document.getElementById("apl01Form");
        if (!form) {
            console.warn("Main form not found");
            return;
        }

        // Ensure form uses POST method and has proper encoding
        form.method = "POST";
        form.enctype = "multipart/form-data";
        form.setAttribute("novalidate", "true");
        form.addEventListener("submit", handleFormSubmit);

        // Auto-copy nama lengkap to nama KTP
        const namaLengkap = form.querySelector('input[name="nama_lengkap"]');
        const namaKtp = form.querySelector('input[name="nama_lengkap_ktp"]');

        if (namaLengkap && namaKtp) {
            namaLengkap.addEventListener("blur", function () {
                if (!namaKtp.value.trim()) {
                    namaKtp.value = this.value;
                    updateProgress();
                }
            });
        }

        // Debug form data on submit
        form.addEventListener("submit", function (e) {
            if (window.DEBUG_MODE) {
                console.log("Form data debug:");
                const formData = new FormData(form);
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
            }
        });

        console.log("Form core initialized with fixes");
    }

    // =====================================
    // SELECT2 INITIALIZATION
    // =====================================

    function initializeSelect2() {
        if (!checkDependencies().select2) {
            console.warn("Select2 not available");
            return;
        }

        try {
            // Initialize all select elements
            $("select").each(function () {
                const $select = $(this);

                if ($select.hasClass("select2-hidden-accessible")) {
                    return; // Already initialized
                }

                const placeholder =
                    $select.find('option[value=""]').text() || "Pilih...";

                $select.select2({
                    theme: "bootstrap-5",
                    placeholder: placeholder,
                    allowClear: true,
                    width: "100%",
                    dropdownAutoWidth: true,
                });

                $select.on("select2:select select2:clear", function () {
                    clearFieldError(this);
                    setTimeout(updateProgress, 50);
                });
            });

            // Special configurations for search-enabled selects
            $("#kota_rumah, #kota_kantor").select2({
                theme: "bootstrap-5",
                placeholder: "Ketik untuk mencari kota...",
                allowClear: true,
                width: "100%",
            });

            console.log("Select2 initialized");
        } catch (error) {
            console.error("Select2 initialization failed:", error);
        }
    }

    function initializeCityAutoFill() {
        if (!checkDependencies().jquery) return;

        // Home address auto-fill
        $(document).on("select2:select", "#kota_rumah", function () {
            const selectedOption = $(this).find("option:selected");
            const provinceId = selectedOption.data("province-id");
            const provinceName = selectedOption.data("province-name");

            if (provinceId) {
                $("#provinsi_rumah").val(provinceId); // ID untuk disimpan
                $("#provinsi_rumah_display").val(provinceName); // nama untuk tampilan
                updateProgress();
            }
        });

        $(document).on("select2:clear", "#kota_rumah", function () {
            $("#provinsi_rumah").val("");
            $("#provinsi_rumah_display").val("");
            updateProgress();
        });

        // Office address auto-fill
        $(document).on("select2:select", "#kota_kantor", function () {
            const selectedOption = $(this).find("option:selected");
            const provinceId = selectedOption.data("province-id");
            const provinceName = selectedOption.data("province-name");

            if (provinceId) {
                $("#provinsi_kantor").val(provinceId); // ID untuk disimpan
                $("#provinsi_kantor_display").val(provinceName); // nama untuk tampilan
                updateProgress();
            }
        });

        $(document).on("select2:clear", "#kota_kantor", function () {
            $("#provinsi_kantor").val("");
            $("#provinsi_kantor_display").val("");
            updateProgress();
        });

        // Set initial display value from DB if select2 already has value
        ["#kota_rumah", "#kota_kantor"].forEach(function (selectId) {
            const selectedOption = $(`${selectId} option:selected`);
            if (selectedOption.length && selectedOption.val()) {
                const provinceId = selectedOption.data("province-id");
                const provinceName = selectedOption.data("province-name");
                if (selectId === "#kota_rumah") {
                    $("#provinsi_rumah").val(provinceId);
                    $("#provinsi_rumah_display").val(provinceName);
                } else if (selectId === "#kota_kantor") {
                    $("#provinsi_kantor").val(provinceId);
                    $("#provinsi_kantor_display").val(provinceName);
                }
            }
        });

        console.log("City auto-fill initialized");
    }

    // =====================================
    // SIGNATURE PAD
    // =====================================

    function initializeSignaturePad() {
        const canvas = document.getElementById("signature-canvas");
        const placeholder = document.getElementById("signature-placeholder");
        const clearBtn = document.getElementById("clear-signature");
        const input = document.getElementById("signature-input");

        if (!canvas) {
            console.warn("SignaturePad canvas not found");
            return;
        }

        // Check if SignaturePad library is available
        if (typeof SignaturePad === "undefined") {
            console.error("SignaturePad library not loaded");
            return;
        }

        try {
            // Setup canvas dimensions
            const container = canvas.parentElement;
            const rect = container.getBoundingClientRect();
            const ratio = Math.max(window.devicePixelRatio || 1, 1);

            // Set actual canvas size
            canvas.width = rect.width * ratio;
            canvas.height = rect.height * ratio;

            // Scale the drawing context
            const ctx = canvas.getContext("2d");
            ctx.scale(ratio, ratio);

            // Set display size
            canvas.style.width = rect.width + "px";
            canvas.style.height = rect.height + "px";

            // Initialize SignaturePad with callback functions
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: "rgba(255, 255, 255, 1)",
                penColor: "rgba(0, 0, 0, 1)",
                minWidth: 1,
                maxWidth: 3,
                throttle: 16,
                minPointDistance: 2,
                // FIX: Use onBegin callback instead of addEventListener
                onBegin: function () {
                    console.log("Signature drawing started");
                    if (placeholder) {
                        placeholder.classList.add("hidden");
                    }
                },
                // FIX: Use onEnd callback instead of addEventListener
                onEnd: function () {
                    console.log("Signature drawing ended");
                    if (!signaturePad.isEmpty() && placeholder) {
                        placeholder.classList.add("hidden");
                    }
                    if (input && !signaturePad.isEmpty()) {
                        try {
                            const dataURL = signaturePad.toDataURL("image/png");
                            input.value = dataURL;
                        } catch (error) {
                            console.error("Error saving signature:", error);
                        }
                    }
                    updateProgress();
                },
            });

            // FIX: Clear button functionality
            if (clearBtn) {
                clearBtn.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log("Clear button clicked");

                    if (signaturePad) {
                        signaturePad.clear();
                        console.log("Signature pad cleared");
                    }

                    if (input) {
                        input.value = "";
                        console.log("Hidden input cleared");
                    }

                    if (placeholder) {
                        placeholder.classList.remove("hidden");
                        console.log("Placeholder shown");
                    }

                    updateProgress();
                });
            }

            // FIX: Load existing signature and handle placeholder
            if (input && input.value && input.value.startsWith("data:image/")) {
                console.log("Loading existing signature");
                const img = new Image();
                img.onload = function () {
                    try {
                        // Clear canvas first
                        ctx.clearRect(
                            0,
                            0,
                            canvas.width / ratio,
                            canvas.height / ratio
                        );

                        // Draw the image
                        ctx.drawImage(img, 0, 0, rect.width, rect.height);

                        // Hide placeholder since we have content
                        if (placeholder) {
                            placeholder.classList.add("hidden");
                        }

                        console.log("Existing signature loaded successfully");
                    } catch (error) {
                        console.error(
                            "Error loading existing signature:",
                            error
                        );
                    }
                };
                img.onerror = function () {
                    console.error("Failed to load signature image");
                };
                img.src = input.value;
            } else {
                // No existing signature, show placeholder
                if (placeholder) {
                    placeholder.classList.remove("hidden");
                }
            }

            // Make globally accessible for debugging
            window.signaturePad = signaturePad;
            window.debugSignature = function () {
                console.log("SignaturePad state:", {
                    isEmpty: signaturePad.isEmpty(),
                    placeholderVisible: placeholder
                        ? placeholder.style.display
                        : "no placeholder",
                    inputValue: input
                        ? input.value
                            ? "has value"
                            : "empty"
                        : "no input",
                    canvasData: signaturePad.toData().length,
                });
            };

            console.log("SignaturePad initialized successfully");
        } catch (error) {
            console.error("SignaturePad initialization failed:", error);
        }
    }

    // =====================================
    // CONDITIONAL SECTIONS
    // =====================================

    function initializeConditionalSections() {
        // Training Provider section
        const kategoriRadios = document.querySelectorAll(
            'input[name="kategori_peserta"]'
        );
        const trainingSection = document.getElementById(
            "training_provider_section"
        );

        if (kategoriRadios.length && trainingSection) {
            kategoriRadios.forEach((radio) => {
                radio.addEventListener("change", function () {
                    toggleTrainingProvider(this.value, trainingSection);
                });
            });

            // Initialize state
            const checked = document.querySelector(
                'input[name="kategori_peserta"]:checked'
            );
            if (checked) {
                toggleTrainingProvider(checked.value, trainingSection);
            }
        }

        // Tujuan Asesmen section
        const tujuanRadios = document.querySelectorAll(
            'input[name="tujuan_asesmen_radio"]'
        );
        const tujuanSection = document.getElementById("tujuan_lainnya_section");

        if (tujuanRadios.length && tujuanSection) {
            tujuanRadios.forEach((radio) => {
                radio.addEventListener("change", function () {
                    toggleTujuanLainnya(this.value, tujuanSection);
                });
            });

            // Initialize state
            const checkedTujuan = document.querySelector(
                'input[name="tujuan_asesmen_radio"]:checked'
            );
            if (checkedTujuan) {
                toggleTujuanLainnya(checkedTujuan.value, tujuanSection);
            }
        }

        // Requirement Templates
        const templateRadios = document.querySelectorAll(
            ".requirement-template-radio"
        );
        if (templateRadios.length) {
            templateRadios.forEach((radio) => {
                radio.addEventListener("change", function () {
                    toggleRequirementTemplate(this.value);
                });
            });

            // Initialize state
            const checkedTemplate = document.querySelector(
                ".requirement-template-radio:checked"
            );
            if (checkedTemplate) {
                toggleRequirementTemplate(checkedTemplate.value);
            }
        }

        console.log("Conditional sections initialized");
    }

    function toggleTrainingProvider(value, section) {
        const isTrainingProvider = value === "training_provider";
        section.style.display = isTrainingProvider ? "block" : "none";

        const select = section.querySelector(
            'select[name="training_provider"]'
        );
        if (select) {
            if (isTrainingProvider) {
                select.setAttribute("required", "required");
            } else {
                select.removeAttribute("required");
                select.value = "";
                if (
                    checkDependencies().select2 &&
                    $(select).hasClass("select2-hidden-accessible")
                ) {
                    $(select).val(null).trigger("change");
                }
            }
        }
        updateProgress();
    }

    function toggleTujuanLainnya(value, section) {
        const isLainnya = value === "Lainnya";
        section.style.display = isLainnya ? "block" : "none";

        const textarea = section.querySelector(
            'textarea[name="tujuan_asesmen"]'
        );
        if (textarea) {
            if (isLainnya) {
                textarea.setAttribute("required", "required");
                setTimeout(() => textarea.focus(), 100);
            } else {
                textarea.removeAttribute("required");
                textarea.value = "";
            }
        }
        updateProgress();
    }

    function toggleRequirementTemplate(templateId) {
        // Hide all templates
        document.querySelectorAll(".template-requirements").forEach((div) => {
            div.style.display = "none";
            // Remove required dari semua input yang tersembunyi
            div.querySelectorAll("input, select, textarea").forEach((input) => {
                input.removeAttribute("required");
            });
        });

        // Show selected template
        const selectedTemplate = document.getElementById(
            `template_requirements_${templateId}`
        );
        if (selectedTemplate) {
            selectedTemplate.style.display = "block";

            // PERBAIKAN: Restore required attribute berdasarkan data dari server
            selectedTemplate
                .querySelectorAll(".upload-card")
                .forEach((card) => {
                    const label = card.querySelector(".form-label");
                    const isRequired =
                        label && label.querySelector(".text-danger"); // Ada tanda *

                    if (isRequired) {
                        const fileInput =
                            card.querySelector('input[type="file"]');
                        if (fileInput) {
                            fileInput.setAttribute("required", "required");
                        }
                    }
                });

            // Restore required untuk non-file inputs
            selectedTemplate
                .querySelectorAll(
                    'input[data-required="true"], select[data-required="true"], textarea[data-required="true"]'
                )
                .forEach((input) => {
                    input.setAttribute("required", "required");
                });
        }

        // Update progress setelah toggle
        setTimeout(updateProgress, 100);
    }

    // =====================================
    // FILE UPLOAD SYSTEM
    // =====================================

    function initializeFileUploads() {
        // Event delegation untuk file inputs yang mungkin ditambahkan dinamis
        document.addEventListener("change", function (e) {
            if (
                e.target.type === "file" &&
                e.target.name &&
                e.target.name.includes("requirement_item_")
            ) {
                handleFileChange(e.target, e);
            }
        });

        // Initialize existing file inputs
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach((input) => {
            if (input.name && input.name.includes("requirement_item_")) {
                // File sudah akan di-handle oleh event delegation
                console.log(`File input registered: ${input.name}`);
            }
        });

        console.log(
            `File upload system initialized for ${fileInputs.length} inputs`
        );
    }

    function handleFileChange(input) {
        const file = input.files[0];

        if (!file) {
            updateProgress();
            return;
        }

        if (!validateFile(file, input)) {
            input.value = "";
            return;
        }

        updateProgress();
    }

    function validateFile(file, input) {
        const uploadCard = input.closest(".upload-card");
        let maxSize = 5 * 1024 * 1024; // 5MB default
        let allowedTypes = [".pdf", ".doc", ".docx", ".jpg", ".jpeg", ".png"];

        // Ambil setting dari data attribute atau text di UI
        if (uploadCard) {
            const sizeText = uploadCard.querySelector("small")?.textContent;
            if (sizeText && sizeText.includes("Max:")) {
                const sizeMatch = sizeText.match(/Max:\s*(\d+)MB/);
                if (sizeMatch) {
                    maxSize = parseInt(sizeMatch[1]) * 1024 * 1024;
                }
            }

            if (sizeText && sizeText.includes("Format:")) {
                const formatMatch = sizeText.match(/Format:\s*([^(]+)/);
                if (formatMatch) {
                    allowedTypes = formatMatch[1]
                        .split(",")
                        .map((f) => "." + f.trim().toLowerCase());
                }
            }
        }

        const fileExt = "." + file.name.split(".").pop().toLowerCase();

        if (file.size > maxSize) {
            showNotification(
                `Ukuran file maksimal ${Math.round(maxSize / 1024 / 1024)}MB`,
                "error"
            );
            return false;
        }

        if (!allowedTypes.includes(fileExt)) {
            showNotification(
                `Format file tidak didukung. Gunakan: ${allowedTypes.join(
                    ", "
                )}`,
                "error"
            );
            return false;
        }

        return true;
    }

    function extractItemId(input) {
        // Extract from name attribute: requirement_item_123
        const nameMatch = input.name.match(/requirement_item_(\d+)/);
        if (nameMatch) return nameMatch[1];

        // Extract from onchange attribute
        const onchangeAttr = input.getAttribute("onchange");
        if (onchangeAttr) {
            const onchangeMatch = onchangeAttr.match(
                /previewFile\(.*?['"](.*?)['"]\)/
            );
            if (onchangeMatch) return onchangeMatch[1];
        }

        return null;
    }

    // =====================================
    // FILE PREVIEW FUNCTIONS
    // =====================================

    window.previewFile = function (input, itemId) {
        console.log(`Preview file for item: ${itemId}`);

        const file = input.files[0];
        if (!file) {
            updateProgress();
            return;
        }

        const previewContainer = document.getElementById(`preview_${itemId}`);
        if (!previewContainer) {
            console.warn(`Preview container not found for item ${itemId}`);
            updateProgress();
            return;
        }

        // Validasi file
        if (!validateFile(file, input)) {
            input.value = "";
            updateProgress();
            return;
        }

        // Show preview container
        previewContainer.style.display = "block";

        // Update file info
        const filenameElement = document.getElementById(`filename_${itemId}`);
        const filesizeElement = document.getElementById(`filesize_${itemId}`);

        if (filenameElement) filenameElement.textContent = file.name;
        if (filesizeElement)
            filesizeElement.textContent = `(${formatFileSize(file.size)})`;

        // Handle different file types
        const fileType = file.type.toLowerCase();
        const imgElement = document.getElementById(`img_${itemId}`);
        const pdfElement = document.getElementById(`pdf_${itemId}`);

        // Hide all preview elements first
        if (imgElement) imgElement.style.display = "none";
        if (pdfElement) pdfElement.style.display = "none";

        if (fileType.includes("image/")) {
            if (imgElement) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgElement.src = e.target.result;
                    imgElement.style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        } else if (fileType.includes("pdf")) {
            if (pdfElement) {
                pdfElement.style.display = "flex";
            }
        } else {
            if (pdfElement) {
                const icon = pdfElement.querySelector("i");
                if (icon) {
                    icon.className = "bi bi-file-earmark text-primary fs-1";
                }
                const text = pdfElement.querySelector("p");
                if (text) text.textContent = "Document File";
                pdfElement.style.display = "flex";
            }
        }

        // Hide existing file display ketika upload file baru
        const uploadCard = input.closest(".upload-card");
        if (uploadCard) {
            const existingFile = uploadCard.querySelector(
                ".existing-file-display"
            );
            if (existingFile) {
                existingFile.style.display = "none";
            }

            // Remove existing marker ketika upload file baru
            const existingMarker = uploadCard.querySelector(
                `input[name*="_existing"]`
            );
            if (existingMarker) {
                existingMarker.remove();
                console.log(
                    `Removed existing marker for item ${itemId} due to new upload`
                );
            }
        }

        updateProgress();
    };

    window.removePreview = function (itemId) {
        console.log(`Removing preview for item: ${itemId}`);

        const input = document.querySelector(
            `input[name*="requirement_item_${itemId}"], input[id*="${itemId}"]`
        );
        if (input) {
            input.value = "";

            // Hide preview container
            const previewContainer = document.getElementById(
                `preview_${itemId}`
            );
            if (previewContainer) {
                previewContainer.style.display = "none";
            }

            // Clear preview elements
            const imgElement = document.getElementById(`img_${itemId}`);
            if (imgElement) {
                imgElement.src = "";
                imgElement.style.display = "none";
            }

            const pdfElement = document.getElementById(`pdf_${itemId}`);
            if (pdfElement) {
                pdfElement.style.display = "none";
            }

            // Show existing file display lagi jika ada
            const uploadCard = input.closest(".upload-card");
            if (uploadCard) {
                const existingFile = uploadCard.querySelector(
                    ".existing-file-display"
                );
                if (existingFile) {
                    existingFile.style.display = "block";
                    input.style.display = "none";

                    // PERBAIKAN: Remove required karena ada existing file
                    input.removeAttribute("required");

                    // Tambahkan kembali existing marker jika belum ada
                    if (!uploadCard.querySelector(`input[name*="_existing"]`)) {
                        const existingMarker = document.createElement("input");
                        existingMarker.type = "hidden";
                        existingMarker.name = input.name + "_existing";
                        existingMarker.value = "1";
                        uploadCard.appendChild(existingMarker);
                    }

                    console.log(`Restored existing file for item ${itemId}`);
                }
            }

            updateProgress();
        }
    };

    window.replaceExistingFile = function (itemId) {
        console.log(`Replacing existing file for item: ${itemId}`);

        const uploadCard = document.querySelector(
            `.upload-card[data-item-id="${itemId}"]`
        );
        if (!uploadCard) {
            console.warn(`Upload card not found for item ${itemId}`);
            return;
        }

        const existingFileDiv = uploadCard.querySelector(
            ".existing-file-display"
        );
        const fileInput = uploadCard.querySelector(`input[type="file"]`);
        const existingMarker = uploadCard.querySelector(
            `input[name*="_existing"]`
        );

        // Hide existing file display
        if (existingFileDiv) {
            existingFileDiv.style.display = "none";
        }

        // Remove existing marker (supaya controller tahu file perlu diupload ulang)
        if (existingMarker) {
            existingMarker.remove();
        }

        // Show file input dan make it required
        if (fileInput) {
            fileInput.style.display = "block";

            // Check if this field should be required
            const label = uploadCard.querySelector(".form-label");
            const isRequired = label && label.querySelector(".text-danger");

            if (isRequired) {
                fileInput.setAttribute("required", "required");
                console.log(`Made file input required for item ${itemId}`);
            }

            fileInput.focus();
        }

        // Update progress
        setTimeout(updateProgress, 100);
    };

    window.removeExistingFile = function (fieldName, button) {
        if (!confirm("Yakin ingin menghapus file ini?")) return;

        const existingDoc = button.closest(".existing-doc");
        if (existingDoc) {
            existingDoc.style.display = "none";

            // Add hidden delete flag
            const deleteInput = document.createElement("input");
            deleteInput.type = "hidden";
            deleteInput.name = fieldName + "_delete";
            deleteInput.value = "1";
            existingDoc.appendChild(deleteInput);

            showNotification("File akan dihapus saat form disimpan", "info");
            updateProgress();
        }
    };

    // =====================================
    // FORM SUBMISSION FIXES
    // =====================================

    function handleFormSubmit(e) {
    e.preventDefault(); // cegah submit default
    const form = e.target;
    const action = e.submitter?.value || "draft";

    console.log("Form submission started:", action);

    // PERBAIKAN: remove required dari input file hidden yg sudah ada file
    const hiddenFileInputs = form.querySelectorAll(
        'input[type="file"][style*="display: none"][required]'
    );
    hiddenFileInputs.forEach((input) => {
        const uploadCard = input.closest(".upload-card");
        if (uploadCard) {
            const existingFile = uploadCard.querySelector(".existing-file-display");
            const hasExistingFile = existingFile && existingFile.style.display !== "none";
            if (hasExistingFile) {
                console.log(`Removing required from hidden input: ${input.name}`);
                input.removeAttribute("required");
                input.dataset.tempRemoved = "true"; // tanda tracking
            }
        }
    });

    // Simpan signature kalau ada
    if (typeof signaturePad !== "undefined" && signaturePad && !signaturePad.isEmpty()) {
        const signatureInput = document.getElementById("signature-input");
        if (signatureInput) {
            signatureInput.value = signaturePad.toDataURL("image/png");
            console.log("Signature data saved to input");
        }
    }

    // Tambahkan hidden input action (selalu ada)
    let hiddenAction = form.querySelector("input[name='action']");
    if (!hiddenAction) {
        hiddenAction = document.createElement("input");
        hiddenAction.type = "hidden";
        hiddenAction.name = "action";
        form.appendChild(hiddenAction);
    }
    hiddenAction.value = action;

    // Kalau submit → validasi + konfirmasi Swal
    if (action === "submit") {
        if (!validateFormForSubmission()) {
            console.log("Form validation failed");

            // Restore required yang dihapus
            hiddenFileInputs.forEach((input) => {
                if (input.dataset.tempRemoved === "true") {
                    input.setAttribute("required", "required");
                    delete input.dataset.tempRemoved;
                }
            });
            return false;
        }

        Swal.fire({
    title: "Konfirmasi Submit",
    text: "Apakah Anda yakin ingin submit APL 01? Setelah submit data tidak dapat diubah.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Submit",
    cancelButtonText: "Batal",
    // reverseButtons: true, // Hapus ini supaya confirm di kiri
    customClass: {
        popup: "rounded-3 shadow-lg", 
        title: "fw-bold text-dark",   
        confirmButton: "btn btn-success px-4 py-2 me-2 rounded-pill shadow-sm",
        cancelButton: "btn btn-secondary px-4 py-2 rounded-pill shadow-sm"
    },
    buttonsStyling: false
}).then((result) => {
    if (result.isConfirmed) {
        showLoadingModal();
        form.submit();
    } else {
        console.log("Submit dibatalkan user");
    }
});


        return false; // stop disini
    }

    // Kalau draft → langsung submit
    showLoadingModal();
    console.log("Form submission proceeding as draft...");
    form.submit();
}


    // =====================================
    // FORM VALIDATION
    // =====================================

    function initializeValidation() {
        const form = document.getElementById("apl01Form");
        if (!form) return;

        // NIK validation
        const nikInput = form.querySelector('input[name="nik"]');
        if (nikInput) {
            nikInput.addEventListener("input", function () {
                let value = this.value.replace(/\D/g, "");
                if (value.length > 16) value = value.slice(0, 16);
                this.value = value;

                if (value.length > 0 && value.length < 16) {
                    showFieldError(this, "NIK harus 16 digit");
                } else {
                    clearFieldError(this);
                }
            });
        }

        // Phone number formatting
        const phoneInputs = form.querySelectorAll(
            'input[name="no_hp"], input[name="no_telp_rumah"], input[name="no_telp_kantor"]'
        );
        phoneInputs.forEach((input) => {
            input.addEventListener("input", function () {
                let value = this.value.replace(/\D/g, "");
                if (value.length > 15) value = value.slice(0, 15);
                this.value = value;
            });
        });

        // General field validation
        const allInputs = form.querySelectorAll("input, textarea, select");
        allInputs.forEach((input) => {
            input.addEventListener("blur", function () {
                if (this.hasAttribute("required")) {
                    validateField(this);
                }
            });

            input.addEventListener("input", function () {
                clearFieldError(this);
                debounceUpdateProgress();
            });

            input.addEventListener("change", function () {
                debounceUpdateProgress();
            });
        });

        console.log("Validation initialized");
    }

    function validateField(field) {
        if (!field.hasAttribute("required")) return true;

        let isValid = false;

        if (field.type === "checkbox" || field.type === "radio") {
            const checked = document.querySelector(
                `input[name="${field.name}"]:checked`
            );
            isValid = checked !== null;
        } else {
            isValid = field.value.trim() !== "";
        }

        if (isValid) {
            clearFieldError(field);
        } else {
            showFieldError(field, "Field ini wajib diisi");
        }

        return isValid;
    }

    function validateFormForSubmission() {
        const form = document.getElementById("apl01Form");
        if (!form) return false;

        let isValid = true;
        const errors = [];

        // Check agreement checkbox
        const agreement = form.querySelector('input[name="pernyataan_benar"]');
        if (!agreement || !agreement.checked) {
            errors.push("Anda harus menyetujui pernyataan");
            if (agreement) {
                agreement.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
                showFieldError(agreement, "Field ini wajib dicentang");
            }
            isValid = false;
        }

        // Check signature - cek existing signature juga
        const signatureInput = document.getElementById("signature-input");
        const existingSignature =
            document.querySelector('input[name="existing_signature"]') ||
            document.querySelector(".existing-signature");

        if (!existingSignature && signaturePad && signaturePad.isEmpty()) {
            errors.push("Tanda tangan digital diperlukan");
            const canvas = document.getElementById("signature-canvas");
            if (canvas) {
                canvas.scrollIntoView({ behavior: "smooth", block: "center" });
            }
            isValid = false;
        } else if (
            !existingSignature &&
            (!signatureInput || !signatureInput.value)
        ) {
            errors.push("Tanda tangan digital diperlukan");
            isValid = false;
        }

        // PERBAIKAN: Gunakan customFormValidation untuk validasi field
        if (!customFormValidation()) {
            isValid = false;
        }

        // Show errors
        if (errors.length > 0) {
            showNotification(errors[0], "error");
        } else if (!isValid) {
            showNotification("Mohon lengkapi semua field yang wajib", "error");
        }

        return isValid;
    }

    function showFieldError(field, message) {
        field.classList.add("error");

        // Handle Select2
        if (
            checkDependencies().jquery &&
            $(field).hasClass("select2-hidden-accessible")
        ) {
            $(field).next(".select2-container").addClass("error");
        }

        let errorElement = field.parentNode.querySelector(".error-message");
        if (!errorElement) {
            errorElement = document.createElement("div");
            errorElement.className = "error-message";
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    function clearFieldError(field) {
        field.classList.remove("error");

        // Handle Select2
        if (
            checkDependencies().jquery &&
            $(field).hasClass("select2-hidden-accessible")
        ) {
            $(field).next(".select2-container").removeClass("error");
        }

        const errorElement = field.parentNode.querySelector(
            ".error-message:not([data-server-error])"
        );
        if (errorElement) {
            errorElement.remove();
        }
    }

    // =====================================
    // PROGRESS TRACKING
    // =====================================

    function initializeProgressTracking() {
        updateProgress();

        // Listen to all form changes
        const form = document.getElementById("apl01Form");
        if (form) {
            form.addEventListener("input", debounceUpdateProgress);
            form.addEventListener("change", debounceUpdateProgress);
        }

        console.log("Progress tracking initialized");
    }

    function updateProgress() {
        try {
            const form = document.getElementById("apl01Form");
            if (!form) return;

            const requiredFields = Array.from(
                form.querySelectorAll("[required]")
            ).filter(isFieldVisible);
            const completedFields = requiredFields.filter((field) => {
                if (field.type === "checkbox" || field.type === "radio") {
                    return (
                        form.querySelector(
                            `input[name="${field.name}"]:checked`
                        ) !== null
                    );
                }

                // PERBAIKAN: Khusus handling untuk file upload
                if (field.type === "file") {
                    // Cek apakah ada file existing yang sudah diupload
                    const uploadCard = field.closest(".upload-card");
                    if (uploadCard) {
                        const existingFile = uploadCard.querySelector(
                            ".existing-file-display"
                        );
                        const hasExistingFile =
                            existingFile &&
                            existingFile.style.display !== "none";
                        const hasNewFile =
                            field.files && field.files.length > 0;

                        return hasExistingFile || hasNewFile;
                    }
                    return field.files && field.files.length > 0;
                }

                return field.value.trim() !== "";
            });

            // Check signature - either existing or new
            const hasExistingSignature = document.querySelector(
                'input[name="existing_signature"]'
            );
            const hasNewSignature = signaturePad && !signaturePad.isEmpty();
            const hasSignature = hasExistingSignature || hasNewSignature;

            const signatureWeight = 1;
            const total = requiredFields.length + signatureWeight;
            const completed = completedFields.length + (hasSignature ? 1 : 0);

            const percentage =
                total > 0 ? Math.round((completed / total) * 100) : 0;

            updateProgressBar(percentage);

            // DEBUGGING: Uncomment untuk debug
            // console.log('Progress Debug:', {
            //     total: total,
            //     completed: completed,
            //     percentage: percentage,
            //     requiredFields: requiredFields.length,
            //     completedFields: completedFields.length,
            //     hasSignature: hasSignature
            // });
        } catch (error) {
            console.warn("Progress update error:", error);
        }
    }

    function updateProgressBar(percentage) {
        const progressBar = document.getElementById("progressBar");
        const progressText = document.getElementById("progressText");

        if (progressBar) {
            progressBar.style.setProperty("--progress-width", `${percentage}%`);
            if (percentage === 100) {
                progressBar.classList.add("completed");
            } else {
                progressBar.classList.remove("completed");
            }
        }

        if (progressText) {
            progressText.textContent = `${percentage}% Completed`;
        }
    }

    function isFieldVisible(field) {
        try {
            let parent = field.parentElement;
            while (parent && parent !== document.body) {
                if (parent.style && parent.style.display === "none") {
                    return false;
                }
                parent = parent.parentElement;
            }
            return true;
        } catch (error) {
            return true;
        }
    }

    // =====================================
    // UTILITY FUNCTIONS
    // =====================================

    function formatFileSize(bytes) {
        if (bytes === 0) return "0 Bytes";
        const k = 1024;
        const sizes = ["Bytes", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const debounceUpdateProgress = debounce(updateProgress, 300);

    // =====================================
    // MODAL FUNCTIONS
    // =====================================

    function showLoadingModal() {
        const modal = document.getElementById("loadingModal");
        if (modal) {
            modal.classList.add("show");
        }
    }

    function hideLoadingModal() {
        const modal = document.getElementById("loadingModal");
        if (modal) {
            modal.classList.remove("show");
        }
    }

    // =====================================
    // NOTIFICATION SYSTEM
    // =====================================

    function showNotification(message, type = "info", duration = 3000) {
        // Remove existing notifications of same type
        const existing = document.querySelectorAll(`.notification-${type}`);
        existing.forEach((n) => n.remove());

        const notification = document.createElement("div");
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;

        document.body.appendChild(notification);

        requestAnimationFrame(() => {
            notification.classList.add("show");
        });

        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.remove("show");
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }

    // =====================================
    // ERROR HANDLING
    // =====================================

    window.addEventListener("error", function (e) {
        console.error("Global error:", e.error);
        if (e.error?.message?.includes("select2")) {
            return; // Ignore Select2 errors
        }
    });

    window.addEventListener("unhandledrejection", function (e) {
        console.error("Unhandled promise rejection:", e.reason);
    });

    // =====================================
    // FINAL INITIALIZATION
    // =====================================

    window.addEventListener("load", function () {
        setTimeout(() => {
            updateProgress();

            // Auto-save setup
            const form = document.getElementById("apl01Form");
            if (form) {
                let autoSaveTimeout;
                form.addEventListener("input", function () {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(() => {
                        console.log("Auto-save triggered");
                    }, 3000);
                });
            }

            console.log("Form fully loaded and ready");
        }, 500);
    });

    function customFormValidation() {
        const form = document.getElementById("apl01Form");
        if (!form) return true;

        let isValid = true;
        let firstInvalid = null;

        const visibleRequiredFields = Array.from(
            form.querySelectorAll("[required]")
        ).filter((field) => {
            // Skip hidden file inputs that have existing files
            if (field.type === "file" && field.style.display === "none") {
                const uploadCard = field.closest(".upload-card");
                if (uploadCard) {
                    const existingFile = uploadCard.querySelector(
                        ".existing-file-display"
                    );
                    const hasExistingFile =
                        existingFile && existingFile.style.display !== "none";
                    if (hasExistingFile) {
                        return false; // Skip validation for this field
                    }
                }
            }
            return isFieldVisible(field);
        });

        console.log(
            `Validating ${visibleRequiredFields.length} visible required fields`
        );

        for (const field of visibleRequiredFields) {
            let fieldValid = false;

            if (field.type === "file") {
                // Untuk file, cek existing file atau new file
                const uploadCard = field.closest(".upload-card");
                if (uploadCard) {
                    const existingFile = uploadCard.querySelector(
                        ".existing-file-display"
                    );
                    const hasExistingFile =
                        existingFile && existingFile.style.display !== "none";
                    const hasNewFile = field.files && field.files.length > 0;
                    fieldValid = hasExistingFile || hasNewFile;
                } else {
                    fieldValid = field.files && field.files.length > 0;
                }
            } else {
                fieldValid = validateField(field);
            }

            if (!fieldValid) {
                if (!firstInvalid) {
                    firstInvalid = field;
                }
                isValid = false;
            }
        }

        // Scroll to first invalid field
        if (firstInvalid) {
            firstInvalid.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            firstInvalid.focus();
        }

        return isValid;
    }
})();
