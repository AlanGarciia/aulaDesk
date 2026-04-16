@push('styles')
    @vite('resources/css/espai/roles/edit.css')
@endpush

<x-app-layout>
    <div class="page">

        <!-- HEADER -->
        <div class="page-title-container">
            <h2 class="page-title">Editar rol</h2>
        </div>

        <!-- ACTIONS -->
        <div class="actions">
            <a href="{{ route('espai.roles.index') }}" class="btn btn-secondary">
                ← Tornar
            </a>
        </div>

        <!-- FORM -->
        <form method="POST" action="{{ route('espai.roles.update', $role) }}" class="form-card">
            @csrf
            @method('PUT')

            <!-- ROLE NAME -->
            <div class="form-group">
                <label>Nom del rol</label>
                <input type="text" name="nom" value="{{ $role->nom }}" required>
            </div>

            <!-- HEADER -->
            <div class="permissions-header">
                <h3>Permisos del rol</h3>
                <span>Selecciona els permisos que tindrà aquest rol</span>
            </div>

            <!-- ACCORDION -->
            <div class="permissions-accordion">

                @foreach($groupedPermissions as $category => $perms)
                    <div class="permission-category">

                        <button type="button" class="category-toggle">
                            <span>{{ ucfirst($category) }}</span>
                            <span class="arrow">▸</span>
                        </button>

                        <div class="category-content">
                            @foreach($perms as $permission)
                                <label class="permission-item">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->id }}"
                                           {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                    <span>{{ ucfirst(str_replace('.', ' ', $permission->nom)) }}</span>
                                </label>
                            @endforeach
                        </div>

                    </div>
                @endforeach

            </div>

            <button class="btn btn-primary">
                Guardar canvis
            </button>
        </form>

    </div>
   @push('scripts')
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.category-toggle');
    if (!btn) return;

    const content = btn.nextElementSibling;

    content.classList.toggle('open');
    btn.classList.toggle('open');
});
</script>
@endpush

</x-app-layout>


