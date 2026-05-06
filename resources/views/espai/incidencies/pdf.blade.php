<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Llista d'incidències</title>
    <style>
        @page { margin: 24px 28px; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1c1c1e;
            margin: 0;
        }
        .pdf-head {
            background: #0d47a1;
            color: #fff;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 14px;
        }
        .pdf-head h1 { margin: 0 0 3px; font-size: 17px; font-weight: 700; }
        .pdf-head .meta {
            font-size: 10px; opacity: .92;
            margin-top: 4px;
        }
        .pdf-head .meta span { margin-right: 14px; }

        h2.section {
            font-size: 12px; text-transform: uppercase;
            letter-spacing: .5px; color: #0d47a1;
            margin: 18px 0 6px; padding-bottom: 3px;
            border-bottom: 1.5px solid #0d47a1;
        }

        table { width: 100%; border-collapse: collapse; }
        th, td {
            border: 1px solid #d1d5db;
            padding: 5px 7px;
            text-align: left;
            font-size: 10.5px;
        }
        thead th {
            background: #f1f5f9;
            color: #0d47a1;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: .4px;
        }
        tbody tr:nth-child(even) td { background: #fafafa; }

        td.num { text-align: center; width: 38px; font-weight: 700; }
        td.tot { background: #eef2ff !important; color: #1e3a8a; font-weight: 800; }

        .day-block { margin-bottom: 10px; }
        .day-title {
            background: #e0e7ff;
            color: #1e3a8a;
            padding: 5px 10px;
            font-weight: 700;
            font-size: 11px;
            border-radius: 4px;
            margin-bottom: 4px;
        }
        .day-block ul { margin: 0; padding-left: 18px; }
        .day-block li { margin: 2px 0; }
        .tag-mini {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 8px;
            margin-right: 3px;
        }
        .tag-assistencia { background: #fee2e2; color: #b91c1c; }
        .tag-deures      { background: #fef3c7; color: #92400e; }
        .tag-material    { background: #e0e7ff; color: #3730a3; }
        .tag-amonestacio { background: #fce7f3; color: #9d174d; }

        .empty {
            padding: 14px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
            font-size: 11px;
        }

        .footer {
            margin-top: 18px;
            font-size: 9px; color: #9ca3af;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="pdf-head">
    <h1>{{ $aulaHorari->grup->nom ?? 'Grup' }}</h1>
    <div class="meta">
        <span>Aula: <strong>{{ $aulaHorari->aula->nom ?? '—' }}</strong></span>
        <span>Hora: <strong>{{ substr($aulaHorari->franja->inici ?? '', 0, 5) }} – {{ substr($aulaHorari->franja->fi ?? '', 0, 5) }}</strong></span>
        <span>Període: <strong>{{ $from->format('d/m/Y') }} – {{ $to->format('d/m/Y') }}</strong></span>
        <span>Alumnes: <strong>{{ $alumnes->count() }}</strong></span>
    </div>
</div>

<h2 class="section">Resum per alumne</h2>

@if($alumnes->isEmpty())
    <div class="empty">Aquest grup encara no té cap alumne.</div>
@else
    <table>
        <thead>
            <tr>
                <th>Alumne</th>
                <th style="width:40px; text-align:center;">Ass</th>
                <th style="width:40px; text-align:center;">Deu</th>
                <th style="width:40px; text-align:center;">Mat</th>
                <th style="width:40px; text-align:center;">Amo</th>
                <th style="width:50px; text-align:center;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumnes as $a)
                @php
                    $r = $resumIdx[$a->id] ?? ['assistencia'=>0,'deures'=>0,'material'=>0,'amonestacio'=>0];
                    $tot = $r['assistencia'] + $r['deures'] + $r['material'] + $r['amonestacio'];
                @endphp
                <tr>
                    <td>{{ trim(($a->cognoms ?? '') . ', ' . ($a->nom ?? '')) }}</td>
                    <td class="num">{{ $r['assistencia'] ?: '–' }}</td>
                    <td class="num">{{ $r['deures'] ?: '–' }}</td>
                    <td class="num">{{ $r['material'] ?: '–' }}</td>
                    <td class="num">{{ $r['amonestacio'] ?: '–' }}</td>
                    <td class="num tot">{{ $tot }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<h2 class="section">Detall per dia</h2>

@if($perDia->isEmpty())
    <div class="empty">No hi ha cap incidència registrada en aquest període.</div>
@else
    @foreach($perDia as $diaStr => $incs)
        @php
            $diaCarbon = \Carbon\Carbon::parse($diaStr);
        @endphp
        <div class="day-block">
            <div class="day-title">
                {{ ucfirst($diaCarbon->isoFormat('dddd D MMMM YYYY')) }}
                <span style="font-weight:400; opacity:.85; margin-left:6px;">({{ $incs->count() }} incidències)</span>
            </div>
            <ul>
                @foreach($incs as $inc)
                    @php
                        $alumneNom = optional($inc->alumne)->cognoms . ', ' . optional($inc->alumne)->nom;
                    @endphp
                    <li>
                        <span class="tag-mini tag-{{ $inc->tipus }}">{{ $tipusLabels[$inc->tipus] ?? $inc->tipus }}</span>
                        <strong>{{ trim($alumneNom, ', ') }}</strong>
                        @if($inc->observacions)
                            — <em>{{ $inc->observacions }}</em>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
@endif

<div class="footer">
    Generat el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} · AulaDesk
</div>

</body>
</html>