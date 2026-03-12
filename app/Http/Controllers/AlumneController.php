<?php

namespace App\Http\Controllers;

use App\Models\Alumne;
use App\Models\Espai;
use Illuminate\Http\Request;

class AlumneController extends Controller
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

        $query = $espai->alumnes();

        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }

        if ($request->filled('idalu')) {
            $query->where('idalu', 'like', '%' . $request->idalu . '%');
        }

        $alumnes = $query
            ->orderByRaw('LOWER(nom) ASC')
            ->paginate(20); // 👈 AQUÍ ESTÁ LA CLAVE

        return view('espai.alumnes.index', [
            'espai' => $espai,
            'alumnes' => $alumnes,
        ]);
    }


    public function create(Request $request)
    {
        $espai = $this->getEspai($request);

        return view('espai.alumnes.create', [
            'espai' => $espai,
        ]);
    }

    public function store(Request $request)
    {
        $espai = $this->getEspai($request);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'cognoms' => ['nullable', 'string', 'max:255'],
            'correu' => ['nullable', 'email', 'max:255'],
            'idalu' => ['required', 'string', 'size:11'],
            'telefon' => ['nullable', 'string', 'max:20'],
        ]);

        // Evitar duplicados
        if ($espai->alumnes()->where('idalu', $data['idalu'])->exists()) {
            return back()
                ->withErrors(['idalu' => 'Aquest IDALU ja existeix dins d’aquest espai.'])
                ->withInput();
        }

        $espai->alumnes()->create($data);

        return redirect()
            ->route('espai.alumnes.index')
            ->with('ok', 'Alumne creat correctament.');
    }

    public function destroy(Request $request, Alumne $alumne)
    {
        $espai = $this->getEspai($request);

        if ((int) $alumne->espai_id !== (int) $espai->id) {
            abort(404);
        }

        $alumne->delete();

        return redirect()
            ->route('espai.alumnes.index')
            ->with('ok', 'Alumne eliminat correctament.');
    }
}
