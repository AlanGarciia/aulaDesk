@vite(['resources/css/espai/noticies/noticies.css'])

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Editar notícia</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('espai.noticies.update', $noticia) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>Títol</label>
                        <input name="titol" value="{{ old('titol', $noticia->titol) }}" required>
                        @error('titol') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Tipus</label>
                        <select name="tipus" required>
                            @foreach($tipusDisponibles as $t)
                                <option value="{{ $t }}" @selected(old('tipus', $noticia->tipus) === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('tipus') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    @if ($noticia->imatge_path)
                        <div class="field">
                            <label>Imatge actual</label>

                            <div style="margin-top:10px">
                                <img
                                    src="{{ asset('storage/'.$noticia->imatge_path) }}"
                                    alt="imatge actual"
                                    style="max-width: 320px; border-radius: 12px; display:block;"
                                    loading="lazy"
                                >
                            </div>

                            <label style="display:flex; gap:10px; align-items:center; margin-top:12px;">
                                <input type="checkbox" name="treure_imatge" value="1"
                                       @checked(old('treure_imatge') == 1)>
                                Treure la imatge actual
                            </label>

                            @error('treure_imatge') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    <div class="field">
                        <label>Canviar imatge (opcional)</label>
                        <input type="file" name="imatge" accept="image/*">
                        <div class="help">Si puges una nova imatge, substituirà l’anterior.</div>
                        @error('imatge') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Contingut (opcional)</label>
                        <textarea name="contingut" rows="5">{{ old('contingut', $noticia->contingut) }}</textarea>
                        @error('contingut') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="actions">
                        <button class="btn btn-primary" type="submit">Desar canvis</button>
                        <a class="btn btn-secondary" href="{{ route('espai.noticies.index') }}">Cancel·lar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
