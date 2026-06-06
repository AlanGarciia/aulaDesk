@push('styles')
    @vite('resources/css/espai/aules/admin.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">
                    <i class="bi bi-ticket-detailed"></i>
                    {{ __('messages.classroom_tickets_title') }}: {{ $aula->nom }}
                </h2>
                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.aules.index') }}">
                        <i class="bi bi-arrow-left"></i> {{ __('messages.back_to_classrooms') }}
                    </a>
                </div>
            </div>

            @if(session('ok'))
                <div class="alert-success" style="margin-bottom:1rem;padding:.75rem 1rem;border-radius:8px;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.4);color:#d1fae5;">
                    {{ session('ok') }}
                </div>
            @endif

            @php
                $_espaiUserId = session('usuari_espai_id');
                $_espaiUser   = $_espaiUserId ? \App\Models\UsuariEspai::find($_espaiUserId) : null;
            @endphp

            <div class="panel-card">
                <div class="section-header">
                    <h3 class="section-title">{{ __('messages.ticket_create_title') }}</h3>
                </div>

                <form method="POST" action="{{ route('espai.aules.tickets.store', $aula) }}" class="ticket-form">
                    @csrf
                    <input type="text" name="titol" class="input-control" placeholder="{{ __('messages.title_placeholder') }}" required>
                    <input type="text" name="descripcio" class="input-control" placeholder="{{ __('messages.description_placeholder_short') }}">
                    <select name="prioritat" class="input-control select-small">
                        <option value="baixa">{{ __('messages.priority_low') }}</option>
                        <option value="mitja" selected>{{ __('messages.priority_medium') }}</option>
                        <option value="alta">{{ __('messages.priority_high') }}</option>
                    </select>
                    <button class="btn btn-primary @cantEspaiClass('tickets.create')">{{ __('messages.create') }}</button>
                </form>
            </div>

            <div class="panel-card">
                <div class="section-header">
                    <h3 class="section-title">{{ __('messages.ticket_list_title') }}</h3>
                </div>

                @if($tickets->isEmpty())
                    <div class="empty-state">{{ __('messages.tickets_empty') }}</div>
                @else
                    <div class="table-wrap">
                        <table class="data-table ticket-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.title') }}</th>
                                    <th>{{ __('messages.priority') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <strong>{{ $ticket->titol }}</strong>
                                            @if($ticket->descripcio)
                                                <div class="ticket-desc">{{ $ticket->descripcio }}</div>
                                            @endif
                                        </td>
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
                                            <form method="POST" action="{{ route('espai.aules.tickets.update', [$aula, $ticket]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <select name="estat" onchange="this.form.submit()"
                                                        {{ ($_espaiUser && $_espaiUser->canEspai('tickets.update')) ? '' : 'disabled' }}>
                                                    <option value="obert"     {{ $ticket->estat === 'obert'     ? 'selected' : '' }}>{{ __('messages.status_open') }}</option>
                                                    <option value="en_proces" {{ $ticket->estat === 'en_proces' ? 'selected' : '' }}>{{ __('messages.status_in_progress') }}</option>
                                                    <option value="tancat"    {{ $ticket->estat === 'tancat'    ? 'selected' : '' }}>{{ __('messages.status_closed') }}</option>
                                                </select>
                                            </form>
                                            <form method="POST" action="{{ route('espai.aules.tickets.destroy', [$aula, $ticket]) }}"
                                                  onsubmit="return confirm('{{ __('messages.ticket_delete_confirm') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-small @cantEspaiClass('tickets.delete')">🗑</button>
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
    </div>
</x-app-layout>