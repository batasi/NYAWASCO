@if($ticketPurchase)
<div class="ticket-details">
    <!-- Ticket Header -->
    <div class="text-center mb-4">
        <h4 class="text-primary">{{ $ticketPurchase->event->title }}</h4>
        <p class="text-muted mb-0">{{ $ticketPurchase->event->organizer->name }}</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="ticket-section">
                <h6 class="section-title">Event Information</h6>
                <div class="info-item">
                    <small class="text-muted">Date & Time</small>
                    <div class="fw-bold">{{ $ticketPurchase->event->start_date->format('l, F d, Y \a\t h:i A') }}</div>
                </div>
                <div class="info-item">
                    <small class="text-muted">Location</small>
                    <div class="fw-bold">{{ $ticketPurchase->event->location }}</div>
                </div>
                <div class="info-item">
                    <small class="text-muted">Organizer</small>
                    <div class="fw-bold">{{ $ticketPurchase->event->organizer->name }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="ticket-section">
                <h6 class="section-title">Ticket Information</h6>
                <div class="info-item">
                    <small class="text-muted">Order Number</small>
                    <div class="fw-bold">{{ $ticketPurchase->order_number }}</div>
                </div>
                <div class="info-item">
                    <small class="text-muted">Ticket Type</small>
                    <div class="fw-bold">{{ $ticketPurchase->ticket->name }}</div>
                </div>
                <div class="info-item">
                    <small class="text-muted">Quantity</small>
                    <div class="fw-bold">{{ $ticketPurchase->quantity }} ticket(s)</div>
                </div>
                <div class="info-item">
                    <small class="text-muted">Purchase Date</small>
                    <div class="fw-bold">{{ $ticketPurchase->created_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendee Information -->
    @if($ticketPurchase->attendee_info && count($ticketPurchase->attendee_info) > 0)
    <div class="ticket-section mt-4">
        <h6 class="section-title">Attendee Information</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticketPurchase->attendee_info as $attendee)
                    <tr>
                        <td>{{ $attendee['name'] ?? 'N/A' }}</td>
                        <td>{{ $attendee['email'] ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Payment Details -->
    <div class="ticket-section mt-4">
        <h6 class="section-title">Payment Details</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="info-item">
                    <small class="text-muted">Subtotal</small>
                    <div class="fw-bold">KES {{ number_format($ticketPurchase->total_amount, 2) }}</div>
                </div>
                <div class="info-item">
                    <small class="text-muted">Tax (16%)</small>
                    <div class="fw-bold">KES {{ number_format($ticketPurchase->tax_amount, 2) }}</div>
                </div>
                <div class="info-item">
                    <small class="text-muted">Service Fee (2%)</small>
                    <div class="fw-bold">KES {{ number_format($ticketPurchase->fee_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-item">
                    <small class="text-muted">Total Amount</small>
                    <div class="fw-bold h5 text-primary">KES {{ number_format($ticketPurchase->final_amount, 2) }}</div>
                </div>
                <div class="info-item">
                    <small class="text-muted">Payment Status</small>
                    <div>
                        <span class="badge bg-{{ $ticketPurchase->status === 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($ticketPurchase->status) }}
                        </span>
                    </div>
                </div>
                @if($ticketPurchase->paid_at)
                <div class="info-item">
                    <small class="text-muted">Paid On</small>
                    <div class="fw-bold">{{ $ticketPurchase->paid_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- QR Code Area (for scanning at event) -->
    <div class="text-center mt-4 p-3 bg-light rounded">
        <p class="text-muted mb-2">Present this ticket at the event entrance</p>
        <div class="qr-placeholder bg-white p-3 d-inline-block rounded">
            <!-- In a real application, you would generate a QR code here -->
            <div class="text-center">
                <i class="fas fa-qrcode fa-3x text-muted"></i>
                <p class="small mt-2 mb-0">QR Code for: {{ $ticketPurchase->order_number }}</p>
            </div>
        </div>
    </div>
</div>

<style>
.ticket-section {
    margin-bottom: 1.5rem;
}
.section-title {
    color: #6c757d;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.info-item {
    margin-bottom: 0.75rem;
}
.qr-placeholder {
    border: 2px dashed #dee2e6;
}
</style>
@else
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Ticket not found or you don't have permission to view this ticket.
</div>
@endif
