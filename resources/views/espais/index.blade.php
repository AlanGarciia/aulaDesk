@push('styles')
    @vite('resources/css/espaisIndex.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">📚 Mis Institutos</h2>
    </x-slot>

    <div class="page">
        <div class="container">
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="actions">
                <a href="{{ route('espais.create') }}" class="btn btn-primary">
                    + Crear Instituto
                </a>
            </div>

            <div class="card">
                @if ($espais->isNotEmpty())
                    <div class="spaces-grid">
                        @foreach ($espais as $espai)
                            <div class="space-card">
                                <div class="space-info">
                                    <div class="space-name">{{ $espai->nom }}</div>

                                    @if ($espai->descripcio)
                                        <div class="space-desc">{{ $espai->descripcio }}</div>
                                    @endif

                                    <div class="space-meta">
                                        Creado: {{ $espai->created_at->format('d/m/Y') }}
                                    </div>
                                </div>

                                <div class="space-actions">
                                    <form method="POST" action="{{ route('espais.entrar', $espai) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Entrar</button>
                                    </form>

                                    <a href="{{ route('espais.edit', $espai) }}" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>

                                    <form method="POST" action="{{ route('espais.destroy', $espai) }}"
                                          onsubmit="return confirm('¿Seguro que quieres eliminar este instituto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                           <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="empty-state">No tienes ningún instituto aún. ¡Crea uno para empezar!</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
