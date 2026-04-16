<x-app-layout>

    @push('styles')
        @vite('resources/css/espais/espaisIndex.css')
    @endpush

    <div class="page">
        <div class="container">

            {{-- ============================
                 HEADER INTEGRADO
               ============================ --}}
            <div class="page-header">
                <h2 class="page-title">Els meus espais</h2>

                <div class="user-inline-menu">
                    <button class="nav-user-btn" id="userMenuBtn">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down nav-caret"></i>
                    </button>

                    <div id="userMenu" class="nav-dropdown">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="bi bi-gear"></i> Perfil
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item logout-item">
                                <i class="bi bi-box-arrow-right"></i> Surt
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ============================
                 BOTÓN CREAR ESPAI
               ============================ --}}
            {{-- ============================
     BOTÓN CREAR ESPAI + PLANS
   ============================ --}}
<div class="actions">

    <button class="btn btn-primary" onclick="window.location='{{ route('espais.create') }}'">
        <i class="bi bi-plus"></i> Crear espai
    </button>

    <button class="btn btn-secondary" onclick="window.location='{{ route('espais.plans.index') }}'">
        <i class="bi bi-stars"></i> Plans
    </button>

</div>


            

            {{-- ============================
                 GRID DE ESPAIS
               ============================ --}}
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
                                        data-no-enter="1"
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ============================
       ENTRAR AL ESPAI AL CLICAR LA CARD
    ============================ */
    document.querySelectorAll('.js-enter-card').forEach(card => {
        card.addEventListener('click', (e) => {

            // Si clicas un botón dentro de la card, NO entrar
            if (e.target.closest('.js-no-enter')) return;

            const url = card.dataset.enterUrl;
            if (url) window.location.href = url;
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

        window.addEventListener('click', () => {
            userMenu.classList.remove('is-open');
        });
    }

    /* ============================
       MODAL ELIMINAR ESPAI
    ============================ */

    const confirmModal = document.getElementById('confirmModal');
    const confirmText = document.getElementById('confirmText');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    // BOTONES DE ELIMINAR
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation(); // evita entrar a la card

            const espaiName = btn.dataset.espaiName;
            const formId = btn.dataset.formId;

            // Texto del modal
            if (confirmText) {
                confirmText.textContent = `Segur que vols eliminar "${espaiName}"?`;
            }

            // Guardamos el form que se debe enviar
            if (confirmBtn) {
                confirmBtn.dataset.formId = formId;
            }

            // Mostrar modal
            if (confirmModal) {
                confirmModal.setAttribute('aria-hidden', 'false');
                confirmModal.classList.add('is-open');
            }
        });
    });

    // CANCELAR
    if (cancelBtn && confirmModal) {
        cancelBtn.addEventListener('click', () => {
            confirmModal.classList.remove('is-open');
            confirmModal.setAttribute('aria-hidden', 'true');
        });
    }

    // CONFIRMAR ELIMINACIÓN
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
   MODAL COMPARTIR ESPAI
============================ */

const shareModal = document.getElementById('shareModal');
const shareTitle = document.getElementById('shareTitle');
const shareForm = document.getElementById('shareForm');
const shareCancelBtn = document.getElementById('shareCancelBtn');

// BOTONES COMPARTIR
document.querySelectorAll('.share-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();

        const espaiName = btn.dataset.espaiName;
        const action = btn.dataset.action;

        // Título
        if (shareTitle) {
            shareTitle.textContent = `Compartir "${espaiName}"`;
        }

        // Action del form
        if (shareForm) {
            shareForm.action = action;
        }

        // Mostrar modal
        if (shareModal) {
            shareModal.classList.add('is-open');
            shareModal.setAttribute('aria-hidden', 'false');
        }
    });
});

// CANCELAR
    if (shareCancelBtn && shareModal) {
        shareCancelBtn.addEventListener('click', () => {
            shareModal.classList.remove('is-open');
            shareModal.setAttribute('aria-hidden', 'true');
        });
    }

});
</script>
@endpush


</x-app-layout>
