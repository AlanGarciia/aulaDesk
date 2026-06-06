@push('styles')
    @vite('resources/css/espai/franges/frangesIndex.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">
            <p>
                <a class="btn btn-secondary" href="{{ route('espai.aules.index') }}">{{ __('messages.back_to_classrooms') }}</a>
                <a class="btn btn-primary @cantEspaiClass('aulas.manage')" href="{{ route('espai.franges.create') }}">{{ __('messages.slot_create_title') }}</a>
            </p>

            @if(session('ok'))
                <div class="alert success">{{ session('ok') }}</div>
            @endif

            <div class="card">
                <table border="1" cellpadding="8" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>{{ __('messages.order') }}</th><th>{{ __('messages.name') }}</th><th>{{ __('messages.start') }}</th><th>{{ __('messages.end') }}</th><th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($franges as $f)
                            <tr>
                                <td>{{ $f->ordre }}</td>
                                <td>{{ $f->nom }}</td>
                                <td>{{ substr($f->inici,0,5) }}</td>
                                <td>{{ substr($f->fi,0,5) }}</td>
                                <td>
                                    <a class="btn @cantEspaiClass('aulas.manage')" href="{{ route('espai.franges.edit', $f) }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('espai.franges.destroy', $f) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn danger @cantEspaiClass('aulas.manage')" type="submit"
                                                onclick="return confirm('{{ __('messages.slot_delete_confirm') }}')">
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">{{ __('messages.slots_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>