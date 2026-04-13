<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseRole extends Model
{
    protected $fillable = ['espai_id', 'nom'];

    public function permissions()
    {
        return $this->belongsToMany(
            BasePermission::class,
            'base_role_permission',
            'base_role_id',
            'base_permission_id'
        );
    }

    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }
}
