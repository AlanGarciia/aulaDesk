<x-app-layout>
    <a href="{{ route('espai.index') }}"class="btn btn-secondary btn-top-right">
        Tornar a l'espai
    </a>
    @vite(['resources/css/espai/noticies/noticiesIndex.css'])

    <x-slot name="header">
        <div class="page-header">
            <div class="page-header__text">
                <h2 class="page-title">Tauló de notícies</h2>
            </div>

            <div class="page-header__actions page-header__actions--with-exit">
                <div class="page-header__actions-left">
                </div>
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

            @if (session('ok'))
                <div class="alert alert-success">
                    <span class="alert-dot"></span>
                    <div>{{ session('ok') }}</div>
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
                            <a class="filters__clear" href="{{ route('espai.noticies.index') }}">
                                Netejar
                            </a>
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
                        $isGuardia = ((string) $n->tipus === 'guardia');

                        $sol = null;
                        if (isset($solByNoticiaId) && isset($solByNoticiaId[$n->id])) {
                            $sol = $solByNoticiaId[$n->id];
                        }

                        $estatSol = '';
                        $solPendent = false;
                        $solAcceptada = false;

                        if ($sol) {
                            if (isset($sol->estat) && $sol->estat) {
                                $estatSol = (string) $sol->estat;
                            }
                            if ($estatSol === 'pendent') $solPendent = true;
                            if ($estatSol === 'acceptada') $solAcceptada = true;
                        }

                        $meuUsuariEspaiId = (int) session('usuari_espai_id');

                        $esMeva = false;
                        if ($sol && isset($sol->solicitant_usuari_espai_id)) {
                            $esMeva = ((int) $sol->solicitant_usuari_espai_id === $meuUsuariEspaiId);
                        }

                        $cobridor = null;
                        if ($sol && isset($sol->cobridor_usuari_espai_id) && $sol->cobridor_usuari_espai_id) {
                            $cobridor = $sol->cobridor; // ← RELACIÓN
                        }
                    @endphp

                    <article class="post">
                        <header class="post__header">
                            <div class="post__title-wrap">
                                <h3 class="post__title">{{ $n->titol }}</h3>

                                <div class="post__meta">
                                    <span class="pill">{{ $n->tipus }}</span>

                                    @if($isGuardia)
                                        <span class="dot">•</span>

                                        @if($solPendent)
                                            <span class="pill" style="background: rgba(245,158,11,.15); color:#92400e; border:1px solid rgba(245,158,11,.35);">
                                                Pendent
                                            </span>
                                        @elseif($solAcceptada)
                                            <span class="pill" style="background: rgba(16,185,129,.12); color:#065f46; border:1px solid rgba(16,185,129,.30);">
                                                Acceptada
                                            </span>

                                            @if($cobridor)
                                                <span class="dot">•</span>
                                                <span>
                                                    Cobridor:
                                                    <strong>{{ $cobridor->nom }}</strong>
                                                </span>
                                            @endif
                                        @else
                                            <span class="pill" style="background: rgba(239,68,68,.10); color:#7f1d1d; border:1px solid rgba(239,68,68,.25);">
                                                {{ $estatSol !== '' ? $estatSol : '—' }}
                                            </span>
                                        @endif
                                    @endif

                                    <span class="dot">•</span>
                                    <span>{{ $n->created_at->format('d/m/Y') }}</span>

                                    <span class="dot">•</span>
                                    <span>
                                        Reaccions:
                                        <strong>{{ $n->reaccions_count }}</strong>
                                    </span>

                                    @if(!empty($n->usuari_espai_id))
                                        <span class="dot">•</span>
                                        <span>
                                            Autor:
                                            <strong>{{ $n->autor->nom ?? 'Autor desconegut' }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="post__actions">
                                {{-- Reacció like --}}
                                <form method="POST"
                                      action="{{ route('espai.noticies.reaccio', $n) }}"
                                      class="inline-form">
                                    @csrf
                                    <input type="hidden" name="tipus" value="like">
                                    <button type="submit" class="icon-btn" title="M'agrada">
                                        👍
                                    </button>
                                </form>

                                {{-- Botó acceptar guardia --}}
                                @if($isGuardia && $sol && $solPendent && !$esMeva)
                                    <form method="POST" action="{{ route('espai.guardies.acceptar', $sol) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            Acceptar guàrdia
                                        </button>
                                    </form>
                                @endif

                                {{-- Pendent meva --}}
                                @if($isGuardia && $sol && $solPendent && $esMeva)
                                    <span class="pill" style="background: rgba(59,130,246,.10); color:#1e3a8a; border:1px solid rgba(59,130,246,.25);">
                                        Pendent (teva)
                                    </span>
                                @endif

                                {{-- Edit/Delete --}}
                                @if(!$isGuardia && (int) session('usuari_espai_id') === (int) $n->usuari_espai_id)
                                    <a class="btn btn-secondary" href="{{ route('espai.noticies.edit', $n) }}">
                                        Editar
                                    </a>

                                    <button type="button" class="btn btn-danger btn-delete" data-action="{{ route('espai.noticies.destroy', $n) }}">
                                        Eliminar
                                    </button>
                                @endif
                            </div>
                        </header>

                        @if($n->imatge_path)
                            <div class="post__media">
                                <img src="{{ asset('storage/'.$n->imatge_path) }}" alt="imatge" loading="lazy">
                            </div>
                        @endif

                        @if($n->contingut)
                            @php
                                $limit = 260;
                                $text = trim((string) $n->contingut);
                                $isLong = mb_strlen($text) > $limit;
                            @endphp

                            <div class="post__content">
                                <p class="post__text post__text--clamp">{{ $text }}</p>

                                @if($isLong)
                                    <a class="btn btn-secondary post__more" href="{{ route('espai.noticies.show', $n) }}">
                                        Veure més
                                    </a>
                                @endif
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="empty">
                        <div class="empty__icon">📰</div>
                        <h3 class="empty__title">Encara no hi ha notícies</h3>
                        <p class="empty__text">
                            Crea la primera notícia perquè aparegui aquí.
                        </p>
                        <a class="btn btn-primary" href="{{ route('espai.noticies.create') }}">
                            + Nova notícia
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    @push('modals')
    <div id="deleteModal" class="modal-periodic">
        <div class="modal-periodic__content">
            <span class="modal-periodic__close">&times;</span>

            <div class="modal-periodic__icon">📰</div>
            <h3 class="modal-periodic__title">Eliminar notícia</h3>
            <p class="modal-periodic__text">
                Estàs segur que vols eliminar aquesta notícia? Aquesta acció no es pot desfer.
            </p>

            <div class="modal-periodic__actions">
                <button id="cancelDelete" class="btn btn-secondary">Cancelar</button>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
    @endpush

    {{-- JS --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('deleteModal');
        if (!modal) return;

        const closeBtn = modal.querySelector('.modal-periodic__close');
        const cancelBtn = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');

        function openModal(action) {
            deleteForm.setAttribute('action', action);
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => openModal(btn.dataset.action));
        });

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        window.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });

        window.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });
    });
    </script>
    @endpush
    @if($noticies->count() > 0)
    <a href="{{ route('espai.noticies.create') }}" class="btn-create-fixed">
        + Nova notícia
    </a>
@endif
</x-app-layout>
