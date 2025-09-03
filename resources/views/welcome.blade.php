<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LSP-PM - Lembaga Sertifikasi Profesi Pasar Modal</title>
<link rel="icon" href="{{ asset('images/logo-putih-small.png') }}" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .main-container {
            width: 100%;
            max-width: 1400px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            min-height: 600px;
            max-height: 900px;
            border: 1px solid rgba(226, 232, 240, 0.5);
        }

        /* Left Section */
        .left-section {
            flex: 1;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #1e293b;
            padding: 3rem 2rem;
            position: relative;
            min-height: 500px;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(30,64,175,0.03)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .logo-container {
            width: 120px;
            height: 120px;
            background: rgba(30, 64, 175, 0.08);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(30, 64, 175, 0.15);
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
        }

        .logo-container i {
            font-size: 2.5rem;
            color: #1e40af;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: rgba(30, 64, 175, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #1e40af;
        }

        .main-title {
            font-size: clamp(1rem, 2vw, 2rem);
            font-weight: 800;
            margin-top: 0.5rem;
            letter-spacing: -0.02em;
        }

        .sub-title {
            font-size: clamp(0.9rem, 2.5vw, 1.1rem);
            font-weight: 400;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 400px;
            text-align: center;
        }

        .features-grid {
            position: relative;
            z-index: 2;
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            max-width: 600px;
            width: 100%;
        }

        .feature-card {
            text-align: center;
            transition: transform 0.2s ease;
        }

        .feature-card:hover {
            transform: translateY(-2px);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(30, 64, 175, 0.08);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.8rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(30, 64, 175, 0.15);
        }

        .feature-icon i {
            font-size: 1.2rem;
            color: #1e40af;
        }

        .feature-title {
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: #1e293b;
        }

        .feature-desc {
            font-size: clamp(0.7rem, 1.8vw, 0.8rem);
            opacity: 0.7;
            line-height: 1.3;
            color: #64748b;
        }

        /* Right Section */
        .right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2rem;
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            color: white;
            min-height: 500px;
            position: relative;
        }

        .right-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        }

        .auth-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .welcome-title {
            font-size: clamp(1.75rem, 4vw, 2.25rem);
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .welcome-desc {
            font-size: clamp(0.9rem, 2.2vw, 1rem);
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
        }

        .auth-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn-auth {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: clamp(0.9rem, 2.2vw, 1rem);
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            min-height: 50px;
        }

        .btn-primary {
            background: white;
            color: #1e40af;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
        }

        .btn-primary:hover {
            background: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            color: #1e40af;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-1px);
            color: white;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: rgba(255, 255, 255, 0.7);
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .divider span {
            padding: 0 1rem;
            font-size: clamp(0.8rem, 2vw, 0.875rem);
            white-space: nowrap;
        }

        .social-login {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .social-btn {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.2s ease;
            text-decoration: none;
            backdrop-filter: blur(10px);
        }

        .social-btn:hover {
            transform: translateY(-1px);
            border-color: white;
            color: white;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-container {
                max-height: 800px;
            }

            .left-section,
            .right-section {
                padding: 2.5rem 1.5rem;
            }
        }

        @media (max-width: 992px) {
            body {
                padding: 0.5rem;
            }

            .main-container {
                flex-direction: column;
                max-height: none;
                min-height: auto;
            }

            .left-section {
                flex: none;
                min-height: 400px;
                padding: 2rem 1.5rem;
            }

            .right-section {
                flex: none;
                min-height: 400px;
                padding: 2rem 1.5rem;
            }

            .features-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
                margin-top: 1.5rem;
            }

            .logo-container {
                width: 100px;
                height: 100px;
            }

            .logo-placeholder {
                width: 65px;
                height: 65px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 0.25rem;
            }

            .main-container {
                border-radius: 15px;
            }

            .left-section,
            .right-section {
                padding: 1.5rem;
                min-height: 350px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 0.8rem;
                margin-top: 1rem;
                max-width: 280px;
            }

            .feature-card {
                display: flex;
                align-items: center;
                text-align: left;
                gap: 0.8rem;
            }

            .feature-icon {
                width: 40px;
                height: 40px;
                margin: 0;
                flex-shrink: 0;
            }

            .feature-icon i {
                font-size: 1rem;
            }

            .feature-text {
                flex: 1;
            }

            .feature-title {
                margin-bottom: 0.1rem;
            }

            .logo-container {
                width: 90px;
                height: 90px;
                margin-bottom: 1rem;
            }

            .logo-placeholder {
                width: 55px;
                height: 55px;
                font-size: 1.2rem;
            }

            .auth-buttons {
                gap: 0.8rem;
            }

            .btn-auth {
                padding: 0.9rem 1.5rem;
                min-height: 48px;
            }

            .social-btn {
                width: 44px;
                height: 44px;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 0;
            }

            .main-container {
                width: 100%;
                border-radius: 0;
                min-height: 100vh;
            }

            .left-section,
            .right-section {
                padding: 1rem;
                min-height: 300px;
            }

            .left-section {
                min-height: 40vh;
            }

            .right-section {
                min-height: 60vh;
            }

            .features-grid {
                margin-top: 0.5rem;
                max-width: 250px;
            }

            .feature-card {
                gap: 0.6rem;
            }

            .feature-icon {
                width: 35px;
                height: 35px;
            }

            .feature-icon i {
                font-size: 0.9rem;
            }

            .logo-container {
                width: 75px;
                height: 75px;
                margin-bottom: 0.8rem;
            }

            .logo-placeholder {
                width: 45px;
                height: 45px;
                font-size: 1rem;
            }

            .welcome-text {
                margin-bottom: 1.5rem;
            }

            .auth-buttons {
                gap: 0.6rem;
                margin-bottom: 1rem;
            }

            .btn-auth {
                padding: 0.8rem 1.2rem;
                min-height: 44px;
            }

            .social-btn {
                width: 40px;
                height: 40px;
            }

            .divider {
                margin: 1rem 0;
            }
        }

        @media (max-width: 400px) {

            .left-section,
            .right-section {
                padding: 0.8rem;
            }

            .features-grid {
                max-width: 220px;
            }

            .feature-card {
                gap: 0.5rem;
            }

            .feature-icon {
                width: 30px;
                height: 30px;
            }

            .feature-icon i {
                font-size: 0.8rem;
            }

            .logo-container {
                width: 65px;
                height: 65px;
            }

            .logo-placeholder {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
            }

            .social-login {
                gap: 0.8rem;
            }

            .social-btn {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }
        }

        /* Landscape mobile optimization */
        @media (max-height: 500px) and (orientation: landscape) {
            .main-container {
                flex-direction: row;
                max-height: 90vh;
            }

            .left-section,
            .right-section {
                flex: 1;
                min-height: auto;
                padding: 1rem;
            }

            .features-grid {
                grid-template-columns: repeat(3, 1fr);
                margin-top: 0.5rem;
            }

            .feature-card {
                display: block;
                text-align: center;
            }

            .feature-icon {
                margin: 0 auto 0.3rem;
            }

            .logo-container {
                width: 60px;
                height: 60px;
                margin-bottom: 0.5rem;
            }

            .logo-placeholder {
                width: 35px;
                height: 35px;
                font-size: 0.8rem;
            }

            .welcome-text {
                margin-bottom: 1rem;
            }
        }

        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {

            .logo-container,
            .feature-icon,
            .social-btn {
                border-width: 0.5px;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Left Section -->
        <div class="left-section text-center p-4">
            <!-- Logo -->
            <div class="logo-section mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="LSP-PM Logo" class="mb-3"
                    style="max-width: 160px; height: auto; object-fit: contain;">
                <h1 class="main-title text-uppercase fw-bold mb-0">
                    Lembaga Sertifikasi Profesi
                </h1>
                <h1 class="main-title text-uppercase fw-bold">
                    Pasar Modal
                </h1>
            </div>
            <!-- Logo Partner -->
            <div class="row g-3 mb-3 align-items-center justify-content-center">
                <div class="col-4">
                    <img src="{{ asset('images/bnsp.png') }}" alt="BNSP" class="w-75">
                </div>
                <div class="col-4">
                    <img src="{{ asset('images/ijk.png') }}" alt="OJK" class="w-75">
                </div>

                <div class="col-4">
                    <img src="{{ asset('images/propami.png') }}" alt="PROPAMI" class="w-50">
                </div>
            </div>
            <!-- Fitur -->
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold mb-1">33 Bidang Sertifikasi</h6>
                    <p class="small text-muted mb-0">Sertifikasi lengkap untuk profesi pasar modal</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold mb-1">Terlisensi BNSP & OJK</h6>
                    <p class="small text-muted mb-0">Lembaga resmi terakreditasi nasional</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold mb-1">Trusted by 24K+</h6>
                    <p class="small text-muted mb-0">Dipercaya ribuan profesional pasar modal</p>
                </div>
            </div>


        </div>



        <!-- Right Section -->
        <div class="right-section">
            <div class="auth-container">
                <div class="welcome-text">
                    <h2 class="welcome-title">LSP-PM ASSESSMENT APPLICATION</h2>
                    {{-- <p class="welcome-desc">Bergabunglah dengan lembaga sertifikasi profesi pasar modal pertama di
                        Indonesia yang terlisensi BNSP dan terdaftar di OJK</p> --}}
                </div>

                <div class="auth-buttons">
                    <a href="/login" class="btn-auth btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk ke Akun
                    </a>
                    <a href="/register" class="btn-auth btn-secondary">
                        <i class="fas fa-user-plus"></i>
                        Daftar Sekarang
                    </a>
                </div>

                <div class="divider">
                    <span>atau masuk dengan</span>
                </div>

                <div class="social-login">
                    <a href="#" class="social-btn" title="Login dengan Google">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#" class="social-btn" title="Login dengan Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-btn" title="Login dengan LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // Add smooth scrolling and enhanced interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.3s ease-in-out';

            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);

            // Add click feedback for buttons
            const buttons = document.querySelectorAll('.btn-auth, .social-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('span');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255,255,255,0.3)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.left = (e.clientX - e.target.offsetLeft) + 'px';
                    ripple.style.top = (e.clientY - e.target.offsetTop) + 'px';

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add viewport height fix for mobile browsers
            function setVH() {
                let vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            }

            setVH();
            window.addEventListener('resize', setVH);
            window.addEventListener('orientationchange', setVH);
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>
