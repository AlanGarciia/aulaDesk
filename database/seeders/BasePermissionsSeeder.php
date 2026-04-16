<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Espai;
use App\Models\BaseRole;
use App\Models\BasePermission;

class BasePermissionsSeeder extends Seeder
{
    public function run(): void
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

        Espai::chunk(50, function ($espais) use ($modules) {
            foreach ($espais as $espai) {

                $adminRole = BaseRole::firstOrCreate(
                    ['espai_id' => $espai->id, 'nom' => 'admin']
                );

                $permissionIds = [];

                foreach ($modules as $module => $actions) {
                    foreach ($actions as $action) {

                        $permName = "{$module}.{$action}";

                        $permission = BasePermission::firstOrCreate(
                            [
                                'espai_id' => $espai->id,
                                'nom' => $permName
                            ]
                        );

                        $permissionIds[] = $permission->id;
                    }
                }

                $adminRole->permissions()->syncWithoutDetaching($permissionIds);
            }
        });
    }
}
