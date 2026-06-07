<x-app-layout>
    <a href="{{ route('espai.index') }}" class="btn btn-secondary btn-top-right">
        <i class="bi bi-box-arrow-left"></i> {{ __('messages.back_to_space') }}
    </a>
    @vite(['resources/css/espai/noticies/noticiesIndex.css'])

    <x-slot name="header">
        <div class="page-header">
            <div class="page-header__text">
                <h2 class="page-title">{{ __('messages.news_board_title') }}</h2>
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
                        <label class="filters__label" for="tipus">{{ __('messages.filter') }}</label>
                        <select id="tipus" name="tipus" class="filters__select" onchange="this.form.submit()">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($tipusDisponibles as $t)
                                <option value="{{ $t }}" @selected(($tipusSeleccionat ?? '') === $t)>
                                    {{ ucfirst($t) }}
                                </option>
                            @endforeach
                        </select>

                        @if(!empty($tipusSeleccionat))
                            <a class="filters__clear" href="{{ route('espai.noticies.index') }}">
                                {{ __('messages.clear') }}
                            </a>
                        @endif
                    </div>

                    <div class="filters__right">
                        <span class="filters__count">
                            {{ __('messages.showing') }} <strong>{{ $noticies->count() }}</strong>
                            {{ $noticies->count() === 1 ? __('messages.result') : __('messages.results') }}
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
                                                {{ __('messages.status_pending') }}
                                            </span>
                                        @elseif($solAcceptada)
                                            <span class="pill" style="background: rgba(16,185,129,.12); color:#065f46; border:1px solid rgba(16,185,129,.30);">
                                                {{ __('messages.status_accepted') }}
                                            </span>

                                            @if($cobridor)
                                                <span class="dot">•</span>
                                                <span>
                                                    {{ __('messages.cover_person') }}:
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
                                        {{ __('messages.reactions') }}:
                                        <strong>{{ $n->reaccions_count }}</strong>
                                    </span>

                                    @if(!empty($n->usuari_espai_id))
                                        <span class="dot">•</span>
                                        <span>
                                            {{ __('messages.author') }}:
                                            <strong>{{ $n->autor->nom ?? __('messages.unknown_author') }}</strong>
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
                                    <button type="submit" class="icon-btn" title="{{ __('messages.i_like') }}">
                                        👍
                                    </button>
                                </form>

                                {{-- Botó acceptar guardia --}}
                                @if($isGuardia && $sol && $solPendent && !$esMeva)
                                    <form method="POST" action="{{ route('espai.guardia.acceptar', $sol) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('messages.accept_guardia') }}
                                        </button>
                                    </form>
                                @endif

                                {{-- Pendent meva --}}
                                @if($isGuardia && $sol && $solPendent && $esMeva)
                                    <span class="pill" style="background: rgba(59,130,246,.10); color:#1e3a8a; border:1px solid rgba(59,130,246,.25);">
                                        {{ __('messages.pending_yours') }}
                                    </span>
                                @endif

                                {{-- Edit/Delete --}}
                                @if(!$isGuardia && (int) session('usuari_espai_id') === (int) $n->usuari_espai_id)
                                    <a class="btn btn-secondary" href="{{ route('espai.noticies.edit', $n) }}">
                                        {{ __('messages.edit') }}
                                    </a>

                                    <button type="button" class="btn btn-danger btn-delete" data-action="{{ route('espai.noticies.destroy', $n) }}">
                                        {{ __('messages.delete') }}
                                    </button>
                                @endif
                            </div>
                        </header>

                        @if($n->imatge_path)
                            <div class="post__media">
                                <img src="{{ asset('storage/'.$n->imatge_path) }}" alt="{{ __('messages.image') }}" loading="lazy">
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
                                        {{ __('messages.read_more') }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="empty">
                        <div class="empty__icon">📰</div>
                        <h3 class="empty__title">{{ __('messages.news_empty_title') }}</h3>
                        <p class="empty__text">
                            {{ __('messages.news_empty_text') }}
                        </p>
                        <a class="btn btn-primary" href="{{ route('espai.noticies.create') }}">
                            + {{ __('messages.news_new') }}
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
            <h3 class="modal-periodic__title">{{ __('messages.news_delete_title') }}</h3>
            <p class="modal-periodic__text">
                {{ __('messages.news_delete_text') }}
            </p>

            <div class="modal-periodic__actions">
                <button id="cancelDelete" class="btn btn-secondary">{{ __('messages.cancel') }}</button>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
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
        + {{ __('messages.news_new') }}
    </a>
@endif
</x-app-layout>