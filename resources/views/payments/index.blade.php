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

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Financial Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Payments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Payments</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            KSh {{ number_format($totalPayments, 2) }}
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            All time collection
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-green-100">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Collection -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Today's Collection</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">
                            KSh {{ number_format($todayCollection, 2) }}
                        </p>
                        <p class="text-xs text-blue-600 mt-1">
                            <i class="fas fa-calendar-day mr-1"></i>
                            {{ \Carbon\Carbon::today()->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-blue-100">
                        <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Payments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Completed</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $completedPaymentsCount }}</p>
                        <p class="text-xs text-purple-600 mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Successful payments
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-purple-100">
                        <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $pendingPaymentsCount }}</p>
                        <p class="text-xs text-yellow-600 mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            Awaiting confirmation
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-yellow-100">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats & Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <!-- Status Quick Filters -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('payments.index') }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        All Payments
                        <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $totalPaymentsCount }}</span>
                    </a>
                    <a href="{{ route('payments.index', ['status' => 'completed']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'completed' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        Completed
                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $completedPaymentsCount }}</span>
                    </a>
                    <a href="{{ route('payments.index', ['status' => 'pending']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'pending' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                        Pending
                        <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $pendingPaymentsCount }}</span>
                    </a>
                    <a href="{{ route('payments.index', ['status' => 'failed']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'failed' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                        Failed
                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $failedPaymentsCount }}</span>
                    </a>
                </div>

                <!-- Search Box -->
                <div class="flex space-x-2">
                    <div class="relative">
                        <input type="text"
                            id="paymentSearch"
                            placeholder="Search..."
                            class="w-64 border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            autocomplete="off">


                        <button id="searchPaymentBtn"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Search
                        </button>

                        <button id="resetPaymentBtn"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition hidden">
                            Reset
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Payments DataTable -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Payments Management</h2>
            </div>

            <!-- Payments Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="paymentsTableBody">
                        @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-semibold text-blue-600">{{ $payment->payment_no }}</div>
                                <div class="text-xs text-gray-500">#{{ $payment->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ($payment->customer?->first_name ?? '') . ' ' . ($payment->customer?->last_name ?? '') }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $payment->customer?->customer_number ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-400">{{ $payment->customer?->phone ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    KSh {{ number_format($payment->amount, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ ucfirst($payment->payment_method) ?? '—' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    @if($payment->payment_method == 'mpesa')
                                        <i class="fas fa-mobile-alt text-green-500 mr-2"></i>
                                        MPESA
                                    @elseif($payment->payment_method == 'bank')
                                        <i class="fas fa-university text-blue-500 mr-2"></i>
                                        Bank
                                    @elseif($payment->payment_method == 'cash')
                                        <i class="fas fa-money-bill text-green-500 mr-2"></i>
                                        Cash
                                    @else
                                        {{ ucfirst($payment->payment_method) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'failed' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusIcons = [
                                        'completed' => 'fa-check-circle',
                                        'pending' => 'fa-clock',
                                        'failed' => 'fa-exclamation-triangle'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$payment->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $statusIcons[$payment->payment_status] ?? 'fa-question-circle' }} mr-1"></i>
                                    {{ ucfirst($payment->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('payments.show', $payment->id) }}"
                                       class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded transition duration-200"
                                       title="View Payment">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit payments')
                                    <a href="{{ route('payments.edit', $payment->id) }}"
                                       class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded transition duration-200"
                                       title="Edit Payment">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete payments')
                                    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this payment?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded transition duration-200"
                                                title="Delete Payment">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-money-bill-wave text-4xl mb-3"></i>
                                    <p class="text-lg font-medium text-gray-900">No payments found</p>
                                    <p class="text-gray-500">Start by recording a payment.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $payments->links() }}
            </div>
            @endif
        </div>

        <!-- Quick Actions Panel -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    @php
                        $recentPayments = $payments->take(3);
                    @endphp
                    @forelse($recentPayments as $activity)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8
                            @if($activity->payment_status == 'completed') bg-green-100
                            @elseif($activity->payment_status == 'pending') bg-yellow-100
                            @else bg-red-100 @endif
                            rounded-full flex items-center justify-center">
                            <i class="fas
                                @if($activity->payment_status == 'completed') fa-check-circle text-green-600
                                @elseif($activity->payment_status == 'pending') fa-clock text-yellow-600
                                @else fa-exclamation-triangle text-red-600 @endif
                                text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700">
                                <strong>Payment {{ $activity->payment_no }}</strong>
                                of KSh {{ number_format($activity->amount, 2) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                            <p class="text-xs text-blue-600 mt-1">
                                By: {{ $activity->customer->first_name ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-info-circle mb-2"></i>
                        <p class="text-sm">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @can('view customers')
                    <a href="{{ route('admin.customers.index') }}"
                       class="w-full bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-users mr-2"></i>
                        View All Customers
                    </a>
                    @endcan
                    @can('view bills')
                    <a href="{{ route('bills.index') }}"
                       class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Bill Management
                    </a>
                    @endcan
                    @can('add payments')
                    <button onclick="openPaymentModal()"
                       class="w-full bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Record New Payment
                    </button>
                    @endcan
                </div>
            </div>
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
// Search functionality
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('paymentSearch');
    const searchBtn = document.getElementById('searchPaymentBtn');
    const resetBtn = document.getElementById('resetPaymentBtn');

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            const searchTerm = searchInput?.value.trim();
            if (searchTerm && searchTerm.length > 0) {
                performSearch(searchTerm);
                resetBtn?.classList.remove('hidden');
            }
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            window.location.href = "{{ route('payments.index') }}";
        });
    }

    // Enter key support
    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchBtn?.click();
            }
        });
    }

    async function performSearch(searchTerm) {
        try {
            // Show loading state
            const originalText = searchBtn.innerHTML;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Searching...';
            searchBtn.disabled = true;

            const response = await fetch(`/payments/search?search=${encodeURIComponent(searchTerm)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Search failed');

            const payments = await response.json();
            updateTableWithSearchResults(payments);

        } catch (error) {
            console.error(error);
            alert('Error performing search. Please try again.');
        } finally {
            // Reset button
            if (searchBtn) {
                searchBtn.innerHTML = originalText;
                searchBtn.disabled = false;
            }
        }
    }

    function updateTableWithSearchResults(payments) {
        const tbody = document.getElementById('paymentsTableBody');

        if (!tbody) return;

        if (!payments || payments.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-search text-4xl mb-3"></i>
                            <p class="text-lg font-medium text-gray-900">No payments found</p>
                            <p class="text-gray-500">No payments match your search criteria.</p>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = payments.map(payment => {
            const paymentDate = payment.payment_date
                ? new Date(payment.payment_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
                : '—';

            const statusColors = {
                'completed': 'bg-green-100 text-green-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'failed': 'bg-red-100 text-red-800'
            };

            const statusIcons = {
                'completed': 'fa-check-circle',
                'pending': 'fa-clock',
                'failed': 'fa-exclamation-triangle'
            };

            const methodIcons = {
                'mpesa': 'fa-mobile-alt text-green-500',
                'bank': 'fa-university text-blue-500',
                'cash': 'fa-money-bill text-green-500'
            };

            const paymentMethodIcon = methodIcons[payment.payment_method] || 'fa-money-bill-wave';

            return `
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-mono font-semibold text-blue-600">${payment.payment_no}</div>
                    <div class="text-xs text-gray-500">#${payment.id}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                ${payment.customer?.first_name || ''} ${payment.customer?.last_name || ''}
                            </div>
                            <div class="text-sm text-gray-500">${payment.customer?.customer_number || 'N/A'}</div>
                            <div class="text-xs text-gray-400">${payment.customer?.phone || 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">
                        KSh ${Number(payment.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}
                    </div>
                    <div class="text-xs text-gray-500">
                        ${payment.payment_method ? payment.payment_method.charAt(0).toUpperCase() + payment.payment_method.slice(1) : '—'}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex items-center">
                        <i class="fas ${paymentMethodIcon} mr-2"></i>
                        ${payment.payment_method ? payment.payment_method.charAt(0).toUpperCase() + payment.payment_method.slice(1) : '—'}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[payment.payment_status] || 'bg-gray-100 text-gray-800'}">
                        <i class="fas ${statusIcons[payment.payment_status] || 'fa-question-circle'} mr-1"></i>
                        ${payment.payment_status ? payment.payment_status.charAt(0).toUpperCase() + payment.payment_status.slice(1) : 'Unknown'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${paymentDate}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <a href="/payments/${payment.id}"
                           class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded transition duration-200"
                           title="View Payment">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/payments/${payment.id}/edit"
                           class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded transition duration-200"
                           title="Edit Payment">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="/payments/${payment.id}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this payment?')"
                              class="inline">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit"
                                    class="text-red-600 hover:text-red-900 px-2 py-1 rounded transition duration-200"
                                    title="Delete Payment">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            `;
        }).join('');
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.getElementById('paymentSearch');
        if (searchInput) searchInput.focus();
    }

    // Alt+N for new payment
    if (e.altKey && e.key === 'n') {
        e.preventDefault();
        openPaymentModal();
    }
});
</script>
@endsection
@endcan
