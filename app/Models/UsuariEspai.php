<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuariEspai extends Model
{
    use HasFactory;

    protected $table = 'usuari_espais';

    protected $fillable = [
        'espai_id',
        'nom',
        'rol',
        'contrasenya',
    ];

    protected $hidden = [
        'contrasenya',
    ];

    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }

    /**
     * RELACIÓN CORRECTA: roles dinámicos del usuario dentro del espai
     * (belongsToMany con BaseRole, no Role)
     */
    public function roles()
    {
        return $this->belongsToMany(BaseRole::class, 'role_usuari_espai');
    }

    /**
     * DEVOLVER SOLO LOS ROLES DEL ESPAI ACTUAL
     */
    public static function baseRoles($espaiId = null)
    {
        if (!$espaiId) {
            return [];
        }

        return BaseRole::where('espai_id', $espaiId)
            ->pluck('nom')
            ->toArray();
    }

    /**
     * Comprobar si el usuario tiene un permiso dinámico
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permission) {
                $q->where('nom', $permission);
            })
            ->exists();
    }

    /**
     * Mutador de contraseña
     */
    public function setContrasenyaAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['contrasenya'] = bcrypt($value);
        }
    }
}
