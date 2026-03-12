<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aula_alumne', function (Blueprint $table) {
            $table->id();

            $table->foreignId('aula_id')
                ->constrained('aulas')
                ->cascadeOnDelete();

            $table->foreignId('alumne_id')
                ->constrained('alumnes')
                ->cascadeOnDelete();

            $table->timestamps();

            // Evitar duplicados
            $table->unique(['aula_id', 'alumne_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aula_alumne');
    }
};
