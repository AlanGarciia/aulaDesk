@push('styles')
<style>
    .guardies-wrap { max-width: 1100px; margin: 0 auto; }
    .guardies-card { background: #fff; border: 1px solid rgba(0,0,0,.08); border-radius: 14px; overflow: hidden; }
    .guardies-head { padding: 18px 18px; border-bottom: 1px solid rgba(0,0,0,.06); display:flex; gap:12px; align-items:center; justify-content:space-between; }
    .guardies-title { margin:0; font-size: 1.25rem; font-weight: 700; }
    .guardies-sub { margin:0; color: rgba(0,0,0,.6); font-size: .95rem; }

    .timetable { width:100%; border-collapse: separate; border-spacing:0; }
    .timetable th, .timetable td { border-bottom: 1px solid rgba(0,0,0,.06); border-right: 1px solid rgba(0,0,0,.06); padding: 10px 10px; vertical-align: middle; }
    .timetable th:first-child, .timetable td:first-child { border-left: 1px solid rgba(0,0,0,.06); }
    .timetable thead th { background: rgba(0,0,0,.03); font-weight: 700; font-size: .9rem; }
    .timetable thead th:first-child { border-top-left-radius: 12px; }
    .timetable thead th:last-child { border-top-right-radius: 12px; }

    .timecell { white-space: nowrap; font-weight: 600; font-size: .9rem; color: rgba(0,0,0,.75); background: rgba(0,0,0,.02); }
    .slot-empty { color: rgba(0,0,0,.35); font-size: .9rem; }
    .slot { display:flex; flex-direction: column; gap:6px; }
    .slot-aula { font-weight: 700; }

    .slot-row { display:flex; align-items:center; justify-content:space-between; gap:10px; }

    .btn-guardia {
        padding: 6px 10px;
        border-radius: 10px;
        font-size: .85rem;
        font-weight: 700;
        border: 1px solid rgba(0,0,0,.12);
        background: rgba(0,0,0,.03);
        text-decoration:none;
        color: rgba(0,0,0,.85);
        white-space: nowrap;
    }
    .btn-guardia:hover { background: rgba(0,0,0,.06); }

    .slot-meta { font-size: .85rem; color: rgba(0,0,0,.6); }

    .badge-guardia {
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 800;
        border: 1px solid rgba(0,0,0,.10);
        width: fit-content;
    }
    .badge-guardia--pendent {
        background: rgba(245,158,11,.14);
        color: #92400e;
        border-color: rgba(245,158,11,.28);
    }
    .badge-guardia--acceptada {
        background: rgba(16,185,129,.14);
        color: #065f46;
        border-color: rgba(16,185,129,.28);
    }
    .badge-guardia--cobridor {
        background: rgba(59,130,246,.12);
        color: #1e3a8a;
        border-color: rgba(59,130,246,.26);
    }

    .legend { display:flex; gap:10px; flex-wrap:wrap; padding: 12px 18px; background: rgba(0,0,0,.015); border-top: 1px solid rgba(0,0,0,.06); }
    .badge-soft { display:inline-flex; align-items:center; gap:6px; padding: 6px 10px; border-radius: 999px; font-size: .85rem; border: 1px solid rgba(0,0,0,.08); background: #fff; }
    .dot { width:10px; height:10px; border-radius: 50%; background: rgba(0,0,0,.25); display:inline-block; }

    @media (max-width: 768px) {
        .timetable th, .timetable td { padding: 8px; }
        .guardies-head { flex-direction: column; align-items: flex-start; }
        .slot-row { flex-direction: column; align-items:flex-start; }
        .btn-guardia { width: 100%; text-align: center; }
    }
</style>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="guardies-wrap">
            <h2 class="page-title" style="margin:0;">Guardies</h2>
        </div>
    </x-slot>

    <div class="guardies-wrap py-3">
        <div class="guardies-card">
            <div class="guardies-head">
                <div>
                    @php
                        $nomUsuari = '';
                        if (isset($usuariEspai) && isset($usuariEspai->nom)) {
                            $nomUsuari = (string) $usuariEspai->nom;
                        } else {
                            if (session()->has('usuari_espai_nom')) {
                                $nomUsuari = (string) session('usuari_espai_nom');
                            }
                        }

                        $salutacio = 'Hola!';
                        if ($nomUsuari !== '') {
                            $salutacio = 'Hola ' . $nomUsuari . '!';
                        }
                    @endphp

                    <h3 class="guardies-title">
                        {{ $salutacio }} aquest es el teu horari de la setmana:
                    </h3>

                    <p class="guardies-sub">
                        Aquest és el teu horari segons les hores assignades a les aules.
                    </p>
                </div>

                <a href="{{ route('espai.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Tornar a l'Espai
                </a>
            </div>

            @php
                $hasData = false;
                if (isset($franjes) && $franjes->count()) {
                    if (isset($dies) && count($dies)) {
                        $hasData = true;
                    }
                }
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
                                    @php
                                        $label = 'Dia ' . $dia;
                                        if (isset($diesLabels) && isset($diesLabels[$dia])) {
                                            $label = $diesLabels[$dia];
                                        }
                                    @endphp
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($franjes as $franja)
                                <tr>
                                    <td class="timecell">
                                        <div>{{ $franja->inici }} - {{ $franja->fi }}</div>
                                    </td>

                                    @foreach($dies as $dia)
                                        @php
                                            $cell = null;
                                            if (isset($slots) && isset($slots[$dia]) && isset($slots[$dia][$franja->id])) {
                                                $cell = $slots[$dia][$franja->id];
                                            }

                                            $sol = null;
                                            if (isset($solSlots) && isset($solSlots[$dia]) && isset($solSlots[$dia][$franja->id])) {
                                                $sol = $solSlots[$dia][$franja->id];
                                            }

                                            $solEstat = '';
                                            $solEsMeva = false;
                                            $solSocCobridor = false;

                                            if (is_array($sol)) {
                                                if (isset($sol['estat']) && $sol['estat'] !== '') $solEstat = (string) $sol['estat'];
                                                if (isset($sol['es_meva'])) $solEsMeva = (bool) $sol['es_meva'];
                                                if (isset($sol['soc_cobridor'])) $solSocCobridor = (bool) $sol['soc_cobridor'];
                                            }

                                            $amagarBoto = false;
                                            if ($sol && ($solEsMeva || $solSocCobridor)) {
                                                $amagarBoto = true;
                                            }
                                        @endphp

                                        <td>
                                            @if(!$cell)
                                                <span class="slot-empty">—</span>
                                            @else
                                                @php
                                                    $aulaNom = 'Aula';
                                                    if (isset($cell['aula']) && $cell['aula'] !== '') {
                                                        $aulaNom = (string) $cell['aula'];
                                                    }
                                                @endphp

                                                <div class="slot">
                                                    <div class="slot-row">
                                                        <div class="slot-aula">
                                                            <i class="bi bi-door-open"></i>
                                                            {{ $aulaNom }}
                                                        </div>

                                                        @if(!$amagarBoto)
                                                            <a class="btn-guardia"
                                                               href="{{ route('espai.guardia.solicitaGuardia', ['dia' => $dia, 'franja' => $franja->id]) }}">
                                                                Solicitar guàrdia
                                                            </a>
                                                        @endif
                                                    </div>

                                                    {{-- ✅ Overlay guardia --}}
                                                    @if($sol && $solEsMeva)
                                                        @if($solEstat === 'pendent')
                                                            <span class="badge-guardia badge-guardia--pendent">Guàrdia pendent</span>
                                                        @elseif($solEstat === 'acceptada')
                                                            <span class="badge-guardia badge-guardia--acceptada">Guàrdia acceptada</span>
                                                        @else
                                                            <span class="badge-guardia badge-guardia--pendent">Guàrdia: {{ $solEstat }}</span>
                                                        @endif
                                                    @endif

                                                    @if($sol && $solSocCobridor)
                                                        <span class="badge-guardia badge-guardia--cobridor">Cobreixes tu</span>
                                                    @endif

                                                    @if(isset($cell['meta']) && $cell['meta'])
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

                <div class="legend">
                    <span class="badge-soft"><span class="dot"></span> Cela buida = sense aula assignada</span>
                    <span class="badge-soft"><i class="bi bi-door-open"></i> Aula assignada en aquella franja</span>
                    <span class="badge-soft">🟠 Guàrdia pendent</span>
                    <span class="badge-soft">🟢 Guàrdia acceptada</span>
                    <span class="badge-soft">🔵 Cobreixes tu</span>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
