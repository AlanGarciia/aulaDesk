@push('styles')
    @vite('resources/css/espai/aules/aulaIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">Aules</h2>

                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.index') }}"><i class="bi bi-box-arrow-right"></i>Tornar a l'espai</a>
                    <a class="btn btn-primary" href="{{ route('espai.aules.create') }}">Nova aula</a>
                    <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">Veure franges</a>
                </div>
            </div>

            @if(session('ok'))
                <div id="successModal" class="modal-overlay">
                    <div class="modal-box">
                        {{ session('ok') }}
                        <button type="button" class="btn btn-secondary modal-close" onclick="document.getElementById('successModal').style.display='none'">
                            Tancar
                        </button>
                    </div>
                </div>
            @endif

            <form method="GET" action="{{ route('espai.aules.index') }}" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" value="{{ request('nom') }}" placeholder="Buscar per nom">
                    </div>
                    <div class="filter-group">
                        <label for="codi">Codi</label>
                        <input type="text" name="codi" id="codi" value="{{ request('codi') }}" placeholder="Buscar per codi">
                    </div>
                    <div class="filter-group">
                        <label for="planta">Planta</label>
                        <input type="text" name="planta" id="planta" value="{{ request('planta') }}" placeholder="Buscar per planta">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('espai.aules.index') }}" class="btn btn-secondary">Netejar</a>
                    </div>
                </div>
            </form>

            <div class="aules-grid">
                @forelse($aules as $aula)
                    <div class="aula-card">
                        <div class="aula-name">{{ $aula->nom }}</div>
                        <div class="aula-meta">Codi: {{ $aula->codi }}</div>
                        <div class="aula-meta">Capacitat: {{ $aula->capacitat }}</div>
                        <div class="aula-meta">Planta: {{ $aula->planta }}</div>

                        <div class="aula-actions">
                            <a class="btn btn-secondary @cantEspaiClass('aulas.manage')" href="{{ route('espai.aules.admin', $aula) }}">
                                Administrar
                            </a>
                            <a class="btn btn-secondary btn-icon @cantEspaiClass('aulas.update')" href="{{ route('espai.aules.edit', $aula) }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form class="inline-form" method="POST" action="{{ route('espai.aules.destroy', $aula) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-icon @cantEspaiClass('aulas.delete')" type="submit">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div>No hi ha aules disponibles.</div>
                        <a class="btn btn-primary @cantEspaiClass('aulas.create')" href="{{ route('espai.aules.create') }}">Crear primera aula</a>
                    </div>
                @endforelse
            </div>

            <div class="pagination">
                {{ $aules->links() }}
            </div>

        </div>
    </div>
    
</x-app-layout>