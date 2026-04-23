@push('styles')
    @vite('resources/css/espai/roles/create.css')
@endpush
<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">Rols de l'espai</h2>
        </div>

        <div class="actions" style="width:min(980px,100%);margin:auto;">
            <a href="{{ route('espai.roles.create') }}" class="btn btn-primary @cantEspaiClass('roles.create')">+ Crear rol</a>
        </div>

        <div class="card role-list-card">
            @forelse($roles as $role)
                <div class="role-row">
                    <span class="role-name">{{ $role->nom }}</span>
                    <a href="{{ route('espai.roles.edit', $role) }}" class="btn btn-secondary @cantEspaiClass('roles.update')">Editar</a>
                </div>
            @empty
                <div class="empty-state">
                    Encara no hi ha rols creats.
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>