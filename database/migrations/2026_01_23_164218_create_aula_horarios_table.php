<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aula_horaris', function (Blueprint $table) {
            $table->id();

            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
            $table->foreignId('usuari_espai_id')->nullable()->constrained('usuari_espais')->nullOnDelete();
            $table->unsignedTinyInteger('dia_setmana');
            $table->unsignedTinyInteger('hora');
            $table->timestamps();
            $table->unique(['aula_id', 'dia_setmana', 'hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aula_horaris');
    }
};
