@extends('layouts.app')

@section('title', 'Reports - NYAWASCO')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [];
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Reports & Analytics',
        'subtitle' => 'Generate comprehensive system reports',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="w-full px-0 py-8">
        <!-- Report Selection Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Generate Report</h2>

            <form id="reportForm" action="{{ route('reports.generate') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Report Type Selection -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Report Type *
                        </label>
                        <select id="report_type" name="report_type" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="">Select a report type</option>
                            <option value="revenue">Revenue Report</option>
                            <option value="customer">Customer Report</option>
                            <option value="meter">Meter Report</option>
                            <option value="consumption">Consumption Report</option>
                            <option value="collection">Collection Report</option>
                            <option value="arrears">Arrears Report</option>
                            <option value="category">Meter Category Report</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date Range (Optional)
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <input type="date" id="start_date" name="start_date"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-xs text-gray-500">Start Date</span>
                            </div>
                            <div>
                                <input type="date" id="end_date" name="end_date"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-xs text-gray-500">End Date</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- In your reports/index.blade.php -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Export Format
                    </label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="view" checked
                                class="text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">View in Browser</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="pdf"
                                class="text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Download PDF</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="excel"
                                class="text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Download Excel</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="csv"
                                class="text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Download CSV</span>
                        </label>
                    </div>
                </div>

                <!-- Add detail level selection -->
                <div class="mt-6">
                    <label for="detail_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Detail Level
                    </label>
                    <select id="detail_level" name="detail_level"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="summary">Summary Only</option>
                        <option value="detailed">Detailed Data</option>
                        <option value="full">Full Report</option>
                    </select>
                </div>

                <!-- Generate Button -->
                <div class="mt-8 flex justify-end">
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Types Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Revenue Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-green-100">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-800">
                        Financial
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Revenue Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Detailed analysis of billing revenue, payments, and outstanding balances
                </p>
                <button onclick="selectReport('revenue')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Customer Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-blue-100">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                        Customer
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Customer Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Customer demographics, status analysis, and consumption patterns
                </p>
                <button onclick="selectReport('customer')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Meter Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-purple-100">
                        <i class="fas fa-tachometer-alt text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-purple-100 text-purple-800">
                        Assets
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Meter Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Meter inventory, status, location, and performance metrics
                </p>
                <button onclick="selectReport('meter')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Consumption Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-cyan-100">
                        <i class="fas fa-water text-cyan-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-cyan-100 text-cyan-800">
                        Usage
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Consumption Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Water consumption patterns, trends, and analysis by category
                </p>
                <button onclick="selectReport('consumption')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Collection Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-yellow-100">
                        <i class="fas fa-cash-register text-yellow-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                        Payments
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Collection Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Payment collection analysis by method, date, and collector
                </p>
                <button onclick="selectReport('collection')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Arrears Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-red-100 text-red-800">
                        Overdue
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Arrears Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Outstanding debt analysis, age analysis, and top debtors
                </p>
                <button onclick="selectReport('arrears')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Reports</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('reports.generate', ['report_type' => 'revenue', 'format' => 'pdf']) }}"
                   class="bg-green-50 hover:bg-green-100 p-4 rounded-lg border border-green-200 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-green-800">Revenue PDF</p>
                            <p class="text-xs text-green-600">This Month</p>
                        </div>
                        <i class="fas fa-file-pdf text-green-600"></i>
                    </div>
                </a>

                <a href="{{ route('reports.generate', ['report_type' => 'arrears', 'format' => 'pdf']) }}"
                   class="bg-red-50 hover:bg-red-100 p-4 rounded-lg border border-red-200 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-red-800">Arrears PDF</p>
                            <p class="text-xs text-red-600">Overdue Bills</p>
                        </div>
                        <i class="fas fa-file-pdf text-red-600"></i>
                    </div>
                </a>

                <a href="{{ route('reports.generate', ['report_type' => 'collection', 'format' => 'csv']) }}"
                   class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg border border-blue-200 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-blue-800">Collection CSV</p>
                            <p class="text-xs text-blue-600">Last 30 Days</p>
                        </div>
                        <i class="fas fa-file-csv text-blue-600"></i>
                    </div>
                </a>

                <a href="{{ route('reports.generate', ['report_type' => 'customer', 'format' => 'pdf']) }}"
                   class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg border border-purple-200 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-purple-800">Customer PDF</p>
                            <p class="text-xs text-purple-600">Active Customers</p>
                        </div>
                        <i class="fas fa-file-pdf text-purple-600"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function selectReport(type) {
    document.getElementById('report_type').value = type;
    document.getElementById('report_type').scrollIntoView({ behavior: 'smooth' });
    document.getElementById('report_type').focus();
}

// Set default dates
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const firstDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 2)
        .toISOString().split('T')[0];

    document.getElementById('start_date').value = firstDayOfMonth;
    document.getElementById('end_date').value = today;
});
</script>
@endsection
