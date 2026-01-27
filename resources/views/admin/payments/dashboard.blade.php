@extends('layouts.app')

@section('title', 'Payments Dashboard - NYAWASCO')
@php
    // Define method colors in PHP
    $methodColors = [
        'mpesa' => '#00A300',
        'bank' => '#4e73df',
        'cash' => '#f6c23e',
        'card' => '#e74a3b',
        'mobile_money' => '#36b9cc'
    ];
@endphp

@section('content')
@can('add payments')

<style>

.flatpickr-prev-month,
.flatpickr-next-month {
    display: none !important;
}
</style>
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>
<div class="min-h-screen bg-gray-50">
    @php
    $actionButtons = [
        [
            'route' => 'payments.index',
            'icon' => 'fas fa-list',
            'label' => 'All Payments',
            'color' => 'bg-blue-600'
        ],
        [
            'route' => 'admin.payments.unallocated',
            'icon' => 'fas fa-clock',
            'label' => 'Unallocated',
            'color' => 'bg-yellow-600'
        ],
        [
            'route' => 'admin.payments.methods-report',
            'icon' => 'fas fa-chart-bar',
            'label' => 'Methods Report',
            'color' => 'bg-purple-600'
        ]
    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Payments Analytics Dashboard',
        'subtitle' => 'Real-time insights and performance metrics',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <form id="dashboardFilter" method="GET" action="{{ route('admin.payments.dashboard') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <input type="text" name="date_range" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flatpickr-range"
                           value="{{ $startDate }} to {{ $endDate }}"
                           data-date-format="Y-m-d">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zone</label>
                    <select name="zone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 select2">
                        <option value="all">All Zones</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ $zoneId == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end space-x-3 md:col-span-3">
                    <button type="button" onclick="resetFilters()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </button>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>

                    <button type="button" onclick="exportDashboard()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-file-export mr-2"></i>
                        Export
                    </button>

                    <div class="flex items-center ml-auto">
                        <span class="realtime-badge bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                            Live
                        </span>
                        <button onclick="refreshDashboard()" class="ml-3 text-gray-600 hover:text-blue-600 transition duration-200" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Collections -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Collections</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">
                            KSh {{ number_format($cardStats['total_collections'], 2) }}
                        </p>
                        <div class="mt-3">
                            @php
                                $trend = $cardStats['total_collections'] - $cardStats['previous_total_collections'];
                                $trendPercent = $cardStats['previous_total_collections'] > 0
                                    ? ($trend / $cardStats['previous_total_collections']) * 100
                                    : ($trend > 0 ? 100 : 0);
                            @endphp
                            <span class="inline-flex items-center text-sm {{ $trend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas fa-arrow-{{ $trend >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ number_format(abs($trendPercent), 1) }}%
                                <span class="text-gray-500 ml-1">from last month</span>
                            </span>
                        </div>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Collection -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Today's Collection</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2" id="todayCollection">
                            KSh {{ number_format($cardStats['today_collection'], 2) }}
                        </p>
                        <div class="mt-3">
                            <span class="inline-flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-day mr-1"></i>
                                {{ now()->format('F j, Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Arrears -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Arrears</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">
                            KSh {{ number_format($cardStats['total_arrears'], 2) }}
                        </p>
                        <div class="mt-3">
                            <span class="inline-flex items-center text-sm text-yellow-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $cardStats['customers_with_arrears'] }} customers
                            </span>
                        </div>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Collection Efficiency -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div class="w-full">
                        <p class="text-sm font-medium text-gray-600">Collection Efficiency</p>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $cardStats['collection_efficiency'] }}%
                            </p>
                            <span class="text-sm text-gray-500">
                                {{ $cardStats['payment_count'] }} payments
                            </span>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full"
                                     style="width: {{ min($cardStats['collection_efficiency'], 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg ml-4">
                        <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Daily Collections Line Chart -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20 lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Daily Collections Trend</h3>
                        <p class="text-sm text-gray-600">Amount collected per day</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="changeChartType('daily', 'line')" class="text-gray-600 hover:text-blue-600 p-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-chart-line"></i>
                        </button>
                        <button onclick="changeChartType('daily', 'bar')" class="text-gray-600 hover:text-blue-600 p-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="dailyCollectionsChart"></canvas>
                </div>
            </div>

            <!-- Payment Methods Pie Chart -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Methods</h3>
                    <p class="text-sm text-gray-600">Distribution by method</p>
                </div>
                <div class="h-64 mb-4">
                    <canvas id="paymentMethodsChart"></canvas>
                </div>
                <div class="space-y-3">
                    @foreach($paymentMethods as $method)
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $methodColors[$method->payment_method] ?? '#6c757d' }}"></div>
                            <span class="text-sm font-medium text-gray-700">{{ ucfirst($method->payment_method) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-gray-900">
                                KSh {{ number_format($method->total_amount, 2) }}
                            </span>
                            <span class="text-xs text-gray-500 block">
                                {{ $method->count }} payments
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Arrears vs Collections -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20 lg:col-span-2">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Arrears vs Collections</h3>
                    <p class="text-sm text-gray-600">Daily comparison and collection ratio</p>
                </div>
                <div class="h-80">
                    <canvas id="arrearsVsCollectionsChart"></canvas>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">6-Month Trend</h3>
                    <p class="text-sm text-gray-600">Monthly collection pattern</p>
                </div>
                <div class="h-80">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Zone Comparison -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Zone Performance Comparison</h3>
                    <p class="text-sm text-gray-600">Collections, arrears, and efficiency by zone</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collections</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrears</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection Rate</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Meters</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Payment</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        @foreach($zoneComparison as $zone)
                        @php
                            $maxCollection = max(array_column($zoneComparison, 'collections'));
                            $collectionPercent = $maxCollection > 0 ? ($zone['collections'] / $maxCollection) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $zone['zone'] }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-gray-900">
                                    KSh {{ number_format($zone['collections'], 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold {{ $zone['arrears'] > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                                    KSh {{ number_format($zone['arrears'], 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-green-600 h-2 rounded-full"
                                             style="width: {{ $zone['collection_rate'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ number_format($zone['collection_rate'], 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($zone['active_meters']) }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    KSh {{ number_format($zone['average_payment'], 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full"
                                         style="width: {{ $collectionPercent }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Top Collectors -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Top 5 Collectors</h3>
                        <p class="text-sm text-gray-600">Customers with highest payments</p>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        High Performance
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($performanceData['top_collectors'] as $index => $collector)
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50/50 to-transparent rounded-lg border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 text-green-800 rounded-full flex items-center justify-center font-bold mr-3">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ $collector->first_name }} {{ $collector->last_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $collector->payment_count }} payments
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-600">
                                KSh {{ number_format($collector->total_collected, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">Total Collected</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Arrears -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Top 5 Arrears</h3>
                        <p class="text-sm text-gray-600">Customers with highest outstanding balances</p>
                    </div>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        Require Attention
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($performanceData['top_arrears'] as $index => $arrear)
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50/50 to-transparent rounded-lg border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 text-yellow-800 rounded-full flex items-center justify-center font-bold mr-3">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ $arrear->first_name }} {{ $arrear->last_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $arrear->overdue_bills }} overdue bills
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-yellow-600">
                                KSh {{ number_format($arrear->total_arrears, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">Outstanding Balance</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Payments Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                    <p class="text-sm text-gray-600">Latest payment transactions</p>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-500 mr-3" id="lastUpdated">
                        Updated: {{ now()->format('H:i:s') }}
                    </span>
                    <button onclick="loadRecentPayments()" class="text-gray-600 hover:text-blue-600 transition duration-200" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="recentPaymentsTable">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        <!-- Will be populated by AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

<script>
    // Chart colors - using Tailwind-like colors
    const chartColors = {
        primary: '#3b82f6',    // blue-500
        success: '#10b981',    // green-500
        info: '#06b6d4',       // cyan-500
        warning: '#f59e0b',    // yellow-500
        danger: '#ef4444',     // red-500
        secondary: '#6b7280',  // gray-500
        purple: '#8b5cf6',     // violet-500
        pink: '#ec4899'        // pink-500
    };

    // Initialize date picker
    $(document).ready(function() {
        // Select2
        $('.select2').select2({
            width: '100%',
            placeholder: 'Select zone...'
        });

        // Date range picker
        $('.flatpickr-range').flatpickr({
            mode: "range",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const [start, end] = selectedDates;
                    $('input[name="start_date"]').val(start.toISOString().split('T')[0]);
                    $('input[name="end_date"]').val(end.toISOString().split('T')[0]);
                }
            }
        });

        // Initialize charts
        initCharts();

        // Load recent payments
        loadRecentPayments();

        // Start real-time updates
        startRealtimeUpdates();
    });

    // Initialize all charts
    function initCharts() {
        // 1. Daily Collections Chart
        const dailyCtx = document.getElementById('dailyCollectionsChart').getContext('2d');
        const dailyChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($chartsData['daily_collections']->toArray())) !!},
                datasets: [{
                    label: 'Daily Collections',
                    data: {!! json_encode(array_values($chartsData['daily_collections']->toArray())) !!},
                    borderColor: chartColors.primary,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: chartColors.primary,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'KSh ' + context.parsed.y.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return 'KSh ' + value.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                        }
                    }
                }
            }
        });

        // 2. Payment Methods Chart
        const methodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        const methodsChart = new Chart(methodsCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($paymentMethods->pluck('payment_method')->map(function($method) {
                    return ucfirst($method);
                })) !!},
                datasets: [{
                    data: {!! json_encode($paymentMethods->pluck('total_amount')) !!},
                    backgroundColor: {!! json_encode($paymentMethods->map(function($method) use ($methodColors) {
                        return $methodColors[$method->payment_method] ?? '#6b7280';
                    })) !!},
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return [
                                    context.label,
                                    'KSh ' + value.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }),
                                    percentage + '% of total'
                                ];
                            }
                        }
                    }
                }
            }
        });

        // 3. Arrears vs Collections Chart
        const arrearsCtx = document.getElementById('arrearsVsCollectionsChart').getContext('2d');
        const arrearsChart = new Chart(arrearsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($chartsData['arrears_vs_collections'], 'date')) !!},
                datasets: [
                    {
                        label: 'Collections',
                        data: {!! json_encode(array_column($chartsData['arrears_vs_collections'], 'collections')) !!},
                        backgroundColor: chartColors.success,
                        borderColor: chartColors.success,
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Arrears',
                        data: {!! json_encode(array_column($chartsData['arrears_vs_collections'], 'arrears')) !!},
                        backgroundColor: chartColors.warning + '80',
                        borderColor: chartColors.warning,
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Collection Ratio (%)',
                        data: {!! json_encode(array_column($chartsData['arrears_vs_collections'], 'collection_ratio')) !!},
                        type: 'line',
                        borderColor: chartColors.purple,
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointStyle: 'circle',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#6b7280',
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Amount (KSh)',
                            color: '#6b7280'
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return 'KSh ' + value.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Collection Ratio (%)',
                            color: '#6b7280'
                        },
                        min: 0,
                        max: 100,
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // 4. Monthly Trend Chart
        const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($chartsData['monthly_trend']->toArray())) !!},
                datasets: [{
                    label: 'Monthly Collections',
                    data: {!! json_encode(array_values($chartsData['monthly_trend']->toArray())) !!},
                    backgroundColor: Array.from({length: {!! $chartsData['monthly_trend']->count() !!}}, (_, i) =>
                        i === {!! $chartsData['monthly_trend']->count() - 1 !!} ? chartColors.primary : chartColors.primary + '80'
                    ),
                    borderColor: chartColors.primary,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return 'KSh ' + value.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                        }
                    }
                }
            }
        });

        // Store charts for updates
        window.charts = {
            daily: dailyChart,
            methods: methodsChart,
            arrears: arrearsChart,
            monthly: monthlyChart
        };
    }

    // Load recent payments via AJAX
    function loadRecentPayments() {
        $.ajax({
            url: '{{ route("admin.payments.dashboard.realtime") }}',
            method: 'GET',
            data: {
                zone: '{{ $zoneId }}'
            },
            success: function(response) {
                // Update today's collection
                $('#todayCollection').text('KSh ' + parseFloat(response.today_collection).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                // Update recent payments table
                const tbody = $('#recentPaymentsTable tbody');
                tbody.empty();

                if (response.recent_payments.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p>No recent payments found</p>
                            </td>
                        </tr>
                    `);
                } else {
                    response.recent_payments.forEach(function(payment) {
                        const statusColors = {
                            'completed': 'green',
                            'pending': 'yellow',
                            'failed': 'red',
                            'allocated': 'blue',
                            'voided': 'gray'
                        };
                        const statusColor = statusColors[payment.payment_status] || 'gray';

                        const row = `
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-medium text-blue-600">
                                        ${payment.payment_no}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        ${payment.customer?.first_name} ${payment.customer?.last_name}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ${payment.customer?.customer_number || 'N/A'}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        ${payment.meter?.meter_number || 'N/A'}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-green-600">
                                        KSh ${parseFloat(payment.amount).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        })}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        ${payment.payment_method}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        ${moment(payment.payment_date).format('MMM D, YYYY')}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800">
                                        ${payment.payment_status}
                                    </span>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }

                // Update timestamp
                $('#lastUpdated').text('Updated: ' + moment(response.updated_at).format('HH:mm:ss'));
            },
            error: function(xhr) {
                console.error('Failed to load recent payments');
            }
        });
    }

    // Start real-time updates every 30 seconds
    function startRealtimeUpdates() {
        setInterval(loadRecentPayments, 30000);
    }

    // Refresh entire dashboard
    function refreshDashboard() {
        location.reload();
    }

    // Reset filters
    function resetFilters() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        $('input[name="start_date"]').val(firstDay.toISOString().split('T')[0]);
        $('input[name="end_date"]').val(lastDay.toISOString().split('T')[0]);
        $('select[name="zone"]').val('all').trigger('change');
        $('.flatpickr-range').val(firstDay.toLocaleDateString() + ' to ' + lastDay.toLocaleDateString());

        $('#dashboardFilter').submit();
    }

    // Export dashboard data
    function exportDashboard() {
        const form = $('#dashboardFilter').clone();
        form.attr('action', '{{ route("admin.payments.dashboard.export") }}');
        form.attr('target', '_blank');
        form.attr('method', 'POST');
        form.append('@csrf');
        $('body').append(form);
        form.submit();
        form.remove();
    }

    // Change chart type
    function changeChartType(chartName, type) {
        if (window.charts[chartName]) {
            window.charts[chartName].config.type = type;
            window.charts[chartName].update();
        }
    }

    // Helper to capitalize first letter
    function ucfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>
@endpush
@endcan
@endsection
