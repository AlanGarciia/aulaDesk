@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="page-title-container">
            <h2 class="page-title">Usuaris de l'espai</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.usuaris.create') }}" class="btn btn-primary">
                + Afegir usuari
            </a>

            <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                Tornar a l'espai
            </a>
        </div>

        <div class="container">
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

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
                            @foreach(\App\Models\UsuariEspai::ROLS as $rol)
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
                            <a class="btn btn-secondary" href="{{ route('espai.usuaris.edit', $usuari) }}">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form class="inline-form"
                                  method="POST"
                                  action="{{ route('espai.usuaris.destroy', $usuari) }}"
                                  onsubmit="return confirm('Segur que vols eliminar aquest usuari?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">No hi ha usuaris creats en aquest espai.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>