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
                            Aquest és el teu horari segons les hores assignades a les aules.
                        </p>
                    </div>

                    <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                        <i class="bi bi-box-arrow-right me-1"></i> Tornar a l'espai
                    </a>

                </div>

                @php
                    $hasData = isset($franjes) && $franjes->count() && isset($dies) && count($dies);
                @endphp

                @if(!$hasData)
                    <div class="p-3">
                        <div class="alert alert-info mb-0">
                            Encara no hi ha franjes o dies configurats per mostrar l’horari.
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="timetable">
                            <thead>
                                <tr>
                                    <th style="width: 1%;">Hora</th>
                                    @foreach($dies as $dia)
                                        <th>{{ $diesLabels[$dia] ?? 'Dia ' . $dia }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($franjes as $franja)
                                    <tr>
                                        <td class="timecell">{{ $franja->inici }} - {{ $franja->fi }}</td>

                                        @foreach($dies as $dia)
                                            @php
                                                $cell = $slots[$dia][$franja->id] ?? null;
                                                $sol = $solSlots[$dia][$franja->id] ?? null;

                                                $solEsMeva = $sol['es_meva'] ?? false;
                                                $solSocCobridor = $sol['soc_cobridor'] ?? false;

                                                $amagarBoto = $sol && ($solEsMeva || $solSocCobridor);
                                                $aulaNom = $cell['aula'] ?? 'Aula';
                                            @endphp

                                            <td>
                                                @if(!$cell)
                                                    <span class="slot-empty">—</span>
                                                @else
                                                    <div class="slot">
                                                        <div class="slot-row">
                                                            <div class="slot-aula">
                                                                <i class="bi bi-door-open"></i> {{ $aulaNom }}
                                                            </div>

                                                            @unless($amagarBoto)
                                                                <a class="btn-guardia"
                                                                   href="{{ route('espai.guardia.solicitaGuardia', ['dia' => $dia, 'franja' => $franja->id]) }}">
                                                                    Solicitar guàrdia
                                                                </a>
                                                            @endunless
                                                        </div>

                                                        @if($solEsMeva)
                                                            <span class="badge-guardia badge-guardia--{{ $sol['estat'] ?? 'pendent' }}">
                                                                Guàrdia {{ $sol['estat'] ?? 'pendent' }}
                                                            </span>
                                                        @endif

                                                        @if($solSocCobridor)
                                                            <span class="badge-guardia badge-guardia--cobridor">
                                                                Cobreixes tu
                                                            </span>
                                                        @endif

                                                        @if(!empty($cell['meta']))
                                                            <div class="slot-meta">{{ $cell['meta'] }}</div>
                                                        @endif
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