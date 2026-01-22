@push('styles')
    @vite('resources/css/espaisIndex.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="page-title">Els meus espais</h2>

            <!-- Botón Surt / Logout -->
            <button type="button" class="btn btn-danger logout-btn" id="logoutBtn">
                <i class="bi bi-box-arrow-right"></i> Surt
            </button>
        </div>
    </x-slot>

    <div class="page">
        <div class="container">

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="actions">
                <a href="{{ route('espais.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Crear espai
                </a>
            </div>

            <div class="card">
                @forelse ($espais as $espai)
                    <div class="space-row">
                        <div class="space-info">
                            <div class="space-name">{{ $espai->nom }}</div>
                            @if ($espai->descripcio)
                                <div class="space-desc">{{ $espai->descripcio }}</div>
                            @endif
                            <div class="space-meta">
                                Creat: {{ $espai->created_at->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="space-actions">
                            <a href="{{ route('espais.entrar.form', $espai) }}" class="btn btn-primary">
                                Entrar
                            </a>

                            <a href="{{ route('espais.edit', $espai) }}" class="btn btn-secondary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>

                            <button type="button" class="btn btn-danger delete-btn" 
                                    data-espai-name="{{ $espai->nom }}" 
                                    data-form-id="deleteForm-{{ $espai->id }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>

                            <!-- Formulario DELETE oculto -->
                            <form id="deleteForm-{{ $espai->id }}" action="{{ route('espais.destroy', $espai) }}" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">No tens cap espai encara. Crea’n un per començar.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Confirm Delete -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p id="confirmText"></p>
            <div class="modal-actions">
                <button id="cancelBtn" class="btn btn-secondary">Cancel·lar</button>
                <button id="confirmBtn" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>

    <!-- Modal Confirm Logout -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <p>Segur que vols sortir de la web?</p>
            <div class="modal-actions">
                <button id="cancelLogoutBtn" class="btn btn-secondary">Cancel·lar</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Surt</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ===== MODAL DELETE =====
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
            if(formToSubmit) formToSubmit.submit();
        });

        cancelBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // ===== MODAL LOGOUT =====
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');

        logoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'flex';
        });

        cancelLogoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });

        // Cerrar modales clicando fuera
        window.addEventListener('click', (e) => {
            if(e.target === modal) modal.style.display = 'none';
            if(e.target === logoutModal) logoutModal.style.display = 'none';
        });
    </script>
    @endpush
</x-app-layout>
