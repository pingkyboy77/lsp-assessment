@extends('layouts.admin')

@section('title', 'Create System Setting')

@section('content')
    <div class="main-card">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 text-dark fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Create System Setting
                    </h5>
                    <p class="mb-0 text-muted">Add new system configuration</p>
                </div>
                <a href="{{ route('admin.system-settings.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        <div class="card-body m-3">
            <form action="{{ route('admin.system-settings.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="key" class="form-label">Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('key') is-invalid @enderror" 
                                   id="key" name="key" value="{{ old('key') }}" 
                                   placeholder="e.g., registration_number_format">
                            @error('key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                <option value="">Select Type</option>
                                <option value="string" {{ old('type') == 'string' ? 'selected' : '' }}>String</option>
                                <option value="integer" {{ old('type') == 'integer' ? 'selected' : '' }}>Integer</option>
                                <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean</option>
                                <option value="json" {{ old('type') == 'json' ? 'selected' : '' }}>JSON</option>
                                <option value="float" {{ old('type') == 'float' ? 'selected' : '' }}>Float</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('value') is-invalid @enderror" 
                                      id="value" name="value" rows="3" 
                                      placeholder="Enter the setting value">{{ old('value') }}</textarea>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                For JSON type, enter valid JSON format. For boolean, use 1 or 0.
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="group" class="form-label">Group</label>
                            <input type="text" class="form-control @error('group') is-invalid @enderror" 
                                   id="group" name="group" value="{{ old('group') }}" 
                                   placeholder="e.g., registration, email"
                                   list="groupList">
                            <datalist id="groupList">
                                @foreach($groups as $group)
                                    <option value="{{ $group }}">
                                @endforeach
                            </datalist>
                            @error('group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="2" 
                              placeholder="Brief description of this setting">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check-lg me-2"></i>Create Setting
                    </button>
                    <a href="{{ route('admin.system-settings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection