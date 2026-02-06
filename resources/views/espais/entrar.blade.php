@push('styles')
    @vite('resources/css/espais/espaisEntrar.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">
            <div class="card">

                {{-- Título tipo post-it dentro de la tarjeta --}}
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
