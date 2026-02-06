<?php

namespace App\Http\Controllers;

use App\Models\AulaHorario;
use App\Models\Espai;
use App\Models\FranjaHoraria;
use Illuminate\Http\Request;
use App\Models\UsuariEspai;
use App\Models\GuardiaSolicitud;
use App\Models\Noticia;
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
        $espai = Espai::query()->findOrFail($espaiId);

        $franjes = FranjaHoraria::query()
            ->where('espai_id', $espaiId)
            ->orderBy('ordre')
            ->orderBy('inici')
            ->get();

        $dies = [1, 2, 3, 4, 5];

        $diesLabels = [
            1 => 'Dl',
            2 => 'Dt',
            3 => 'Dc',
            4 => 'Dj',
            5 => 'Dv',
        ];

        // Horari assignat del professor (base)
        $horaris = AulaHorario::query()
            ->where('usuari_espai_id', $usuariEspaiId)
            ->whereIn('dia_setmana', $dies)
            ->with(['aula', 'franja'])
            ->get();

        $slots = [];

        foreach ($horaris as $h) {
            if (!$h->franja) {
                continue;
            }

            $dia = (int) $h->dia_setmana;
            $franjaId = (int) $h->franja->id;

            if (isset($slots[$dia]) && isset($slots[$dia][$franjaId])) {
                continue;
            }

            $aulaNom = 'Aula';
            $aulaId = null;

            if ($h->aula && isset($h->aula->nom) && $h->aula->nom !== '') {
                $aulaNom = (string) $h->aula->nom;
            }
            if ($h->aula && isset($h->aula->id)) {
                $aulaId = (int) $h->aula->id;
            }

            if (!isset($slots[$dia])) {
                $slots[$dia] = [];
            }

            $slots[$dia][$franjaId] = [
                'aula' => $aulaNom,
                'aula_id' => $aulaId,
            ];
        }

        // Solicitudes que afectan a mi horario:
        // - las que yo he pedido (solicitant = yo)
        // - las que yo he aceptado (cobridor = yo)
        $sols = GuardiaSolicitud::query()
            ->where('espai_id', $espaiId)
            ->where(function ($q) use ($usuariEspaiId) {
                $q->where('solicitant_usuari_espai_id', $usuariEspaiId)
                    ->orWhere('cobridor_usuari_espai_id', $usuariEspaiId);
            })
            ->get();

        // [dia][franja_id] => info solicitud
        $solSlots = [];

        foreach ($sols as $s) {
            $d = (int) $s->dia_setmana;
            $f = (int) $s->franja_horaria_id;

            if (!isset($solSlots[$d])) {
                $solSlots[$d] = [];
            }

            $esMeva = ((int) $s->solicitant_usuari_espai_id === $usuariEspaiId);
            $socCobridor = false;

            if (isset($s->cobridor_usuari_espai_id) && $s->cobridor_usuari_espai_id) {
                $socCobridor = ((int) $s->cobridor_usuari_espai_id === $usuariEspaiId);
            }

            $cobridorId = null;
            if (isset($s->cobridor_usuari_espai_id) && $s->cobridor_usuari_espai_id) {
                $cobridorId = (int) $s->cobridor_usuari_espai_id;
            }

            $solSlots[$d][$f] = [
                'id' => (int) $s->id,
                'estat' => isset($s->estat) && $s->estat ? (string) $s->estat : '',
                'es_meva' => $esMeva,
                'soc_cobridor' => $socCobridor,
                'cobridor_id' => $cobridorId,
            ];
        }

        return view('espai.guardies.index', compact(
            'espai',
            'usuariEspai',
            'franjes',
            'dies',
            'diesLabels',
            'slots',
            'solSlots'
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
        $espai = Espai::query()->findOrFail($espaiId);

        $diesLabels = [
            1 => 'Dilluns',
            2 => 'Dimarts',
            3 => 'Dimecres',
            4 => 'Dijous',
            5 => 'Divendres',
        ];

        $diaLabel = '';
        if (isset($diesLabels[$dia])) {
            $diaLabel = (string) $diesLabels[$dia];
        }

        $franja = FranjaHoraria::query()
            ->where('espai_id', $espaiId)
            ->where('id', $franjaId)
            ->first();

        abort_unless($franja, 404, 'Franja no trobada.');

        $inici = '';
        $fi = '';

        if (isset($franja->inici)) {
            $inici = substr((string) $franja->inici, 0, 5);
        }
        if (isset($franja->fi)) {
            $fi = substr((string) $franja->fi, 0, 5);
        }

        $franjaLabel = $inici . ' - ' . $fi;
        if (isset($franja->nom) && $franja->nom) {
            $franjaLabel = (string) $franja->nom . ' (' . $inici . ' - ' . $fi . ')';
        }

        // ✅ CORRECCIÓN: el campo es dia_setmana (no dia_setwana)
        $horari = AulaHorario::query()
            ->where('usuari_espai_id', $usuariEspaiId)
            ->where('dia_setmana', $dia)
            ->where('franja_horaria_id', $franjaId)
            ->with('aula')
            ->first();

        abort_unless($horari, 422, 'No tens cap aula assignada en aquesta franja.');

        $aulaNom = 'Aula';
        $aulaId = null;

        if ($horari->aula && isset($horari->aula->nom) && $horari->aula->nom !== '') {
            $aulaNom = (string) $horari->aula->nom;
        }
        if ($horari->aula && isset($horari->aula->id)) {
            $aulaId = (int) $horari->aula->id;
        }

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

        $franja = FranjaHoraria::query()
            ->where('espai_id', $espaiId)
            ->where('id', $franjaId)
            ->first();

        abort_unless($franja, 404, 'Franja no trobada.');

        $horari = AulaHorario::query()
            ->where('usuari_espai_id', $usuariEspaiId)
            ->where('dia_setmana', $dia)
            ->where('franja_horaria_id', $franjaId)
            ->first();

        abort_unless($horari, 422, 'No tens cap aula assignada en aquesta franja.');

        $aulaId = null;
        if (isset($horari->aula_id) && $horari->aula_id) {
            $aulaId = (int) $horari->aula_id;
        }

        $tipus = null;
        if (isset($data['tipus']) && $data['tipus'] !== '') {
            $tipus = (string) $data['tipus'];
        }

        $comentari = null;
        if (isset($data['comentari']) && trim((string) $data['comentari']) !== '') {
            $comentari = (string) $data['comentari'];
        }

        DB::transaction(function () use (
            $espaiId,
            $usuariEspaiId,
            $aulaId,
            $dia,
            $franjaId,
            $tipus,
            $comentari
        ) {
            $sol = GuardiaSolicitud::query()->create([
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

            $dies = [
                1 => 'Dilluns',
                2 => 'Dimarts',
                3 => 'Dimecres',
                4 => 'Dijous',
                5 => 'Divendres',
            ];

            $diaTxt = 'Dia ' . (string) $dia;
            if (isset($dies[$dia])) {
                $diaTxt = (string) $dies[$dia];
            }

            $titol = 'Guàrdia pendent (' . $diaTxt . ')';

            // Campos reales: titol + contingut
            $cont = "S'ha sol·licitat una guàrdia.\n";
            $cont .= "Dia: " . $diaTxt . "\n";
            $cont .= "Franja ID: " . (string) $franjaId . "\n";
            $cont .= "Aula ID: " . ($aulaId ? (string) $aulaId : '-') . "\n";

            if ($tipus) {
                $cont .= "Tipus: " . $tipus . "\n";
            }
            if ($comentari) {
                $cont .= "Comentari: " . $comentari . "\n";
            }

            $cont .= "\nUn altre professor pot acceptar-la des del tauló de notícies.";

            $noticia = Noticia::query()->create([
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
        });

        return redirect()
            ->route('espai.guardies.index')
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

        // 1) Bloquea y verifica que sigue pendiente
        $sol = GuardiaSolicitud::query()
            ->where('id', (int) $solicitud->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ((string) $sol->estat !== 'pendent') {
            return ['ok' => false, 'msg' => 'Aquesta guàrdia ja ha estat gestionada.'];
        }

        $dia = (int) $sol->dia_setmana;
        $franjaId = (int) $sol->franja_horaria_id;
        $aulaId = (int) $sol->aula_id;

        // 2) El cobridor NO puede estar ya en otra aula ese día/franja
        $ocupat = AulaHorario::query()
            ->where('dia_setmana', $dia)
            ->where('franja_horaria_id', $franjaId)
            ->where('usuari_espai_id', $usuariEspaiId)
            ->where('aula_id', '!=', $aulaId)
            ->whereHas('aula', function ($q) use ($espaiId) {
                $q->where('espai_id', $espaiId);
            })
            ->first();

        abort_if($ocupat, 422, 'Ja tens una aula assignada en aquesta franja. No pots cobrir aquesta guàrdia.');

        // 3) Busca el registro de horario del aula en ese slot
        $slot = AulaHorario::query()
            ->where('aula_id', $aulaId)
            ->where('dia_setmana', $dia)
            ->where('franja_horaria_id', $franjaId)
            ->lockForUpdate()
            ->first();

        // Si no existe, lo creamos (por seguridad)
        if (!$slot) {
            $slot = AulaHorario::query()->create([
                'aula_id' => $aulaId,
                'dia_setwana' => $dia, // <-- NO. tu campo es dia_setmana, lo ponemos bien abajo
            ]);
        }

        // (por si tuviste el typo en algún sitio viejo)
        if (isset($slot->dia_setwana)) {
            // nada, solo para que lo veas: tu DB debe tener dia_setmana
        }

        // 4) Guarda quién era el profe original y cambia al cobridor
        $originalId = $slot ? (int) $slot->usuari_espai_id : null;

        // Si tu tabla tiene el campo correcto, actualizamos/creamos bien:
        AulaHorario::updateOrCreate(
            [
                'aula_id' => $aulaId,
                'dia_setmana' => $dia,
                'franja_horaria_id' => $franjaId,
            ],
            [
                'usuari_espai_id' => $usuariEspaiId,
            ]
        );

        // 5) Marca la solicitud como aceptada + guarda original + fecha de reversión (7 días)
        $sol->estat = 'acceptada';
        $sol->cobridor_usuari_espai_id = $usuariEspaiId;
        $sol->original_usuari_espai_id = $originalId ?: (int) $sol->solicitant_usuari_espai_id;
        $sol->revertir_el = Carbon::now()->addDays(7);
        $sol->updated_at = now();
        $sol->save();

        return ['ok' => true, 'msg' => 'Has acceptat la guàrdia i s’ha actualitzat l’horari.'];
    });

    return redirect()
        ->route('espai.noticies.index', ['tipus' => 'guardia'])
        ->with('ok', $result['msg']);
}

}