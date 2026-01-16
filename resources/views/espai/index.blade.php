@push('styles')
    @vite('resources/css/espai/espaiIndex.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Espai</h2>
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
                    Afegir usuaris
                </a>

                <a href="{{ route('espai.usuaris.index') }}" class="btn btn-secondary">
                    Veure tots els usuaris
                </a>

                <a href="{{ route('espais.index') }}" class="btn btn-danger">
                    Sortir de l'espai
                </a>
            </div>

            <div class="card">
                <h3 class="space-name">Panell de l'espai</h3>
                <p class="space-desc">
                    Aquí aniran totes les opcions del teu espai (usuaris, assignatures, classes, etc.).
                </p>
            </div>
        </div>
    </div>
</x-app-layout>