@push('styles')
    @vite('resources/css/espaisIndex.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Els meus espais</h2>
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
                    + Crear espai
                </a>
            </div>

            <div class="card">
                @forelse ($espais as $espai)
                    <div class="space-row">
                        <div class="space-info">
                            <div class="space-name">{{ $espai->nom }}</div>

                            @if ($espai->descripcio)
                                <div class="space-desc">{{ $espai->descripcio }}</div>
                            @endif

                            <div class="space-meta">
                                Creat: {{ $espai->created_at->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="space-actions">
                            {{-- Botó per entrar a l'espai --}}
                            <form method="POST" action="{{ route('espais.entrar', $espai) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    Entrar
                                </button>
                            </form>

                            <a href="{{ route('espais.edit', $espai) }}" class="btn btn-secondary">
                                Editar
                            </a>

                            <form method="POST" action="{{ route('espais.destroy', $espai) }}"
                                  onsubmit="return confirm('Segur que vols eliminar aquest espai?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">No tens cap espai encara. Crea’n un per començar.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
