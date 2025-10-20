<?php

namespace App\Models\Profiles;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'category',
        'license_number',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
