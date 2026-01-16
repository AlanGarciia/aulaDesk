<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Editar usuari</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.usuaris.update', $usuariEspai) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', $usuariEspai->nom) }}" required>
                        @error('nom') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Rol</label>
                        <select name="rol" required
                            @disabled($usuariEspai->nom === 'admin' || $usuariEspai->rol === \App\Models\UsuariEspai::ROL_ADMIN)>
                            @foreach (\App\Models\UsuariEspai::ROLS as $rol)
                                <option value="{{ $rol }}" @selected(old('rol', $usuariEspai->rol) === $rol)>
                                    {{ $rol }}
                                </option>
                            @endforeach
                        </select>
                        @error('rol') <div class="error">{{ $message }}</div> @enderror

                        @if ($usuariEspai->nom === 'admin' || $usuariEspai->rol === \App\Models\UsuariEspai::ROL_ADMIN)
                            <div class="help">L'usuari admin no pot canviar de rol.</div>
                        @endif
                    </div>

                    <div class="field">
                        <label>Nova contrasenya (opcional)</label>
                        <input type="password" name="contrasenya" autocomplete="new-password">
                        @error('contrasenya') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="actions">
                        <button class="btn btn-primary" type="submit">Desar</button>
                        <a class="btn btn-secondary" href="{{ route('espai.usuaris.index') }}">Tornar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
