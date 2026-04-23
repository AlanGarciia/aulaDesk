@push('styles')
    @vite('resources/css/espai/franges/frangesIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">
            <p>
                <a class="btn btn-secondary" href="{{ route('espai.aules.index') }}">Tornar a Aules</a>
                <a class="btn btn-primary @cantEspaiClass('aulas.manage')" href="{{ route('espai.franges.create') }}">Nova franja</a>
            </p>

            @if(session('ok'))
                <div class="alert success">{{ session('ok') }}</div>
            @endif

            <div class="card">
                <table border="1" cellpadding="8" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Ordre</th><th>Nom</th><th>Inici</th><th>Fi</th><th>Accions</th>
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
                                    <a class="btn @cantEspaiClass('aulas.manage')" href="{{ route('espai.franges.edit', $f) }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('espai.franges.destroy', $f) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn danger @cantEspaiClass('aulas.manage')" type="submit"
                                                onclick="return confirm('Eliminar aquesta franja?')">
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