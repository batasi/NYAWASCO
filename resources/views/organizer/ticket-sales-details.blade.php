@if($ticket)
<div class="ticket-sales-details">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4>{{ $ticket->name }}</h4>
            <p class="text-muted mb-0">Event: {{ $ticket->event->title }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button class="btn btn-outline-primary btn-sm">Export</button>
                <button class="btn btn-outline-secondary btn-sm">Print</button>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">Price</h6>
                    <h4 class="text-primary">KES {{ number_format($ticket->price, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">Sold</h6>
                    <h4 class="text-success">{{ $ticket->quantity_sold }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">Available</h6>
                    <h4 class="text-info">{{ $ticket->quantity_available ?: 'Unlimited' }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">Revenue</h6>
                    <h4 class="text-success">KES {{ number_format($ticket->price * $ticket->quantity_sold, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Recent Purchases</h5>

    @if($purchases->count() > 0)
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->order_number }}</td>
                    <td>
                        <div>
                            <strong>{{ $purchase->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $purchase->user->email }}</small>
                        </div>
                    </td>
                    <td>{{ $purchase->quantity }}</td>
                    <td>KES {{ number_format($purchase->final_amount, 2) }}</td>
                    <td>{{ $purchase->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="badge bg-{{ $purchase->status === 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $purchases->links() }}
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        No purchases found for this ticket.
    </div>
    @endif
</div>
@else
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Ticket not found or you don't have permission to view this ticket.
</div>
@endif
