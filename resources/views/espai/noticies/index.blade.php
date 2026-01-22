<x-app-layout>
    {{-- Carga CSS (mejor aquí que dentro del header slot) --}}
    @vite(['resources/css/espai/noticies/noticies.css'])

    <x-slot name="header">
        <div class="page-header">
            <div class="page-header__text">
                <h2 class="page-title">Tauló de notícies</h2>
                <p class="page-subtitle">Les últimes novetats de l’espai, en format feed.</p>
            </div>

            <div class="page-header__actions">
                <a class="btn btn-primary" href="{{ route('espai.noticies.create') }}">
                    + Nova notícia
                </a>
                <a class="btn btn-secondary" href="{{ route('espai.index') }}">
                    Tornar a l’espai
                </a>
            </div>
        </div>
    </x-slot>

    <div class="page">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">
                    <span class="alert-dot"></span>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <div class="feed">
                @forelse($noticies as $n)
                    <article class="post">
                        <header class="post__header">
                            <div class="post__title-wrap">
                                <h3 class="post__title">{{ $n->titol }}</h3>
                                <div class="post__meta">
                                    <span class="pill">{{ $n->tipus }}</span>
                                    <span class="dot">•</span>
                                    <span>{{ $n->created_at->format('d/m/Y') }}</span>
                                    <span class="dot">•</span>
                                    <span>Reaccions: <strong>{{ $n->reaccions_count }}</strong></span>
                                </div>
                            </div>

                            <div class="post__actions">
                                <form class="inline-form" method="POST" action="{{ route('espai.noticies.reaccio', $n) }}">
                                    @csrf
                                    <input type="hidden" name="tipus" value="like">
                                    <button class="icon-btn" type="submit" title="M'agrada">
                                        👍 <span class="sr-only">M'agrada</span>
                                    </button>
                                </form>

                                <form class="inline-form" method="POST" action="{{ route('espai.noticies.destroy', $n) }}"
                                      onsubmit="return confirm('Eliminar la notícia?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </header>

                        @if($n->imatge_path)
                            <div class="post__media">
                                <img
                                    src="{{ asset('storage/'.$n->imatge_path) }}"
                                    alt="imatge"
                                    loading="lazy"
                                >
                            </div>
                        @endif

                        @if($n->contingut)
                            <div class="post__content">
                                <p>{{ $n->contingut }}</p>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="empty">
                        <div class="empty__icon">📰</div>
                        <h3 class="empty__title">Encara no hi ha notícies</h3>
                        <p class="empty__text">Crea la primera notícia perquè aparegui aquí.</p>
                        <a class="btn btn-primary" href="{{ route('espai.noticies.create') }}">+ Nova notícia</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>