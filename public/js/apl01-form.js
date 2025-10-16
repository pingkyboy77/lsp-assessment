/**
 * APL-01 Form JavaScript - Fixed Auto-fill Profile Version
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
                deps.select2 = typeof $.fn.select2 !== "undefined" && $.fn.select2.defaults !== undefined;
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
                console.log('Retry attempt failed:', error.message);
            }

            if (attempts < maxRetries) {
                setTimeout(attempt, delay * attempts);
            }
        }

        attempt();
    }

    // =====================================
    // USER PROFILE AUTO-FILL SYSTEM - FIXED
    // =====================================

    function getUserProfile() {
        let profile = null;
        
        // Method 1: Check window.userProfile
        if (typeof window.userProfile !== 'undefined' && window.userProfile) {
            console.log('Profile found in window.userProfile:', window.userProfile);
            profile = window.userProfile;
        }
        
        // Method 2: Check embedded JSON script
        if (!profile) {
            const profileScript = document.querySelector('script[type="application/json"][data-profile]');
            if (profileScript) {
                try {
                    profile = JSON.parse(profileScript.textContent);
                    console.log('Profile found in JSON script:', profile);
                } catch (e) {
                    console.log('Failed to parse profile JSON:', e);
                }
            }
        }

        // Method 3: Check Laravel global
        if (!profile && typeof window.Laravel !== 'undefined' && window.Laravel.user) {
            profile = window.Laravel.user;
            console.log('Profile found in Laravel.user:', profile);
        }

        // Method 4: Check data attribute
        if (!profile) {
            const userDataElement = document.querySelector('[data-user-profile]');
            if (userDataElement) {
                try {
                    profile = JSON.parse(userDataElement.getAttribute('data-user-profile'));
                    console.log('Profile found in data attribute:', profile);
                } catch (e) {
                    console.log('Failed to parse data attribute:', e);
                }
            }
        }

        // Method 5: Check meta tag (NEW)
        if (!profile) {
            const metaProfile = document.querySelector('meta[name="user-profile"]');
            if (metaProfile) {
                try {
                    profile = JSON.parse(metaProfile.getAttribute('content'));
                    console.log('Profile found in meta tag:', profile);
                } catch (e) {
                    console.log('Failed to parse meta profile:', e);
                }
            }
        }

        // Method 6: Check from controller data passed via Blade (NEW)
        if (!profile && typeof userProfileData !== 'undefined') {
            profile = userProfileData;
            console.log('Profile found in userProfileData variable:', profile);
        }

        console.log('Final profile result:', profile);
        return profile;
    }

    function prefillProfileData() {
        console.log('Starting prefillProfileData...');
        
        const userProfile = getUserProfile();
        
        if (!userProfile) {
            console.log('No user profile found, skipping auto-fill');
            return;
        }

        console.log('User profile found:', userProfile);

        // Mapping field names to form inputs
        const fieldMappings = {
            'nama_lengkap': 'input[name="nama_lengkap"]',
            'nik': 'input[name="nik"]',
            'tempat_lahir': 'input[name="tempat_lahir"]',
            'tanggal_lahir': 'input[name="tanggal_lahir"]',
            'jenis_kelamin': 'input[name="jenis_kelamin"]',
            'kebangsaan': 'input[name="kebangsaan"]',
            'alamat_rumah': 'textarea[name="alamat_rumah"]',
            'kota_rumah': 'select[name="kota_rumah"]',
            'provinsi_rumah': 'input[name="provinsi_rumah"]',
            'kode_pos': 'input[name="kode_pos"]',
            'no_telp_rumah': 'input[name="no_telp_rumah"]',
            'no_hp': 'input[name="no_hp"]',
            'email': 'input[name="email"]',
            'pendidikan_terakhir': 'select[name="pendidikan_terakhir"]',
            'nama_sekolah_terakhir': 'input[name="nama_sekolah_terakhir"]',
            'nama_tempat_kerja': 'input[name="nama_tempat_kerja"]',
            'kategori_pekerjaan': 'select[name="kategori_pekerjaan"]',
            'jabatan': 'input[name="jabatan"]',
            'nama_jalan_kantor': 'textarea[name="nama_jalan_kantor"]',
            'kota_kantor': 'select[name="kota_kantor"]',
            'provinsi_kantor': 'input[name="provinsi_kantor"]',
            'kode_pos_kantor': 'input[name="kode_pos_kantor"]',
            'no_telp_kantor': 'input[name="no_telp_kantor"]'
        };

        let filledCount = 0;
        const filledFields = [];

        for (const [profileKey, selector] of Object.entries(fieldMappings)) {
            const element = document.querySelector(selector);
            const profileValue = userProfile[profileKey];
            
            console.log(`Checking field: ${profileKey}`, {
                element: element,
                profileValue: profileValue,
                elementValue: element ? element.value : 'N/A'
            });
            
            if (element && profileValue !== null && profileValue !== undefined && profileValue !== '') {
                // Check if field is empty
                const isEmpty = element.type === 'checkbox' || element.type === 'radio' 
                    ? !document.querySelector(`${selector}:checked`)
                    : !element.value.trim();

                console.log(`Field ${profileKey} isEmpty:`, isEmpty);

                if (isEmpty) {
                    if (element.type === 'radio') {
                        const radioOption = document.querySelector(`${selector}[value="${profileValue}"]`);
                        if (radioOption) {
                            radioOption.checked = true;
                            filledCount++;
                            filledFields.push(profileKey);
                            console.log(`Filled radio field: ${profileKey} = ${profileValue}`);
                        }
                    } else if (element.type === 'date' && profileValue) {
                        try {
                            const date = new Date(profileValue);
                            if (!isNaN(date.getTime())) {
                                element.value = date.toISOString().split('T')[0];
                                filledCount++;
                                filledFields.push(profileKey);
                                console.log(`Filled date field: ${profileKey} = ${element.value}`);
                            }
                        } catch (e) {
                            console.log(`Error parsing date for ${profileKey}:`, e);
                        }
                    } else {
                        element.value = profileValue;
                        filledCount++;
                        filledFields.push(profileKey);
                        console.log(`Filled field: ${profileKey} = ${profileValue}`);

                        // Trigger change for select elements
                        if (element.tagName === 'SELECT') {
                            const changeEvent = new Event('change', { bubbles: true });
                            element.dispatchEvent(changeEvent);
                            
                            // Trigger Select2 if available
                            if (checkDependencies().jquery) {
                                try {
                                    $(element).val(profileValue).trigger('change');
                                } catch (e) {
                                    console.log('Select2 trigger failed:', e);
                                }
                            }
                        }
                    }
                } else {
                    console.log(`Field ${profileKey} already has value: ${element.value}`);
                }
            } else if (!element) {
                console.log(`Element not found for selector: ${selector}`);
            } else if (!profileValue) {
                console.log(`No profile value for: ${profileKey}`);
            }
        }

        // Handle city/province auto-fill with better logging
        if (userProfile.kota_rumah) {
            console.log('Processing kota_rumah:', userProfile.kota_rumah);
            const kotaRumah = document.querySelector('select[name="kota_rumah"]');
            if (kotaRumah) {
                kotaRumah.value = userProfile.kota_rumah;
                const event = new Event('change', { bubbles: true });
                kotaRumah.dispatchEvent(event);
                
                if (checkDependencies().jquery) {
                    try {
                        $(kotaRumah).val(userProfile.kota_rumah).trigger('change').trigger('select2:select');
                    } catch (e) {
                        console.log('Select2 city trigger failed:', e);
                    }
                }
                
                console.log('Set kota_rumah value:', kotaRumah.value);
            }
        }

        if (userProfile.kota_kantor) {
            console.log('Processing kota_kantor:', userProfile.kota_kantor);
            const kotaKantor = document.querySelector('select[name="kota_kantor"]');
            if (kotaKantor) {
                kotaKantor.value = userProfile.kota_kantor;
                const event = new Event('change', { bubbles: true });
                kotaKantor.dispatchEvent(event);
                
                if (checkDependencies().jquery) {
                    try {
                        $(kotaKantor).val(userProfile.kota_kantor).trigger('change').trigger('select2:select');
                    } catch (e) {
                        console.log('Select2 office city trigger failed:', e);
                    }
                }
                
                console.log('Set kota_kantor value:', kotaKantor.value);
            }
        }

        // Update progress after auto-fill
        setTimeout(updateProgress, 500);

        // Show notification if fields were filled
        if (filledCount > 0) {
            console.log(`Auto-filled ${filledCount} fields:`, filledFields);
            showNotification(`${filledCount} field otomatis terisi dari profil Anda`, 'info', 4000);
        } else {
            console.log('No fields were auto-filled');
        }
    }

    // =====================================
    // REST OF THE ORIGINAL CODE (UNCHANGED)
    // =====================================

    // CITY PROVINCE HANDLERS
    function initializeCityProvinceHandlers() {
        if (!checkDependencies().jquery) {
            initializeCityProvinceVanilla();
            return;
        }

        // Home address auto-fill
        $(document).on("select2:select change", "#kota_rumah", function () {
            const selectedOption = $(this).find("option:selected");
            const provinceId = selectedOption.data("province-id");
            const provinceName = selectedOption.data("province-name");

            if (provinceId) {
                $("#provinsi_rumah").val(provinceId);
                $("#provinsi_rumah_display").val(provinceName);
                updateProgress();
            }
        });

        $(document).on("select2:clear", "#kota_rumah", function () {
            $("#provinsi_rumah").val("");
            $("#provinsi_rumah_display").val("");
            updateProgress();
        });

        // Office address auto-fill
        $(document).on("select2:select change", "#kota_kantor", function () {
            const selectedOption = $(this).find("option:selected");
            const provinceId = selectedOption.data("province-id");
            const provinceName = selectedOption.data("province-name");

            if (provinceId) {
                $("#provinsi_kantor").val(provinceId);
                $("#provinsi_kantor_display").val(provinceName);
                updateProgress();
            }
        });

        $(document).on("select2:clear", "#kota_kantor", function () {
            $("#provinsi_kantor").val("");
            $("#provinsi_kantor_display").val("");
            updateProgress();
        });

        // Set initial values on page load
        setTimeout(() => {
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
        }, 200);
    }

    function initializeCityProvinceVanilla() {
        const kotaRumah = document.getElementById('kota_rumah');
        const kotaKantor = document.getElementById('kota_kantor');

        if (kotaRumah) {
            kotaRumah.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const provinceId = selectedOption.getAttribute('data-province-id');
                const provinceName = selectedOption.getAttribute('data-province-name');

                if (provinceId) {
                    const provinsiRumah = document.getElementById('provinsi_rumah');
                    const provinsiRumahDisplay = document.getElementById('provinsi_rumah_display');
                    
                    if (provinsiRumah) provinsiRumah.value = provinceId;
                    if (provinsiRumahDisplay) provinsiRumahDisplay.value = provinceName;
                    
                    updateProgress();
                }
            });
        }

        if (kotaKantor) {
            kotaKantor.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const provinceId = selectedOption.getAttribute('data-province-id');
                const provinceName = selectedOption.getAttribute('data-province-name');

                if (provinceId) {
                    const provinsiKantor = document.getElementById('provinsi_kantor');
                    const provinsiKantorDisplay = document.getElementById('provinsi_kantor_display');
                    
                    if (provinsiKantor) provinsiKantor.value = provinceId;
                    if (provinsiKantorDisplay) provinsiKantorDisplay.value = provinceName;
                    
                    updateProgress();
                }
            });
        }
    }

    // FORM INITIALIZATION
    function initializeForm() {
        const form = document.getElementById("apl01Form");
        if (!form) {
            return;
        }

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
    }

    // SELECT2 INITIALIZATION
    function initializeSelect2() {
        if (!checkDependencies().select2) {
            return;
        }

        try {
            $("select").each(function () {
                const $select = $(this);

                if ($select.hasClass("select2-hidden-accessible")) {
                    return;
                }

                const placeholder = $select.find('option[value=""]').text() || "Pilih...";

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

            $("#kota_rumah, #kota_kantor").select2({
                theme: "bootstrap-5",
                placeholder: "Ketik untuk mencari kota...",
                allowClear: true,
                width: "100%",
            });

        } catch (error) {
            console.log('Select2 initialization failed:', error);
        }
    }

    // SIGNATURE PAD
   function initializeSignaturePad() {
    const canvas = document.getElementById("signature-canvas");
    const placeholder = document.getElementById("signature-placeholder");
    const clearBtn = document.getElementById("clear-signature");
    const input = document.getElementById("signature-input");

    if (!canvas || typeof SignaturePad === "undefined") {
        return;
    }

    try {
        const container = canvas.parentElement;
        const rect = container.getBoundingClientRect();
        const ratio = Math.max(window.devicePixelRatio || 1, 1);

        // Set canvas dimensions
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;

        const ctx = canvas.getContext("2d");
        ctx.scale(ratio, ratio);

        canvas.style.width = rect.width + "px";
        canvas.style.height = rect.height + "px";

        // Function to force reset canvas context
        function resetCanvasContext() {
            const ctx = canvas.getContext("2d");
            ctx.fillStyle = "rgba(255, 255, 255, 1)";
            ctx.strokeStyle = "rgba(0, 0, 0, 1)";
            ctx.lineWidth = 2;
            ctx.lineCap = "round";
            ctx.lineJoin = "round";
            
            // Fill background putih
            ctx.fillRect(0, 0, rect.width, rect.height);
        }

        // Initialize canvas background
        resetCanvasContext();

        // Destroy existing SignaturePad if exists
        if (window.signaturePad) {
            window.signaturePad.clear();
            window.signaturePad = null;
        }

        // Create new SignaturePad
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: "rgba(255, 255, 255, 1)",
            penColor: "rgba(0, 0, 0, 1)",
            minWidth: 2,
            maxWidth: 4,
            throttle: 16,
            minPointDistance: 2,
            onBegin: function () {
                if (placeholder) {
                    placeholder.classList.add("hidden");
                }
                // Pastikan pen color tetap hitam
                signaturePad._ctx.strokeStyle = "rgba(0, 0, 0, 1)";
                signaturePad._ctx.fillStyle = "rgba(0, 0, 0, 1)";
            },
            onEnd: function () {
                if (!signaturePad.isEmpty() && placeholder) {
                    placeholder.classList.add("hidden");
                }
                if (input && !signaturePad.isEmpty()) {
                    try {
                        const dataURL = signaturePad.toDataURL("image/png");
                        input.value = dataURL;
                    } catch (error) {
                        console.log('Signature save failed:', error);
                    }
                }
                updateProgress();
            },
        });

        // Override SignaturePad methods to ensure consistency
        const originalClear = signaturePad.clear;
        signaturePad.clear = function() {
            originalClear.call(this);
            resetCanvasContext();
            
            // Reset SignaturePad internal properties
            this._ctx.strokeStyle = "rgba(0, 0, 0, 1)";
            this._ctx.fillStyle = "rgba(0, 0, 0, 1)";
        };

        if (clearBtn) {
            clearBtn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (signaturePad) {
                    signaturePad.clear(); // This will call our overridden clear method
                }

                if (input) {
                    input.value = "";
                }

                if (placeholder) {
                    placeholder.classList.remove("hidden");
                }

                updateProgress();
            });
        }

        // Load existing signature
        if (input && input.value && input.value.startsWith("data:image/")) {
            const img = new Image();
            img.onload = function () {
                try {
                    // Clear everything first
                    signaturePad.clear();
                    
                    // Reset context
                    resetCanvasContext();
                    
                    // Draw existing image
                    ctx.drawImage(img, 0, 0, rect.width, rect.height);
                    
                    // Force reset SignaturePad context after drawing
                    setTimeout(() => {
                        signaturePad._ctx.strokeStyle = "rgba(0, 0, 0, 1)";
                        signaturePad._ctx.fillStyle = "rgba(0, 0, 0, 1)";
                        signaturePad._ctx.lineWidth = 2;
                        signaturePad._ctx.lineCap = "round";
                        signaturePad._ctx.lineJoin = "round";
                    }, 100);

                    if (placeholder) {
                        placeholder.classList.add("hidden");
                    }
                } catch (error) {
                    console.log('Existing signature load failed:', error);
                    resetCanvasContext();
                }
            };
            img.onerror = function () {
                console.log('Existing signature image load error');
                resetCanvasContext();
            };
            img.src = input.value;
        } else {
            if (placeholder) {
                placeholder.classList.remove("hidden");
            }
        }

        // Force context reset on window resize
        window.addEventListener('resize', function() {
            setTimeout(() => {
                if (signaturePad && signaturePad._ctx) {
                    signaturePad._ctx.strokeStyle = "rgba(0, 0, 0, 1)";
                    signaturePad._ctx.fillStyle = "rgba(0, 0, 0, 1)";
                }
            }, 100);
        });

        // Force cursor consistency - GANTI JADI PEN HITAM YANG SAMA KAYAK CSS
        canvas.addEventListener('mouseenter', function() {
            this.style.cursor = 'url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMTIgNEwxNCA2TDggMTZINEw2IDEyTDEyIDRaIiBmaWxsPSIjMDAwMDAwIi8+PHBhdGggZD0iTTEyIDRMMTQgNkwxMiA4TDEwIDZMMTIgNFoiIGZpbGw9IiMzMzMzMzMiLz48Y2lyY2xlIGN4PSIxMiIgY3k9IjE2IiByPSIxIiBmaWxsPSIjMDAwMDAwIi8+PC9zdmc+") 12 20, crosshair';
        });

        canvas.addEventListener('mousemove', function() {
            this.style.cursor = 'url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMTIgNEwxNCA2TDggMTZINEw2IDEyTDEyIDRaIiBmaWxsPSIjMDAwMDAwIi8+PHBhdGggZD0iTTEyIDRMMTQgNkwxMiA4TDEwIDZMMTIgNFoiIGZpbGw9IiMzMzMzMzMiLz48Y2lyY2xlIGN4PSIxMiIgY3k9IjE2IiByPSIxIiBmaWxsPSIjMDAwMDAwIi8+PC9zdmc+") 12 20, crosshair';
        });

        window.signaturePad = signaturePad;

    } catch (error) {
        console.log('Signature pad initialization failed:', error);
    }
}

    // CONDITIONAL SECTIONS
    function initializeConditionalSections() {
        // Training Provider section
        const kategoriRadios = document.querySelectorAll('input[name="kategori_peserta"]');
        const trainingSection = document.getElementById("training_provider_section");

        if (kategoriRadios.length && trainingSection) {
            kategoriRadios.forEach((radio) => {
                radio.addEventListener("change", function () {
                    toggleTrainingProvider(this.value, trainingSection);
                });
            });

            const checked = document.querySelector('input[name="kategori_peserta"]:checked');
            if (checked) {
                toggleTrainingProvider(checked.value, trainingSection);
            }
        }

        // Tujuan Asesmen section
        const tujuanRadios = document.querySelectorAll('input[name="tujuan_asesmen_radio"]');
        const tujuanSection = document.getElementById("tujuan_lainnya_section");

        if (tujuanRadios.length && tujuanSection) {
            tujuanRadios.forEach((radio) => {
                radio.addEventListener("change", function () {
                    toggleTujuanLainnya(this.value, tujuanSection);
                });
            });

            const checkedTujuan = document.querySelector('input[name="tujuan_asesmen_radio"]:checked');
            if (checkedTujuan) {
                toggleTujuanLainnya(checkedTujuan.value, tujuanSection);
            }
        }

        // Requirement Templates
        const templateRadios = document.querySelectorAll(".requirement-template-radio");
        if (templateRadios.length) {
            templateRadios.forEach((radio) => {
                radio.addEventListener("change", function () {
                    toggleRequirementTemplate(this.value);
                });
            });

            const checkedTemplate = document.querySelector(".requirement-template-radio:checked");
            if (checkedTemplate) {
                toggleRequirementTemplate(checkedTemplate.value);
            }
        }
    }

    function toggleTrainingProvider(value, section) {
        const isTrainingProvider = value === "training_provider";
        section.style.display = isTrainingProvider ? "block" : "none";

        const select = section.querySelector('select[name="training_provider"]');
        if (select) {
            if (isTrainingProvider) {
                select.setAttribute("required", "required");
            } else {
                select.removeAttribute("required");
                select.value = "";
                if (checkDependencies().select2 && $(select).hasClass("select2-hidden-accessible")) {
                    $(select).val(null).trigger("change");
                }
            }
        }
        updateProgress();
    }

    function toggleTujuanLainnya(value, section) {
        const isLainnya = value === "Lainnya";
        section.style.display = isLainnya ? "block" : "none";

        const textarea = section.querySelector('textarea[name="tujuan_asesmen"]');
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
        document.querySelectorAll(".template-requirements").forEach((div) => {
            div.style.display = "none";
            div.querySelectorAll("input, select, textarea").forEach((input) => {
                input.removeAttribute("required");
            });
        });

        const selectedTemplate = document.getElementById(`template_requirements_${templateId}`);
        if (selectedTemplate) {
            selectedTemplate.style.display = "block";

            selectedTemplate.querySelectorAll(".upload-card").forEach((card) => {
                const label = card.querySelector(".form-label");
                const isRequired = label && label.querySelector(".text-danger");

                if (isRequired) {
                    const fileInput = card.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.setAttribute("required", "required");
                    }
                }
            });

            selectedTemplate.querySelectorAll('input[data-required="true"], select[data-required="true"], textarea[data-required="true"]').forEach((input) => {
                input.setAttribute("required", "required");
            });
        }

        setTimeout(updateProgress, 100);
    }

    // FILE UPLOAD SYSTEM
    function initializeFileUploads() {
        document.addEventListener("change", function (e) {
            if (e.target.type === "file" && e.target.name && e.target.name.includes("requirement_item_")) {
                handleFileChange(e.target, e);
            }
        });
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
                    allowedTypes = formatMatch[1].split(",").map((f) => "." + f.trim().toLowerCase());
                }
            }
        }

        const fileExt = "." + file.name.split(".").pop().toLowerCase();

        if (file.size > maxSize) {
            showNotification(`Ukuran file maksimal ${Math.round(maxSize / 1024 / 1024)}MB`, "error");
            return false;
        }

        if (!allowedTypes.includes(fileExt)) {
            showNotification(`Format file tidak didukung. Gunakan: ${allowedTypes.join(", ")}`, "error");
            return false;
        }

        return true;
    }

    // FILE PREVIEW FUNCTIONS (Global)
    window.previewFile = function (input, itemId) {
        const file = input.files[0];
        if (!file) {
            updateProgress();
            return;
        }

        const previewContainer = document.getElementById(`preview_${itemId}`);
        if (!previewContainer) {
            updateProgress();
            return;
        }

        if (!validateFile(file, input)) {
            input.value = "";
            updateProgress();
            return;
        }

        previewContainer.style.display = "block";

        const filenameElement = document.getElementById(`filename_${itemId}`);
        const filesizeElement = document.getElementById(`filesize_${itemId}`);

        if (filenameElement) filenameElement.textContent = file.name;
        if (filesizeElement) filesizeElement.textContent = `(${formatFileSize(file.size)})`;

        const fileType = file.type.toLowerCase();
        const imgElement = document.getElementById(`img_${itemId}`);
        const pdfElement = document.getElementById(`pdf_${itemId}`);

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

        const uploadCard = input.closest(".upload-card");
        if (uploadCard) {
            const existingFile = uploadCard.querySelector(".existing-file-display");
            if (existingFile) {
                existingFile.style.display = "none";
            }

            const existingMarker = uploadCard.querySelector(`input[name*="_existing"]`);
            if (existingMarker) {
                existingMarker.remove();
            }
        }

        updateProgress();
    };

    window.removePreview = function (itemId) {
        const input = document.querySelector(`input[name*="requirement_item_${itemId}"], input[id*="${itemId}"]`);
        if (input) {
            input.value = "";

            const previewContainer = document.getElementById(`preview_${itemId}`);
            if (previewContainer) {
                previewContainer.style.display = "none";
            }

            const imgElement = document.getElementById(`img_${itemId}`);
            if (imgElement) {
                imgElement.src = "";
                imgElement.style.display = "none";
            }

            const pdfElement = document.getElementById(`pdf_${itemId}`);
            if (pdfElement) {
                pdfElement.style.display = "none";
            }

            const uploadCard = input.closest(".upload-card");
            if (uploadCard) {
                const existingFile = uploadCard.querySelector(".existing-file-display");
                if (existingFile) {
                    existingFile.style.display = "block";
                    input.style.display = "none";
                    input.removeAttribute("required");

                    if (!uploadCard.querySelector(`input[name*="_existing"]`)) {
                        const existingMarker = document.createElement("input");
                        existingMarker.type = "hidden";
                        existingMarker.name = input.name + "_existing";
                        existingMarker.value = "1";
                        uploadCard.appendChild(existingMarker);
                    }
                }
            }

            updateProgress();
        }
    };

    window.replaceExistingFile = function (itemId) {
        const uploadCard = document.querySelector(`.upload-card[data-item-id="${itemId}"]`);
        if (!uploadCard) {
            return;
        }

        const existingFileDiv = uploadCard.querySelector(".existing-file-display");
        const fileInput = uploadCard.querySelector(`input[type="file"]`);
        const existingMarker = uploadCard.querySelector(`input[name*="_existing"]`);

        if (existingFileDiv) {
            existingFileDiv.style.display = "none";
        }

        if (existingMarker) {
            existingMarker.remove();
        }

        if (fileInput) {
            fileInput.style.display = "block";

            const label = uploadCard.querySelector(".form-label");
            const isRequired = label && label.querySelector(".text-danger");

            if (isRequired) {
                fileInput.setAttribute("required", "required");
            }

            fileInput.focus();
        }

        setTimeout(updateProgress, 100);
    };

    window.removeExistingFile = function (fieldName, button) {
        if (!confirm("Yakin ingin menghapus file ini?")) return;

        const existingDoc = button.closest(".existing-doc");
        if (existingDoc) {
            existingDoc.style.display = "none";

            const deleteInput = document.createElement("input");
            deleteInput.type = "hidden";
            deleteInput.name = fieldName + "_delete";
            deleteInput.value = "1";
            existingDoc.appendChild(deleteInput);

            showNotification("File akan dihapus saat form disimpan", "info");
            updateProgress();
        }
    };

    // FORM SUBMISSION
    function handleFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const action = e.submitter?.value || "draft";

        const hiddenFileInputs = form.querySelectorAll('input[type="file"][style*="display: none"][required]');
        hiddenFileInputs.forEach((input) => {
            const uploadCard = input.closest(".upload-card");
            if (uploadCard) {
                const existingFile = uploadCard.querySelector(".existing-file-display");
                const hasExistingFile = existingFile && existingFile.style.display !== "none";
                if (hasExistingFile) {
                    input.removeAttribute("required");
                    input.dataset.tempRemoved = "true";
                }
            }
        });

        if (typeof signaturePad !== "undefined" && signaturePad && !signaturePad.isEmpty()) {
            const signatureInput = document.getElementById("signature-input");
            if (signatureInput) {
                signatureInput.value = signaturePad.toDataURL("image/png");
            }
        }

        let hiddenAction = form.querySelector("input[name='action']");
        if (!hiddenAction) {
            hiddenAction = document.createElement("input");
            hiddenAction.type = "hidden";
            hiddenAction.name = "action";
            form.appendChild(hiddenAction);
        }
        hiddenAction.value = action;

        if (action === "submit") {
            if (!validateFormForSubmission()) {
                hiddenFileInputs.forEach((input) => {
                    if (input.dataset.tempRemoved === "true") {
                        input.setAttribute("required", "required");
                        delete input.dataset.tempRemoved;
                    }
                });
                return false;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "Konfirmasi Submit",
                    text: "Apakah Anda yakin ingin submit APL 01? Setelah submit data tidak dapat diubah.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Submit",
                    cancelButtonText: "Batal",
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
                    }
                });
            } else {
                if (confirm("Apakah Anda yakin ingin submit APL 01? Setelah submit data tidak dapat diubah.")) {
                    showLoadingModal();
                    form.submit();
                }
            }

            return false;
        }

        showLoadingModal();
        form.submit();
    }

    // FORM VALIDATION
    function initializeValidation() {
        const form = document.getElementById("apl01Form");
        if (!form) return;

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

        const phoneInputs = form.querySelectorAll('input[name="no_hp"], input[name="no_telp_rumah"], input[name="no_telp_kantor"]');
        phoneInputs.forEach((input) => {
            input.addEventListener("input", function () {
                let value = this.value.replace(/\D/g, "");
                if (value.length > 15) value = value.slice(0, 15);
                this.value = value;
            });
        });

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
    }

    function validateField(field) {
        if (!field.hasAttribute("required")) return true;

        let isValid = false;

        if (field.type === "checkbox" || field.type === "radio") {
            const checked = document.querySelector(`input[name="${field.name}"]:checked`);
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

        const signatureInput = document.getElementById("signature-input");
        const existingSignature = document.querySelector('input[name="existing_signature"]') || document.querySelector(".existing-signature");

        if (!existingSignature && signaturePad && signaturePad.isEmpty()) {
            errors.push("Tanda tangan digital diperlukan");
            const canvas = document.getElementById("signature-canvas");
            if (canvas) {
                canvas.scrollIntoView({ behavior: "smooth", block: "center" });
            }
            isValid = false;
        } else if (!existingSignature && (!signatureInput || !signatureInput.value)) {
            errors.push("Tanda tangan digital diperlukan");
            isValid = false;
        }

        if (!customFormValidation()) {
            isValid = false;
        }

        if (errors.length > 0) {
            showNotification(errors[0], "error");
        } else if (!isValid) {
            showNotification("Mohon lengkapi semua field yang wajib", "error");
        }

        return isValid;
    }

    function showFieldError(field, message) {
        field.classList.add("error");

        if (checkDependencies().jquery && $(field).hasClass("select2-hidden-accessible")) {
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

        if (checkDependencies().jquery && $(field).hasClass("select2-hidden-accessible")) {
            $(field).next(".select2-container").removeClass("error");
        }

        const errorElement = field.parentNode.querySelector(".error-message:not([data-server-error])");
        if (errorElement) {
            errorElement.remove();
        }
    }

    // PROGRESS TRACKING
    function initializeProgressTracking() {
        updateProgress();

        const form = document.getElementById("apl01Form");
        if (form) {
            form.addEventListener("input", debounceUpdateProgress);
            form.addEventListener("change", debounceUpdateProgress);
        }
    }

    function updateProgress() {
        try {
            const form = document.getElementById("apl01Form");
            if (!form) return;

            const requiredFields = Array.from(form.querySelectorAll("[required]")).filter(isFieldVisible);
            const completedFields = requiredFields.filter((field) => {
                if (field.type === "checkbox" || field.type === "radio") {
                    return form.querySelector(`input[name="${field.name}"]:checked`) !== null;
                }

                if (field.type === "file") {
                    const uploadCard = field.closest(".upload-card");
                    if (uploadCard) {
                        const existingFile = uploadCard.querySelector(".existing-file-display");
                        const hasExistingFile = existingFile && existingFile.style.display !== "none";
                        const hasNewFile = field.files && field.files.length > 0;

                        return hasExistingFile || hasNewFile;
                    }
                    return field.files && field.files.length > 0;
                }

                return field.value.trim() !== "";
            });

            const hasExistingSignature = document.querySelector('input[name="existing_signature"]');
            const hasNewSignature = signaturePad && !signaturePad.isEmpty();
            const hasSignature = hasExistingSignature || hasNewSignature;

            const signatureWeight = 1;
            const total = requiredFields.length + signatureWeight;
            const completed = completedFields.length + (hasSignature ? 1 : 0);

            const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;

            updateProgressBar(percentage);

        } catch (error) {
            console.log('Progress update failed:', error);
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

    function customFormValidation() {
        const form = document.getElementById("apl01Form");
        if (!form) return true;

        let isValid = true;
        let firstInvalid = null;

        const visibleRequiredFields = Array.from(form.querySelectorAll("[required]")).filter((field) => {
            if (field.type === "file" && field.style.display === "none") {
                const uploadCard = field.closest(".upload-card");
                if (uploadCard) {
                    const existingFile = uploadCard.querySelector(".existing-file-display");
                    const hasExistingFile = existingFile && existingFile.style.display !== "none";
                    if (hasExistingFile) {
                        return false;
                    }
                }
            }
            return isFieldVisible(field);
        });

        for (const field of visibleRequiredFields) {
            let fieldValid = false;

            if (field.type === "file") {
                const uploadCard = field.closest(".upload-card");
                if (uploadCard) {
                    const existingFile = uploadCard.querySelector(".existing-file-display");
                    const hasExistingFile = existingFile && existingFile.style.display !== "none";
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

        if (firstInvalid) {
            firstInvalid.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            firstInvalid.focus();
        }

        return isValid;
    }

    // UTILITY FUNCTIONS
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

    // MODAL FUNCTIONS
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

    // NOTIFICATION SYSTEM
    function showNotification(message, type = "info", duration = 3000) {
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

    // ERROR HANDLING
    window.addEventListener("error", function (e) {
        if (e.error?.message?.includes("select2")) {
            return;
        }
    });

    window.addEventListener("unhandledrejection", function (e) {
        // Silent handling
    });

    // GLOBAL FUNCTIONS FOR BACKWARD COMPATIBILITY
    window.initializeCityProvinceHandlers = initializeCityProvinceHandlers;
    window.prefillProfileData = prefillProfileData;
    window.updateProgress = updateProgress;

    // MAIN INITIALIZATION
    document.addEventListener("DOMContentLoaded", function () {
        console.log('DOM Content Loaded - Starting APL01 Form initialization...');
        
        setTimeout(() => {
            const deps = checkDependencies();
            console.log('Dependencies check:', deps);

            initializeForm();
            initializeConditionalSections();
            initializeFileUploads();
            initializeValidation();
            initializeProgressTracking();
            initializeCityProvinceHandlers();

            if (deps.jquery) {
                if (deps.select2) {
                    initializeSelect2();
                } else {
                    initializeWithRetry(() => {
                        const currentDeps = checkDependencies();
                        if (currentDeps.select2) {
                            initializeSelect2();
                            return true;
                        }
                        return false;
                    }, 5, 300);
                }
            }

            if (deps.signaturePad) {
                initializeSignaturePad();
            }

            // PERBAIKAN: Panggil prefillProfileData dengan delay lebih lama
            setTimeout(() => {
                console.log('Calling prefillProfileData...');
                prefillProfileData();
            }, 1000); // Increased delay

        }, 100);
    });

    // FINAL INITIALIZATION
    window.addEventListener("load", function () {
        console.log('Window loaded - Final initialization...');
        
        setTimeout(() => {
            updateProgress();

            const form = document.getElementById("apl01Form");
            if (form) {
                let autoSaveTimeout;
                form.addEventListener("input", function () {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(() => {
                        // Auto-save logic can be added here
                    }, 3000);
                });
            }

        }, 500);
    });

})();