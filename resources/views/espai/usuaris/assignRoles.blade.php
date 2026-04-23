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
                 <i class="bi bi-box-arrow-right me-1"></i>
                Tornar
            </a>


            <!-- ⭐ NUEVO: crear rol -->
            <a href="{{ route('espai.roles.create', $usuari->id) }}" class="btn btn-primary">
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
                       <a href="{{ route('espai.roles.edit', ['role' => $role->id, 'from_user' => $usuari->id]) }}"class="btn btn-secondary">
                                <i class="bi bi-gear me-1"></i>
                            Gestionar rol
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