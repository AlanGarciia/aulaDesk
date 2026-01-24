<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Franges horàries</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            <p>
                <a class="btn btn-secondary" href="{{ route('espai.aules.index') }}">Tornar a Aules</a>
                <a class="btn" href="{{ route('espai.franges.create') }}">Nova franja</a>
            </p>

            @if(session('ok'))
                <div class="alert success">{{ session('ok') }}</div>
            @endif

            <div class="card">
                <table border="1" cellpadding="8" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Ordre</th>
                            <th>Nom</th>
                            <th>Inici</th>
                            <th>Fi</th>
                            <th>Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($franges as $f)
                            <tr>
                                <td>{{ $f->ordre }}</td>
                                <td>{{ $f->nom }}</td>
                                <td>{{ substr($f->inici,0,5) }}</td>
                                <td>{{ substr($f->fi,0,5) }}</td>
                                <td>
                                    <a class="btn" href="{{ route('espai.franges.edit', $f) }}">Editar</a>

                                    <form method="POST" action="{{ route('espai.franges.destroy', $f) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn danger" type="submit" onclick="return confirm('Eliminar aquesta franja?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No hi ha franges creades.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
