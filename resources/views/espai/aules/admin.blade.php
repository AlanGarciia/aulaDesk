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

                @php($tickets = $tickets ?? collect())

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
                                    <td>{{ $t->creador?->nom ?? '-' }}</td>
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
