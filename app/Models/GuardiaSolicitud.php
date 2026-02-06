<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardiaSolicitud extends Model
{
    protected $table = 'guardia_solicituds';

    protected $fillable = [
        'espai_id',
        'solicitant_usuari_espai_id',
        'cobridor_usuari_espai_id',
        'noticia_id',
        'aula_id',
        'dia_setmana',
        'franja_horaria_id',
        'tipus',
        'comentari',
        'estat',
        // si luego añades vigencia:
        // 'vigent_fins',
    ];

    protected $casts = [
        'espai_id' => 'integer',
        'solicitant_usuari_espai_id' => 'integer',
        'cobridor_usuari_espai_id' => 'integer',
        'noticia_id' => 'integer',
        'aula_id' => 'integer',
        'dia_setmana' => 'integer',
        'franja_horaria_id' => 'integer',
        // 'vigent_fins' => 'datetime',
    ];

    public function espai(): BelongsTo
    {
        return $this->belongsTo(Espai::class, 'espai_id');
    }

    public function solicitant(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'solicitant_usuari_espai_id');
    }

    public function cobridor(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'cobridor_usuari_espai_id');
    }

    public function noticia(): BelongsTo
    {
        return $this->belongsTo(Noticia::class, 'noticia_id');
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'aula_id');
    }

    public function franja(): BelongsTo
    {
        return $this->belongsTo(FranjaHoraria::class, 'franja_horaria_id');
    }
}
