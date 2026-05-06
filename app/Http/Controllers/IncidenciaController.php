<?php

namespace App\Http\Controllers;

use App\Models\AulaHorario;
use App\Models\Incidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidenciaController extends Controller
{
    private function checkAccess(Request $request, AulaHorario $aulaHorari): array
    {
        $espaiId = (int) $request->session()->get('espai_id');
        $usuariEspaiId = (int) $request->session()->get('usuari_espai_id');
        abort_unless($espaiId && $usuariEspaiId, 403);

        $aulaHorari->load(['aula', 'franja', 'grup']);

        abort_if(
            (int) ($aulaHorari->aula->espai_id ?? 0) !== $espaiId,
            403
        );

        abort_if(
            (int) $aulaHorari->usuari_espai_id !== $usuariEspaiId,
            403,
            'Aquesta hora no és teva.'
        );

        return [$espaiId, $usuariEspaiId];
    }

    public function index(Request $request, AulaHorario $aulaHorari)
    {
        [$espaiId, $usuariEspaiId] = $this->checkAccess($request, $aulaHorari);

        if (!$aulaHorari->grup) {
            return redirect()
                ->route('espai.guardies.index')
                ->with('error_modal', 'Aquesta hora no té cap grup assignat.');
        }

        $alumnes = $aulaHorari->grup->alumnes()
            ->orderBy('cognoms')
            ->orderBy('nom')
            ->get();

        $data = $request->query('data')
            ? Carbon::parse($request->query('data'))->toDateString()
            : Carbon::today()->toDateString();

        $incidencies = Incidencia::query()
            ->where('aula_horari_id', $aulaHorari->id)
            ->where('data', $data)
            ->orderBy('created_at')
            ->get()
            ->groupBy('alumne_id');

        return view('espai.incidencies.index', [
            'aulaHorari' => $aulaHorari,
            'alumnes' => $alumnes,
            'incidencies' => $incidencies,
            'data' => $data,
            'tipusLabels' => Incidencia::TIPUS_LABELS,
            'tipusIcones' => Incidencia::TIPUS_ICONES,
            'tipusValids' => Incidencia::TIPUS_VALIDS,
        ]);
    }

    public function store(Request $request, AulaHorario $aulaHorari)
    {
        [$espaiId, $usuariEspaiId] = $this->checkAccess($request, $aulaHorari);

        $payload = $request->validate([
            'alumne_id' => ['required', 'integer'],
            'tipus' => ['required', 'string', 'in:' . implode(',', Incidencia::TIPUS_VALIDS)],
            'observacions' => ['nullable', 'string', 'max:500'],
            'data' => ['nullable', 'date'],
        ]);

        $perteany = $aulaHorari->grup
            ? $aulaHorari->grup->alumnes()->where('alumnes.id', $payload['alumne_id'])->exists()
            : false;

        abort_unless($perteany, 422, 'L\'alumne no pertany al grup d\'aquesta hora.');

        $data = isset($payload['data'])
            ? Carbon::parse($payload['data'])->toDateString()
            : Carbon::today()->toDateString();

        Incidencia::create([
            'espai_id' => $espaiId,
            'alumne_id' => (int) $payload['alumne_id'],
            'grup_id' => (int) ($aulaHorari->grup_id ?? 0) ?: null,
            'aula_horari_id' => (int) $aulaHorari->id,
            'usuari_espai_id' => $usuariEspaiId,
            'tipus' => (string) $payload['tipus'],
            'data' => $data,
            'observacions' => $payload['observacions'] ?? null,
        ]);

        return redirect()
            ->route('espai.incidencies.index', [
                'aulaHorari' => $aulaHorari->id,
                'data' => $data,
            ])
            ->with('ok', 'Incidència afegida.');
    }

    public function destroy(Request $request, Incidencia $incidencia)
    {
        $espaiId = (int) $request->session()->get('espai_id');
        $usuariEspaiId = (int) $request->session()->get('usuari_espai_id');
        abort_unless($espaiId && $usuariEspaiId, 403);

        abort_if((int) $incidencia->espai_id !== $espaiId, 403);
        abort_if((int) $incidencia->usuari_espai_id !== $usuariEspaiId, 403, 'No pots eliminar incidències d\'altres professors.');

        $aulaHorariId = $incidencia->aula_horari_id;
        $data = $incidencia->data->toDateString();

        $incidencia->delete();

        return redirect()
            ->route('espai.incidencies.index', [
                'aulaHorari' => $aulaHorariId,
                'data' => $data,
            ])
            ->with('ok', 'Incidència eliminada.');
    }

    public function saveBulk(Request $request, AulaHorario $aulaHorari)
    {
        [$espaiId, $usuariEspaiId] = $this->checkAccess($request, $aulaHorari);

        if (!$aulaHorari->grup) {
            return redirect()
                ->route('espai.guardies.index')
                ->with('error_modal', 'Aquesta hora no té cap grup assignat.');
        }

        $payload = $request->validate([
            'data' => ['nullable', 'date'],
            'selections' => ['nullable', 'array'],
        ]);

        $data = isset($payload['data'])
            ? Carbon::parse($payload['data'])->toDateString()
            : Carbon::today()->toDateString();

        $selections = $payload['selections'] ?? [];

        // IDs vàlids del grup
        $alumnesIds = $aulaHorari->grup->alumnes()->pluck('alumnes.id')->toArray();
        $alumnesIdsSet = array_flip($alumnesIds);

        // Construir el set seleccionat: [alumne_id][tipus] => true
        $seleccionatsIdx = [];
        foreach ($selections as $alumneId => $tipusArr) {
            $alumneId = (int) $alumneId;
            if (!isset($alumnesIdsSet[$alumneId])) continue;
            if (!is_array($tipusArr)) continue;
            foreach ($tipusArr as $tipus => $val) {
                $tipus = (string) $tipus;
                if (!in_array($tipus, Incidencia::TIPUS_VALIDS, true)) continue;
                $seleccionatsIdx[$alumneId][$tipus] = true;
            }
        }

        // Incidències actuals d'aquesta hora i dia
        $existents = Incidencia::query()
            ->where('aula_horari_id', $aulaHorari->id)
            ->where('data', $data)
            ->get();

        $existentsIdx = [];
        foreach ($existents as $inc) {
            $existentsIdx[(int) $inc->alumne_id][(string) $inc->tipus] = $inc;
        }

        // Sync: afegir noves, eliminar les que ja no estan
        DB::transaction(function () use (
            $espaiId, $usuariEspaiId, $aulaHorari, $data,
            $existentsIdx, $seleccionatsIdx
        ) {
            // Eliminar les desmarcades
            foreach ($existentsIdx as $alumneId => $tipusMap) {
                foreach ($tipusMap as $tipus => $inc) {
                    if (!isset($seleccionatsIdx[$alumneId][$tipus])) {
                        $inc->delete();
                    }
                }
            }

            // Afegir les noves
            foreach ($seleccionatsIdx as $alumneId => $tipusMap) {
                foreach ($tipusMap as $tipus => $_) {
                    if (!isset($existentsIdx[$alumneId][$tipus])) {
                        Incidencia::create([
                            'espai_id' => $espaiId,
                            'alumne_id' => (int) $alumneId,
                            'grup_id' => (int) ($aulaHorari->grup_id ?? 0) ?: null,
                            'aula_horari_id' => (int) $aulaHorari->id,
                            'usuari_espai_id' => $usuariEspaiId,
                            'tipus' => (string) $tipus,
                            'data' => $data,
                            'observacions' => null,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('espai.incidencies.index', [
                'aulaHorari' => $aulaHorari->id,
                'data' => $data,
            ])
            ->with('ok', 'Llista guardada correctament.');
    }

    public function pdf(Request $request, AulaHorario $aulaHorari)
    {
        [$espaiId, $usuariEspaiId] = $this->checkAccess($request, $aulaHorari);

        if (!$aulaHorari->grup) {
            return redirect()
                ->route('espai.guardies.index')
                ->with('error_modal', 'Aquesta hora no té cap grup assignat.');
        }

        // Període: per defecte la setmana actual (dilluns-divendres)
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::today()->startOfWeek();
        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::today()->startOfWeek()->addDays(4); // divendres

        $alumnes = $aulaHorari->grup->alumnes()
            ->orderBy('cognoms')
            ->orderBy('nom')
            ->get();

        $incidencies = Incidencia::query()
            ->where('aula_horari_id', $aulaHorari->id)
            ->whereBetween('data', [$from->toDateString(), $to->toDateString()])
            ->orderBy('data')
            ->orderBy('created_at')
            ->get();

        // Index per [alumne_id][tipus] => count
        $resumIdx = [];
        foreach ($alumnes as $a) {
            $resumIdx[$a->id] = [
                'assistencia' => 0,
                'deures' => 0,
                'material' => 0,
                'amonestacio' => 0,
            ];
        }
        foreach ($incidencies as $inc) {
            if (isset($resumIdx[$inc->alumne_id][$inc->tipus])) {
                $resumIdx[$inc->alumne_id][$inc->tipus]++;
            }
        }

        // Detall per dia
        $perDia = $incidencies->groupBy(fn ($i) => $i->data->toDateString());

        $pdf = Pdf::loadView('espai.incidencies.pdf', [
            'aulaHorari' => $aulaHorari,
            'alumnes' => $alumnes,
            'resumIdx' => $resumIdx,
            'perDia' => $perDia,
            'from' => $from,
            'to' => $to,
            'tipusLabels' => Incidencia::TIPUS_LABELS,
        ])->setPaper('a4', 'portrait');

        $nomFile = 'llista-'
            . ($aulaHorari->grup->nom ?? 'grup') . '-'
            . $from->format('Y-m-d') . '_'
            . $to->format('Y-m-d')
            . '.pdf';

        return $pdf->download($nomFile);
    }

    public function globalPdf(Request $request)
    {
        $espaiId = (int) $request->session()->get('espai_id');
        $usuariEspaiId = (int) $request->session()->get('usuari_espai_id');
        abort_unless($espaiId && $usuariEspaiId, 403);

        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::today()->startOfWeek();
        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::today()->startOfWeek()->addDays(4);

        $tipus = $request->query('tipus', 'setmanal'); // 'setmanal' | 'mensual'

        // Totes les hores de l'usuari amb grup
        $horaris = AulaHorario::query()
            ->where('usuari_espai_id', $usuariEspaiId)
            ->whereNotNull('grup_id')
            ->with([
                'aula',
                'franja',
                'grup.alumnes' => fn ($q) => $q->orderBy('cognoms')->orderBy('nom'),
            ])
            ->whereHas('aula', fn ($q) => $q->where('espai_id', $espaiId))
            ->get();

        $horariData = [];
        foreach ($horaris as $h) {
            if (!$h->grup) continue;

            $incs = Incidencia::query()
                ->where('aula_horari_id', $h->id)
                ->whereBetween('data', [$from->toDateString(), $to->toDateString()])
                ->orderBy('data')
                ->get();

            $resumIdx = [];
            foreach ($h->grup->alumnes as $a) {
                $resumIdx[$a->id] = [
                    'assistencia' => 0,
                    'deures' => 0,
                    'material' => 0,
                    'amonestacio' => 0,
                ];
            }
            foreach ($incs as $inc) {
                if (isset($resumIdx[$inc->alumne_id][$inc->tipus])) {
                    $resumIdx[$inc->alumne_id][$inc->tipus]++;
                }
            }

            $horariData[] = [
                'horari' => $h,
                'incidencies' => $incs,
                'resumIdx' => $resumIdx,
            ];
        }

        // Ordena per dia + hora
        usort($horariData, function ($a, $b) {
            $da = (int) $a['horari']->dia_setmana;
            $db = (int) $b['horari']->dia_setmana;
            if ($da !== $db) return $da <=> $db;
            $fa = (string) ($a['horari']->franja->inici ?? '');
            $fb = (string) ($b['horari']->franja->inici ?? '');
            return strcmp($fa, $fb);
        });

        $pdf = Pdf::loadView('espai.incidencies.global-pdf', [
            'horariData' => $horariData,
            'from' => $from,
            'to' => $to,
            'tipus' => $tipus,
            'tipusLabels' => Incidencia::TIPUS_LABELS,
        ])->setPaper('a4', 'portrait');

        $nomFile = 'informe-' . $tipus . '-'
            . $from->format('Y-m-d') . '_'
            . $to->format('Y-m-d') . '.pdf';

        return $pdf->download($nomFile);
    }
}