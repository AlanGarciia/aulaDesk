<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alumnes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->string('nom');
            $table->string('cognom1')->nullable();
            $table->string('cognom2')->nullable();
            $table->string('slug')->nullable();
            $table->string('correu')->nullable();
            $table->string('idalu', 11);
            $table->string('telefon')->nullable();
            $table->string('dni')->nullable();
            $table->date('data_naixement')->nullable();
            $table->timestamps();

            $table->unique(['espai_id', 'idalu']);
            $table->unique(['espai_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnes');
    }
};