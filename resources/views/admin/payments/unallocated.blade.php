@extends('layouts.app')

@section('title', 'Unallocated Payments - NYAWASCO')
@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
@endphp
@section('content')
@can('add payments')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>
<div class="min-h-screen bg-gray-50">
    @php
    $actionButtons = [
        [
            'route' => 'admin.payments.methods-report',
            'icon' => 'fas fa-chart-bar',
            'label' => 'Methods Report',
            'color' => 'bg-purple-600'
        ]
    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Unallocated Payments',
        'subtitle' => 'Allocate Payments to Customer Bills',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <form method="GET" action="{{ route('admin.payments.unallocated') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mpesa" {{ request('payment_method') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-end space-x-3">
                    <a href="{{ route('admin.payments.unallocated') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Reset
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Unallocated Payments</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $payments->total() }}</p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Amount</p>
                    <p class="text-2xl font-bold text-green-600">
                        KSh {{ number_format($payments->sum('amount'), 2) }}
                    </p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Average Age</p>
                    <p class="text-2xl font-bold text-blue-600">
                        @php
                            $avgAge = $payments->avg('age_days');
                        @endphp
                        {{ round($avgAge) }} days
                    </p>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Unallocated Payments</h3>
                    <p class="text-sm text-gray-600">Showing {{ $payments->count() }} payments</p>
                </div>

                <div class="flex space-x-3 mt-2 sm:mt-0">
                    <button onclick="allocateAll()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Auto-allocate All
                    </button>
                </div>
            </div>

            @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Details</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        @foreach($payments as $payment)
                        @php
                            $ageDays = \Carbon\Carbon::parse($payment->payment_date)->diffInDays(now());
                            $ageColor = $ageDays > 7 ? 'text-red-600' : ($ageDays > 3 ? 'text-yellow-600' : 'text-green-600');
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-4 py-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $payment->payment_no }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $payment->payment_method == 'cash' ? 'bg-green-100 text-green-800' :
                                               ($payment->payment_method == 'mpesa' ? 'bg-purple-100 text-purple-800' :
                                               ($payment->payment_method == 'bank' ? 'bg-blue-100 text-blue-800' :
                                               'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($payment->payment_method) }}
                                        </span>
                                        @if($payment->transaction_reference)
                                        <span class="ml-2 text-xs text-gray-500 truncate" title="{{ $payment->transaction_reference }}">
                                            Ref: {{ Str::limit($payment->transaction_reference, 15) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $payment->customer->first_name }} {{ $payment->customer->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $payment->customer->customer_number }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $payment->customer->phone }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-lg font-bold text-green-600">
                                    KSh {{ number_format($payment->amount, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $payment->payment_status }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm font-medium {{ $ageColor }}">
                                    {{ $ageDays }} days
                                </div>
                                <div class="text-xs text-gray-500">
                                    Since {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d') }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.payments.allocate.form', $payment) }}"
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center">
                                        <i class="fas fa-hand-holding-usd mr-1"></i>
                                        Allocate
                                    </a>

                                    <button onclick="autoAllocateSingle({{ $payment->id }})"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center">
                                        <i class="fas fa-bolt mr-1"></i>
                                        Auto
                                    </button>

                                    <button onclick="viewPayment({{ $payment->id }})"
                                            class="text-gray-600 hover:text-gray-800" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $payments->links() }}
            </div>

            @else
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-4xl text-green-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No unallocated payments</h3>
                <p class="text-gray-500">All payments have been allocated to bills.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function allocateAll() {
    if (confirm('Auto-allocate all unallocated payments? This will use oldest-first allocation method.')) {
        // Implement bulk auto-allocation
        alert('Bulk auto-allocation feature will be implemented');
    }
}

function autoAllocateSingle(paymentId) {
    fetch(`/admin/payments/${paymentId}/auto-allocate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ method: 'oldest_first' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment auto-allocated successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function viewPayment(paymentId) {
    // Implement view payment details
    alert('View payment: ' + paymentId);
}
</script>
@endcan
@endsection
