@push('styles')
    @vite('resources/css/espai/guardies/guardia.css')
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="page">
            <div class="container">
                <h2 class="page-title">Guardies</h2>
            </div>
        </div>
    </x-slot>

    <div class="page">
        <div class="container">
            <div class="guardies-card">
                <div class="guardies-head">
                     <div>
                        @php
                            $nomUsuari = '';
                            if (isset($usuariEspai) && isset($usuariEspai->nom)) {
                                $nomUsuari = (string) $usuariEspai->nom;
                            } elseif (session()->has('usuari_espai_nom')) {
                                $nomUsuari = (string) session('usuari_espai_nom');
                            }
                            $salutacio = $nomUsuari !== '' ? 'Hola ' . $nomUsuari . '!' : 'Hola!';
                        @endphp

                        <h3 class="guardies-title">
                            {{ $salutacio }} aquest és el teu horari de la setmana:
                        </h3>

                        <p class="guardies-sub">
                            Clica "Llista" per passar llista, o descarrega un informe.
                        </p>
                    </div>

                    @php
                        $weekStart = \Carbon\Carbon::today()->startOfWeek()->toDateString();
                        $weekEnd = \Carbon\Carbon::today()->startOfWeek()->addDays(4)->toDateString();
                        $monthStart = \Carbon\Carbon::today()->startOfMonth()->toDateString();
                        $monthEnd = \Carbon\Carbon::today()->endOfMonth()->toDateString();
                    @endphp

                    <div class="guardies-actions">
                        <a href="{{ route('espai.incidencies.globalPdf', ['from' => $weekStart, 'to' => $weekEnd, 'tipus' => 'setmanal']) }}"
                        class="btn-informe btn-informe--week" target="_blank">
                            <i class="bi bi-calendar-week"></i> Informe setmanal
                        </a>

                        <a href="{{ route('espai.incidencies.globalPdf', ['from' => $monthStart, 'to' => $monthEnd, 'tipus' => 'mensual']) }}"
                        class="btn-informe btn-informe--month" target="_blank">
                            <i class="bi bi-calendar-month"></i> Informe mensual
                        </a>

                        <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                            <i class="bi bi-box-arrow-right me-1"></i> Tornar
                        </a>
                    </div>
                </div>

                @php
                    $hasData = isset($franjes) && $franjes->count() && isset($dies) && count($dies);
                    $weekStart = \Carbon\Carbon::today()->startOfWeek()->toDateString();
                    $weekEnd = \Carbon\Carbon::today()->startOfWeek()->addDays(4)->toDateString();
                @endphp

                @if(!$hasData)
                    <div class="p-3">
                        <div class="alert alert-info mb-0">
                            Encara no hi ha franjes o dies configurats per mostrar l'horari.
                        </div>
                    </div>
                @else
                    <div class="timetable-wrap">
                        <table class="timetable">
                            <thead>
                                <tr>
                                    <th class="th-hora">Hora</th>
                                    @foreach($dies as $dia)
                                        <th>{{ $diesLabels[$dia] ?? 'Dia ' . $dia }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($franjes as $franja)
                                    <tr>
                                        <td class="timecell">
                                            <div class="timecell-inner">
                                                <span class="timecell-inici">{{ substr($franja->inici, 0, 5) }}</span>
                                                <span class="timecell-sep">–</span>
                                                <span class="timecell-fi">{{ substr($franja->fi, 0, 5) }}</span>
                                            </div>
                                        </td>

                                        @foreach($dies as $dia)
                                            @php
                                                $cell = $slots[$dia][$franja->id] ?? null;
                                                $sol = $solSlots[$dia][$franja->id] ?? null;

                                                $solEsMeva = $sol['es_meva'] ?? false;
                                                $solSocCobridor = $sol['soc_cobridor'] ?? false;

                                                $aulaNom = $cell['aula'] ?? '';
                                                $horariId = $cell['horari_id'] ?? 0;
                                                $estatSol = $sol['estat'] ?? 'pendent';
                                            @endphp

                                            <td class="daycell">
                                                @if(!$cell)
                                                    <div class="slot slot--empty">
                                                        <span>—</span>
                                                    </div>
                                                @else
                                                    <div class="slot">
                                                        <div class="slot-aula" title="{{ $aulaNom }}">
                                                            <i class="bi bi-door-open"></i>
                                                            <span>{{ $aulaNom }}</span>
                                                        </div>

                                                        <div class="slot-actions">
                                                            @if($horariId)
                                                                <a class="btn-llista"
                                                                   href="{{ route('espai.incidencies.index', ['aulaHorari' => $horariId]) }}"
                                                                   title="Passar llista">
                                                                    <i class="bi bi-list-check"></i> Llista
                                                                </a>
                                                            @endif

                                                            @if($solEsMeva)
                                                                <span class="badge-guardia badge-guardia--{{ $estatSol }}">
                                                                    {{ ucfirst($estatSol) }}
                                                                </span>
                                                            @elseif($solSocCobridor)
                                                                <span class="badge-guardia badge-guardia--cobridor">
                                                                    <i class="bi bi-shield-check"></i> Cobr.
                                                                </span>
                                                            @else
                                                                <a class="btn-guardia"
                                                                   href="{{ route('espai.guardia.solicitaGuardia', ['dia' => $dia, 'franja' => $franja->id]) }}"
                                                                   title="Sol·licitar guàrdia">
                                                                    <i class="bi bi-plus-circle"></i> Guàrdia
                                                                </a>
                                                            @endif

                                                            @if($horariId)
                                                                <a class="btn-pdf-mini"
                                                                   href="{{ route('espai.incidencies.pdf', ['aulaHorari' => $horariId, 'from' => $weekStart, 'to' => $weekEnd]) }}"
                                                                   title="PDF setmana"
                                                                   target="_blank">
                                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
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