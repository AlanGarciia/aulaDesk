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

    public function index()
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hay espai actual seleccionado.');

        $aules = Aula::where('espai_id', $espaiId)
            ->latest()
            ->paginate(15);

        return view('espai.aules.aules', compact('aules'));
    }

    public function create()
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hay espai actual seleccionado.');

        return view('espai.aules.create');
    }

    public function store(Request $request)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hay espai actual seleccionado.');

        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'codi' => ['nullable', 'string', 'max:50'],
                'capacitat' => ['nullable', 'integer', 'min:0'],
                'planta' => ['nullable', 'string', 'max:50'],
                'activa' => ['sometimes', 'boolean'],
            ]
        );

        $data['espai_id'] = $espaiId;
        $data['activa'] = $request->boolean('activa');

        Aula::create($data);

        return redirect()->route('espai.aules.index')->with('ok', 'Aula creada.');
    }

    public function edit(Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hay espai actual seleccionado.');

        // Seguridad: que no editen aulas de otro espai
        abort_if($aula->espai_id !== $espaiId, 403);

        return view('espai.aules.edit', compact('aula'));
    }

    public function update(Request $request, Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hay espai actual seleccionado.');
        abort_if($aula->espai_id !== $espaiId, 403);

        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'codi' => ['nullable', 'string', 'max:50'],
                'capacitat' => ['nullable', 'integer', 'min:0'],
                'planta' => ['nullable', 'string', 'max:50'],
                'activa' => ['sometimes', 'boolean'],
            ]
        );

        $data['activa'] = $request->boolean('activa');

        $aula->update($data);

        return redirect()->route('espai.aules.index')->with('ok', 'Aula actualizada.');
    }

    public function destroy(Aula $aula)
    {
        $espaiId = $this->currentEspaiId();
        if (!$espaiId) abort(403, 'No hay espai actual seleccionado.');
        abort_if($aula->espai_id !== $espaiId, 403);

        $aula->delete();

        return redirect()->route('espai.aules.index')->with('ok', 'Aula eliminada.');
    }
}