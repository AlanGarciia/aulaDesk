<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grup extends Model
{
    protected $fillable = ['espai_id', 'nom'];

    public function usuaris()
    {
        return $this->belongsToMany(UsuariEspai::class, 'grup_usuari', 'grup_id', 'usuari_espai_id');
    }

    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }
}

