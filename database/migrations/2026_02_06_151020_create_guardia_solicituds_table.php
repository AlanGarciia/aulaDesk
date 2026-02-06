<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardia_solicituds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->foreignId('solicitant_usuari_espai_id')->constrained('usuari_espais')->cascadeOnDelete();
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->unsignedTinyInteger('dia_setmana');
            $table->foreignId('franja_horaria_id')->constrained('franja_horaries')->cascadeOnDelete();
            $table->string('tipus', 50)->nullable();
            $table->text('comentari')->nullable();
            $table->string('estat', 20)->default('pendent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardia_solicituds');
    }
};