@extends('layouts.app')

@section('title', 'Allocate Payment - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>
<div class="min-h-screen bg-gray-50">
    @php
    $actionButtons = [
        [
            'route' => 'admin.payments.unallocated',
            'icon' => 'fas fa-arrow-left',
            'label' => 'Back',
            'color' => 'bg-gray-600'
        ]
    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Allocate Payment',
        'subtitle' => 'Allocate Payment to Customer Bills',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <div class="max-w-6xl mx-auto">
            <!-- Payment Summary -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Payment Number</p>
                                <p class="font-medium text-gray-900">{{ $payment->payment_no }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Date</p>
                                <p class="font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Method</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $payment->payment_method == 'cash' ? 'bg-green-100 text-green-800' :
                                       ($payment->payment_method == 'mpesa' ? 'bg-purple-100 text-purple-800' :
                                       ($payment->payment_method == 'bank' ? 'bg-blue-100 text-blue-800' :
                                       'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Reference</p>
                                <p class="font-medium text-gray-900">{{ $payment->transaction_reference ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-xl p-4">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-600">Payment Amount</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">
                                KSh {{ number_format($payment->amount, 2) }}
                            </p>
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">Customer Balance</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    KSh {{ number_format($customer->credit_balance, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Customer Name</p>
                        <p class="font-medium text-gray-900">
                            {{ $customer->first_name }} {{ $customer->last_name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Account Number</p>
                        <p class="font-medium text-gray-900">{{ $customer->customer_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-medium text-gray-900">{{ $customer->phone }}</p>
                    </div>
                </div>
            </div>

            <!-- Allocation Options -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Allocation Method</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($allocationOptions as $key => $label)
                    <button onclick="applyAllocationMethod('{{ $key }}')"
                            class="p-4 border border-gray-300 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition text-left">
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg bg-blue-100 mr-3">
                                <i class="fas fa-{{ $key == 'manual' ? 'hand-pointer' : ($key == 'oldest_first' ? 'sort-amount-down' : 'sort-amount-up') }} text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $label }}</p>
                                <p class="text-sm text-gray-500 mt-1">Click to apply</p>
                            </div>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Allocation Form -->
            <form id="allocationForm" method="POST" action="{{ route('admin.payments.allocate', $payment) }}">
                @csrf

                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Allocate to Bills</h3>

                    <!-- Payment Amount Summary -->
                    <div class="bg-blue-50 rounded-xl p-4 mb-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Payment Amount</p>
                                <p class="text-2xl font-bold text-green-600">
                                    KSh <span id="paymentAmount">{{ number_format($payment->amount, 2) }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-700">Allocated</p>
                                <p class="text-2xl font-bold text-blue-600">
                                    KSh <span id="allocatedTotal">0.00</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-700">Remaining</p>
                                <p class="text-2xl font-bold {{ $payment->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    KSh <span id="remainingAmount">{{ number_format($payment->amount, 2) }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                            <div id="allocationProgress" class="h-2 rounded-full bg-green-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Bills Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Late Fee</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocate Principal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocate Late Fee</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Allocated</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white/50 divide-y divide-gray-200" id="billsTableBody">
                                @foreach($unpaidBills as $bill)
                                @php
                                    $isOverdue = $bill->due_date < now();
                                    $principalBalance = $bill->balance - $bill->late_fee;
                                @endphp
                                <tr class="{{ $isOverdue ? 'bg-red-50/50' : 'hover:bg-gray-50/50' }} transition duration-150">
                                    <td class="px-4 py-4">
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $bill->bill_number }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($bill->billing_period_start)->format('M d') }} -
                                                {{ \Carbon\Carbon::parse($bill->billing_period_end)->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="text-sm {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            {{ \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') }}
                                        </div>
                                        @if($isOverdue)
                                        <div class="text-xs text-red-500">
                                            {{ \Carbon\Carbon::parse($bill->due_date)->diffForHumans() }}
                                        </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            KSh {{ number_format($bill->total_amount, 2) }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="text-lg font-bold text-gray-900">
                                            KSh {{ number_format($bill->balance, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Paid: KSh {{ number_format($bill->paid_amount, 2) }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="text-sm text-red-600">
                                            KSh {{ number_format($bill->late_fee, 2) }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               max="{{ $principalBalance }}"
                                               data-bill-id="{{ $bill->id }}"
                                               data-field="principal"
                                               class="w-32 principal-input border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="0.00"
                                               onchange="updateAllocation()">
                                    </td>

                                    <td class="px-4 py-4">
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               max="{{ $bill->late_fee }}"
                                               data-bill-id="{{ $bill->id }}"
                                               data-field="late_fee"
                                               class="w-32 late-fee-input border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="0.00"
                                               onchange="updateAllocation()">
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="text-sm font-semibold text-blue-600 bill-total" data-bill-id="{{ $bill->id }}">
                                            KSh 0.00
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                            <!-- Totals Row -->
                            <tfoot class="bg-gray-50/80">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                        Totals:
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-semibold text-gray-900" id="totalPrincipal">
                                            KSh 0.00
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-semibold text-gray-900" id="totalLateFee">
                                            KSh 0.00
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-lg font-bold text-green-600" id="grandTotal">
                                            KSh 0.00
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allocation Notes</label>
                        <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any notes about this allocation..."></textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('admin.payments.unallocated') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                                id="submitBtn"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            Complete Allocation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let paymentAmount = {{ $payment->amount }};

function applyAllocationMethod(method) {
    fetch(`/admin/payments/{{ $payment->id }}/auto-allocate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ method: method })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear all inputs first
            document.querySelectorAll('.principal-input, .late-fee-input').forEach(input => {
                input.value = '';
            });

            // Apply allocations
            data.allocations.forEach(allocation => {
                const principalInput = document.querySelector(`.principal-input[data-bill-id="${allocation.bill_id}"]`);
                const lateFeeInput = document.querySelector(`.late-fee-input[data-bill-id="${allocation.bill_id}"]`);

                if (principalInput) principalInput.value = allocation.principal_amount.toFixed(2);
                if (lateFeeInput) lateFeeInput.value = allocation.late_fee_amount.toFixed(2);
            });

            updateAllocation();

            if (data.remaining_amount > 0) {
                alert(`Allocation applied. Remaining amount: KSh ${data.remaining_amount.toFixed(2)} will be credited to customer account.`);
            }
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function updateAllocation() {
    let totalPrincipal = 0;
    let totalLateFee = 0;
    let grandTotal = 0;

    // Calculate totals
    document.querySelectorAll('.principal-input').forEach(input => {
        const value = parseFloat(input.value) || 0;
        const max = parseFloat(input.max);

        if (value > max) {
            input.value = max.toFixed(2);
            totalPrincipal += max;
        } else {
            totalPrincipal += value;
        }
    });

    document.querySelectorAll('.late-fee-input').forEach(input => {
        const value = parseFloat(input.value) || 0;
        const max = parseFloat(input.max);

        if (value > max) {
            input.value = max.toFixed(2);
            totalLateFee += max;
        } else {
            totalLateFee += value;
        }
    });

    grandTotal = totalPrincipal + totalLateFee;

    // Update display totals
    document.getElementById('totalPrincipal').textContent = 'KSh ' + totalPrincipal.toFixed(2);
    document.getElementById('totalLateFee').textContent = 'KSh ' + totalLateFee.toFixed(2);
    document.getElementById('grandTotal').textContent = 'KSh ' + grandTotal.toFixed(2);
    document.getElementById('allocatedTotal').textContent = grandTotal.toFixed(2);

    // Update remaining amount
    const remaining = paymentAmount - grandTotal;
    document.getElementById('remainingAmount').textContent = remaining.toFixed(2);

    // Update progress bar
    const progress = (grandTotal / paymentAmount) * 100;
    document.getElementById('allocationProgress').style.width = Math.min(progress, 100) + '%';

    // Update individual bill totals
    document.querySelectorAll('.principal-input').forEach(input => {
        const billId = input.dataset.billId;
        const principal = parseFloat(input.value) || 0;
        const lateFeeInput = document.querySelector(`.late-fee-input[data-bill-id="${billId}"]`);
        const lateFee = parseFloat(lateFeeInput?.value) || 0;
        const billTotal = principal + lateFee;

        const billTotalElement = document.querySelector(`.bill-total[data-bill-id="${billId}"]`);
        if (billTotalElement) {
            billTotalElement.textContent = 'KSh ' + billTotal.toFixed(2);
        }
    });

    // Enable/disable submit button
    const submitBtn = document.getElementById('submitBtn');
    if (grandTotal > 0 && grandTotal <= paymentAmount) {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }

    // Color code remaining amount
    const remainingElement = document.getElementById('remainingAmount');
    if (remaining < 0) {
        remainingElement.parentElement.classList.remove('text-green-600');
        remainingElement.parentElement.classList.add('text-red-600');
    } else {
        remainingElement.parentElement.classList.remove('text-red-600');
        remainingElement.parentElement.classList.add('text-green-600');
    }
}

// Prepare form data before submission
document.getElementById('allocationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const allocations = [];
    const billRows = document.querySelectorAll('tbody tr');

    billRows.forEach(row => {
        const principalInput = row.querySelector('.principal-input');
        const lateFeeInput = row.querySelector('.late-fee-input');

        if (principalInput && lateFeeInput) {
            const billId = principalInput.dataset.billId;
            const principalAmount = parseFloat(principalInput.value) || 0;
            const lateFeeAmount = parseFloat(lateFeeInput.value) || 0;

            if (principalAmount > 0 || lateFeeAmount > 0) {
                allocations.push({
                    bill_id: billId,
                    principal_amount: principalAmount,
                    late_fee_amount: lateFeeAmount
                });
            }
        }
    });

    // Create hidden input for allocations
    const allocationsInput = document.createElement('input');
    allocationsInput.type = 'hidden';
    allocationsInput.name = 'allocations';
    allocationsInput.value = JSON.stringify(allocations);

    this.appendChild(allocationsInput);
    this.submit();
});

// Initialize allocation update
updateAllocation();
</script>
@endsection
