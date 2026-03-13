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
        Schema::create('grup_alumne', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grup_id');
            $table->unsignedBigInteger('alumne_id');
            $table->timestamps();
            $table->foreign('grup_id')->references('id')->on('grups')->onDelete('cascade');
            $table->foreign('alumne_id')->references('id')->on('alumnes')->onDelete('cascade');
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
