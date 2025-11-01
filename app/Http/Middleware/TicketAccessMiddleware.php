<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketPurchase;

class TicketAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ticketPurchase = $request->route('ticketPurchase');

        // Allow access if user owns the ticket
        if ($ticketPurchase->user_id && Auth::id() === $ticketPurchase->user_id) {
            return $next($request);
        }

        // Allow access for guest purchases via session
        if (session('guest_ticket_access') === $ticketPurchase->id) {
            return $next($request);
        }

        // For guest purchases, redirect to access page
        if ($ticketPurchase->isGuestPurchase()) {
            return redirect()->route('tickets.guest-access')
                ->with('error', 'Please provide your order details to access this ticket.');
        }

        abort(403, 'Unauthorized access to ticket.');
    }
}
