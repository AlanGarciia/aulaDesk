@push('styles')
    @vite('resources/css/espai/roles/edit.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">Editar rol</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.roles.index') }}" class="btn btn-secondary">← Tornar</a>
        </div>

        <form method="POST" action="{{ route('espai.roles.update', $role) }}" class="form-card">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nom del rol</label>
                <input type="text" name="nom" value="{{ $role->nom }}" required>
            </div>

            <div class="permissions-header">
                <h3>Permisos del rol</h3>
                <span>Selecciona els permisos que tindrà aquest rol</span>
            </div>

            {{-- Botó seleccionar-ho tot --}}
            <div style="margin-bottom:1rem;">
                <button type="button" id="btnTotsElsPermisos" class="btn btn-secondary">
                    ✅ Tots els permisos
                </button>
                <button type="button" id="btnCapPermis" class="btn btn-secondary" style="margin-left:.5rem;">
                    ☐ Cap permís
                </button>
            </div>

            <div class="permissions-accordion">
                @foreach($groupedPermissions as $category => $perms)
                    @php
                        $moduleNames = [
                            'users'       => 'Usuaris',
                            'groups'      => 'Grups',
                            'students'    => 'Alumnes',
                            'aulas'       => 'Aules',
                            'noticies'    => 'Notícies',
                            'guardies'    => 'Guardies',
                            'tickets'     => 'Tiquets',
                            'roles'       => 'Rols',
                            'permissions' => 'Permisos',
                        ];
                        $categoryLabel = $moduleNames[$category] ?? ucfirst($category);
                    @endphp

                    <div class="permission-category">
                        <button type="button" class="category-toggle">
                            <span>{{ $categoryLabel }}</span>
                            <span class="arrow">▸</span>
                        </button>

                        <div class="category-content">
                            @foreach($perms as $permission)
                                <label class="permission-item">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->id }}"
                                           {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                    <span>{{ $permission->nom_format }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="btn btn-primary" style="margin-top:1.5rem;">Guardar canvis</button>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Accordion
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.category-toggle');
            if (!btn) return;
            btn.nextElementSibling.classList.toggle('open');
            btn.classList.toggle('open');
        });

        // Tots els permisos
        document.getElementById('btnTotsElsPermisos').addEventListener('click', function () {
            document.querySelectorAll('.permissions-accordion input[type="checkbox"]')
                .forEach(cb => cb.checked = true);
        });

        // Cap permís
        document.getElementById('btnCapPermis').addEventListener('click', function () {
            document.querySelectorAll('.permissions-accordion input[type="checkbox"]')
                .forEach(cb => cb.checked = false);
        });
    });
    </script>
    @endpush
</x-app-layout>