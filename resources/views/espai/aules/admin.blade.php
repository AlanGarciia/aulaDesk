@vite('resources/css/espai/aules/admin.css')

<x-app-layout>
    <div class="page">
        <div class="container">

            <div class="page-header">
                <h2 class="page-title">Administrar aula: {{ $aula->nom }}</h2>
                <div class="top-actions">
                    <a class="btn btn-secondary" href="{{ route('espai.aules.index') }}">Tornar</a>
                </div>
            </div>

            @php $conflicts = session('conflicts'); $hasConflicts = is_array($conflicts) && count($conflicts); @endphp
            @if($hasConflicts)
                <div class="conflict-backdrop" id="conflictModal" style="display:flex;">
                    <div class="conflict-card">
                        <div class="conflict-head">
                            <h3 class="conflict-title">⚠️ Conflicte d'horari</h3>
                        </div>
                        <div class="conflict-body">
                            <p>Els professors següents <strong>no s'han pogut assignar</strong> perquè ja estan en una altra aula en el mateix horari:</p>
                            <ul class="conflict-list">
                                @foreach($conflicts as $c)
                                    <li>
                                        <span class="conflict-tag">{{ $c['professor'] }}</span>
                                        <span>Dia {{ $c['dia'] }} — {{ $c['franja'] }}</span>
                                        <span class="conflict-extra">Ja està a: <strong>{{ $c['aula'] }}</strong></span>
                                    </li>
                                @endforeach
                            </ul>
                            <p style="margin-top:.75rem;font-size:.85rem;opacity:.7;">La resta de l'horari s'ha desat correctament.</p>
                        </div>
                        <div class="conflict-foot">
                            <button type="button" class="btn btn-primary" id="closeConflictModal">D'acord</button>
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('closeConflictModal').onclick =
                        () => document.getElementById('conflictModal').style.display = 'none';
                </script>
            @endif

            @if(session('ok'))
                <div class="alert-success" style="margin-bottom:1rem;padding:.75rem 1rem;border-radius:8px;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.4);color:#d1fae5;">
                    {{ session('ok') }}
                </div>
            @endif

            {{-- Horari --}}
            <div class="panel-card">
                <div class="section-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;">
                    <h3 class="section-title">Horari de l'aula</h3>

                    @php
                        $_espaiUserId = session('usuari_espai_id');
                        $_espaiUser   = $_espaiUserId ? \App\Models\UsuariEspai::find($_espaiUserId) : null;
                        $potEditar    = $_espaiUser && $_espaiUser->canEspai('aulas.horari.update');
                    @endphp

                    <button type="button"
                            class="btn btn-primary {{ $potEditar ? '' : 'btn-disabled' }}"
                            id="assignarGrupTot"
                            {{ $potEditar ? '' : 'disabled' }}
                            title="{{ $potEditar ? 'Assignar el mateix grup a totes les franges' : 'No tens permís per modificar l\'horari' }}">
                        <i class="bi bi-people-fill"></i> Assignar grup a tot l'horari
                    </button>
                </div>

                <form method="POST" action="{{ route('espai.aules.horari.update', $aula) }}">
                    @csrf
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Franja</th>
                                    @foreach($dies as $diaNom)<th>{{ $diaNom }}</th>@endforeach
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($franges as $franja)
                                <tr>
                                    <td class="franja-cell">
                                        @if($franja->nom)<strong>{{ $franja->nom }}</strong><br>@endif
                                        {{ substr($franja->inici,0,5) }} - {{ substr($franja->fi,0,5) }}
                                    </td>
                                    @foreach($dies as $diaNum => $diaNom)
                                        @php
                                            $professorId = $assignacions[$diaNum][$franja->id]['professor'] ?? '';
                                            $grupId      = $assignacions[$diaNum][$franja->id]['grup'] ?? null;
                                            $grupNom     = $grupId ? optional($grups->firstWhere('id',$grupId))->nom : null;
                                            $isGuardia   = isset($solSlots[$diaNum][$franja->id]);
                                        @endphp
                                        <td>
                                            <div class="horari-cell {{ $isGuardia ? 'guardia' : '' }}">

                                                <select class="input-control select-control"
                                                        name="assignacions[{{ $diaNum }}][{{ $franja->id }}]"
                                                        {{ $potEditar ? '' : 'disabled' }}
                                                        title="{{ $potEditar ? '' : 'No tens permís per modificar l\'horari' }}">
                                                    <option value="">-- lliure --</option>
                                                    @foreach($professors as $p)
                                                        <option value="{{ $p->id }}" {{ (string)$professorId === (string)$p->id ? 'selected' : '' }}>
                                                            👨‍🏫 {{ $p->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @unless($potEditar)
                                                    <input type="hidden"
                                                           name="assignacions[{{ $diaNum }}][{{ $franja->id }}]"
                                                           value="{{ $professorId }}">
                                                @endunless

                                                <button type="button"
                                                        class="btn btn-small btn-secondary open-grup-modal {{ $potEditar ? '' : 'btn-disabled' }}"
                                                        data-dia="{{ $diaNum }}"
                                                        data-franja="{{ $franja->id }}"
                                                        {{ $potEditar ? '' : 'disabled' }}
                                                        style="margin-top:4px;"
                                                        title="{{ $potEditar ? '' : 'No tens permís per modificar l\'horari' }}">
                                                    Assignar grup
                                                </button>

                                                <input type="hidden" class="input-grup"
                                                       name="grups[{{ $diaNum }}][{{ $franja->id }}]"
                                                       value="{{ $grupId }}">

                                                <div class="grup-label">
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
                        <button class="btn btn-primary @cantEspaiClass('aulas.horari.update')" type="submit">
                            Desar horari
                        </button>
                    </div>
                </form>
            </div>

            {{-- Modal --}}
            <div id="modalGrups" class="gm-backdrop" role="dialog" aria-modal="true" aria-labelledby="gmTitle">
                <div class="gm-card">
                    <div class="gm-header">
                        <div>
                            <h3 id="gmTitle">Selecciona un grup</h3>
                            <div class="gm-header__sub" id="gmSubtitle">Tria el grup d'alumnes per aquesta franja</div>
                        </div>
                        <button type="button" class="gm-close" id="tancarModalGrups" aria-label="Tancar">✕</button>
                    </div>

                    <div class="gm-search">
                        <span class="gm-search-ic"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscadorGrups" placeholder="Cerca grup..." autocomplete="off">
                    </div>

                    <div class="gm-list" id="llistaGrups">
                        @forelse($grups as $g)
                            <button type="button" class="gm-item grup-option"
                                    data-grup-id="{{ $g->id }}"
                                    data-grup-nom="{{ $g->nom }}">
                                <span class="gm-icon"><i class="bi bi-people-fill"></i></span>
                                <span>{{ $g->nom }}</span>
                            </button>
                        @empty
                            <div class="gm-empty">No hi ha grups creats encara.</div>
                        @endforelse
                        <div class="gm-empty" id="gmNoResults" style="display:none;">
                            Cap grup coincideix amb la cerca.
                        </div>
                    </div>

                    <div class="gm-foot">
                        <button type="button" class="btn btn-secondary" id="tancarModalGrupsFoot">Tancar</button>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal      = document.getElementById('modalGrups');
                const buscador   = document.getElementById('buscadorGrups');
                const noResults  = document.getElementById('gmNoResults');
                const closeBtn1  = document.getElementById('tancarModalGrups');
                const closeBtn2  = document.getElementById('tancarModalGrupsFoot');
                const subtitle   = document.getElementById('gmSubtitle');
                const btnGlobal  = document.getElementById('assignarGrupTot');

                let modeGlobal = false;
                let currentHiddenInput = null;
                let currentLabel = null;

                function obrirModal() {
                    modal.classList.add('is-open');
                    setTimeout(() => buscador.focus(), 50);
                }
                function tancarModal() {
                    modal.classList.remove('is-open');
                }

                // Botó global
                if (btnGlobal) {
                    btnGlobal.addEventListener('click', function () {
                        modeGlobal = true;
                        currentHiddenInput = null;
                        currentLabel = null;
                        subtitle.textContent = "El grup s'aplicarà a totes les franges de la setmana";
                        buscador.value = '';
                        filtrarGrups('');
                        obrirModal();
                    });
                }

                // Botons individuals de cada cel·la
                document.querySelectorAll('.open-grup-modal:not([disabled])').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modeGlobal = false;
                        const dia = this.dataset.dia;
                        const franja = this.dataset.franja;
                        currentHiddenInput = document.querySelector(`input[name="grups[${dia}][${franja}]"]`);
                        currentLabel = this.parentElement.querySelector('.grup-label');
                        subtitle.textContent = "Tria el grup d'alumnes per aquesta franja";
                        buscador.value = '';
                        filtrarGrups('');
                        obrirModal();
                    });
                });

                buscador.addEventListener('input', () => filtrarGrups(buscador.value.toLowerCase()));

                function filtrarGrups(t) {
                    let visibles = 0;
                    document.querySelectorAll('.grup-option').forEach(b => {
                        const match = b.dataset.grupNom.toLowerCase().includes(t);
                        b.style.display = match ? 'flex' : 'none';
                        if (match) visibles++;
                    });
                    noResults.style.display = visibles === 0 ? 'block' : 'none';
                }

                // Selecció d'un grup
                document.querySelectorAll('.grup-option').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const grupId  = this.dataset.grupId;
                        const grupNom = this.dataset.grupNom;

                        if (modeGlobal) {
                            // Aplica el grup a totes les cel·les
                            document.querySelectorAll('input.input-grup').forEach(input => {
                                input.value = grupId;
                            });
                            document.querySelectorAll('.grup-label').forEach(label => {
                                label.innerHTML = `Grup: <strong>${grupNom}</strong>`;
                            });
                        } else {
                            if (currentHiddenInput) currentHiddenInput.value = grupId;
                            if (currentLabel) currentLabel.innerHTML = `Grup: <strong>${grupNom}</strong>`;
                        }

                        tancarModal();
                    });
                });

                closeBtn1.addEventListener('click', tancarModal);
                closeBtn2.addEventListener('click', tancarModal);

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && modal.classList.contains('is-open')) tancarModal();
                });

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) tancarModal();
                });
            });
            </script>

        </div>
    </div>
</x-app-layout>