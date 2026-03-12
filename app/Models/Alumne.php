<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumne extends Model
{
    protected $table = 'alumnes';

    protected $fillable = [
        'espai_id',
        'nom',
        'cognoms',
        'correu',
        'idalu',
        'telefon',
    ];

    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }

    public function aulas()
    {
        return $this->belongsToMany(Aula::class, 'aula_alumne');
    }

}
