@vite('resources/css/espai/aules/admin.css')

<x-app-layout>
    <div class="page">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">Administrar aula: {{ $aula->nom }}</h2>

                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.aules.index') }}">
                        Tornar
                    </a>
                </div>
            </div>

            {{-- Conflictes --}}
            @php
                $conflicts = session('conflicts');
                $hasConflicts = is_array($conflicts) && count($conflicts);
            @endphp

            @if($hasConflicts)
                <div class="conflict-backdrop" id="conflictModal">
                    <div class="conflict-card">
                        <div class="conflict-head">
                            <h3 class="conflict-title">Conflicte d’horari</h3>
                            <button type="button" class="btn btn-secondary" id="closeConflictModal">Tancar</button>
                        </div>

                        <div class="conflict-body">
                            <p>No s’ha pogut desar perquè el professor ja està assignat a una altra aula en el mateix moment.</p>

                            <ul class="conflict-list">
                                @foreach($conflicts as $c)
                                    <li>
                                        <span class="conflict-tag">{{ $c['professor'] }}</span>
                                        <span>{{ $c['dia'] }}, {{ $c['franja'] }}</span>
                                        <span class="conflict-extra">Ja està a: <strong>{{ $c['aula'] }}</strong></span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="conflict-foot">
                            <button type="button" class="btn btn-primary" id="closeConflictModal2">Entes</button>
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('closeConflictModal').onclick =
                    document.getElementById('closeConflictModal2').onclick =
                        () => document.getElementById('conflictModal').style.display = 'none';
                </script>
            @endif

            {{-- Horari --}}
            <div class="panel-card">
                <div class="section-header">
                    <h3 class="section-title">Horari de l’aula</h3>
                </div>

                <form method="POST" action="{{ route('espai.aules.admin.update', $aula) }}">
                    @csrf

                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Franja</th>
                                    @foreach($dies as $diaNom)
                                        <th>{{ $diaNom }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($franges as $franja)
                                <tr>
                                    <td class="franja-cell">
                                        @if($franja->nom)
                                            <strong>{{ $franja->nom }}</strong><br>
                                        @endif
                                        {{ substr($franja->inici, 0, 5) }} - {{ substr($franja->fi, 0, 5) }}
                                    </td>

                                    @foreach($dies as $diaNum => $diaNom)
                                        @php
                                            $professorId = $assignacions[$diaNum][$franja->id]['professor'] ?? '';
                                            $grupId = $assignacions[$diaNum][$franja->id]['grup'] ?? '';
                                            $grupNom = $grups->firstWhere('id', $grupId)->nom ?? null;
                                        @endphp

                                        <td>
                                            <div class="horari-cell">

                                                {{-- SELECT DE PROFESSOR --}}
                                                <select class="input-control select-control"
                                                        name="professors[{{ $diaNum }}][{{ $franja->id }}]">
                                                    <option value="">-- lliure --</option>

                                                    @foreach($professors as $p)
                                                        <option value="{{ $p->id }}"
                                                            {{ (string)$professorId === (string)$p->id ? 'selected' : '' }}>
                                                            👨‍🏫 {{ $p->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                {{-- BOTÓN PARA ASIGNAR GRUPO --}}
                                                <button type="button"
                                                        class="btn btn-small btn-secondary open-grup-modal"
                                                        data-dia="{{ $diaNum }}"
                                                        data-franja="{{ $franja->id }}"
                                                        style="margin-top:4px;">
                                                    Assignar grup
                                                </button>

                                                {{-- INPUT HIDDEN PARA GUARDAR EL GRUPO --}}
                                                <input type="hidden"
                                                       class="input-grup"
                                                       name="grups[{{ $diaNum }}][{{ $franja->id }}]"
                                                       value="{{ $grupId }}">

                                                {{-- MOSTRAR GRUPO ASIGNADO --}}
                                                <div class="grup-label" style="margin-top:4px; font-size:13px; color:#444;">
                                                    @if($grupNom)
                                                        Grup: <strong>{{ $grupNom }}</strong>
                                                    @else
                                                        Sense grup
                                                    @endif
                                                </div>

                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit">Desar horari</button>
                    </div>
                </form>
            </div>

            {{-- MODAL DE GRUPS --}}
            <style>
                .modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.65);
                    display: none;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    backdrop-filter: blur(3px);
                }
                .modal-content {
                    background: linear-gradient(180deg, #ffffff 0%, #f3f3f3 100%);
                    padding: 22px;
                    border-radius: 10px;
                    max-width: 420px;
                    width: 90%;
                    box-shadow: 0 8px 30px rgba(0,0,0,0.35);
                    border: 1px solid #d0d0d0;
                }
            </style>

            <div id="modalGrups" class="modal">
                <div class="modal-content">
                    <h3>Selecciona un grup</h3>

                    <input type="text" id="buscadorGrups" class="input-control"
                           placeholder="Cerca grup..." style="margin-bottom:10px;">

                    <div id="llistaGrups">
                        @foreach($grups as $g)
                            <button class="btn btn-primary grup-option"
                                    data-grup-id="{{ $g->id }}"
                                    data-grup-nom="{{ $g->nom }}"
                                    style="width:100%; margin-bottom:5px;">
                                {{ $g->nom }}
                            </button>
                        @endforeach
                    </div>

                    <button class="btn btn-secondary" id="tancarModalGrups">Tancar</button>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    let modal = document.getElementById('modalGrups');
                    let buscador = document.getElementById('buscadorGrups');
                    let currentHiddenInput = null;
                    let currentLabel = null;

                    // Abrir modal
                    document.querySelectorAll('.open-grup-modal').forEach(btn => {
                        btn.addEventListener('click', function () {

                            let dia = this.dataset.dia;
                            let franja = this.dataset.franja;

                            currentHiddenInput = document.querySelector(
                                `input[name="grups[${dia}][${franja}]"]`
                            );

                            currentLabel = this.parentElement.querySelector('.grup-label');

                            buscador.value = "";
                            filtrarGrups("");

                            modal.style.display = 'flex';
                        });
                    });

                    // Filtrar grupos
                    buscador.addEventListener('input', function () {
                        filtrarGrups(this.value.toLowerCase());
                    });

                    function filtrarGrups(texto) {
                        document.querySelectorAll('.grup-option').forEach(btn => {
                            let nom = btn.dataset.grupNom.toLowerCase();
                            btn.style.display = nom.includes(texto) ? 'block' : 'none';
                        });
                    }

                    // Seleccionar grupo
                    document.querySelectorAll('.grup-option').forEach(btn => {
                        btn.addEventListener('click', function () {

                            let id = this.dataset.grupId;
                            let nom = this.dataset.grupNom;

                            currentHiddenInput.value = id;
                            currentLabel.innerHTML = `Grup: <strong>${nom}</strong>`;

                            modal.style.display = 'none';
                        });
                    });

                    // Cerrar modal
                    document.getElementById('tancarModalGrups').onclick =
                        () => modal.style.display = 'none';
                });
            </script>

            {{-- TICKETS --}}
            <div class="panel-card">
                <div class="section-header">
                    <h3 class="section-title">Tickets de l’aula</h3>
                </div>

                {{-- FORMULARIO TICKETS --}}
                <form method="POST" action="{{ route('espai.aules.tickets.store', $aula) }}">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label for="titol">Títol</label>
                            <input id="titol" class="input-control" name="titol" value="{{ old('titol') }}" required>
                        </div>

                        <div class="field field-small">
                            <label for="prioritat">Prioritat</label>
                            <select id="prioritat" class="input-control select-control" name="prioritat">
                                @foreach(['baixa'=>'Baixa','mitja'=>'Mitja','alta'=>'Alta'] as $k => $v)
                                    <option value="{{ $k }}" {{ old('prioritat','mitja') === $k ? 'selected' : '' }}>
                                        {{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label for="descripcio">Descripció</label>
                        <textarea id="descripcio" class="input-control textarea-control" name="descripcio" rows="4">{{ old('descripcio') }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit">Crear ticket</button>
                    </div>
                </form>

                <hr class="section-divider">

                {{-- LISTA DE TICKETS --}}
                @if($tickets->isEmpty())
                    <div class="empty-state">No hi ha tickets.</div>
                @else
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Títol</th>
                                    <th>Prioritat</th>
                                    <th>Estat</th>
                                    <th>Creat per</th>
                                    <th>Creat</th>
                                    <th>Accions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($tickets as $t)
                                    <tr>
                                        <td>{{ $t->id }}</td>
                                        <td>
                                            <strong>{{ $t->titol }}</strong>
                                            @if($t->descripcio)
                                                <div class="ticket-desc">{{ $t->descripcio }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $t->prioritat }}</td>
                                        <td>{{ $t->estat }}</td>
                                        <td>{{ $t->creador->nom ?? '-' }}</td>
                                        <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>

                                        <td class="actions-cell">
                                            <form method="POST" action="{{ route('espai.aules.tickets.update', [$aula, $t]) }}" class="inline-form">
                                                @csrf
                                                @method('PATCH')
                                                <select class="input-control select-control select-small" name="estat" onchange="this.form.submit()">
                                                    @foreach(['obert'=>'Obert','en_proces'=>'En procés','tancat'=>'Tancat'] as $k => $v)
                                                        <option value="{{ $k }}" {{ $t->estat === $k ? 'selected' : '' }}>
                                                            {{ $v }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>

                                            <form method="POST" action="{{ route('espai.aules.tickets.destroy', [$aula, $t]) }}" class="inline-form" onsubmit="return confirm('Eliminar ticket?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit">Eliminar</button>
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
