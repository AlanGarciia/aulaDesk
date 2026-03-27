<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aula;
use App\Models\AulaHorario;

class AulaHorarioController extends Controller
{
    public function update(Request $request, Aula $aula)
    {
        $assignacions = $request->input('assignacions', []);
        $grups = $request->input('grups', []);

        foreach ($assignacions as $dia => $franges) {
            foreach ($franges as $franjaId => $profId) {

                $grupId = $grups[$dia][$franjaId] ?? null;

            AulaHorario::updateOrCreate(
            [
                'aula_id' => $aula->id,
                'dia_setmana' => $dia,
                'franja_horaria_id' => $franjaId,
            ],
            [
                'espai_id' => $aula->espai_id,
                'usuari_espai_id' => $profId ?: null,
                'grup_id' => $grupId ?: null,
            ]
        );
            }
        }

        return back()->with('ok', 'Horari guardat correctament');
    }
}