<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UsuariEspai;
use App\Models\BaseRole;

return new class extends Migration
{
    public function up(): void
    {
        UsuariEspai::chunk(100, function ($usuaris) {
            foreach ($usuaris as $usuari) {

                if (!$usuari->rol) {
                    continue;
                }

                $baseRole = BaseRole::where('espai_id', $usuari->espai_id)
                    ->where('nom', $usuari->rol)
                    ->first();

                if ($baseRole) {
                    $usuari->roles()->syncWithoutDetaching([$baseRole->id]);
                }
            }
        });
    }

    public function down(): void
    {
        // Revertir: eliminar todas las asignaciones dinámicas
        Schema::table('role_usuari_espai', function (Blueprint $table) {
            // Simplemente vaciamos la tabla pivote
            DB::table('role_usuari_espai')->truncate();
        });
    }
};
