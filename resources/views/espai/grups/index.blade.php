@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">{{ __('messages.groups_index_title') }}</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.grups.create') }}" class="btn btn-primary @cantEspaiClass('groups.create')">
                + {{ __('messages.group_create_title') }}
            </a>
            <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                <i class="bi bi-box-arrow-right"></i> {{ __('messages.back_to_space') }}
            </a>

        </div>

        <div class="container">
            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            <div class="card">
                @forelse ($grups as $grup)
                    <div class="user-row">
                        <div class="user-info">
                            <div class="user-name">{{ $grup->nom }}</div>
                            <div class="user-meta">{{ $grup->alumnes()->count() }} {{ __('messages.students_lower') }}</div>
                        </div>

                        <div class="user-actions">
                            <a class="btn btn-secondary @cantEspaiClass('groups.view')"
                               href="{{ route('espai.grups.veure', $grup) }}">
                                {{ __('messages.view') }}
                            </a>
                            <a class="btn btn-secondary @cantEspaiClass('groups.update')"
                               href="{{ route('espai.grups.edit', $grup) }}">
                                {{ __('messages.manage') }}
                            </a>
                            <form class="inline-form"
                                  method="POST"
                                  action="{{ route('espai.grups.destroy', $grup) }}"
                                  onsubmit="return confirm('{{ __('messages.group_delete_confirm') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger @cantEspaiClass('groups.delete')">
                                    {{ __('messages.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">{{ __('messages.groups_empty') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>