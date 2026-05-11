@push('styles')
    @vite('resources/css/espai/aules/admin.css')
@endpush

<x-app-layout>
    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">
                    <i class="bi bi-ticket-detailed"></i>
                    Tickets de l'aula: {{ $aula->nom }}
                </h2>
                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.aules.index') }}">
                        <i class="bi bi-arrow-left"></i> Tornar a les aules
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
                    <h3 class="section-title">Crear nou ticket</h3>
                </div>

                <form method="POST" action="{{ route('espai.aules.tickets.store', $aula) }}" class="ticket-form">
                    @csrf
                    <input type="text" name="titol" class="input-control" placeholder="Títol..." required>
                    <input type="text" name="descripcio" class="input-control" placeholder="Descripció...">
                    <select name="prioritat" class="input-control select-small">
                        <option value="baixa">Baixa</option>
                        <option value="mitja" selected>Mitja</option>
                        <option value="alta">Alta</option>
                    </select>
                    <button class="btn btn-primary @cantEspaiClass('tickets.create')">Crear</button>
                </form>
            </div>

            <div class="panel-card">
                <div class="section-header">
                    <h3 class="section-title">Llista de tickets</h3>
                </div>

                @if($tickets->isEmpty())
                    <div class="empty-state">No hi ha tickets per aquesta aula.</div>
                @else
                    <div class="table-wrap">
                        <table class="data-table ticket-table">
                            <thead>
                                <tr>
                                    <th>Títol</th>
                                    <th>Prioritat</th>
                                    <th>Estat</th>
                                    <th>Accions</th>
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
                                            <form method="POST" action="{{ route('espai.aules.tickets.update', [$aula, $ticket]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <select name="estat" onchange="this.form.submit()"
                                                        {{ ($_espaiUser && $_espaiUser->canEspai('tickets.update')) ? '' : 'disabled' }}>
                                                    <option value="obert"     {{ $ticket->estat === 'obert'     ? 'selected' : '' }}>Obert</option>
                                                    <option value="en_proces" {{ $ticket->estat === 'en_proces' ? 'selected' : '' }}>En procés</option>
                                                    <option value="tancat"    {{ $ticket->estat === 'tancat'    ? 'selected' : '' }}>Tancat</option>
                                                </select>
                                            </form>
                                            <form method="POST" action="{{ route('espai.aules.tickets.destroy', [$aula, $ticket]) }}"
                                                  onsubmit="return confirm('Segur que vols eliminar aquest ticket?');">
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