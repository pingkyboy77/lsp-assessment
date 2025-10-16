@extends('layouts.admin')

@section('title', 'Buat APL 02 Baru')

@section('content')
    <div class="main-card">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header Section -->
        <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="m-0">BUAT APL 02 BARU</h4>
                <small class="text-muted">Pilih APL 01 yang sudah disetujui untuk membuat APL 02</small>
            </div>
            <div>
                <a href="{{ route('asesi.apl02.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        @if($availableApl01s->count() > 0)
            <form method="POST" action="{{ route('asesi.apl02.store') }}">
                @csrf
                
                <div class="card m-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Pilih APL 01</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($availableApl01s as $apl01)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border apl01-option {{ $loop->first ? 'border-primary' : '' }}" 
                                         data-apl01-id="{{ $apl01->id }}">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="apl_01_id" 
                                                       id="apl01_{{ $apl01->id }}" value="{{ $apl01->id }}"
                                                       {{ $loop->first ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="apl01_{{ $apl01->id }}">
                                                    <div class="fw-bold text-primary mb-2">{{ $apl01->nomor_apl_01 }}</div>
                                                    <div class="small text-muted mb-2">{{ $apl01->nama_lengkap }}</div>
                                                    
                                                    <div class="scheme-info">
                                                        <div class="fw-bold small">{{ $apl01->certificationScheme->nama }}</div>
                                                        <div class="text-muted small">{{ $apl01->certificationScheme->jenjang }}</div>
                                                    </div>
                                                    
                                                    <div class="mt-2">
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle"></i> Disetujui
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="small text-muted mt-2">
                                                        Disetujui: {{ $apl01->reviewed_at ? $apl01->reviewed_at->format('d/m/Y') : '-' }}
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Preview Scheme Info -->
                <div class="card m-3" id="schemePreview">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Preview Skema Sertifikasi</h6>
                    </div>
                    <div class="card-body" id="schemeDetails">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card m-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('asesi.apl02.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Batal
                            </a>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus me-1"></i>Buat APL 02
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <div class="card m-3">
                <div class="card-body text-center py-5">
                    <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                    <h5 class="mt-3">Tidak Ada APL 01 yang Tersedia</h5>
                    <p class="text-muted">Anda perlu memiliki APL 01 yang sudah disetujui untuk dapat membuat APL 02.</p>
                    
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle me-2"></i>Langkah-langkah:</h6>
                                <ol class="mb-0 text-start">
                                    <li>Buat APL 01 terlebih dahulu</li>
                                    <li>Submit APL 01 untuk direview</li>
                                    <li>Tunggu APL 01 disetujui oleh reviewer</li>
                                    <li>Setelah disetujui, Anda dapat membuat APL 02</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('asesi.apl01.index') }}" class="btn btn-primary me-2">
                            <i class="bi bi-file-earmark-plus me-1"></i>Lihat APL 01
                        </a>
                        <a href="{{ route('asesi.certification-schemes.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus me-1"></i>Buat APL 01 Baru
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .apl01-option {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .apl01-option:hover {
            border-color: #0d6efd !important;
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.15);
        }
        
        .apl01-option.border-primary {
            border-color: #0d6efd !important;
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.15);
        }
        
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .scheme-info {
            border-left: 3px solid #dee2e6;
            padding-left: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[name="apl_01_id"]');
            const schemeDetails = document.getElementById('schemeDetails');
            
            // APL 01 data for preview
            const apl01Data = @json($availableApl01s->toArray());
            
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Update card borders
                    document.querySelectorAll('.apl01-option').forEach(card => {
                        card.classList.remove('border-primary');
                    });
                    
                    const selectedCard = this.closest('.apl01-option');
                    selectedCard.classList.add('border-primary');
                    
                    // Update scheme preview
                    updateSchemePreview(this.value);
                });
            });
            
            function updateSchemePreview(apl01Id) {
                const selectedApl01 = apl01Data.find(apl => apl.id == apl01Id);
                
                if (selectedApl01 && selectedApl01.certification_scheme) {
                    const scheme = selectedApl01.certification_scheme;
                    
                    schemeDetails.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">${scheme.nama}</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted">Kode Skema:</td>
                                        <td class="fw-bold">${scheme.code_1}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jenjang:</td>
                                        <td><span class="badge bg-info">${scheme.jenjang}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status:</td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Informasi APL 01</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted">Nomor:</td>
                                        <td class="fw-bold">${selectedApl01.nomor_apl_01}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nama:</td>
                                        <td>${selectedApl01.nama_lengkap}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Email:</td>
                                        <td>${selectedApl01.email}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="alert alert-success mt-3">
                            <i class="bi bi-check-circle me-2"></i>
                            APL 02 akan dibuat berdasarkan unit-unit kompetensi dalam skema ini.
                        </div>
                    `;
                }
            }
            
            // Initialize with first selection
            if (radioButtons.length > 0) {
                updateSchemePreview(radioButtons[0].value);
            }
        });
    </script>
@endpush