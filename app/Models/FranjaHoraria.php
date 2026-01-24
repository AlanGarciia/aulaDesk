<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranjaHoraria extends Model
{
    protected $table = 'franja_horaries';

    protected $fillable = [
        'espai_id',
        'ordre',
        'inici',
        'fi',
        'nom',
    ];
}
