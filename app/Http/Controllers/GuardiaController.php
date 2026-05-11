<?php

namespace App\Http\Controllers;

use App\Models\AulaHorario;
use App\Models\Espai;
use App\Models\FranjaHoraria;
use Illuminate\Http\Request;
use App\Models\UsuariEspai;
use App\Models\GuardiaSolicitud;
use App\Models\Noticia;
use App\Models\Notificacio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GuardiaController extends Controller
{
    public function index(Request $request)
    {
        $espaiId = (int) session('espai_id');
        abort_unless($espaiId, 403);

        $usuariEspaiId = (int) session('usuari_espai_id');
        abort_unless($usuariEspaiId, 403);

        $usuariEspai = UsuariEspai::findOrFail($usuariEspaiId);
        $espai = Espai::findOrFail($espaiId);

        $franjes = FranjaHoraria::where('espai_id', $espaiId)
            ->orderBy('ordre')->orderBy('inici')->get();

        $dies = [1, 2, 3, 4, 5];
        $diesLabels = [1 => 'Dl', 2 => 'Dt', 3 => 'Dc', 4 => 'Dj', 5 => 'Dv'];

        $horaris = AulaHorario::where('usuari_espai_id', $usuariEspaiId)
            ->whereIn('dia_setmana', $dies)
            ->with(['aula', 'franja'])
            ->get();

        $slots = [];

        foreach ($horaris as $h) {
            if (!$h->franja) continue;

            $dia = (int) $h->dia_setmana;
            $franjaId = (int) $h->franja->id;

            if (isset($slots[$dia][$franjaId])) continue;

            $aulaNom = 'Aula';
            $aulaId = null;

            if ($h->aula && $h->aula->nom) $aulaNom = (string) $h->aula->nom;
            if ($h->aula && $h->aula->id) $aulaId = (int) $h->aula->id;

            $slots[$dia][$franjaId] = [
                'aula' => $aulaNom,
                'aula_id' => $aulaId,
                'horari_id' => (int) $h->id,
            ];
        }

        $sols = GuardiaSolicitud::where('espai_id', $espaiId)
            ->where(function ($q) use ($usuariEspaiId) {
                $q->where('solicitant_usuari_espai_id', $usuariEspaiId)
                  ->orWhere('cobridor_usuari_espai_id', $usuariEspaiId);
            })
            ->get();

        $solSlots = [];

        foreach ($sols as $s) {
            $d = (int) $s->dia_setmana;
            $f = (int) $s->franja_horaria_id;

            $solSlots[$d][$f] = [
                'id' => (int) $s->id,
                'estat' => (string) $s->estat,
                'es_meva' => ((int) $s->solicitant_usuari_espai_id === $usuariEspaiId),
                'soc_cobridor' => ((int) $s->cobridor_usuari_espai_id === $usuariEspaiId),
            ];
        }

        return view('espai.guardies.index', compact(
            'espai', 'usuariEspai', 'franjes', 'dies', 'diesLabels', 'slots', 'solSlots'
        ));
    }

    public function solicitaGuardia(Request $request)
    {
        // ⭐ FREE BLOCK
        if (auth()->user()->plan === 'free') {
            return back()->with('error_modal', 'Les guàrdies són una funció Premium.');
        }

        $espaiId = (int) session('espai_id');
        abort_unless($espaiId, 403);

        $usuariEspaiId = (int) session('usuari_espai_id');
        abort_unless($usuariEspaiId, 403);

        $dia = (int) $request->query('dia');
        $franjaId = (int) $request->query('franja');

        abort_if($dia < 1 || $dia > 5, 422);

        $franja = FranjaHoraria::findOrFail($franjaId);

        return view('espai.guardies.solicitaGuardia', compact(
            'espaiId', 'usuariEspaiId', 'dia', 'franja'
        ));
    }

    public function guardarSolicitud(Request $request)
    {
        // ⭐ FREE BLOCK
        if (auth()->user()->plan === 'free') {
            return back()->with('error_modal', 'Les guàrdies són una funció Premium.');
        }

        $espaiId = (int) session('espai_id');
        $usuariEspaiId = (int) session('usuari_espai_id');

        $data = $request->validate([
            'dia' => ['required'],
            'franja_id' => ['required'],
            'comentari' => ['nullable'],
        ]);

        DB::transaction(function () use ($data, $espaiId, $usuariEspaiId) {

            GuardiaSolicitud::create([
                'espai_id' => $espaiId,
                'solicitant_usuari_espai_id' => $usuariEspaiId,
                'dia_setmana' => $data['dia'],
                'franja_horaria_id' => $data['franja_id'],
                'comentari' => $data['comentari'] ?? null,
                'estat' => 'pendent',
            ]);
        });

        return redirect()->route('espai.guardies.index')
            ->with('ok', 'Sol·licitud creada.');
    }

    public function acceptar(Request $request, GuardiaSolicitud $solicitud)
    {
        // ⭐ FREE BLOCK
        if (auth()->user()->plan === 'free') {
            return redirect()
                ->route('espai.guardies.index')
                ->with('error_modal', 'Les guàrdies són una funció Premium.');
        }

        $espaiId = (int) session('espai_id');
        $usuariEspaiId = (int) session('usuari_espai_id');

        abort_if($solicitud->espai_id !== $espaiId, 403);

        DB::transaction(function () use ($solicitud, $usuariEspaiId) {

            $solicitud->update([
                'estat' => 'acceptada',
                'cobridor_usuari_espai_id' => $usuariEspaiId,
            ]);
        });

        return redirect()->route('espai.guardies.index')
            ->with('ok', 'Guàrdia acceptada.');
    }
}