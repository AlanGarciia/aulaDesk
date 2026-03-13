@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">Grups de l'espai</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.grups.create') }}" class="btn btn-primary">
                + Crear grup
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

                @forelse ($grups as $grup)
                    <div class="user-row">

                        <div class="user-info">
                            <div class="user-name">{{ $grup->nom }}</div>

                            <div class="user-meta">
                                {{ $grup->alumnes()->count() }} alumnes
                            </div>
                        </div>

                        <div class="user-actions">

                            <a class="btn btn-secondary"
                               href="{{ route('espai.grups.edit', $grup) }}">
                                Gestionar
                            </a>

                            <form class="inline-form"
                                  method="POST"
                                  action="{{ route('espai.grups.destroy', $grup) }}"
                                  onsubmit="return confirm('Segur que vols eliminar aquest grup?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    Eliminar
                                </button>
                            </form>

                        </div>

                    </div>
                @empty
                    <p class="empty-state">No hi ha grups creats en aquest espai.</p>
                @endforelse

            </div>

        </div>
    </div>
</x-app-layout>
