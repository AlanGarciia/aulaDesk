<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Aulas</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            <a class="btn" href="{{ route('espai.aules.create') }}">Nueva aula</a>

            @if(session('ok'))
                <div class="alert success">{{ session('ok') }}</div>
            @endif

            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Capacidad</th>
                            <th>Planta</th>
                            <th>Activa</th>
                            <th></th>
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
                                <td style="display:flex; gap:8px;">
                                    <a class="btn" href="{{ route('espai.aules.edit', $aula) }}">Editar</a>
                                    <form method="POST" action="{{ route('espai.aules.destroy', $aula) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn danger" type="submit">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No hay aulas.</td></tr>
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
