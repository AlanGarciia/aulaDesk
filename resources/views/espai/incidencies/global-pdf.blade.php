<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Informe d'incidències</title>
    <style>
        @page { margin: 22px 26px; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
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
            font-size: 10px; opacity: .92; margin-top: 4px;
        }
        .pdf-head .meta span { margin-right: 14px; }

        .group-block {
            page-break-inside: avoid;
            margin-bottom: 18px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        .group-block.empty { background: #fafafa; }

        .group-title {
            margin: 0 0 6px;
            font-size: 13px;
            font-weight: 700;
            color: #0d47a1;
            border-bottom: 1.5px solid #e0e7ff;
            padding-bottom: 4px;
        }
        .group-meta {
            font-size: 9.5px;
            color: #6b7280;
            margin-bottom: 7px;
        }
        .group-meta span { margin-right: 12px; }

        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th, td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
            text-align: left;
            font-size: 9.5px;
        }
        thead th {
            background: #f1f5f9;
            color: #0d47a1;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8.5px;
            letter-spacing: .3px;
        }
        td.num { text-align: center; width: 32px; font-weight: 700; }
        td.tot { background: #eef2ff !important; color: #1e3a8a; font-weight: 800; }

        .empty-msg {
            color: #9ca3af; font-style: italic; font-size: 10px; padding: 6px 0;
        }

        .footer {
            margin-top: 14px;
            font-size: 8.5px;
            color: #9ca3af;
            text-align: right;
        }

        .totals-row {
            background: #fef3c7 !important;
        }
    </style>
</head>
<body>

<div class="pdf-head">
    <h1>Informe {{ $tipus === 'mensual' ? 'mensual' : 'setmanal' }} d'incidències</h1>
    <div class="meta">
        <span>Període: <strong>{{ $from->format('d/m/Y') }} – {{ $to->format('d/m/Y') }}</strong></span>
        <span>Grups: <strong>{{ count($horariData) }}</strong></span>
    </div>
</div>

@if(empty($horariData))
    <div class="empty-msg" style="text-align:center; padding:30px 0;">
        No tens cap hora amb grup assignat per generar l'informe.
    </div>
@else
    @foreach($horariData as $hd)
        @php
            $h = $hd['horari'];
            $incs = $hd['incidencies'];
            $resum = $hd['resumIdx'];
            $diesLabels = [1=>'Dilluns', 2=>'Dimarts', 3=>'Dimecres', 4=>'Dijous', 5=>'Divendres'];
            $diaTxt = $diesLabels[(int) $h->dia_setmana] ?? '';
            $totalGroup = collect($resum)->sum(fn ($r) => array_sum($r));
        @endphp

        <div class="group-block {{ $totalGroup === 0 ? 'empty' : '' }}">
            <div class="group-title">{{ $h->grup->nom ?? 'Grup' }}</div>
            <div class="group-meta">
                <span>📅 {{ $diaTxt }}</span>
                <span>🕐 {{ substr($h->franja->inici ?? '', 0, 5) }}–{{ substr($h->franja->fi ?? '', 0, 5) }}</span>
                <span>🚪 {{ $h->aula->nom ?? '—' }}</span>
                <span>👥 {{ $h->grup->alumnes->count() }} alumnes</span>
                <span>📊 {{ $totalGroup }} incidències</span>
            </div>

            @if($h->grup->alumnes->isEmpty())
                <div class="empty-msg">Sense alumnes assignats al grup.</div>
            @elseif($totalGroup === 0)
                <div class="empty-msg">Cap incidència registrada en aquest període.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Alumne</th>
                            <th style="width:32px; text-align:center;">Ass</th>
                            <th style="width:32px; text-align:center;">Deu</th>
                            <th style="width:32px; text-align:center;">Mat</th>
                            <th style="width:32px; text-align:center;">Amo</th>
                            <th style="width:40px; text-align:center;">Tot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totals = ['assistencia'=>0,'deures'=>0,'material'=>0,'amonestacio'=>0]; @endphp
                        @foreach($h->grup->alumnes as $a)
                            @php
                                $r = $resum[$a->id] ?? ['assistencia'=>0,'deures'=>0,'material'=>0,'amonestacio'=>0];
                                $tot = $r['assistencia'] + $r['deures'] + $r['material'] + $r['amonestacio'];
                                foreach ($r as $k => $v) $totals[$k] += $v;
                            @endphp
                            @if($tot > 0)
                                <tr>
                                    <td>{{ trim(($a->cognoms ?? '') . ', ' . ($a->nom ?? '')) }}</td>
                                    <td class="num">{{ $r['assistencia'] ?: '–' }}</td>
                                    <td class="num">{{ $r['deures'] ?: '–' }}</td>
                                    <td class="num">{{ $r['material'] ?: '–' }}</td>
                                    <td class="num">{{ $r['amonestacio'] ?: '–' }}</td>
                                    <td class="num tot">{{ $tot }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="totals-row">
                            <td><strong>TOTALS</strong></td>
                            <td class="num">{{ $totals['assistencia'] }}</td>
                            <td class="num">{{ $totals['deures'] }}</td>
                            <td class="num">{{ $totals['material'] }}</td>
                            <td class="num">{{ $totals['amonestacio'] }}</td>
                            <td class="num tot">{{ array_sum($totals) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
@endif

<div class="footer">
    Generat el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} · AulaDesk
</div>

</body>
</html>