<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgingBucket extends Model
{
    protected $fillable = [
        'name',
        'from_days',
        'to_days',
        'color',
        'collection_priority',
        'action_required',
        'is_active'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('from_days');
    }

    public function getRangeAttribute()
    {
        return "{$this->from_days}-{$this->to_days} days";
    }
}
