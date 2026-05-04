<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Notificacio extends Model
{
    protected $table = 'notificacions';

    protected $fillable = [
        'espai_id',
        'usuari_espai_id',
        'tipus',
        'titol',
        'missatge',
        'url',
        'related_type',
        'related_id',
        'actor_usuari_espai_id',
        'llegida_el',
    ];

    protected $casts = [
        'espai_id' => 'integer',
        'usuari_espai_id' => 'integer',
        'related_id' => 'integer',
        'actor_usuari_espai_id' => 'integer',
        'llegida_el' => 'datetime',
    ];

    public function espai(): BelongsTo
    {
        return $this->belongsTo(Espai::class, 'espai_id');
    }

    public function destinatari(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'usuari_espai_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'actor_usuari_espai_id');
    }

    public static function notifyEspai(
        int $espaiId,
        string $tipus,
        array $payload,
        ?int $actorUsuariEspaiId = null,
        bool $excludeActor = true
    ): int {
        $titol = isset($payload['titol']) ? (string) $payload['titol'] : '';
        $missatge = isset($payload['missatge']) ? (string) $payload['missatge'] : null;
        $url = isset($payload['url']) ? (string) $payload['url'] : null;
        $relatedType = isset($payload['related_type']) ? (string) $payload['related_type'] : null;
        $relatedId = isset($payload['related_id']) ? (int) $payload['related_id'] : null;

        $query = UsuariEspai::query()->where('espai_id', $espaiId);

        if ($excludeActor && $actorUsuariEspaiId) {
            $query->where('id', '!=', $actorUsuariEspaiId);
        }

        $recipients = $query->pluck('id');

        if ($recipients->isEmpty()) {
            return 0;
        }

        $now = now();
        $rows = [];

        foreach ($recipients as $rid) {
            $rows[] = [
                'espai_id' => $espaiId,
                'usuari_espai_id' => (int) $rid,
                'tipus' => $tipus,
                'titol' => $titol,
                'missatge' => $missatge,
                'url' => $url,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
                'actor_usuari_espai_id' => $actorUsuariEspaiId,
                'llegida_el' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('notificacions')->insert($rows);

        return count($rows);
    }

    public function scopeNoLlegides($q)
    {
        return $q->whereNull('llegida_el');
    }

    public function scopePerUsuariEspai($q, int $usuariEspaiId)
    {
        return $q->where('usuari_espai_id', $usuariEspaiId);
    }
}