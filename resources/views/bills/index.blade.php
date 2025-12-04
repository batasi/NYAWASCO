@extends('layouts.app')

@section('title', 'Billing Management - NYAWASCO')

@section('content')
@can('view bills')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
        @php
                $actionButtons = [];

                if (auth()->user()->can('add bills')) {
                    $actionButtons[] = [
                        'text' => 'Generate Bill',
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
                <div class="relative">
                    <input type="text"
                           id="billSearch"
                           placeholder="Search bills..."
                           class="w-80 border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
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
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($bill->due_date)
                                    <span class="{{ $bill->is_overdue ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                        {{ $bill->formatted_due_date }}
                                        @if($bill->is_overdue)
                                            <i class="fas fa-exclamation-triangle ml-1"></i>
                                        @endif
                                    </span>
                                @else
                                    —
                                @endif
                            </td> -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                               <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
    <div class="flex justify-end space-x-2">
        <a href="{{ route('bills.show', $bill->id) }}"
           class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded transition duration-200"
           title="View Bill">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('bills.edit', $bill->id) }}"
           class="text-green-600 hover:text-green-900 px-2 py-1 rounded transition duration-200"
           title="Edit Bill">
            <i class="fas fa-edit"></i>
        </a>

        <!-- Receipt Dropdown Menu -->
        <div class="relative inline-block text-left">
            <button type="button"
                    class="text-purple-600 hover:text-purple-900 px-2 py-1 rounded transition duration-200 receipt-dropdown"
                    title="Generate Receipt"
                    onclick="toggleReceiptDropdown(this)">
                <i class="fas fa-receipt"></i>
                <i class="fas fa-caret-down ml-1 text-xs"></i>
            </button>

            <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200 receipt-dropdown-menu">
                <div class="py-1">
                    <a href="{{ route('bills.receipt', ['bill' => $bill->id, 'format' => 'print']) }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-900"
                       target="_blank">
                        <i class="fas fa-print mr-2"></i> Auto Print
                    </a>
                    <a href="{{ route('bills.receipt', ['bill' => $bill->id, 'format' => 'preview']) }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-900"
                       target="_blank">
                        <i class="fas fa-eye mr-2"></i> Preview First
                    </a>
                    <a href="{{ route('bills.receipt', ['bill' => $bill->id, 'format' => 'pdf']) }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-900">
                        <i class="fas fa-file-pdf mr-2"></i> Download PDF
                    </a>
                    <a href="{{ route('bills.receipt', ['bill' => $bill->id, 'format' => 'thermal']) }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-900">
                        <i class="fas fa-file-alt mr-2"></i> Raw Text
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('Are you sure you want to delete this bill?')"
                    class="text-red-600 hover:text-red-900 px-2 py-1 rounded transition duration-200"
                    title="Delete Bill">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>
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
                    <a href="{{ route('admin.customers.index') }}"
                       class="w-full bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-users mr-2"></i>
                        View All Customers
                    </a>
                    <a href="{{ route('admin.meters.index') }}"
                       class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Meter Management
                    </a>
                    <a href="{{ route('payments.create') }}"
                       class="w-full bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Record Payment
                    </a>
                </div>
            </div>
        </div>
    </div>
       @include('components.modal-quick-bill')
</div>

<!-- Simple Search JavaScript -->
<script>
    // Dropdown toggle function
function toggleReceiptDropdown(button) {
    const dropdown = button.nextElementSibling;
    dropdown.classList.toggle('hidden');

    // Close other dropdowns
    document.querySelectorAll('.receipt-dropdown-menu').forEach(menu => {
        if (menu !== dropdown) {
            menu.classList.add('hidden');
        }
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative.inline-block.text-left')) {
        document.querySelectorAll('.receipt-dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

// Close dropdown on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.receipt-dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('billSearch');
    let searchTimeout;

    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = e.target.value.trim();
            if (searchTerm.length > 2) {
                performSearch(searchTerm);
            } else if (searchTerm.length === 0) {
                // Reset to original view if search is cleared
                window.location.reload();
            }
        }, 500);
    });

    async function performSearch(searchTerm) {
        try {
            const response = await fetch(`/bills/search?search=${encodeURIComponent(searchTerm)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Search failed');

            const bills = await response.json();
            updateTableWithSearchResults(bills);

        } catch (error) {
            console.error('Search error:', error);
        }
    }

    function updateTableWithSearchResults(bills) {
        const tbody = document.getElementById('billsTableBody');

        if (bills.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-search text-4xl mb-3"></i>
                            <p class="text-lg font-medium text-gray-900">No bills found</p>
                            <p class="text-gray-500">No bills match your search criteria.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = bills.map(bill => `
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
                    <div class="text-sm font-medium text-gray-900">${bill.meter.meter_number}</div>
                    <div class="text-xs text-gray-500">${bill.meter.meter_category.name}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${bill.billing_period_start ? new Date(bill.billing_period_start).toLocaleDateString() : '—'}
                    to
                    ${bill.billing_period_end ? new Date(bill.billing_period_end).toLocaleDateString() : '—'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-medium">${parseFloat(bill.consumption).toFixed(2)} m³</div>
                    <div class="text-xs text-gray-500">Consumption</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">
                        KSh ${parseFloat(bill.total_amount).toFixed(2)}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getStatusColor(bill.bill_status, bill.due_date)}-100 text-${getStatusColor(bill.bill_status, bill.due_date)}-800">
                        <i class="fas ${getStatusIcon(bill.bill_status, bill.due_date)} mr-1"></i>
                        ${bill.bill_status.charAt(0).toUpperCase() + bill.bill_status.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${bill.due_date ? new Date(bill.due_date).toLocaleDateString() : '—'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <a href="/bills/${bill.id}"
                           class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded transition duration-200"
                           title="View Bill">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/bills/${bill.id}/edit"
                           class="text-green-600 hover:text-green-900 px-2 py-1 rounded transition duration-200"
                           title="Edit Bill">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function getStatusColor(status, dueDate) {
        const isOverdue = dueDate && new Date(dueDate) < new Date();
        if (status === 'paid') return 'green';
        if (status === 'unpaid' && isOverdue) return 'red';
        if (status === 'unpaid') return 'yellow';
        if (status === 'partial') return 'blue';
        return 'gray';
    }

    function getStatusIcon(status, dueDate) {
        const isOverdue = dueDate && new Date(dueDate) < new Date();
        if (status === 'paid') return 'fa-check-circle';
        if (status === 'unpaid' && isOverdue) return 'fa-exclamation-triangle';
        if (status === 'unpaid') return 'fa-clock';
        if (status === 'partial') return 'fa-hourglass-half';
        return 'fa-question-circle';
    }
});
</script>
@endsection
@endcan
