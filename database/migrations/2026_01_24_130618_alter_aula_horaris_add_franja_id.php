<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('aula_horaris', function (Blueprint $table) {
            $table->foreignId('franja_horaria_id')
                ->after('aula_id')
                ->constrained('franja_horaries')
                ->cascadeOnDelete();
            $table->dropUnique(['aula_id', 'dia_setmana', 'hora']);
            $table->dropColumn('hora');
            $table->unique(['aula_id', 'dia_setmana', 'franja_horaria_id']);
        });
    }

    public function down(): void
    {
        Schema::table('aula_horaris', function (Blueprint $table) {
            $table->dropUnique(['aula_id', 'dia_setmana', 'franja_horaria_id']);
            $table->dropConstrainedForeignId('franja_horaria_id');

            $table->unsignedTinyInteger('hora')->after('dia_setmana');
            $table->unique(['aula_id', 'dia_setmana', 'hora']);
        });
    }
};
