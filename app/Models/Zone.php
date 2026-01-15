<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'description',

    ];
    public function walkroutes() {
        return $this->hasMany(WalkRoute::class);
    }
     public function meters()
    {
        return $this->hasMany(Meter::class);
    }

}
