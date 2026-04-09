@push('styles')
    @vite('resources/css/espais/espaisEntrar.css')
@endpush

@push('scripts')
    @vite('resources/js/espais/particles-bg.js')
@endpush

<x-app-layout>
    <div class="page particles-page">

        {{-- Fondo de partículas --}}
        <div id="particles-bg" class="particles-bg" aria-hidden="true"></div>

        <div class="container">
            <div class="card card-foreground">

                <h2 class="page-title">Entrar a l'espai</h2>

                <p class="subtitle">Introdueix el teu usuari d'espai per continuar.</p>

                <form method="POST" action="{{ route('espais.entrar', $espai) }}">
                    @csrf

                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input id="nom" name="nom" type="text" value="{{ old('nom') }}" required autofocus>
                        @error('nom')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contrasenya">Contrasenya</label>
                        <input id="contrasenya" name="contrasenya" type="password" required>
                        @error('contrasenya')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                        <a href="{{ route('espais.index') }}" class="btn btn-secondary">Tornar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>