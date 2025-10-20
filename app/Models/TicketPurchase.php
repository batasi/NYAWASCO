<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_id',
        'order_number',
        'quantity',
        'unit_price',
        'total_amount',
        'tax_amount',
        'fee_amount',
        'final_amount',
        'currency',
        'status',
        'payment_method',
        'payment_id',
        'attendee_info',
        'notes',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'quantity' => 'integer',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'attendee_info' => 'array',
    ];

    protected $attributes = [
        'tax_amount' => 0.00,
        'fee_amount' => 0.00,
        'currency' => 'KES',
        'status' => 'pending',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper Methods
    public function markAsPaid($paymentMethod = null, $paymentId = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
        ]);

        // Update ticket sold count
        $this->ticket->increment('quantity_sold', $this->quantity);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Decrement ticket sold count
        $this->ticket->decrement('quantity_sold', $this->quantity);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function generateOrderNumber()
    {
        if (!$this->order_number) {
            $this->order_number = 'TKT-' . strtoupper(uniqid());
        }
        return $this->order_number;
    }
}
