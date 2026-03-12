<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('grup_usuari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grup_id');
            $table->unsignedBigInteger('usuari_espai_id');
            $table->timestamps();

            $table->foreign('grup_id')->references('id')->on('grups')->onDelete('cascade');
            $table->foreign('usuari_espai_id')->references('id')->on('usuari_espai')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grup_usuari');
    }
};
