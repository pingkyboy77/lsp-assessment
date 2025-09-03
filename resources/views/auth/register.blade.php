<x-guest-layout>
    <div class="d-flex justify-content-center mt-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 160px;">
    </div>

    <div class="text-center my-4">
        <h2 class="h3 fw-bold text-dark">Create Your Account</h2>
        <p class="text-muted small">Join us and explore certification services</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-medium">{{ __('Name') }}</label>
            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}"
                required autofocus autocomplete="name" />
            @if ($errors->get('name'))
                <div class="text-danger small mt-1">
                    @foreach ($errors->get('name') as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Identitas Number --}}
        <div class="mb-3">
            <label for="id_number" class="form-label fw-medium">{{ __('ID Number (NIK / PASPOR / No MET)') }}</label>
            <input id="id_number" class="form-control" type="text" name="id_number" value="{{ old('id_number') }}"
                required autofocus autocomplete="id_number" />
            @if ($errors->get('id_number'))
                <div class="text-danger small mt-1">
                    @foreach ($errors->get('id_number') as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label fw-medium">{{ __('Email') }}</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}"
                required autocomplete="username" />
            @if ($errors->get('email'))
                <div class="text-danger small mt-1">
                    @foreach ($errors->get('email') as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-medium">{{ __('Password') }}</label>
            <div class="position-relative">
                <input type="password" name="password" id="password" required autocomplete="new-password"
                    class="form-control pe-5">
                <button type="button" id="togglePassword" class="btn position-absolute"
                    style="top: 50%; right: 8px; transform: translateY(-50%); border: none; background: none; color: #6c757d; z-index: 10;">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            @if ($errors->get('password'))
                <div class="text-danger small mt-1">
                    @foreach ($errors->get('password') as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-medium">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required
                autocomplete="new-password" />
            @if ($errors->get('password_confirmation'))
                <div class="text-danger small mt-1">
                    @foreach ($errors->get('password_confirmation') as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('login') }}" class="text-decoration-none small text-primary">
                {{ __('Already registered?') }}
            </a>

            <button type="submit" class="btn btn-primary px-4">
                {{ __('Register') }}
            </button>
        </div>
    </form>

    @push('scripts')
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
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.944-9.543-7a10.042 10.042 0 012.982-4.334M15 12a3 3 0 00-3-3m0 0a3 3 0 013 3m0 0a3 3 0 01-3 3m6.708 1.708A10.042 10.042 0 0019.543 12c-.21-.728-.524-1.41-.928-2.03M3 3l18 18"/>` :
                            `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
                    });
                }
            });
        </script>
    @endpush
</x-guest-layout>
