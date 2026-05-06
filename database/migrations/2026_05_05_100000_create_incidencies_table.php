<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incidencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->foreignId('alumne_id')->constrained('alumnes')->cascadeOnDelete();
            $table->foreignId('grup_id')->nullable()->constrained('grups')->nullOnDelete();
            
            $table->foreignId('aula_horari_id')->nullable()->constrained('aula_horaris')->nullOnDelete();
            $table->foreignId('usuari_espai_id')->constrained('usuari_espais')->cascadeOnDelete();
            $table->string('tipus', 32);
            $table->date('data');
            $table->text('observacions')->nullable();
            $table->timestamps();
            $table->index(['espai_id', 'data']);
            $table->index(['alumne_id', 'data']);
            $table->index(['aula_horari_id', 'data']);
            $table->index(['usuari_espai_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencies');
    }
};