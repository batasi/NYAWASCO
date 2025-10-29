<?php

namespace App\Http\Controllers;

use App\Models\TicketPurchase;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    public function index()
    {
        $ticketPurchases = TicketPurchase::with(['event', 'ticket'])
            ->where('user_id', Auth::id())
            ->where('status', 'paid')
            ->latest()
            ->paginate(10);

        return view('tickets.index', compact('ticketPurchases'));
    }

    // public function show(TicketPurchase $ticketPurchase)
    // {
    //     if ($ticketPurchase->user_id !== Auth::id()) {
    //         abort(403);
    //     }

    //     $ticketPurchase->load(['event', 'ticket', 'event.organizer']);

    //     return view('tickets.show', compact('ticketPurchase'));
    // }

    public function show(Event $event)
    {
        // Check if event is available for ticket purchase
        if (!$event->is_active || $event->status !== 'approved') {
            return redirect()->back()->with('error', 'This event is not available for ticket purchase.');
        }

        $tickets = $event->tickets()->where('is_active', true)->get();

        return view('tickets.purchase', compact('event', 'tickets'));
    }

    public function purchase(Request $request, Event $event)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1|max:10',
            'attendee_info' => 'sometimes|array',
            'attendee_info.*.name' => 'required|string',
            'attendee_info.*.email' => 'required|email',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        // Check ticket availability
        if ($ticket->quantity_available && ($ticket->quantity_sold + $request->quantity) > $ticket->quantity_available) {
            return redirect()->back()->with('error', 'Not enough tickets available.');
        }

        // Check sale dates
        if ($ticket->sale_start_date && now()->lt($ticket->sale_start_date)) {
            return redirect()->back()->with('error', 'Ticket sales have not started yet.');
        }

        if ($ticket->sale_end_date && now()->gt($ticket->sale_end_date)) {
            return redirect()->back()->with('error', 'Ticket sales have ended.');
        }

        // Calculate amounts
        $unitPrice = $ticket->price;
        $totalAmount = $unitPrice * $request->quantity;
        $taxAmount = $totalAmount * 0.16; // 16% VAT
        $feeAmount = $totalAmount * 0.02; // 2% service fee
        $finalAmount = $totalAmount + $taxAmount + $feeAmount;

        // Create ticket purchase
        $ticketPurchase = TicketPurchase::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'order_number' => 'TKT-' . Str::upper(Str::random(8)) . '-' . time(),
            'quantity' => $request->quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'tax_amount' => $taxAmount,
            'fee_amount' => $feeAmount,
            'final_amount' => $finalAmount,
            'currency' => 'KES',
            'status' => 'pending',
            'attendee_info' => $request->attendee_info ?? [],
        ]);

        // Update ticket sold count
        $ticket->increment('quantity_sold', $request->quantity);

        return redirect()->route('payments.process', ['type' => 'ticket', 'id' => $ticketPurchase->id]);
    }

    public function myTickets()
    {
        $ticketPurchases = TicketPurchase::with(['event', 'ticket'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.my-tickets', compact('ticketPurchases'));
    }

    public function download(TicketPurchase $ticketPurchase)
    {
        // Check if user owns this ticket
        if ($ticketPurchase->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if ticket is paid
        if ($ticketPurchase->status !== 'paid') {
            return redirect()->back()->with('error', 'Ticket not paid for yet.');
        }

        $pdf = PDF::loadView('tickets.pdf', compact('ticketPurchase'));

        return $pdf->download("ticket-{$ticketPurchase->order_number}.pdf");
    }

    public function view(TicketPurchase $ticketPurchase)
    {
        // Check if user owns this ticket
        if ($ticketPurchase->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if ticket is paid
        if ($ticketPurchase->status !== 'paid') {
            return redirect()->back()->with('error', 'Ticket not paid for yet.');
        }

        return view('tickets.view', compact('ticketPurchase'));
    }

}
