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
                        <span>Reaccions: <strong>{{ $noticia->reaccions_count ?? $noticia->reaccions()->count() }}</strong></span>

                        @if(!empty($noticia->usuari_espai_id))
                            <span class="dot">•</span>
                            <span>Autor: <strong>{{ $noticia->usuari_espai_id }}</strong></span>
                        @endif
                    </div>
                </header>

                {{-- Layout: imatge a l'esquerra (si hi ha) i text a la dreta --}}
                <div style="padding:16px 18px;">
                    <div style="display:flex; gap:18px; align-items:flex-start; flex-wrap:wrap;">
                        @if($noticia->imatge_path)
                            <div style="flex:0 0 340px; max-width:340px;">
                                <img
                                    src="{{ asset('storage/'.$noticia->imatge_path) }}"
                                    alt="imatge"
                                    loading="lazy"
                                    style="width:100%; display:block; border:2px solid #000;"
                                >
                            </div>
                        @endif

                        <div style="flex:1 1 320px; min-width:280px;">
                            {{-- Títol sobre el text --}}
                            <h3 class="post__title" style="margin:0 0 10px 0;">
                                {{ $noticia->titol }}
                            </h3>

                            <div class="post__content" style="padding:0;">
                                @if($noticia->contingut)
                                    <p style="margin:0;">{{ $noticia->contingut }}</p>
                                @else
                                    <p style="margin:0; color:var(--muted);">Sense contingut.</p>
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
