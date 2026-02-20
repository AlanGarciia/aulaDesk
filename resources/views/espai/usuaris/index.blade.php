@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <!-- Título con fondo -->
        <div class="page-title-container">
            <h2 class="page-title">Usuaris de l'espai</h2>
        </div>

        <!-- Botones fuera del container -->
        <div class="actions">
            <a href="{{ route('espai.usuaris.create') }}" class="btn btn-primary">
                + Afegir usuari
            </a>

            <a href="{{ route('espai.index') }}" class="btn btn-secondary">
            <i class="bi bi-box-arrow-right"></i>
                Sortir
                
            </a>
        </div>

        <div class="container">
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

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
