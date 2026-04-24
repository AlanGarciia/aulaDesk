@push('styles')
    @vite('resources/css/espai/roles/create.css')
@endpush

<x-app-layout>
    <div class="page">

        <div style="width:min(780px,100%);margin:0 auto 1rem;">
            <a href="{{ route('espai.roles.index') }}" class="btn btn-secondary">Tornar</a>
        </div>

        <form method="POST" action="{{ route('espai.roles.store') }}" class="form-card">
            @csrf

            <label>Nom del rol</label>
            <input type="text" name="nom" required>

            <h3 style="color:rgba(255,255,255,.92);margin-bottom:.6rem;">Permisos disponibles</h3>

            {{-- Botons ràpids --}}
            <div style="margin-bottom:1rem;display:flex;gap:.5rem;flex-wrap:wrap;">
                <button type="button" id="btnTotsElsPermisos" class="btn btn-secondary">
                    ✅ Tots els permisos
                </button>
                <button type="button" id="btnCapPermis" class="btn btn-secondary">
                    ☐ Cap permís
                </button>
            </div>

            @php
                $grouped = $permissions->groupBy(function($p){
                    return explode('.', $p->nom)[0];
                });

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
            @endphp

            @foreach($grouped as $module => $items)
                <div class="permission-group">
                    <button type="button" class="permission-group-toggle"
                            onclick="this.nextElementSibling.classList.toggle('open')">
                        {{ $moduleNames[$module] ?? ucfirst($module) }}
                    </button>

                    <div class="permission-group-content">
                        <div class="permissions-grid">
                            @foreach($items as $permission)
                                <label class="permission-item">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}">
                                    <span>{{ $permission->nom_format }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <button class="btn btn-primary" style="margin-top:1.5rem;">Crear</button>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btnTotsElsPermisos').addEventListener('click', function () {
            document.querySelectorAll('input[name="permissions[]"]')
                .forEach(cb => cb.checked = true);
        });
        document.getElementById('btnCapPermis').addEventListener('click', function () {
            document.querySelectorAll('input[name="permissions[]"]')
                .forEach(cb => cb.checked = false);
        });
    });
    </script>
    @endpush
</x-app-layout>