@vite('resources/css/espai/aules/admin.css')

<x-app-layout>
    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">
                    <i class="bi bi-ticket-detailed"></i> Tots els tickets
                </h2>
                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.aules.index') }}">
                        <i class="bi bi-arrow-left"></i> Tornar a les aules
                    </a>
                </div>
            </div>

            @if(session('ok'))
                <div class="modal-overlay">
                    <div class="modal-box">{{ session('ok') }}</div>
                </div>
            @endif

            @php
                $_espaiUserId = session('usuari_espai_id');
                $_espaiUser   = $_espaiUserId ? \App\Models\UsuariEspai::find($_espaiUserId) : null;
                $potUpdate    = $_espaiUser && $_espaiUser->canEspai('tickets.update');
                $potDelete    = $_espaiUser && $_espaiUser->canEspai('tickets.delete');
            @endphp

            {{-- Filtres --}}
            <div class="panel-card">
                <div class="section-header">
                    <h3 class="section-title">Filtres</h3>
                </div>
                <form method="GET" class="tickets-filters">
                    <div class="form-grid" style="grid-template-columns: repeat(4, 1fr); gap: .8rem;">
                        <div class="field field-small">
                            <label>Cercar</label>
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="Títol o descripció..." class="input-control">
                        </div>
                        <div class="field field-small">
                            <label>Aula</label>
                            <select name="aula_id" class="input-control select-control">
                                <option value="">Totes</option>
                                @foreach($aules as $a)
                                    <option value="{{ $a->id }}" {{ (string) request('aula_id') === (string) $a->id ? 'selected' : '' }}>
                                        {{ $a->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field field-small">
                            <label>Estat</label>
                            <select name="estat" class="input-control select-control">
                                <option value="">Tots</option>
                                <option value="obert"     {{ request('estat') === 'obert'     ? 'selected' : '' }}>Obert</option>
                                <option value="en_proces" {{ request('estat') === 'en_proces' ? 'selected' : '' }}>En procés</option>
                                <option value="tancat"    {{ request('estat') === 'tancat'    ? 'selected' : '' }}>Tancat</option>
                            </select>
                        </div>
                        <div class="field field-small">
                            <label>Prioritat</label>
                            <select name="prioritat" class="input-control select-control">
                                <option value="">Totes</option>
                                <option value="alta"   {{ request('prioritat') === 'alta'   ? 'selected' : '' }}>Alta</option>
                                <option value="mitja"  {{ request('prioritat') === 'mitja'  ? 'selected' : '' }}>Mitja</option>
                                <option value="baixa"  {{ request('prioritat') === 'baixa'  ? 'selected' : '' }}>Baixa</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <a href="{{ route('espai.tickets.all') }}" class="btn btn-secondary">Netejar</a>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>

            {{-- Llista --}}
            <div class="panel-card">
                <div class="section-header">
                    <h3 class="section-title">Tickets ({{ $tickets->total() }})</h3>
                </div>

                @if($tickets->isEmpty())
                    <div class="empty-state">No hi ha tickets que coincideixin amb els filtres.</div>
                @else
                    <div class="table-wrap">
                        <table class="data-table ticket-table">
                            <thead>
                                <tr>
                                    <th>Títol</th>
                                    <th>Aula</th>
                                    <th>Prioritat</th>
                                    <th>Estat</th>
                                    <th>Creador</th>
                                    <th>Data</th>
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
                                            @if($ticket->aula)
                                                <a href="{{ route('espai.aules.tickets.index', $ticket->aula) }}"
                                                   style="color: hsla(150, 80%, 78%, 0.98); text-decoration: none;">
                                                    {{ $ticket->aula->nom }}
                                                </a>
                                            @else
                                                —
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
                                        <td>
                                            @if($ticket->creador)
                                                {{ $ticket->creador->nom }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="ticket-actions">
                                            @if($ticket->aula)
                                                <form method="POST"
                                                      action="{{ route('espai.aules.tickets.update', [$ticket->aula, $ticket]) }}"
                                                      class="inline-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="estat" onchange="this.form.submit()"
                                                            class="select-small"
                                                            {{ $potUpdate ? '' : 'disabled' }}>
                                                        <option value="obert"     {{ $ticket->estat === 'obert'     ? 'selected' : '' }}>Obert</option>
                                                        <option value="en_proces" {{ $ticket->estat === 'en_proces' ? 'selected' : '' }}>En procés</option>
                                                        <option value="tancat"    {{ $ticket->estat === 'tancat'    ? 'selected' : '' }}>Tancat</option>
                                                    </select>
                                                </form>

                                                <form method="POST"
                                                      action="{{ route('espai.aules.tickets.destroy', [$ticket->aula, $ticket]) }}"
                                                      class="inline-form"
                                                      onsubmit="return confirm('Eliminar aquest ticket?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-small"
                                                            {{ $potDelete ? '' : 'disabled' }}>🗑</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination" style="margin-top:1rem;">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>