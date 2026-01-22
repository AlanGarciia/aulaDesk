<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Noticia extends Model
{
    protected $table = 'noticias';

    protected $fillable = [
        'espai_id',
        'usuari_espai_id',
        'titol',
        'contingut',
        'tipus',
        'imatge_path',
        'publicat_el',
    ];

    protected $casts = [
        'publicat_el' => 'datetime',
    ];

    public function espai(): BelongsTo
    {
        return $this->belongsTo(Espai::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(UsuariEspai::class, 'usuari_espai_id');
    }

    public function reaccions(): HasMany
    {
        return $this->hasMany(NoticiaReaccio::class, 'noticia_id');
    }
}
