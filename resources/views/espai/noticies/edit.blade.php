@vite([
    'resources/css/espai/noticies/noticies.css',
    'resources/css/espai/noticies/noticiesEdit.css',
])

<x-app-layout>

    <div class="news-create-page">
        <div class="news-sheet">

            <a href="{{ route('espai.noticies.index') }}" class="news-back">
                <i class="bi bi-arrow-left"></i> {{ __('messages.back_to_board') }}
            </a>

            <div class="news-sheet__head">
                <span class="news-sheet__kicker">{{ __('messages.news_edit') }}</span>
            </div>

            <form method="POST" action="{{ route('espai.noticies.update', $noticia) }}" enctype="multipart/form-data" id="newsForm">
                @csrf
                @method('PUT')

                <input
                    class="news-title-input"
                    id="f-title"
                    name="titol"
                    placeholder="{{ __('messages.news_headline_placeholder') }}"
                    value="{{ old('titol', $noticia->titol) }}"
                    required
                >
                @error('titol') <span class="news-error">{{ $message }}</span> @enderror

                <div class="news-meta">
                    <div class="news-field">
                        <label>{{ __('messages.type') }}</label>
                        <select name="tipus">
                            @foreach($tipusDisponibles as $t)
                                <option value="{{ $t }}" @selected(old('tipus', $noticia->tipus) === $t)>
                                    {{ ucfirst($t) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="news-field">
                        <label>{{ __('messages.image') }}</label>
                        <input type="file" name="imatge" id="f-image" accept="image/*">
                    </div>
                </div>
                @error('imatge') <span class="news-error">{{ $message }}</span> @enderror

                {{-- IMAGEN ACTUAL --}}
                @if ($noticia->imatge_path)
                    <div class="news-current-image">
                        <img src="{{ asset('storage/'.$noticia->imatge_path) }}"
                             alt="{{ __('messages.current_image') }}" loading="lazy">
                        <label class="news-remove-image">
                            <input type="checkbox" name="treure_imatge" value="1" id="f-removeimg">
                            {{ __('messages.remove_current_image') }}
                        </label>
                    </div>
                @endif

                <textarea
                    class="news-content-input"
                    id="f-content"
                    name="contingut"
                    placeholder="{{ __('messages.news_content_placeholder') }}"
                    rows="10"
                >{{ old('contingut', $noticia->contingut) }}</textarea>

                {{-- SELECTOR DE LAYOUT --}}
                <div class="layout-picker">
                    <span class="layout-picker__label">{{ __('messages.layout_label') }}</span>
                    <div class="layout-picker__options">
                        @php $layouts = ['top','bottom','left','right','text']; @endphp
                        @foreach($layouts as $lay)
                            <label class="layout-opt" data-layout="{{ $lay }}">
                                <input type="radio" name="layout" value="{{ $lay }}"
                                    @checked(old('layout', $noticia->layout ?? 'top') === $lay)>
                                <span class="layout-thumb layout-thumb--{{ $lay }}">
                                    <span class="lt-img"></span>
                                    <span class="lt-lines"><i></i><i></i><i></i></span>
                                </span>
                                <span class="layout-opt__name">{{ __('messages.layout_'.$lay) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- PREVIEW EN VIVO --}}
                <div class="news-preview">
                    <div class="news-preview__head">{{ __('messages.preview') }}</div>
                    <div class="news-preview__card" id="previewCard">
                        <div class="pv-grid" id="pvGrid">
                            <div class="pv-media" id="pvMedia">
                                <div class="pv-media__empty">{{ __('messages.image') }}</div>
                                <img id="pvImg" alt=""
                                    @if($noticia->imatge_path) src="{{ asset('storage/'.$noticia->imatge_path) }}" style="display:block;" @else style="display:none;" @endif>
                            </div>
                            <div class="pv-text">
                                <h3 class="pv-title" id="pvTitle"></h3>
                                <p class="pv-content" id="pvContent"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="news-actions">
                    <a href="{{ route('espai.noticies.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const titleI   = document.getElementById('f-title');
        const contentI = document.getElementById('f-content');
        const imageI   = document.getElementById('f-image');
        const removeI  = document.getElementById('f-removeimg');
        const pvTitle  = document.getElementById('pvTitle');
        const pvContent= document.getElementById('pvContent');
        const pvImg    = document.getElementById('pvImg');
        const pvMedia  = document.getElementById('pvMedia');
        const pvGrid   = document.getElementById('pvGrid');
        const layoutRadios = document.querySelectorAll('input[name="layout"]');

        const phTitle   = @json(__('messages.preview_title_ph'));
        const phContent = @json(__('messages.preview_content_ph'));

        const originalImg = pvImg.getAttribute('src') || '';

        function syncText(){
            pvTitle.textContent   = titleI.value.trim()   || phTitle;
            pvContent.textContent = contentI.value.trim() || phContent;
        }

        function syncImage(){
            // si marca "quitar imagen", la preview no muestra foto
            if (removeI && removeI.checked && !(imageI.files && imageI.files[0])) {
                pvImg.style.display = 'none';
                pvMedia.classList.remove('has-img');
                return;
            }

            const file = imageI.files && imageI.files[0];
            if (file) {
                pvImg.src = URL.createObjectURL(file);
                pvImg.style.display = 'block';
                pvMedia.classList.add('has-img');
            } else if (originalImg) {
                pvImg.src = originalImg;
                pvImg.style.display = 'block';
                pvMedia.classList.add('has-img');
            } else {
                pvImg.style.display = 'none';
                pvMedia.classList.remove('has-img');
            }
        }

        function syncLayout(){
            const val = document.querySelector('input[name="layout"]:checked')?.value || 'top';
            pvGrid.className = 'pv-grid pv-grid--' + val;
            document.querySelectorAll('.layout-opt').forEach(o => {
                o.classList.toggle('is-active', o.dataset.layout === val);
            });
        }

        titleI.addEventListener('input', syncText);
        contentI.addEventListener('input', syncText);
        imageI.addEventListener('change', syncImage);
        if (removeI) removeI.addEventListener('change', syncImage);
        layoutRadios.forEach(r => r.addEventListener('change', syncLayout));

        syncText();
        syncImage();
        syncLayout();
    });
    </script>
    @endpush
</x-app-layout>