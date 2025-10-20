<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bookable_type',
        'bookable_id',
        'ticket_number',
        'quantity',
        'amount',
        'status',
        'payment_status',
        'amount_paid',
        'payment_reference',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    protected $attributes = [
        'quantity' => 1,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'amount_paid' => 0.00,
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    // Helper Methods
    public function confirm()
    {
        $this->update(['status' => 'confirmed']);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAsPaid($amount = null, $reference = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'amount_paid' => $amount ?? $this->amount,
            'payment_reference' => $reference,
            'status' => 'confirmed',
        ]);
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function generateTicketNumber()
    {
        if (!$this->ticket_number) {
            $this->ticket_number = 'BK-' . strtoupper(uniqid());
        }
        return $this->ticket_number;
    }
}
