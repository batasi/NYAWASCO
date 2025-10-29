@extends('layouts.app')

@section('title', 'My Tickets')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Tickets</h5>
                </div>
                <div class="card-body">
                    @if($ticketPurchases->count() > 0)
                    <div class="row">
                        @foreach($ticketPurchases as $purchase)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card ticket-card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">{{ $purchase->event->title }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="ticket-info">
                                        <div class="mb-3">
                                            <small class="text-muted">Order Number</small>
                                            <div class="fw-bold">{{ $purchase->order_number }}</div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Ticket Type</small>
                                                <div class="fw-bold">{{ $purchase->ticket->name }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Quantity</small>
                                                <div class="fw-bold">{{ $purchase->quantity }}</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Amount</small>
                                                <div class="fw-bold">KES {{ number_format($purchase->final_amount, 2) }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Status</small>
                                                <div>
                                                    <span class="badge bg-{{ $purchase->status === 'paid' ? 'success' : ($purchase->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($purchase->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted">Event Date</small>
                                            <div class="fw-bold">
                                                {{ $purchase->event->start_date->format('M d, Y h:i A') }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted">Location</small>
                                            <div class="fw-bold">{{ $purchase->event->location }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                        @if($purchase->status === 'paid')
                                        <a href="{{ route('tickets.show', $purchase) }}"
                                           class="btn btn-outline-primary btn-sm"
                                           data-bs-toggle="modal"
                                           data-bs-target="#ticketModal"
                                           data-purchase-id="{{ $purchase->id }}">
                                            View Ticket
                                        </a>
                                        <a href="{{ route('tickets.download', $purchase) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-download me-1"></i>Download PDF
                                        </a>
                                        @else
                                        <a href="{{ route('payment.process', ['type' => 'ticket', 'id' => $purchase->id]) }}"
                                           class="btn btn-warning btn-sm">
                                            Complete Payment
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $ticketPurchases->links() }}
                    </div>

                    @else
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <h4>No Tickets Yet</h4>
                            <p class="text-muted">You haven't purchased any tickets yet. Explore events and book your spot!</p>
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

<!-- Ticket Details Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ticketModalBody">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadTicket()">Download PDF</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPurchaseId = null;

document.addEventListener('DOMContentLoaded', function() {
    const ticketModal = document.getElementById('ticketModal');

    ticketModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        currentPurchaseId = button.getAttribute('data-purchase-id');
        const modalBody = document.getElementById('ticketModalBody');

        // Show loading
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading ticket details...</p>
            </div>
        `;

        // Fetch ticket details
        fetch(`/tickets/${currentPurchaseId}`)
            .then(response => response.text())
            .then(html => {
                modalBody.innerHTML = html;
            })
            .catch(error => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load ticket details. Please try again.
                    </div>
                `;
            });
    });
});

function downloadTicket() {
    if (currentPurchaseId) {
        window.location.href = `/tickets/${currentPurchaseId}/download`;
    }
}
</script>
@endpush
