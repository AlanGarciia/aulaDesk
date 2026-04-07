<x-app-layout>

    @push('styles')
        @vite('resources/css/espais/espaisIndex.css')
    @endpush

    {{-- ============================
         NUEVO HEADER MODERNO
       ============================ --}}
    <nav class="nav-modern">
        <div class="nav-inner">

            {{-- LOGO --}}
            <div class="nav-left">
                <div class="nav-logo">aulaDesk</div>
            </div>

            {{-- USER MENU --}}
            <div class="nav-right">
                <button class="nav-user-btn" id="userMenuBtn">
                    <i class="bi bi-person-circle"></i>
                    <span>{{ auth()->user()->name }}</span>
                    <i class="bi bi-chevron-down nav-caret"></i>
                </button>

                <div id="userMenu" class="nav-dropdown">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-gear"></i>
                        Perfil
                    </a>

                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item logout-item">
                            <i class="bi bi-box-arrow-right"></i>
                            Surt
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </nav>

    {{-- ============================
         CONTENIDO PRINCIPAL
       ============================ --}}
    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">Els meus espais</h2>
            </div>

            <div class="actions">
                <button class="btn btn-primary" onclick="window.location='{{ route('espais.create') }}'">
                    <i class="bi bi-plus"></i> Crear espai
                </button>
            </div>

            <div class="tilt-grid">
                @forelse ($espais as $espai)
                    <div
                        class="tilt-card js-enter-card"
                        data-enter-url="{{ route('espais.entrar.form', $espai) }}"
                        data-tilt
                        data-tilt-amplitude="12"
                        data-tilt-scale="1.05"
                        role="link"
                        tabindex="0"
                        aria-label="Entrar a {{ $espai->nom }}"
                    >
                        <div class="tilt-card__inner">
                            <div class="tilt-card__bg"></div>

                            <div class="tilt-card__content">
                                <div class="tilt-card__title">{{ $espai->nom }}</div>

                                @if ($espai->descripcio)
                                    <div class="tilt-card__desc">{{ $espai->descripcio }}</div>
                                @endif

                                <div class="tilt-card__meta">
                                    Creat: {{ $espai->created_at->format('d/m/Y') }}
                                </div>

                                @if ((int) $espai->user_id !== (int) auth()->id())
                                    <div class="tilt-card__badge">Compartit amb tu</div>
                                @endif
                            </div>

                            @if ((int) $espai->user_id === (int) auth()->id())
                                <div class="tilt-card__actions" data-no-enter="1">
                                    <button
                                        type="button"
                                        class="tilt-action tilt-action--neutral js-no-enter"
                                        onclick="window.location='{{ route('espais.edit', $espai) }}'"
                                        aria-label="Editar {{ $espai->nom }}"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="tilt-action tilt-action--neutral share-btn js-no-enter"
                                        data-espai-name="{{ $espai->nom }}"
                                        data-action="{{ route('espais.compartir', $espai) }}"
                                        aria-label="Compartir {{ $espai->nom }}"
                                    >
                                        <i class="bi bi-share"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="tilt-action tilt-action--danger delete-btn js-no-enter"
                                        data-espai-name="{{ $espai->nom }}"
                                        data-form-id="deleteForm-{{ $espai->id }}"
                                        aria-label="Eliminar {{ $espai->nom }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="deleteForm-{{ $espai->id }}" action="{{ route('espais.destroy', $espai) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="tilt-card__tooltip" role="tooltip">
                            {{ $espai->nom }}
                        </div>
                    </div>
                @empty
                    <div class="empty-state-container">
                        <p class="empty-state">No tens cap espai encara. Crea’n un per començar.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- ============================
         MODALES
       ============================ --}}
    <div id="confirmModal" class="modal" aria-hidden="true">
        <div class="modal-content modal-delete" role="dialog" aria-modal="true" aria-labelledby="confirmText">
            <p id="confirmText"></p>
            <div class="modal-actions">
                <button id="cancelBtn" class="btn btn-cancel" type="button">Cancel·lar</button>
                <button id="confirmBtn" class="btn btn-delete" type="button">Eliminar</button>
            </div>
        </div>
    </div>

    <div id="successModal" class="modal" aria-hidden="true">
        <div class="modal-content modal-success" role="dialog" aria-modal="true" aria-labelledby="successText">
            <p id="successText"></p>
            <div class="modal-actions">
                <button id="successCloseBtn" class="btn btn-primary" type="button">Tancar</button>
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
                    placeholder="Email de l’usuari"
                    style="width:100%; padding:10px; border-radius:10px; border:1px solid #ddd; margin-bottom:12px;"
                >

                <div class="modal-actions">
                    <button type="button" id="shareCancelBtn" class="btn btn-cancel">Cancel·lar</button>
                    <button type="submit" class="btn btn-primary">Compartir</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================
         SCRIPTS
       ============================ --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* --------------------
               Tilt effect
            -------------------- */
            const cards = document.querySelectorAll('[data-tilt]');
            cards.forEach((card) => {
                const inner = card.querySelector('.tilt-card__inner');
                const tooltip = card.querySelector('.tilt-card__tooltip');

                const amplitude = Number(card.dataset.tiltAmplitude || 12);
                const scaleOnHover = Number(card.dataset.tiltScale || 1.05);

                function onMove(e) {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const ox = x - rect.width / 2;
                    const oy = y - rect.height / 2;

                    const rx = (oy / (rect.height / 2)) * -amplitude;
                    const ry = (ox / (rect.width / 2)) * amplitude;

                    inner.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg) scale(${scaleOnHover})`;

                    if (tooltip) {
                        tooltip.style.transform = `translate3d(${x + 10}px, ${y + 10}px, 0)`;
                    }
                }

                function onEnter() {
                    if (tooltip) tooltip.style.opacity = '1';
                }

                function onLeave() {
                    inner.style.transform = `rotateX(0deg) rotateY(0deg) scale(1)`;
                    if (tooltip) tooltip.style.opacity = '0';
                }

                card.addEventListener('mousemove', onMove);
                card.addEventListener('mouseenter', onEnter);
                card.addEventListener('mouseleave', onLeave);

                card.addEventListener('focus', () => {
                    inner.style.transform = `rotateX(0deg) rotateY(0deg) scale(${scaleOnHover})`;
                    if (tooltip) {
                        tooltip.style.opacity = '1';
                        tooltip.style.transform = `translate3d(12px, 12px, 0)`;
                    }
                });
                card.addEventListener('blur', onLeave);
            });

            /* --------------------
               Click tarjeta => entrar
            -------------------- */
            document.querySelectorAll('.js-enter-card').forEach((card) => {
                const url = card.dataset.enterUrl;

                card.addEventListener('click', (e) => {
                    if (e.target.closest('.js-no-enter') || e.target.closest('[data-no-enter]')) return;
                    if (url) window.location = url;
                });

                card.addEventListener('keydown', (e) => {
                    if (e.target.closest('.js-no-enter') || e.target.closest('[data-no-enter]')) return;
                    if (e.key !== 'Enter' && e.key !== ' ') return;
                    e.preventDefault();
                    if (url) window.location = url;
                });
            });

            /* --------------------
               SHARE modal
            -------------------- */
            const shareBtns = document.querySelectorAll('.share-btn');
            const shareModal = document.getElementById('shareModal');
            const shareTitle = document.getElementById('shareTitle');
            const shareForm = document.getElementById('shareForm');
            const shareEmail = document.getElementById('shareEmail');
            const shareCancelBtn = document.getElementById('shareCancelBtn');

            shareBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    shareTitle.textContent = `Compartir l'espai "${btn.dataset.espaiName}"`;
                    shareForm.action = btn.dataset.action;
                    shareEmail.value = '';
                    shareModal.style.display = 'flex';
                    shareEmail.focus();
                });
            });

            shareCancelBtn?.addEventListener('click', () => {
                shareModal.style.display = 'none';
            });

            /* --------------------
               DELETE confirm modal
            -------------------- */
            const deleteBtns = document.querySelectorAll('.delete-btn');
            const modal = document.getElementById('confirmModal');
            const confirmText = document.getElementById('confirmText');
            const confirmBtn = document.getElementById('confirmBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            let formToSubmit = null;

            deleteBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    formToSubmit = document.getElementById(btn.dataset.formId);
                    confirmText.textContent = `Segur que vols eliminar l'espai "${btn.dataset.espaiName}"?`;
                    modal.style.display = 'flex';
                });
            });

            confirmBtn?.addEventListener('click', () => {
                if (formToSubmit) formToSubmit.submit();
            });

            cancelBtn?.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            /* --------------------
               SUCCESS modal
            -------------------- */
            const successModal = document.getElementById('successModal');
            const successText = document.getElementById('successText');
            const successCloseBtn = document.getElementById('successCloseBtn');

            @if(session('status'))
                successText.textContent = "{{ session('status') }}";
                successModal.style.display = 'flex';
            @endif

            successCloseBtn?.addEventListener('click', () => {
                successModal.style.display = 'none';
            });

            /* --------------------
               Close modals clicking outside
            -------------------- */
            window.addEventListener('click', (e) => {
                if (e.target === modal) modal.style.display = 'none';
                if (e.target === successModal) successModal.style.display = 'none';
                if (e.target === shareModal) shareModal.style.display = 'none';
            });

            /* --------------------
               MENU MODERNO
            -------------------- */
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenu = document.getElementById('userMenu');

            if (userMenuBtn) {
                userMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userMenu.style.display = userMenu.style.display === 'flex' ? 'none' : 'flex';
                });
            }

            window.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target) && e.target !== userMenuBtn) {
                    userMenu.style.display = 'none';
                }
            });

        });
    </script>
    @endpush

</x-app-layout>
