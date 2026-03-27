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

                <form method="POST" action="{{ route('espai.aules.horari.update', $aula) }}">
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
                                            $grupId = $assignacions[$diaNum][$franja->id]['grup'] ?? null;
                                            $grupNom = $grupId ? optional($grups->firstWhere('id', $grupId))->nom : null;

                                            // Detectar si hay guardia
                                            $isGuardia = isset($solSlots[$diaNum][$franja->id]);
                                        @endphp

                                        <td>
                                            <div class="horari-cell {{ $isGuardia ? 'guardia' : '' }}">
                                                {{-- SELECT DEL PROFESSOR --}}
                                                <select class="input-control select-control"
                                                        name="assignacions[{{ $diaNum }}][{{ $franja->id }}]">
                                                    <option value="">-- lliure --</option>
                                                    @foreach($professors as $p)
                                                        <option value="{{ $p->id }}"
                                                            {{ (string)$professorId === (string)$p->id ? 'selected' : '' }}>
                                                            👨‍🏫 {{ $p->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                {{-- BOTÓN DE GRUP --}}
                                                <button type="button"
                                                        class="btn btn-small btn-secondary open-grup-modal"
                                                        data-dia="{{ $diaNum }}"
                                                        data-franja="{{ $franja->id }}"
                                                        style="margin-top:4px;">
                                                    Assignar grup
                                                </button>

                                                <input type="hidden"
                                                       class="input-grup"
                                                       name="grups[{{ $diaNum }}][{{ $franja->id }}]"
                                                       value="{{ $grupId }}">

                                                <div class="grup-label" style="margin-top:4px; font-size:13px; color:#444;">
                                                    {{ $grupNom ? "Grup: $grupNom" : "Sense grup" }}
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

            {{-- MODAL GRUPS --}}
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

                /* Guardia amarillo */
                .horari-cell.guardia {
                    background-color: #ffeb3b;
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

                    buscador.addEventListener('input', function () {
                        filtrarGrups(this.value.toLowerCase());
                    });

                    function filtrarGrups(texto) {
                        document.querySelectorAll('.grup-option').forEach(btn => {
                            let nom = btn.dataset.grupNom.toLowerCase();
                            btn.style.display = nom.includes(texto) ? 'block' : 'none';
                        });
                    }

                    document.querySelectorAll('.grup-option').forEach(btn => {
                        btn.addEventListener('click', function () {
                            let id = this.dataset.grupId;
                            let nom = this.dataset.grupNom;
                            currentHiddenInput.value = id;
                            currentLabel.innerHTML = `Grup: <strong>${nom}</strong>`;
                            modal.style.display = 'none';
                        });
                    });

                    document.getElementById('tancarModalGrups').onclick =
                        () => modal.style.display = 'none';
                });
            </script>

        </div>
    </div>
</x-app-layout>