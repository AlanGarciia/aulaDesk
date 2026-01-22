@vite(['resources/css/espai/noticies/noticies.css'])
<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Nova notícia</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.noticies.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="field">
                        <label>Títol</label>
                        <input name="titol" value="{{ old('titol') }}" required>
                        @error('titol') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Tipus</label>
                        <select name="tipus" required>
                            @foreach($tipus as $t)
                                <option value="{{ $t }}" @selected(old('tipus','noticia')===$t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('tipus') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Imatge (opcional)</label>
                        <input type="file" name="imatge" accept="image/*">
                        @error('imatge') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Contingut (opcional)</label>
                        <textarea name="contingut" rows="5">{{ old('contingut') }}</textarea>
                        @error('contingut') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="actions">
                        <button class="btn btn-primary" type="submit">Publicar</button>
                        <a class="btn btn-secondary" href="{{ route('espai.noticies.index') }}">Cancel·lar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
