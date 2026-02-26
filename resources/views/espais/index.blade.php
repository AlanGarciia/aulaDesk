<x-app-layout>

    @push('styles')
        @vite('resources/css/espais/espaisIndex.css')
    @endpush

    <nav class="breeze-nav">
    <div class="breeze-container">
        <div class="breeze-logo">
            aulaDesk
        </div>

        <div class="breeze-user-wrapper">
            <button type="button" class="btn btn-secondary breeze-user-btn" id="userMenuBtn">
                <i class="bi bi-person-circle"></i>
                {{ auth()->user()->name }}
                <i class="bi bi-caret-down-fill caret-icon"></i>
            </button>

            <div id="userMenu" class="breeze-dropdown">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="bi bi-gear"></i>
                    <span>Perfil</span>
                </a>

                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item logout-item">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Surt</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

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

            <div class="card">
                @forelse ($espais as $espai)
                    {{-- CLICK a la fila => entrar --}}
                    <div class="space-row js-enter-row"
                         data-enter-url="{{ route('espais.entrar.form', $espai) }}"
                         style="cursor:pointer;">
                        <div class="space-info">
                            <div class="space-name">{{ $espai->nom }}</div>

                            @if ($espai->descripcio)
                                <div class="space-desc">{{ $espai->descripcio }}</div>
                            @endif

                            <div class="space-meta">
                                Creat: {{ $espai->created_at->format('d/m/Y') }}
                            </div>

                            @if ((int) $espai->user_id !== (int) auth()->id())
                                <div class="space-meta">Compartit amb tu</div>
                            @endif
                        </div>

                        {{-- Accions: NO han de disparar l'entrar --}}
                        <div class="space-actions" data-no-enter="1">
                            @if ((int) $espai->user_id === (int) auth()->id())
                                <button type="button" class="btn btn-secondary js-no-enter"
                                        onclick="window.location='{{ route('espais.edit', $espai) }}'">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>

                                <button type="button"
                                        class="btn btn-secondary share-btn js-no-enter"
                                        data-espai-name="{{ $espai->nom }}"
                                        data-action="{{ route('espais.compartir', $espai) }}">
                                    <i class="bi bi-share"></i> Compartir
                                </button>

                                <button type="button" class="btn btn-danger delete-btn js-no-enter"
                                        data-espai-name="{{ $espai->nom }}"
                                        data-form-id="deleteForm-{{ $espai->id }}">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>

                                <form id="deleteForm-{{ $espai->id }}" action="{{ route('espais.destroy', $espai) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
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

    <div id="confirmModal" class="modal">
        <div class="modal-content modal-delete">
            <p id="confirmText"></p>
            <div class="modal-actions">
                <button id="cancelBtn" class="btn btn-cancel">Cancel·lar</button>
                <button id="confirmBtn" class="btn btn-delete">Eliminar</button>
            </div>
        </div>
    </div>

    <div id="successModal" class="modal">
        <div class="modal-content modal-success">
            <p id="successText"></p>
            <div class="modal-actions">
                <button id="successCloseBtn" class="btn btn-primary">Tancar</button>
            </div>
        </div>
    </div>

    {{-- MODAL COMPARTIR --}}
    <div id="shareModal" class="modal">
        <div class="modal-content modal-success">
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.space-row').forEach((postit) => {
                postit.style.setProperty('--random', Math.random());
            });

            // CLICK FILA => ENTRAR (excepte si cliques un botó/acció)
            document.querySelectorAll('.js-enter-row').forEach((row) => {
                row.addEventListener('click', (e) => {
                    if (e.target.closest('.js-no-enter')) return;
                    const url = row.dataset.enterUrl;
                    if (url) window.location = url;
                });
            });

            // DELETE
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

            confirmBtn.addEventListener('click', () => {
                if (formToSubmit) formToSubmit.submit();
            });

            cancelBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // SUCCESS
            const successModal = document.getElementById('successModal');
            const successText = document.getElementById('successText');
            const successCloseBtn = document.getElementById('successCloseBtn');

            @if(session('status'))
                successText.textContent = "{{ session('status') }}";
                successModal.style.display = 'flex';
            @endif

            successCloseBtn.addEventListener('click', () => {
                successModal.style.display = 'none';
            });

            // COMPARTIR
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

            shareCancelBtn.addEventListener('click', () => {
                shareModal.style.display = 'none';
            });

            // MENU BREEZE
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenu = document.getElementById('userMenu');
            const logoutBtn = document.getElementById('logoutBtn');

            function toggleUserMenu(forceOpen = null) {
                if (!userMenu) return;
                if (forceOpen === true) return userMenu.style.display = 'block';
                if (forceOpen === false) return userMenu.style.display = 'none';
                userMenu.style.display = (userMenu.style.display === 'block') ? 'none' : 'block';
            }

            if (userMenuBtn) {
                userMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleUserMenu();
                });
            }

            if (logoutBtn) {
                logoutBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleUserMenu(true);
                });
            }

            // CLICK FORA
            window.addEventListener('click', (e) => {
                if (e.target === modal) modal.style.display = 'none';
                if (e.target === successModal) successModal.style.display = 'none';
                if (e.target === shareModal) shareModal.style.display = 'none';

                if (userMenu && !userMenu.contains(e.target) && e.target !== userMenuBtn) {
                    userMenu.style.display = 'none';
                }
            });
        });
    </script>
    @endpush

</x-app-layout>