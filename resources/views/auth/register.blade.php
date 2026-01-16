<x-guest-layout>
    <div class="login-card">
        <h1 class="login-title">Crear compte</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text"id="name"name="name"value="{{ old('name') }}"requiredautofocus>
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required >
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Contrasenya</label>
                <input type="password"id="password" name="password" required >
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">Confirmar contrasenya</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required >
                @error('password_confirmation')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit">Registrar-se</button>

            <a class="forgot-link" href="{{ route('login') }}">
                Ja tens un compte? Inicia sessió
            </a>
        </form>
    </div>
</x-guest-layout>
