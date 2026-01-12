@extends('layouts.app')

@section('title', 'Report - NYAWASCO')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @include('components.dashboard-header', [
        'title' => $reportData['type'],
        'subtitle' => 'Generated on ' . now()->format('Y-m-d H:i:s'),
        'actionButtons' => [
        ]
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Report Summary -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $reportData['type'] }}</h2>
                    <p class="text-sm text-gray-600">
                        @if($startDate)
                            Period: {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}
                        @else
                            All Time Data
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Generated</p>
                    <p class="font-medium">{{ now()->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @foreach($reportData['summary'] as $key => $value)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                        {{ str_replace('_', ' ', $key) }}
                    </p>
                    <p class="text-lg font-semibold text-gray-800">
                        @if(is_numeric($value))
                            @if(strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false)
                                KSh {{ number_format($value, 2) }}
                            @elseif(strpos($key, 'rate') !== false || strpos($key, 'percentage') !== false)
                                {{ number_format($value, 2) }}%
                            @elseif(strpos($key, 'consumption') !== false)
                                {{ number_format($value, 2) }} m³
                            @else
                                {{ number_format($value) }}
                            @endif
                        @elseif($value instanceof \Carbon\Carbon)
                            {{ $value->format('Y-m-d') }}
                        @else
                            {{ $value }}
                        @endif
                    </p>
                </div>
                @endforeach
            </div>

            <!-- Additional Data Sections -->
            @if(isset($reportData['monthly_breakdown']))
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Breakdown</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bills</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reportData['monthly_breakdown'] as $month)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ date('F Y', mktime(0, 0, 0, $month->month, 1, $month->year)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month->bill_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KSh {{ number_format($month->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KSh {{ number_format($month->paid_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KSh {{ number_format($month->total_amount - $month->paid_amount, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if(isset($reportData['category_breakdown']))
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Category Breakdown</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bills</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection %</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reportData['category_breakdown'] as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $category->category }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $category->bill_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KSh {{ number_format($category->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KSh {{ number_format($category->paid_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $category->total_amount > 0 ? number_format(($category->paid_amount / $category->total_amount) * 100, 2) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Main Data Table -->
            @if(isset($reportData['bills']))
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Detailed Data</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reportData['bills'] as $bill)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                    {{ $bill->bill_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $bill->customer->first_name }} {{ $bill->customer->last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $bill->meter->meter_number ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KSh {{ number_format($bill->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KSh {{ number_format($bill->paid_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KSh {{ number_format($bill->balance, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'paid' => 'bg-green-100 text-green-800',
                                            'unpaid' => 'bg-red-100 text-red-800',
                                            'partial' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bill->bill_status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($bill->bill_status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Export Options</h3>
            <!-- In your reports/show.blade.php -->
            <div class="flex space-x-4">
                <button onclick="exportReport('pdf')"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200 flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export as PDF
                </button>
                <button onclick="exportReport('excel')"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition duration-200 flex items-center">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export as Excel
                </button>
                <button onclick="exportReport('csv')"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 flex items-center">
                    <i class="fas fa-file-csv mr-2"></i>
                    Export as CSV
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport(format) {
    const url = new URL(window.location.href);
    url.searchParams.set('format', format);
    window.location.href = url.toString();
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        font-size: 12px;
    }
    table {
        font-size: 10px;
    }
}
</style>
@endsection
