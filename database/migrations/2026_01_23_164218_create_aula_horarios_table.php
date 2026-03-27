<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aula_horaris', function (Blueprint $table) {

            $table->id();

            $table->foreignId('espai_id')
                ->constrained('espais')
                ->cascadeOnDelete();

            $table->foreignId('aula_id')
            //si no va, cambiar a aules (By Alan)
                ->constrained('aulas')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('dia_setmana');

            $table->foreignId('franja_horaria_id')
                ->constrained('franja_horaries')
                ->cascadeOnDelete();
            $table->foreignId('professor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('grup_id')
                ->nullable()
                ->constrained('grups')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['aula_id', 'dia_setmana', 'franja_horaria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aula_horaris');
    }
};