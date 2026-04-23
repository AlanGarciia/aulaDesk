<x-guest-layout>

    @push('styles')
        @vite('resources/css/register.css')
    @endpush

    @push('scripts')
        @vite('resources/js/particles.js')
    @endpush

    <!-- Canvas partículas -->
    <canvas id="particles" class="absolute inset-0 w-full h-full z-0"></canvas>

    <!-- Botón Tornar fijo esquina superior izquierda -->
    <a href="{{ url('/') }}" class="back-link" aria-label="Tornar a l'inici">
        <i class="bi bi-box-arrow-right"></i> Tornar
    </a>

    <!-- Card registro -->
    <div class="login-card z-10">
        <h1 class="login-title">Crear compte</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nom -->
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="El teu nom complet" required autofocus>
                @error('name') <span class="error">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="exemple@domini.com" required>
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>

            <!-- Contrasenya -->
            <div class="form-group">
                <label for="password">Contrasenya</label>
                <input type="password" id="password" name="password" placeholder="La teva contrasenya" required>
                @error('password') <span class="error">{{ $message }}</span> @enderror
            </div>

            <!-- Confirmar contrasenya -->
            <div class="form-group">
                <label for="password_confirmation">Confirmar contrasenya</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repetir contrasenya" required>
                @error('password_confirmation') <span class="error">{{ $message }}</span> @enderror
            </div>

            <!-- Botón Registrar -->
            <button type="submit">Registrar-se</button>

            <!-- Link a login -->
            <a class="forgot-link" href="{{ route('login') }}">Ja tens un compte? Inicia sessió</a>
        </form>
    </div>

</x-guest-layout>