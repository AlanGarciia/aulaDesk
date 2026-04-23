@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">

        <!-- TÍTULO -->
        <div class="page-title-container">
            <h2 class="page-title">Usuaris de l'espai</h2>
        </div>

        <!-- ACCIONES -->
        <div class="actions">
            <a href="{{ route('espai.usuaris.create') }}" class="btn btn-primary">
                + Afegir usuari
            </a>

            <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                <i class="bi bi-box-arrow-right"></i>Tornar a l'espai
            </a>
        </div>

        <div class="container">

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- FILTROS -->
            <form method="GET" action="{{ route('espai.usuaris.index') }}" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" value="{{ request('nom') }}" placeholder="Buscar per nom">
                    </div>

                    <div class="filter-group">
                        <label for="rol">Rol</label>
                        <select name="rol" id="rol">
                            <option value="">Tots</option>

                            @foreach(\App\Models\BaseRole::pluck('nom') as $rol)
                                <option value="{{ $rol }}" {{ request('rol') === $rol ? 'selected' : '' }}>
                                    {{ ucfirst($rol) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('espai.usuaris.index') }}" class="btn btn-secondary">Netejar</a>
                    </div>
                </div>
            </form>

            <!-- LISTA DE USUARIOS -->
            <div class="card">
                @forelse ($usuaris as $usuari)
                    <div class="user-row">

                        <div class="user-info">
                            <div class="user-name">{{ $usuari->nom }}</div>
                            <div class="user-meta">
                                Rol: {{ $usuari->rol }}
                            </div>
                        </div>

                        <div class="user-actions">

                            <a class="btn btn-secondary" href="{{ route('espai.usuaris.roles', $usuari) }}">
                                <i class="bi bi-shield-check"></i>
                            </a>

                            <a class="btn btn-secondary" href="{{ route('espai.usuaris.edit', $usuari) }}">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <!-- BOTÓN QUE ABRE EL POP-UP -->
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="openDeleteModal('{{ $usuari->id }}')">
                                Eliminar
                            </button>

                        </div>
                    </div>
                @empty
                    <p class="empty-state">No hi ha usuaris creats en aquest espai.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- =============================== -->
    <!-- MODAL ELIMINAR USUARI (FUERA DE .page) -->
    <!-- =============================== -->
    <div id="deleteModal" class="modal-overlay hidden">
        <div class="modal-card">
            <h3>Eliminar usuari</h3>
            <p>Estàs segur que vols eliminar aquest usuari? Aquesta acció no es pot desfer.</p>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                        Cancel·lar
                    </button>

                    <button type="submit" class="btn btn-danger">
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openDeleteModal(userId) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');

            form.action = `/espai/usuaris/${userId}`;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
    @endpush

</x-app-layout>
