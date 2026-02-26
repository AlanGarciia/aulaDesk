<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuariExternEspai extends Model
{
    protected $table = 'usuari_extern_espais';

    protected $fillable = [
        'espai_id',
        'user_id',
        // 'rol',
    ];

    public function espai(): BelongsTo
    {
        return $this->belongsTo(Espai::class, 'espai_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
