<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AulaHorario extends Model
{
    protected $table = 'aula_horaris';

    protected $fillable = [
        'aula_id',
        'usuari_espai_id',
        'dia_setmana',
        'hora',
    ];

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'usuari_espai_id');
    }
}
