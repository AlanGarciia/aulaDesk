<?php

namespace App\Http\Controllers;

use App\Models\BasePermission;
use App\Models\BaseRole;
use App\Models\Espai;
use Illuminate\Http\Request;

class PermissionController extends Controller
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

        // Rol admin del espai
        $adminRole = BaseRole::firstOrCreate([
            'espai_id' => $espai->id,
            'nom' => 'admin',
        ]);

        $permissionIds = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {

                $permName = "{$module}.{$action}";

                $permission = BasePermission::firstOrCreate([
                    'espai_id' => $espai->id,
                    'nom' => $permName,
                ]);

                $permissionIds[] = $permission->id;
            }
        }

        // Admin siempre tiene todos los permisos
        $adminRole->permissions()->syncWithoutDetaching($permissionIds);
    }

    public function index(Request $request)
    {
        $espai = $this->getEspai($request);

        // Generar permisos automáticamente
        $this->generatePermissionsForEspai($espai);

        return view('espai.permissions.index', [
            'permissions' => BasePermission::where('espai_id', $espai->id)->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('espai.permissions.create');
    }

    public function store(Request $request)
    {
        $espai = $this->getEspai($request);

        $data = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        BasePermission::create([
            'espai_id' => $espai->id,
            'nom' => $data['nom'],
        ]);

        return redirect()->route('espai.permissions.index')
            ->with('status', 'Permís creat correctament.');
    }

    public function edit(Request $request, BasePermission $permission)
    {
        $espai = $this->getEspai($request);

        abort_if($permission->espai_id !== $espai->id, 404);

        return view('espai.permissions.edit', compact('permission'));
    }

    public function update(Request $request, BasePermission $permission)
    {
        $espai = $this->getEspai($request);

        abort_if($permission->espai_id !== $espai->id, 404);

        $data = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $permission->update($data);

        return redirect()->route('espai.permissions.index')
            ->with('status', 'Permís actualitzat correctament.');
    }

    public function destroy(Request $request, BasePermission $permission)
    {
        $espai = $this->getEspai($request);

        abort_if($permission->espai_id !== $espai->id, 404);

        $permission->delete();

        return back()->with('status', 'Permís eliminat.');
    }
}
