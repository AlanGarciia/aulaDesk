<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

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

    // ❌ IMPORTANTE: NO ocultamos la contraseña
    // protected $hidden = ['contrasenya'];

    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }

    /**
     * RELACIÓN: roles dinámicos del usuario dentro del espai
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
     * Mutador de contraseña (siempre hashea)
     */
    public function setContrasenyaAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['contrasenya'] = Hash::make($value);
        }
    }
}