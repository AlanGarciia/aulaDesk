<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('base_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('espai_id')->constrained('espais')->onDelete('cascade');
            $table->string('nom');
            $table->timestamps();
            $table->unique(['espai_id', 'nom']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_roles');
    }
};