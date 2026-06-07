<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AulaTicketController extends Controller
{

    public function index(Aula $aula)
    {
        $tickets = $aula->tickets()->where('estat', 'obert')->get();
        return view('espai.tickets.index', compact('aula', 'tickets'));
    }
    public function store(Request $request, Aula $aula)
    {

        $espaiId = session('espai_id');
        if (!$espaiId) abort(403, __('messages.no_active_space'));
        if ((int)$aula->espai_id !== (int)$espaiId) abort(403);

        $data = $request->validate([
            'titol' => ['required', 'string', 'max:255'],
            'descripcio' => ['nullable', 'string'],
            'prioritat' => ['required', 'in:baixa,mitja,alta'],
        ]);

        $usuariEspaiId = session('usuari_espai_id');
        if (!$usuariEspaiId) abort(403, __('messages.no_space_user'));

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

        return back()->with('ok', __('messages.ticket_created'));
    }

    public function update(Request $request, Aula $aula, Ticket $ticket)
    {
        $espaiId = session('espai_id');
        if (!$espaiId) abort(403, __('messages.no_active_space'));

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

        return back()->with('ok', __('messages.ticket_updated'));
    }

    public function destroy(Request $request, Aula $aula, Ticket $ticket)
    {
        $espaiId = session('espai_id');
        if (!$espaiId) abort(403, __('messages.no_active_space'));

        if ((int)$aula->espai_id !== (int)$espaiId) abort(403);
        if ((int)$ticket->espai_id !== (int)$espaiId) abort(403);
        if ((int)$ticket->aula_id !== (int)$aula->id) abort(404);

        $ticket->delete();

        return back()->with('ok', __('messages.ticket_deleted'));
    }

    public function showAula(Aula $aula)
    {
        $espaiId = (int) session('espai_id');
        abort_unless($espaiId, 403);
        abort_if((int) $aula->espai_id !== $espaiId, 403);

        $tickets = Ticket::where('espai_id', $espaiId)
            ->where('aula_id', $aula->id)
            ->with('creador')
            ->latest()
            ->get();

        return view('espai.aules.tickets', compact('aula', 'tickets'));
    }

    public function all(Request $request)
    {
        $espaiId = (int) session('espai_id');
        abort_unless($espaiId, 403);

        $query = \App\Models\Ticket::where('espai_id', $espaiId)
            ->with(['aula', 'creador'])
            ->latest();

        if ($request->filled('aula_id')) {
            $query->where('aula_id', (int) $request->input('aula_id'));
        }
        if ($request->filled('estat')) {
            $query->where('estat', $request->input('estat'));
        }
        if ($request->filled('prioritat')) {
            $query->where('prioritat', $request->input('prioritat'));
        }
        if ($request->filled('q')) {
            $q = (string) $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('titol', 'like', '%' . $q . '%')
                ->orWhere('descripcio', 'like', '%' . $q . '%');
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        $aules = \App\Models\Aula::where('espai_id', $espaiId)->orderBy('nom')->get();

        return view('espai.tickets.all', compact('tickets', 'aules'));
    }
}