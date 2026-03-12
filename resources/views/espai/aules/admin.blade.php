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

            @if(session('ok'))
                <div class="modal-overlay">
                    <div class="modal-box">
                        <span>{{ session('ok') }}</span>
                    </div>
                </div>
            @endif

            @php
                $conflicts = session('conflicts');
                $hasConflicts = is_array($conflicts) && count($conflicts);
            @endphp

            @if($hasConflicts)
                <div class="conflict-backdrop" id="conflictModal">
                    <div class="conflict-card" role="dialog" aria-modal="true" aria-labelledby="conflictTitle">
                        <div class="conflict-head">
                            <h3 class="conflict-title" id="conflictTitle">Conflicte d’horari</h3>
                            <button type="button" class="btn btn-secondary" id="closeConflictModal">Tancar</button>
                        </div>

                        <div class="conflict-body">
                            <p class="conflict-text">
                                No s’ha pogut desar perquè el professor ja està assignat a una altra aula en el mateix moment.
                            </p>

                            <ul class="conflict-list">
                                @foreach($conflicts as $c)
                                    @php
                                        $profTxt = is_array($c) && isset($c['professor']) ? (string) $c['professor'] : '';
                                        $diaTxt = is_array($c) && isset($c['dia']) ? (string) $c['dia'] : '';
                                        $franjaTxt = is_array($c) && isset($c['franja']) ? (string) $c['franja'] : '';
                                        $aulaTxt = is_array($c) && isset($c['aula']) ? (string) $c['aula'] : '';
                                    @endphp

                                    <li>
                                        <span class="conflict-tag">{{ $profTxt }}</span>
                                        <span>{{ $diaTxt }}, {{ $franjaTxt }}</span>
                                        <span class="conflict-extra">Ja està a: <strong>{{ $aulaTxt }}</strong></span>
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
                    (function () {
                        var modal = document.getElementById('conflictModal');
                        var btn1 = document.getElementById('closeConflictModal');
                        var btn2 = document.getElementById('closeConflictModal2');

                        function closeModal() {
                            if (modal) modal.style.display = 'none';
                        }

                        if (btn1) btn1.addEventListener('click', closeModal);
                        if (btn2) btn2.addEventListener('click', closeModal);

                        if (modal) {
                            modal.addEventListener('click', function (e) {
                                if (e.target === modal) closeModal();
                            });
                        }

                        document.addEventListener('keydown', function (e) {
                            if (e.key === 'Escape') closeModal();
                        });
                    })();
                </script>
            @endif

            @if($franges->isEmpty())
                <div class="panel-card">
                    <div class="empty-state">
                        No hi ha franges horàries creades.
                    </div>
                </div>
            @else
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
                                                    <strong>{{ $franja->nom }}</strong>
                                                    <br>
                                                @endif
                                                {{ substr($franja->inici, 0, 5) }} - {{ substr($franja->fi, 0, 5) }}
                                            </td>

                                            @foreach($dies as $diaNum => $diaNom)
                                                @php
                                                    $valor = '';
                                                    if (isset($assignacions[$diaNum]) && isset($assignacions[$diaNum][$franja->id])) {
                                                        $valor = $assignacions[$diaNum][$franja->id];
                                                    }

                                                    $oldVal = old('assignacions.' . $diaNum . '.' . $franja->id);
                                                    if ($oldVal !== null) {
                                                        $valor = $oldVal;
                                                    }
                                                @endphp

                                                <td>
                                                    <select class="input-control select-control" name="assignacions[{{ $diaNum }}][{{ $franja->id }}]">
                                                        <option value="">-- lliure --</option>
                                                        @foreach($professors as $p)
                                                            <option value="{{ $p->id }}" {{ (string)$valor === (string)$p->id ? 'selected' : '' }}>
                                                                {{ $p->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
            @endif

            <div class="panel-card">
                <div class="section-header">
                    <h3 class="section-title">Tickets de l’aula</h3>
                </div>

                <form method="POST" action="{{ route('espai.aules.tickets.store', $aula) }}">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label for="titol">Títol</label>
                            <input id="titol" class="input-control" name="titol" value="{{ old('titol') }}" required>
                            @error('titol')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
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
                            @error('prioritat')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="descripcio">Descripció</label>
                        <textarea id="descripcio" class="input-control textarea-control" name="descripcio" rows="4">{{ old('descripcio') }}</textarea>
                        @error('descripcio')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit">Crear ticket</button>
                    </div>
                </form>

                <hr class="section-divider">

                @php
                    if (!isset($tickets) || $tickets === null) {
                        $tickets = collect();
                    }
                @endphp

                @if($tickets->isEmpty())
                    <div class="empty-state">
                        No hi ha tickets.
                    </div>
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
                                    @php
                                        $creadorNom = '-';
                                        if ($t->creador && isset($t->creador->nom) && $t->creador->nom !== '') {
                                            $creadorNom = $t->creador->nom;
                                        }
                                    @endphp

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
                                        <td>{{ $creadorNom }}</td>
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