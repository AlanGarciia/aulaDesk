<x-app-layout>
    @vite([
        'resources/css/espai/noticies/noticiesIndex.css',
        'resources/css/espai/noticies/noticiesShow.css',
    ])

    <div class="page">
        <div class="container">
            <div class="page-header" style="margin-bottom:24px;">
                <div class="page-header__text">
                    <h2 class="page-title">{{ __('messages.news_single') }}</h2>
                </div>

                <div class="page-header__actions">
                    <a class="btn btn-secondary" href="{{ route('espai.noticies.index') }}">
                        <i class="bi bi-arrow-left"></i> {{ __('messages.back_to_board') }}
                    </a>
                    <a class="btn btn-secondary" href="{{ route('espai.index') }}">
                        <i class="bi bi-box-arrow-left"></i> {{ __('messages.back_to_space') }}
                    </a>
                </div>
            </div>

            @php
                $layout = $noticia->layout ?? 'top';
                $hasImg = !empty($noticia->imatge_path);
                // si no hay imagen, forzamos modo solo texto
                if (!$hasImg) $layout = 'text';
            @endphp

            <article class="post">
                <header class="post__header">
                    <div class="post__meta">
                        <span class="pill">{{ $noticia->tipus }}</span>
                        <span class="dot">•</span>
                        <span>{{ $noticia->created_at->format('d/m/Y') }}</span>
                        <span class="dot">•</span>
                        <span>
                            {{ __('messages.reactions') }}:
                            <strong>{{ $noticia->reaccions_count ?? $noticia->reaccions()->count() }}</strong>
                        </span>

                        @if(!empty($noticia->usuari_espai_id))
                            <span class="dot">•</span>
                            <span>{{ __('messages.author') }}: <strong>{{ $noticia->autor->nom ?? $noticia->usuari_espai_id }}</strong></span>
                        @endif
                    </div>
                </header>

                <div class="show-body">
                    <div class="show-layout show-layout--{{ $layout }}">

                        @if($hasImg)
                            <div class="show-media">
                                <img
                                    src="{{ asset('storage/'.$noticia->imatge_path) }}"
                                    alt="{{ __('messages.image') }}"
                                    loading="lazy"
                                >
                            </div>
                        @endif

                        <div class="show-text">
                            <h3 class="show-title">{{ $noticia->titol }}</h3>

                            @if($noticia->contingut)
                                <div class="show-content">{{ $noticia->contingut }}</div>
                            @else
                                <div style="color:var(--muted);">{{ __('messages.no_content') }}</div>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="post__actions">
                    <form class="inline-form" method="POST" action="{{ route('espai.noticies.reaccio', $noticia) }}">
                        @csrf
                        <input type="hidden" name="tipus" value="like">
                        <button class="icon-btn" type="submit" title="{{ __('messages.i_like') }}">
                            👍 <span class="sr-only">{{ __('messages.i_like') }}</span>
                        </button>
                    </form>

                    @if ((int) session('usuari_espai_id') === (int) $noticia->usuari_espai_id)
                        <a class="btn btn-secondary" href="{{ route('espai.noticies.edit', $noticia) }}">
                            {{ __('messages.edit') }}
                        </a>

                        <form class="inline-form" method="POST" action="{{ route('espai.noticies.destroy', $noticia) }}"
                              onsubmit="return confirm('{{ __('messages.news_delete_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                        </form>
                    @endif
                </div>
            </article>
        </div>
    </div>
</x-app-layout>