<x-guest-layout>

    @push('styles')
        @vite('resources/css/register.css')
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
                <h1 class="auth-title">{{ __('messages.register_title') }}</h1>
                <p class="auth-sub">{{ __('messages.register_sub') }}</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="field">
                    <label for="name">{{ __('messages.name') }}</label>
                    <div class="input-wrap">
                        <i class="bi bi-person"></i>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               placeholder="{{ __('messages.your_full_name') }}" required autofocus>
                    </div>
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="email">{{ __('messages.email_label') }}</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               placeholder="exemple@domini.com" required>
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

                <div class="field">
                    <label for="password_confirmation">{{ __('messages.confirm_password') }}</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="{{ __('messages.repeat_password') }}" required>
                    </div>
                    @error('password_confirmation') <span class="error">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="auth-btn">{{ __('messages.register_title') }}</button>
            </form>

            <p class="auth-foot">
                {{ __('messages.have_account') }}
                <a href="{{ route('login') }}">{{ __('messages.login_title') }}</a>
            </p>

        </div>
    </div>

</x-guest-layout>