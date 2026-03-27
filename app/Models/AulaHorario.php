<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AulaHorario extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'aula_horaris';

    // Columnas que se pueden asignar masivamente
    protected $fillable = [
        'aula_id',
        'espai_id',
        'usuari_espai_id',
        'grup_id',
        'dia_setmana',
        'franja_horaria_id',
    ];

    /**
     * Relación con el aula
     */
    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    /**
     * Relación con el profesor (usuari_espai)
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'usuari_espai_id');
    }

    /**
     * Relación con la franja horaria
     */
    public function franja(): BelongsTo
    {
        return $this->belongsTo(FranjaHoraria::class, 'franja_horaria_id');
    }

    /**
     * Relación con el grupo
     */
    public function grup(): BelongsTo
    {
        return $this->belongsTo(Grup::class);
    }
}