@vite('resources/css/espai/aules/admin.css')

<x-app-layout>
    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">{{ __('messages.open_tickets_title') }}</h2>
                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.index') }}">{{ __('messages.back') }}</a>
                </div>
            </div>
            <h1>{{ __('messages.hello') }}</h1>

            @if($tickets->isEmpty())
                <div class="empty-state">{{ __('messages.open_tickets_empty') }}</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.classroom') }}</th>
                                <th>{{ __('messages.title') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.priority') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->aula->nom }}</td>
                                    <td><strong>{{ $ticket->titol }}</strong></td>
                                    <td>{{ $ticket->descripcio ?? '-' }}</td>
                                    <td>
                                        @if($ticket->prioritat === 'alta')
                                            <span class="badge badge-alta">{{ __('messages.priority_high') }}</span>
                                        @elseif($ticket->prioritat === 'mitja')
                                            <span class="badge badge-mitja">{{ __('messages.priority_medium') }}</span>
                                        @else
                                            <span class="badge badge-baixa">{{ __('messages.priority_low') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->estat === 'obert')
                                            <span class="badge badge-obert">{{ __('messages.status_open') }}</span>
                                        @elseif($ticket->estat === 'en_proces')
                                            <span class="badge badge-proces">{{ __('messages.status_in_progress') }}</span>
                                        @else
                                            <span class="badge badge-tancat">{{ __('messages.status_closed') }}</span>
                                        @endif
                                    </td>
                                    <td class="ticket-actions">
                                        <form method="POST" action="{{ route('espai.aules.tickets.update', [$ticket->aula, $ticket]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="estat" onchange="this.form.submit()">
                                                <option value="obert" {{ $ticket->estat === 'obert' ? 'selected' : '' }}>{{ __('messages.status_open') }}</option>
                                                <option value="en_proces" {{ $ticket->estat === 'en_proces' ? 'selected' : '' }}>{{ __('messages.status_in_progress') }}</option>
                                                <option value="tancat" {{ $ticket->estat === 'tancat' ? 'selected' : '' }}>{{ __('messages.status_closed') }}</option>
                                            </select>
                                        </form>

                                        <form method="POST" action="{{ route('espai.aules.tickets.destroy', [$ticket->aula, $ticket]) }}" style="margin-top:5px;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-small">🗑 {{ __('messages.close_ticket') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>