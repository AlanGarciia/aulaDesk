<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();

            $table->string('nom');                 // Ej: "Aula 2.1"
            $table->string('codi')->nullable();    // Ej: "A21"
            $table->unsignedInteger('capacitat')->nullable();
            $table->string('planta')->nullable();  // Ej: "2", "Baja", etc
            $table->boolean('activa')->default(true);

            $table->timestamps();

            // Evitar duplicados dentro del mismo espai
            $table->unique(['espai_id', 'nom']);
            $table->unique(['espai_id', 'codi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};

