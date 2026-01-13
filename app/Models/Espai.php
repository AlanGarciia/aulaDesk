<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Espai extends Model
{
    protected $fillable = [
        'user_id',
        'nom',
        'descripcio',
    ];
}

