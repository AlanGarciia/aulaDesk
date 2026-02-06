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

            $table->string('nom');
            $table->string('codi')->nullable();
            $table->unsignedInteger('capacitat')->nullable();
            $table->string('planta')->nullable();
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

