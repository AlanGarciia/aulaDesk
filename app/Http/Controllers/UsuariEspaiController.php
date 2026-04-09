<?php

namespace App\Http\Controllers;

use App\Models\BaseRole;
use App\Models\Espai;
use App\Models\UsuariEspai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

        $query = $espai->usuaris();

        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        $usuaris = $query
            ->orderByRaw('LOWER(nom) ASC')
            ->get();

        return view('espai.usuaris.index', [
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
            'baseRoles' => BaseRole::where('espai_id', $espai->id)->get(),
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

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'rol' => ['required', Rule::in(BaseRole::where('espai_id', $espaiId)->pluck('nom')->toArray())],
            'contrasenya' => ['required', 'string', 'min:6', 'max:255'],
        ]);

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

        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        $espai = Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('espai.usuaris.edit', [
            'espai' => $espai,
            'usuariEspai' => $usuariEspai,
            'baseRoles' => BaseRole::where('espai_id', $espai->id)->get(),
        ]);
    }

    public function update(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        $espai = Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'rol' => ['required', Rule::in(BaseRole::where('espai_id', $espaiId)->pluck('nom')->toArray())],
            'contrasenya' => ['nullable', 'string', 'min:6', 'max:255'],
        ]);

        $exists = $espai->usuaris()
            ->where('nom', $data['nom'])
            ->where('id', '!=', $usuariEspai->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['nom' => 'Aquest nom ja existeix dins d’aquest espai.'])
                ->withInput();
        }

        // Admin protegido
        $isAdmin = ($usuariEspai->nom === 'admin' || $usuariEspai->rol === 'admin');
        if ($isAdmin) {
            $data['rol'] = 'admin';
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

    public function destroy(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', 'Selecciona un espai per continuar.');
        }

        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        Espai::where('id', $espaiId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $usuariEspai->delete();

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', 'Usuari eliminat correctament.');
    }

    /* ---------------------------------------------------------
     *  ROLES DINÁMICOS AVANZADOS
     * --------------------------------------------------------- */

    public function assignRolesForm(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        $espai = Espai::findOrFail($espaiId);

        return view('espai.usuaris.assignRoles', [
            'usuari' => $usuariEspai,
            'roles' => BaseRole::where('espai_id', $espaiId)->get(),
        ]);
    }


    public function assignRoles(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        $usuariEspai->roles()->sync($request->roles ?? []);

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', 'Rols actualitzats correctament.');
    }
}
