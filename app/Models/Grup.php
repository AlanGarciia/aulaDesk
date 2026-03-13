<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grup extends Model
{
    protected $fillable = ['espai_id', 'nom'];
    public function alumnes()
    {
        return $this->belongsToMany(Alumne::class, 'grup_alumne', 'grup_id', 'alumne_id');
    }

    public function espai()
    {
        return $this->belongsTo(Espai::class);
    }
}
