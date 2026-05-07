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
        $diesLabels = [1=>'Dl', 2=>'Dt', 3=>'Dc', 4=>'Dj', 5=>'Dv'];

        $horaris = AulaHorario::where('usuari_espai_id', $usuariEspaiId)
            ->whereIn('dia_setmana', $dies)
            ->with(['aula', 'franja'])
            ->get();

        $slots = [];
        foreach ($horaris as $h) {
            if (!$h->franja) continue;

            $dia = (int) $h->dia_setmana;
            $franjaId = (int) $h->franja->id;

            // Si ja hi ha una assignació per aquest slot, saltem
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

            $esMeva = ((int) $s->solicitant_usuari_espai_id === $usuariEspaiId);
            $socCobridor = false;
            $cobridorId = null;
            if ($s->cobridor_usuari_espai_id) {
                $socCobridor = ((int) $s->cobridor_usuari_espai_id === $usuariEspaiId);
                $cobridorId = (int) $s->cobridor_usuari_espai_id;
            }

            $estat = '';
            if ($s->estat) $estat = (string) $s->estat;

            $solSlots[$d][$f] = [
                'id' => (int) $s->id,
                'estat' => $estat,
                'es_meva' => $esMeva,
                'soc_cobridor' => $socCobridor,
                'cobridor_id' => $cobridorId,
            ];
        }

        return view('espai.guardies.index', compact(
            'espai', 'usuariEspai', 'franjes', 'dies', 'diesLabels', 'slots', 'solSlots'
        ));
    }

    public function solicitaGuardia(Request $request)
    {
        $espaiId = (int) session('espai_id');
        abort_unless($espaiId, 403);

        $usuariEspaiId = (int) session('usuari_espai_id');
        abort_unless($usuariEspaiId, 403);

        $dia = (int) $request->query('dia');
        $franjaId = (int) $request->query('franja');

        abort_if($dia < 1 || $dia > 5, 422, 'Dia invàlid.');
        abort_if($franjaId <= 0, 422, 'Franja invàlida.');

        $usuariEspai = UsuariEspai::findOrFail($usuariEspaiId);
        $espai = Espai::findOrFail($espaiId);

        $diesLabels = [1=>'Dilluns', 2=>'Dimarts', 3=>'Dimecres', 4=>'Dijous', 5=>'Divendres'];

        $diaLabel = '';
        if (isset($diesLabels[$dia])) $diaLabel = (string) $diesLabels[$dia];

        $franja = FranjaHoraria::where('espai_id', $espaiId)->where('id', $franjaId)->first();
        abort_unless($franja, 404, 'Franja no trobada.');

        $inici = '';
        $fi = '';
        if ($franja->inici) $inici = substr((string) $franja->inici, 0, 5);
        if ($franja->fi) $fi = substr((string) $franja->fi, 0, 5);

        $franjaLabel = $inici . ' - ' . $fi;
        if ($franja->nom) $franjaLabel = (string) $franja->nom . ' (' . $inici . ' - ' . $fi . ')';

        $horari = AulaHorario::where('usuari_espai_id', $usuariEspaiId)
            ->where('dia_setmana', $dia)
            ->where('franja_horaria_id', $franjaId)
            ->with('aula')
            ->first();

        abort_unless($horari, 422, 'No tens cap aula assignada en aquesta franja.');

        $aulaNom = 'Aula';
        $aulaId = null;
        if ($horari->aula && $horari->aula->nom) $aulaNom = (string) $horari->aula->nom;
        if ($horari->aula && $horari->aula->id) $aulaId = (int) $horari->aula->id;

        return view('espai.guardies.solicitaGuardia', [
            'espai' => $espai,
            'usuariEspai' => $usuariEspai,
            'dia' => $dia,
            'diaLabel' => $diaLabel,
            'diesLabels' => $diesLabels,
            'franja' => $franja,
            'franjaId' => $franjaId,
            'franjaLabel' => $franjaLabel,
            'aulaNom' => $aulaNom,
            'aulaId' => $aulaId,
        ]);
    }

    public function guardarSolicitud(Request $request)
    {
        $espaiId = (int) session('espai_id');
        abort_unless($espaiId, 403);

        $usuariEspaiId = (int) session('usuari_espai_id');
        abort_unless($usuariEspaiId, 403);

        $data = $request->validate([
            'dia' => ['required', 'integer', 'min:1', 'max:5'],
            'franja_id' => ['required', 'integer', 'min:1'],
            'tipus' => ['nullable', 'string', 'max:50'],
            'comentari' => ['nullable', 'string', 'max:1000'],
        ]);

        $dia = (int) $data['dia'];
        $franjaId = (int) $data['franja_id'];

        $franja = FranjaHoraria::where('espai_id', $espaiId)->where('id', $franjaId)->first();
        abort_unless($franja, 404, 'Franja no trobada.');

        $inici = substr($franja->inici, 0, 5);
        $fi = substr($franja->fi, 0, 5);

        $franjaNom = "$inici - $fi";
        if ($franja->nom) $franjaNom = $franja->nom . " ($inici - $fi)";

        $horari = AulaHorario::where('usuari_espai_id', $usuariEspaiId)
            ->where('dia_setmana', $dia)
            ->where('franja_horaria_id', $franjaId)
            ->first();

        abort_unless($horari, 422, 'No tens cap aula assignada en aquesta franja.');

        $aulaId = null;
        if ($horari->aula_id) $aulaId = (int) $horari->aula_id;

        $aulaNom = '-';
        if ($horari->aula && $horari->aula->nom) $aulaNom = $horari->aula->nom;

        $tipus = null;
        if (isset($data['tipus']) && $data['tipus'] !== '') $tipus = (string) $data['tipus'];

        $comentari = null;
        if (isset($data['comentari']) && trim((string) $data['comentari']) !== '') $comentari = (string) $data['comentari'];

        DB::transaction(function () use (
            $espaiId, $usuariEspaiId, $aulaId, $dia, $franjaId,
            $tipus, $comentari, $franjaNom, $aulaNom
        ) {
            $sol = GuardiaSolicitud::create([
                'espai_id' => $espaiId,
                'solicitant_usuari_espai_id' => $usuariEspaiId,
                'cobridor_usuari_espai_id' => null,
                'noticia_id' => null,
                'aula_id' => $aulaId,
                'dia_setmana' => $dia,
                'franja_horaria_id' => $franjaId,
                'tipus' => $tipus,
                'comentari' => $comentari,
                'estat' => 'pendent',
            ]);

            $dies = [1=>'Dilluns', 2=>'Dimarts', 3=>'Dimecres', 4=>'Dijous', 5=>'Divendres'];

            $diaTxt = 'Dia ' . $dia;
            if (isset($dies[$dia])) $diaTxt = (string) $dies[$dia];

            $titol = 'Guàrdia pendent (' . $diaTxt . ')';

            $cont = "S'ha solicitat una guàrdia.\n";
            $cont .= "(Dia: " . $diaTxt . "\n) ";
            $cont .= "(Franja: " . $franjaNom . ") ";
            $cont .= "(Aula: " . $aulaNom . ") ";
            if ($tipus) $cont .= "Tipus: " . $tipus . "\n";
            if ($comentari) $cont .= "Comentari: " . $comentari . "\n";
            $cont .= "\nUn altre professor pot acceptar-la des del tauló de notícies.";

            $noticia = Noticia::create([
                'espai_id' => $espaiId,
                'usuari_espai_id' => $usuariEspaiId,
                'tipus' => 'guardia',
                'titol' => $titol,
                'contingut' => $cont,
                'imatge_path' => null,
                'publicat_el' => now(),
            ]);

            $sol->noticia_id = (int) $noticia->id;
            $sol->save();

            // Notificar a la resta de membres del espai (campaneta)
            $solicitant = UsuariEspai::find($usuariEspaiId);
            $solicitantNom = 'Un professor';
            if ($solicitant && $solicitant->nom) $solicitantNom = $solicitant->nom;

            Notificacio::notifyEspai(
                (int) $espaiId,
                'guardia_solicitada',
                [
                    'titol' => $solicitantNom . ' ha demanat una guàrdia (' . $diaTxt . ')',
                    'missatge' => 'Aula ' . $aulaNom . ' · ' . $franjaNom,
                    'url' => route('espai.noticies.index', ['tipus' => 'guardia']),
                    'related_type' => GuardiaSolicitud::class,
                    'related_id' => (int) $sol->id,
                ],
                $usuariEspaiId,
                true
            );
        });

        return redirect()->route('espai.guardies.index')
            ->with('ok', 'Sol·licitud de guàrdia enviada i publicada al tauló.');
    }

    public function acceptar(Request $request, GuardiaSolicitud $solicitud)
    {
        $espaiId = (int) session('espai_id');
        abort_unless($espaiId, 403);

        $usuariEspaiId = (int) session('usuari_espai_id');
        abort_unless($usuariEspaiId, 403);

        abort_if((int) $solicitud->espai_id !== $espaiId, 403);
        abort_if((int) $solicitud->solicitant_usuari_espai_id === $usuariEspaiId, 422, 'No pots acceptar la teva pròpia guàrdia.');

        $result = DB::transaction(function () use ($solicitud, $usuariEspaiId, $espaiId) {

            $sol = GuardiaSolicitud::where('id', (int) $solicitud->id)
                ->lockForUpdate()->firstOrFail();

            if ((string) $sol->estat !== 'pendent') {
                return ['ok' => false, 'msg' => 'Aquesta guàrdia ja ha estat gestionada.'];
            }

            $dia = (int) $sol->dia_setmana;
            $franjaId = (int) $sol->franja_horaria_id;
            $aulaId = (int) $sol->aula_id;

            $ocupat = AulaHorario::where('dia_setmana', $dia)
                ->where('franja_horaria_id', $franjaId)
                ->where('usuari_espai_id', $usuariEspaiId)
                ->where('aula_id', '!=', $aulaId)
                ->whereHas('aula', function ($q) use ($espaiId) {
                    $q->where('espai_id', $espaiId);
                })->first();

            if ($ocupat) {
                return [
                    'ok' => false,
                    'msg' => 'Ja tens una aula assignada en aquesta franja. No pots cobrir aquesta guàrdia.',
                ];
            }

            $slot = AulaHorario::where('aula_id', $aulaId)
                ->where('dia_setmana', $dia)
                ->where('franja_horaria_id', $franjaId)
                ->lockForUpdate()->first();

            if (!$slot) {
                $slot = AulaHorario::create([
                    'aula_id' => $aulaId,
                    'dia_setmana' => $dia,
                    'franja_horaria_id' => $franjaId,
                ]);
            }

            $originalId = null;
            if ($slot && $slot->usuari_espai_id) $originalId = (int) $slot->usuari_espai_id;

            AulaHorario::updateOrCreate(
                ['aula_id' => $aulaId, 'dia_setmana' => $dia, 'franja_horaria_id' => $franjaId],
                ['usuari_espai_id' => $usuariEspaiId]
            );

            // Dura 7 dies
            $sol->estat = 'acceptada';
            $sol->cobridor_usuari_espai_id = $usuariEspaiId;
            $sol->original_usuari_espai_id = $originalId;
            if (!$originalId) $sol->original_usuari_espai_id = (int) $sol->solicitant_usuari_espai_id;
            $sol->revertir_el = Carbon::now()->addDays(7);
            $sol->updated_at = now();
            $sol->save();

            return [
                'ok' => true,
                'msg' => 'Has acceptat la guàrdia i s’ha actualitzat l’horari.',
                'sol' => $sol,
            ];
        });

        // Si l'acceptació ha estat correcta, notifiquem
        if (isset($result['ok']) && $result['ok'] && isset($result['sol'])) {
            $sol = $result['sol'];

            $cobridor = UsuariEspai::find($usuariEspaiId);
            $cobridorNom = 'Un professor';
            if ($cobridor && $cobridor->nom) $cobridorNom = $cobridor->nom;

            $solicitant = UsuariEspai::find((int) $sol->solicitant_usuari_espai_id);
            $solicitantNom = 'el professor';
            if ($solicitant && $solicitant->nom) $solicitantNom = $solicitant->nom;

            $diesLabels = [1=>'Dilluns', 2=>'Dimarts', 3=>'Dimecres', 4=>'Dijous', 5=>'Divendres'];
            $diaTxt = 'Dia ' . (int) $sol->dia_setmana;
            if (isset($diesLabels[(int) $sol->dia_setmana])) $diaTxt = $diesLabels[(int) $sol->dia_setmana];

            Notificacio::notifyEspai(
                (int) $espaiId,
                'guardia_acceptada',
                [
                    'titol' => $cobridorNom . ' cobrirà la guàrdia de ' . $solicitantNom,
                    'missatge' => $diaTxt . ' · suplència acceptada',
                    'url' => route('espai.noticies.index', ['tipus' => 'guardia']),
                    'related_type' => GuardiaSolicitud::class,
                    'related_id' => (int) $sol->id,
                ],
                $usuariEspaiId,
                true
            );
        }

        if (isset($result['ok']) && $result['ok']) {
            return redirect()->route('espai.noticies.index', ['tipus' => 'guardia'])
                ->with('ok', $result['msg']);
        }

        $msg = 'No s\'ha pogut acceptar la guàrdia.';
        if (isset($result['msg'])) $msg = $result['msg'];

        return redirect()->route('espai.noticies.index', ['tipus' => 'guardia'])
            ->with('error_modal', $msg);
    }
}