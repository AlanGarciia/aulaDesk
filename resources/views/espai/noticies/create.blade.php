@vite(['resources/css/espai/noticies/noticies.css'])

<x-app-layout>

    <div class="news-create-page">
        <div class="news-sheet">

            <a href="{{ route('espai.noticies.index') }}" class="news-back">
                <i class="bi bi-arrow-left"></i> {{ __('messages.back_to_board') }}
            </a>

            <div class="news-sheet__head">
                <span class="news-sheet__kicker">{{ __('messages.news_new') }}</span>
            </div>

            <form method="POST" action="{{ route('espai.noticies.store') }}" enctype="multipart/form-data" id="newsForm">
                @csrf

                <input
                    class="news-title-input"
                    id="f-title"
                    name="titol"
                    placeholder="{{ __('messages.news_headline_placeholder') }}"
                    value="{{ old('titol') }}"
                    required
                >
                @error('titol') <span class="news-error">{{ $message }}</span> @enderror

                <div class="news-meta">
                    <div class="news-field">
                        <label>{{ __('messages.type') }}</label>
                        <select name="tipus">
                            @foreach($tipus as $t)
                                <option value="{{ $t }}" @selected(old('tipus','noticia')===$t)>
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

                <textarea
                    class="news-content-input"
                    id="f-content"
                    name="contingut"
                    placeholder="{{ __('messages.news_content_placeholder') }}"
                    rows="10"
                >{{ old('contingut') }}</textarea>

                {{-- SELECTOR DE LAYOUT --}}
                <div class="layout-picker">
                    <span class="layout-picker__label">{{ __('messages.layout_label') }}</span>
                    <div class="layout-picker__options">
                        @php $layouts = ['top','bottom','left','right','text']; @endphp
                        @foreach($layouts as $lay)
                            <label class="layout-opt" data-layout="{{ $lay }}">
                                <input type="radio" name="layout" value="{{ $lay }}"
                                    @checked(old('layout','top')===$lay)>
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
                                <img id="pvImg" alt="" style="display:none;">
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
                    <button type="submit" class="btn btn-primary">{{ __('messages.publish') }}</button>
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
        const pvTitle  = document.getElementById('pvTitle');
        const pvContent= document.getElementById('pvContent');
        const pvImg    = document.getElementById('pvImg');
        const pvMedia  = document.getElementById('pvMedia');
        const pvGrid   = document.getElementById('pvGrid');
        const layoutRadios = document.querySelectorAll('input[name="layout"]');

        const phTitle   = @json(__('messages.preview_title_ph'));
        const phContent = @json(__('messages.preview_content_ph'));

        function syncText(){
            pvTitle.textContent   = titleI.value.trim()   || phTitle;
            pvContent.textContent = contentI.value.trim() || phContent;
        }

        function syncImage(){
            const file = imageI.files && imageI.files[0];
            if (file) {
                pvImg.src = URL.createObjectURL(file);
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
        layoutRadios.forEach(r => r.addEventListener('change', syncLayout));

        syncText();
        syncImage();
        syncLayout();
    });
    </script>
    @endpush
</x-app-layout>