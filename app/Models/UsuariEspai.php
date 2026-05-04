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

    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }

    public function roles()
    {
        return $this->belongsToMany(
            BaseRole::class,
            'role_usuari_espai',
            'usuari_espai_id',
            'base_role_id'
        );
    }

    public static function baseRoles($espaiId = null)
    {
        if (!$espaiId) {
            return [];
        }

        return BaseRole::where('espai_id', $espaiId)
            ->pluck('nom')
            ->toArray();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permission) {
                $q->where('nom', $permission);
            })
            ->exists();
    }

    public function canEspai(string $permission): bool
    {
        return $this->hasPermission($permission);
    }

    public function setContrasenyaAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['contrasenya'] = Hash::make($value);
        }
    }
}