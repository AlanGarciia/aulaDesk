<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticiaReaccio extends Model
{
    protected $table = 'noticia_reaccions';

    protected $fillable = [
        'noticia_id',
        'usuari_espai_id',
        'tipus',
    ];

    public function noticia(): BelongsTo
    {
        return $this->belongsTo(Noticia::class, 'noticia_id');
    }

    public function usuariEspai(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'usuari_espai_id');
    }
}

