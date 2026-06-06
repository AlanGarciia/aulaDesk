@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">{{ __('messages.assign_roles_to', ['name' => $usuari->nom]) }}</h2>
        </div>

        <div class="actions" style="display:flex; gap:1rem;">

            <a href="{{ route('espai.usuaris.index') }}" class="btn btn-secondary">
                <i class="bi bi-box-arrow-right me-1"></i>
                {{ __('messages.back') }}
            </a>

            <a href="{{ route('espai.roles.create', $usuari->id) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                {{ __('messages.role_create') }}
            </a>

        </div>

        <div class="container">

            @if(session('ok'))
                <div class="alert-success" style="margin-bottom:1rem;padding:.75rem 1rem;border-radius:8px;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.4);color:#d1fae5;">
                    {{ session('ok') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error" style="margin-bottom:1rem;padding:.75rem 1rem;border-radius:8px;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.4);color:#fecaca;">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $rolesOrdenats = ($roles ?? collect())
                    ->sortBy(function ($r) {
                        return $r->nom === 'admin' ? '0_admin' : '1_' . strtolower($r->nom);
                    })
                    ->values();
            @endphp

            <form method="POST" action="{{ route('espai.usuaris.roles.store', $usuari) }}" class="card" style="padding:1.5rem;">
                @csrf

                <h3 style="color:white; margin-bottom:1rem;">{{ __('messages.available_roles') }}</h3>

                @forelse($rolesOrdenats as $role)
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:.5rem; gap:.75rem;">

                        <label style="color:white; display:flex; align-items:center; gap:.5rem;">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   {{ $usuari->roles->contains($role->id) ? 'checked' : '' }}>
                            {{ $role->nom }}
                        </label>

                        <div style="display:flex; gap:.5rem;">
                            <a href="{{ route('espai.roles.edit', ['role' => $role->id, 'from_user' => $usuari->id]) }}"
                               class="btn btn-secondary">
                                <i class="bi bi-gear me-1"></i>
                                {{ __('messages.manage_role') }}
                            </a>

                            @if($role->nom !== 'admin')
                                <button type="button"
                                        class="btn btn-danger btn-icon @cantEspaiClass('roles.delete')"
                                        title="{{ __('messages.delete_role') }}"
                                        data-role-name="{{ $role->nom }}"
                                        data-form-id="delete-role-{{ $role->id }}"
                                        onclick="confirmDeleteRole(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>

                    </div>
                @empty
                    <p style="color:white;">{{ __('messages.roles_empty') }}</p>
                @endforelse

                <button type="submit" class="btn btn-primary" style="margin-top:1rem;">{{ __('messages.save') }}</button>
            </form>

            @foreach($rolesOrdenats as $role)
                @if($role->nom !== 'admin')
                    <form id="delete-role-{{ $role->id }}"
                          method="POST"
                          action="{{ route('espai.roles.destroy', $role) }}"
                          style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            @endforeach

        </div>

    </div>

    @push('scripts')
    <script>
        const txtDeleteRoleConfirm = @json(__('messages.role_delete_confirm'));

        function confirmDeleteRole(btn) {
            const roleName = btn.dataset.roleName;
            const formId = btn.dataset.formId;
            if (confirm(txtDeleteRoleConfirm + ' "' + roleName + '"?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>
    @endpush
</x-app-layout>