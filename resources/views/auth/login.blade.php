<x-guest-layout>

    @push('styles')
        @vite('resources/css/login.css')
    @endpush

    {{-- Botó Tornar --}}
    <a href="{{ url('/') }}" class="back-link" aria-label="{{ __('messages.back_to_home') }}">
        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
    </a>

    <div class="auth-wrap">
        <div class="auth-card">

            <div class="auth-head">
                <div class="auth-logo">
                    <img src="{{ asset('img/logo_solo.png') }}" alt="aulaDesk">
                </div>
                <h1 class="auth-title">{{ __('messages.login_title') }}</h1>
                <p class="auth-sub">{{ __('messages.login_sub') }}</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email">{{ __('messages.email_label') }}</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               placeholder="exemple@domini.com" required autofocus>
                    </div>
                    @error('email') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password">{{ __('messages.password') }}</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password"
                               placeholder="{{ __('messages.your_password') }}" required>
                    </div>
                    @error('password') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="row-between">
                    <label class="remember">
                        <input type="checkbox" id="remember" name="remember">
                        <span>{{ __('messages.remember_me') }}</span>
                    </label>
                    <a class="forgot-link" href="{{ route('password.request') }}">{{ __('messages.forgot_password') }}</a>
                </div>

                <button type="submit" class="auth-btn">{{ __('messages.login_title') }}</button>
            </form>

            <p class="auth-foot">
                {{ __('messages.no_account') }}
                <a href="{{ route('register') }}">{{ __('messages.create_account') }}</a>
            </p>

        </div>
    </div>

</x-guest-layout>