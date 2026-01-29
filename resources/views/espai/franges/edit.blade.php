<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Editar franja</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.franges.update', $franja) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>Ordre</label>
                        <input type="number" name="ordre" value="{{ old('ordre', $franja->ordre) }}" min="1" required>
                        @error('ordre') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Nom (opcional)</label>
                        <input name="nom" value="{{ old('nom', $franja->nom) }}">
                        @error('nom') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Inici</label>
                        <input type="time" name="inici" value="{{ old('inici', substr($franja->inici,0,5)) }}" required>
                        @error('inici') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Fi</label>
                        <input type="time" name="fi" value="{{ old('fi', substr($franja->fi,0,5)) }}" required>
                        @error('fi') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:flex; gap:10px; margin-top:12px;">
                        <button class="btn btn-primary" type="submit">Desar</button>
                        <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">Cancel·lar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
