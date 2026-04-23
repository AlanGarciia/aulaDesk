<x-guest-layout>

    @push('styles')
        @vite('resources/css/login.css')
    @endpush

    @push('scripts')
        @vite('resources/js/particles.js')
    @endpush

    <!-- Botón Tornar fijo en la esquina superior izquierda -->
        <a href="{{ url('/') }}" class="back-link" aria-label="Tornar a l'inici">
            <i class="bi bi-box-arrow-right"></i> Tornar
        </a>


    <!-- Canvas para partículas -->
    <canvas id="particles" class="absolute inset-0 w-full h-full z-0"></canvas>

    <div class="login-card">
        <h1 class="login-title">Iniciar sessió</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="exemple@domini.com" required autofocus>
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password">Contrasenya</label>
                <input type="password" id="password" name="password" placeholder="La teva contrasenya" required>
                @error('password') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="remember">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Recordar-me</label>
            </div>

            <a class="forgot-link" href="{{ route('password.request') }}">Has oblidat la contrasenya?</a>

            <button type="submit">Iniciar sessió</button>
        </form>
    </div>

</x-guest-layout>