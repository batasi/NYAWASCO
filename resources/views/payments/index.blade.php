@extends('layouts.app')
@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Auth;
@endphp
@can('view payments')

<div class="min-h-screen bg-gray-50">

    @php
    $actionButtons = [];

    if (auth()->user()->can('add payments')) {
        $actionButtons[] = [
            'text' => 'Add Payment',
            'onclick' => 'openPaymentModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Payments Management',
        'subtitle' => 'Financial Management Platform',
        'actionButtons' => $actionButtons
    ])

    <!-- Filters and Search Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Payments</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" placeholder="Search by payment number, customer..."
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 pr-12 py-3 text-sm border-gray-300 rounded-md">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-10 py-3 text-sm border-gray-300 rounded-md">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-50 border-b border-blue-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Payment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Payment Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-blue-100">
                    @forelse ($payments as $payment)
                    <tr class="hover:bg-blue-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $payment->payment_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $payment->user?->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->user?->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">KSh {{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($payment->payment_method) ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'completed' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-blue-100 text-blue-800',
                                    'failed' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$payment->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($payment->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('payments.show', $payment->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <a href="{{ route('payments.edit', $payment->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('Delete this payment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors duration-200">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-blue-100 bg-blue-50">
            {{ $payments->links() }}
        </div>
    </div>
</div>

<!-- Modal -->

    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="flex items-end justify-center w-full min-h-screen px-4 pb-8">
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl mx-auto my-8" id="paymentModalContent">
            <!-- Header -->
            <div class="flex justify-between items-center border-b px-6 py-4 sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-800">Create New Payment</h3>
                <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>
            <!-- Body -->
            <div class="px-6 py-4 overflow-y-auto" style="max-height: calc(100vh - 8rem);">
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Search by Meter Number -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Meter Number *</label>
                            <input type="text" name="meter_no" id="meter_no" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                                placeholder="Search assigned meters...">
                            <p id="meter_error" class="text-red-600 text-sm mt-1 hidden"></p>
                            <small class="text-gray-500 text-xs mt-1">
                                Only assigned meters will be found. Start typing meter number to see suggestions.
                            </small>
                        </div>

                        <!-- Meter Model -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Meter Model</label>
                            <input type="text" id="meter_model" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                        <!-- Meter Type -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Meter Type</label>
                            <input type="text" id="meter_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                        <!-- Auto-filled Customer Name -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Customer</label>
                            <input type="text" id="customer_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                    </div>
                    <!-- Unpaid Bills Table -->
                    <h4 class="mt-6 font-semibold text-gray-700">Unpaid Bills</h4>
                    <table id="unpaid-bills-table" class="table-auto w-full mt-2 border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-2 py-1">Bill No</th>
                                <th class="border px-2 py-1">Total</th>
                                <th class="border px-2 py-1">Due</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <br>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Total Unpaid Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Due Amount</label>
                            <input type="text" id="due_amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                        <!-- Amount to Pay -->
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount to Pay *</label>
                            <input type="number" name="amount" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <small class="text-gray-500 text-xs">Enter amount not exceeding total due</small>
                        </div>

                        <!-- Update payment method options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method *</label>
                            <select name="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">- Select Payment Method -</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                            </select>
                        </div>

                        <!-- Transaction Reference -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Transaction Reference</label>
                            <input type="text" name="transaction_reference" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>


                    </div>



                    <!-- Footer -->
                    <div class="mt-6 flex justify-end space-x-2 border-t pt-4 sticky bottom-0 bg-white z-10">
                        <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Payment</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    </div>




<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
/* Modal Open/Close */
function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    const modalContent = document.getElementById('paymentModalContent');

    // Reset form when opening modal
    document.querySelector('form').reset();
    clearMeterDetails();
    
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    setTimeout(() => {
        modalContent.classList.remove('translate-y-full');
        initializeAutocomplete();
    }, 10);
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    const modalContent = document.getElementById('paymentModalContent');

    modalContent.classList.add('translate-y-full');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        // Destroy autocomplete to prevent multiple instances
        $('#meter_no').autocomplete('destroy');
    }, 300);
}

window.addEventListener('click', function (event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) closePaymentModal();
});

// Initialize autocomplete
function initializeAutocomplete() {
    $('#meter_no').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '/payments/search-meters',
                data: { search: request.term },
                success: function(data) {
                    console.log('Autocomplete results:', data);
                    if (data.length > 0) {
                        response(data.map(function(item) {
                            return {
                                label: item.display_text,
                                value: item.meter_number,
                                customer: item.customer_name
                            };
                        }));
                    } else {
                        response([]);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Autocomplete error:', error);
                    response([]);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            console.log('Meter selected from autocomplete:', ui.item.value);
            // Set the value
            $(this).val(ui.item.value);
            // Search for the exact meter number
            searchMeterDetails(ui.item.value);
            return false;
        },
        focus: function(event, ui) {
            // Prevent the input value from changing on focus
            event.preventDefault();
        }
    });
}

// Meter number search with debounce
let meterSearchTimeout;
$(document).on('input', '#meter_no', function () {
    let meter = $(this).val().trim();
    
    // Clear previous timeout
    clearTimeout(meterSearchTimeout);
    
    // Clear previous results immediately
    clearMeterDetails();
    
    // Only search when at least 3 characters are typed (reduced from 2 to 3)
    if (meter.length < 3) {
        return;
    }

    // Debounce the search
    meterSearchTimeout = setTimeout(() => {
        searchMeterDetails(meter);
    }, 600); // Increased debounce time
});

function searchMeterDetails(meterNumber) {
    console.log('Searching for assigned meter:', meterNumber);
    
    // Show loading state
    $('#meter_no').addClass('loading');
    
    const url = `/payments/meter-details/${encodeURIComponent(meterNumber)}`;
    console.log('Making request to:', url);
    
    $.ajax({
        url: url,
        method: 'GET',
        success: function (response) {
            console.log('Meter details response:', response);
            $('#meter_no').removeClass('loading');
            
            if (response.success) {
                // Clear any error message
                $("#meter_error").addClass("hidden").text("");
                
                // Populate customer info
                $('#customer_name').val(response.customer.name);
                
                // Populate meter info
                $('#meter_model').val(response.meter.model);
                $('#meter_type').val(response.meter.type);
                
                // Populate total due
                $('#due_amount').val('KSh ' + response.total_due);
                
                // Populate unpaid bills table
                let table = $('#unpaid-bills-table tbody');
                table.empty();
                
                if (response.unpaid_bills.length > 0) {
                    response.unpaid_bills.forEach(bill => {
                        table.append(`
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 text-sm">${bill.bill_number}</td>
                                <td class="border px-3 py-2 text-sm text-right">KSh ${bill.total_amount}</td>
                                <td class="border px-3 py-2 text-sm text-right">KSh ${bill.due}</td>
                            </tr>
                        `);
                    });
                    
                    // Auto-fill amount to pay with total due
                    const numericDue = response.total_due.replace('KSh ', '').replace(/,/g, '');
                    $('input[name="amount"]').val(numericDue);
                    
                } else {
                    table.append(`
                        <tr>
                            <td colspan="3" class="border px-3 py-2 text-sm text-center text-gray-500">
                                No unpaid bills found
                            </td>
                        </tr>
                    `);
                    $('input[name="amount"]').val('');
                }
                
                // Show active meter status
                $('#meter_no').after(`
                    <div id="meter_status" class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ✓ Active Meter - ${response.customer.name}
                    </div>
                `);
                
            } else {
                console.log('Assigned meter not found for:', meterNumber);
                // Don't show error message - just keep fields clear
                clearMeterDetails();
            }
        },
        error: function (xhr, status, error) {
            console.log('AJAX Error:', { 
                status: xhr.status, 
                error: error 
            });
            $('#meter_no').removeClass('loading');
            
            // For 404 (not found), just clear fields without error message
            if (xhr.status === 404) {
                clearMeterDetails();
            } else {
                // Only show error for server errors
                showMeterError("Error connecting to server");
            }
        }
    });
}

function clearMeterDetails() {
    $('#customer_name').val("");
    $('#meter_model').val("");
    $('#meter_type').val("");
    $('#due_amount').val("");
    $('input[name="amount"]').val("");
    $('#unpaid-bills-table tbody').empty();
    $('#meter_status').remove();
    $("#meter_error").addClass("hidden").text("");
}

function showMeterError(message) {
    $("#meter_error")
        .removeClass("hidden")
        .text(message);
}

// Form submission handling
$(document).on('submit', 'form', function(e) {
    const amount = parseFloat($('input[name="amount"]').val()) || 0;
    const dueAmountText = $('#due_amount').val();
    const customerName = $('#customer_name').val().trim();
    
    // Extract numeric value from "KSh X,XXX.XX" format
    const dueAmount = parseFloat(dueAmountText.replace('KSh ', '').replace(/,/g, '')) || 0;
    
    if (amount > dueAmount) {
        e.preventDefault();
        alert('Payment amount cannot exceed total due amount.');
        return false;
    }
    
    if (amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid payment amount.');
        return false;
    }
    
    // Validate that we have a valid customer
    if (!customerName || customerName === '' || customerName === 'Customer Record Missing') {
        e.preventDefault();
        alert('Please select a valid assigned meter with customer information.');
        return false;
    }
    
    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...');
});

// Add CSS for loading state and autocomplete
const style = document.createElement('style');
style.textContent = `
    .loading {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233b82f6' stroke-width='2'%3E%3Cpath d='M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 16px 16px;
    }
    
    /* jQuery UI Autocomplete Styles */
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 10000;
    }
    
    .ui-menu-item {
        padding: 8px 12px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        font-size: 14px;
    }
    
    .ui-menu-item:hover {
        background-color: #f3f4f6;
    }
    
    .ui-state-focus {
        background-color: #3b82f6 !important;
        color: white !important;
        border: none;
    }
`;
document.head.appendChild(style);
</script>

@endsection
@endcan
