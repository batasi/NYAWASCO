@extends('layouts.app')

@section('title', 'Purchase Tickets - ' . $event->title)

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp
<div class="min-h-screen bg-gray-900">
    <!-- Header Section -->
    <div class="bg-black shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-white">
                        Purchase Tickets
                    </h1>
                    <p class="mt-2 text-lg text-gray-400">
                        {{ $event->title }}
                    </p>
                </div>
                <a href="{{ route('events.show', $event) }}"
                   class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Back to Event
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 p-6">
            <!-- Event Info -->
            <div class="mb-8 p-6 bg-gray-750 rounded-lg border border-gray-600">
                <div class="flex items-start space-x-4">
                    @if($event->banner_image)
                    <img src="{{ Storage::disk('public')->url($event->banner_image) }}"
                         alt="{{ $event->title }}"
                         class="w-24 h-24 rounded-lg object-cover">
                    @else
                    <div class="w-24 h-24 rounded-lg bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-white">{{ $event->title }}</h2>
                        <p class="text-gray-300 mt-2">{{ $event->description }}</p>
                        <div class="mt-3 flex flex-wrap gap-4 text-sm text-gray-400">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $event->start_date->format('M j, Y g:i A') }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $event->location }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets Selection -->
            <form action="{{ route('tickets.purchase', $event) }}" method="POST">
                @csrf

                <div class="space-y-6">
                    @foreach($tickets as $ticket)
                    <div class="ticket-option border border-gray-600 rounded-lg p-6 hover:border-purple-500 transition-colors duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-white">{{ $ticket->name }}</h3>
                                <p class="text-gray-300 mt-2">{{ $ticket->description }}</p>

                                <div class="mt-4 flex items-center space-x-6 text-sm text-gray-400">
                                    @if($ticket->quantity_available)
                                    <span>{{ $ticket->quantity_available - $ticket->quantity_sold }} available</span>
                                    @endif
                                    <span>Max {{ $ticket->max_per_order }} per order</span>
                                </div>

                                @if($ticket->sale_start_date || $ticket->sale_end_date)
                                <div class="mt-2 text-xs text-gray-500">
                                    @if($ticket->sale_start_date)
                                    Sale starts: {{ $ticket->sale_start_date->format('M j, Y g:i A') }}
                                    @endif
                                    @if($ticket->sale_end_date)
                                    <br>Sale ends: {{ $ticket->sale_end_date->format('M j, Y g:i A') }}
                                    @endif
                                </div>
                                @endif
                            </div>

                            <div class="text-right">
                                <div class="text-2xl font-bold text-white">
                                    @if($ticket->price > 0)
                                    KES {{ number_format($ticket->price, 2) }}
                                    @else
                                    FREE
                                    @endif
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-purple-400 mb-2">Quantity</label>
                                    <select name="quantity" class="ticket-quantity bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                            data-max="{{ $ticket->max_per_order }}"
                                            data-available="{{ $ticket->quantity_available ? $ticket->quantity_available - $ticket->quantity_sold : 999 }}"
                                            data-price="{{ $ticket->price }}">
                                        @for($i = 1; $i <= min(10, $ticket->max_per_order, ($ticket->quantity_available ? $ticket->quantity_available - $ticket->quantity_sold : 10)); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <button type="button" class="select-ticket mt-3 w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200"
                                        data-ticket-id="{{ $ticket->id }}">
                                    Select Ticket
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Selected Ticket Summary -->
                <div id="ticketSummary" class="mt-8 p-6 bg-gray-750 rounded-lg border border-gray-600 hidden">
                    <h3 class="text-lg font-semibold text-white mb-4">Order Summary</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-300">
                            <span>Tickets:</span>
                            <span id="summaryQuantity">0</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Price per ticket:</span>
                            <span id="summaryPrice">KES 0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Subtotal:</span>
                            <span id="summarySubtotal">KES 0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Tax (16%):</span>
                            <span id="summaryTax">KES 0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Service fee (2%):</span>
                            <span id="summaryFee">KES 0.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold text-white border-t border-gray-600 pt-3">
                            <span>Total:</span>
                            <span id="summaryTotal">KES 0.00</span>
                        </div>
                    </div>

                    <!-- Attendee Information -->
                    <div id="attendeeInfo" class="mt-6 space-y-4 hidden">
                        <h4 class="text-md font-semibold text-white">Attendee Information</h4>
                        <div class="space-y-3" id="attendeeFields"></div>
                    </div>

                    <input type="hidden" name="ticket_id" id="selectedTicketId">

                    <div class="mt-6">
                        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-lg font-semibold transition-colors duration-200">
                            Proceed to Payment
                        </button>
                    </div>
                </div>
            </form>

            @if($tickets->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-white">No tickets available</h3>
                <p class="mt-2 text-sm text-gray-400">There are no tickets available for this event at the moment.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedTicket = null;

    // Ticket selection
    document.querySelectorAll('.select-ticket').forEach(button => {
        button.addEventListener('click', function() {
            const ticketId = this.dataset.ticketId;
            const ticketElement = this.closest('.ticket-option');
            const quantitySelect = ticketElement.querySelector('.ticket-quantity');
            const quantity = parseInt(quantitySelect.value);
            const price = parseFloat(quantitySelect.dataset.price);

            // Update selected ticket
            selectedTicket = {
                id: ticketId,
                quantity: quantity,
                price: price
            };

            // Update form
            document.getElementById('selectedTicketId').value = ticketId;

            // Update summary
            updateSummary(quantity, price);

            // Show attendee info fields
            showAttendeeInfo(quantity);

            // Show summary
            document.getElementById('ticketSummary').classList.remove('hidden');

            // Scroll to summary
            document.getElementById('ticketSummary').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        });
    });

    // Quantity change
    document.querySelectorAll('.ticket-quantity').forEach(select => {
        select.addEventListener('change', function() {
            if (selectedTicket && selectedTicket.id === this.closest('.ticket-option').querySelector('.select-ticket').dataset.ticketId) {
                const quantity = parseInt(this.value);
                const price = parseFloat(this.dataset.price);
                selectedTicket.quantity = quantity;
                selectedTicket.price = price;
                updateSummary(quantity, price);
                showAttendeeInfo(quantity);
            }
        });
    });

    function updateSummary(quantity, price) {
        const subtotal = quantity * price;
        const tax = subtotal * 0.16;
        const fee = subtotal * 0.02;
        const total = subtotal + tax + fee;

        document.getElementById('summaryQuantity').textContent = quantity;
        document.getElementById('summaryPrice').textContent = `KES ${price.toFixed(2)}`;
        document.getElementById('summarySubtotal').textContent = `KES ${subtotal.toFixed(2)}`;
        document.getElementById('summaryTax').textContent = `KES ${tax.toFixed(2)}`;
        document.getElementById('summaryFee').textContent = `KES ${fee.toFixed(2)}`;
        document.getElementById('summaryTotal').textContent = `KES ${total.toFixed(2)}`;
    }

    function showAttendeeInfo(quantity) {
        const container = document.getElementById('attendeeFields');
        container.innerHTML = '';

        for (let i = 1; i <= quantity; i++) {
            const attendeeHtml = `
                <div class="bg-gray-700 p-4 rounded-lg border border-gray-600">
                    <h5 class="text-white font-medium mb-3">Attendee ${i}</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-purple-400 mb-1">Full Name</label>
                            <input type="text" name="attendee_info[${i-1}][name]"
                                   class="w-full bg-gray-600 border border-gray-500 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-purple-400 mb-1">Email</label>
                            <input type="email" name="attendee_info[${i-1}][email]"
                                   class="w-full bg-gray-600 border border-gray-500 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   required>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += attendeeHtml;
        }

        document.getElementById('attendeeInfo').classList.remove('hidden');
    }
});
</script>

<style>
.ticket-option {
    transition: all 0.3s ease;
}

.ticket-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
}
</style>
@endsection
