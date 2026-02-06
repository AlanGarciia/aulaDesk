<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guardia_solicituds', function (Blueprint $table) {
            $table->unsignedBigInteger('original_usuari_espai_id')->nullable()->after('cobridor_usuari_espai_id');
            $table->timestamp('revertir_el')->nullable()->after('original_usuari_espai_id');
            $table->timestamp('revertit_el')->nullable()->after('revertir_el');

            $table->foreign('original_usuari_espai_id')->references('id')->on('usuari_espais')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('guardia_solicituds', function (Blueprint $table) {
            $table->dropForeign(['original_usuari_espai_id']);
            $table->dropColumn(['original_usuari_espai_id', 'revertir_el', 'revertit_el']);
        });
    }
};
