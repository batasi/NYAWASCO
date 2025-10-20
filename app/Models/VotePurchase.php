<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotePurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nominee_id',
        'votes_count',
        'amount',
        'status',
    ];

    protected $casts = [
        'votes_count' => 'integer',
        'amount' => 'decimal:2',
    ];

    protected $attributes = [
        'votes_count' => 1,
        'status' => 'pending',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nominee()
    {
        return $this->belongsTo(Nominee::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Helper Methods
    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);

        // Create votes for the purchased votes
        for ($i = 0; $i < $this->votes_count; $i++) {
            Vote::create([
                'user_id' => $this->user_id,
                'contest_id' => $this->nominee->voting_contest_id,
                'nominee_id' => $this->nominee_id,
            ]);
        }

        // Update user spending statistics
        $this->user->increment('total_amount_spent', $this->amount);
    }

    public function getTotalCostAttribute()
    {
        return $this->amount * $this->votes_count;
    }
}
