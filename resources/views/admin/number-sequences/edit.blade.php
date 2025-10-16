@extends('layouts.admin')
@section('title', 'Edit Number Sequence')

@section('content')
    <div class="main-card">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Edit Number Sequence
                    </h5>
                    <p class="mb-0 text-muted">Modify automatic number generation settings</p>
                </div>
                <a href="{{ route('admin.number-sequences.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <div class="card-body m-3">
            <!-- Current Info Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Current Sequence Info</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Current Number:</strong> {{ $numberSequence->current_number }}
                            </div>
                            <div class="col-md-3">
                                <strong>Last Generated:</strong> {{ $numberSequence->updated_at->format('d M Y H:i') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $numberSequence->is_active ? 'success' : 'danger' }}">
                                    {{ $numberSequence->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Preview:</strong> 
                                <code class="bg-light px-2 py-1 rounded text-primary">
                                    @php
                                        try {
                                            echo $numberSequence->generatePreview(1)[0];
                                        } catch (\Exception $e) {
                                            echo 'Error';
                                        }
                                    @endphp
                                </code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.number-sequences.update', $numberSequence->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sequence_key" class="form-label">Sequence Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sequence_key') is-invalid @enderror" 
                                   id="sequence_key" name="sequence_key" 
                                   value="{{ old('sequence_key', $numberSequence->sequence_key) }}" 
                                   placeholder="e.g., apl_01_registration">
                            @error('sequence_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Unique identifier for this sequence</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name', $numberSequence->name) }}" 
                                   placeholder="e.g., APL-01 Registration Number">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="prefix" class="form-label">Prefix</label>
                            <input type="text" class="form-control @error('prefix') is-invalid @enderror" 
                                   id="prefix" name="prefix" 
                                   value="{{ old('prefix', $numberSequence->prefix) }}" 
                                   placeholder="e.g., APL">
                            @error('prefix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="suffix" class="form-label">Suffix</label>
                            <input type="text" class="form-control @error('suffix') is-invalid @enderror" 
                                   id="suffix" name="suffix" 
                                   value="{{ old('suffix', $numberSequence->suffix) }}" 
                                   placeholder="Optional">
                            @error('suffix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="digits" class="form-label">Number Digits <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('digits') is-invalid @enderror" 
                                   id="digits" name="digits" 
                                   value="{{ old('digits', $numberSequence->digits) }}" 
                                   min="1" max="10">
                            @error('digits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">6 = 000001, 4 = 0001</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="separator" class="form-label">Separator <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('separator') is-invalid @enderror" 
                                   id="separator" name="separator" 
                                   value="{{ old('separator', $numberSequence->separator) }}" 
                                   maxlength="3">
                            @error('separator')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">- or / or .</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="use_year" name="use_year" 
                                       value="1" {{ old('use_year', $numberSequence->use_year) ? 'checked' : '' }}>
                                <label class="form-check-label" for="use_year">
                                    Use Year in Number
                                </label>
                            </div>
                            
                            <select class="form-select @error('year_format') is-invalid @enderror" 
                                    id="year_format" name="year_format">
                                <option value="Y" {{ old('year_format', $numberSequence->year_format) == 'Y' ? 'selected' : '' }}>Full Year (2024)</option>
                                <option value="y" {{ old('year_format', $numberSequence->year_format) == 'y' ? 'selected' : '' }}>Short Year (24)</option>
                                <option value="Ym" {{ old('year_format', $numberSequence->year_format) == 'Ym' ? 'selected' : '' }}>Year-Month (202401)</option>
                            </select>
                            @error('year_format')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Reset Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="reset_yearly" name="reset_yearly" 
                                       value="1" {{ old('reset_yearly', $numberSequence->reset_yearly) ? 'checked' : '' }}>
                                <label class="form-check-label" for="reset_yearly">
                                    Reset counter every year
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="reset_monthly" name="reset_monthly" 
                                       value="1" {{ old('reset_monthly', $numberSequence->reset_monthly) ? 'checked' : '' }}>
                                <label class="form-check-label" for="reset_monthly">
                                    Reset counter every month
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="format_template" class="form-label">Format Template <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('format_template') is-invalid @enderror" 
                           id="format_template" name="format_template" 
                           value="{{ old('format_template', $numberSequence->format_template) }}">
                    @error('format_template')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Available variables: {prefix}, {suffix}, {year}, {number}, {separator}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="start_number" class="form-label">Start Number</label>
                            <input type="number" class="form-control @error('start_number') is-invalid @enderror" 
                                   id="start_number" name="start_number" 
                                   value="{{ old('start_number', $numberSequence->start_number) }}" 
                                   min="0">
                            @error('start_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Only affects new sequences or after reset</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="current_number" class="form-label">Current Number</label>
                            <input type="number" class="form-control @error('current_number') is-invalid @enderror" 
                                   id="current_number" name="current_number" 
                                   value="{{ old('current_number', $numberSequence->current_number) }}" 
                                   min="0">
                            @error('current_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <strong class="text-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Be careful! This affects next generation
                                </strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $numberSequence->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="2" 
                              placeholder="Brief description of this sequence">{{ old('description', $numberSequence->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Preview Section -->
                <div class="mb-4">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-eye me-2"></i>Live Preview
                                <button type="button" id="refreshPreview" class="btn btn-outline-primary btn-sm ms-2">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="previewContainer">
                                <div class="d-flex align-items-center">
                                    <span class="me-3">Next numbers will be:</span>
                                    <div id="previewNumbers" class="d-flex gap-2 flex-wrap">
                                        <!-- Preview numbers will be generated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-2"></i>Update Sequence
                    </button>
                    <button type="button" id="testSequence" class="btn btn-outline-info">
                        <i class="bi bi-play me-2"></i>Test Generate
                    </button>
                    <a href="{{ route('admin.number-sequences.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const useYearCheckbox = document.getElementById('use_year');
            const yearFormatSelect = document.getElementById('year_format');
            const resetYearlyCheckbox = document.getElementById('reset_yearly');
            const resetMonthlyCheckbox = document.getElementById('reset_monthly');
            const testSequenceBtn = document.getElementById('testSequence');
            const refreshPreviewBtn = document.getElementById('refreshPreview');
            
            function toggleYearOptions() {
                const isChecked = useYearCheckbox.checked;
                yearFormatSelect.disabled = !isChecked;
                resetYearlyCheckbox.disabled = !isChecked;
                resetMonthlyCheckbox.disabled = !isChecked;
            }
            
            useYearCheckbox.addEventListener('change', toggleYearOptions);
            toggleYearOptions(); // Initial state
            
            // Prevent both reset options being checked
            resetMonthlyCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    resetYearlyCheckbox.checked = false;
                }
            });
            
            resetYearlyCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    resetMonthlyCheckbox.checked = false;
                }
            });

            // Test sequence functionality
            testSequenceBtn.addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testing...';
                btn.disabled = true;

                fetch(`{{ route('admin.number-sequences.test', $numberSequence->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Test successful! Preview: ' + data.previews.join(', '));
                        updatePreviewNumbers(data.previews);
                    } else {
                        showToast('error', 'Test failed: ' + data.message);
                    }
                })
                .catch(error => {
                    showToast('error', 'Error: ' + error.message);
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });

            // Refresh preview functionality
            refreshPreviewBtn.addEventListener('click', function() {
                updatePreview();
            });

            // Update preview when form changes
            const formInputs = document.querySelectorAll('#prefix, #suffix, #digits, #separator, #format_template, #current_number');
            formInputs.forEach(input => {
                input.addEventListener('input', debounce(updatePreview, 500));
            });

            const formCheckboxes = document.querySelectorAll('#use_year, #reset_yearly, #reset_monthly');
            formCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updatePreview);
            });

            document.getElementById('year_format').addEventListener('change', updatePreview);

            // Initial preview update
            updatePreview();

            function updatePreview() {
                // This is a simplified preview - in a real scenario you'd want to make an AJAX call
                // to generate actual preview numbers based on current form values
                const prefix = document.getElementById('prefix').value || '';
                const suffix = document.getElementById('suffix').value || '';
                const digits = parseInt(document.getElementById('digits').value) || 6;
                const separator = document.getElementById('separator').value || '-';
                const currentNumber = parseInt(document.getElementById('current_number').value) || 1;
                const useYear = document.getElementById('use_year').checked;
                const yearFormat = document.getElementById('year_format').value;

                const previews = [];
                for (let i = 1; i <= 3; i++) {
                    let preview = '';
                    const number = (currentNumber + i).toString().padStart(digits, '0');
                    
                    if (prefix) preview += prefix + separator;
                    if (useYear) {
                        const year = new Date().getFullYear();
                        let yearStr = year.toString();
                        if (yearFormat === 'y') yearStr = yearStr.slice(-2);
                        else if (yearFormat === 'Ym') yearStr += (new Date().getMonth() + 1).toString().padStart(2, '0');
                        preview += yearStr + separator;
                    }
                    preview += number;
                    if (suffix) preview += separator + suffix;
                    
                    previews.push(preview);
                }

                updatePreviewNumbers(previews);
            }

            function updatePreviewNumbers(previews) {
                const container = document.getElementById('previewNumbers');
                container.innerHTML = previews.map(preview => 
                    `<code class="bg-white px-2 py-1 rounded text-primary border">${preview}</code>`
                ).join('');
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

            function showToast(type, message) {
                // Simple toast notification
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }
        });
    </script>
@endsection