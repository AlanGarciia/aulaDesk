<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->foreignId('usuari_espai_id')->nullable()
                ->constrained('usuari_espais')->nullOnDelete();
            $table->string('titol');
            $table->text('contingut')->nullable();
            $table->string('tipus')->default('noticia');
            $table->string('imatge_path')->nullable();
            $table->timestamp('publicat_el')->nullable();
            $table->timestamps();
            $table->index(['espai_id', 'publicat_el']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
