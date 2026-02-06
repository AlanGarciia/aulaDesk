<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Aules</h2>
    </x-slot>

    @push('styles')
        @vite('resources/css/espai/aules/aula.css')
    @endpush


    <div class="page">
        <div class="container">
            <p>
                <a class="btn btn-secondary" href="{{ route('espai.index') }}">Tornar a l'espai</a>
                <a class="btn" href="{{ route('espai.aules.create') }}">Nova aula</a>
                <a class="btn btn-secondary" href="{{ route('espai.franges.index') }}">
                    Veure franges
                </a>
            </p>

            @if(session('ok'))
                <div class="alert success">{{ session('ok') }}</div>
            @endif

            <div class="card">
                <table border="1" cellpadding="8" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th align="left">Nom</th>
                            <th align="left">Codi</th>
                            <th align="left">Capacitat</th>
                            <th align="left">Planta</th>
                            <th align="left">Activa</th>
                            <th align="left">Accions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($aules as $aula)
                            <tr>
                                <td>{{ $aula->nom }}</td>
                                <td>{{ $aula->codi }}</td>
                                <td>{{ $aula->capacitat }}</td>
                                <td>{{ $aula->planta }}</td>
                                <td>{{ $aula->activa ? 'Sí' : 'No' }}</td>
                                <td>
                                    <a class="btn" href="{{ route('espai.aules.admin', $aula) }}">Administrar aula</a>
                                    <a class="btn" href="{{ route('espai.aules.edit', $aula) }}">Editar</a>

                                    <form method="POST" action="{{ route('espai.aules.destroy', $aula) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn danger" type="submit" onclick="return confirm('Eliminar aquesta aula?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No hi ha aules.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div style="margin-top: 12px;">
                    {{ $aules->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
