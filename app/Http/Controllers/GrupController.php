<?php

namespace App\Http\Controllers;

use App\Models\Espai;
use App\Models\Grup;
use Illuminate\Http\Request;

class GrupController extends Controller
{
    private function getEspai(Request $request)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        return Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $espai = $this->getEspai($request);

        $grups = $espai->grups()->orderBy('nom')->get();

        return view('espai.grups.index', compact('espai', 'grups'));
    }

    public function create(Request $request)
    {
        $espai = $this->getEspai($request);

        return view('espai.grups.create', compact('espai'));
    }

    public function store(Request $request)
    {
        $espai = $this->getEspai($request);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
        ]);

        $espai->grups()->create($data);

        return redirect()->route('espai.grups.index')
            ->with('status', 'Grup creat correctament.');
    }

    public function edit(Request $request, Grup $grup)
    {
        $espai = $this->getEspai($request);

        if ($grup->espai_id !== $espai->id) {
            abort(404);
        }

        $usuaris = $espai->usuaris()->orderBy('nom')->get();

        return view('espai.grups.edit', compact('espai', 'grup', 'usuaris'));
    }

    public function update(Request $request, Grup $grup)
    {
        $espai = $this->getEspai($request);

        if ($grup->espai_id !== $espai->id) {
            abort(404);
        }

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'usuaris' => ['array'],
        ]);

        $grup->update(['nom' => $data['nom']]);

        // Sincronizar usuarios del grupo
        $grup->usuaris()->sync($data['usuaris'] ?? []);

        return redirect()->route('espai.grups.index')
            ->with('status', 'Grup actualitzat correctament.');
    }

    public function destroy(Request $request, Grup $grup)
    {
        $espai = $this->getEspai($request);

        if ($grup->espai_id !== $espai->id) {
            abort(404);
        }

        $grup->delete();

        return redirect()->route('espai.grups.index')
            ->with('status', 'Grup eliminat correctament.');
    }
}

