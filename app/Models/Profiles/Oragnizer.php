<?php

namespace App\Models\Profiles;

use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    protected $fillable = [
        'user_id',
        'organization_name',
        'contact_number',
        'address',
        'website',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
