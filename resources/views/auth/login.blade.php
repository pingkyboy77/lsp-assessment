<x-guest-layout>
    <div class="d-flex justify-content-center mb-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 300px;">
    </div>

    <div class="text-center mb-4">
        <h2 class="h3 fw-semi-bold text-dark">Login</h2>
        <p class="text-muted small">Please enter your credentials</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-3" :errors="$errors" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label fw-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" 
                class="form-control" required autofocus>
        </div>

        <!-- Password -->
        <div class="mb-3 position-relative">
            <label for="password" class="form-label fw-medium">Password</label>
            <input id="password" type="password" name="password" required
                class="form-control pe-5">

            <button type="button" id="togglePassword" 
                class="btn position-absolute" 
                style="top: 35px; right: 8px; border: none; background: none; color: #6c757d; z-index: 10;">
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path id="eyeOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path id="eyeOpenLine" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-3">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label text-muted">Remember me</label>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="submit" class="btn btn-primary px-4">
                Login
            </button>

            @if (Route::has('password.request'))
                <a class="text-decoration-none small text-primary" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif
        </div>
    </form>

    <div class="text-center mt-4">
        <p class="text-muted small">Don't have an account?</p>
        <a href="{{ route('register') }}"
            class="btn btn-outline-primary mt-2">
            Register Here
        </a>
    </div>
</x-guest-layout>