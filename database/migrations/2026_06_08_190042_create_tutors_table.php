<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumne_id')->constrained('alumnes')->cascadeOnDelete();
            $table->string('parentiu')->nullable();
            $table->string('nom');
            $table->string('cognoms')->nullable();
            $table->string('correu')->nullable();
            $table->string('telefon')->nullable();
            $table->string('dni')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};