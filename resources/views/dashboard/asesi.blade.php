@extends('layouts.admin')

@section('content')
    <!-- Profile Completion Alert -->
    @php
        $userProfile = auth()->user()->profile ?? null;
        $isProfileIncomplete = !$userProfile || 
            !$userProfile->nama_lengkap || 
            !$userProfile->nik || 
            !$userProfile->tempat_lahir || 
            !$userProfile->tanggal_lahir;
    @endphp
    @if($isProfileIncomplete)
        <div class="profile-alert-container mb-4">
            <div class="profile-alert-card">
                <div class="alert-content">
                    <div class="alert-icon">
                        <i class="bi bi-person-exclamation"></i>
                    </div>
                    <div class="alert-text">
                        <h4>Lengkapi Profil Anda!</h4>
                        <p>Untuk mengakses skema sertifikasi dan melakukan pendaftaran, silakan lengkapi profil Anda terlebih dahulu.</p>
                        <div class="profile-benefits">
                            <div class="benefit-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Akses ke semua skema sertifikasi</span>
                            </div>
                            <div class="benefit-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Pendaftaran asesmen</span>
                            </div>
                            <div class="benefit-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Sertifikat digital</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert-bottom">
                    <div class="alert-actions">
                        <a href="{{ route('asesi.data-pribadi.index') }}" class="btn btn-complete-profile">
                            <i class="bi bi-person-plus"></i>
                            Lengkapi Profil Sekarang
                        </a>
                    </div>
                    <div class="alert-progress">
                        @php
                            $completedFields = 0;
                            $totalFields = 4;
                            
                            if ($userProfile) {
                                if ($userProfile->nama_lengkap) $completedFields++;
                                if ($userProfile->nik) $completedFields++;
                                if ($userProfile->tempat_lahir) $completedFields++;
                                if ($userProfile->tanggal_lahir) $completedFields++;
                            }
                            
                            $progressPercentage = ($completedFields / $totalFields) * 100;
                        @endphp
                        
                        <div class="progress-circle">
                            <svg viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="6"/>
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#3b82f6" stroke-width="6"
                                        stroke-dasharray="{{ 2 * 3.14159 * 45 }}"
                                        stroke-dashoffset="{{ 2 * 3.14159 * 45 * (1 - $progressPercentage / 100) }}"
                                        class="progress-bar"/>
                            </svg>
                            <div class="progress-text">{{ round($progressPercentage) }}%</div>
                        </div>
                        <p class="progress-label">Profil Terisi</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Welcome Message for Complete Profile -->
        <div class="welcome-message mb-4">
            <div class="welcome-card">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h3>Selamat Datang, {{ $userProfile->nama_lengkap }}! 👋</h3>
                        <p>Profil Anda sudah lengkap. Anda siap mengakses semua fitur yang tersedia.</p>
                    </div>
                    <div class="welcome-action">
                        <a href="{{ route('asesi.skema-sertifikasi.index') }}" class="btn btn-explore-schemes">
                            <i class="bi bi-clipboard-check"></i>
                            Jelajahi Skema Sertifikasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="stats-row">
        <div class="row g-4">
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stat-card  {{ $isProfileIncomplete ? 'disabled' : '' }}">
                    @if($isProfileIncomplete)
                        <div class="card-overlay">
                            <i class="bi bi-lock"></i>
                            <span>Lengkapi Profil</span>
                        </div>
                    @endif
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <a href="{{ route('asesi.apl01.index') }}">
                    <div class="stat-value counter-number" data-target="{{ $isProfileIncomplete ? 0 : ($dashboardData['total_jadwal_asesmen'] ?? 0) }}">0</div>
                    <div class="stat-label">Jadwal Asesmen</div></a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stat-card {{ $isProfileIncomplete ? 'disabled' : '' }}">
                    @if($isProfileIncomplete)
                        <div class="card-overlay">
                            <i class="bi bi-lock"></i>
                            <span>Lengkapi Profil</span>
                        </div>
                    @endif
                    <div class="stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-value counter-number" data-target="{{ $isProfileIncomplete ? 0 : ($dashboardData['asesmen_selesai'] ?? 0) }}">0</div>
                    <div class="stat-label">Asesmen Selesai</div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stat-card {{ $isProfileIncomplete ? 'disabled' : '' }}">
                    @if($isProfileIncomplete)
                        <div class="card-overlay">
                            <i class="bi bi-lock"></i>
                            <span>Lengkapi Profil</span>
                        </div>
                    @endif
                    <div class="stat-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <div class="stat-value counter-number" data-target="{{ $isProfileIncomplete ? 0 : ($dashboardData['sertifikat_diterbitkan'] ?? 0) }}">0</div>
                    <div class="stat-label">Sertifikat Diterbitkan</div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stat-card {{ $isProfileIncomplete ? 'disabled' : '' }}">
                    @if($isProfileIncomplete)
                        <div class="card-overlay">
                            <i class="bi bi-lock"></i>
                            <span>Lengkapi Profil</span>
                        </div>
                    @endif
                    <div class="stat-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div class="stat-value counter-number" data-target="{{ $isProfileIncomplete ? 0 : ($dashboardData['tingkat_keberhasilan'] ?? 0) }}">0</div>
                    <div class="stat-label">Tingkat Keberhasilan (%)</div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .profile-alert-container {
            animation: slideInFromTop 0.4s ease-out;
        }

        .profile-alert-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            padding: 32px;
            color: #334155;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            position: relative;
        }

        .profile-alert-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6, #06b6d4, #3b82f6);
            border-radius: inherit;
            z-index: -1;
            opacity: 0.1;
        }

        .alert-content {
            display: flex;
            align-items: flex-start;
            gap: 30px;
            position: relative;
            z-index: 2;
        }

        .alert-icon {
            font-size: 2.5rem;
            color: #64748b;
            opacity: 0.8;
        }

        .alert-text h4 {
            margin: 0 0 12px 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        .alert-text p {
            margin: 0 0 20px 0;
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
        }

        .profile-benefits {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: #475569;
        }

        .benefit-item i {
            color: #059669;
            font-size: 1rem;
        }

        .btn-complete-profile {
            background: #3b82f6;
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
            font-size: 0.95rem;
        }

        .btn-complete-profile:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .btn-explore-schemes {
            background: #2563eb;
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2);
            font-size: 0.95rem;
        }

        .btn-explore-schemes:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
            color: white;
        }

        .alert-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .alert-actions {
            flex: 1;
        }

        .alert-progress {
            text-align: center;
            margin-left: 20px;
        }

        .progress-circle {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
        }

        .progress-circle svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .progress-bar {
            transition: stroke-dashoffset 1s ease-in-out;
        }

        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
        }

        .progress-label {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
        }

        .welcome-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 24px 28px;
            color: #0c4a6e;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.1);
        }

        .welcome-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .welcome-text h3 {
            margin: 0 0 8px 0;
            font-size: 1.4rem;
            font-weight: 600;
            color: #0c4a6e;
        }

        .welcome-text p {
            margin: 0;
            color: #0369a1;
            font-size: 0.95rem;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .stat-card.disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(2px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 10;
            border-radius: inherit;
        }

        .card-overlay i {
            font-size: 1.75rem;
            margin-bottom: 8px;
            opacity: 0.9;
        }

        .card-overlay span {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #3b82f6;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .quick-access-section {
            margin-top: 40px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 1rem;
            margin: 0;
        }

        .quick-access-card {
            background: white;
            border-radius: 12px;
            padding: 28px 24px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .quick-access-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .quick-access-card:hover::before {
            transform: scaleX(1);
        }

        .quick-access-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }

        .quick-access-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quick-access-card.disabled:hover {
            transform: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .quick-access-icon {
            font-size: 2.5rem;
            color: #3b82f6;
            margin-bottom: 16px;
        }

        .quick-access-card h5 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .quick-access-card p {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .quick-access-arrow {
            color: #3b82f6;
            font-size: 1.1rem;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .quick-access-card:hover .quick-access-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        .pulse-effect {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes slideInFromTop {
            0% {
                transform: translateY(-20px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .alert-content {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .alert-bottom {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .alert-progress {
                margin-left: 0;
            }

            .profile-benefits {
                justify-content: center;
            }

            .progress-circle {
                width: 70px;
                height: 70px;
            }

            .welcome-content {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }

            .quick-access-card {
                margin-bottom: 16px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Counter Animation
            function animateCounters() {
                const counters = document.querySelectorAll('.counter-number');
                const animationDuration = 2000; // 2 seconds

                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    const increment = target / (animationDuration / 16); // 60 FPS
                    let current = 0;

                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            counter.textContent = target.toLocaleString();
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current).toLocaleString();
                        }
                    }, 16);
                });
            }

            // Only animate if profile is complete
            @if(!$isProfileIncomplete)
                setTimeout(animateCounters, 500);
            @endif
        });
    </script>
@endsection