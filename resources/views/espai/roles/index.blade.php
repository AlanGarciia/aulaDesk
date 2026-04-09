<x-app-layout>
    <div class="page">

        <h2 class="page-title">Rols de l'espai</h2>

        <a href="{{ route('espai.roles.create') }}" class="btn btn-primary">+ Crear rol</a>

        <div class="card">
            @foreach($roles as $role)
                <div class="info-row">
                    <span>{{ $role->nom }}</span>
                    <a href="{{ route('espai.roles.edit', $role) }}" class="btn btn-secondary">Editar</a>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>
