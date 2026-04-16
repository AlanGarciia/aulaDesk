<?php

namespace App\Http\Controllers;

use App\Models\BaseRole;
use App\Models\Espai;
use Illuminate\Http\Request;
use App\Models\UsuariEspai;
use App\Models\UsuariExternEspai;
use Illuminate\Support\Facades\Hash;

class EspaiController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $idsCompartits = UsuariExternEspai::where('user_id', $userId)
            ->pluck('espai_id')
            ->toArray();

        $espais = Espai::query()
            ->where('user_id', $userId)
            ->orWhereIn('id', $idsCompartits)
            ->orderByDesc('created_at')
            ->get();

        return view('espais.index', [
            'espais' => $espais,
        ]);
    }

    public function edit(Espai $espai)
    {
        if ($espai->user_id !== auth()->id()) {
            abort(404);
        }

        return view('espais.edit', compact('espai'));
    }

    public function create()
    {
        return view('espais.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'descripcio' => ['nullable', 'string']
            ],
            ['nom' => 'nom', 'descripcio' => 'descripció']
        );

        $espai = $request->user()->espais()->create([
            'nom' => $data['nom'],
            'descripcio' => $data['descripcio'] ?? null,
        ]);

        $adminRole = BaseRole::create([
            'espai_id' => $espai->id,
            'nom' => 'admin',
        ]);

        BaseRole::create([
            'espai_id' => $espai->id,
            'nom' => 'professor',
        ]);

        //Alan: permisos de todo
        $modules = [
            'users'       => ['view', 'create', 'update', 'delete', 'manage'],
            'groups'      => ['view', 'create', 'update', 'delete', 'manage'],
            'students'    => ['view', 'create', 'update', 'delete', 'import', 'export', 'manage'],
            'aulas'       => ['view', 'create', 'update', 'delete', 'manage', 'horari.update'],
            'noticies'    => ['view', 'create', 'update', 'delete', 'reaccionar', 'manage'],
            'guardies'    => ['view', 'create', 'update', 'delete', 'manage'],
            'tickets'     => ['view', 'create', 'update', 'delete', 'manage'],
            'roles'       => ['view', 'create', 'update', 'delete', 'manage'],
            'permissions' => ['view', 'create', 'update', 'delete', 'manage'],
        ];

        $permissionIds = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $perm = \App\Models\BasePermission::firstOrCreate([
                    'espai_id' => $espai->id,
                    'nom'      => "{$module}.{$action}",
                ]);
                $permissionIds[] = $perm->id;
            }
        }

        $adminRole->permissions()->syncWithoutDetaching($permissionIds);

        $adminUser = $espai->usuaris()->create([
            'nom'        => 'admin',
            'rol'        => 'admin',
            'contrasenya' => 'admin',
        ]);

        $adminUser->roles()->attach($adminRole->id);

        return redirect()
            ->route('espais.index')
            ->with('status', 'Espai creat correctament. Usuari per defecte: admin / admin');
    }

    public function update(Request $request, Espai $espai)
    {
        if ($espai->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'descripcio' => ['nullable', 'string'],
            ],
            [],
            [
                'nom' => 'nom',
                'descripcio' => 'descripció',
            ]
        );

        $espai->update([
            'nom' => $data['nom'],
            'descripcio' => $data['descripcio'] ?? null,
        ]);

        return redirect()
            ->route('espais.index')
            ->with('status', 'Espai actualitzat correctament.');
    }

    public function destroy(Request $request, Espai $espai)
    {
        if ($espai->user_id !== $request->user()->id) {
            abort(404);
        }

        $espai->delete();

        return redirect()
            ->route('espais.index')
            ->with('status', 'Espai eliminat correctament.');
    }

    public function entrarForm(Espai $espai)
    {
        return view('espais.entrar', [
            'espai' => $espai,
        ]);
    }

    public function entrar(Request $request, Espai $espai)
    {
        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'contrasenya' => ['required', 'string', 'max:255'],
            ],
            [],
            [
                'nom' => 'nom',
                'contrasenya' => 'contrasenya',
            ]
        );

        $nomNormalitzat = trim(mb_strtolower($data['nom']));

        $usuari = UsuariEspai::where('espai_id', $espai->id)
            ->whereRaw('LOWER(nom) = ?', [$nomNormalitzat])
            ->first();

        if (!$usuari || !Hash::check($data['contrasenya'], $usuari->contrasenya)) {
            return back()
                ->withErrors(['nom' => 'Nom o contrasenya incorrectes.'])
                ->withInput();
        }

        $request->session()->put('espai_id', $espai->id);
        $request->session()->put('usuari_espai_id', $usuari->id);
        $request->session()->put('rol_espai', $usuari->rol);

        return redirect()->route('espai.index');
    }


    public function accesForm(Espai $espai)
    {
        return view('espais.acces', [
            'espai' => $espai,
        ]);
    }

    public function acces(Request $request, Espai $espai)
    {
        $data = $request->validate(
            [
                'nom' => ['required', 'string', 'max:255'],
                'contrasenya' => ['required', 'string', 'max:255'],
            ],
            [],
            [
                'nom' => 'nom',
                'contrasenya' => 'contrasenya',
            ]
        );

        $usuari = UsuariEspai::where('espai_id', $espai->id)
            ->where('nom', $data['nom'])
            ->first();

        if (!$usuari || !Hash::check($data['contrasenya'], $usuari->contrasenya)) {
            return back()
                ->withErrors(['nom' => 'Nom o contrasenya incorrectes.'])
                ->withInput();
        }

        $request->session()->put('espai_id', $espai->id);
        $request->session()->put('usuari_espai_id', $usuari->id);
        $request->session()->put('rol_espai', $usuari->rol);

        return redirect()->route('espai.index');
    }
}