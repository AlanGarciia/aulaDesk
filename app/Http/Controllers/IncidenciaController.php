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

        $aulaEspaiId = 0;
        if ($aulaHorari->aula && $aulaHorari->aula->espai_id) {
            $aulaEspaiId = (int) $aulaHorari->aula->espai_id;
        }

        abort_if($aulaEspaiId !== $espaiId, 403);
        abort_if((int) $aulaHorari->usuari_espai_id !== $usuariEspaiId, 403, 'Aquesta hora no és teva.');

        return [$espaiId, $usuariEspaiId];
    }

    /** Construeix [alumne_id => [tipus => count, ...]] inicialitzat a 0 */
    private function buildResumIdx($alumnes, $incidencies): array
    {
        $idx = [];
        foreach ($alumnes as $a) {
            $idx[$a->id] = ['assistencia' => 0, 'deures' => 0, 'material' => 0, 'amonestacio' => 0];
        }
        foreach ($incidencies as $inc) {
            if (isset($idx[$inc->alumne_id][$inc->tipus])) {
                $idx[$inc->alumne_id][$inc->tipus]++;
            }
        }
        return $idx;
    }

    /** Resol grup_id de l'aula_horari a int o null */
    private function grupIdOrNull(AulaHorario $aulaHorari): ?int
    {
        if ($aulaHorari->grup_id) return (int) $aulaHorari->grup_id;
        return null;
    }

    public function index(Request $request, AulaHorario $aulaHorari)
    {
        [$espaiId, $usuariEspaiId] = $this->checkAccess($request, $aulaHorari);

        if (!$aulaHorari->grup) {
            return redirect()->route('espai.guardies.index')
                ->with('error_modal', 'Aquesta hora no té cap grup assignat.');
        }

        $alumnes = $aulaHorari->grup->alumnes()->orderBy('cognoms')->orderBy('nom')->get();

        $data = Carbon::today()->toDateString();
        if ($request->query('data')) {
            $data = Carbon::parse($request->query('data'))->toDateString();
        }

        $rows = Incidencia::where('aula_horari_id', $aulaHorari->id)
            ->whereDate('data', $data)
            ->orderBy('created_at')
            ->get();

        // Taula plana [alumne_id_int][tipus] => incidencia
        $incidenciesIdx = [];
        foreach ($rows as $inc) {
            $aid = (int) $inc->alumne_id;
            $incidenciesIdx[$aid][(string) $inc->tipus] = $inc;
        }

        return view('espai.incidencies.index', [
            'aulaHorari' => $aulaHorari,
            'alumnes' => $alumnes,
            'incidenciesIdx' => $incidenciesIdx,
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

        $perteany = false;
        if ($aulaHorari->grup) {
            $perteany = $aulaHorari->grup->alumnes()
                ->where('alumnes.id', $payload['alumne_id'])
                ->exists();
        }
        abort_unless($perteany, 422, 'L\'alumne no pertany al grup d\'aquesta hora.');

        $data = Carbon::today()->toDateString();
        if (isset($payload['data'])) {
            $data = Carbon::parse($payload['data'])->toDateString();
        }

        $observacions = null;
        if (isset($payload['observacions'])) $observacions = $payload['observacions'];

        Incidencia::create([
            'espai_id' => $espaiId,
            'alumne_id' => (int) $payload['alumne_id'],
            'grup_id' => $this->grupIdOrNull($aulaHorari),
            'aula_horari_id' => (int) $aulaHorari->id,
            'usuari_espai_id' => $usuariEspaiId,
            'tipus' => (string) $payload['tipus'],
            'data' => $data,
            'observacions' => $observacions,
        ]);

        return redirect()
            ->route('espai.incidencies.index', ['aulaHorari' => $aulaHorari->id, 'data' => $data])
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
            ->route('espai.incidencies.index', ['aulaHorari' => $aulaHorariId, 'data' => $data])
            ->with('ok', 'Incidència eliminada.');
    }

    public function saveBulk(Request $request, AulaHorario $aulaHorari)
    {
        [$espaiId, $usuariEspaiId] = $this->checkAccess($request, $aulaHorari);

        if (!$aulaHorari->grup) {
            return redirect()->route('espai.guardies.index')
                ->with('error_modal', 'Aquesta hora no té cap grup assignat.');
        }

        $payload = $request->validate([
            'data' => ['nullable', 'date'],
            'selections' => ['nullable', 'array'],
        ]);

        $data = Carbon::today()->toDateString();
        if (isset($payload['data'])) {
            $data = Carbon::parse($payload['data'])->toDateString();
        }

        $selections = [];
        if (isset($payload['selections'])) $selections = $payload['selections'];

        // IDs vàlids del grup
        $alumnesIds = [];
        foreach ($aulaHorari->grup->alumnes as $a) {
            $alumnesIds[] = (int) $a->id;
        }
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

        $existents = Incidencia::where('aula_horari_id', $aulaHorari->id)
            ->whereDate('data', $data)
            ->get();

        $existentsIdx = [];
        foreach ($existents as $inc) {
            $existentsIdx[(int) $inc->alumne_id][(string) $inc->tipus] = $inc;
        }

        $grupId = $this->grupIdOrNull($aulaHorari);

        DB::transaction(function () use (
            $espaiId, $usuariEspaiId, $aulaHorari, $data, $grupId,
            $existentsIdx, $seleccionatsIdx
        ) {

            foreach ($existentsIdx as $alumneId => $tipusMap) {
                foreach ($tipusMap as $tipus => $inc) {
                    if (!isset($seleccionatsIdx[$alumneId][$tipus])) {
                        $inc->delete();
                    }
                }
            }

            foreach ($seleccionatsIdx as $alumneId => $tipusMap) {
                foreach ($tipusMap as $tipus => $_) {
                    if (isset($existentsIdx[$alumneId][$tipus])) continue;

                    Incidencia::create([
                        'espai_id' => $espaiId,
                        'alumne_id' => (int) $alumneId,
                        'grup_id' => $grupId,
                        'aula_horari_id' => (int) $aulaHorari->id,
                        'usuari_espai_id' => $usuariEspaiId,
                        'tipus' => (string) $tipus,
                        'data' => $data,
                        'observacions' => null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('espai.incidencies.index', ['aulaHorari' => $aulaHorari->id, 'data' => $data])
            ->with('ok', 'Llista guardada correctament.');
    }

    public function pdf(Request $request, AulaHorario $aulaHorari)
    {
        [$espaiId, $usuariEspaiId] = $this->checkAccess($request, $aulaHorari);

        if (!$aulaHorari->grup) {
            return redirect()->route('espai.guardies.index')
                ->with('error_modal', 'Aquesta hora no té cap grup assignat.');
        }

        $from = Carbon::today()->startOfWeek();
        if ($request->query('from')) $from = Carbon::parse($request->query('from'))->startOfDay();

        $to = Carbon::today()->startOfWeek()->addDays(4);
        if ($request->query('to')) $to = Carbon::parse($request->query('to'))->endOfDay();

        $alumnes = $aulaHorari->grup->alumnes()->orderBy('cognoms')->orderBy('nom')->get();

        $incidencies = Incidencia::where('aula_horari_id', $aulaHorari->id)
            ->whereBetween('data', [$from->toDateString(), $to->toDateString()])
            ->orderBy('data')->orderBy('created_at')->get();

        $resumIdx = $this->buildResumIdx($alumnes, $incidencies);

        $perDia = $incidencies->groupBy(function ($i) {
            return $i->data->toDateString();
        });

        $pdf = Pdf::loadView('espai.incidencies.pdf', [
            'aulaHorari' => $aulaHorari,
            'alumnes' => $alumnes,
            'resumIdx' => $resumIdx,
            'perDia' => $perDia,
            'from' => $from,
            'to' => $to,
            'tipusLabels' => Incidencia::TIPUS_LABELS,
        ])->setPaper('a4', 'portrait');

        $nomGrup = 'grup';
        if ($aulaHorari->grup->nom) $nomGrup = $aulaHorari->grup->nom;

        $nomFile = 'llista-' . $nomGrup . '-' . $from->format('Y-m-d') . '_' . $to->format('Y-m-d') . '.pdf';

        return $pdf->download($nomFile);
    }

    public function globalPdf(Request $request)
    {
        $espaiId = (int) $request->session()->get('espai_id');
        $usuariEspaiId = (int) $request->session()->get('usuari_espai_id');
        abort_unless($espaiId && $usuariEspaiId, 403);

        $from = Carbon::today()->startOfWeek();
        if ($request->query('from')) $from = Carbon::parse($request->query('from'))->startOfDay();

        $to = Carbon::today()->startOfWeek()->addDays(4);
        if ($request->query('to')) $to = Carbon::parse($request->query('to'))->endOfDay();

        $tipus = $request->query('tipus', 'setmanal');

        $horaris = AulaHorario::where('usuari_espai_id', $usuariEspaiId)
            ->whereNotNull('grup_id')
            ->with([
                'aula',
                'franja',
                'grup.alumnes' => function ($q) {
                    $q->orderBy('cognoms')->orderBy('nom');
                },
            ])
            ->whereHas('aula', function ($q) use ($espaiId) {
                $q->where('espai_id', $espaiId);
            })
            ->get();

        $horariData = [];
        foreach ($horaris as $h) {
            if (!$h->grup) continue;

            $incs = Incidencia::where('aula_horari_id', $h->id)
                ->whereBetween('data', [$from->toDateString(), $to->toDateString()])
                ->orderBy('data')
                ->get();

            $horariData[] = [
                'horari' => $h,
                'incidencies' => $incs,
                'resumIdx' => $this->buildResumIdx($h->grup->alumnes, $incs),
            ];
        }

        usort($horariData, function ($a, $b) {
            $da = (int) $a['horari']->dia_setmana;
            $db = (int) $b['horari']->dia_setmana;
            if ($da !== $db) return $da <=> $db;

            $fa = '';
            if ($a['horari']->franja) $fa = (string) $a['horari']->franja->inici;

            $fb = '';
            if ($b['horari']->franja) $fb = (string) $b['horari']->franja->inici;

            return strcmp($fa, $fb);
        });

        $pdf = Pdf::loadView('espai.incidencies.global-pdf', [
            'horariData' => $horariData,
            'from' => $from,
            'to' => $to,
            'tipus' => $tipus,
            'tipusLabels' => Incidencia::TIPUS_LABELS,
        ])->setPaper('a4', 'portrait');

        $nomFile = 'informe-' . $tipus . '-' . $from->format('Y-m-d') . '_' . $to->format('Y-m-d') . '.pdf';

        return $pdf->download($nomFile);
    }
}