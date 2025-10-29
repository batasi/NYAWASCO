@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Bookings</h5>
                </div>
                <div class="card-body">
                    @if($bookings->count() > 0 || $ticketPurchases->count() > 0)
                        <!-- Event Bookings -->
                        @if($bookings->count() > 0)
                        <div class="mb-5">
                            <h6 class="text-muted mb-3">Event Bookings</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>Booking ID</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookings as $booking)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($booking->event->banner_image)
                                                    <img src="{{ asset('storage/' . $booking->event->banner_image) }}"
                                                         alt="{{ $booking->event->title }}"
                                                         class="rounded me-3" width="50" height="50">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $booking->event->title }}</h6>
                                                        <small class="text-muted">{{ $booking->event->location }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $booking->ticket_number }}</td>
                                            <td>{{ $booking->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($booking->payment_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#bookingModal"
                                                        data-booking-id="{{ $booking->id }}">
                                                    View Details
                                                </button>
                                                @if($booking->status === 'pending')
                                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Ticket Purchases -->
                        @if($ticketPurchases->count() > 0)
                        <div>
                            <h6 class="text-muted mb-3">Ticket Purchases</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>Order Number</th>
                                            <th>Ticket Type</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ticketPurchases as $purchase)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($purchase->event->banner_image)
                                                    <img src="{{ asset('storage/' . $purchase->event->banner_image) }}"
                                                         alt="{{ $purchase->event->title }}"
                                                         class="rounded me-3" width="50" height="50">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $purchase->event->title }}</h6>
                                                        <small class="text-muted">{{ $purchase->event->location }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $purchase->order_number }}</td>
                                            <td>{{ $purchase->ticket->name }}</td>
                                            <td>{{ $purchase->quantity }}</td>
                                            <td>KES {{ number_format($purchase->final_amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $purchase->status === 'paid' ? 'success' : ($purchase->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($purchase->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($purchase->status === 'paid')
                                                <a href="{{ route('tickets.show', $purchase) }}" class="btn btn-sm btn-outline-primary">
                                                    View Ticket
                                                </a>
                                                <a href="{{ route('tickets.download', $purchase) }}" class="btn btn-sm btn-outline-success">
                                                    Download
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                @if($bookings->count() > 0)
                                <p class="mb-0">Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings</p>
                                @endif
                            </div>
                            <div>
                                @if($bookings->count() > 0)
                                {{ $bookings->links() }}
                                @endif
                            </div>
                        </div>

                    @else
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4>No Bookings Yet</h4>
                            <p class="text-muted">You haven't made any bookings yet. Explore events and book your tickets!</p>
                            <a href="{{ route('events.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Browse Events
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bookingModalBody">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingModal = document.getElementById('bookingModal');

    bookingModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const bookingId = button.getAttribute('data-booking-id');
        const modalBody = document.getElementById('bookingModalBody');

        // Show loading
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading booking details...</p>
            </div>
        `;

        // Fetch booking details
        fetch(`/bookings/${bookingId}`)
            .then(response => response.text())
            .then(html => {
                modalBody.innerHTML = html;
            })
            .catch(error => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load booking details. Please try again.
                    </div>
                `;
            });
    });
});
</script>
@endpush
