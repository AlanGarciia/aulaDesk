<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BaseRole;

class BaseRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'professor',
            'informatic',
        ];

        foreach ($roles as $rol) {
            BaseRole::firstOrCreate(['nom' => $rol]);
        }
    }
}

