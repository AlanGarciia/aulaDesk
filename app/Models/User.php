<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Espai;
use App\Models\UsuariEspai;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Espais creados por el usuario (propietario)
     */
    public function espais(): HasMany
    {
        return $this->hasMany(Espai::class);
    }

    /**
     * Relación con los accesos del usuario a cada espai
     * (MUY IMPORTANTE para permisos)
     */
    public function usuariEspais(): HasMany
    {
        return $this->hasMany(UsuariEspai::class);
    }
}