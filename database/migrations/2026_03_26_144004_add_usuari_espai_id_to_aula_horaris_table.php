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
        Schema::table('aula_horaris', function (Blueprint $table) {
            $table->unsignedBigInteger('usuari_espai_id')->nullable()->after('franja_horaria_id');
        });
    }

    public function down()
    {
        Schema::table('aula_horaris', function (Blueprint $table) {
            $table->dropColumn('usuari_espai_id');
        });
    }
};
