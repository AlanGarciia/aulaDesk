@push('styles')
    @vite('resources/css/espai/roles/create.css')
@endpush
<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">{{ __('messages.roles_index_title') }}</h2>
        </div>

        <div class="actions" style="width:min(980px,100%);margin:auto;">
            <a href="{{ route('espai.roles.create') }}" class="btn btn-primary @cantEspaiClass('roles.create')">+ {{ __('messages.role_create') }}</a>
        </div>

        <div class="card role-list-card">
            @forelse($roles as $role)
                <div class="role-row">
                    <span class="role-name">{{ $role->nom }}</span>
                    <a href="{{ route('espai.roles.edit', $role) }}" class="btn btn-secondary @cantEspaiClass('roles.update')">{{ __('messages.edit') }}</a>
                </div>
            @empty
                <div class="empty-state">
                    {{ __('messages.roles_empty') }}
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>