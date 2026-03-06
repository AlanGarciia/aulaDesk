@push('styles')
    @vite('resources/css/espai/aules/aulaIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">
<<<<<<< HEAD
            <!-- Botones superiores -->
            <p>
                <a class="btn btn-secondary" href="{{ route('espai.index') }}">Tornar a l'espai</a>
                <a class="btn" href="{{ route('espai.aules.create') }}">Nova aula</a>
                <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">Veure franges</a>
            </p>

            <!-- Mensaje de éxito -->
                        @if(session('ok'))
            <div id="successModal" class="modal-overlay">
                <div class="modal-box">
                    {{ session('ok') }}
                    </div>
                    </div>
                        @endif

            <!-- GRID DE TARJETAS -->
            <div class="card {{ $aules->isEmpty() ? 'card-empty' : '' }}">
=======

            <div class="page-header">
                <h2 class="page-title">Aules</h2>

                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.index') }}">Tornar a l'espai</a>
                    <a class="btn btn-primary" href="{{ route('espai.aules.create') }}">Nova aula</a>
                    <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">Veure franges</a>
                </div>
            </div>

            @if(session('ok'))
                <div class="alert-success">{{ session('ok') }}</div>
            @endif

            <div class="aules-grid">
>>>>>>> fb1925c (Nuevooo)
                @forelse($aules as $aula)
                    <div class="aula-card">
                        <div class="aula-name">{{ $aula->nom }}</div>
                        <div class="aula-meta">Codi: {{ $aula->codi }}</div>
                        <div class="aula-meta">Capacitat: {{ $aula->capacitat }}</div>
                        <div class="aula-meta">Planta: {{ $aula->planta }}</div>
                        <div class="aula-activa">Activa: {{ $aula->activa ? 'Sí' : 'No' }}</div>

                        <div class="aula-actions">
                            <a class="btn btn-secondary" href="{{ route('espai.aules.admin', $aula) }}">Administrar</a>
                            <a class="btn btn-secondary" href="{{ route('espai.aules.edit', $aula) }}">Editar</a>

                            <form class="inline-form" method="POST" action="{{ route('espai.aules.destroy', $aula) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit" onclick="return confirm('Eliminar aquesta aula?')">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
<<<<<<< HEAD
                    <div class="empty-state">
                        No hi ha aules disponibles.
                        <a href="{{ route('espai.aules.create') }}">Crear primera aula</a>
                    </div>
=======
                    <div class="empty-state">No hi ha aules.</div>
>>>>>>> fb1925c (Nuevooo)
                @endforelse
            </div>

            <div class="pagination">
                {{ $aules->links() }}
            </div>

        </div>
    </div>
</x-app-layout>