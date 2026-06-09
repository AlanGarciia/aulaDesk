<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tutor extends Model
{
    protected $table = 'tutors';

    protected $fillable = [
        'alumne_id',
        'parentiu',
        'nom',
        'cognoms',
        'correu',
        'telefon',
        'dni',
    ];

    public function alumne(): BelongsTo
    {
        return $this->belongsTo(Alumne::class, 'alumne_id');
    }
}