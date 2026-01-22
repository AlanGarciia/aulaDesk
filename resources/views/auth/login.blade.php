<x-guest-layout>

    <!-- Botón Tornar arriba a la izquierda -->
    <a href="{{ url('/') }}" class="back-link" aria-label="Tornar a l'inici">← Tornar</a>

    <div class="login-card">
        <h1 class="login-title">Iniciar sessió</h1>

        @if (session('status'))
            <div class="status-message">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="exemple@domini.com"
                    required
                    autofocus
                >
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contrasenya</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="La teva contrasenya"
                    required
                >
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="remember">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Recordar-me</label>
            </div>

            <button type="submit">Iniciar sessió</button>

            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">
                    Has oblidat la contrasenya?
                </a>
            @endif
        </form>
    </div>

</x-guest-layout>
