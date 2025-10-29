<?php

namespace App\Http\Controllers;

use App\Models\TicketPurchase;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Event;

class PaymentController extends Controller
{
    public function process($type, $id)
    {
        if ($type === 'ticket') {
            $purchase = TicketPurchase::with(['event', 'ticket'])->findOrFail($id);
            return view('payments.process', compact('type', 'id', 'purchase'));
        } elseif ($type === 'booking') {
            $booking = Booking::with(['bookable'])->findOrFail($id);
            return view('payments.process', compact('type', 'id', 'booking'));
        }

        abort(404);
    }

    public function complete(Request $request, $type, $id)
    {
        // Handle payment completion logic here
        // This would integrate with your payment gateway (Pesapal)

        if ($type === 'ticket') {
            $purchase = TicketPurchase::findOrFail($id);
            $purchase->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Also update the associated booking
            Booking::where('bookable_type', Event::class)
                ->where('bookable_id', $purchase->event_id)
                ->where('user_id', $purchase->user_id)
                ->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'amount_paid' => $purchase->final_amount,
                    'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
                ]);
        }

        return redirect()->route('bookings.index')
            ->with('success', 'Payment completed successfully!');
    }
}
