<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Aules</h2>
    </x-slot>

    @push('styles')
        @vite('resources/css/espai/aules/aulaIndex.css')
    @endpush

    <div class="page">
        <div class="container">
            <p>
                <a class="btn btn-secondary" href="{{ route('espai.index') }}">Tornar a l'espai</a>
                <a class="btn" href="{{ route('espai.aules.create') }}">Nova aula</a>
                <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">Veure franges</a>
            </p>

            @if(session('ok'))
                <div class="alert success">{{ session('ok') }}</div>
            @endif

            <!-- GRID DE TARJETAS -->
            <div class="card">
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

                            <form method="POST" action="{{ route('espai.aules.destroy', $aula) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit" onclick="return confirm('Eliminar aquesta aula?')">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">No hi ha aules.</p>
                @endforelse
            </div>

            <!-- Paginación -->
            <div style="margin-top: 12px;">
                {{ $aules->links() }}
            </div>
        </div>
    </div>
</x-app-layout>