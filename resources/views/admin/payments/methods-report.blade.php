@extends('layouts.app')

@section('title', 'Payment Methods Report - NYAWASCO')

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
            'label' => 'Unallocated',
            'color' => 'bg-blue-600'
        ]
    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Payment Methods Report',
        'subtitle' => 'Analysis of Payment Methods and Trends',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <!-- Date Range Filter -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <form method="GET" action="{{ route('admin.payments.methods-report') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-end space-x-3">
                    <a href="{{ route('admin.payments.methods-report') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Reset
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Payments</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $report->sum('payment_count') }}</p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Amount</p>
                    <p class="text-2xl font-bold text-green-600">
                        KSh {{ number_format($report->sum('total_amount'), 2) }}
                    </p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Average Payment</p>
                    <p class="text-2xl font-bold text-purple-600">
                        KSh {{ number_format($report->avg('average_amount'), 2) }}
                    </p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Most Popular</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $report->sortByDesc('total_amount')->first()->payment_method ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Payment Methods Breakdown -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Payment Methods Breakdown</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Table View -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Share</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white/50 divide-y divide-gray-200">
                            @foreach($report as $item)
                            @php
                                $totalAmountAll = $report->sum('total_amount');
                                $share = $totalAmountAll > 0 ? ($item->total_amount / $totalAmountAll) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg
                                            {{ $item->payment_method == 'cash' ? 'bg-green-100' :
                                               ($item->payment_method == 'mpesa' ? 'bg-purple-100' :
                                               ($item->payment_method == 'bank' ? 'bg-blue-100' : 'bg-gray-100')) }} mr-3">
                                            <i class="fas
                                                {{ $item->payment_method == 'cash' ? 'fa-money-bill' :
                                                   ($item->payment_method == 'mpesa' ? 'fa-mobile-alt' :
                                                   ($item->payment_method == 'bank' ? 'fa-university' : 'fa-credit-card')) }}
                                                {{ $item->payment_method == 'cash' ? 'text-green-600' :
                                                   ($item->payment_method == 'mpesa' ? 'text-purple-600' :
                                                   ($item->payment_method == 'bank' ? 'text-blue-600' : 'text-gray-600')) }}">
                                            </i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 capitalize">
                                                {{ $item->payment_method }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $item->payment_method == 'mpesa' ? 'Mobile Money' :
                                                   ($item->payment_method == 'bank' ? 'Bank Transfer' : 'Cash Payment') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $item->payment_count }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ round(($item->payment_count / $report->sum('payment_count')) * 100, 1) }}%
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="text-sm font-semibold text-green-600">
                                        KSh {{ number_format($item->total_amount, 2) }}
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        KSh {{ number_format($item->average_amount, 2) }}
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="h-2 rounded-full
                                                {{ $item->payment_method == 'cash' ? 'bg-green-500' :
                                                   ($item->payment_method == 'mpesa' ? 'bg-purple-500' :
                                                   ($item->payment_method == 'bank' ? 'bg-blue-500' : 'bg-gray-500')) }}"
                                                style="width: {{ $share }}%">
                                            </div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ round($share, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Chart View -->
                <div>
                    <div class="bg-gray-50 rounded-xl p-4 h-64 flex items-center justify-center">
                        <!-- This would be a chart using Chart.js or similar -->
                        <div id="paymentMethodsChart" class="w-full h-full">
                            <!-- Chart will be rendered here -->
                            <div class="text-center text-gray-500">
                                <i class="fas fa-chart-pie text-4xl mb-2"></i>
                                <p>Payment Methods Distribution</p>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        @foreach($report as $item)
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2
                                {{ $item->payment_method == 'cash' ? 'bg-green-500' :
                                   ($item->payment_method == 'mpesa' ? 'bg-purple-500' :
                                   ($item->payment_method == 'bank' ? 'bg-blue-500' : 'bg-gray-500')) }}">
                            </div>
                            <span class="text-sm text-gray-700 capitalize">{{ $item->payment_method }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Trend -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Daily Payment Trends</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cash</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">M-Pesa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daily Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cumulative</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        @php
                            $cumulative = 0;
                            $groupedByDate = $dailyTrend->groupBy('date');
                        @endphp

                        @foreach($groupedByDate as $date => $methods)
                        @php
                            $cash = $methods->where('payment_method', 'cash')->first()->daily_total ?? 0;
                            $mpesa = $methods->where('payment_method', 'mpesa')->first()->daily_total ?? 0;
                            $bank = $methods->where('payment_method', 'bank')->first()->daily_total ?? 0;
                            $other = $methods->whereNotIn('payment_method', ['cash', 'mpesa', 'bank'])->sum('daily_total');
                            $dailyTotal = $cash + $mpesa + $bank + $other;
                            $cumulative += $dailyTotal;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($date)->format('l') }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($cash > 0)
                                    KSh {{ number_format($cash, 2) }}
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($mpesa > 0)
                                    KSh {{ number_format($mpesa, 2) }}
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($bank > 0)
                                    KSh {{ number_format($bank, 2) }}
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($other > 0)
                                    KSh {{ number_format($other, 2) }}
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm font-semibold text-green-600">
                                    KSh {{ number_format($dailyTotal, 2) }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-blue-600">
                                    KSh {{ number_format($cumulative, 2) }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <!-- Monthly Totals -->
                    <tfoot class="bg-gray-50/80">
                        <tr>
                            <td class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                Totals:
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">
                                    KSh {{ number_format($report->where('payment_method', 'cash')->sum('total_amount'), 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">
                                    KSh {{ number_format($report->where('payment_method', 'mpesa')->sum('total_amount'), 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">
                                    KSh {{ number_format($report->where('payment_method', 'bank')->sum('total_amount'), 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">
                                    KSh {{ number_format($report->whereNotIn('payment_method', ['cash', 'mpesa', 'bank'])->sum('total_amount'), 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-lg font-bold text-green-600">
                                    KSh {{ number_format($report->sum('total_amount'), 2) }}
                                </div>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Export Options -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mt-8 border border-white/20">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Report</h3>
            <div class="flex flex-wrap gap-4">
                <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export to Excel
                </button>


            </div>
        </div>
    </div>
</div>

<script>
function exportToExcel() {
    // Implement Excel export
    alert('Excel export feature will be implemented');
}

function exportToPDF() {
    // Implement PDF export
    alert('PDF export feature will be implemented');
}

function printReport() {
    window.print();
}

// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
    // This would initialize Chart.js if included
    // For now, we'll just show a placeholder
});
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
        border: 1px solid #e5e7eb !important;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #e5e7eb;
        padding: 8px;
    }
}
</style>
@endsection
