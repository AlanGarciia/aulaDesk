<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\AulaHorario;
use App\Models\UsuariEspai;
use Illuminate\Http\Request;

class AulaAdminController extends Controller
{
    private function currentEspaiId(): ?int
    {
        $espaiId = session('espai_id');
        if ($espaiId) return (int) $espaiId;

        $espai = session('espai');
        if (is_array($espai) && isset($espai['id'])) return (int) $espai['id'];
        if (is_object($espai) && isset($espai->id)) return (int) $espai->id;

        return null;
    }

    public function show(Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hay espai actual seleccionado.');
        abort_if($aula->espai_id !== $espaiId, 403);
        $professors = UsuariEspai::where('espai_id', $espaiId)
            ->where('rol', UsuariEspai::ROL_PROFESSOR ?? 'professor')
            ->orderBy('nom')
            ->get();


            $slots = AulaHorario::where('aula_id', $aula->id)->get();

            $assignacions = [];
        foreach ($slots as $s) {
            $assignacions[$s->dia_setmana][$s->hora] = $s->usuari_espai_id;
        }

        $dies = [
            1 => 'Dilluns',
            2 => 'Dimarts',
            3 => 'Dimecres',
            4 => 'Dijous',
            5 => 'Divendres',
        ];

        $hores = range(8, 19);

        return view('espai.aules.admin', compact('aula', 'professors', 'assignacions', 'dies', 'hores'));
    }

    public function update(Request $request, Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hay espai actual seleccionado.');
        abort_if($aula->espai_id !== $espaiId, 403);

        $data = $request->validate([
            'assignacions' => ['required', 'array'],
            'assignacions.*' => ['array'],
        ]);

        $profIds = UsuariEspai::where('espai_id', $espaiId)
            ->where('rol', UsuariEspai::ROL_PROFESSOR ?? 'professor')
            ->pluck('id')
            ->all();

        $dies = [1,2,3,4,5];
        $hores = range(8, 19);

        foreach ($dies as $dia) {
            foreach ($hores as $hora) {
                $profId = $data['assignacions'][$dia][$hora] ?? null;

                if ($profId === '' || $profId === null) {
                    AulaHorario::updateOrCreate(
                        ['aula_id' => $aula->id, 'dia_setmana' => $dia, 'hora' => $hora],
                        ['usuari_espai_id' => null]
                    );
                    continue;
                }

                $profId = (int) $profId;
                abort_if(!in_array($profId, $profIds, true), 422, 'Profesor inválido.');

                AulaHorario::updateOrCreate(
                    ['aula_id' => $aula->id, 'dia_setmana' => $dia, 'hora' => $hora],
                    ['usuari_espai_id' => $profId]
                );
            }
        }

        return redirect()
            ->route('espai.aules.admin', $aula)
            ->with('ok', 'Horario guardado.');
    }
}
