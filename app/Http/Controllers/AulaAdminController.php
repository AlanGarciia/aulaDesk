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

        abort_if((int) $aula->espai_id !== (int) $espaiId, 403);

        $rolProfessor = $this->professorRolValue();

        $professors = UsuariEspai::query()
            ->where('espai_id', $espaiId)
            ->where('rol', $rolProfessor)
            ->orderBy('nom')
            ->get();

        $grups = Grup::query()
            ->where('espai_id', $espaiId)
            ->orderBy('nom')
            ->get();

        $dies = [
            1 => 'Dilluns',
            2 => 'Dimarts',
            3 => 'Dimecres',
            4 => 'Dijous',
            5 => 'Divendres',
        ];

        $franges = FranjaHoraria::query()
            ->where('espai_id', $espaiId)
            ->orderBy('ordre')
            ->get();

        $assignacions = [];

        if ($franges->isNotEmpty()) {
            $slots = AulaHorario::query()
                ->where('aula_id', $aula->id)
                ->get();

            foreach ($slots as $s) {
                $dia = (int) $s->dia_setmana;
                $franjaId = (int) $s->franja_horaria_id;

                if (!isset($assignacions[$dia])) {
                    $assignacions[$dia] = [];
                }

                $assignacions[$dia][$franjaId] = [
                    'professor' => $s->usuari_espai_id,
                    'grup' => $s->grup_id ?? null,
                ];
            }
        }

        $ocupats = [];

        if ($franges->isNotEmpty()) {
            $franjaIds = $franges->pluck('id')->map(fn($v) => (int)$v)->all();

            $ocupacions = AulaHorario::query()
                ->whereNotNull('usuari_espai_id')
                ->whereIn('dia_setmana', array_keys($dies))
                ->whereIn('franja_horaria_id', $franjaIds)
                ->where('aula_id', '!=', $aula->id)
                ->whereHas('aula', fn($q) => $q->where('espai_id', $espaiId))
                ->with('aula')
                ->get();

            foreach ($ocupacions as $o) {
                $dia = (int) $o->dia_setmana;
                $franjaId = (int) $o->franja_horaria_id;
                $profId = (int) $o->usuari_espai_id;

                $ocupats[$dia][$franjaId][$profId] = $o->aula->nom ?? 'Altra aula';
            }
        }

        // Guardies acceptades
        $substituts = [];
        if ($franges->isNotEmpty()) {
            $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $weekEnd = Carbon::now()->endOfWeek(Carbon::SUNDAY);

            $guardies = GuardiaSolicitud::query()
                ->where('espai_id', $espaiId)
                ->where('aula_id', $aula->id)
                ->where('estat', 'acceptada')
                ->whereNotNull('cobridor_usuari_espai_id')
                ->whereBetween('updated_at', [$weekStart, $weekEnd])
                ->with('cobridor')
                ->get();

            foreach ($guardies as $g) {
                $d = (int) $g->dia_setmana;
                $f = (int) $g->franja_horaria_id;
                $nom = $g->cobridor->nom ?? '';

                if ($nom) {
                    $substituts[$d][$f] = $nom;
                }
            }
        }

        $tickets = Ticket::query()
            ->where('espai_id', $espaiId)
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
            'ocupats',
            'substituts',
            'grups'
        ));
    }

    public function update(Request $request, Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, 'No hi ha cap espai seleccionat.');
        }

        abort_if((int) $aula->espai_id !== (int) $espaiId, 403);

        $data = $request->validate([
            'assignacions' => ['required', 'array'],
            'assignacions.*' => ['array'],
        ]);

        $rolProfessor = $this->professorRolValue();

        $professors = UsuariEspai::query()
            ->where('espai_id', $espaiId)
            ->where('rol', $rolProfessor)
            ->orderBy('nom')
            ->get();

        $profIds = $professors->pluck('id')->map(fn($v) => (int)$v)->all();
        $profNoms = $professors->pluck('nom', 'id')->map(fn($v) => (string)$v)->all();

        $franges = FranjaHoraria::query()
            ->where('espai_id', $espaiId)
            ->orderBy('ordre')
            ->get();

        $franjaIds = $franges->pluck('id')->map(fn($v) => (int)$v)->all();
        $franjaLabels = [];
        foreach ($franges as $f) {
            $label = substr($f->inici, 0, 5) . ' - ' . substr($f->fi, 0, 5);
            if ($f->nom) $label = $f->nom . ' (' . $label . ')';
            $franjaLabels[(int)$f->id] = $label;
        }

        $dies = [1, 2, 3, 4, 5];
        $diesLabels = [1=>'Dilluns',2=>'Dimarts',3=>'Dimecres',4=>'Dijous',5=>'Divendres'];

        // Conflictes
        $conflicts = [];
        foreach ($dies as $dia) {
            foreach ($franjaIds as $franjaId) {
                $profId = $data['assignacions'][$dia][$franjaId] ?? null;

                if ($profId === '' || $profId === null) continue;
                $profId = (int) $profId;

                abort_if(!in_array($profId, $profIds, true), 422, 'Professor invàlid.');

                $ocupacio = AulaHorario::query()
                    ->where('usuari_espai_id', $profId)
                    ->where('dia_setmana', $dia)
                    ->where('franja_horaria_id', $franjaId)
                    ->where('aula_id', '!=', $aula->id)
                    ->whereHas('aula', fn($q) => $q->where('espai_id', $espaiId))
                    ->with('aula')
                    ->first();

                if ($ocupacio) {
                    $conflicts[] = [
                        'dia' => $diesLabels[$dia] ?? ('Dia '.$dia),
                        'franja' => $franjaLabels[$franjaId] ?? ('Franja '.$franjaId),
                        'professor' => $profNoms[$profId] ?? ('Professor #'.$profId),
                        'aula' => $ocupacio->aula->nom ?? 'Altra aula',
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

        // Guardar horari
        foreach ($dies as $dia) {
            foreach ($franjaIds as $franjaId) {
                $profId = $data['assignacions'][$dia][$franjaId] ?? null;

                if ($profId === '' || $profId === null) {
                    AulaHorario::updateOrCreate(
                        ['aula_id'=>$aula->id,'dia_setmana'=>$dia,'franja_horaria_id'=>$franjaId],
                        ['usuari_espai_id'=>null]
                    );
                    continue;
                }

                $profId = (int) $profId;
                abort_if(!in_array($profId, $profIds, true), 422, 'Professor invàlid.');

                AulaHorario::updateOrCreate(
                    ['aula_id'=>$aula->id,'dia_setmana'=>$dia,'franja_horaria_id'=>$franjaId],
                    ['usuari_espai_id'=>$profId]
                );
            }
        }

        return redirect()
            ->route('espai.aules.admin', $aula)
            ->with('ok', 'Horari desat.');
    }
}