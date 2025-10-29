<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        // Get user's bookings for events
        $bookings = Booking::where('user_id', Auth::id())
            ->where('bookable_type', Event::class)
            ->with(['bookable', 'bookable.organizer'])
            ->latest()
            ->paginate(10);

        $ticketPurchases = TicketPurchase::with(['event', 'ticket'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings', 'ticketPurchases'));
    }

    public function show(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['bookable', 'bookable.organizer']);

        return view('bookings.show', compact('booking'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1|max:10',
            'attendee_info' => 'nullable|array',
            'attendee_info.*.name' => 'required|string',
            'attendee_info.*.email' => 'required|email',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        // Check if ticket is available
        if (!$ticket->canPurchase($request->quantity)) {
            return back()->with('error', 'Sorry, the requested number of tickets is not available.');
        }

        // Calculate amounts
        $unitPrice = $ticket->price;
        $totalAmount = $unitPrice * $request->quantity;
        $taxAmount = $totalAmount * 0.16; // 16% VAT
        $feeAmount = $totalAmount * 0.02; // 2% service fee
        $finalAmount = $totalAmount + $taxAmount + $feeAmount;

        // Create ticket purchase
        $purchase = TicketPurchase::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'order_number' => 'TKT-' . strtoupper(Str::random(12)),
            'quantity' => $request->quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'tax_amount' => $taxAmount,
            'fee_amount' => $feeAmount,
            'final_amount' => $finalAmount,
            'currency' => 'KES',
            'status' => 'pending',
            'attendee_info' => $request->attendee_info,
        ]);

        // Create booking record using polymorphic relationship
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'bookable_type' => Event::class,
            'bookable_id' => $event->id,
            'ticket_number' => 'EVT-' . strtoupper(Str::random(10)),
            'quantity' => $request->quantity,
            'amount' => $finalAmount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return redirect()->route('payment.process', ['type' => 'ticket', 'id' => $purchase->id])
            ->with('success', 'Booking created successfully! Please complete your payment.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking is already cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);

        // Also cancel associated ticket purchase if exists
        TicketPurchase::where('event_id', $booking->bookable_id)
            ->where('user_id', Auth::id())
            ->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
