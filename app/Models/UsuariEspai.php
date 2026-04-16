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

    /**
     * Relación con el espai
     */
    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }

    /**
     * Relación: roles dinámicos del usuario dentro del espai
     */
    public function roles()
    {
        return $this->belongsToMany(
            BaseRole::class,
            'role_usuari_espai',
            'usuari_espai_id',
            'base_role_id'
        );
    }

    /**
     * Obtener solo los roles del espai actual
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
     * Alias más semántico para Blade y middleware
     */
    public function canEspai(string $permission): bool
    {
        return $this->hasPermission($permission);
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
