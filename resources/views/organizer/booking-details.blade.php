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
            <h6 class="text-muted mb-2">Customer Information</h6>
            <div class="mb-3">
                <strong>Name:</strong>
                <span class="float-end">{{ $booking->user->name }}</span>
            </div>
            <div class="mb-3">
                <strong>Email:</strong>
                <span class="float-end">{{ $booking->user->email }}</span>
            </div>
            <div class="mb-3">
                <strong>Phone:</strong>
                <span class="float-end">{{ $booking->user->phone ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-12">
            <h6 class="text-muted mb-2">Event Information</h6>
            <div class="d-flex align-items-center">
                @if($booking->event->banner_image)
                <img src="{{ asset('storage/' . $booking->event->banner_image) }}"
                     alt="{{ $booking->event->title }}"
                     class="rounded me-3" width="60" height="60">
                @endif
                <div>
                    <h5 class="mb-1">{{ $booking->event->title }}</h5>
                    <p class="text-muted mb-1">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $booking->event->location }}
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $booking->event->start_date->format('l, F d, Y \a\t h:i A') }}
                    </p>
                </div>
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
</div>
@else
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Booking not found or you don't have permission to view this booking.
</div>
@endif
