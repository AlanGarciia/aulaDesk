<x-app-layout>
    @vite(['resources/css/espai/noticies/noticiesIndex.css'])

    <x-slot name="header">
        <div class="page-header">
            <div class="page-header__text">
                <h2 class="page-title">Tauló de notícies</h2>
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

            <div class="filters">
                <form method="GET" action="{{ route('espai.noticies.index') }}" class="filters__form">
                    <div class="filters__left">
                        <label class="filters__label" for="tipus">Filtrar</label>
                        <select id="tipus" name="tipus" class="filters__select" onchange="this.form.submit()">
                            <option value="">Tots</option>
                            @foreach($tipusDisponibles as $t)
                                <option value="{{ $t }}" @selected(($tipusSeleccionat ?? '') === $t)>
                                    {{ ucfirst($t) }}
                                </option>
                            @endforeach
                        </select>

                        @if(!empty($tipusSeleccionat))
                            <a class="filters__clear" href="{{ route('espai.noticies.index') }}">Netejar</a>
                        @endif
                    </div>

                    <div class="filters__right">
                        <span class="filters__count">
                            Mostrant <strong>{{ $noticies->count() }}</strong>
                            {{ $noticies->count() === 1 ? 'resultat' : 'resultats' }}
                        </span>
                    </div>
                </form>
            </div>

            <div class="feed">
                @forelse($noticies as $n)
                    @php
                    //esto no lo borres gonza que me la lias
                    //es para poner un limite de letras
                        $limit = 320;
                        $isLong = !empty($n->contingut) && mb_strlen($n->contingut) > $limit;
                    @endphp

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

                                    @if(!empty($n->usuari_espai_id))
                                        <span class="dot">•</span>
                                        <span>Autor: <strong>{{ $n->usuari_espai_id }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </header>

                        @if($n->imatge_path)
                            <div class="post__media">
                                <img src="{{ asset('storage/'.$n->imatge_path) }}" alt="imatge" loading="lazy">
                            </div>
                        @endif
                        @if($n->contingut)
                            <div class="post__content">
                                <p
                                    style="
                                        display:-webkit-box;
                                        -webkit-box-orient:vertical;
                                        -webkit-line-clamp:4;
                                        overflow:hidden;
                                        margin:0;
                                    "
                                >
                                    {{ $n->contingut }}
                                </p>

                                @if($isLong)
                                    <div style="margin-top:12px;">
                                        <a class="btn btn-secondary" href="{{ route('espai.noticies.show', $n) }}">
                                            Veure més
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="post__actions">
                            <form class="inline-form" method="POST" action="{{ route('espai.noticies.reaccio', $n) }}">
                                @csrf
                                <input type="hidden" name="tipus" value="like">
                                <button class="icon-btn" type="submit" title="M'agrada">
                                    👍 <span class="sr-only">M'agrada</span>
                                </button>
                            </form>

                            @if ((int) session('usuari_espai_id') === (int) $n->usuari_espai_id)
                                <a class="btn btn-secondary" href="{{ route('espai.noticies.edit', $n) }}">
                                    Editar
                                </a>

                                <button type="button"
                                    class="btn btn-danger btn-delete"
                                    data-action="{{ route('espai.noticies.destroy', $n) }}">
                                    Eliminar
                                </button>
                            @endif
                        </div>
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
