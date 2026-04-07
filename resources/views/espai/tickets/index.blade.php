@vite('resources/css/espai/aules/admin.css')

<x-app-layout>
    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">Tickets oberts</h2>
                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.index') }}">Tornar</a>
                </div>
            </div>

            @if($tickets->isEmpty())
                <div class="empty-state">No hi ha tickets oberts.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aula</th>
                                <th>Títol</th>
                                <th>Descripció</th>
                                <th>Prioritat</th>
                                <th>Estat</th>
                                <th>Accions</th>
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
                                            <span class="badge badge-alta">Alta</span>
                                        @elseif($ticket->prioritat === 'mitja')
                                            <span class="badge badge-mitja">Mitja</span>
                                        @else
                                            <span class="badge badge-baixa">Baixa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->estat === 'obert')
                                            <span class="badge badge-obert">Obert</span>
                                        @elseif($ticket->estat === 'en_proces')
                                            <span class="badge badge-proces">En procés</span>
                                        @else
                                            <span class="badge badge-tancat">Tancat</span>
                                        @endif
                                    </td>
                                    <td class="ticket-actions">
                                        <form method="POST" action="{{ route('espai.aules.tickets.update', [$ticket->aula, $ticket]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="estat" onchange="this.form.submit()">
                                                <option value="obert" {{ $ticket->estat === 'obert' ? 'selected' : '' }}>Obert</option>
                                                <option value="en_proces" {{ $ticket->estat === 'en_proces' ? 'selected' : '' }}>En procés</option>
                                                <option value="tancat" {{ $ticket->estat === 'tancat' ? 'selected' : '' }}>Tancat</option>
                                            </select>
                                        </form>

                                        <form method="POST" action="{{ route('espai.aules.tickets.destroy', [$ticket->aula, $ticket]) }}" style="margin-top:5px;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-small">🗑 Tancar</button>
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