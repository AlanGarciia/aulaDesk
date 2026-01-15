<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuariEspai extends Model
{
    protected $table = 'usuari_espais';

    protected $fillable = [
        'espai_id',
        'nom',
        'contrasenya',
    ];

    protected $hidden = [
        'contrasenya',
    ];

    public function espai(): BelongsTo
    {
        return $this->belongsTo(Espai::class);
    }
}
