<?php

namespace App\Http\Controllers;

use App\Models\BaseRole;
use App\Models\Espai;
use App\Models\BasePermission;
use Illuminate\Http\Request;

class BaseRoleController extends Controller
{
    private function getEspai(Request $request)
    {
        $espaiId = $request->session()->get('espai_id');
        return Espai::findOrFail($espaiId);
    }

    /**
     * Genera automáticamente todos los permisos del espai
     */
    private function generatePermissionsForEspai(Espai $espai)
    {
        $modules = [
            'users' => ['view', 'create', 'update', 'delete', 'manage'],
            'groups' => ['view', 'create', 'update', 'delete', 'manage'],
            'students' => ['view', 'create', 'update', 'delete', 'import', 'export', 'manage'],
            'aulas' => ['view', 'create', 'update', 'delete', 'manage', 'horari.update'],
            'noticies' => ['view', 'create', 'update', 'delete', 'reaccionar', 'manage'],
            'guardies' => ['view', 'create', 'update', 'delete', 'manage'],
            'tickets' => ['view', 'create', 'update', 'delete', 'manage'],
            'roles' => ['view', 'create', 'update', 'delete', 'manage'],
            'permissions' => ['view', 'create', 'update', 'delete', 'manage'],
        ];

        // Crear rol admin si no existe
        $adminRole = BaseRole::firstOrCreate([
            'espai_id' => $espai->id,
            'nom' => 'admin',
        ]);

        $permissionIds = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {

                $perm = BasePermission::firstOrCreate([
                    'espai_id' => $espai->id,
                    'nom' => "$module.$action",
                ]);

                $permissionIds[] = $perm->id;
            }
        }

        // Admin siempre tiene todos los permisos
        $adminRole->permissions()->syncWithoutDetaching($permissionIds);
    }

    public function index(Request $request)
    {
        $espai = $this->getEspai($request);

        // 🔥 Asegurar que los permisos existen
        $this->generatePermissionsForEspai($espai);

        return view('espai.roles.index', [
            'espai' => $espai,
            'roles' => $espai->roles,
        ]);
    }

    public function create(Request $request)
    {
        $espai = $this->getEspai($request);

        // 🔥 Asegurar que los permisos existen
        $this->generatePermissionsForEspai($espai);

        return view('espai.roles.create', [
            'permissions' => BasePermission::where('espai_id', $espai->id)->get(),
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

        // 🔥 Asegurar que los permisos existen
        $this->generatePermissionsForEspai($espai);

        return view('espai.roles.edit', [
            'role' => $role,
            'permissions' => BasePermission::where('espai_id', $espai->id)->get(),
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
