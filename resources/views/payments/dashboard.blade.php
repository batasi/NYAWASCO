@extends('layouts.app')

@section('title', 'Payments Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
    .card-stat {
        border-radius: 10px;
        transition: transform 0.2s;
    }
    .card-stat:hover {
        transform: translateY(-2px);
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    .trend-up {
        color: #10b981;
    }
    .trend-down {
        color: #ef4444;
    }
    .chart-container {
        height: 300px;
        position: relative;
    }
    .performance-card {
        border-left: 4px solid;
        transition: all 0.3s;
    }
    .performance-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .zone-progress {
        height: 8px;
        border-radius: 4px;
    }
    .realtime-badge {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-3 mb-4 border-bottom">
        <h1 class="h2 mb-0">
            <i class="fas fa-chart-line text-primary me-2"></i>Payments Dashboard
        </h1>
        <div class="d-flex align-items-center gap-2">
            <span class="realtime-badge badge bg-success">
                <i class="fas fa-circle me-1"></i>Live
            </span>
            <button onclick="refreshDashboard()" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="dashboardFilter" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <input type="text" name="date_range" class="form-control flatpickr-range"
                                   value="{{ $startDate }} to {{ $endDate }}"
                                   data-date-format="Y-m-d">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Zone</label>
                            <select name="zone" class="form-control select2">
                                <option value="all">All Zones</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ $zoneId == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" onclick="resetFilters()" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" onclick="exportDashboard()" class="btn btn-success w-100">
                                <i class="fas fa-file-export me-1"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Collections -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stat border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Collections
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KSh {{ number_format($cardStats['total_collections'], 2) }}
                            </div>
                            <div class="mt-2">
                                @php
                                    $trend = $cardStats['total_collections'] - $cardStats['previous_total_collections'];
                                    $trendPercent = $cardStats['previous_total_collections'] > 0
                                        ? ($trend / $cardStats['previous_total_collections']) * 100
                                        : 100;
                                @endphp
                                <span class="{{ $trend >= 0 ? 'trend-up' : 'trend-down' }}">
                                    <i class="fas fa-arrow-{{ $trend >= 0 ? 'up' : 'down' }} me-1"></i>
                                    {{ number_format(abs($trendPercent), 1) }}%
                                </span>
                                <span class="text-muted text-xs">from previous period</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave stat-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Collection -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stat border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Collection
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayCollection">
                                KSh {{ number_format($cardStats['today_collection'], 2) }}
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-calendar-day me-1"></i>
                                {{ now()->format('F j, Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check stat-icon text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Arrears -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stat border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Arrears
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KSh {{ number_format($cardStats['total_arrears'], 2) }}
                            </div>
                            <div class="mt-2">
                                <span class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $cardStats['customers_with_arrears'] }} customers
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle stat-icon text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collection Efficiency -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stat border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Collection Efficiency
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ $cardStats['collection_efficiency'] }}%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress">
                                        <div class="progress-bar bg-info"
                                             role="progressbar"
                                             style="width: {{ min($cardStats['collection_efficiency'], 100) }}%"
                                             aria-valuenow="{{ $cardStats['collection_efficiency'] }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                {{ $cardStats['payment_count'] }} payments
                                @if($cardStats['previous_payment_count'])
                                    <span class="{{ $cardStats['payment_count'] >= $cardStats['previous_payment_count'] ? 'trend-up' : 'trend-down' }}">
                                        ({{ $cardStats['payment_count'] >= $cardStats['previous_payment_count'] ? '+' : '' }}{{ $cardStats['payment_count'] - $cardStats['previous_payment_count'] }})
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie stat-icon text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <!-- Daily Collections Line Chart -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-1"></i> Daily Collections Trend
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeChartType('daily', 'line')">Line Chart</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartType('daily', 'bar')">Bar Chart</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="dailyCollectionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Pie Chart -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card me-1"></i> Payment Methods Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($paymentMethods as $method)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-xs">
                                <i class="fas fa-circle me-1" style="color: {{ $methodColors[$method->payment_method] ?? '#6c757d' }}"></i>
                                {{ ucfirst($method->payment_method) }}
                            </span>
                            <span class="text-xs font-weight-bold">
                                KSh {{ number_format($method->total_amount, 2) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <!-- Arrears vs Collections -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-balance-scale me-1"></i> Arrears vs Collections
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="arrearsVsCollectionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-1"></i> 6-Month Trend
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone Comparison -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-globe-africa me-1"></i> Zone Performance Comparison
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Zone</th>
                                    <th>Collections</th>
                                    <th>Arrears</th>
                                    <th>Collection Rate</th>
                                    <th>Active Meters</th>
                                    <th>Avg Payment</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zoneComparison as $zone)
                                @php
                                    $maxCollection = max(array_column($zoneComparison, 'collections'));
                                    $collectionPercent = $maxCollection > 0 ? ($zone['collections'] / $maxCollection) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="font-weight-bold">{{ $zone['zone'] }}</td>
                                    <td>KSh {{ number_format($zone['collections'], 2) }}</td>
                                    <td class="{{ $zone['arrears'] > 0 ? 'text-warning' : 'text-success' }}">
                                        KSh {{ number_format($zone['arrears'], 2) }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress zone-progress flex-grow-1 me-2">
                                                <div class="progress-bar bg-success"
                                                     style="width: {{ $zone['collection_rate'] }}%">
                                                </div>
                                            </div>
                                            <span>{{ number_format($zone['collection_rate'], 1) }}%</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($zone['active_meters']) }}</td>
                                    <td>KSh {{ number_format($zone['average_payment'], 2) }}</td>
                                    <td>
                                        <div class="progress zone-progress" style="width: 100px;">
                                            <div class="progress-bar bg-primary"
                                                 style="width: {{ $collectionPercent }}%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Cards -->
    <div class="row">
        <!-- Top Collectors -->
        <div class="col-xl-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-trophy me-1"></i> Top 5 Collectors
                    </h6>
                    <span class="badge bg-success">High Performance</span>
                </div>
                <div class="card-body">
                    @foreach($performanceData['top_collectors'] as $index => $collector)
                    <div class="performance-card card border-0 mb-3 border-left-success">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="rank-circle bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 30px; height: 30px; font-weight: bold;">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $collector->first_name }} {{ $collector->last_name }}</h6>
                                            <small class="text-muted">{{ $collector->payment_count }} payments</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0 font-weight-bold text-success">
                                        KSh {{ number_format($collector->total_collected, 2) }}
                                    </div>
                                    <small class="text-muted">Total Collected</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Arrears -->
        <div class="col-xl-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i> Top 5 Arrears
                    </h6>
                    <span class="badge bg-warning">Require Attention</span>
                </div>
                <div class="card-body">
                    @foreach($performanceData['top_arrears'] as $index => $arrear)
                    <div class="performance-card card border-0 mb-3 border-left-warning">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="rank-circle bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 30px; height: 30px; font-weight: bold;">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $arrear->first_name }} {{ $arrear->last_name }}</h6>
                                            <small class="text-muted">{{ $arrear->overdue_bills }} overdue bills</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0 font-weight-bold text-warning">
                                        KSh {{ number_format($arrear->total_arrears, 2) }}
                                    </div>
                                    <small class="text-muted">Outstanding Balance</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-1"></i> Recent Payments
                    </h6>
                    <small class="text-muted" id="lastUpdated">Updated: {{ now()->format('H:i:s') }}</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="recentPaymentsTable">
                            <thead>
                                <tr>
                                    <th>Payment #</th>
                                    <th>Customer</th>
                                    <th>Meter</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

<script>
    // Chart colors
    const chartColors = {
        primary: '#4e73df',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b',
        secondary: '#858796',
        light: '#f8f9fc',
        dark: '#5a5c69'
    };

    const methodColors = {
        mpesa: '#00A300',
        bank: '#4e73df',
        cash: '#f6c23e',
        card: '#e74a3b',
        mobile_money: '#36b9cc'
    };

    // Initialize date picker
    $(document).ready(function() {
        // Select2
        $('.select2').select2({
            width: '100%'
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
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
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
                    y: {
                        beginAtZero: true,
                        ticks: {
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
                    backgroundColor: {!! json_encode($paymentMethods->map(function($method) {
                        return methodColors[$method->payment_method] || chartColors.secondary;
                    })) !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return context.label + ': KSh ' + value.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + ' (' + percentage + '%)';
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
                        yAxisID: 'y'
                    },
                    {
                        label: 'Arrears',
                        data: {!! json_encode(array_column($chartsData['arrears_vs_collections'], 'arrears')) !!},
                        backgroundColor: chartColors.warning,
                        borderColor: chartColors.warning,
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Collection Ratio (%)',
                        data: {!! json_encode(array_column($chartsData['arrears_vs_collections'], 'collection_ratio')) !!},
                        type: 'line',
                        borderColor: chartColors.info,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        fill: false,
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
                        display: true
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Amount (KSh)'
                        },
                        ticks: {
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
                            text: 'Collection Ratio (%)'
                        },
                        min: 0,
                        max: 100,
                        grid: {
                            drawOnChartArea: false
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
                    backgroundColor: chartColors.primary,
                    borderColor: chartColors.primary,
                    borderWidth: 1
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
                    y: {
                        beginAtZero: true,
                        ticks: {
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
            url: '{{ route("payments.dashboard.realtime") }}',
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

                response.recent_payments.forEach(function(payment) {
                    const row = `
                        <tr>
                            <td>
                                <a href="/payments/${payment.id}" class="text-primary">
                                    ${payment.payment_no}
                                </a>
                            </td>
                            <td>
                                ${payment.customer?.first_name} ${payment.customer?.last_name}
                                <br>
                                <small class="text-muted">${payment.customer?.customer_number}</small>
                            </td>
                            <td>${payment.meter?.meter_number || 'N/A'}</td>
                            <td class="font-weight-bold">
                                KSh ${parseFloat(payment.amount).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    ${payment.payment_method}
                                </span>
                            </td>
                            <td>${moment(payment.payment_date).format('MMM D, YYYY')}</td>
                            <td>
                                <span class="badge bg-${getStatusColor(payment.payment_status)}">
                                    ${payment.payment_status}
                                </span>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

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
        // You can implement export to Excel, PDF, or CSV
        // For now, just redirect to export route
        const form = $('#dashboardFilter').clone();
        form.attr('action', '{{ route("payments.dashboard.export") }}');
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

    // Helper function to get status color
    function getStatusColor(status) {
        const colors = {
            'completed': 'success',
            'pending': 'warning',
            'failed': 'danger',
            'allocated': 'info',
            'voided': 'secondary'
        };
        return colors[status] || 'secondary';
    }

    // Helper to capitalize first letter
    function ucfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>
@endpush
