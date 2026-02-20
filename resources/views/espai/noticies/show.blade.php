<x-app-layout>
    @vite(['resources/css/espai/noticies/noticiesIndex.css'])

    <x-slot name="header">
        <div class="page-header">
            <div class="page-header__text">
                <h2 class="page-title">Notícia</h2>
            </div>

            <div class="page-header__actions">
                <a class="btn btn-secondary" href="{{ route('espai.noticies.index') }}">
                    Tornar al tauló
                </a>
                <a class="btn btn-secondary" href="{{ route('espai.index') }}">
                    Tornar a l’espai
                </a>
            </div>
        </div>
    </x-slot>

    <div class="page">
        <div class="container">
            <article class="post">
                <header class="post__header">
                    <div class="post__meta">
                        <span class="pill">{{ $noticia->tipus }}</span>
                        <span class="dot">•</span>
                        <span>{{ $noticia->created_at->format('d/m/Y') }}</span>
                        <span class="dot">•</span>
                        <span>
                            Reaccions:
                            <strong>{{ $noticia->reaccions_count ?? $noticia->reaccions()->count() }}</strong>
                        </span>

                        @if(!empty($noticia->usuari_espai_id))
                            <span class="dot">•</span>
                            <span>Autor: <strong>{{ $noticia->usuari_espai_id }}</strong></span>
                        @endif
                    </div>
                </header>

                <style>
                    /* ✅ només per aquesta vista */
                    .show-grid{
                        display: grid;
                        grid-template-columns: 340px 1fr;
                        gap: 18px;
                        align-items: start;
                    }

                    @media (max-width: 900px){
                        .show-grid{
                            grid-template-columns: 1fr;
                        }
                    }

                    .show-media img{
                        width: 100%;
                        display: block;
                        border: 2px solid #000;
                    }

                    /* ✅ lectura bona i respecta salts de línia */
                    .show-content{
                        white-space: pre-wrap;
                        word-break: break-word;
                        overflow-wrap: anywhere;
                    }

                    /* ✅ desactiva el dropcap del teu CSS només en show */
                    .show-content:first-letter{
                        float: none !important;
                        font-size: inherit !important;
                        line-height: inherit !important;
                        padding: 0 !important;
                        font-weight: inherit !important;
                    }
                </style>

                <div style="padding:16px 18px;">
                    <div class="show-grid">
                        @if($noticia->imatge_path)
                            <div class="show-media">
                                <img
                                    src="{{ asset('storage/'.$noticia->imatge_path) }}"
                                    alt="imatge"
                                    loading="lazy"
                                >
                            </div>
                        @endif

                        <div>
                            <h3 class="post__title" style="margin:0 0 10px 0;">
                                {{ $noticia->titol }}
                            </h3>

                            <div class="post__content" style="padding:0;">
                                @if($noticia->contingut)
                                    <div class="show-content">{{ $noticia->contingut }}</div>
                                @else
                                    <div style="color:var(--muted);">Sense contingut.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="post__actions">
                    <form class="inline-form" method="POST" action="{{ route('espai.noticies.reaccio', $noticia) }}">
                        @csrf
                        <input type="hidden" name="tipus" value="like">
                        <button class="icon-btn" type="submit" title="M'agrada">
                            👍 <span class="sr-only">M'agrada</span>
                        </button>
                    </form>

                    @if ((int) session('usuari_espai_id') === (int) $noticia->usuari_espai_id)
                        <a class="btn btn-secondary" href="{{ route('espai.noticies.edit', $noticia) }}">
                            Editar
                        </a>

                        <form class="inline-form" method="POST" action="{{ route('espai.noticies.destroy', $noticia) }}"
                              onsubmit="return confirm('Eliminar la notícia?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Eliminar</button>
                        </form>
                    @endif
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
