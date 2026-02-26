<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuari_extern_espais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['espai_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuari_extern_espais');
    }
};