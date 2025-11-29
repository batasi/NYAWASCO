<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalkRoute extends Model
{
    protected $fillable = [
        'name',
        'zone_id',
        'route_order',

    ];

    public function meters() {
        return $this->hasMany(Meter::class);
    }

}
