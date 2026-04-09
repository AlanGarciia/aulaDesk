<x-app-layout>
    <div class="page">

        <h2 class="page-title">Crear rol</h2>

        <form method="POST" action="{{ route('espai.roles.store') }}">
            @csrf

            <label>Nom del rol</label>
            <input type="text" name="nom" required>

            <h3>Permisos</h3>

            @foreach($permissions as $permission)
                <label>
                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}">
                    {{ ucfirst(str_replace('_', ' ', $permission->nom)) }}
                </label><br>
            @endforeach

            <button class="btn btn-primary">Crear</button>
        </form>

    </div>
</x-app-layout>