<?php

namespace App\Http\Controllers;

use App\Models\Espai;
use App\Models\UsuariEspai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariEspaiController extends Controller
{

    public function index(Request $request)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        $espai = Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $usuaris = $espai->usuaris()->latest()->get();

        return view('usuariEspai.index', [
            'espai' => $espai,
            'usuaris' => $usuaris,
        ]);
    }

    public function create(Request $request)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        $espai = Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('espai.usuaris.create', [
            'espai' => $espai,
        ]);

    }

    public function store(Request $request)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        $espai = Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'contrasenya' => ['required', 'string', 'min:6', 'max:255'],
            ],
            [],
            [
                'nom' => 'nom',
                'contrasenya' => 'contrasenya',
            ]
        );

        if ($espai->usuaris()->where('nom', $data['nom'])->exists()) {
            return back()
                ->withErrors(['nom' => 'Aquest nom ja existeix dins d’aquest espai.'])
                ->withInput();
        }

        $espai->usuaris()->create([
            'nom' => $data['nom'],
            'contrasenya' => Hash::make($data['contrasenya']),
        ]);

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', 'Usuari creat correctament.');
    }

    public function destroy(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        // Comprovació de seguretat: el registre pertany a l'espai actiu
        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }
        $espai = Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $usuariEspai->delete();

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', 'Usuari eliminat correctament.');
    }
}
