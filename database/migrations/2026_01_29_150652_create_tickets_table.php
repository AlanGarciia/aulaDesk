<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
            $table->foreignId('creat_per_usuari_espai_id')->constrained('usuari_espais')->cascadeOnDelete();
            $table->string('titol');
            $table->text('descripcio')->nullable();
            $table->string('estat')->default('obert');
            $table->string('prioritat')->default('mitja');
            $table->timestamp('tancat_at')->nullable();
            $table->timestamps();
            $table->index(['aula_id', 'estat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
