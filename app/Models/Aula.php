<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aula extends Model
{
    protected $table = 'aulas';

    protected $fillable = [
        'espai_id',
        'nom',
        'codi',
        'capacitat',
        'planta',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'capacitat' => 'integer',
    ];

    public function espai(): BelongsTo
    {
        return $this->belongsTo(Espai::class);
    }
}
