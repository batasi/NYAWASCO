@extends('layouts.app')

@section('title', 'Billing Management - NYAWASCO')

@section('content')
@can('view bills')
<style>
/* Print button hover effects */
.print-receipt-btn {
    position: relative;
    transition: all 0.3s ease;
}

.print-receipt-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(128, 0, 128, 0.3);
}

.print-receipt-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
}

/* Loading spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spinner.fa-spin {
    animation: spin 1s linear infinite;
}
</style>
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
        @php
                $actionButtons = [];

                if (auth()->user()->can('add bills')) {
                    $actionButtons[] = [
                        'text' => 'Record',
                        'onclick' => 'openQuickBillModal()',
                        'icon' => 'fas fa-bolt',
                        'color' => 'bg-green-600 hover:bg-green-700'
                    ];}
                @endphp

                @include('components.dashboard-header', [
                    'title' => 'Billings Management',
                    'subtitle' => 'Bills Management Platform',
                    'actionButtons' => $actionButtons
                ])



    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Financial Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            @can('view payments')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            KSh {{ number_format($totalRevenue, 2) }}
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            All time billing
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-green-100">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Outstanding Balance -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Outstanding Balance</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            KSh {{ number_format($outstandingBalance, 2) }}
                        </p>
                        <p class="text-xs text-red-600 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Requires attention
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-red-100">
                        <i class="fas fa-clock text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
            @endcan

            <!-- Total Bills -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Bills</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalBills }}</p>
                        <p class="text-xs text-blue-600 mt-1">
                            <i class="fas fa-file-invoice mr-1"></i>
                            All generated bills
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-blue-100">
                        <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Collection Rate -->
            @can('view payments')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Collection Rate</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">
                            {{ number_format($collectionRate, 1) }}%
                        </p>
                        <p class="text-xs text-purple-600 mt-1">
                            <i class="fas fa-chart-line mr-1"></i>
                            Payment efficiency
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-purple-100">
                        <i class="fas fa-percentage text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
            @endcan

        </div>

        <!-- Quick Stats & Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <!-- Status Quick Filters -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('bills.index') }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        All Bills
                        <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $totalBillsCount }}</span>
                    </a>
                    <a href="{{ route('bills.index', ['status' => 'unpaid']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'unpaid' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                        Unpaid
                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $unpaidBillsCount }}</span>
                    </a>
                    <a href="{{ route('bills.index', ['status' => 'paid']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'paid' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        Paid
                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $paidBillsCount }}</span>
                    </a>
                    <a href="{{ route('bills.index', ['status' => 'partial']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'partial' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                        Partial
                        <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $partialBillsCount }}</span>
                    </a>
                    <a href="{{ route('bills.index', ['status' => 'overdue']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'overdue' ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-700 hover:bg-orange-200' }}">
                        Overdue
                        <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $overdueBillsCount }}</span>
                    </a>
                </div>

                <!-- Search Box -->
                <div class="flex space-x-2">
                    <div class="relative">
                        <input type="text"
                            id="billSearch"
                            placeholder="Search (Bill No, Customer, Phone, Meter)..."
                            class="w-80 border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            autocomplete="off">

                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>

                    <button id="searchBtn"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Search
                    </button>

                    <button id="resetBtn"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition hidden">
                        Reset
                    </button>
                </div>

            </div>
        </div>

        <!-- Bills DataTable -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Bills Management</h2>
            </div>

            <!-- Bills Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th> -->
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="billsTableBody">
                        @forelse($bills as $bill)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-semibold text-blue-600">{{ $bill->bill_number }}</div>
                                <div class="text-xs text-gray-500">#{{ $bill->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $bill->customer->first_name }} {{ $bill->customer->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $bill->customer->customer_number }}</div>
                                        <div class="text-xs text-gray-400">{{ $bill->customer->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $bill->meter->meter_number ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $bill->meter->meterCategory->name ?? 'No Category' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($bill->billing_period_start && $bill->billing_period_end)
                                    {{ $bill->billing_period_start->format('M d') }} - {{ $bill->billing_period_end->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ number_format($bill->consumption, 2) }} m³</div>
                                <div class="text-xs text-gray-500">Consumption</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    KSh {{ number_format($bill->total_amount, 2) }}
                                </div>
                                @if($bill->payments->count() > 0)
                                    <div class="text-xs text-green-600">
                                        Paid: KSh {{ number_format($bill->payments->sum('amount'), 2) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'paid' => 'bg-green-100 text-green-800',
                                        'unpaid' => $bill->is_overdue ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800',
                                        'partial' => 'bg-blue-100 text-blue-800'
                                    ];
                                    $statusIcons = [
                                        'paid' => 'fa-check-circle',
                                        'unpaid' => $bill->is_overdue ? 'fa-exclamation-triangle' : 'fa-clock',
                                        'partial' => 'fa-hourglass-half'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bill->bill_status] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $statusIcons[$bill->bill_status] ?? 'fa-question-circle' }} mr-1"></i>
                                    {{ ucfirst($bill->bill_status) }}
                                    @if($bill->is_overdue && $bill->bill_status === 'unpaid')
                                        (Overdue)
                                    @endif
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('bills.show', $bill->id) }}"
                                    class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded transition duration-200"
                                    title="View Bill">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Simple Print Link -->
                                    <a href="{{ route('bills.print', $bill->id) }}"
                                        target="_blank"
                                        class="text-purple-600 hover:text-purple-900 px-2 py-1 rounded transition duration-200"
                                        title="Print Receipt">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <!-- <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this bill?')"
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded transition duration-200"
                                                title="Delete Bill">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> -->
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-file-invoice text-4xl mb-3"></i>
                                    <p class="text-lg font-medium text-gray-900">No bills found</p>
                                    <p class="text-gray-500">Start by recording a meter reading to generate bills.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($bills->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $bills->links() }}
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
                        $recentBills = $bills->take(3);
                    @endphp
                    @forelse($recentBills as $activity)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-invoice text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700">
                                <strong>Bill {{ $activity->bill_number }}</strong> generated for
                                {{ $activity->customer->first_name }} {{ $activity->customer->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                            <p class="text-xs text-blue-600 mt-1">
                                Amount: KSh {{ number_format($activity->total_amount, 2) }}
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
                    @can('view meters')
                    <a href="{{ route('admin.meters.index') }}"
                       class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Meter Management
                    </a>
                    @endcan
                    @can('view payments')
                    <a href="{{ route('payments.create') }}"
                       class="w-full bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Record Payment
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
       @include('components.modal-quick-bill')
</div>

<!-- Simple Search JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle print receipt button clicks
    document.querySelectorAll('.print-receipt-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const billId = this.getAttribute('data-bill-id');

            // Open a larger window for better visibility
            const printWindow = window.open(`/bills/${billId}/receipt/print`,
                'PrintReceipt',
                'width=500,height=700,scrollbars=yes,toolbar=no,location=no,menubar=no');

            // Focus the window
            if (printWindow) {
                printWindow.focus();
            }

            // Show a loading indicator
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;
            this.classList.add('opacity-50');

            // Reset button after 5 seconds
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
                this.classList.remove('opacity-50');
            }, 5000);
        });
    });

    // Add keyboard shortcut for search
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            document.getElementById('billSearch').focus();
        }

        // Alt+P for quick print (first bill's print button)
        if (e.altKey && e.key === 'p') {
            e.preventDefault();
            const firstPrintBtn = document.querySelector('.print-receipt-btn');
            if (firstPrintBtn) {
                firstPrintBtn.click();
            }
        }
    });
});

////////////////////////////////////////////////////////////////////////
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('billSearch');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');

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
            window.location.reload();
        });
    }

    async function performSearch(searchTerm) {
        try {
            const response = await fetch(`api/bills/search?search=${encodeURIComponent(searchTerm)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Search failed');

            const bills = await response.json();
            updateTableWithSearchResults(bills);
        } catch (error) {
            console.error(error);
        }
    }

    function updateTableWithSearchResults(bills) {
        const tbody = document.getElementById('billsTableBody');

        if (!tbody) return;  // extra safeguard

        if (!bills || bills.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-search text-4xl mb-3"></i>
                            <p class="text-lg font-medium text-gray-900">No bills found</p>
                            <p class="text-gray-500">No bills match your search criteria.</p>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = bills.map(bill => {
            const billingPeriod = bill.billing_period_start && bill.billing_period_end
                ? `${bill.billing_period_start_formatted} - ${bill.billing_period_end_formatted}`
                : '—';

            const paidAmount = bill.payments?.length > 0
                ? bill.payments.reduce((sum, p) => sum + p.amount, 0)
                : 0;

            let statusBadge = '';
            let statusColor = '';
            let statusIcon = '';

            switch (bill.bill_status) {
                case 'paid':
                    statusColor = 'bg-green-100 text-green-800';
                    statusIcon = 'fa-check-circle';
                    break;
                case 'partial':
                    statusColor = 'bg-blue-100 text-blue-800';
                    statusIcon = 'fa-hourglass-half';
                    break;
                case 'unpaid':
                    statusColor = bill.is_overdue ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
                    statusIcon = bill.is_overdue ? 'fa-exclamation-triangle' : 'fa-clock';
                    break;
                default:
                    statusColor = 'bg-gray-100 text-gray-800';
                    statusIcon = 'fa-question-circle';
            }

            const overdueSuffix = bill.is_overdue && bill.bill_status === 'unpaid'
                ? '(Overdue)'
                : '';

            return `
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-mono font-semibold text-blue-600">${bill.bill_number}</div>
                    <div class="text-xs text-gray-500">#${bill.id}</div>
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                ${bill.customer.first_name} ${bill.customer.last_name}
                            </div>
                            <div class="text-sm text-gray-500">${bill.customer.customer_number}</div>
                            <div class="text-xs text-gray-400">${bill.customer.phone}</div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${bill.meter?.meter_number ?? 'N/A'}</div>
                    <div class="text-xs text-gray-500">${bill.meter?.meter_category?.name ?? 'No Category'}</div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${billingPeriod}
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-medium">${Number(bill.consumption).toFixed(2)} m³</div>
                    <div class="text-xs text-gray-500">Consumption</div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">
                        KSh ${Number(bill.total_amount).toLocaleString()}
                    </div>
                    ${
                        bill.payments?.length > 0
                        ? `<div class="text-xs text-green-600">Paid: KSh ${paidAmount.toLocaleString()}</div>`
                        : ''
                    }
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColor}">
                        <i class="fas ${statusIcon} mr-1"></i>
                        ${bill.bill_status.charAt(0).toUpperCase() + bill.bill_status.slice(1)} ${overdueSuffix}
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">

                        <a href="/bills/${bill.id}"
                            class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded transition duration-200"
                            title="View Bill">
                            <i class="fas fa-eye"></i>
                        </a>


                        <a href="/bills/${bill.id}/print"
                            target="_blank"
                            class="text-purple-600 hover:text-purple-900 px-2 py-1 rounded transition duration-200"
                            title="Print Receipt">
                            <i class="fas fa-print"></i>
                        </a>



                    </div>
                </td>
            </tr>
            `;
        }).join('');


    }
});

</script>
@endsection
@endcan
