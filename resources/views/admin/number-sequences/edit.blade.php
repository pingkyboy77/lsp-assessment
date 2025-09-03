@extends('layouts.admin')
@section('title', 'Create Number Sequence')

@section('content')
    <div class="main-card">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Create Number Sequence
                    </h5>
                    <p class="mb-0 text-muted">Setup new automatic number generation</p>
                </div>
                <a href="{{ route('admin.number-sequences.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <div class="card-body m-3">
            <form action="{{ route('admin.number-sequences.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sequence_key" class="form-label">Sequence Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sequence_key') is-invalid @enderror" 
                                   id="sequence_key" name="sequence_key" value="{{ old('sequence_key') }}" 
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
                                   id="name" name="name" value="{{ old('name') }}" 
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
                                   id="prefix" name="prefix" value="{{ old('prefix') }}" 
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
                                   id="suffix" name="suffix" value="{{ old('suffix') }}" 
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
                                   id="digits" name="digits" value="{{ old('digits', 6) }}" 
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
                                   id="separator" name="separator" value="{{ old('separator', '-') }}" 
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
                                       value="1" {{ old('use_year', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="use_year">
                                    Use Year in Number
                                </label>
                            </div>
                            
                            <select class="form-select @error('year_format') is-invalid @enderror" 
                                    id="year_format" name="year_format">
                                <option value="Y" {{ old('year_format', 'Y') == 'Y' ? 'selected' : '' }}>Full Year (2024)</option>
                                <option value="y" {{ old('year_format') == 'y' ? 'selected' : '' }}>Short Year (24)</option>
                                <option value="Ym" {{ old('year_format') == 'Ym' ? 'selected' : '' }}>Year-Month (202401)</option>
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
                                       value="1" {{ old('reset_yearly', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="reset_yearly">
                                    Reset counter every year
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="reset_monthly" name="reset_monthly" 
                                       value="1" {{ old('reset_monthly') ? 'checked' : '' }}>
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
                           value="{{ old('format_template', '{prefix}{separator}{year}{separator}{number}') }}">
                    @error('format_template')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Available variables: {prefix}, {suffix}, {year}, {number}, {separator}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_number" class="form-label">Start Number</label>
                            <input type="number" class="form-control @error('start_number') is-invalid @enderror" 
                                   id="start_number" name="start_number" value="{{ old('start_number', 1) }}" 
                                   min="0">
                            @error('start_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
                              placeholder="Brief description of this sequence">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check-lg me-2"></i>Create Sequence
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
        });
    </script>
@endsection