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
        $grups        = $request->input('grups', []);
        $espaiId      = $aula->espai_id;

        $conflicts = [];

        foreach ($assignacions as $dia => $franges) {
            foreach ($franges as $franjaId => $profId) {

                $grupId = $grups[$dia][$franjaId] ?? null;

                if ($profId) {
                    $conflict = AulaHorario::where('espai_id',          $espaiId)
                        ->where('dia_setmana',       $dia)
                        ->where('franja_horaria_id', $franjaId)
                        ->where('usuari_espai_id',   $profId)
                        ->where('aula_id',           '!=', $aula->id)
                        ->with(['aula', 'professor', 'franja'])
                        ->first();

                    if ($conflict) {
                        $conflicts[] = [
                            'professor' => $conflict->professor->nom  ?? "Professor #{$profId}",
                            'dia'       => $conflict->dia_setmana,
                            'franja'    => ($conflict->franja->nom ?? '') . ' ' .
                                           substr($conflict->franja->inici ?? '', 0, 5) . '-' .
                                           substr($conflict->franja->fi    ?? '', 0, 5),
                            'aula'      => $conflict->aula->nom ?? "Aula #{$conflict->aula_id}",
                        ];
                        continue;
                    }
                }

                AulaHorario::updateOrCreate(
                    [
                        'aula_id'           => $aula->id,
                        'dia_setmana'       => $dia,
                        'franja_horaria_id' => $franjaId,
                    ],
                    [
                        'espai_id'        => $espaiId,
                        'usuari_espai_id' => $profId ?: null,
                        'grup_id'         => $grupId ?: null,
                    ]
                );
            }
        }

        if (!empty($conflicts)) {
            return back()
                ->with('conflicts', $conflicts)
                ->with('warning', 'Alguns professors ja estaven assignats en una altra aula en el mateix horari.');
        }

        return back()->with('ok', 'Horari guardat correctament');
    }
}