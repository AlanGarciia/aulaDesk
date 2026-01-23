@push('styles')
    @vite('resources/css/espai/espaiIndex.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="page-title">Espai</h2>
            <nav class="navbar">
                <a href="{{ route('espai.usuaris.create') }}" class="btn btn-primary">
                    Afegir usuaris
                </a>
                <a href="{{ route('espai.usuaris.index') }}" class="btn btn-secondary">
                    Veure usuaris
                </a>
                <a href="{{ route('espai.noticies.index') }}" class="btn btn-secondary">
                    Tauló de notícies
                </a>
                <a href="{{ route('espai.aules.index') }}" class="btn btn-secondary">
                    Aules
                </a>
                <a href="{{ route('espais.index') }}" class="btn btn-danger">
                    Sortir de l'espai
                </a>
                <span class="navbar-spacer"></span>
                <span class="navbar-spacer"></span>
            </nav>
        </div>
    </x-slot>

    <div class="page">
        <div class="container">
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card">
                <h3 class="space-name">Panell de l'espai</h3>
                <p class="space-desc">
                    Aquí aniran totes les opcions del teu espai (usuaris, assignatures, classes, etc.).
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
