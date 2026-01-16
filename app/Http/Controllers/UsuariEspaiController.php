<?php

namespace App\Http\Controllers;

use App\Models\Espai;
use App\Models\UsuariEspai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuariEspaiController extends Controller
{
    /**
     * Llista usuaris de l'espai actiu.
     */
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

        return view('espai.usuaris.index', [
            'espai' => $espai,
            'usuaris' => $usuaris,
        ]);
    }

    /**
     * Formulari per crear usuari dins l'espai actiu.
     */
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

    /**
     * Desa un usuari dins l'espai actiu.
     */
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
                'rol' => ['required', Rule::in(UsuariEspai::ROLS)],
                'contrasenya' => ['required', 'string', 'min:6', 'max:255'],
            ],
            [],
            [
                'nom' => 'nom',
                'rol' => 'rol',
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
            'rol' => $data['rol'],
            'contrasenya' => Hash::make($data['contrasenya']),
        ]);

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', 'Usuari creat correctament.');
    }
    public function edit(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        // Ha de pertànyer a l'espai actiu
        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        // I l'espai actiu ha de ser del propietari loguejat
        $espai = Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('espai.usuaris.edit', [
            'espai' => $espai,
            'usuariEspai' => $usuariEspai,
        ]);
    }

    public function update(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        // Ha de pertànyer a l'espai actiu
        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        // I l'espai actiu ha de ser del propietari loguejat
        $espai = Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'rol' => ['required', Rule::in(UsuariEspai::ROLS)],
                'contrasenya' => ['nullable', 'string', 'min:6', 'max:255'],
            ],
            [],
            [
                'nom' => 'nom',
                'rol' => 'rol',
                'contrasenya' => 'contrasenya',
            ]
        );

        // Evita duplicats de nom dins l'espai (excloent l'usuari actual)
        $exists = $espai->usuaris()
            ->where('nom', $data['nom'])
            ->where('id', '!=', $usuariEspai->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['nom' => 'Aquest nom ja existeix dins d’aquest espai.'])
                ->withInput();
        }

        // (Opcional) Protegir l'admin: no canviar-li el rol
        $isAdmin = ($usuariEspai->nom === 'admin' || $usuariEspai->rol === UsuariEspai::ROL_ADMIN);
        if ($isAdmin) {
            $data['rol'] = UsuariEspai::ROL_ADMIN;
        }

        $usuariEspai->nom = $data['nom'];
        $usuariEspai->rol = $data['rol'];

        if (!empty($data['contrasenya'])) {
            $usuariEspai->contrasenya = Hash::make($data['contrasenya']);
        }

        $usuariEspai->save();

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', 'Usuari actualitzat correctament.');
    }

    /**
     * Elimina un usuari de l'espai actiu.
     */
    public function destroy(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        // Ha de pertànyer a l'espai actiu
        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        // I l'espai actiu ha de ser del propietari loguejat
        Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $usuariEspai->delete();

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', 'Usuari eliminat correctament.');
    }
}
