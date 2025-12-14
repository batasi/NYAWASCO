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
                            <div class="text-sm font-medium text-gray-900">{{ ($payment->customer?->first_name ?? '') . ' ' . ($payment->customer?->last_name ?? '') }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->customer?->email ?? '-' }}</div>
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
                        <div class="form-group md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Find Customer by Meter Number</label>
                            <div class="flex space-x-2">
                                <div class="flex-1 relative">
                                    <input
                                        type="text"
                                        name="meter_no"
                                        id="meter_no"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="Enter meter number..."
                                        autocomplete="off"
                                        spellcheck="false"
                                    >
                                </div>

                                <button type="button" id="searchMeterBtn"
                                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 flex items-center justify-center shadow-md">
                                    Search
                                </button>
                            </div>
                            <p id="meter_error" class="text-red-600 text-sm mt-2 hidden"></p>
                        </div>

                        <!-- Customer Info Display -->
                        <div id="customerInfoDisplay" class="md:col-span-2 hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-semibold text-blue-800 text-sm">Customer Information</h5>
                                        <p class="text-blue-700 text-sm mt-1" id="customerDisplayInfo"></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-user mr-1"></i> Found
                                        </span>
                                    </div>
                                </div>
                            </div>
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

                        <!-- Customer Name (hidden input) -->
                        <div class="form-group hidden">
                            <label class="block text-sm font-medium text-gray-700">Customer</label>
                            <input type="text" name="customer_name" id="customer_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <!-- Unpaid Bills Table -->
                    <div class="mt-6">
                        <h4 class="text-md font-semibold text-gray-700 mb-3">Unpaid Bills</h4>
                        <div id="unpaidBillsContainer" class="hidden">
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table id="unpaid-bills-table" class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill No</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <!-- Bills will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- No Bills Message -->
                        <div id="noBillsMessage" class="hidden text-center py-8 border-2 border-dashed border-gray-300 rounded-lg mt-4">
                            <i class="fas fa-file-invoice text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-500 font-medium">No unpaid bills found</p>
                            <p class="text-gray-400 text-sm mt-1">This customer has no outstanding bills</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <!-- Total Unpaid Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Due Amount</label>
                            <input type="text" id="due_amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                        <!-- Amount to Pay -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount to Pay *</label>
                            <input type="number" name="amount" id="payment_amount" step="0.01"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   required>
                            <p id="amount_validation" class="text-sm mt-1 hidden"></p>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method *</label>
                            <select name="payment_method" id="payment_method"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                                <option value="">- Select Payment Method -</option>
                                <option value="mpesa">MPESA</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>

                        <!-- Transaction Reference -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Transaction Reference *</label>
                            <input type="text" name="transaction_reference"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   required
                                   placeholder="Enter transaction reference number">
                        </div>
                    </div>

                    <!-- Payment Date -->
                    <div class="mt-4 d-none" style="display:none;">
                        <label class="block text-sm font-medium text-gray-700">Payment Date *</label>
                        <input type="date" name="payment_date"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               value="{{ date('Y-m-d') }}"
                               max="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <!-- Notes -->
                    <div class="mt-4 d-none" style="display:none;">
                        <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea name="notes" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Any additional notes about this payment..."></textarea>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex justify-end space-x-2 border-t pt-4 sticky bottom-0 bg-white z-10">
                        <button type="button" onclick="closePaymentModal()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                            Cancel
                        </button>
                        <button type="submit" id="submitPaymentBtn"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i> Save Payment
                        </button>
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

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    setTimeout(() => modalContent.classList.remove('translate-y-full'), 10);

    // Clear any previous data
    clearPaymentForm();
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    const modalContent = document.getElementById('paymentModalContent');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        clearPaymentForm();
    }, 300);
}

function clearPaymentForm() {
    // Clear all fields
    $('#meter_no').val('');
    $('#meter_model').val('');
    $('#meter_type').val('');
    $('#customer_name').val('');
    $('#due_amount').val('');
    $('#payment_amount').val('');
    $('#payment_method').val('');
    $('input[name="transaction_reference"]').val('');
    $('textarea[name="notes"]').val('');

    // Hide customer info
    $('#customerInfoDisplay').addClass('hidden');
    $('#customerDisplayInfo').text('');

    // Clear tables and messages
    $('#unpaid-bills-table tbody').empty();
    $('#unpaidBillsContainer').addClass('hidden');
    $('#noBillsMessage').addClass('hidden');

    // Clear errors
    $('#meter_error').addClass('hidden').text('');
    $('#amount_validation').addClass('hidden').text('');

    // Reset validation styles
    $('#meter_no').removeClass('border-red-500').removeClass('border-green-500');
    $('#payment_amount').removeClass('border-red-500').removeClass('border-green-500');
}

window.addEventListener('click', function (event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) closePaymentModal();
});

// Search meter functionality
$(document).ready(function() {
    // Search button click handler
    $('#searchMeterBtn').on('click', function() {
        searchMeter();
    });

    // Enter key press in meter number field
    $('#meter_no').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchMeter();
        }
    });

    // Validate payment amount
    $('#payment_amount').on('input', function() {
        validatePaymentAmount();
    });
});

function searchMeter() {
    let meter = $('#meter_no').val().trim();

    if (!meter) {
        $('#meter_error').removeClass('hidden').text('Please enter a meter number');
        $('#meter_no').addClass('border-red-500').removeClass('border-green-500');
        return;
    }

    // Show loading state
    const searchBtn = $('#searchMeterBtn');
    const originalText = searchBtn.html();
    searchBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Searching...');
    searchBtn.prop('disabled', true);

    // Clear previous error
    $('#meter_error').addClass('hidden').text('');
    $('#meter_no').removeClass('border-red-500').removeClass('border-green-500');

    $.ajax({
        url: `/bills/info/meter/${meter}?t=${Date.now()}`,
        method: 'GET',
        success: function (response) {
            // Update UI with success state
            $('#meter_no').addClass('border-green-500').removeClass('border-red-500');

            // Customer Info
            $('#customer_name').val(response.customer.name);

            // Display customer info
            $('#customerDisplayInfo').html(`
                <strong>${response.customer.name}</strong><br>
                ${response.customer.email ? response.customer.email + '<br>' : ''}
                ${response.customer.phone ? response.customer.phone : ''}
            `);
            $('#customerInfoDisplay').removeClass('hidden');

            // Meter Info
            $('#meter_model').val(response.meter.model);
            $('#meter_type').val(response.meter.type);

            // Total Due
            const totalDue = parseFloat(response.total_due) || 0;
            $('#due_amount').val('KSh ' + totalDue.toLocaleString('en-US', {minimumFractionDigits: 2}));

            // Bills
            let table = $('#unpaid-bills-table tbody');
            table.empty();

            if (response.unpaid_bills && response.unpaid_bills.length > 0) {
                response.unpaid_bills.forEach(bill => {
                    const dueDate = bill.due_date ? new Date(bill.due_date).toLocaleDateString() : 'N/A';
                    table.append(`
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">${bill.bill_number}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">KSh ${parseFloat(bill.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">KSh ${parseFloat(bill.due).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">${dueDate}</td>
                        </tr>
                    `);
                });

                $('#unpaidBillsContainer').removeClass('hidden');
                $('#noBillsMessage').addClass('hidden');
            } else {
                $('#unpaidBillsContainer').addClass('hidden');
                $('#noBillsMessage').removeClass('hidden');
            }

            // Set max payment amount
            $('#payment_amount').attr('max', totalDue);

            // Validate payment amount
            validatePaymentAmount();
        },
        error: function (xhr) {
            if (xhr.status === 404) {
                $('#meter_error').removeClass('hidden').text('Meter number not found. Please check and try again.');
                $('#meter_no').addClass('border-red-500').removeClass('border-green-500');

                // Clear all fields
                clearFields();
            } else {
                $('#meter_error').removeClass('hidden').text('An error occurred. Please try again.');
                $('#meter_no').addClass('border-red-500').removeClass('border-green-500');
            }
        },
        complete: function() {
            // Reset search button
            $('#searchMeterBtn').html(originalText);
            $('#searchMeterBtn').prop('disabled', false);
        }
    });
}

function clearFields() {
    $('#meter_model').val('');
    $('#meter_type').val('');
    $('#customer_name').val('');
    $('#due_amount').val('');
    $('#customerDisplayInfo').text('');
    $('#customerInfoDisplay').addClass('hidden');
    $('#unpaid-bills-table tbody').empty();
    $('#unpaidBillsContainer').addClass('hidden');
    $('#noBillsMessage').addClass('hidden');
}

function validatePaymentAmount() {
    const amountInput = $('#payment_amount');
    const amount = parseFloat(amountInput.val()) || 0;
    const totalDue = parseFloat($('#due_amount').val().replace(/[^0-9.-]+/g, '')) || 0;
    const validationMsg = $('#amount_validation');

    if (amount <= 0) {
        validationMsg.removeClass('hidden').addClass('text-gray-600').removeClass('text-green-600').removeClass('text-red-600');
        validationMsg.html('<i class="fas fa-info-circle mr-1"></i> Enter payment amount');
        amountInput.removeClass('border-red-500').removeClass('border-green-500');
        return false;
    }

    if (amount > totalDue) {
        validationMsg.removeClass('hidden').addClass('text-red-600').removeClass('text-green-600');
        validationMsg.html(`<i class="fas fa-exclamation-triangle mr-1"></i> Amount (${amount}) exceeds total due (${totalDue})`);
        amountInput.addClass('border-red-500').removeClass('border-green-500');
        return false;
    }

    // Valid amount
    validationMsg.removeClass('hidden').addClass('text-green-600').removeClass('text-red-600');
    validationMsg.html(`<i class="fas fa-check-circle mr-1"></i> Amount is within limits. Remaining: ${(totalDue - amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
    amountInput.removeClass('border-red-500').addClass('border-green-500');
    return true;
}

// Form submission validation
$('form').on('submit', function(e) {
    const meterNo = $('#meter_no').val().trim();
    const paymentAmount = parseFloat($('#payment_amount').val()) || 0;
    const paymentMethod = $('#payment_method').val();

    if (!meterNo) {
        e.preventDefault();
        $('#meter_error').removeClass('hidden').text('Please search for a meter first');
        $('#meter_no').addClass('border-red-500');
        $('#meter_no').focus();
        return false;
    }

    if (!validatePaymentAmount()) {
        e.preventDefault();
        $('#payment_amount').focus();
        return false;
    }

    if (!paymentMethod) {
        e.preventDefault();
        $('#payment_method').addClass('border-red-500');
        $('#payment_method').focus();
        return false;
    }

    // Show loading on submit button
    $('#submitPaymentBtn').html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...');
    $('#submitPaymentBtn').prop('disabled', true);
});
</script>
@endsection
@endcan
