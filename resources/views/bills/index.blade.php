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

/* Mobile responsiveness */
@media (max-width: 768px) {
    .text-2xl {
        font-size: 1.25rem !important;
        line-height: 1.75rem !important;
    }

    .text-xl {
        font-size: 1.125rem !important;
        line-height: 1.75rem !important;
    }

    .text-lg {
        font-size: 1rem !important;
        line-height: 1.5rem !important;
    }

    .text-sm {
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
    }

    .text-xs {
        font-size: 0.75rem !important;
        line-height: 1rem !important;
    }

    .px-6 {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    .py-4 {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }

    .py-3 {
        padding-top: 0.375rem !important;
        padding-bottom: 0.375rem !important;
    }

    .py-6 {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }

    .px-4 {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }

    .px-2 {
        padding-left: 0.25rem !important;
        padding-right: 0.25rem !important;
    }

    .space-x-2 > * + * {
        margin-left: 0.25rem !important;
    }

    .space-x-3 > * + * {
        margin-left: 0.375rem !important;
    }

    .gap-6 {
        gap: 0.75rem !important;
    }

    .gap-2 {
        gap: 0.25rem !important;
    }

    .mb-8 {
        margin-bottom: 1rem !important;
    }

    .mb-4 {
        margin-bottom: 0.5rem !important;
    }

    .mb-3 {
        margin-bottom: 0.375rem !important;
    }

    .mt-1 {
        margin-top: 0.125rem !important;
    }

    .w-8 {
        width: 1.5rem !important;
    }

    .h-8 {
        height: 1.5rem !important;
    }

    .w-40 {
        width: 8rem !important;
    }

    .text-4xl {
        font-size: 2rem !important;
        line-height: 2.5rem !important;
    }
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

<div class="w-full px-2.5 py-8">

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
                            {{ $unpaidBillsCount + $partialBillsCount }} unpaid bills
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
                    @php
                        $buildUrl = function($params = []) {
                            $current = request()->all();
                            $merged = array_merge($current, $params);
                            return route('bills.index', $merged);
                        };
                    @endphp
                    <a href="{{ $buildUrl(['status' => null]) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        All Bills
                        <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $totalBillsCount }}</span>
                    </a>
                    <a href="{{ $buildUrl(['status' => 'unpaid']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'unpaid' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                        Unpaid
                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $unpaidBillsCount }}</span>
                    </a>
                    <a href="{{ $buildUrl(['status' => 'paid']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'paid' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        Paid
                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $paidBillsCount }}</span>
                    </a>
                    <a href="{{ $buildUrl(['status' => 'partial']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'partial' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                        Partial
                        <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $partialBillsCount }}</span>
                    </a>
                    <a href="{{ $buildUrl(['status' => 'overdue']) }}"
                    class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == 'overdue' ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-700 hover:bg-orange-200' }}">
                        Overdue
                        <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs ml-1">{{ $overdueBillsCount }}</span>
                    </a>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Zone Filter -->
                    <div class="relative">
                        <select id="zoneFilter"
                            class="border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 appearance-none bg-white">
                            <option value="all" {{ !request('zone') ? 'selected' : '' }}>All Zones</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ request('zone') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="relative">
                        <select name="date_filter" id="date_filter"
                                class="border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 appearance-none bg-white">
                            <option value="all" {{ request('date_filter') == 'all' ? 'selected' : '' }}>All Time</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('date_filter') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('date_filter') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="flex space-x-2">
                        <div class="relative">
                            <input type="text"
                                id="billSearch"
                                placeholder="Search (Bill No)..."
                                class="w-40 border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                autocomplete="off">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
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

            <!-- Custom Date Range (hidden by default) -->
            <div id="custom_date_range" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4"
                 style="{{ request('date_filter') == 'custom' ? '' : 'display: none;' }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ request('start_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date"
                           name="end_date"
                           id="end_date"
                           value="{{ request('end_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>
            </div>

            <!-- Selected Filters Display -->
            @if(request('zone') || request('date_filter') || request('status'))
            <div class="mt-4 flex items-center text-sm text-gray-600 flex-wrap gap-2">
                <span class="mr-2">Active Filters:</span>

                @if(request('zone') && $selectedZone)
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Zone: {{ $selectedZone->name }}
                    <a href="{{ route('bills.index', array_merge(request()->except('zone'), ['status' => request('status'), 'date_filter' => request('date_filter'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])) }}"
                    class="ml-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif

                @if(request('status'))
                @php
                    $statusColors = [
                        'unpaid' => 'red',
                        'paid' => 'green',
                        'partial' => 'yellow',
                        'overdue' => 'orange'
                    ];
                    $currentStatusColor = $statusColors[request('status')] ?? 'gray';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-{{ $currentStatusColor }}-100 text-{{ $currentStatusColor }}-800">
                    <i class="fas fa-filter mr-1"></i>
                    Status: {{ ucfirst(request('status')) }}
                    <a href="{{ route('bills.index', array_merge(request()->except('status'), ['zone' => request('zone'), 'date_filter' => request('date_filter'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])) }}"
                    class="ml-2 text-{{ $currentStatusColor }}-600 hover:text-{{ $currentStatusColor }}-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif

                @if(request('date_filter') && request('date_filter') != 'all')
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Date: {{ ucfirst(str_replace('_', ' ', request('date_filter'))) }}
                    @if(request('date_filter') == 'custom' && request('start_date') && request('end_date'))
                        ({{ \Carbon\Carbon::parse(request('start_date'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }})
                    @endif
                    <a href="{{ route('bills.index', array_merge(request()->except(['date_filter', 'start_date', 'end_date']), ['zone' => request('zone'), 'status' => request('status')])) }}"
                    class="ml-2 text-purple-600 hover:text-purple-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
            </div>
            @endif
        </div>

        <!-- Bills DataTable -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            <!-- Table Stats --> <br>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 text-sm text-gray-600">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Bills Management</h2>
                </div>
                <div class="flex items-center space-x-4 mt-2 sm:mt-0">
                    <!-- Download Button -->
                    @php
                        $exportParams = [
                            'report_type' => 'revenue',
                            'format' => 'excel',
                            'detail_level' => 'full'
                        ];

                        // Add zone filter if active
                        if(request('zone') && request('zone') != 'all') {
                            $exportParams['zone'] = request('zone');
                        }

                        // Add status filter if active
                        if(request('status')) {
                            $exportParams['status'] = request('status');
                        }

                        // Add date filter if active
                        if(request('date_filter') && request('date_filter') != 'all') {
                            $exportParams['date_filter'] = request('date_filter');
                            if(request('date_filter') == 'custom' && request('start_date') && request('end_date')) {
                                $exportParams['start_date'] = request('start_date');
                                $exportParams['end_date'] = request('end_date');
                            }
                        }

                        // Add search term if active
                        if(request('search')) {
                            $exportParams['search'] = request('search');
                        }
                    @endphp

                    <a href="{{ route('reports.generate', $exportParams) }}"
                        class="download-excel-btn flex items-center space-x-2 bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded text-sm transition duration-200"
                        onclick="showBillsDownloadSpinner()">
                        <i id="billsDownloadIcon" class="fas fa-file-excel"></i>
                        <span id="billsDownloadText">Export Excel</span>
                        <i id="billsDownloadSpinner" class="fas fa-spinner fa-spin hidden"></i>
                    </a>
                </div>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zone</th>
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
                                    $currentBalance = $bill->meter->current_balance ?? 0;
                                @endphp

                                @if($currentBalance > 0)
                                    <div class="text-sm font-semibold text-red-600">
                                        KSh {{ number_format($currentBalance, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-exclamation-circle text-red-500 mr-1"></i> Outstanding
                                    </div>
                                @else

                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-check-circle text-green-500 mr-1"></i> No balance
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($bill->meter->zone)
                                    <div class="text-sm font-medium text-gray-900">{{ $bill->meter->zone->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $bill->meter->zone->code ?? 'N/A' }}</div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
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
    // Add this to your existing JavaScript in bills.blade.php

// Bills download spinner function
function showBillsDownloadSpinner() {
    const downloadIcon = document.getElementById('billsDownloadIcon');
    const downloadText = document.getElementById('billsDownloadText');
    const downloadSpinner = document.getElementById('billsDownloadSpinner');

    if (downloadIcon && downloadText && downloadSpinner) {
        downloadIcon.classList.add('hidden');
        downloadText.textContent = 'Exporting...';
        downloadSpinner.classList.remove('hidden');

        // Reset after 5 seconds (in case something goes wrong)
        setTimeout(() => {
            downloadIcon.classList.remove('hidden');
            downloadText.textContent = 'Export Excel';
            downloadSpinner.classList.add('hidden');
        }, 5000);
    }
}

// Handle the download button click for bills
document.querySelectorAll('.download-excel-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        // Check if this is the bills export button
        if (this.querySelector('#billsDownloadIcon')) {
            showBillsDownloadSpinner();
        }
    });
});

// Handle print receipt button clicks
document.addEventListener('DOMContentLoaded', function() {
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
    const dateFilter = document.getElementById('date_filter');
    const customDateRange = document.getElementById('custom_date_range');
    const zoneFilter = document.getElementById('zoneFilter');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    // Show/hide custom date range
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.style.display = 'grid';
            } else {
                customDateRange.style.display = 'none';
            }
        });

        // Initialize on page load
        if (dateFilter.value === 'custom') {
            customDateRange.style.display = 'grid';
        }
    }

    // Handle zone filter change
    if (zoneFilter) {
        zoneFilter.addEventListener('change', applyFilters);
    }

    // Handle date filter change
    if (dateFilter) {
        dateFilter.addEventListener('change', applyFilters);
    }

    // Handle custom date inputs change
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            if (dateFilter.value === 'custom') {
                applyFilters();
            }
        });
    }

    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            if (dateFilter.value === 'custom') {
                applyFilters();
            }
        });
    }

    function applyFilters() {
        const zoneId = zoneFilter?.value || 'all';
        const dateFilterValue = dateFilter?.value || 'all';
        const startDate = startDateInput?.value;
        const endDate = endDateInput?.value;

        let url = '{{ route("bills.index") }}?';

        const params = [];

        // Get current status
        const currentStatus = new URLSearchParams(window.location.search).get('status');
        if (currentStatus) {
            params.push(`status=${currentStatus}`);
        }

        if (zoneId && zoneId !== 'all') {
            params.push(`zone=${zoneId}`);
        }

        if (dateFilterValue && dateFilterValue !== 'all') {
            params.push(`date_filter=${dateFilterValue}`);
        }

        if (dateFilterValue === 'custom' && startDate && endDate) {
            params.push(`start_date=${startDate}`);
            params.push(`end_date=${endDate}`);
        }

        window.location.href = url + params.join('&');
    }

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
            const zoneId = zoneFilter?.value || 'all';
            const dateFilterValue = dateFilter?.value || 'all';
            const startDate = startDateInput?.value;
            const endDate = endDateInput?.value;

            let url = `/api/bills/search?search=${encodeURIComponent(searchTerm)}&zone=${zoneId}&date_filter=${dateFilterValue}`;

            if (dateFilterValue === 'custom' && startDate && endDate) {
                url += `&start_date=${startDate}&end_date=${endDate}`;
            }

            const response = await fetch(url, {
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
            showNotification('Search failed. Please try again.', 'error');
        }
    }

    // Update the status filter links to preserve all filters
    document.querySelectorAll('a[href*="status="]').forEach(link => {
        const href = link.getAttribute('href');
        const zoneId = zoneFilter?.value;
        const dateFilterValue = dateFilter?.value;
        const startDate = startDateInput?.value;
        const endDate = endDateInput?.value;

        if (zoneId || dateFilterValue || startDate || endDate) {
            const url = new URL(href, window.location.origin);

            if (zoneId && zoneId !== 'all') {
                url.searchParams.set('zone', zoneId);
            }

            if (dateFilterValue && dateFilterValue !== 'all') {
                url.searchParams.set('date_filter', dateFilterValue);
            }

            if (dateFilterValue === 'custom' && startDate && endDate) {
                url.searchParams.set('start_date', startDate);
                url.searchParams.set('end_date', endDate);
            }

            link.setAttribute('href', url.toString());
        }
    });

    function updateTableWithSearchResults(bills) {
        const tbody = document.getElementById('billsTableBody');

        if (!tbody) return;

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
                    ${(() => {
                        const balance = bill.meter?.current_balance ?? 0;

                        if (balance > 0) {
                            return `
                                <div class="text-sm font-semibold text-red-600">
                                    KSh ${Number(balance).toLocaleString()}
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-exclamation-circle text-red-500 mr-1"></i> Outstanding
                                </div>
                            `;
                        } else {
                            return `
                                <div class="text-xs text-green-500">
                                    <i class="fas fa-check-circle text-gray-500 mr-1"></i> No Balance
                                </div>
                            `;
                        }
                    })()}
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColor}">
                        <i class="fas ${statusIcon} mr-1"></i>
                        ${bill.bill_status.charAt(0).toUpperCase() + bill.bill_status.slice(1)} ${overdueSuffix}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${bill.meter?.zone
                        ? `<div class="text-sm font-medium text-gray-900">${bill.meter.zone.name}</div>
                        <div class="text-xs text-gray-500">${bill.meter.zone.code || 'N/A'}</div>`
                        : '<span class="text-sm text-gray-400">—</span>'
                    }
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

// Helper function to show notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'error' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-blue-100 text-blue-800 border border-blue-300'}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection
@endcan
