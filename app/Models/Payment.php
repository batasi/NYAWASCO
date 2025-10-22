<?php

namespace App\Models;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'voting_contest_id',
        'nominee_id',
        'order_tracking_id',
        'merchant_reference',
        'amount',
        'currency',
        'status',
        'payment_method',
        'phone_number',
        'raw_response',
    ];

    public function nominee() {
        return $this->belongsTo(Nominee::class);
    }

    public function contest() {
        return $this->belongsTo(VotingContest::class, 'voting_contest_id');
    }
}
