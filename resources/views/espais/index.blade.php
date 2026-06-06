<x-app-layout>

    @push('styles')
        @vite('resources/css/espais/espaisIndex.css')
    @endpush

    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">{{ __('messages.espais_index_title') }}</h2>

                @php
                    $plan = auth()->user()->plan;
                    $isFree = $plan === 'free';
                    $espaisCount = $espais->count();
                    $limitReached = $isFree && $espaisCount >= 1;
                @endphp

                <div style="display:flex; align-items:center; gap:10px;">

                    {{-- PLAN BADGE --}}
                    <div class="plan-badge {{ $plan }}">
                        @if($plan === 'premium')
                            <i class="bi bi-stars"></i> Premium
                        @else
                            <i class="bi bi-lock"></i> Free
                        @endif
                    </div>

                    {{-- USER MENU --}}
                    <div class="user-inline-menu">
                        <button class="nav-user-btn" id="userMenuBtn">
                            <i class="bi bi-person-circle"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down nav-caret"></i>
                        </button>

                        <div id="userMenu" class="nav-dropdown">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <i class="bi bi-gear"></i> {{ __('messages.Profile') }}
                            </a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item logout-item">
                                    <i class="bi bi-box-arrow-right"></i> {{ __('messages.logout_short') }}
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <div class="actions">

                <button class="btn btn-primary create-btn {{ $limitReached ? 'disabled-btn' : '' }}"
                    @if($limitReached) disabled @else onclick="window.location='{{ route('espais.create') }}'" @endif
                    title="{{ $limitReached ? __('messages.espais_limit_reached') : '' }}">
                    <i class="bi bi-plus"></i> {{ __('messages.espais_create_btn') }}
                </button>

                <button class="btn btn-secondary" onclick="window.location='{{ route('espais.plans.index') }}'">
                    <i class="bi bi-stars"></i> {{ __('messages.plans') }}
                </button>

            </div>

            {{-- ============================
                GRID DE ESPAIS (arrossegable)
            ============================ --}}
            <div class="tilt-grid">
                @forelse ($espais as $espai)
                    <div
                        class="tilt-card js-enter-card"
                        data-enter-url="{{ route('espais.entrar.form', $espai) }}"
                        data-espai-id="{{ $espai->id }}"
                        role="link"
                        tabindex="0"
                        aria-label="{{ __('messages.enter') }} {{ $espai->nom }}"
                    >
                        <div class="tilt-card__inner">

                            <div class="tilt-card__content">
                                <div class="tilt-card__title">{{ $espai->nom }}</div>

                                @if ($espai->descripcio)
                                    <div class="tilt-card__desc">{{ $espai->descripcio }}</div>
                                @endif

                                @if ((int) $espai->user_id !== (int) auth()->id())
                                    <div class="tilt-card__badge">{{ __('messages.shared_with_you') }}</div>
                                @endif
                            </div>

                            @if ((int) $espai->user_id === (int) auth()->id())
                                <div class="card-menu js-no-enter" data-no-enter="1">
                                    <button
                                        type="button"
                                        class="card-menu__toggle js-card-menu-btn"
                                        aria-label="{{ __('messages.options') }}"
                                    >
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <div class="card-menu__dropdown">
                                        <button
                                            type="button"
                                            class="card-menu__item"
                                            onclick="window.location='{{ route('espais.edit', $espai) }}'"
                                        >
                                            <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
                                        </button>

                                        <button
                                            type="button"
                                            class="card-menu__item share-btn"
                                            data-espai-name="{{ $espai->nom }}"
                                            data-action="{{ route('espais.compartir', $espai) }}"
                                        >
                                            <i class="bi bi-share"></i> {{ __('messages.share') }}
                                        </button>

                                        <button
                                            type="button"
                                            class="card-menu__item card-menu__item--danger delete-btn"
                                            data-espai-name="{{ $espai->nom }}"
                                            data-form-id="deleteForm-{{ $espai->id }}"
                                        >
                                            <i class="bi bi-trash"></i> {{ __('messages.delete') }}
                                        </button>
                                    </div>

                                    <form id="deleteForm-{{ $espai->id }}" action="{{ route('espais.destroy', $espai) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state-container">
                        <p class="empty-state">{{ __('messages.espais_empty') }}</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- ============================
        MODALS
    ============================ --}}
    <div id="confirmModal" class="modal" aria-hidden="true">
        <div class="modal-content modal-delete" role="dialog" aria-modal="true" aria-labelledby="confirmText">
            <p id="confirmText"></p>
            <div class="modal-actions">
                <button id="cancelBtn" class="btn-cancel" type="button">{{ __('messages.cancel') }}</button>
                <button id="confirmBtn" class="btn-delete" type="button">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <div id="successModal" class="modal" aria-hidden="true">
        <div class="modal-content modal-success" role="dialog" aria-modal="true" aria-labelledby="successText">
            <p id="successText"></p>
            <div class="modal-actions">
                <button id="successCloseBtn" class="btn btn-primary" type="button">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>

    <div id="createdModal" class="modal" aria-hidden="true">
        <div class="modal-content modal-success" role="dialog" aria-modal="true">

            <h3 style="margin-bottom:10px; text-align:center;">
                ✅ {{ __('messages.espais_created_title') }}
            </h3>

            <p style="text-align:center; margin-bottom:20px;">
                {{ __('messages.espais_default_user') }}<br><br>
                <strong>{{ __('messages.user') }}:</strong> admin<br>
                <strong>{{ __('messages.password') }}:</strong> admin
            </p>

            <div class="modal-actions" style="justify-content:center;">
                <button id="createdCloseBtn" class="btn btn-primary" type="button">
                    {{ __('messages.continue') }}
                </button>
            </div>

        </div>
    </div>

    <div id="shareModal" class="modal" aria-hidden="true">
        <div class="modal-content modal-success" role="dialog" aria-modal="true">
            <p id="shareTitle" style="margin-bottom:10px;"></p>

            <form id="shareForm" method="POST" action="">
                @csrf
                <input
                    type="email"
                    name="email"
                    id="shareEmail"
                    required
                    placeholder="{{ __('messages.user_email_placeholder') }}"
                    style="width:100%; padding:10px; border-radius:10px; margin-bottom:12px;"
                >

                <div class="modal-actions">
                    <button type="button" id="shareCancelBtn" class="btn-cancel">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.share') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: JA ETS PREMIUM --}}
    <div id="premiumModal" class="modal" aria-hidden="true">
        <div class="modal-content modal-success" role="dialog" aria-modal="true">
            <h3 style="margin-bottom:10px; text-align:center;">🎉 {{ __('messages.premium_title') }}</h3>

            <p style="text-align:center; margin-bottom:20px;">
                {{ __('messages.premium_text') }}
            </p>

            <div class="modal-actions" style="justify-content:center;">
                <button id="premiumCloseBtn" class="btn btn-primary" type="button">
                    {{ __('messages.continue') }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        /* ============================
        ENTRAR AL ESPAI AL CLICAR LA CARD
        ============================ */
        document.querySelectorAll('.js-enter-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (e.target.closest('.js-no-enter')) return;
                if (card.classList.contains('just-dragged')) return; // evita entrar tras arrossegar
                const url = card.dataset.enterUrl;
                if (url) window.location.href = url;
            });
        });

        /* ============================
        DRAG & DROP DELS ESPAIS
        ============================ */
        const grid = document.querySelector('.tilt-grid');
        if (grid && typeof Sortable !== 'undefined') {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            Sortable.create(grid, {
                animation: 160,
                draggable: '.tilt-card',
                filter: '.empty-state-container, .card-menu',
                preventOnFilter: false,
                ghostClass: 'card-dragging',
                onStart: function (evt) {
                    evt.item.classList.add('just-dragged');
                },
                onEnd: function (evt) {
                    // Pequeño retardo para que el click posterior no entre al espacio
                    setTimeout(() => evt.item.classList.remove('just-dragged'), 50);

                    const ordre = Array.from(grid.querySelectorAll('.tilt-card'))
                        .map(card => card.dataset.espaiId)
                        .filter(Boolean);

                    fetch("{{ route('espais.reordenar') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ ordre })
                    }).catch(err => console.error('Error guardant ordre:', err));
                }
            });
        }

        /* ============================
        MENU TRES PUNTITOS DE LA CARD
        ============================ */
        document.querySelectorAll('.js-card-menu-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const menu = btn.closest('.card-menu');
                const yaAbierto = menu.classList.contains('is-open');

                document.querySelectorAll('.card-menu.is-open').forEach(m => m.classList.remove('is-open'));

                if (!yaAbierto) menu.classList.add('is-open');
            });
        });

        /* ============================
        MENU USUARIO
        ============================ */
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');

        if (userMenuBtn && userMenu) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('is-open');
            });
        }

        // Cerrar menús al hacer clic fuera
        window.addEventListener('click', () => {
            document.querySelectorAll('.card-menu.is-open').forEach(m => m.classList.remove('is-open'));
            if (userMenu) userMenu.classList.remove('is-open');
        });

        /* ============================
        MODAL ELIMINAR ESPAI
        ============================ */
        const confirmModal = document.getElementById('confirmModal');
        const confirmText = document.getElementById('confirmText');
        const confirmBtn = document.getElementById('confirmBtn');
        const cancelBtn = document.getElementById('cancelBtn');

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();

                const espaiName = btn.dataset.espaiName;
                const formId = btn.dataset.formId;

                if (confirmText) {
                    confirmText.textContent = `{{ __('messages.espais_delete_confirm_js') }} "${espaiName}"?`;
                }
                if (confirmBtn) {
                    confirmBtn.dataset.formId = formId;
                }
                if (confirmModal) {
                    confirmModal.setAttribute('aria-hidden', 'false');
                    confirmModal.classList.add('is-open');
                }
            });
        });

        if (cancelBtn && confirmModal) {
            cancelBtn.addEventListener('click', () => {
                confirmModal.classList.remove('is-open');
                confirmModal.setAttribute('aria-hidden', 'true');
            });
        }
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                const formId = confirmBtn.dataset.formId;
                if (formId) {
                    const form = document.getElementById(formId);
                    if (form) form.submit();
                }
            });
        }

        /* ============================
        MODAL COMPARTIR
        ============================ */
        const shareModal = document.getElementById('shareModal');
        const shareTitle = document.getElementById('shareTitle');
        const shareForm = document.getElementById('shareForm');
        const shareCancelBtn = document.getElementById('shareCancelBtn');

        document.querySelectorAll('.share-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();

                const espaiName = btn.dataset.espaiName;
                const action = btn.dataset.action;

                if (shareTitle) {
                    shareTitle.textContent = `{{ __('messages.share') }} "${espaiName}"`;
                }
                if (shareForm) {
                    shareForm.action = action;
                }
                if (shareModal) {
                    shareModal.classList.add('is-open');
                    shareModal.setAttribute('aria-hidden', 'false');
                }
            });
        });

        if (shareCancelBtn && shareModal) {
            shareCancelBtn.addEventListener('click', () => {
                shareModal.classList.remove('is-open');
                shareModal.setAttribute('aria-hidden', 'true');
            });
        }

    });

    /* ============================
    MODAL PREMIUM
    ============================ */
    @if(session('premium_success'))
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('premiumModal');
        const closeBtn = document.getElementById('premiumCloseBtn');

        if (modal) {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            });
        }
    });
    @endif

    @if(session('espai_created'))
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('createdModal');
        const closeBtn = document.getElementById('createdCloseBtn');

        if (modal) {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            });
        }
    });
    @endif
    </script>
    @endpush

</x-app-layout>