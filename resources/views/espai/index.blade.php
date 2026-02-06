@push('styles')
    @vite('resources/css/espai/espaiIndex.css')
@endpush
<x-app-layout>
    <div class="page">
        <div class="container">

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Botón de salir arriba derecha -->
            <a href="{{ route('espais.index') }}" class="btn-exit">
                <i class="bi bi-box-arrow-right icon"></i>
                Sortir
            </a>

            <!-- Título del panel -->
            <h2 class="page-title">Espai</h2>
            <p class="page-subtitle">Selecciona una opció per gestionar el teu espai</p>

            <!-- GRID DE BOTONES -->
            <nav class="navbar">
                <a href="{{ route('espai.usuaris.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus icon"></i>
                    Afegir usuaris
                </a>
                <a href="{{ route('espai.usuaris.index') }}" class="btn btn-secondary">
                    <i class="bi bi-people icon"></i>
                    Veure usuaris
                </a>
                <a href="{{ route('espai.noticies.index') }}" class="btn btn-secondary">
                    <i class="bi bi-journal-text icon"></i>
                    Tauló de notícies
                </a>
                <a href="{{ route('espai.aules.index') }}" class="btn btn-secondary">
                    <i class="bi bi-building icon"></i>
                    Aules
                </a>
                <a href="{{ route('espai.guardies.index') }}" class="btn btn-secondary">
                    <i class="bi bi-calendar-check icon"></i>
                    Guardies
                </a>
            </nav>
        </div>
    </div>
</x-app-layout>
