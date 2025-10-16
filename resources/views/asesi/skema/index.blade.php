@extends('layouts.admin')

@section('content')
<div class="container-fluid p-0">
    @php
        // Check if user profile is complete
        $userProfile = auth()->user()->profile ?? null;
        $isProfileIncomplete = !$userProfile || 
            !$userProfile->nama_lengkap || 
            !$userProfile->nik || 
            !$userProfile->tempat_lahir || 
            !$userProfile->tanggal_lahir;
        
        $groupedSchemes = $schemes->groupBy(function($scheme) {
            return $scheme->field && $scheme->field->bidang 
                ? $scheme->field->bidang 
                : 'Tidak Ada Bidang';
        });
    @endphp
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <!-- Profile Incomplete Warning -->
    @if($isProfileIncomplete)
    <div class="alert alert-warning d-flex align-items-center mb-3">
        <div class="flex-grow-1">
            <h6 class="mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Profil Belum Lengkap</h6>
            <small>Lengkapi profil Anda untuk dapat mendaftar skema sertifikasi</small>
        </div>
        <a href="{{ route('asesi.data-pribadi.index') }}" class="btn btn-warning btn-sm">
            <i class="fas fa-user-edit me-1"></i>Lengkapi Profil
        </a>
    </div>
    @endif

    <div class="row g-0">
        <!-- Mobile Toggle Button -->
        <div class="d-lg-none p-3">
            <button class="btn btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#navCollapse">
                <i class="fas fa-bars me-2"></i>Pilih Kategori
            </button>
        </div>

        <!-- Left Navigation Panel -->
        <div class="col-lg-3 col-12">
            <div class="collapse d-lg-block" id="navCollapse">
                <div class="bg-white border-end h-100">
                    <ul class="nav flex-column" id="bidangTab" role="tablist">
                        @foreach($groupedSchemes as $bidangName => $group)
                            <li class="nav-item">
                                <button class="nav-link category-tab w-100 text-start {{ $loop->first ? 'active' : '' }}" 
                                    id="tab-{{ Str::slug($bidangName) }}" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#content-{{ Str::slug($bidangName) }}" 
                                    type="button" role="tab"
                                    data-bs-dismiss="collapse" 
                                    data-bs-target="#navCollapse">
                                    {{ $bidangName }}
                                    <span class="badge bg-secondary ms-auto">{{ $group->count() }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="col-lg-9 col-12">
            <div class="p-3">
                <!-- Tab Content -->
                <div class="tab-content" id="bidangTabContent">
                    @foreach($groupedSchemes as $bidangName => $group)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                            id="content-{{ Str::slug($bidangName) }}" 
                            role="tabpanel">

                            <!-- Cards Grid -->
                            <div class="row g-3">
                                @foreach($group as $skema)
                                    @php
                                        // Check if user already has APL 01 for this scheme
                                        $existingApl = auth()->user()->apl01Registrations()
                                            ->where('certification_scheme_id', $skema->id)
                                            ->first();
                                            
                                        // Check if units and kelompok kerjas are complete
                                        $hasUnits = $skema->units()->count() > 0;
                                        $hasKelompokKerjas = $skema->kelompokKerjas()->count() > 0;
                                        $isSchemeComplete = $hasUnits && $hasKelompokKerjas;
                                    @endphp
                                    <div class="col-md-6 col-lg-6">
                                        <div class="card h-100 scheme-card {{ !$isSchemeComplete ? 'coming-soon' : '' }}">
                                            <div class="card-header p-0">
                                                <div class="scheme-top-line {{ !$isSchemeComplete ? 'bg-info' : 'bg-primary' }}"></div>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                {{-- <div class="mb-3">
                                                    <span class="badge {{ !$isSchemeComplete ? 'bg-info' : 'bg-primary' }} mb-2">
                                                        {{ $skema->code_1 ?? 'N/A' }}
                                                    </span>
                                                </div> --}}
                                                
                                                <h6 class="card-title flex-grow-1 mb-4">{{ $skema->nama }}</h6>
                                                
                                                @if(!$isSchemeComplete)
                                                    <div class="text-center mb-3">
                                                        <div class="badge bg-info mb-2">
                                                            <i class="fas fa-clock me-1"></i>Coming Soon
                                                        </div>
                                                        {{-- <small class="text-muted d-block">
                                                            @if(!$hasUnits && !$hasKelompokKerjas)
                                                                Unit kompetensi dan kelompok kerja sedang disiapkan
                                                            @elseif(!$hasUnits)
                                                                Unit kompetensi sedang disiapkan
                                                            @else
                                                                Kelompok kerja sedang disiapkan
                                                            @endif
                                                        </small> --}}
                                                    </div>
                                                    <button class="btn btn-info w-100" disabled onclick="showComingSoonModal('{{ $skema->nama }}')">
                                                        <i class="fas fa-clock me-1"></i>Coming Soon
                                                    </button>
                                                @else
                                                    @if($existingApl)
                                                        <div class="text-center mb-3">
                                                            <small class="text-muted d-block">APL 01: {{ $existingApl->nomor_apl_01 }}</small>
                                                            <span class="badge bg-{{ $existingApl->statusColor }}">{{ $existingApl->statusText }}</span>
                                                        </div>
                                                        
                                                        @if($existingApl->isEditable)
                                                            <a href="{{ route('asesi.apl01.edit', $existingApl->id) }}" 
                                                               class="btn btn-success w-100">
                                                                <i class="fas fa-edit me-1"></i>Lanjutkan
                                                            </a>
                                                        @else
                                                            <a href="{{ route('asesi.apl01.show', $existingApl->id) }}" 
                                                               class="btn btn-info w-100">
                                                                <i class="fas fa-eye me-1"></i>Lihat Detail
                                                            </a>
                                                        @endif
                                                    @else
                                                        @if($isProfileIncomplete)
                                                            <button class="btn btn-secondary w-100" 
                                                                    title="Lengkapi profil untuk dapat mendaftar"
                                                                    onclick="showProfileIncompleteModal()">
                                                                <i class="fas fa-user-times me-1"></i>Lengkapi Profil Dulu
                                                            </button>
                                                        @else
                                                            <a href="{{ route('asesi.apl01.create', $skema->id) }}" 
                                                               class="btn btn-primary w-100">
                                                                <i class="fas fa-edit me-1"></i>Daftar
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Incomplete Modal -->
<div class="modal fade" id="profileIncompleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>Profil Belum Lengkap
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-slash text-muted display-1"></i>
                </div>
                <h6 class="mb-3">Anda belum dapat mendaftar skema sertifikasi</h6>
                <p class="text-muted mb-4">
                    Untuk dapat mendaftar dan mengakses semua fitur, silakan lengkapi profil Anda terlebih dahulu.
                </p>
                <div class="mb-4">
                    <small class="text-muted d-block mb-2">Data yang diperlukan:</small>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle {{ $userProfile && $userProfile->nama_lengkap ? 'text-success' : 'text-muted' }} me-2"></i>
                            <span>Nama Lengkap</span>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle {{ $userProfile && $userProfile->nik ? 'text-success' : 'text-muted' }} me-2"></i>
                            <span>NIK</span>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle {{ $userProfile && $userProfile->tempat_lahir ? 'text-success' : 'text-muted' }} me-2"></i>
                            <span>Tempat Lahir</span>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle {{ $userProfile && $userProfile->tanggal_lahir ? 'text-success' : 'text-muted' }} me-2"></i>
                            <span>Tanggal Lahir</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('asesi.data-pribadi.index') }}" class="btn btn-primary">
                    <i class="fas fa-user-edit me-1"></i>Lengkapi Profil
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Coming Soon Modal -->
<div class="modal fade" id="comingSoonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-info">
                    <i class="fas fa-clock me-2"></i>Coming Soon
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-construction text-info display-1"></i>
                </div>
                <h6 class="mb-3" id="comingSoonSchemeName">Skema Sertifikasi</h6>
                <p class="text-muted mb-4">
                    Skema sertifikasi ini sedang dalam tahap persiapan. Unit kompetensi dan kelompok kerja sedang disiapkan oleh tim kami.
                </p>
                <div class="row text-center mb-4">
                    <div class="col-4">
                        <i class="fas fa-tasks text-info mb-2 d-block"></i>
                        <small>Unit Kompetensi</small>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-users text-info mb-2 d-block"></i>
                        <small>Kelompok Kerja</small>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-file-alt text-info mb-2 d-block"></i>
                        <small>Materi</small>
                    </div>
                </div>
                <p class="text-muted">
                    <i class="fas fa-bell me-1"></i>
                    Kami akan memberitahu Anda segera setelah skema ini tersedia!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-info" onclick="subscribeNotification()">
                    <i class="fas fa-bell me-1"></i>Ingatkan Saya
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Custom Brand Colors */
:root {
    --brand-primary: #0d3b66;
    --brand-secondary: #1a5490;
    --brand-accent: #ffd700;
    --brand-success: #28a745;
    --brand-info: #17a2b8;
}

/* Override Bootstrap Primary Colors */
.btn-primary {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)) !important;
    border-color: var(--brand-primary) !important;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-primary:hover, .btn-primary:focus {
    background: linear-gradient(135deg, var(--brand-secondary), #2a6bb3) !important;
    border-color: var(--brand-secondary) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(13, 59, 102, 0.3);
}

.bg-primary {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)) !important;
}

.badge.bg-primary {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)) !important;
    box-shadow: 0 2px 8px rgba(13, 59, 102, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, var(--brand-success), #20c997) !important;
    border-color: var(--brand-success) !important;
}

.btn-success:hover, .btn-success:focus {
    background: linear-gradient(135deg, #218838, #1e7e34) !important;
    transform: translateY(-2px);
}

.btn-info {
    background: linear-gradient(135deg, var(--brand-info), #20c997) !important;
    border-color: var(--brand-info) !important;
}

.btn-info:hover, .btn-info:focus {
    background: linear-gradient(135deg, #138496, #1e7e34) !important;
    transform: translateY(-2px);
}

.badge.bg-info {
    background: linear-gradient(135deg, var(--brand-info), #20c997) !important;
    animation: pulse 2s infinite;
}

/* Custom Styles */
.scheme-top-line {
    height: 4px;
    background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary), var(--brand-accent));
}

.scheme-top-line.bg-info {
    background: linear-gradient(90deg, var(--brand-info), #20c997, var(--brand-info)) !important;
    animation: shimmer 2s infinite;
}

.scheme-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.scheme-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.scheme-card.coming-soon {
    opacity: 0.8;
}

.category-tab {
    padding: 1.2rem 1.5rem;
    border: none;
    background: transparent;
    color: #495057;
    font-weight: 500;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.category-tab:hover {
    background: #f8f9fa;
    color: var(--brand-primary);
    padding-left: 2rem;
}

.category-tab.active {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    color: white;
    font-weight: 600;
    border-left: 4px solid var(--brand-accent);
}

.category-tab.active .badge {
    background: rgba(255,255,255,0.3) !important;
}

/* Animation */
.tab-pane {
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .category-tab {
        padding: 0.8rem 1rem;
    }
    
    .category-tab:hover {
        padding-left: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Show profile incomplete modal
function showProfileIncompleteModal() {
    const modal = new bootstrap.Modal(document.getElementById('profileIncompleteModal'));
    modal.show();
}

// Show coming soon modal
function showComingSoonModal(schemeName) {
    const modal = new bootstrap.Modal(document.getElementById('comingSoonModal'));
    const schemeNameElement = document.getElementById('comingSoonSchemeName');
    if (schemeNameElement) {
        schemeNameElement.textContent = schemeName;
    }
    modal.show();
}

// Subscribe for notification
function subscribeNotification() {
    alert('Terima kasih! Kami akan memberitahu Anda segera setelah skema ini tersedia.');
    const modal = bootstrap.Modal.getInstance(document.getElementById('comingSoonModal'));
    if (modal) {
        modal.hide();
    }
}

// Auto-collapse navigation on mobile after selection
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    const mobileBreakpoint = 992;
    
    if (window.innerWidth < mobileBreakpoint) {
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const collapse = document.querySelector('#navCollapse');
                if (collapse && collapse.classList.contains('show')) {
                    setTimeout(() => {
                        const bsCollapse = bootstrap.Collapse.getInstance(collapse);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }, 100);
                }
            });
        });
    }
});
</script>
@endpush