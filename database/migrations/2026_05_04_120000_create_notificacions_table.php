<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notificacions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('espai_id')
                ->constrained('espais')
                ->cascadeOnDelete();

            $table->foreignId('usuari_espai_id')
                ->constrained('usuari_espais')
                ->cascadeOnDelete();

            $table->string('tipus', 64);

            $table->string('titol');
            $table->text('missatge')->nullable();
            $table->string('url')->nullable();

            $table->string('related_type', 64)->nullable();
            $table->unsignedBigInteger('related_id')->nullable();

            $table->foreignId('actor_usuari_espai_id')
                ->nullable()
                ->constrained('usuari_espais')
                ->nullOnDelete();

            $table->timestamp('llegida_el')->nullable();

            $table->timestamps();

            $table->index(['usuari_espai_id', 'llegida_el', 'created_at'], 'notif_user_unread_idx');
            $table->index(['espai_id', 'created_at']);
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacions');
    }
};