@push('styles')
    @vite('resources/css/espai/roles/index.css')
@endpush

<x-app-layout>
    <div class="page">

        <!-- BOTÓN TORNAR ARRIBA IZQUIERDA -->
        <div class="actions" style="width:min(980px,100%);margin:1rem auto 0;justify-content:flex-start;">
            <a href="{{ route('espai.index') }}" class="btn btn-secondary">← Tornar</a>
        </div>

        <!-- TÍTULO -->
        <div class="page-title-container">
            <h2 class="page-title">Rols de l'espai</h2>
        </div>

        <!-- BOTÓN CREAR ROL -->
        <div class="actions" style="width:min(980px,100%);margin:auto;">
            <a href="{{ route('espai.roles.create') }}" class="btn btn-primary">+ Crear rol</a>
        </div>

        <!-- LISTA DE ROLES -->
        <div class="card role-list-card">
            @forelse($roles as $role)
                <div class="role-row">
                    <span class="role-name">{{ $role->nom }}</span>
                    <a href="{{ route('espai.roles.edit', $role) }}" class="btn btn-secondary">Editar</a>
                </div>
            @empty
                <div class="empty-state">
                    Encara no hi ha rols creats.
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
