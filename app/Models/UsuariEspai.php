<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuariEspai extends Model
{
    protected $table = 'usuari_espais';

    public const ROL_ADMIN = 'admin';
    public const ROL_PROFESSOR = 'professor';
    public const ROL_INFORMATIC = 'informatic';

    public const ROLS = [
        self::ROL_ADMIN,
        self::ROL_PROFESSOR,
        self::ROL_INFORMATIC,
    ];

    protected $fillable = [
        'espai_id',
        'nom',
        'rol',
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
