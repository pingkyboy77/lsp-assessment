<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LSP Assessment Application</title>
<link rel="icon" href="{{ asset('images/logo-putih-small.png') }}" type="image/png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js'])     --}}

    <style>
        .bg-login-left {
            background-image: url('{{ asset('images/bg-login.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100%;
            width: 100%;
        }
    </style>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="font-sans text-gray-900 antialiased">

    <div class="container-fluid p-0">
        <div class="row min-vh-100 g-0">
            <!-- Left Side -->
            <div class="col-lg-7 d-none d-lg-block">
                <div class="bg-login-left"></div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="col-lg-5 d-flex align-items-center justify-content-center bg-white">
                <div class="w-100 mx-4 my-5">
                    <div class="card-body">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            let isVisible = false;

            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    isVisible = !isVisible;
                    passwordInput.type = isVisible ? 'text' : 'password';
                    eyeIcon.innerHTML = isVisible ?
                        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.965 9.965 0 011.308-2.727M6.62 6.621A9.967 9.967 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.955 9.955 0 01-4.198 5.385M3 3l18 18"/>` :
                        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                        -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
                    });
                }
            });
            </script>
            <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            @stack('scripts')
</body>

</html>
