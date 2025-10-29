@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Complete Your Payment</h1>
            <p class="text-lg text-gray-200">Secure payment processing powered by Pesapal</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Order Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <!-- Progress Steps -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between max-w-md mx-auto">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Details</p>
                                </div>
                            </div>
                            <div class="flex-1 h-0.5 bg-green-500 mx-4"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-white">2</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Payment</p>
                                </div>
                            </div>
                            <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-gray-500">3</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Confirmation</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Payment Methods -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Select Payment Method</h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <!-- M-Pesa Option -->
                                <label class="payment-method-card">
                                    <input type="radio" name="payment_method" value="mpesa" class="sr-only" checked>
                                    <div class="card-content">
                                        <div class="icon-container bg-green-50">
                                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="method-info">
                                            <h3 class="method-title">M-Pesa</h3>
                                            <p class="method-description">Pay via M-Pesa</p>
                                        </div>
                                        <div class="checkmark">
                                            <div class="checkmark-circle"></div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Card Option -->
                                <label class="payment-method-card">
                                    <input type="radio" name="payment_method" value="card" class="sr-only">
                                    <div class="card-content">
                                        <div class="icon-container bg-blue-50">
                                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                        <div class="method-info">
                                            <h3 class="method-title">Card</h3>
                                            <p class="method-description">Credit/Debit Card</p>
                                        </div>
                                        <div class="checkmark">
                                            <div class="checkmark-circle"></div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Bank Transfer Option -->
                                <label class="payment-method-card">
                                    <input type="radio" name="payment_method" value="bank" class="sr-only">
                                    <div class="card-content">
                                        <div class="icon-container bg-purple-50">
                                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div class="method-info">
                                            <h3 class="method-title">Bank Transfer</h3>
                                            <p class="method-description">Direct Bank Transfer</p>
                                        </div>
                                        <div class="checkmark">
                                            <div class="checkmark-circle"></div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Payment Forms -->
                            <!-- M-Pesa Form -->
                            <div id="mpesa-form" class="payment-form">
                                <form action="{{ route('payment.complete', ['type' => $type, 'id' => $id]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="mpesa">

                                    <div class="space-y-4">
                                        <div>
                                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                                M-Pesa Phone Number
                                            </label>
                                            <div class="relative">
                                                <input type="tel"
                                                       id="phone_number"
                                                       name="phone_number"
                                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                                       placeholder="2547XXXXXXXX"
                                                       required>
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">Enter your M-Pesa registered phone number</p>
                                        </div>

                                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                            <div class="flex">
                                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>
                                                    <p class="text-sm text-blue-800 font-medium">Payment Instructions</p>
                                                    <p class="text-sm text-blue-700 mt-1">You will receive a prompt on your phone to enter your M-Pesa PIN to complete the payment.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex space-x-4 pt-4">
                                            <a href="{{ url()->previous() }}" class="flex-1 bg-gray-100 text-gray-700 py-3 px-6 rounded-xl font-semibold text-center hover:bg-gray-200 transition-colors duration-200">
                                                Cancel
                                            </a>
                                            <button type="submit" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                                Pay with M-Pesa
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Card Form -->
                            <div id="card-form" class="payment-form hidden">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                                    <svg class="w-12 h-12 text-yellow-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Card Payments Unavailable</h3>
                                    <p class="text-yellow-700">Credit/Debit card payments are currently being integrated. Please use M-Pesa for now.</p>
                                </div>
                            </div>

                            <!-- Bank Transfer Form -->
                            <div id="bank-form" class="payment-form hidden">
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bank Transfer Details</h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Bank:</span>
                                            <span class="font-semibold">KCB Bank</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Account Name:</span>
                                            <span class="font-semibold">EventPro Limited</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Account Number:</span>
                                            <span class="font-semibold">1234567890</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-gray-600">Branch:</span>
                                            <span class="font-semibold">Nairobi Main</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-gray-600">Reference:</span>
                                            <span class="font-semibold text-blue-600">{{ $type === 'ticket' ? $purchase->order_number : ($booking->ticket_number ?? '') }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <p class="text-sm text-blue-800 text-center">
                                            Please use the reference number above when making the transfer. Your booking will be confirmed once payment is received.
                                        </p>
                                    </div>
                                    <div class="mt-6">
                                        <a href="{{ url()->previous() }}" class="w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-xl font-semibold text-center hover:bg-gray-200 transition-colors duration-200 block">
                                            Back to Booking
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 sticky top-8">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-900">Order Summary</h2>
                    </div>

                    <div class="p-6">
                        @if($type === 'ticket' && isset($purchase))
                        <div class="space-y-4">
                            <!-- Event Info -->
                            <div class="flex items-start space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $purchase->event->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $purchase->ticket->name }}</p>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-3 pt-4 border-t border-gray-100">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal ({{ $purchase->quantity }} × KES {{ number_format($purchase->unit_price, 2) }})</span>
                                    <span class="text-gray-900">KES {{ number_format($purchase->total_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax (16%)</span>
                                    <span class="text-gray-900">KES {{ number_format($purchase->tax_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Service Fee (2%)</span>
                                    <span class="text-gray-900">KES {{ number_format($purchase->fee_amount, 2) }}</span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-xl font-bold text-green-600">KES {{ number_format($purchase->final_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @elseif($type === 'booking' && isset($booking))
                        <div class="space-y-4">
                            <!-- Booking Info -->
                            <div class="flex items-start space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $booking->event->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Booking #{{ $booking->ticket_number }}</p>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                                    <span class="text-xl font-bold text-green-600">KES {{ number_format($booking->amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Security Badge -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span>Secure payment powered by Pesapal</span>
                            </div>
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
    // Payment method selection
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
                if (form) form.classList.add('hidden');
            });

            // Show selected form
            const selectedForm = paymentForms[this.value];
            if (selectedForm) {
                selectedForm.classList.remove('hidden');
            }

            // Update card states
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.classList.remove('selected');
            });
            this.closest('.payment-method-card').classList.add('selected');
        });
    });

    // Format phone number
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = '254' + value.substring(1);
            } else if (value.startsWith('7') || value.startsWith('1')) {
                value = '254' + value;
            }
            e.target.value = value;
        });

        // Auto-format on load if there's a value
        if (phoneInput.value) {
            let value = phoneInput.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                phoneInput.value = '254' + value.substring(1);
            }
        }
    }

    // Initialize first payment method as selected
    document.querySelector('input[name="payment_method"]:checked')
        .closest('.payment-method-card')
        .classList.add('selected');
});
</script>

<style>
.payment-method-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    background: white;
}

.payment-method-card:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.payment-method-card.selected {
    border-color: #3b82f6;
    background-color: #f0f9ff;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.card-content {
    display: flex;
    align-items: center;
}

.icon-container {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.method-info {
    flex: 1;
}

.method-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 2px;
}

.method-description {
    font-size: 0.875rem;
    color: #6b7280;
}

.checkmark {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.payment-method-card.selected .checkmark {
    border-color: #3b82f6;
    background-color: #3b82f6;
}

.checkmark-circle {
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.payment-method-card.selected .checkmark-circle {
    opacity: 1;
}

.payment-form {
    transition: all 0.3s ease;
}

/* Smooth animations */
.hidden {
    display: none !important;
}

/* Custom scrollbar */
.payment-summary::-webkit-scrollbar {
    width: 6px;
}

.payment-summary::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.payment-summary::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.payment-summary::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush
