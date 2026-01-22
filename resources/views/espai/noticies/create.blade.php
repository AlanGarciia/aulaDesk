@vite(['resources/css/espai/noticies/noticies.css'])

<div class="news-paper-clean">
    <form method="POST" action="{{ route('espai.noticies.store') }}" enctype="multipart/form-data">
        @csrf

        <input
            class="title"
            name="titol"
            placeholder="Titular"
            value="{{ old('titol') }}"
            required
        >

        <div class="meta">
            <select name="tipus">
                @foreach($tipus as $t)
                    <option value="{{ $t }}" @selected(old('tipus','noticia')===$t)>
                        {{ ucfirst($t) }}
                    </option>
                @endforeach
            </select>

            <input type="file" name="imatge" accept="image/*">
        </div>

        <textarea
            name="contingut"
            placeholder="Escriu la notícia…"
            rows="14"
        >{{ old('contingut') }}</textarea>

        <div class="actions">
            <a href="{{ route('espai.noticies.index') }}">Cancel·lar</a>
            <button type="submit">Publicar</button>
        </div>
    </form>
</div>
