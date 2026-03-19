<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AulaHorari extends Model
{
    protected $fillable = [
        'espai_id',
        'aula_id',
        'dia_setmana',
        'franja_horaria_id',
        'grup_id',
    ];

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function franja()
    {
        return $this->belongsTo(FranjaHoraria::class, 'franja_horaria_id');
    }

    public function grup()
    {
        return $this->belongsTo(Grup::class);
    }

    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }
}
