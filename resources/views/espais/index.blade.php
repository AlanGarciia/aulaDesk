<x-app-layout>

    @push('styles')
        @vite('resources/css/espais/espaisIndex.css')
    @endpush

    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">Els meus espais</h2>
                <button type="button" class="btn btn-danger logout-btn" id="logoutBtn">
                    <i class="bi bi-box-arrow-right"></i> Surt
                </button>
            </div>

            <div class="actions">
                <button class="btn btn-primary" onclick="window.location='{{ route('espais.create') }}'">
                    <i class="bi bi-plus"></i> Crear espai
                </button>
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
                            <button type="button" class="btn btn-primary" 
                                    onclick="window.location='{{ route('espais.entrar.form', $espai) }}'">
                                <i class="bi bi-box-arrow-in-right"></i> Entrar
                            </button>

                            <button type="button" class="btn btn-secondary" 
                                    onclick="window.location='{{ route('espais.edit', $espai) }}'">
                                <i class="bi bi-pencil"></i> Editar
                            </button>

                            <button type="button" class="btn btn-danger delete-btn" 
                                    data-espai-name="{{ $espai->nom }}" 
                                    data-form-id="deleteForm-{{ $espai->id }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>

                            <form id="deleteForm-{{ $espai->id }}" action="{{ route('espais.destroy', $espai) }}" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @empty
                   @forelse ($espais as $espai)
                        @empty
                            <div class="empty-state-container">
                                <p class="empty-state">No tens cap espai encara. Crea’n un per començar.</p>
                            </div>
                        @endforelse
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

    <div id="logoutModal" class="modal">
        <div class="modal-content modal-logout">
            <p>Segur que vols sortir de la web?</p>
            <div class="modal-actions">
                <button id="cancelLogoutBtn" class="btn btn-cancel">Cancel·lar</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-delete">Surt</button>
                </form>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.space-row').forEach((postit) => {
                postit.style.setProperty('--random', Math.random());
            });

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

            const logoutBtn = document.getElementById('logoutBtn');
            const logoutModal = document.getElementById('logoutModal');
            const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');

            logoutBtn.addEventListener('click', () => {
                logoutModal.style.display = 'flex';
            });

            cancelLogoutBtn.addEventListener('click', () => {
                logoutModal.style.display = 'none';
            });

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

            window.addEventListener('click', (e) => {
                if(e.target === modal) modal.style.display = 'none';
                if(e.target === logoutModal) logoutModal.style.display = 'none';
                if(e.target === successModal) successModal.style.display = 'none';
            });
        });
    </script>
    @endpush

</x-app-layout>
