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

    public function show(TicketPurchase $ticketPurchase)
    {
        if ($ticketPurchase->user_id !== Auth::id()) {
            abort(403);
        }

        $ticketPurchase->load(['event', 'ticket', 'event.organizer']);

        return view('tickets.show', compact('ticketPurchase'));
    }

    public function download(TicketPurchase $ticketPurchase)
    {
        if ($ticketPurchase->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ticketPurchase->status !== 'paid') {
            return back()->with('error', 'You can only download tickets for paid purchases.');
        }

        $ticketPurchase->load(['event', 'ticket', 'event.organizer']);

        $pdf = Pdf::loadView('tickets.pdf', compact('ticketPurchase'));

        return $pdf->download("ticket-{$ticketPurchase->order_number}.pdf");
    }

    public function purchase(Request $request, Event $event)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1|max:10',
            'attendee_info' => 'required|array|min:1',
            'attendee_info.*.name' => 'required|string',
            'attendee_info.*.email' => 'required|email',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        if (!$ticket->canPurchase($request->quantity)) {
            return back()->with('error', 'Sorry, the requested number of tickets is not available.');
        }

        // Calculate amounts
        $unitPrice = $ticket->price;
        $totalAmount = $unitPrice * $request->quantity;
        $taxAmount = $totalAmount * 0.16;
        $feeAmount = $totalAmount * 0.02;
        $finalAmount = $totalAmount + $taxAmount + $feeAmount;

        $purchase = TicketPurchase::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'order_number' => 'TKT-' . strtoupper(uniqid()),
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

        return redirect()->route('payment.process', ['type' => 'ticket', 'id' => $purchase->id])
            ->with('success', 'Please complete your payment to confirm your tickets.');
    }
}
