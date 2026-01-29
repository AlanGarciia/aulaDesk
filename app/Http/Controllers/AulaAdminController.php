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

        // ✅ IMPORTANT: no abortar si no hi ha franges, la vista ho mostra
        // if ($franges->count() === 0) {
        //     abort(422, "No hi ha franges horàries creades per aquest espai.");
        // }

        // [dia][franja_id] => usuari_espai_id
        $assignacions = [];

        // Només calculem slots si hi ha franges (sinó no cal)
        if ($franges->isNotEmpty()) {
            $slots = AulaHorario::where('aula_id', $aula->id)->get();

            foreach ($slots as $s) {
                if (!isset($assignacions[$s->dia_setmana])) {
                    $assignacions[$s->dia_setmana] = [];
                }
                $assignacions[$s->dia_setmana][$s->franja_horaria_id] = $s->usuari_espai_id;
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
            'tickets'
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

        $profIds = UsuariEspai::where('espai_id', $espaiId)
            ->where('rol', $rolProfessor)
            ->pluck('id')
            ->all();

        $franges = FranjaHoraria::where('espai_id', $espaiId)
            ->orderBy('ordre')
            ->get();

        $franjaIds = [];
        foreach ($franges as $f) {
            $franjaIds[] = (int) $f->id;
        }

        $dies = [1, 2, 3, 4, 5];

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
