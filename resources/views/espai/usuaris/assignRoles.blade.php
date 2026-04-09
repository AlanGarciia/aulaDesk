@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">Assignar rols a {{ $usuari->nom }}</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.usuaris.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Tornar
            </a>
        </div>

        <div class="container">

            <form method="POST" action="{{ route('espai.usuaris.roles.store', $usuari) }}" class="card" style="padding:1.5rem;">
                @csrf

                <h3 style="color:white; margin-bottom:1rem;">Rols disponibles</h3>

                @forelse(($roles ?? []) as $role)
                    <label style="color:white; display:flex; align-items:center; gap:.5rem; margin-bottom:.5rem;">
                        <input type="checkbox"
                               name="roles[]"
                               value="{{ $role->id }}"
                               {{ $usuari->roles->contains($role->id) ? 'checked' : '' }}>
                        {{ $role->nom }}
                    </label>
                @empty
                    <p style="color:white;">No hi ha rols creats en aquest espai.</p>
                @endforelse

                <button class="btn btn-primary" style="margin-top:1rem;">Guardar</button>
            </form>

        </div>

    </div>
</x-app-layout>
