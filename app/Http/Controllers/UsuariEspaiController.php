<?php

namespace App\Http\Controllers;

use App\Models\BaseRole;
use App\Models\Espai;
use App\Models\UsuariEspai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Notificacio;


class UsuariEspaiController extends Controller
{
    public function index(Request $request)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', __('messages.select_space_first'));
        }

        $espai = Espai::findOrFail($espaiId);

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
                ->with('status', __('messages.select_space_first'));
        }

        $espai = Espai::findOrFail($espaiId);

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
                ->with('status', __('messages.select_space_first'));
        }

        $espai = Espai::findOrFail($espaiId);

        // LIMIT PLAN FREE
        if (
            auth()->user()->plan === 'free'
            &&
            UsuariEspai::where('espai_id', $espai->id)->count() >= 3
        ) {
            return redirect()->route('espai.usuaris.index')->with('showPlanModal', true);
        }

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'rol' => ['required', Rule::in(
                BaseRole::where('espai_id', $espaiId)->pluck('nom')->toArray()
            )],
            'contrasenya' => ['required', 'string', 'min:6', 'max:255'],
        ]);

        if ($espai->usuaris()->where('nom', $data['nom'])->exists()) {
            return back()
                ->withErrors(['nom' => __('messages.user_name_exists')])
                ->withInput();
        }

        $usuari = $espai->usuaris()->create([
            'nom' => $data['nom'],
            'rol' => $data['rol'],
            'contrasenya' => $data['contrasenya'],
        ]);

        $baseRole = BaseRole::where('espai_id', $espaiId)
            ->where('nom', $data['rol'])
            ->first();

        if ($baseRole) {
            $usuari->roles()->syncWithoutDetaching([$baseRole->id]);
        }

        $actorId = (int) $request->session()->get('usuari_espai_id');

        Notificacio::notifyEspai(
            (int) $espai->id,
            'usuari_nou',
            [
                'titol' => __('messages.user_new_notif', ['name' => $usuari->nom]),
                'missatge' => __('messages.role') . ': ' . $usuari->rol,
                'url' => route('espai.usuaris.index'),
                'related_type' => UsuariEspai::class,
                'related_id' => (int) $usuari->id,
            ],
            $actorId ?: null,
            true
        );

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', __('messages.user_created_ok'));
    }

    public function edit(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', __('messages.select_space_first'));
        }

        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        $espai = Espai::findOrFail($espaiId);

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
                ->with('status', __('messages.select_space_first'));
        }

        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        $espai = Espai::findOrFail($espaiId);

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
                ->withErrors(['nom' => __('messages.user_name_exists')])
                ->withInput();
        }

        $isAdmin = ($usuariEspai->nom === 'admin' || $usuariEspai->rol === 'admin');
        if ($isAdmin) {
            $data['rol'] = 'admin';
        }

        $usuariEspai->nom = $data['nom'];
        $usuariEspai->rol = $data['rol'];

        // Mutator hashea automáticamente
        if (!empty($data['contrasenya'])) {
            $usuariEspai->contrasenya = $data['contrasenya'];
        }

        $usuariEspai->save();

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', __('messages.user_updated_ok'));
    }

    public function destroy(Request $request, UsuariEspai $usuariEspai)
    {
        $espaiId = $request->session()->get('espai_id');

        if (!$espaiId) {
            return redirect()->route('espais.index')
                ->with('status', __('messages.select_space_first'));
        }

        if ((int) $usuariEspai->espai_id !== (int) $espaiId) {
            abort(404);
        }

        $espai = Espai::findOrFail($espaiId);

        $usuariEspai->delete();

        return redirect()
            ->route('espai.usuaris.index')
            ->with('status', __('messages.user_deleted_ok'));
    }

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
            ->with('status', __('messages.roles_updated_ok'));
    }
}