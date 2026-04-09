<x-app-layout>
    <div class="page">

        <h2 class="page-title">Editar rol</h2>

        <form method="POST" action="{{ route('espai.roles.update', $role) }}">
            @csrf
            @method('PUT')

            <label>Nom del rol</label>
            <input type="text" name="nom" value="{{ $role->nom }}" required>

            <h3>Permisos</h3>

            @foreach($permissions as $permission)
                <label>
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->id }}"
                           {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $permission->nom)) }}
                </label><br>
            @endforeach

            <button class="btn btn-primary">Guardar</button>
        </form>

    </div>
</x-app-layout>