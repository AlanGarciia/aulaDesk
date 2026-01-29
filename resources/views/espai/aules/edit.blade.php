<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Editar aula</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.aules.update', $aula) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>Nombre</label>
                        <input name="nom" value="{{ old('nom', $aula->nom) }}" required>
                        @error('nom') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Código</label>
                        <input name="codi" value="{{ old('codi', $aula->codi) }}">
                        @error('codi') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Capacidad</label>
                        <input type="number" name="capacitat" value="{{ old('capacitat', $aula->capacitat) }}" min="0">
                        @error('capacitat') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Planta</label>
                        <input name="planta" value="{{ old('planta', $aula->planta) }}">
                        @error('planta') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>
                            <input type="checkbox" name="activa" value="1" {{ old('activa', $aula->activa) ? 'checked' : '' }}>
                            Activa
                        </label>
                        @error('activa') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:flex; gap:10px;">
                        <button class="btn" type="submit">Guardar</button>
                        <a class="btn secondary" href="{{ route('espai.aules.index') }}">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
