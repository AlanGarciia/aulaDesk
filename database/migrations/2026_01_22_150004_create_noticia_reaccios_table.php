<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('noticia_reaccions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('noticia_id')->constrained('noticias')->cascadeOnDelete();
            $table->foreignId('usuari_espai_id')->constrained('usuari_espais')->cascadeOnDelete();
            $table->string('tipus');
            $table->timestamps();
            $table->unique(['noticia_id', 'usuari_espai_id']);
            $table->index(['noticia_id', 'tipus']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticia_reaccions');
    }
};
