<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['nom', 'descripcio'];

    public function roles()
    {
        return $this->belongsToMany(BaseRole::class, 'base_role_permission');
    }
}


