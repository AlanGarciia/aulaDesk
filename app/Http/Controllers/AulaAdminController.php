<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\AulaHorario;
use App\Models\UsuariEspai;
use App\Models\FranjaHoraria;
use App\Models\Ticket;
use App\Models\GuardiaSolicitud;
use App\Models\Grup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

    private function professorRolValue(): string
    {
        if (defined('\App\Models\UsuariEspai::ROL_PROFESSOR')) return UsuariEspai::ROL_PROFESSOR;
        return 'professor';
    }

    public function show(Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hi ha cap espai seleccionat.');
        abort_if((int) $aula->espai_id !== (int) $espaiId, 403);

        $rolProfessor = $this->professorRolValue();

        $professors = UsuariEspai::where('espai_id', $espaiId)->where('rol', $rolProfessor)->orderBy('nom')->get();
        $grups = Grup::where('espai_id', $espaiId)->orderBy('nom')->get();
        $franges = FranjaHoraria::where('espai_id', $espaiId)->orderBy('ordre')->get();

        $dies = [1=>'Dilluns', 2=>'Dimarts', 3=>'Dimecres', 4=>'Dijous', 5=>'Divendres'];

        $assignacions = [];
        $ocupats = [];
        $substituts = [];

        if ($franges->isNotEmpty()) {

            $slots = AulaHorario::where('aula_id', $aula->id)->get();
            foreach ($slots as $s) {
                $assignacions[(int) $s->dia_setmana][(int) $s->franja_horaria_id] = [
                    'professor' => $s->usuari_espai_id,
                    'grup' => $s->grup_id,
                ];
            }

            $franjaIds = [];
            foreach ($franges as $f) $franjaIds[] = (int) $f->id;

            $ocupacions = AulaHorario::whereNotNull('usuari_espai_id')
                ->whereIn('dia_setmana', array_keys($dies))
                ->whereIn('franja_horaria_id', $franjaIds)
                ->where('aula_id', '!=', $aula->id)
                ->whereHas('aula', function ($q) use ($espaiId) {
                    $q->where('espai_id', $espaiId);
                })
                ->with('aula')
                ->get();

            foreach ($ocupacions as $o) {
                $nom = 'Altra aula';
                if ($o->aula && $o->aula->nom) $nom = $o->aula->nom;
                $ocupats[(int) $o->dia_setmana][(int) $o->franja_horaria_id][(int) $o->usuari_espai_id] = $nom;
            }

            // Substituts (guàrdies acceptades aquesta setmana)
            $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $weekEnd = Carbon::now()->endOfWeek(Carbon::SUNDAY);

            $guardies = GuardiaSolicitud::where('espai_id', $espaiId)
                ->where('aula_id', $aula->id)
                ->where('estat', 'acceptada')
                ->whereNotNull('cobridor_usuari_espai_id')
                ->whereBetween('updated_at', [$weekStart, $weekEnd])
                ->with('cobridor')
                ->get();

            foreach ($guardies as $g) {
                $nom = '';
                if ($g->cobridor && $g->cobridor->nom) $nom = $g->cobridor->nom;
                if ($nom) $substituts[(int) $g->dia_setmana][(int) $g->franja_horaria_id] = $nom;
            }
        }

        $tickets = Ticket::where('espai_id', $espaiId)->where('aula_id', $aula->id)->with('creador')->latest()->get();

        return view('espai.aules.admin', compact(
            'aula', 'professors', 'assignacions', 'dies', 'franges',
            'tickets', 'ocupats', 'substituts', 'grups'
        ));
    }

    public function update(Request $request, Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hi ha cap espai seleccionat.');
        abort_if((int) $aula->espai_id !== (int) $espaiId, 403);

        $data = $request->validate([
            'assignacions' => ['required', 'array'],
            'assignacions.*' => ['array'],
        ]);

        $rolProfessor = $this->professorRolValue();

        $professors = UsuariEspai::where('espai_id', $espaiId)->where('rol', $rolProfessor)->orderBy('nom')->get();
        $profIds = [];
        $profNoms = [];
        foreach ($professors as $p) {
            $profIds[] = (int) $p->id;
            $profNoms[(int) $p->id] = (string) $p->nom;
        }

        $franges = FranjaHoraria::where('espai_id', $espaiId)->orderBy('ordre')->get();
        $franjaIds = [];
        $franjaLabels = [];
        foreach ($franges as $f) {
            $id = (int) $f->id;
            $franjaIds[] = $id;
            $label = substr($f->inici, 0, 5) . ' - ' . substr($f->fi, 0, 5);
            if ($f->nom) $label = $f->nom . ' (' . $label . ')';
            $franjaLabels[$id] = $label;
        }

        $dies = [1, 2, 3, 4, 5];
        $diesLabels = [1=>'Dilluns', 2=>'Dimarts', 3=>'Dimecres', 4=>'Dijous', 5=>'Divendres'];
        $conflicts = [];
        foreach ($dies as $dia) {
            foreach ($franjaIds as $franjaId) {

                $profId = null;
                if (isset($data['assignacions'][$dia][$franjaId])) $profId = $data['assignacions'][$dia][$franjaId];

                if ($profId === '' || $profId === null) continue;
                $profId = (int) $profId;
                abort_if(!in_array($profId, $profIds, true), 422, 'Professor invàlid.');

                $ocupacio = AulaHorario::where('usuari_espai_id', $profId)
                    ->where('dia_setmana', $dia)
                    ->where('franja_horaria_id', $franjaId)
                    ->where('aula_id', '!=', $aula->id)
                    ->whereHas('aula', function ($q) use ($espaiId) {
                        $q->where('espai_id', $espaiId);
                    })
                    ->with('aula')
                    ->first();

                if (!$ocupacio) continue;

                $diaTxt = 'Dia ' . $dia;
                if (isset($diesLabels[$dia])) $diaTxt = $diesLabels[$dia];

                $franjaTxt = 'Franja ' . $franjaId;
                if (isset($franjaLabels[$franjaId])) $franjaTxt = $franjaLabels[$franjaId];

                $profTxt = 'Professor #' . $profId;
                if (isset($profNoms[$profId])) $profTxt = $profNoms[$profId];

                $aulaTxt = 'Altra aula';
                if ($ocupacio->aula && $ocupacio->aula->nom) $aulaTxt = $ocupacio->aula->nom;

                $conflicts[] = [
                    'dia' => $diaTxt,
                    'franja' => $franjaTxt,
                    'professor' => $profTxt,
                    'aula' => $aulaTxt,
                ];
            }
        }

        if (!empty($conflicts)) {
            return redirect()->route('espai.aules.admin', $aula)->withInput()->with('conflicts', $conflicts);
        }

        foreach ($dies as $dia) {
            foreach ($franjaIds as $franjaId) {

                $profId = null;
                if (isset($data['assignacions'][$dia][$franjaId])) $profId = $data['assignacions'][$dia][$franjaId];

                $usuariId = null;
                if ($profId !== '' && $profId !== null) {
                    $profId = (int) $profId;
                    abort_if(!in_array($profId, $profIds, true), 422, 'Professor invàlid.');
                    $usuariId = $profId;
                }

                AulaHorario::updateOrCreate(
                    ['aula_id' => $aula->id, 'dia_setmana' => $dia, 'franja_horaria_id' => $franjaId],
                    ['usuari_espai_id' => $usuariId]
                );
            }
        }

        return redirect()->route('espai.aules.admin', $aula)->with('ok', 'Horari desat.');
    }
}