<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_phone',
        'customer_id',
        'meter_id',
        'sender_id',
        'message',
        'message_type',
        'status',
        'api_response_code',
        'api_response_message',
        'error_message',
        'api_response',
        'sent_by',
        'sent_at',
        'delivered_at',
        'retry_count',
        'cost',
        'metadata'
    ];

    protected $casts = [
        'api_response' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
