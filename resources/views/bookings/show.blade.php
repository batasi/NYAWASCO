@if($booking)
<div class="booking-details">
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-muted mb-2">Booking Information</h6>
            <div class="mb-3">
                <strong>Booking ID:</strong>
                <span class="float-end">{{ $booking->ticket_number }}</span>
            </div>
            <div class="mb-3">
                <strong>Status:</strong>
                <span class="float-end">
                    <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </span>
            </div>
            <div class="mb-3">
                <strong>Payment Status:</strong>
                <span class="float-end">
                    <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'pending' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </span>
            </div>
            <div class="mb-3">
                <strong>Booking Date:</strong>
                <span class="float-end">{{ $booking->created_at->format('F d, Y \a\t h:i A') }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <h6 class="text-muted mb-2">Event Information</h6>
            <div class="mb-3">
                <strong>Event:</strong>
                <span class="float-end">{{ $booking->event->title }}</span>
            </div>
            <div class="mb-3">
                <strong>Organizer:</strong>
                <span class="float-end">{{ $booking->event->organizer->name }}</span>
            </div>
            <div class="mb-3">
                <strong>Location:</strong>
                <span class="float-end">{{ $booking->event->location }}</span>
            </div>
            <div class="mb-3">
                <strong>Date & Time:</strong>
                <span class="float-end">
                    {{ $booking->event->start_date->format('M d, Y h:i A') }}
                </span>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-12">
            <h6 class="text-muted mb-3">Payment Details</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td>Amount:</td>
                            <td class="text-end">KES {{ number_format($booking->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Amount Paid:</td>
                            <td class="text-end">KES {{ number_format($booking->amount_paid, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Payment Reference:</td>
                            <td class="text-end">{{ $booking->payment_reference ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($booking->status === 'pending')
    <div class="alert alert-warning mt-3">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Your booking is pending payment. Please complete the payment to confirm your booking.
    </div>
    @endif

    @if($booking->status === 'confirmed')
    <div class="alert alert-success mt-3">
        <i class="fas fa-check-circle me-2"></i>
        Your booking has been confirmed. We look forward to seeing you at the event!
    </div>
    @endif
</div>
@else
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Booking not found or you don't have permission to view this booking.
</div>
@endif
