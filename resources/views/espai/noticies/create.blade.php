@vite(['resources/css/espai/noticies/noticies.css'])

<div class="news-paper-clean">
    <form method="POST" action="{{ route('espai.noticies.store') }}" enctype="multipart/form-data">
        @csrf

        <input
            class="title"
            name="titol"
            placeholder="{{ __('messages.news_headline_placeholder') }}"
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
            placeholder="{{ __('messages.news_content_placeholder') }}"
            rows="14"
        >{{ old('contingut') }}</textarea>

        <div class="actions">
            <a href="{{ route('espai.noticies.index') }}">{{ __('messages.cancel') }}</a>
            <button type="submit">{{ __('messages.publish') }}</button>
        </div>
    </form>
</div>