<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

    class PermissionSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run()
    {
        $permisos = [
            'crear_usuaris',
            'editar_usuaris',
            'eliminar_usuaris',

            'crear_grups',
            'veure_grups',
            'editar_grups',
            'eliminar_grups',

            'crear_alumnes',
            'editar_alumnes',
            'eliminar_alumnes',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['nom' => $p]);
        }
    }

}
