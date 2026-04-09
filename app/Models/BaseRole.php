<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseRole extends Model
{
    protected $fillable = ['espai_id', 'nom'];

    public function permissions()
    {
        return $this->belongsToMany(\App\Models\Permission::class);
    }

    public function espai()
    {
        return $this->belongsTo(\App\Models\Espai::class);
    }
}



