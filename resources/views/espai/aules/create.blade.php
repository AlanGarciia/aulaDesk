@push('styles')
  @vite('resources/css/espai/aules/aulaForm.css')
@endpush

<<<<<<< HEAD
  @push('styles')
        @vite('resources/css/espai/aules/aulaCreate.css')
    @endpush

=======
<x-app-layout>
>>>>>>> fb1925c (Nuevooo)
    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.aules.store') }}">
                    @csrf
                    <h1>Cració d'aula</h1>
                    <div class="field">
                        <label>Nombre</label>
                        <input name="nom" value="{{ old('nom') }}" required>
                        @error('nom') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Código</label>
                        <input name="codi" value="{{ old('codi') }}">
                        @error('codi') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Capacidad</label>
                        <input type="number" name="capacitat" value="{{ old('capacitat') }}" min="0">
                        @error('capacitat') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Planta</label>
                        <input name="planta" value="{{ old('planta') }}">
                        @error('planta') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>
                            <input type="checkbox" name="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}>
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
