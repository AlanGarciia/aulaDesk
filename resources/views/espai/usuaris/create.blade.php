@push('styles')
    @vite('resources/css/espai/usuarisCreate.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Afegir usuari</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            @if ($errors->any())
                <div class="alert-danger">
                    Revisa els camps, hi ha errors.
                </div>
            @endif

            <div class="card">
                <form method="POST" action="{{ route('espai.usuaris.store') }}">
                    @csrf

                    <div class="field">
                        <label for="nom" class="label">Nom</label>
                        <input
                            id="nom"
                            name="nom"
                            type="text"
                            value="{{ old('nom') }}"
                            class="input"
                            autocomplete="off"
                            autofocus
                            required
                        >
                        @error('nom')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="contrasenya" class="label">Contrasenya</label>
                        <input
                            id="contrasenya"
                            name="contrasenya"
                            type="password"
                            class="input"
                            required
                        >
                        @error('contrasenya')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">
                            Crear usuari
                        </button>

                        <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                            Tornar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
