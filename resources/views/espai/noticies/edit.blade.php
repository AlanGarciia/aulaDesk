@vite(['resources/css/espai/noticies/noticies.css'])

<div class="news-paper-clean">
    <form method="POST"
          action="{{ route('espai.noticies.update', $noticia) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- TITULAR --}}
        <input
            class="title"
            name="titol"
            placeholder="Titular"
            value="{{ old('titol', $noticia->titol) }}"
            required
        >

        {{-- META --}}
        <div class="meta">
            <select name="tipus">
                @foreach($tipusDisponibles as $t)
                    <option value="{{ $t }}"
                        @selected(old('tipus', $noticia->tipus) === $t)>
                        {{ ucfirst($t) }}
                    </option>
                @endforeach
            </select>

            <input type="file" name="imatge" accept="image/*">
        </div>

        {{-- IMAGEN ACTUAL --}}
        @if ($noticia->imatge_path)
            <div class="current-image">
                <img
                    src="{{ asset('storage/'.$noticia->imatge_path) }}"
                    alt="Imatge actual"
                    loading="lazy"
                >

                <label class="remove-image">
                    <input type="checkbox" name="treure_imatge" value="1">
                    Treure la imatge actual
                </label>
            </div>
        @endif

        {{-- CONTENIDO --}}
        <textarea
            name="contingut"
            placeholder="Escriu la notícia…"
            rows="14"
        >{{ old('contingut', $noticia->contingut) }}</textarea>

        {{-- ACCIONES --}}
        <div class="actions">
            <a href="{{ route('espai.noticies.index') }}">Cancel·lar</a>
            <button type="submit">Desar canvis</button>
        </div>
    </form>
</div>
