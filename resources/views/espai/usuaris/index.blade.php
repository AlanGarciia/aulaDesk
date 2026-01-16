@push('styles')
    @vite('resources/css/espai/usuarisIndex.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Usuaris de l'espai</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="actions">
                <a href="{{ route('espai.usuaris.create') }}" class="btn btn-primary">
                    + Afegir usuari
                </a>

                <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                    Tornar a l'espai
                </a>
            </div>

            <div class="card">
                @forelse ($usuaris as $usuari)
                    <div class="user-row">
                        <div class="user-info">
                            <div class="user-name">{{ $usuari->nom }}</div>
                            <div class="user-meta">
                                Rol: {{ $usuari->rol }} · Creat: {{ $usuari->created_at->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="user-actions">
                            <a class="btn btn-secondary" href="{{ route('espai.usuaris.edit', $usuari) }}">
                                Editar
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
