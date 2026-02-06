<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardia_solicituds', function (Blueprint $table) {
            $table->unsignedBigInteger('noticia_id')->nullable()->after('id');
            $table->unsignedBigInteger('cobridor_usuari_espai_id')->nullable()->after('solicitant_usuari_espai_id');
        });
    }

    public function down(): void
    {
        Schema::table('guardia_solicituds', function (Blueprint $table) {
            $table->dropColumn('noticia_id');
            $table->dropColumn('cobridor_usuari_espai_id');
        });
    }
};
