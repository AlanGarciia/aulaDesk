@push('styles')
    @vite('resources/css/espai/usuaris/usuarisCreate.css')
@endpush

<x-app-layout>

    <div class="page">
        <div class="container">

            @if ($errors->any())
                <div class="alert-danger">
                    Revisa els camps, hi ha errors.
                </div>
            @endif

            <div class="card">
                <h2 class="inside-title">Afegir usuari</h2>

                <form method="POST" action="{{ route('espai.usuaris.store') }}">
                    @csrf

                    {{-- NOM --}}
                    <div class="field">
                        <label for="nom" class="label">Nom</label>
                        <input id="nom" name="nom" type="text"
                               value="{{ old('nom') }}"
                               class="input" autocomplete="off" autofocus required>
                        @error('nom')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ROL (dinámico desde base_roles) --}}
                    <div class="field">
                        <label for="rol" class="label">Rol</label>

                        <select id="rol" name="rol" class="input" required>
                            @foreach(\App\Models\BaseRole::all() as $rol)
                                <option value="{{ $rol->nom }}" @selected(old('rol', 'professor') === $rol->nom)>
                                    {{ ucfirst($rol->nom) }}
                                </option>
                            @endforeach
                        </select>

                        @error('rol')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="contrasenya" class="label">Contrasenya</label>
                        <input id="contrasenya" name="contrasenya" type="password"
                               class="input" required>
                        @error('contrasenya')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">
                            Crear usuari
                        </button>

                        <a href="{{ route('espai.usuaris.index') }}" class="btn btn-secondary">
                            Cancel·lar
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </div>

</x-app-layout>