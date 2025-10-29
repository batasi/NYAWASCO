@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Complete Your Payment</h5>
                </div>
                <div class="card-body">
                    <!-- Payment Summary -->
                    <div class="payment-summary mb-4">
                        <h6 class="text-muted mb-3">Order Summary</h6>

                        @if($type === 'ticket' && isset($purchase))
                        <div class="summary-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Event:</span>
                                <strong>{{ $purchase->event->title }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ticket Type:</span>
                                <strong>{{ $purchase->ticket->name }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Quantity:</span>
                                <strong>{{ $purchase->quantity }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong>KES {{ number_format($purchase->total_amount, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (16%):</span>
                                <strong>KES {{ number_format($purchase->tax_amount, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Service Fee (2%):</span>
                                <strong>KES {{ number_format($purchase->fee_amount, 2) }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="h5">Total Amount:</span>
                                <strong class="h5 text-primary">KES {{ number_format($purchase->final_amount, 2) }}</strong>
                            </div>
                        </div>
                        @elseif($type === 'booking' && isset($booking))
                        <div class="summary-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Event:</span>
                                <strong>{{ $booking->event->title }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Booking ID:</span>
                                <strong>{{ $booking->ticket_number }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Amount:</span>
                                <strong>KES {{ number_format($booking->amount, 2) }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="h5">Total Amount:</span>
                                <strong class="h5 text-primary">KES {{ number_format($booking->amount, 2) }}</strong>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Methods -->
                    <div class="payment-methods">
                        <h6 class="text-muted mb-3">Select Payment Method</h6>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="mpesa" value="mpesa" checked>
                                <label class="form-check-label" for="mpesa">
                                    <i class="fas fa-mobile-alt me-2"></i>M-Pesa
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                <label class="form-check-label" for="card">
                                    <i class="fas fa-credit-card me-2"></i>Credit/Debit Card
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank">
                                <label class="form-check-label" for="bank">
                                    <i class="fas fa-university me-2"></i>Bank Transfer
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- M-Pesa Form (shown by default) -->
                    <div id="mpesa-form" class="payment-form">
                        <form action="{{ route('payment.complete', ['type' => $type, 'id' => $id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="mpesa">

                            <div class="mb-3">
                                <label for="phone_number" class="form-label">M-Pesa Phone Number</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                       placeholder="2547XXXXXXXX" required>
                                <div class="form-text">Enter your M-Pesa registered phone number</div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                You will receive a prompt on your phone to enter your M-Pesa PIN to complete the payment.
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-lock me-2"></i>Pay with M-Pesa
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <!-- Card Form (hidden by default) -->
                    <div id="card-form" class="payment-form" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Card payments are currently unavailable. Please use M-Pesa.
                        </div>
                    </div>

                    <!-- Bank Transfer Form (hidden by default) -->
                    <div id="bank-form" class="payment-form" style="display: none;">
                        <div class="alert alert-info">
                            <h6>Bank Transfer Details</h6>
                            <p class="mb-1"><strong>Bank:</strong> KCB Bank</p>
                            <p class="mb-1"><strong>Account Name:</strong> EventPro Limited</p>
                            <p class="mb-1"><strong>Account Number:</strong> 1234567890</p>
                            <p class="mb-1"><strong>Branch:</strong> Nairobi Main</p>
                            <p class="mb-0"><strong>Reference:</strong> {{ $type === 'ticket' ? $purchase->order_number : ($booking->ticket_number ?? '') }}</p>
                        </div>
                        <div class="d-grid">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentForms = {
        'mpesa': document.getElementById('mpesa-form'),
        'card': document.getElementById('card-form'),
        'bank': document.getElementById('bank-form')
    };

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Hide all forms
            Object.values(paymentForms).forEach(form => {
                if (form) form.style.display = 'none';
            });

            // Show selected form
            const selectedForm = paymentForms[this.value];
            if (selectedForm) {
                selectedForm.style.display = 'block';
            }
        });
    });

    // Format phone number
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = '254' + value.substring(1);
            }
            e.target.value = value;
        });
    }
});
</script>

<style>
.payment-summary {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.summary-item {
    font-size: 14px;
}

.payment-form {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}
</style>
@endpush
