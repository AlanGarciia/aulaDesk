<?php

namespace App\Http\Controllers;

use App\Models\AulaHorario;
use App\Models\Espai;
use App\Models\FranjaHoraria;
use Illuminate\Http\Request;
use App\Models\UsuariEspai;


class GuardiaController extends Controller
{

    public function index(Request $request)
    {
        $espaiId = (int) session('espai_id');
        abort_unless($espaiId, 403);

        $usuariEspaiId = (int) session('usuari_espai_id');
        abort_unless($usuariEspaiId, 403);
        
        $usuariEspai = UsuariEspai::findOrFail($usuariEspaiId);
        $espai = Espai::query()->findOrFail($espaiId);
        $franjes = FranjaHoraria::query()->where('espai_id', $espaiId)->orderBy('ordre')->orderBy('inici')->get();
        $dies = [1, 2, 3, 4, 5];
        $diesLabels = [
            1 => 'Dl',
            2 => 'Dt',
            3 => 'Dc',
            4 => 'Dj',
            5 => 'Dv',
        ];

        //horari assignat del professor
        $horaris = AulaHorario::query()->where('usuari_espai_id', $usuariEspaiId)->whereIn('dia_setmana', $dies)->with(['aula', 'franja'])->get();

        $slots = [];
        foreach ($horaris as $h) {
            if (!$h->franja) {
                continue;
            }

            $dia = (int) $h->dia_setmana;
            $franjaId = (int) $h->franja->id;

            if (isset($slots[$dia][$franjaId])) {
                continue;
            }

            $slots[$dia][$franjaId] = [
                'aula' => $h->aula?->nom ?? 'Aula',
                'aula_id' => $h->aula?->id,
            ];
        }

        return view('espai.guardies.index', compact(
            'espai',
            'usuariEspai',
            'franjes',
            'dies',
            'diesLabels',
            'slots'
        ));
    }
}
