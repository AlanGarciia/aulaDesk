@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">Assignar rols a {{ $usuari->nom }}</h2>
        </div>

        <div class="actions" style="display:flex; gap:1rem;">

            <a href="{{ route('espai.usuaris.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Tornar
            </a>

            <a href="{{ route('espai.roles.index') }}" class="btn btn-primary">
                <i class="bi bi-gear"></i>
                Gestionar rols
            </a>

            <!-- ⭐ NUEVO: crear rol -->
            <a href="{{ route('espai.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Crear rol
            </a>

        </div>

        <div class="container">

            <form method="POST" action="{{ route('espai.usuaris.roles.store', $usuari) }}" class="card" style="padding:1.5rem;">
                @csrf

                <h3 style="color:white; margin-bottom:1rem;">Rols disponibles</h3>

                @forelse(($roles ?? []) as $role)
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:.5rem;">

                        <label style="color:white; display:flex; align-items:center; gap:.5rem;">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   {{ $usuari->roles->contains($role->id) ? 'checked' : '' }}>
                            {{ $role->nom }}
                        </label>

                        <!-- ⭐ NUEVO: editar rol -->
                        <a href="{{ route('espai.roles.edit', $role) }}"
                           class="btn btn-secondary"
                           style="padding:.25rem .75rem;">
                            Editar
                        </a>

                    </div>
                @empty
                    <p style="color:white;">No hi ha rols creats en aquest espai.</p>
                @endforelse

                <button class="btn btn-primary" style="margin-top:1rem;">Guardar</button>
            </form>

        </div>

    </div>
</x-app-layout>