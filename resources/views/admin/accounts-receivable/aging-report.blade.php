@extends('layouts.app')

@section('title', 'Aging Report - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>
<div class="min-h-screen bg-gray-50">
    @php
    $actionButtons = [
        [
            'route' => 'admin.accounts-receivable.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'label' => 'Dashboard',
            'color' => 'bg-blue-600'
        ]
    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Aging Report',
        'subtitle' => 'Receivables Aging Analysis',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <form method="GET" action="{{ route('admin.accounts-receivable.aging-report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">As of Date</label>
                    <input type="date" name="as_of_date" value="{{ $date }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zone</label>
                    <select name="zone_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Zones</option>
                        @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                            {{ $zone->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meter Category</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-4 flex justify-end space-x-3">
                    <a href="{{ route('admin.accounts-receivable.aging-report') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Reset
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Generate Report
                    </button>
                    <button type="button" onclick="exportAgingReport('excel')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            @foreach($summary as $bucketName => $data)
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4 border border-white/20 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600 mb-1">{{ $bucketName }}</p>
                    <p class="text-xl font-bold text-gray-900 mb-1">
                        KSh {{ number_format($data['total_amount'], 2) }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ number_format($data['customer_count']) }} customers
                    </p>
                    <div class="mt-2 text-xs">
                        <span class="inline-block px-2 py-1 rounded-full {{ $data['percentage'] > 30 ? 'bg-red-100 text-red-800' : ($data['percentage'] > 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ number_format($data['percentage'], 1) }}%
                        </span>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">
                        {{ $data['range'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Aging Report Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Aging Report Details</h3>
                    <p class="text-sm text-gray-600">As of {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
                    <p class="text-sm text-gray-500">
                        Showing {{ $agingData->count() }} of {{ $agingData->total() }} customers
                        @if(request('zone_id'))
                            • Zone: {{ $zones->where('id', request('zone_id'))->first()->name ?? 'N/A' }}
                        @endif
                        @if(request('category_id'))
                            • Category: {{ $categories->where('id', request('category_id'))->first()->name ?? 'N/A' }}
                        @endif
                    </p>
                </div>


            </div>

            @if($agingData->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Due</th>
                            @foreach($agingBuckets as $bucket)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: {{ $bucket->color }}">
                                {{ $bucket->name }}
                            </th>
                            @endforeach
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        @foreach($agingData as $customer)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-4 py-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $customer->customer_number }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $customer->phone ?? 'N/A' }}
                                    </div>
                                    @if($customer->meters->count() > 0)
                                    <div class="text-xs text-gray-500 truncate">
                                        <i class="fas fa-tachometer-alt mr-1"></i>
                                        {{ $customer->meters->pluck('meter_number')->implode(', ') }}
                                    </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-lg font-semibold {{ $customer->total_due > 10000 ? 'text-red-600' : ($customer->total_due > 5000 ? 'text-yellow-600' : 'text-gray-900') }}">
                                    KSh {{ number_format($customer->total_due, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $customer->bill_count }} bills
                                </div>
                            </td>

                            @foreach($agingBuckets as $bucket)
                            <td class="px-4 py-4">
                                @php
                                    $bucketData = $customer->buckets->where('bucket_id', $bucket->id)->first();
                                    $bucketAmount = $bucketData['amount'] ?? 0;
                                    $bucketBillCount = $bucketData['bill_count'] ?? 0;
                                @endphp
                                @if($bucketAmount > 0)
                                <div class="text-sm font-medium text-gray-900" style="color: {{ $bucket->color }}">
                                    KSh {{ number_format($bucketAmount, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $bucketBillCount }} bills
                                </div>
                                @else
                                <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            @endforeach

                            <td class="px-4 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}"
                                       class="text-blue-600 hover:text-blue-800" title="View Customer">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button onclick="logActivityForCustomer({{ $customer->id }})"
                                       class="text-green-600 hover:text-green-800" title="Log Collection Activity">
                                        <i class="fas fa-phone-alt"></i>
                                    </button>

                                    <a href="{{ route('admin.accounts-receivable.write-offs.create', $customer->id) }}"
                                       class="text-red-600 hover:text-red-800" title="Write-off">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <!-- Footer Totals -->
                    <tfoot class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">Totals</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">
                                KSh {{ number_format($agingData->sum('total_due'), 2) }}
                            </th>
                            @foreach($agingBuckets as $bucket)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">
                                @php
                                    $total = 0;
                                    foreach($agingData as $customer) {
                                        $bucketData = $customer->buckets->where('bucket_id', $bucket->id)->first();
                                        $total += $bucketData['amount'] ?? 0;
                                    }
                                @endphp
                                KSh {{ number_format($total, 2) }}
                            </th>
                            @endforeach
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $agingData->links() }}
            </div>

            @else
            <div class="text-center py-12">
                <i class="fas fa-file-invoice text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No aging data found</h3>
                <p class="text-gray-500">
                    @if(request()->anyFilled(['zone_id', 'category_id']))
                        No customers match your filters.
                    @else
                        No customers have outstanding balances as of {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}.
                    @endif
                </p>
            </div>
            @endif
        </div>

        <!-- Summary Analysis -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Aging Distribution Chart -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aging Distribution</h3>
                <div class="h-64">
                    <canvas id="agingChart"></canvas>
                </div>
            </div>

            <!-- Top Debtors -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Debtors</h3>
                <div class="space-y-3">
                    @php
                        $topDebtors = $agingData->sortByDesc('total_due')->take(10);
                    @endphp
                    @foreach($topDebtors as $index => $customer)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg {{ $index < 3 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }} flex items-center justify-center mr-3">
                                <span class="text-sm font-bold">#{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 truncate max-w-[180px] group-hover:text-blue-600">
                                    {{ $customer->first_name }} {{ $customer->last_name }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $customer->customer_number }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold {{ $customer->total_due > 10000 ? 'text-red-600' : 'text-gray-900' }}">
                                KSh {{ number_format($customer->total_due, 2) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $customer->bill_count }} bills
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Collection Activity Modal -->
<div id="collectionActivityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Log Collection Activity</h3>
            <button onclick="closeActivityModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="collectionActivityForm" method="POST" action="{{ route('admin.accounts-receivable.collection-activities.store') }}">
            @csrf
            <div class="space-y-4">
                <input type="hidden" name="customer_id" id="modalCustomerId">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Type *</label>
                        <select name="activity_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="call">Phone Call</option>
                            <option value="visit">Site Visit</option>
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="letter">Letter</option>
                            <option value="promise_to_pay">Promise to Pay</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date *</label>
                        <input type="datetime-local" name="activity_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes *</label>
                    <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describe the activity..." required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Outcome</label>
                        <select name="outcome" id="outcomeSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Outcome</option>
                            <option value="contacted">Contacted</option>
                            <option value="promise_to_pay">Promise to Pay</option>
                            <option value="payment_made">Payment Made</option>
                            <option value="no_answer">No Answer</option>
                            <option value="disconnected">Disconnected</option>
                            <option value="dispute">Dispute</option>
                        </select>
                    </div>
                    <div id="promisedAmountContainer" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promised Amount</label>
                        <input type="number" step="0.01" name="promised_amount" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800" id="activityTips">
                        Log a collection activity for the selected customer
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeActivityModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Save Activity
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeAgingChart();
    setupEventListeners();
});

function initializeAgingChart() {
    const ctx = document.getElementById('agingChart').getContext('2d');

    // Get chart data from summary
    const labels = @json(array_keys($summary));
    const data = @json(array_column($summary, 'total_amount'));
    const colors = @json(array_column($summary, 'color'));

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'KSh ' + context.raw.toLocaleString('en-US', {minimumFractionDigits: 2});
                            return label;
                        }
                    }
                }
            }
        }
    });
}

function setupEventListeners() {
    // Outcome selection for modal
    document.getElementById('outcomeSelect')?.addEventListener('change', function() {
        const promisedContainer = document.getElementById('promisedAmountContainer');
        promisedContainer.classList.toggle('hidden', this.value !== 'promise_to_pay');
    });

    // Form submission
    document.getElementById('collectionActivityForm')?.addEventListener('submit', function(e) {
        const customerId = document.getElementById('modalCustomerId').value;
        if (!customerId) {
            e.preventDefault();
            showToast('Please select a customer first', 'error');
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

        return true;
    });
}

function printReport() {
    window.print();
}

function exportAgingReport(format = 'excel') {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/admin/accounts-receivable/aging-report/export?${params.toString()}&format=${format}`;
}

function logActivityForCustomer(customerId) {
    // Set customer ID in modal
    document.getElementById('modalCustomerId').value = customerId;

    // Show modal
    const modal = document.getElementById('collectionActivityModal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    // Update tips
    document.getElementById('activityTips').textContent =
        'Log a collection activity for customer ID: ' + customerId;
}

function closeActivityModal() {
    const modal = document.getElementById('collectionActivityModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('collectionActivityForm').reset();
    document.getElementById('promisedAmountContainer').classList.add('hidden');
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-50 animate-slide-in ${
        type === 'success' ? 'bg-green-600' :
        type === 'error' ? 'bg-red-600' :
        'bg-blue-600'
    }`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('animate-slide-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('collectionActivityModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeActivityModal();
    }
});

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slide-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }

    .animate-slide-out {
        animation: slide-out 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }

    .bg-white\/80 {
        background: white !important;
    }

    .rounded-2xl {
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
