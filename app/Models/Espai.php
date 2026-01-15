<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Espai extends Model
{
    protected $fillable = [
        'user_id',
        'nom',
        'descripcio',
    ];

    public function usuaris(): HasMany
    {
        return $this->hasMany(\App\Models\UsuariEspai::class);
    }

}

