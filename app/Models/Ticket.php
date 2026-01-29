<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'espai_id',
        'aula_id',
        'creat_per_usuari_espai_id',
        'titol',
        'descripcio',
        'estat',
        'prioritat',
        'tancat_at',
    ];

    public const ESTATS = ['obert', 'en_proces', 'tancat'];
    public const PRIORITATS = ['baixa', 'mitja', 'alta'];

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'creat_per_usuari_espai_id');
    }

    public function espai(): BelongsTo
    {
        return $this->belongsTo(Espai::class);
    }
}

