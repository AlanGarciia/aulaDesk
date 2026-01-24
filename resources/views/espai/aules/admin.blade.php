<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Administrar aula: {{ $aula->nom }}</h2>
    </x-slot>

    <div class="page">
        <div class="container">

            <p>
                <a class="btn" href="{{ route('espai.aules.index') }}">← Tornar</a>
            </p>

            @if(session('ok'))
                <div class="alert success">{{ session('ok') }}</div>
            @endif

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

        </div>
    </div>
</x-app-layout>
