<?php

namespace App\Models\Profiles;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_number',
        'preferences',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
