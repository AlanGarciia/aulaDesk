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
        Schema::create('grups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('espai_id');
            $table->string('nom');
            $table->timestamps();

            $table->foreign('espai_id')->references('id')->on('espais')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grups');
    }
};
