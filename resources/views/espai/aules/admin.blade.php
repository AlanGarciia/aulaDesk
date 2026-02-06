{{-- resources/views/espai/aules/admin.blade.php --}}

@vite('resources/css/espai/aules/admin.css')

<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Administrar aula: {{ $aula->nom }}</h2>
    </x-slot>

    <div class="page">
        <div class="container">

            <p>
                <a class="btn" href="{{ route('espai.aules.index') }}">Tornar</a>
            </p>

            @if(session('ok'))
                <div class="alert success">{{ session('ok') }}</div>
            @endif

            @php
                $conflicts = session('conflicts');
                $hasConflicts = false;

                if (is_array($conflicts) && count($conflicts)) {
                    $hasConflicts = true;
                }
            @endphp

            @if($hasConflicts)
                <div class="modal-backdrop" id="conflictModal">
                    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="conflictTitle">
                        <div class="modal-head">
                            <h3 class="modal-title" id="conflictTitle">Conflicte d’horari</h3>
                            <button type="button" class="btn-close-modal" id="closeConflictModal">Tancar</button>
                        </div>

                        <div class="modal-body">
                            <p>
                                No s’ha pogut desar perquè el professor ja està assignat a una altra aula en el mateix moment:
                            </p>

                            <ul class="conf-list">
                                @foreach($conflicts as $c)
                                    @php
                                        $profTxt = '';
                                        $diaTxt = '';
                                        $franjaTxt = '';
                                        $aulaTxt = '';

                                        if (is_array($c)) {
                                            if (isset($c['professor'])) $profTxt = (string) $c['professor'];
                                            if (isset($c['dia'])) $diaTxt = (string) $c['dia'];
                                            if (isset($c['franja'])) $franjaTxt = (string) $c['franja'];
                                            if (isset($c['aula'])) $aulaTxt = (string) $c['aula'];
                                        }
                                    @endphp

                                    <li>
                                        <span class="conf-tag">{{ $profTxt }}</span>
                                        — {{ $diaTxt }}, {{ $franjaTxt }}
                                        (ja està a: <strong>{{ $aulaTxt }}</strong>)
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="modal-foot">
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
                <div class="card">
                    <div class="alert" style="margin:0;">
                        No hi ha franges horàries creades.
                    </div>
                </div>
            @else
                <div class="card">
                    <form method="POST" action="{{ route('espai.aules.admin.update', $aula) }}">
                        @csrf

                        <table class="table" width="100%">
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
                                        <td class="cell-franja">
                                            @if($franja->nom)
                                                <strong>{{ $franja->nom }}</strong><br>
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
                                                <select class="select" name="assignacions[{{ $diaNum }}][{{ $franja->id }}]">
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

                        <div class="actions">
                            <button class="btn btn-primary" type="submit">Desar horari</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="card card-tickets">
                <h3 class="card-title">Tickets de l’aula</h3>

                <form method="POST" action="{{ route('espai.aules.tickets.store', $aula) }}">
                    @csrf

                    <div class="form-row">
                        <div class="field">
                            <label>Títol</label>
                            <input class="input" name="titol" value="{{ old('titol') }}" required>
                            @error('titol') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field field-small">
                            <label>Prioritat</label>
                            <select class="select" name="prioritat">
                                @foreach(['baixa'=>'Baixa','mitja'=>'Mitja','alta'=>'Alta'] as $k => $v)
                                    <option value="{{ $k }}" {{ old('prioritat','mitja')===$k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                            @error('prioritat') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label>Descripció</label>
                        <textarea class="textarea" name="descripcio" rows="3">{{ old('descripcio') }}</textarea>
                        @error('descripcio') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="actions">
                        <button class="btn btn-primary" type="submit">Crear ticket</button>
                    </div>
                </form>

                <hr class="divider">

                @php
                    if (!isset($tickets) || $tickets === null) {
                        $tickets = collect();
                    }
                @endphp

                @if($tickets->isEmpty())
                    <p class="muted">No hi ha tickets.</p>
                @else
                    <table class="table" width="100%">
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
                                    <td class="nowrap">
                                        <form method="POST" action="{{ route('espai.aules.tickets.update', [$aula, $t]) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <select class="select select-small" name="estat" onchange="this.form.submit()">
                                                @foreach(['obert'=>'Obert','en_proces'=>'En procés','tancat'=>'Tancat'] as $k => $v)
                                                    <option value="{{ $k }}" {{ $t->estat===$k ? 'selected' : '' }}>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                        </form>

                                        <form method="POST" action="{{ route('espai.aules.tickets.destroy', [$aula, $t]) }}" class="inline" onsubmit="return confirm('Eliminar ticket?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn" type="submit">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
