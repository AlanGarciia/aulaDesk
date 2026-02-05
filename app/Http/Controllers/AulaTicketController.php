<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AulaTicketController extends Controller
{
    public function store(Request $request, Aula $aula)
    {

        $espaiId = session('espai_id');
        if (!$espaiId) abort(403, 'No hi ha cap espai actiu a la sessió.');
        if ((int)$aula->espai_id !== (int)$espaiId) abort(403);

        $data = $request->validate([
            'titol' => ['required', 'string', 'max:255'],
            'descripcio' => ['nullable', 'string'],
            'prioritat' => ['required', 'in:baixa,mitja,alta'],
        ]);

        $usuariEspaiId = session('usuari_espai_id');
        if (!$usuariEspaiId) abort(403, 'No hi ha usuari d’espai a la sessió.');

        Ticket::create([
            'espai_id' => $espaiId,
            'aula_id' => $aula->id,
            'creat_per_usuari_espai_id' => (int)$usuariEspaiId,
            'titol' => $data['titol'],
            'descripcio' => $data['descripcio'] ?? null,
            'prioritat' => $data['prioritat'],
            'estat' => 'obert',
            'tancat_at' => null,
        ]);

        return back()->with('ok', 'Ticket creat.');
    }

    public function update(Request $request, Aula $aula, Ticket $ticket)
    {
        $espaiId = session('espai_id');
        if (!$espaiId) abort(403, 'No hi ha cap espai actiu a la sessió.');

        //seguridad: aula y ticket deben ser del espai actual y coincidir
        if ((int)$aula->espai_id !== (int)$espaiId) abort(403);
        if ((int)$ticket->espai_id !== (int)$espaiId) abort(403);
        if ((int)$ticket->aula_id !== (int)$aula->id) abort(404);

        $data = $request->validate([
            'estat' => ['required', 'in:obert,en_proces,tancat'],
            'prioritat' => ['nullable', 'in:baixa,mitja,alta'],
            'titol' => ['nullable', 'string', 'max:255'],
            'descripcio' => ['nullable', 'string'],
        ]);

        if (isset($data['estat'])) {
            $ticket->estat = $data['estat'];
            $ticket->tancat_at = $data['estat'] === 'tancat' ? now() : null;
        }

        if (array_key_exists('prioritat', $data) && $data['prioritat'] !== null) {
            $ticket->prioritat = $data['prioritat'];
        }

        if (array_key_exists('titol', $data) && $data['titol'] !== null) {
            $ticket->titol = $data['titol'];
        }

        if (array_key_exists('descripcio', $data)) {
            $ticket->descripcio = $data['descripcio'];
        }

        $ticket->save();

        return back()->with('ok', 'Ticket actualitzat.');
    }

    public function destroy(Request $request, Aula $aula, Ticket $ticket)
    {
        $espaiId = session('espai_id');
        if (!$espaiId) abort(403, 'No hi ha cap espai actiu a la sessió.');

        if ((int)$aula->espai_id !== (int)$espaiId) abort(403);
        if ((int)$ticket->espai_id !== (int)$espaiId) abort(403);
        if ((int)$ticket->aula_id !== (int)$aula->id) abort(404);

        $ticket->delete();

        return back()->with('ok', 'Ticket eliminat.');
    }
}
