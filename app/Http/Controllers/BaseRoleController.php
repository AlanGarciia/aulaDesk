<?php

namespace App\Http\Controllers;

use App\Models\BaseRole;
use App\Models\Espai;
use App\Models\Permission;
use Illuminate\Http\Request;

class BaseRoleController extends Controller
{
    private function getEspai(Request $request)
    {
        $espaiId = $request->session()->get('espai_id');
        return Espai::findOrFail($espaiId);
    }

    public function index(Request $request)
    {
        $espai = $this->getEspai($request);

        return view('espai.roles.index', [
            'espai' => $espai,
            'roles' => $espai->roles, // SIEMPRE roles del espai
        ]);
    }

    public function create(Request $request)
    {
        return view('espai.roles.create', [
            'permissions' => Permission::all(),
        ]);
    }

    public function store(Request $request)
    {
        $espai = $this->getEspai($request);

        $role = $espai->roles()->create([
            'nom' => $request->nom,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('espai.roles.index');
    }

    public function edit(Request $request, BaseRole $role)
    {
        $espai = $this->getEspai($request);

        abort_if($role->espai_id !== $espai->id, 404);

        return view('espai.roles.edit', [
            'role' => $role,
            'permissions' => Permission::all(),
        ]);
    }

    public function update(Request $request, BaseRole $role)
    {
        $role->update(['nom' => $request->nom]);
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('espai.roles.index');
    }

    public function destroy(Request $request, BaseRole $role)
    {
        $espai = $this->getEspai($request);

        abort_if($role->espai_id !== $espai->id, 404);

        $role->delete();

        return back();
    }
}