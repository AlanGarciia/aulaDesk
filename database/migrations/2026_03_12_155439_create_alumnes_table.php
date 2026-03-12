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
            $table->string('cognoms')->nullable();
            $table->string('correu')->nullable();
            $table->string('idalu', 11);
            $table->string('telefon')->nullable();
            $table->timestamps();
            $table->unique(['espai_id', 'idalu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnes');
    }
};
