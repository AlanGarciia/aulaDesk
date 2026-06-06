<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('espai_user_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('espai_id')->constrained('espais')->cascadeOnDelete();
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'espai_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('espai_user_order');
    }
};