<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;

class AulaController extends Controller
{

    private function currentEspaiId(): ?int
    {
        $espaiId = session('espai_id');
        if ($espaiId) return (int) $espaiId;

        $espai = session('espai');
        if (is_array($espai) && isset($espai['id'])) return (int) $espai['id'];

        if (is_object($espai) && isset($espai->id)) return (int) $espai->id;

        $espaiIdAlt = session('espai_actual_id') ?? session('espaiId');
        if ($espaiIdAlt) return (int) $espaiIdAlt;

        return null;
    }

    public function index(Request $request)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) {
            abort(403, __('messages.no_space_selected'));
        }

        $query = Aula::where('espai_id', $espaiId);

        if ($request->nom) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }

        if ($request->codi) {
            $query->where('codi', 'like', '%' . $request->codi . '%');
        }

        if ($request->planta) {
            $query->where('planta', 'like', '%' . $request->planta . '%');
        }

        $aules = $query
            ->orderBy('codi', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('espai.aules.aules', compact('aules'));
    }

    public function create()
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, __('messages.no_space_selected'));

        return view('espai.aules.create');
    }

    public function store(Request $request)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, __('messages.no_space_selected'));

        if (
            auth()->user()->plan === 'free'
            && Aula::where('espai_id', $espaiId)->count() >= 10
        ) {
            return redirect()
                ->route('espai.aules.index')
                ->with('showLimitModal', true);
        }

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'codi' => ['nullable', 'string', 'max:50'],
            'capacitat' => ['nullable', 'integer', 'min:0'],
            'planta' => ['nullable', 'string', 'max:50'],
        ]);

        $data['espai_id'] = $espaiId;

        Aula::create($data);

        return redirect()
            ->route('espai.aules.index')
            ->with('ok', __('messages.classroom_created'));
    }

    public function edit(Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, __('messages.no_space_selected'));
        abort_if($aula->espai_id !== $espaiId, 403);

        return view('espai.aules.edit', compact('aula'));
    }


    public function update(Request $request, Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, __('messages.no_space_selected'));
        abort_if($aula->espai_id !== $espaiId, 403);

        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'codi' => ['nullable', 'string', 'max:50'],
                'capacitat' => ['nullable', 'integer', 'min:0'],
                'planta' => ['nullable', 'string', 'max:50'],
            ]
        );
        $aula->update($data);

        return redirect()->route('espai.aules.index')->with('ok', __('messages.classroom_updated'));
    }

    public function destroy(Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, __('messages.no_space_selected'));
        abort_if($aula->espai_id !== $espaiId, 403);

        $aula->delete();

        return redirect()->route('espai.aules.index')->with('ok', __('messages.classroom_deleted'));
    }
}