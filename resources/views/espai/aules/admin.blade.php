<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Administrar aula: {{ $aula->nom }}</h2>
    </x-slot>
    @push('styles')
        @vite('resources/css/espai/aules/admin.css')
    @endpush

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
                <style>
                    .modal-backdrop {
                        position: fixed;
                        inset: 0;
                        background: rgba(0,0,0,.55);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 9999;
                    }
                    .modal-card {
                        width: min(720px, calc(100% - 24px));
                        background: #fff;
                        border-radius: 14px;
                        border: 1px solid rgba(0,0,0,.12);
                        overflow: hidden;
                        box-shadow: 0 18px 50px rgba(0,0,0,.22);
                    }
                    .modal-head {
                        padding: 14px 16px;
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                        border-bottom: 1px solid rgba(0,0,0,.08);
                    }
                    .modal-title {
                        margin:0;
                        font-size: 1.05rem;
                        font-weight: 800;
                    }
                    .modal-body { padding: 14px 16px; }
                    .modal-body p { margin-top:0; color: rgba(0,0,0,.7); }
                    .conf-list { margin: 0; padding-left: 18px; }
                    .conf-list li { margin: 8px 0; }
                    .conf-tag { color: #b42318; font-weight: 800; }
                    .modal-foot {
                        padding: 12px 16px;
                        border-top: 1px solid rgba(0,0,0,.08);
                        display:flex;
                        justify-content:flex-end;
                        gap:10px;
                    }
                    .btn-close-modal {
                        background:#111827;
                        color:#fff;
                        border:0;
                        padding:8px 12px;
                        border-radius:10px;
                        cursor:pointer;
                    }
                </style>

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
            {{-- ✅ FIN MODAL --}}

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

                        <table border="1" cellpadding="8" cellspacing="0" width="100%">
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
                                        <td>
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

                                                // si vuelve con withInput(), prioriza old()
                                                $oldVal = old('assignacions.' . $diaNum . '.' . $franja->id);
                                                if ($oldVal !== null) {
                                                    $valor = $oldVal;
                                                }
                                            @endphp

                                            <td>
                                                <select name="assignacions[{{ $diaNum }}][{{ $franja->id }}]">
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

                        <div style="margin-top:12px;">
                            <button class="btn btn-primary" type="submit">Desar horari</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="card" style="margin-top:16px;">
                <h3 style="margin-top:0;">Tickets de l’aula</h3>

                <form method="POST" action="{{ route('espai.aules.tickets.store', $aula) }}">
                    @csrf

                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <div style="flex:1; min-width:220px;">
                            <label>Títol</label>
                            <input name="titol" value="{{ old('titol') }}" required style="width:100%;">
                            @error('titol') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div style="width:180px;">
                            <label>Prioritat</label>
                            <select name="prioritat" style="width:100%;">
                                @foreach(['baixa'=>'Baixa','mitja'=>'Mitja','alta'=>'Alta'] as $k => $v)
                                    <option value="{{ $k }}" {{ old('prioritat','mitja')===$k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                            @error('prioritat') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="margin-top:10px;">
                        <label>Descripció</label>
                        <textarea name="descripcio" rows="3" style="width:100%;">{{ old('descripcio') }}</textarea>
                        @error('descripcio') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div style="margin-top:12px;">
                        <button class="btn btn-primary" type="submit">Crear ticket</button>
                    </div>
                </form>

                <hr style="margin:16px 0;">

                @php
                    if (!isset($tickets) || $tickets === null) {
                        $tickets = collect();
                    }
                @endphp

                @if($tickets->isEmpty())
                    <p style="margin:0;">No hi ha tickets.</p>
                @else
                    <table border="1" cellpadding="8" cellspacing="0" width="100%">
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
                                            <div style="margin-top:6px;">{{ $t->descripcio }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $t->prioritat }}</td>
                                    <td>{{ $t->estat }}</td>
                                    <td>{{ $creadorNom }}</td>
                                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                    <td style="white-space:nowrap;">
                                        <form method="POST" action="{{ route('espai.aules.tickets.update', [$aula, $t]) }}" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <select name="estat" onchange="this.form.submit()">
                                                @foreach(['obert'=>'Obert','en_proces'=>'En procés','tancat'=>'Tancat'] as $k => $v)
                                                    <option value="{{ $k }}" {{ $t->estat===$k ? 'selected' : '' }}>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                        </form>

                                        <form method="POST" action="{{ route('espai.aules.tickets.destroy', [$aula, $t]) }}" style="display:inline;" onsubmit="return confirm('Eliminar ticket?')">
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
