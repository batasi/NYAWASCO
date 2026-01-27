@extends('layouts.app')

@section('title', 'Accounts Receivable Dashboard - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>
<div class="min-h-screen bg-gray-50">
    @php
    $actionButtons = [
        [
            'route' => 'admin.accounts-receivable.aging-report',
            'icon' => 'fas fa-file-invoice',
            'label' => 'Aging Report',
            'color' => 'bg-blue-600'
        ],
        [
            'route' => 'admin.accounts-receivable.collections-tracking',
            'icon' => 'fas fa-phone-alt',
            'label' => 'Collections',
            'color' => 'bg-green-600'
        ],
        [
            'route' => 'admin.accounts-receivable.write-offs.index',
            'icon' => 'fas fa-trash-alt',
            'label' => 'Write-offs',
            'color' => 'bg-red-600'
        ],
        [
            'route' => 'admin.accounts-receivable.customer-balances',
            'icon' => 'fas fa-users',
            'label' => 'Customer Balances',
            'color' => 'bg-purple-600'
        ]
    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Accounts Receivable',
        'subtitle' => 'Track money owned to company by customers',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Receivables -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Receivables</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1" id="totalReceivables">
                            KSh {{ number_format($summary['total_receivables'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500 flex items-center justify-between">
                    <span>{{ number_format($summary['active_customers']) }} active customers</span>
                    <button onclick="refreshSummary()" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-sync-alt text-xs"></i>
                    </button>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    Last updated: <span id="lastUpdated">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>

            <!-- Collection Rate -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Collection Rate</p>
                        <p class="text-2xl font-bold {{ $summary['collection_rate'] >= 80 ? 'text-green-600' : ($summary['collection_rate'] >= 60 ? 'text-yellow-600' : 'text-red-600') }} mt-1">
                            {{ number_format($summary['collection_rate'], 1) }}%
                        </p>
                    </div>
                    <div class="p-3 {{ $summary['collection_rate'] >= 80 ? 'bg-green-100' : ($summary['collection_rate'] >= 60 ? 'bg-yellow-100' : 'bg-red-100') }} rounded-lg">
                        <i class="fas fa-chart-line {{ $summary['collection_rate'] >= 80 ? 'text-green-600' : ($summary['collection_rate'] >= 60 ? 'text-yellow-600' : 'text-red-600') }} text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    This month's collection efficiency
                </div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $summary['collection_rate'] >= 80 ? 'bg-green-500' : ($summary['collection_rate'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                         style="width: {{ min($summary['collection_rate'], 100) }}%"></div>
                </div>
            </div>

            <!-- Overdue Amount -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Overdue Amount</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            KSh {{ number_format($summary['overdue_amount'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    {{ number_format($summary['overdue_customers']) }} customers
                </div>
                <div class="mt-2 text-xs {{ $summary['overdue_percentage'] > 30 ? 'text-red-600' : ($summary['overdue_percentage'] > 15 ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ number_format($summary['overdue_percentage'], 1) }}% of total receivables
                </div>
            </div>

            <!-- Write-offs This Month -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Write-offs (Monthly)</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            KSh {{ number_format($writeOffSummary->sum('total_amount'), 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="fas fa-trash-alt text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    {{ $writeOffSummary->sum('count') }} write-offs processed
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    Bad debt ratio: {{ number_format($summary['bad_debt_ratio'], 2) }}%
                </div>
            </div>
        </div>
        <!-- Quick Actions -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Buttons</h3>

            <div class="flex flex-wrap gap-3">
                <!-- Log Call -->
                <button
                    onclick="showCollectionActivityModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                        bg-blue-600 text-white hover:bg-blue-200
                        transition font-medium shadow-sm"
                >
                    <i class="fas fa-phone-alt"></i>
                    Log Collection Activity
                </button>

                <!-- Export Aging -->
                <a
                    href="{{ route('admin.accounts-receivable.aging-report', ['as_of_date' => now()->toDateString()]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                        bg-purple-600 text-white hover:bg-purple-200
                        transition font-medium shadow-sm"
                >
                    <i class="fas fa-file-pdf"></i>
                    Export Aging
                </a>

                <!-- Process Write-off -->
                <a
                    href="{{ route('admin.accounts-receivable.write-offs.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                        bg-red-600 text-white hover:bg-red-200
                        transition font-medium shadow-sm"
                >
                    <i class="fas fa-file-invoice-dollar"></i>
                    Process Write-off
                </a>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="grid mt-2 grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Aging Analysis Chart -->
            <div class="lg:col-span-2 bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Aging Analysis</h3>
                        <p class="text-sm text-gray-600">Distribution of overdue amounts by age</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <select id="agingFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Customers</option>
                            <option value="commercial">Commercial Only</option>
                            <option value="domestic">Domestic Only</option>
                        </select>
                        <a href="{{ route('admin.accounts-receivable.aging-report') }}"
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View Full Report →
                        </a>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="h-64 mb-4">
                    <canvas id="agingChart"></canvas>
                </div>

                <!-- Aging Buckets Details -->
                <div class="space-y-3">
                    @foreach($agingReport as $item)
                    <div class="flex items-center justify-between hover:bg-gray-50 p-2 rounded-lg transition">
                        <div class="flex items-center flex-1">
                            <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $item['bucket']->color }}"></div>
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-900">{{ $item['bucket']->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $item['bucket']->range }}</span>
                                </div>
                                <div class="text-xs text-gray-500 flex justify-between">
                                    <span>{{ number_format($item['customer_count']) }} customers</span>
                                    <span>{{ number_format($item['bill_count']) }} bills</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <p class="font-semibold text-gray-900">KSh {{ number_format($item['total_amount'], 2) }}</p>
                            <p class="text-xs text-gray-500">
                                {{ number_format($item['percentage'], 1) }}%
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Delinquent Customers & Recent Activities -->
            <div class="space-y-8">
                <!-- Top Delinquent Customers -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Top Delinquent Customers</h3>
                            <p class="text-sm text-gray-600">Highest overdue balances</p>
                        </div>
                        <a href="{{ route('admin.accounts-receivable.customer-balances') }}"
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View All →
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach($delinquentCustomers as $index => $customer)
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
                                <p class="font-semibold text-red-600">
                                    KSh {{ number_format($customer->total_overdue, 2) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $customer->overdue_bill_count }} overdue bills
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Collection Activities -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                            <p class="text-sm text-gray-600">Latest collection efforts</p>
                        </div>
                        <a href="{{ route('admin.accounts-receivable.collections-tracking') }}"
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View All →
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach($recentActivities as $activity)
                        <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="p-2 rounded-lg {{ $activity->outcome == 'promise_to_pay' ? 'bg-green-100' : ($activity->outcome == 'contacted' ? 'bg-blue-100' : 'bg-gray-100') }} mr-3">
                                <i class="fas fa-{{ $activity->activity_icon }} {{ $activity->outcome == 'promise_to_pay' ? 'text-green-600' : ($activity->outcome == 'contacted' ? 'text-blue-600' : 'text-gray-600') }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $activity->customer->first_name }} {{ $activity->customer->last_name }}
                                    </p>
                                    <span class="text-xs text-gray-500">
                                        {{ $activity->activity_date->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                                    {{ $activity->notes }}
                                </p>
                                @if($activity->outcome)
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium
                                    {{ $activity->outcome == 'promise_to_pay' ? 'bg-green-100 text-green-800' :
                                       ($activity->outcome == 'contacted' ? 'bg-blue-100 text-blue-800' :
                                       'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $activity->outcome)) }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Write-off Summary -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Write-off Summary (This Month)</h3>
                        <p class="text-sm text-gray-600">Breakdown by type</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="exportWriteOffSummary()" class="text-sm text-gray-600 hover:text-gray-800">
                            <i class="fas fa-download"></i>
                        </button>
                        <a href="{{ route('admin.accounts-receivable.write-offs.index') }}"
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View All →
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($writeOffSummary as $item)
                    <div class="bg-gray-50/50 rounded-xl p-4 hover:bg-gray-100 transition cursor-pointer" onclick="filterByWriteOffType('{{ $item->type }}')">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $item->type) }}</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800">
                                {{ $item->count }}
                            </span>
                        </div>
                        <p class="text-xl font-bold text-gray-900">KSh {{ number_format($item->total_amount, 2) }}</p>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            @php
                                $totalWriteOffs = $writeOffSummary->sum('total_amount');
                                $percentage = $totalWriteOffs > 0 ? ($item->total_amount / $totalWriteOffs) * 100 : 0;
                            @endphp
                            <div class="h-2 rounded-full bg-red-500" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 flex justify-between">
                            <span>Avg: KSh {{ number_format($item->total_amount / max(1, $item->count), 2) }}</span>
                            <span>{{ number_format($percentage, 1) }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Write-off Trend -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Monthly Write-off Trend</h4>
                    <div class="h-32">
                        <canvas id="writeOffTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Collection Performance -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Collection Performance</h3>
                        <p class="text-sm text-gray-600">This month's collection metrics</p>
                    </div>
                    <a href="{{ route('admin.accounts-receivable.collection-performance') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View Details →
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-blue-700">Activities Today</span>
                            <i class="fas fa-phone-alt text-blue-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-blue-800">{{ $collectionStats['today_activities'] }}</p>
                        <div class="text-xs text-blue-600 mt-1">
                            {{ $collectionStats['today_promises'] }} promises made
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-green-700">Month Activities</span>
                            <i class="fas fa-chart-bar text-green-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-green-800">{{ $collectionStats['month_activities'] }}</p>
                        <div class="text-xs text-green-600 mt-1">
                            {{ $collectionStats['month_promises'] }} promises total
                        </div>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-purple-700">Promise Fulfillment</span>
                            <i class="fas fa-handshake text-purple-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-purple-800">{{ number_format($collectionStats['promise_fulfillment_rate'], 1) }}%</p>
                        <div class="text-xs text-purple-600 mt-1">
                            {{ $collectionStats['promises_kept'] }} of {{ $collectionStats['total_promises'] }} kept
                        </div>
                    </div>

                    <div class="bg-yellow-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-yellow-700">Avg Promise Amount</span>
                            <i class="fas fa-money-bill-wave text-yellow-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-yellow-800">KSh {{ number_format($collectionStats['avg_promise_amount'], 2) }}</p>
                        <div class="text-xs text-yellow-600 mt-1">
                            Total: KSh {{ number_format($collectionStats['total_promise_amount'], 2) }}
                        </div>
                    </div>
                </div>

                <!-- Performance Chart -->
                <div class="h-40">
                    <canvas id="collectionPerformanceChart"></canvas>
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

        <form id="collectionActivityForm" method="POST" action="{{ route('admin.accounts-receivable.collection-activities.store') }}">   @csrf
            <div class="space-y-4">
                <!-- Replace the existing select with this -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Customer *</label>
                    <div class="relative">
                        <input type="text"
                            id="customerSearch"
                            name="customer_search"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Search by Meter No, Phone, ID, or Name..."
                            autocomplete="off">
                        <div class="absolute left-3 top-3.5 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="absolute right-3 top-3.5">
                            <span id="searchSpinner" class="hidden">
                                <i class="fas fa-spinner fa-spin text-blue-600"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Search Results Container -->
                    <div id="searchResults"
                        class="hidden mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>

                    <!-- Hidden field for selected customer -->
                    <input type="hidden" name="customer_id" id="selectedCustomerId">
                </div>

                <!-- Selected Customer Info Display -->
                <div id="selectedCustomerInfo" class="hidden mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-blue-900" id="selectedCustomerName"></h4>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 mt-2 text-sm">
                                <div class="flex items-center">
                                    <span class="text-gray-600 w-24">Account No:</span>
                                    <span class="font-medium" id="selectedCustomerNumber"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-600 w-24">Phone:</span>
                                    <span class="font-medium" id="selectedCustomerPhone"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-600 w-24">ID No:</span>
                                    <span class="font-medium" id="selectedCustomerIdNumber"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-600 w-24">Meters:</span>
                                    <span class="font-medium" id="selectedCustomerMeters"></span>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <span class="text-gray-600 w-24">Balance Due:</span>
                                    <span class="font-medium text-red-600" id="selectedCustomerBalance"></span>
                                </div>
                            </div>
                        </div>
                        <button type="button"
                                onclick="clearCustomerSelection()"
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Activity Type *</label>
                    <select name="activity_type" id="activityType" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="call"> Phone Call</option>
                        <option value="visit"> Site Visit</option>
                        <option value="email"> Email</option>
                        <option value="sms"> SMS</option>
                        <option value="letter"> Letter</option>
                        <option value="promise_to_pay"> Promise to Pay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes *</label>
                    <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describe the activity..." required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date *</label>
                        <input type="datetime-local" name="activity_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                        <input type="datetime-local" name="follow_up_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
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
                    <p class="text-sm text-blue-800 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="activityTips">Select a customer and activity type to see tips</span>
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
// Chart.js Initialization
let agingChart, writeOffTrendChart, performanceChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupEventListeners();
    startAutoRefresh();
});

function initializeCharts() {
    // Aging Analysis Chart
    const agingCtx = document.getElementById('agingChart').getContext('2d');
    agingChart = new Chart(agingCtx, {
        type: 'bar',
        data: {
            labels: @json($agingReport->pluck('bucket.name')),
            datasets: [{
                label: 'Amount (KSh)',
                data: @json($agingReport->pluck('total_amount')),
                backgroundColor: @json($agingReport->pluck('bucket.color')),
                borderColor: @json($agingReport->pluck('bucket.color')),
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `KSh ${context.raw.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KSh ' + (value / 1000).toFixed(0) + 'K';
                        }
                    }
                }
            }
        }
    });

    // Write-off Trend Chart
    const writeOffCtx = document.getElementById('writeOffTrendChart').getContext('2d');
    writeOffTrendChart = new Chart(writeOffCtx, {
        type: 'line',
        data: {
            labels: @json($writeOffTrend->pluck('month')),
            datasets: [{
                label: 'Write-off Amount',
                data: @json($writeOffTrend->pluck('amount')),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KSh ' + (value / 1000).toFixed(0) + 'K';
                        }
                    }
                }
            }
        }
    });

    // Collection Performance Chart
    const performanceCtx = document.getElementById('collectionPerformanceChart').getContext('2d');
    performanceChart = new Chart(performanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Successful', 'Partial', 'Failed'],
            datasets: [{
                data: [
                    {{ $collectionStats['successful_activities'] }},
                    {{ $collectionStats['partial_activities'] }},
                    {{ $collectionStats['failed_activities'] }}
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

function setupEventListeners() {
    // Aging filter
    document.getElementById('agingFilter').addEventListener('change', function() {
        updateAgingChart(this.value);
    });

    // Activity type tips
    document.getElementById('activityType').addEventListener('change', function() {
        updateActivityTips(this.value);
    });

    // Outcome selection
    document.getElementById('outcomeSelect').addEventListener('change', function() {
        const promisedContainer = document.getElementById('promisedAmountContainer');
        promisedContainer.classList.toggle('hidden', this.value !== 'promise_to_pay');
    });

    // Customer selection
    document.querySelector('select[name="customer_id"]').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        updateCustomerTips(selectedOption);
    });
}

function updateAgingChart(filter) {
    // In a real implementation, this would fetch filtered data from the server
    // For now, we'll just show a loading state
    agingChart.data.datasets[0].data = agingChart.data.datasets[0].data.map(() => 0);
    agingChart.update();

    // Simulate API call
    setTimeout(() => {
        // Reset to original data (in reality, this would be new data from server)
        agingChart.data.datasets[0].data = @json($agingReport->pluck('total_amount'));
        agingChart.update();
    }, 500);
}

function updateActivityTips(activityType) {
    const tips = {
        'call': 'Tips: Have the customer\'s account information ready. Be polite but firm. Document all promises made.',
        'visit': 'Tips: Schedule visits during business hours. Bring necessary documentation. Confirm customer identity.',
        'email': 'Tips: Use a professional subject line. Include account details. Request read receipt.',
        'sms': 'Tips: Keep messages brief. Include reference number. Avoid sensitive information.',
        'letter': 'Tips: Use official letterhead. Include all relevant details. Send via registered mail.',
        'promise_to_pay': 'Tips: Document exact amount and date. Follow up before due date. Update account notes.'
    };

    document.getElementById('activityTips').textContent = tips[activityType] || 'Select an activity type for tips';
}

function updateCustomerTips(option) {
    if (!option.value) return;

    const balance = option.dataset.balance || 0;
    const tips = document.getElementById('activityTips');
    tips.textContent = `Customer has KSh ${parseFloat(balance).toLocaleString('en-US', {minimumFractionDigits: 2})} overdue. Consider discussing payment plan options.`;
}

function showCollectionActivityModal(type = 'call') {
    const modal = document.getElementById('collectionActivityModal');
    if (type) {
        document.getElementById('activityType').value = type;
        updateActivityTips(type);
    }
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeActivityModal() {
    const modal = document.getElementById('collectionActivityModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('collectionActivityForm').reset();
    document.getElementById('promisedAmountContainer').classList.add('hidden');
}

function filterByWriteOffType(type) {
    window.location.href = `{{ route('admin.accounts-receivable.write-offs.index') }}?type=${type}`;
}

function exportWriteOffSummary() {
    // Implement export functionality
    alert('Export feature will be implemented');
}

function refreshSummary() {
    const lastUpdated = document.getElementById('lastUpdated');
    lastUpdated.textContent = 'Refreshing...';

    // In a real implementation, this would fetch updated data
    setTimeout(() => {
        lastUpdated.textContent = new Date().toLocaleTimeString();
        alert('Dashboard data refreshed!');
    }, 1000);
}

function startAutoRefresh() {
    // Auto-refresh every 5 minutes
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            refreshSummary();
        }
    }, 300000); // 5 minutes
}

// Close modal when clicking outside
document.getElementById('collectionActivityModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeActivityModal();
    }
});
// form submission to handle both AJAX and regular form submission
document.getElementById('collectionActivityForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

    try {
        const formData = new FormData(form);

        // Convert form data to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Send AJAX request
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.success) {
            // Success
            showToast('Activity logged successfully!', 'success');
            closeActivityModal();

            // Refresh dashboard data
            refreshDashboard();
        } else {
            // Error
            showToast(result.message || 'Failed to log activity', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error. Please try again.', 'error');
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
// Add these functions to your existing JavaScript
let searchTimeout;

document.getElementById('customerSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const searchTerm = e.target.value.trim();

    if (searchTerm.length < 2) {
        document.getElementById('searchResults').classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(() => {
        performCustomerSearch(searchTerm);
    }, 300);
});

async function performCustomerSearch(searchTerm) {
    const spinner = document.getElementById('searchSpinner');
    const resultsContainer = document.getElementById('searchResults');

    spinner.classList.remove('hidden');

    try {
        const response = await fetch('{{ route("admin.accounts-receivable.search-customer") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ search: searchTerm })
        });

        const data = await response.json();

        if (data.success && data.customers.length > 0) {
            displaySearchResults(data.customers);
        } else {
            resultsContainer.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-search fa-lg mb-2"></i>
                    <p>No customers found</p>
                </div>
            `;
            resultsContainer.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Search error:', error);
        resultsContainer.innerHTML = `
            <div class="p-4 text-center text-red-500">
                <i class="fas fa-exclamation-triangle fa-lg mb-2"></i>
                <p>Search failed. Please try again.</p>
            </div>
        `;
        resultsContainer.classList.remove('hidden');
    } finally {
        spinner.classList.add('hidden');
    }
}

function displaySearchResults(customers) {
    const resultsContainer = document.getElementById('searchResults');
    let html = '';

    customers.forEach(customer => {
        const balance = parseFloat(customer.balance);
        const balanceClass = balance > 10000 ? 'bg-red-100 text-red-800' :
                            balance > 5000 ? 'bg-orange-100 text-orange-800' :
                            'bg-yellow-100 text-yellow-800';

        html += `
            <div class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                 onclick="selectCustomer(${JSON.stringify(customer).replace(/"/g, '&quot;')})">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <span class="font-medium text-gray-900">${customer.name}</span>
                            <span class="ml-2 text-xs px-2 py-0.5 ${balanceClass} rounded-full">
                                KSh ${balance.toLocaleString('en-US', {minimumFractionDigits: 2})}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-4 text-xs text-gray-600 mt-1">
                            <div class="truncate">
                                <i class="fas fa-hashtag mr-1"></i>
                                ${customer.customer_number}
                            </div>
                            <div class="truncate">
                                <i class="fas fa-phone mr-1"></i>
                                ${customer.phone || 'N/A'}
                            </div>
                            <div class="truncate">
                                <i class="fas fa-id-card mr-1"></i>
                                ${customer.id_number || 'N/A'}
                            </div>
                            <div class="truncate">
                                <i class="fas fa-tachometer-alt mr-1"></i>
                                ${customer.meter_numbers || 'N/A'}
                            </div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 mt-1"></i>
                </div>
            </div>
        `;
    });

    resultsContainer.innerHTML = html;
    resultsContainer.classList.remove('hidden');
}

function selectCustomer(customer) {
    // Set hidden field
    document.getElementById('selectedCustomerId').value = customer.id;

    // Update display
    document.getElementById('selectedCustomerName').textContent = customer.name;
    document.getElementById('selectedCustomerNumber').textContent = customer.customer_number;
    document.getElementById('selectedCustomerPhone').textContent = customer.phone || 'N/A';
    document.getElementById('selectedCustomerIdNumber').textContent = customer.id_number || 'N/A';
    document.getElementById('selectedCustomerMeters').textContent = customer.meter_numbers || 'N/A';
    document.getElementById('selectedCustomerBalance').textContent =
        'KSh ' + parseFloat(customer.balance).toLocaleString('en-US', {minimumFractionDigits: 2});

    // Show selected customer info, hide search results
    document.getElementById('selectedCustomerInfo').classList.remove('hidden');
    document.getElementById('searchResults').classList.add('hidden');
    document.getElementById('customerSearch').value = '';

    // Update activity tips
    document.getElementById('activityTips').textContent =
        `Customer has KSh ${parseFloat(customer.balance).toLocaleString('en-US', {minimumFractionDigits: 2})} overdue. ` +
        `Consider discussing payment plan options. Last bill due: ${customer.latest_bill_due || 'N/A'}`;
}

function clearCustomerSelection() {
    document.getElementById('selectedCustomerId').value = '';
    document.getElementById('selectedCustomerInfo').classList.add('hidden');
    document.getElementById('activityTips').textContent =
        'Select a customer and activity type to see tips';
}

// Add route to modal form submission
document.getElementById('collectionActivityForm').addEventListener('submit', function(e) {
    if (!document.getElementById('selectedCustomerId').value) {
        e.preventDefault();
        showToast('Please select a customer first', 'error');
        return false;
    }
});
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

    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.add('animate-slide-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

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
// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeActivityModal();
    }
    if (e.ctrlKey && e.key === 'l') {
        e.preventDefault();
        showCollectionActivityModal();
    }
});
</script>

<style>
/* Custom styles for better UX */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .bg-white\/80 {
        background: white !important;
    }
}
</style>
@endsection
