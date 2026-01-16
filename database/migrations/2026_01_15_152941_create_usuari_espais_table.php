<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuari_espais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->string('nom');
            $table->string('rol')->default('professor');
            $table->string('contrasenya');
            $table->timestamps();
            $table->unique(['espai_id', 'nom']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuari_espais');
    }
};