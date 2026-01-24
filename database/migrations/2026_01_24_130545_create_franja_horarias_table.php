<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('franja_horaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->unsignedSmallInteger('ordre');
            $table->time('inici');
            $table->time('fi');
            $table->string('nom')->nullable();
            $table->timestamps();
            $table->unique(['espai_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franja_horaries');
    }
};
