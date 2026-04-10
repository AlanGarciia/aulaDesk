<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        return view('espai.permissions.index', [
            'permissions' => Permission::all(),
        ]);
    }

    public function create()
    {
        return view('espai.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255|unique:permissions,nom',
            'descripcio' => 'nullable|string|max:255',
        ]);

        Permission::create($data);

        return redirect()->route('espai.permissions.index')
            ->with('status', 'Permís creat correctament.');
    }

    public function edit(Permission $permission)
    {
        return view('espai.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255|unique:permissions,nom,' . $permission->id,
            'descripcio' => 'nullable|string|max:255',
        ]);

        $permission->update($data);

        return redirect()->route('espai.permissions.index')
            ->with('status', 'Permís actualitzat correctament.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return back()->with('status', 'Permís eliminat.');
    }
}