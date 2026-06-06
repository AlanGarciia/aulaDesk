@push('styles')
    @vite('resources/css/espai/roles/edit.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">{{ __('messages.role_edit_title') }}</h2>
        </div>

        <div class="actions">
           <a href="{{ $from_user ? route('espai.usuaris.roles', $from_user) : route('espai.roles.index') }}" class="btn btn-secondary">
               <i class="bi bi-box-arrow-right me-2" ></i> {{ __('messages.back') }}
           </a>
        </div>

        <form method="POST" action="{{ route('espai.roles.update', $role) }}" class="form-card">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>{{ __('messages.role_name') }}</label>
                <input type="text" name="nom" value="{{ $role->nom }}" required>
            </div>

            <div class="permissions-header">
                <h3>{{ __('messages.role_permissions') }}</h3>
                <span>{{ __('messages.role_permissions_sub') }}</span>
            </div>

            {{-- Botó seleccionar-ho tot --}}
            <div style="margin-bottom:1rem;">
                <button type="button" id="btnTotsElsPermisos" class="btn btn-secondary">
                    ✅ {{ __('messages.all_permissions') }}
                </button>
                <button type="button" id="btnCapPermis" class="btn btn-secondary" style="margin-left:.5rem;">
                    ☐ {{ __('messages.no_permissions') }}
                </button>
            </div>

            <div class="permissions-accordion">
                @foreach($groupedPermissions as $category => $perms)
                    @php
                        $moduleNames = [
                            'users'       => __('messages.users'),
                            'groups'      => __('messages.groups'),
                            'students'    => __('messages.students'),
                            'aulas'       => __('messages.classrooms'),
                            'noticies'    => __('messages.news'),
                            'guardies'    => __('messages.guardies_title'),
                            'tickets'     => __('messages.tickets'),
                            'roles'       => __('messages.roles'),
                            'permissions' => __('messages.permissions'),
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
            
            <button class="btn btn-primary" style="margin-top:1.5rem;">{{ __('messages.save_changes') }}</button>
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