@push('styles')
    @vite('resources/css/espai/usuaris/usuarisIndex.css')
@endpush

<x-app-layout>
    <div class="page">

        <div class="page-title-container">
            <h2 class="page-title">{{ __('messages.students_index_title') }}</h2>
        </div>

        <div class="actions">
            <a href="{{ route('espai.alumnes.create') }}" class="btn btn-primary @cantEspaiClass('students.create')">
                + {{ __('messages.students_add_title') }}
            </a>

                    @if(auth()->user()->plan === 'premium')
            <a href="{{ route('espai.alumnes.import.form') }}" class="btn btn-primary">
                {{ __('messages.import_csv') }}
            </a>
        @else
            <button class="btn btn-secondary" disabled>
                {{ __('messages.import_csv') }} 🔒 PREMIUM
            </button>
        @endif

        @if(auth()->user()->plan === 'premium')
            <a href="{{ route('espai.alumnes.export') }}" class="btn btn-primary">
                {{ __('messages.export_csv') }}
            </a>
        @else
            <button class="btn btn-secondary" disabled>
                {{ __('messages.export_csv') }} 🔒 PREMIUM
            </button>
        @endif

            <a href="{{ route('espai.grups.index') }}" class="btn btn-secondary @cantEspaiClass('groups.view')">
                <i class="bi bi-people"></i> {{ __('messages.view_groups') }}
            </a>
            <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                <i class="bi bi-box-arrow-right"></i>
                {{ __('messages.back_to_space') }}
            </a>
        </div>

        <div class="container">
            @if (session('ok'))
                <div class="alert-success">{{ session('ok') }}</div>
            @endif

            <form method="GET" action="{{ route('espai.alumnes.index') }}" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="nom">{{ __('messages.name') }}</label>
                        <input type="text" name="nom" id="nom" value="{{ request('nom') }}" placeholder="{{ __('messages.search_by_name') }}">
                    </div>
                    <div class="filter-group">
                        <label for="idalu">{{ __('messages.idalu') }}</label>
                        <input type="text" name="idalu" id="idalu" value="{{ request('idalu') }}" placeholder="{{ __('messages.search_by_idalu') }}">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                        <a href="{{ route('espai.alumnes.index') }}" class="btn btn-secondary">{{ __('messages.clear') }}</a>
                    </div>
                </div>
            </form>

            <div class="card">
                @forelse ($alumnes as $alumne)
                    <div class="user-row">
                        <div class="user-info">
                            <div class="user-name">{{ $alumne->nom }} {{ $alumne->cognoms }}</div>
                            <div class="user-meta">
                                {{ __('messages.idalu') }}: {{ $alumne->idalu }}<br>
                                @if($alumne->correu) {{ __('messages.email') }}: {{ $alumne->correu }}<br> @endif
                                @if($alumne->telefon) {{ __('messages.phone') }}: {{ $alumne->telefon }} @endif
                            </div>
                        </div>
                        <div class="user-actions">
                            <a href="{{ route('espai.alumnes.edit', $alumne) }}" class="btn btn-secondary @cantEspaiClass('students.update')">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="{{ route('espai.alumnes.info', $alumne) }}" class="btn btn-secondary @cantEspaiClass('students.view')">
                                <i class="bi bi-info-circle"></i>
                            </a>
                            <form class="inline-form"
                                  method="POST"
                                  action="{{ route('espai.alumnes.destroy', $alumne) }}"
                                  onsubmit="return confirm('{{ __('messages.students_delete_confirm') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger @cantEspaiClass('students.delete')">
                                    {{ __('messages.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">{{ __('messages.students_empty') }}</p>
                @endforelse
            </div>

            <div class="pagination">{{ $alumnes->links() }}</div>
        </div>
    </div>
</x-app-layout>