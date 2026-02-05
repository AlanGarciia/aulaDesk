<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\AulaHorario;
use App\Models\UsuariEspai;
use App\Models\FranjaHoraria;
use App\Models\Ticket;
use Illuminate\Http\Request;

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

        abort_if($aula->espai_id !== $espaiId, 403);

        $rolProfessor = $this->professorRolValue();

        $professors = UsuariEspai::where('espai_id', $espaiId)
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

        $franges = FranjaHoraria::where('espai_id', $espaiId)
            ->orderBy('ordre')
            ->get();

        $assignacions = [];

        if ($franges->isNotEmpty()) {
            $slots = AulaHorario::where('aula_id', $aula->id)->get();

            foreach ($slots as $s) {
                if (!isset($assignacions[$s->dia_setmana])) {
                    $assignacions[$s->dia_setwana] = [];
                }
            }
        }

        $assignacions = [];
        if ($franges->isNotEmpty()) {
            $slots = AulaHorario::where('aula_id', $aula->id)->get();
            foreach ($slots as $s) {
                if (!isset($assignacions[$s->dia_setmana])) {
                    $assignacions[$s->dia_setmana] = [];
                }
                $assignacions[$s->dia_setmana][$s->franja_horaria_id] = $s->usuari_espai_id;
            }
        }

        $ocupats = [];
        if ($franges->isNotEmpty()) {
            $ocupacions = AulaHorario::query()
                ->where('usuari_espai_id', '!=', null)
                ->whereIn('dia_setmana', array_keys($dies))
                ->whereIn('franja_horaria_id', $franges->pluck('id')->all())
                ->where('aula_id', '!=', $aula->id)
                ->with('aula')
                ->get();

            foreach ($ocupacions as $o) {
                $dia = (int) $o->dia_setmana;
                $franjaId = (int) $o->franja_horaria_id;
                $profId = (int) $o->usuari_espai_id;

                if (!isset($ocupats[$dia])) $ocupats[$dia] = [];
                if (!isset($ocupats[$dia][$franjaId])) $ocupats[$dia][$franjaId] = [];

                $nomAula = 'Altra aula';
                if ($o->aula && isset($o->aula->nom) && $o->aula->nom !== '') {
                    $nomAula = $o->aula->nom;
                }

                $ocupats[$dia][$franjaId][$profId] = $nomAula;
            }
        }

        $tickets = Ticket::where('espai_id', $espaiId)
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
            'ocupats'
        ));
    }

    public function update(Request $request, Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        abort_if($aula->espai_id !== $espaiId, 403);

        $data = $request->validate([
            'assignacions' => ['required', 'array'],
            'assignacions.*' => ['array'],
        ]);

        $rolProfessor = $this->professorRolValue();

        $professors = UsuariEspai::where('espai_id', $espaiId)
            ->where('rol', $rolProfessor)
            ->orderBy('nom')
            ->get();

        $profIds = $professors->pluck('id')->map(function ($v) { return (int) $v; })->all();
        $profNoms = [];
        foreach ($professors as $p) {
            $profNoms[(int) $p->id] = (string) $p->nom;
        }

        $franges = FranjaHoraria::where('espai_id', $espaiId)
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

        //Nota ALan: Guarda los conflictos de los usuarios con rol profesor para que no puedas poner dos cosas a la vez
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
                    ->with('aula')
                    ->first();

                if ($ocupacio) {
                    $aulaOcupadaNom = 'Altra aula';
                    if ($ocupacio->aula && isset($ocupacio->aula->nom) && $ocupacio->aula->nom !== '') {
                        $aulaOcupadaNom = (string) $ocupacio->aula->nom;
                    }

                    $diaNom = isset($diesLabels[$dia]) ? $diesLabels[$dia] : ('Dia ' . $dia);
                    $franjaTxt = isset($franjaLabels[$franjaId]) ? $franjaLabels[$franjaId] : ('Franja ' . $franjaId);
                    $profNom = isset($profNoms[$profId]) ? $profNoms[$profId] : ('Professor #' . $profId);

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
