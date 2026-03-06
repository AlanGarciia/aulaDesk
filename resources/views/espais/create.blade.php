@push('styles')
    @vite('resources/css/espais/espaisCreate.css')
@endpush

@push('scripts')
    @vite('resources/js/espais/particles-bg.js')
@endpush

<x-app-layout>
    <div class="create-page particles-page">
        {{-- Fondo de partículas --}}
        <div id="particles-bg" class="particles-bg" aria-hidden="true"></div>

        <div class="create-postit create-foreground">
            <h2 class="create-title">Crear Espai</h2>

            <h3 class="create-subtitle">
                Introdueix la informació del nou espai
            </h3>

            <form method="POST" action="{{ route('espais.store') }}">
                @csrf

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input id="nom" name="nom" type="text"
                           value="{{ old('nom') }}"
                           placeholder="Nom de l'espai" autofocus>
                    @error('nom')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descripcio">Descripció (opcional)</label>
                    <textarea id="descripcio" name="descripcio" rows="4"
                              placeholder="Escriu una descripció breu">{{ old('descripcio') }}</textarea>
                    @error('descripcio')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Desar</button>
                    <a href="{{ route('espais.index') }}" class="btn-secondary">Cancel·lar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>