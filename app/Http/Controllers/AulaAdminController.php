<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\AulaHorario;
use App\Models\UsuariEspai;
use App\Models\FranjaHoraria;
use App\Models\Ticket;
use App\Models\GuardiaSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AulaAdminController extends Controller
{
    private function currentEspaiId(): ?int
    {
        $espaiId = session('espai_id');
        if ($espaiId) {
            return (int) $espaiId;
        }

        $espai = session('espai');

        if (is_array($espai) && isset($espai['id'])) {
            return (int) $espai['id'];
        }

        if (is_object($espai) && isset($espai->id)) {
            return (int) $espai->id;
        }

        return null;
    }

    private function professorRolValue(): string
    {
        if (defined('\App\Models\UsuariEspai::ROL_PROFESSOR')) {
            $val = \App\Models\UsuariEspai::ROL_PROFESSOR;
            if (is_string($val) && $val !== '') {
                return $val;
            }
        }

        return 'professor';
    }

    public function show(Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        abort_if((int) $aula->espai_id !== (int) $espaiId, 403);

        $rolProfessor = $this->professorRolValue();

        $professors = UsuariEspai::query()
            ->where('espai_id', $espaiId)
            ->where('rol', $rolProfessor)
            ->orderBy('nom')
            ->get();

        $dies = [
            1 => 'Dilluns',
            2 => 'Dimarts',
            3 => 'Dimecres',
            4 => 'Dijous',
            5 => 'Divendres',
        ];

        $franges = FranjaHoraria::query()
            ->where('espai_id', $espaiId)
            ->orderBy('ordre')
            ->get();

        // [dia][franja_id] => usuari_espai_id (profe "normal")
        $assignacions = [];

        if ($franges->isNotEmpty()) {
            $slots = AulaHorario::query()
                ->where('aula_id', $aula->id)
                ->get();

            foreach ($slots as $s) {
                $dia = (int) $s->dia_setmana;
                $franjaId = (int) $s->franja_horaria_id;

                if (!isset($assignacions[$dia])) {
                    $assignacions[$dia] = [];
                }

                $assignacions[$dia][$franjaId] = $s->usuari_espai_id;
            }
        }

        // ✅ Ocupats: [dia][franja_id][prof_id] = nomAula (para pintar conflictos en rojo / avisos)
        $ocupats = [];

        if ($franges->isNotEmpty()) {
            $franjaIds = $franges->pluck('id')->map(function ($v) { return (int) $v; })->all();

            $ocupacions = AulaHorario::query()
                ->whereNotNull('usuari_espai_id')
                ->whereIn('dia_setmana', array_keys($dies))
                ->whereIn('franja_horaria_id', $franjaIds)
                ->where('aula_id', '!=', $aula->id)
                ->whereHas('aula', function ($q) use ($espaiId) {
                    $q->where('espai_id', $espaiId);
                })
                ->with('aula')
                ->get();

            foreach ($ocupacions as $o) {
                $dia = (int) $o->dia_setmana;
                $franjaId = (int) $o->franja_horaria_id;
                $profId = (int) $o->usuari_espai_id;

                if (!isset($ocupats[$dia])) {
                    $ocupats[$dia] = [];
                }
                if (!isset($ocupats[$dia][$franjaId])) {
                    $ocupats[$dia][$franjaId] = [];
                }

                $nomAula = 'Altra aula';
                if ($o->aula && isset($o->aula->nom) && $o->aula->nom !== '') {
                    $nomAula = (string) $o->aula->nom;
                }

                $ocupats[$dia][$franjaId][$profId] = $nomAula;
            }
        }

        // ✅ Guardies acceptades d'AQUESTA AULA, només la setmana actual
        // [dia][franja_id] => nom cobridor
        $substituts = [];

        if ($franges->isNotEmpty()) {
            $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $weekEnd = Carbon::now()->endOfWeek(Carbon::SUNDAY);

            $guardies = GuardiaSolicitud::query()
                ->where('espai_id', $espaiId)
                ->where('aula_id', $aula->id)
                ->where('estat', 'acceptada')
                ->whereNotNull('cobridor_usuari_espai_id')
                // usamos updated_at porque al aceptar la guardia se actualiza
                ->whereBetween('updated_at', [$weekStart, $weekEnd])
                ->with('cobridor') // ⚠️ necesita relación cobridor() en el modelo
                ->get();

            foreach ($guardies as $g) {
                $d = (int) $g->dia_setmana;
                $f = (int) $g->franja_horaria_id;

                $nom = '';
                if ($g->cobridor && isset($g->cobridor->nom) && $g->cobridor->nom !== '') {
                    $nom = (string) $g->cobridor->nom;
                }

                if ($nom !== '') {
                    if (!isset($substituts[$d])) {
                        $substituts[$d] = [];
                    }
                    $substituts[$d][$f] = $nom;
                }
            }
        }

        $tickets = Ticket::query()
            ->where('espai_id', $espaiId)
            ->where('aula_id', $aula->id)
            ->with('creador')
            ->latest()
            ->get();

        return view('espai.aules.admin', compact(
            'aula',
            'professors',
            'assignacions',
            'dies',
            'franges',
            'tickets',
            'ocupats',
            'substituts'
        ));
    }

    public function update(Request $request, Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        abort_if((int) $aula->espai_id !== (int) $espaiId, 403);

        $data = $request->validate([
            'assignacions' => ['required', 'array'],
            'assignacions.*' => ['array'],
        ]);

        $rolProfessor = $this->professorRolValue();

        $professors = UsuariEspai::query()
            ->where('espai_id', $espaiId)
            ->where('rol', $rolProfessor)
            ->orderBy('nom')
            ->get();

        $profIds = $professors->pluck('id')->map(function ($v) { return (int) $v; })->all();

        $profNoms = [];
        foreach ($professors as $p) {
            $profNoms[(int) $p->id] = (string) $p->nom;
        }

        $franges = FranjaHoraria::query()
            ->where('espai_id', $espaiId)
            ->orderBy('ordre')
            ->get();

        $franjaIds = [];
        $franjaLabels = [];

        foreach ($franges as $f) {
            $fid = (int) $f->id;
            $franjaIds[] = $fid;

            $label = substr((string) $f->inici, 0, 5) . ' - ' . substr((string) $f->fi, 0, 5);
            if (isset($f->nom) && $f->nom) {
                $label = (string) $f->nom . ' (' . $label . ')';
            }
            $franjaLabels[$fid] = $label;
        }

        $dies = [1, 2, 3, 4, 5];

        $diesLabels = [
            1 => 'Dilluns',
            2 => 'Dimarts',
            3 => 'Dimecres',
            4 => 'Dijous',
            5 => 'Divendres',
        ];

        // ✅ Conflictes (no permitir el mismo profe en otra aula a la vez)
        $conflicts = [];

        foreach ($dies as $dia) {
            foreach ($franjaIds as $franjaId) {

                $profId = null;

                if (isset($data['assignacions'][$dia]) && isset($data['assignacions'][$dia][$franjaId])) {
                    $profId = $data['assignacions'][$dia][$franjaId];
                }

                if ($profId === '' || $profId === null) {
                    continue;
                }

                $profId = (int) $profId;

                abort_if(!in_array($profId, $profIds, true), 422, 'Professor invàlid.');

                $ocupacio = AulaHorario::query()
                    ->where('usuari_espai_id', $profId)
                    ->where('dia_setmana', $dia)
                    ->where('franja_horaria_id', $franjaId)
                    ->where('aula_id', '!=', $aula->id)
                    ->whereHas('aula', function ($q) use ($espaiId) {
                        $q->where('espai_id', $espaiId);
                    })
                    ->with('aula')
                    ->first();

                if ($ocupacio) {
                    $aulaOcupadaNom = 'Altra aula';
                    if ($ocupacio->aula && isset($ocupacio->aula->nom) && $ocupacio->aula->nom !== '') {
                        $aulaOcupadaNom = (string) $ocupacio->aula->nom;
                    }

                    $diaNom = isset($diesLabels[$dia]) ? (string) $diesLabels[$dia] : ('Dia ' . (string) $dia);
                    $franjaTxt = isset($franjaLabels[$franjaId]) ? (string) $franjaLabels[$franjaId] : ('Franja ' . (string) $franjaId);
                    $profNom = isset($profNoms[$profId]) ? (string) $profNoms[$profId] : ('Professor #' . (string) $profId);

                    $conflicts[] = [
                        'dia' => $diaNom,
                        'franja' => $franjaTxt,
                        'professor' => $profNom,
                        'aula' => $aulaOcupadaNom,
                    ];
                }
            }
        }

        if (!empty($conflicts)) {
            return redirect()
                ->route('espai.aules.admin', $aula)
                ->withInput()
                ->with('conflicts', $conflicts);
        }

        // ✅ Guardar
        foreach ($dies as $dia) {
            foreach ($franjaIds as $franjaId) {

                $profId = null;

                if (isset($data['assignacions'][$dia]) && isset($data['assignacions'][$dia][$franjaId])) {
                    $profId = $data['assignacions'][$dia][$franjaId];
                }

                if ($profId === '' || $profId === null) {
                    AulaHorario::updateOrCreate(
                        [
                            'aula_id' => $aula->id,
                            'dia_setmana' => $dia,
                            'franja_horaria_id' => $franjaId,
                        ],
                        [
                            'usuari_espai_id' => null,
                        ]
                    );
                    continue;
                }

                $profId = (int) $profId;

                abort_if(!in_array($profId, $profIds, true), 422, 'Professor invàlid.');

                AulaHorario::updateOrCreate(
                    [
                        'aula_id' => $aula->id,
                        'dia_setmana' => $dia,
                        'franja_horaria_id' => $franjaId,
                    ],
                    [
                        'usuari_espai_id' => $profId,
                    ]
                );
            }
        }

        return redirect()
            ->route('espai.aules.admin', $aula)
            ->with('ok', 'Horari desat.');
    }
}
