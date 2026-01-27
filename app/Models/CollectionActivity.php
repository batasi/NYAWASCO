<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionActivity extends Model
{
    protected $fillable = [
        'customer_id',
        'collection_agent_id',
        'activity_type',
        'notes',
        'activity_date',
        'follow_up_date',
        'outcome',
        'promised_amount',
        'promised_date',
        'attachments'
    ];

    protected $casts = [
        'activity_date' => 'date',
        'follow_up_date' => 'date',
        'promised_date' => 'date',
        'promised_amount' => 'decimal:2',
        'attachments' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'collection_agent_id');
    }

    public function getActivityIconAttribute()
    {
        return match($this->activity_type) {
            'call' => 'phone',
            'visit' => 'home',
            'email' => 'envelope',
            'sms' => 'chat',
            'letter' => 'mail',
            'promise_to_pay' => 'handshake',
            'payment_arrangement' => 'file-contract',
            default => 'circle'
        };
    }
}
