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
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif
    <!-- Profile Incomplete Warning -->
    @if($isProfileIncomplete)
    <div class="profile-warning-banner">
        <div class="container-fluid">
            <div class="warning-content">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="warning-text">
                    <h5>Profil Belum Lengkap</h5>
                    <p>Lengkapi profil Anda untuk dapat mendaftar skema sertifikasi</p>
                </div>
                <div class="warning-action">
                    <a href="{{ route('asesi.data-pribadi.index') }}" class="btn btn-warning-action">
                        <i class="fas fa-user-edit"></i>
                        Lengkapi Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-0">
        <!-- Mobile Toggle Button -->
        <div class="mobile-toggle d-lg-none">
            <button class="btn btn-primary w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#navCollapse">
                <i class="fas fa-bars me-2"></i>Pilih Kategori
            </button>
        </div>

        <!-- Left Navigation Panel -->
        <div class="col-lg-3 col-12">
            <div class="nav-wrapper">
                <div class="collapse d-lg-block" id="navCollapse">
                    <div class="category-nav">
                        <ul class="nav flex-column" id="bidangTab" role="tablist">
                            @foreach($groupedSchemes as $bidangName => $group)
                                <li class="nav-item">
                                    <button class="nav-link category-tab {{ $loop->first ? 'active' : '' }}" 
                                        id="tab-{{ Str::slug($bidangName) }}" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#content-{{ Str::slug($bidangName) }}" 
                                        type="button" role="tab"
                                        data-bs-dismiss="collapse" 
                                        data-bs-target="#navCollapse">
                                        {{ $bidangName }}
                                        <span class="badge">{{ $group->count() }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="col-lg-9 col-12">
            <div class="area-skema-content">
                <!-- Tab Content -->
                <div class="tab-content h-100" id="bidangTabContent">
                    @foreach($groupedSchemes as $bidangName => $group)
                        <div class="tab-pane fade h-100 {{ $loop->first ? 'show active' : '' }}" 
                            id="content-{{ Str::slug($bidangName) }}" 
                            role="tabpanel">

                            <!-- Cards Grid -->
                            <div class="row">
                                @foreach($group as $skema)
                                    @php
                                        // Check if user already has APL 01 for this scheme
                                        $existingApl = auth()->user()->apl01Registrations()
                                            ->where('certification_scheme_id', $skema->id)
                                            ->first();
                                    @endphp
                                    <div class="col-6 mb-2">
                                        <div class="skema-scheme-card {{ $isProfileIncomplete ? 'profile-incomplete' : '' }}">
                                            <div class="skema-card-top-line"></div>
                                            <div class="skema-card-content">
                                                <div class="skema-scheme-badge">
                                                    {{ $skema->code_1 ?? 'N/A' }}
                                                </div>
                                                <h3 class="skema-scheme-name">
                                                    {{ $skema->nama }}
                                                </h3>
                                                
                                                @if($existingApl)
                                                    <div class="apl-status-info">
                                                        <small class="text-muted d-block mb-2">
                                                            APL 01: {{ $existingApl->nomor_apl_01 }}
                                                        </small>
                                                        <span class="badge badge-{{ $existingApl->statusColor }} mb-2">
                                                            {{ $existingApl->statusText }}
                                                        </span>
                                                    </div>
                                                    
                                                    @if($existingApl->isEditable)
                                                        <a href="{{ route('asesi.apl01.edit', $existingApl->id) }}" 
                                                           class="skema-register-btn edit-btn">
                                                            <i class="fas fa-edit me-1"></i>
                                                            Lanjutkan
                                                        </a>
                                                    @else
                                                        <a href="{{ route('asesi.apl01.show', $existingApl->id) }}" 
                                                           class="skema-register-btn view-btn">
                                                            <i class="fas fa-eye me-1"></i>
                                                            Lihat Detail
                                                        </a>
                                                    @endif
                                                @else
                                                    @if($isProfileIncomplete)
                                                        <button class="skema-register-btn disabled" disabled 
                                                                title="Lengkapi profil untuk dapat mendaftar"
                                                                onclick="showProfileIncompleteModal()">
                                                            <i class="fas fa-lock me-1"></i>
                                                            Daftar
                                                        </button>
                                                    @else
                                                        <a href="{{ route('asesi.apl01.create', $skema->id) }}" 
                                                           class="skema-register-btn">
                                                            <i class="fas fa-edit me-1"></i>
                                                            Daftar
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                            
                                            @if($isProfileIncomplete)
                                                <div class="card-lock-overlay">
                                                    <div class="lock-content">
                                                        <i class="fas fa-user-times"></i>
                                                        <span>Profil Belum Lengkap</span>
                                                    </div>
                                                </div>
                                            @endif
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
            <div class="modal-header border-0">
                <h5 class="modal-title text-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Profil Belum Lengkap
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="modal-icon mb-3">
                    <i class="fas fa-user-slash text-muted"></i>
                </div>
                <h6 class="mb-3">Anda belum dapat mendaftar skema sertifikasi</h6>
                <p class="text-muted mb-4">
                    Untuk dapat mendaftar dan mengakses semua fitur, silakan lengkapi profil Anda terlebih dahulu.
                </p>
                <div class="profile-requirements mb-4">
                    <small class="text-muted d-block mb-2">Data yang diperlukan:</small>
                    <div class="requirement-list">
                        <div class="requirement-item">
                            <i class="fas fa-check-circle {{ $userProfile && $userProfile->nama_lengkap ? 'text-success' : 'text-muted' }}"></i>
                            <span>Nama Lengkap</span>
                        </div>
                        <div class="requirement-item">
                            <i class="fas fa-check-circle {{ $userProfile && $userProfile->nik ? 'text-success' : 'text-muted' }}"></i>
                            <span>NIK</span>
                        </div>
                        <div class="requirement-item">
                            <i class="fas fa-check-circle {{ $userProfile && $userProfile->tempat_lahir ? 'text-success' : 'text-muted' }}"></i>
                            <span>Tempat Lahir</span>
                        </div>
                        <div class="requirement-item">
                            <i class="fas fa-check-circle {{ $userProfile && $userProfile->tanggal_lahir ? 'text-success' : 'text-muted' }}"></i>
                            <span>Tanggal Lahir</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('asesi.data-pribadi.index') }}" class="btn btn-primary">
                    <i class="fas fa-user-edit me-1"></i>
                    Lengkapi Profil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Previous styles remain the same, adding new ones below */

.apl-status-info {
    margin-bottom: 1rem;
}

.edit-btn {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.edit-btn:hover {
    background: linear-gradient(135deg, #218838, #1e7e34) !important;
}

.view-btn {
    background: linear-gradient(135deg, #17a2b8, #20c997) !important;
}

.view-btn:hover {
    background: linear-gradient(135deg, #138496, #1e7e34) !important;
}

/* All previous styles from the original skema index */
.profile-warning-banner {
    background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
    border-bottom: 2px solid #f59e0b;
    padding: 1rem 0;
    margin-bottom: 1rem;
}

.warning-content {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 0 1rem;
}

.warning-icon {
    font-size: 1.5rem;
    color: #d97706;
}

.warning-text h5 {
    margin: 0 0 4px 0;
    color: #92400e;
    font-weight: 600;
    font-size: 1rem;
}

.warning-text p {
    margin: 0;
    color: #a16207;
    font-size: 0.85rem;
}

.btn-warning-action {
    background: #d97706;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-warning-action:hover {
    background: #b45309;
    color: white;
    transform: translateY(-1px);
}

.mobile-toggle {
    padding: 1rem;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.nav-wrapper {
    position: sticky;
    top: 0;
    height: 100vh;
}

.category-nav {
    background: #ffffff;
    height: auto;
    padding: 0;
    overflow-y: auto;
    border-right: 2px solid #e9ecef;
    box-shadow: 2px 0 10px rgba(0,0,0,0.05);
}

.category-tab {
    width: 100%;
    text-align: left;
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
    color: #0d3b66;
    padding-left: 2rem;
}

.category-tab.active {
    background: linear-gradient(135deg, #0d3b66, #1a5490);
    color: white;
    font-weight: 600;
    border-left: 4px solid #ffd700;
}

.category-tab .badge {
    background: rgba(255,255,255,0.2);
    color: inherit;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.category-tab.active .badge {
    background: rgba(255,255,255,0.3);
}

.area-skema-content {
    min-height: 100vh;
    overflow-x: hidden;
    overflow-y: auto;
    padding-left: 1.5rem;
}

.skema-scheme-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 100%;
    position: relative;
    overflow: hidden;
}

.skema-scheme-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.skema-scheme-card.profile-incomplete {
    opacity: 0.7;
}

.skema-scheme-card.profile-incomplete:hover {
    transform: translateY(-4px);
}

.skema-card-top-line {
    height: 4px;
    background: linear-gradient(90deg, #0d3b66, #1a5490, #ffd700);
}

.skema-scheme-card.profile-incomplete .skema-card-top-line {
    background: linear-gradient(90deg, #6b7280, #9ca3af);
}

.skema-card-content {
    padding: 1.2rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.skema-scheme-badge {
    background: linear-gradient(135deg, #0d3b66, #1a5490);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(13, 59, 102, 0.3);
    align-self: flex-start;
}

.skema-scheme-card.profile-incomplete .skema-scheme-badge {
    background: linear-gradient(135deg, #6b7280, #9ca3af);
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

.skema-scheme-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #212529;
    line-height: 1.3;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
    min-height: 3rem;
}

.skema-register-btn {
    width: 100%;
    background: linear-gradient(135deg, #0d3b66, #1a5490);
    color: white;
    border: none;
    padding: 0.6rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: auto;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.skema-register-btn:hover:not(.disabled) {
    background: linear-gradient(135deg, #1a5490, #2a6bb3);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(13, 59, 102, 0.3);
    color: white;
}

.skema-register-btn.disabled {
    background: linear-gradient(135deg, #6b7280, #9ca3af);
    cursor: not-allowed;
    opacity: 0.7;
}

.skema-register-btn.disabled:hover {
    transform: none;
    box-shadow: none;
}

.card-lock-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(1px);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    z-index: 5;
}

.lock-content {
    text-align: center;
    color: #6b7280;
}

.lock-content i {
    font-size: 2rem;
    margin-bottom: 8px;
    display: block;
}

.lock-content span {
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modal-icon i {
    font-size: 3rem;
}

.requirement-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.requirement-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.85rem;
}

.requirement-item i {
    font-size: 0.9rem;
}

.apl-status-info {
    text-align: center;
}

/* Responsive */
@media (max-width: 1400px) {
    .skema-card-content {
        padding: 1rem;
    }
}

@media (max-width: 1200px) {
    .skema-scheme-name {
        font-size: 0.8rem;
    }
    
    .skema-register-btn {
        font-size: 0.75rem;
        padding: 0.5rem 0.8rem;
    }
}

@media (max-width: 992px) {
    .nav-wrapper {
        position: static;
        height: auto;
    }
    
    .category-nav {
        height: auto;
        max-height: none;
        border-right: none;
        border-bottom: 2px solid #e9ecef;
    }
    
    .area-skema-content {
        min-height: auto;
        padding: 1rem;
    }
    
    .warning-content {
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }
}

@media (max-width: 768px) {
    .area-skema-content {
        padding: 0.8rem;
    }
    
    .skema-card-content {
        padding: 0.8rem;
    }
    
    .skema-scheme-name {
        font-size: 0.75rem;
        min-height: 2.5rem;
    }
    
    .category-tab {
        padding: 1rem;
        font-size: 0.9rem;
    }
    
    .skema-register-btn {
        font-size: 0.7rem;
        padding: 0.4rem 0.6rem;
    }
}

@media (max-width: 576px) {
    .area-skema-content {
        padding: 0.5rem;
    }
    
    .skema-card-content {
        padding: 0.8rem;
    }
    
    .skema-scheme-name {
        font-size: 0.7rem;
        min-height: 2rem;
    }
    
    .skema-register-btn {
        font-size: 0.65rem;
        padding: 0.4rem 0.5rem;
    }
}

/* Scrollbar styling */
.area-skema-content::-webkit-scrollbar,
.category-nav::-webkit-scrollbar {
    width: 6px;
}

.area-skema-content::-webkit-scrollbar-track,
.category-nav::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.area-skema-content::-webkit-scrollbar-thumb,
.category-nav::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #0d3b66, #1a5490);
    border-radius: 3px;
}

.area-skema-content::-webkit-scrollbar-thumb:hover,
.category-nav::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #1a5490, #2a6bb3);
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

.skema-scheme-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
    transition: left 0.5s ease;
}

.skema-scheme-card:hover::before {
    left: 100%;
}

body {
    overflow-x: hidden;
}

.container-fluid,
.row,
.area-skema-content {
    overflow-x: hidden;
    max-width: 100%;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const tabPanes = document.querySelectorAll('.tab-pane');
            tabPanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            const targetId = this.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
    });
    
    // Auto-collapse navigation on mobile after selection
    const mobileBreakpoint = 992;
    if (window.innerWidth < mobileBreakpoint) {
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const collapse = document.querySelector('#navCollapse');
                if (collapse) {
                    const bsCollapse = new bootstrap.Collapse(collapse, {
                        hide: true
                    });
                }
            });
        });
    }
});

// Show profile incomplete modal
function showProfileIncompleteModal() {
    const modal = new bootstrap.Modal(document.getElementById('profileIncompleteModal'));
    modal.show();
}

// Registration confirmation
function handleRegistration(skemaId, hasExistingApl = false) {
    @if($isProfileIncomplete)
        showProfileIncompleteModal();
        return;
    @endif
    
    if (!hasExistingApl) {
        if (confirm('Anda akan membuat APL 01 baru untuk skema ini. Lanjutkan?')) {
            window.location.href = `/asesi/apl01/create/${skemaId}`;
        }
    }
}

// Smooth button animation
document.querySelectorAll('.skema-register-btn:not(.disabled)').forEach(btn => {
    btn.addEventListener('click', function() {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
});

// Add tooltip for disabled buttons
document.querySelectorAll('.skema-register-btn.disabled').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        console.log('Button disabled - profile incomplete');
    });
});

// Check for existing APL 01 status updates
@auth
setInterval(function() {
    fetch('{{ route("asesi.apl01.index") }}?ajax=1')
        .then(response => response.json())
        .then(data => {
            // Update status badges if any changes
            data.forEach(apl => {
                const card = document.querySelector(`[data-scheme-id="${apl.certification_scheme_id}"]`);
                if (card) {
                    const statusBadge = card.querySelector('.badge');
                    if (statusBadge && statusBadge.textContent !== apl.status_text) {
                        location.reload(); // Reload if status changed
                    }
                }
            });
        })
        .catch(console.error);
}, 60000); // Check every minute
@endauth
</script>
@endpush