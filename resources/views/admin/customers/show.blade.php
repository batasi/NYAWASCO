@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp
@section('title', 'Customer Profile - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div class="text-center md:text-left">
            <h1 class="text-2xl md:text-3xl font-bold text-blue-800">{{ $customer->first_name }}'s Profile</h1>
            <p class="text-gray-600 mt-2">Complete customer information, billing history, and account management</p>
        </div>

        <a href="{{ route('admin.customers.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition duration-200 flex items-center justify-center w-20px md:w-auto no-print text-sm">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Customers
        </a>
    </div>

    <!-- Account Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Account Balance -->
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Account Balance</p>
                    <p class="text-xl md:text-2xl font-bold mt-1 {{ $billingStats['account_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        KSh {{ number_format(abs($billingStats['account_balance']), 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $billingStats['account_balance'] >= 0 ? 'Credit Balance' : 'Overdue Amount' }}
                    </p>
                </div>
                <div class="p-3 rounded-full {{ $billingStats['account_balance'] >= 0 ? 'bg-green-100' : 'bg-red-100' }}">
                    <i class="fas {{ $billingStats['account_balance'] >= 0 ? 'fa-wallet text-green-600' : 'fa-exclamation-triangle text-red-600' }} text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Outstanding Balance -->
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Outstanding Balance</p>
                    <p class="text-xl md:text-2xl font-bold mt-1 text-red-600">
                        KSh {{ number_format($billingStats['outstanding_balance'], 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $billingStats['unpaid_bills'] }} unpaid bills</p>
                </div>
                <div class="p-3 rounded-full bg-red-100">
                    <i class="fas fa-file-invoice text-red-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Consumption -->
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Consumption</p>
                    <p class="text-xl md:text-2xl font-bold mt-1 text-blue-600">
                        {{ number_format($readingStats['total_consumption'], 2) }} m³
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $readingStats['total_readings'] }} readings</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-tachometer-alt text-blue-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Monthly Usage -->
        <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Avg Monthly Usage</p>
                    <p class="text-xl md:text-2xl font-bold mt-1 text-purple-600">
                        {{ number_format($readingStats['average_monthly_consumption'], 2) }} m³
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Based on history</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-chart-line text-purple-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6 md:space-y-8">

            <!-- Status & Meter Management Section -->
            <div class="space-y-6 no-print">
                <!-- Status Management Card -->
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <i class="fas fa-cogs text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Account Status Management</h2>
                                <p class="text-sm text-gray-500">Manage customer account status and transitions</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-500">Last Updated</div>
                            <div class="text-sm text-gray-400">{{ $customer->updated_at->format('M j, Y') }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <!-- Current Status Panel -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Current Status</h3>

                            @php
                                $statusConfig = [
                                    'active' => ['color' => 'green', 'icon' => 'check-circle', 'description' => 'Account is active and operational'],
                                    'inactive' => ['color' => 'gray', 'icon' => 'ban', 'description' => 'Account is not active'],
                                    'pending' => ['color' => 'yellow', 'icon' => 'clock', 'description' => 'Account is pending activation'],
                                    'suspended' => ['color' => 'red', 'icon' => 'lock', 'description' => 'Account temporarily suspended'],
                                ];
                                $currentStatus = $statusConfig[$customer->status];
                            @endphp

                            <div class="bg-{{ $currentStatus['color'] }}-50 border border-{{ $currentStatus['color'] }}-200 rounded-lg p-4">
                                <div class="flex items-start space-x-4">
                                    <div class="p-3 bg-{{ $currentStatus['color'] }}-100 rounded-lg">
                                        <i class="fas fa-{{ $currentStatus['icon'] }} text-{{ $currentStatus['color'] }}-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-3 mb-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-{{ $currentStatus['color'] }}-100 text-{{ $currentStatus['color'] }}-800 capitalize">
                                                {{ $customer->status }}
                                            </span>
                                            @if($customer->status === 'active')
                                                <span class="flex h-2 w-2">
                                                    <span class="animate-ping absolute h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                                                    <span class="relative h-2 w-2 rounded-full bg-green-500"></span>
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-sm text-{{ $currentStatus['color'] }}-800 mb-3">{{ $currentStatus['description'] }}</p>

                                        <!-- Status Details -->
                                        <div class="space-y-2 text-sm">
                                            @if($customer->status_reason)
                                            <div class="flex items-start space-x-2">
                                                <i class="fas fa-tag text-{{ $currentStatus['color'] }}-600 mt-0.5"></i>
                                                <div>
                                                    <span class="font-medium text-gray-700">Reason:</span>
                                                    <span class="ml-2 text-gray-600 capitalize">
                                                        {{ str_replace('_', ' ', $customer->status_reason) }}
                                                    </span>
                                                </div>
                                            </div>
                                            @endif

                                            @if($customer->status_notes)
                                            <div class="flex items-start space-x-2">
                                                <i class="fas fa-comment text-{{ $currentStatus['color'] }}-600 mt-0.5"></i>
                                                <div class="flex-1">
                                                    <span class="font-medium text-gray-700">Administrative Notes:</span>
                                                    <p class="mt-1 text-gray-600 bg-white p-2 rounded border text-xs whitespace-pre-line">{{ $customer->status_notes }}</p>
                                                </div>
                                            </div>
                                            @endif

                                            @if($customer->status_updated_at)
                                            <div class="flex items-center space-x-2 text-gray-500">
                                                <i class="fas fa-clock text-sm"></i>
                                                <span class="text-xs">Last updated: {{ $customer->status_updated_at->format('M j, Y g:i A') }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Update Panel -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Update Status</h3>

                            <!-- Display Success Messages -->
                            @if(session('success'))
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            <!-- Display Error Messages -->
                            @if(session('error'))
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span>{{ session('error') }}</span>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.customers.update-status', $customer) }}" class="space-y-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                                    <select name="status" id="statusSelect" required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                        <option value="">Select new status...</option>
                                        @foreach(['active', 'inactive', 'pending', 'suspended'] as $status)
                                            @if($status !== $customer->status)
                                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status Reason Field -->
                                <div id="statusReasonField" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Reason for Status Change
                                        <span class="text-gray-400 font-normal">(Required)</span>
                                    </label>
                                    <textarea name="status_reason" rows="2" id="statusReasonInput"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                                            placeholder="Document the reason for this status change..." required></textarea>
                                    @error('status_reason')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Administrative Notes
                                        <span class="text-gray-400 font-normal">(Required)</span>
                                    </label>
                                    <textarea name="notes" rows="3" id="statusNotes"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                                            placeholder="Document any additional notes for this status change..." required></textarea>
                                    @error('notes')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2 shadow-sm">
                                    <i class="fas fa-sync-alt"></i>
                                    <span>Update Account Status</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Meter Management Card -->
                @if($customer->meters->count() > 0)
                <!-- Meter Assigned State -->
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-50 rounded-lg">
                                <i class="fas fa-tachometer-alt text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Meter Management</h2>
                                <p class="text-sm text-gray-500">{{ $customer->meters->count() }} meter(s) assigned</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-500">Last Assigned</div>
                            <div class="text-sm text-gray-600">{{ $customer->meters->sortByDesc('installation_date')->first()->installation_date?->format('M j, Y') ?? 'Not specified' }}</div>
                        </div>
                    </div>

                    <!-- Multiple Meters Display -->
                    <div class="space-y-4 mb-6">
                        @foreach($customer->meters as $meter)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition duration-200">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-lg">{{ $meter->meter_number }}</h4>
                                    <p class="text-sm text-gray-600">{{ $meter->meterCategory->name ?? 'No Category' }} • {{ ucfirst($meter->meter_type) }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($meter->status) }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">Installed: {{ $meter->installation_date?->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Meter details -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 block">Current Reading</span>
                                    <span class="font-semibold text-green-600">{{ number_format($meter->current_reading, 2) }} m³</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Initial Reading</span>
                                    <span class="font-semibold">{{ number_format($meter->initial_reading, 2) }} m³</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Total Consumption</span>
                                    <span class="font-semibold text-blue-600">{{ number_format($meter->current_reading - $meter->initial_reading, 2) }} m³</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Balance</span>
                                    <span class="font-semibold {{ $meter->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        KSh {{ number_format($meter->current_balance, 2) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Individual Meter Actions -->
                            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">
                                @if($customer->status === 'active')
                                    <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id, 'meter_id' => $meter->id]) }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center">
                                        <i class="fas fa-tachometer-alt mr-1"></i>
                                        Record Reading
                                    </a>
                                @else
                                    <button class="bg-gray-400 cursor-not-allowed text-white px-3 py-1 rounded text-sm flex items-center"
                                            title="Cannot record reading - Customer status is {{ $customer->status }}">
                                        <i class="fas fa-tachometer-alt mr-1"></i>
                                        Record Reading
                                    </button>
                                @endif

                                <a href="{{ route('admin.meters.show', $meter) }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center">
                                    <i class="fas fa-eye mr-1"></i>
                                    View Meter
                                </a>

                                <form method="POST" action="{{ route('admin.customers.unassign-meter', [$customer, $meter]) }}"
                                    onsubmit="return confirmUnassignMeter('{{ $meter->meter_number }}')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center">
                                        <i class="fas fa-unlink mr-1"></i>
                                        Unassign
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Quick Actions for All Meters -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Meter Information Summary -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Meter Summary</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Meters</span>
                                    <span class="font-semibold text-gray-900">{{ $customer->meters->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Active Meters</span>
                                    <span class="font-semibold text-green-600">{{ $customer->meters->where('status', 'assigned')->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Consumption</span>
                                    <span class="font-semibold text-blue-600">
                                        {{ number_format($customer->meters->sum(function($meter) { return $meter->current_reading - $meter->initial_reading; }), 2) }} m³
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Balance</span>
                                    <span class="font-semibold {{ $customer->meters->sum('current_balance') > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        KSh {{ number_format($customer->meters->sum('current_balance'), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>


                        <!-- Quick Actions -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                            <div class="space-y-3">
                                <button onclick="showMeterAssignment()"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center space-x-2 shadow-sm">
                                    <i class="fas fa-plus"></i>
                                    <span>Add Another Meter</span>
                                </button>

                                @if($customer->status === 'active')
                                    <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center space-x-2 shadow-sm">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span>Record Reading</span>
                                    </a>
                                @else
                                    <button class="w-full bg-gray-400 cursor-not-allowed text-white px-4 py-3 rounded-lg flex items-center justify-center space-x-2 shadow-sm"
                                            title="Cannot record reading - Customer status is {{ $customer->status }}">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span>Record Reading</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Administrative Actions -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Administrative</h3>
                            <div class="space-y-3">
                                @if($customer->meters->count() > 1)
                                <form method="POST" action="{{ route('admin.customers.unassign-all-meters', $customer) }}"
                                    onsubmit="return confirmUnassignAllMeters()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center space-x-2 shadow-sm">
                                        <i class="fas fa-unlink"></i>
                                        <span>Unassign All Meters</span>
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('admin.meters.index', ['customer' => $customer->id]) }}"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center space-x-2 shadow-sm">
                                    <i class="fas fa-list"></i>
                                    <span>View All Meters</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- No Meter Assigned State -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-tachometer-alt text-yellow-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Meters Assigned</h3>
                            <p class="text-yellow-700 mb-4">This customer does not have any water meters assigned yet.</p>
                            <div class="flex flex-wrap gap-3">
                                <button onclick="showMeterAssignment()"
                                        class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg transition duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-plus"></i>
                                    <span>Assign Meter</span>
                                </button>
                                <a href="{{ route('admin.meters.available') }}"
                                class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-5 py-2.5 rounded-lg transition duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-search"></i>
                                    <span>Browse Inventory</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Billing History -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                    <h2 class="text-lg md:text-xl font-semibold text-blue-800 flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-2 text-blue-600"></i>
                        Billing History
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $billingStats['total_bills'] }} bills
                    </span>
                </div>

                @if($customer->bills->count() > 0)
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill #</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Period</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Due Date</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->bills as $bill)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-mono font-semibold text-blue-600">{{ $bill->bill_number }}</div>
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-500 hidden sm:table-cell">
                                    @if($bill->billing_period_start && $bill->billing_period_end)
                                        {{ $bill->billing_period_start->format('M d') }} - {{ $bill->billing_period_end->format('M d, Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-900">
                                    {{ number_format($bill->consumption, 2) }} m³
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-semibold text-gray-900">KSh {{ number_format($bill->total_amount, 2) }}</div>
                                    @if($bill->payments->count() > 0)
                                        <div class="text-xs text-green-600">
                                            Paid: KSh {{ number_format($bill->payments->sum('amount'), 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
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
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-500 hidden md:table-cell">
                                    @if($bill->due_date)
                                        <span class="{{ $bill->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $bill->formatted_due_date }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-sm font-medium no-print">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('bills.show', $bill->id) }}"
                                           class="text-blue-600 hover:text-blue-900 transition duration-150 p-1 rounded">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('bills.edit', $bill->id) }}"
                                           class="text-green-600 hover:text-green-900 transition duration-150 p-1 rounded">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-file-invoice text-4xl text-gray-400 mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">No Bills Found</h3>
                    <p class="text-gray-500">No bills have been generated for this customer yet.</p>
                </div>
                @endif
            </div>

            <!-- Reading History -->
            @if($customer->meters->count() > 0)
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                    <h2 class="text-lg md:text-xl font-semibold text-green-700 flex items-center">
                        <i class="fas fa-tachometer-alt mr-2 text-green-600"></i>
                        Meter Reading History
                    </h2>
                </div>

                @if($customer->meterReadings->count() > 0)
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Previous</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Reader</th>
                                <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->meterReadings as $reading)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-medium text-gray-900">{{ $reading->meter->meter_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-medium text-gray-900">{{ $reading->reading_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 hidden sm:block">{{ $reading->reading_period }}</div>
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-900 hidden sm:table-cell">
                                    {{ number_format($reading->previous_reading, 2) }} m³
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm font-medium text-green-600">
                                    {{ number_format($reading->current_reading, 2) }} m³
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-medium text-blue-600">{{ number_format($reading->consumption, 2) }} m³</div>
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $reading->reader->name ?? 'System' }}
                                </td>
                                <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                                    @if($reading->billed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Billed
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Unbilled
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-tachometer-alt text-4xl text-gray-400 mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">No Reading History</h3>
                    <p class="text-gray-500">No meter readings have been recorded for this customer yet.</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-6 md:space-y-8">

            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100">
                <h2 class="text-lg md:text-xl font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                    Customer Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <!-- Personal Information -->
                    <div class="print-break-inside-avoid">
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-id-card mr-2 text-gray-500 text-sm"></i>
                            Personal Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Customer ID</label>
                                <p class="font-medium text-blue-600">{{ $customer->customer_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Full Name</label>
                                <p class="font-medium">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Email</label>
                                <p class="font-medium break-all">{{ $customer->email }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Phone</label>
                                <p class="font-medium">{{ $customer->phone }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">ID Number</label>
                                <p class="font-medium">{{ $customer->id_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">KRA PIN</label>
                                <p class="font-medium">{{ $customer->kra_pin ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Property Information -->
                    <div class="print-break-inside-avoid">
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-home mr-2 text-gray-500 text-sm"></i>
                            Property Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Plot Number</label>
                                <p class="font-medium">{{ $customer->plot_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">House Number</label>
                                <p class="font-medium">{{ $customer->house_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Estate/Area</label>
                                <p class="font-medium">{{ $customer->estate ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Physical Address</label>
                                <p class="font-medium">{{ $customer->physical_address }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Property Owner</label>
                                <p class="font-medium">{{ $customer->property_owner }}</p>
                            </div>
                            <div>
                                <label class="text-xs md:text-sm text-gray-500 block">Expected Users</label>
                                <p class="font-medium">{{ $customer->expected_users ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Notes -->
                @if($customer->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-gray-500 text-sm"></i>
                        Customer Notes
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $customer->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Account Information -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-gray-500 text-sm"></i>
                        Account Information
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <label class="text-gray-500 block">Created On</label>
                            <p class="font-medium">{{ $customer->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-gray-500 block">Last Updated</label>
                            <p class="font-medium">{{ $customer->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Application Documents Section -->
                @if($customer->waterApplication)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                        Application Documents
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- National ID Document -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200 card-hover">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-700 text-sm">National ID</h4>
                                @if($customer->waterApplication->national_id_file)
                                    <span class="bg-green-100 text-green-800 text-xs px-1 py-1 rounded flex items-center">
                                        <i class="fas fa-check mr-1 text-xs"></i>
                                        Uploaded
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs px-1 py-1 rounded flex items-center">
                                        <i class="fas fa-times mr-1 text-xs"></i>
                                        Missing
                                    </span>
                                @endif
                            </div>
                            @if($customer->waterApplication->national_id_file)
                                <div class="flex flex-col space-y-2">
                                    <a href="{{ Storage::url($customer->waterApplication->national_id_file) }}"
                                    target="_blank"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Document
                                    </a>
                                    <a href="{{ Storage::url($customer->waterApplication->national_id_file) }}"
                                    download
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                        <i class="fas fa-download mr-2"></i>
                                        Download
                                    </a>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic text-center py-2">Document not uploaded</p>
                            @endif
                        </div>

                        <!-- KRA Pin Certificate -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200 card-hover">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-700 text-sm">KRA Pin Certificate</h4>
                                @if($customer->waterApplication->kra_pin_file)
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                                        <i class="fas fa-check mr-1 text-xs"></i>
                                        Uploaded
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded flex items-center">
                                        <i class="fas fa-times mr-1 text-xs"></i>
                                        Missing
                                    </span>
                                @endif
                            </div>
                            @if($customer->waterApplication->kra_pin_file)
                                <div class="flex flex-col space-y-2">
                                    <a href="{{ Storage::url($customer->waterApplication->kra_pin_file) }}"
                                    target="_blank"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Document
                                    </a>
                                    <a href="{{ Storage::url($customer->waterApplication->kra_pin_file) }}"
                                    download
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                        <i class="fas fa-download mr-2"></i>
                                        Download
                                    </a>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic text-center py-2">Document not uploaded</p>
                            @endif
                        </div>

                        <!-- Title Document -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200 card-hover">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-700 text-sm">Title Document</h4>
                                @if($customer->waterApplication->title_document)
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                                        <i class="fas fa-check mr-1 text-xs"></i>
                                        Uploaded
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded flex items-center">
                                        <i class="fas fa-times mr-1 text-xs"></i>
                                        Missing
                                    </span>
                                @endif
                            </div>
                            @if($customer->waterApplication->title_document)
                                <div class="flex flex-col space-y-2">
                                    <a href="{{ Storage::url($customer->waterApplication->title_document) }}"
                                    target="_blank"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Document
                                    </a>
                                    <a href="{{ Storage::url($customer->waterApplication->title_document) }}"
                                    download
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                        <i class="fas fa-download mr-2"></i>
                                        Download
                                    </a>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic text-center py-2">Document not uploaded</p>
                            @endif
                        </div>
                    </div>

                    <!-- Document Status Summary -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        @php
                            $uploadedDocs = 0;
                            $totalDocs = 3;
                            if ($customer->waterApplication->national_id_file) $uploadedDocs++;
                            if ($customer->waterApplication->kra_pin_file) $uploadedDocs++;
                            if ($customer->waterApplication->title_document) $uploadedDocs++;
                        @endphp
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Document Completion</span>
                            <span class="text-sm font-semibold {{ $uploadedDocs === $totalDocs ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $uploadedDocs }}/{{ $totalDocs }} documents
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $uploadedDocs === $totalDocs ? 'green' : 'yellow' }}-600 h-2 rounded-full"
                                style="width: {{ ($uploadedDocs / $totalDocs) * 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Application Details -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center">
                                <strong class="text-gray-700 mr-3 w-24">Application ID:</strong>
                                <span class="text-blue-600 font-medium">#WC{{ $customer->waterApplication->id }}</span>
                            </div>
                            <div class="flex items-center">
                                <strong class="text-gray-700 mr-3 w-24">Applied:</strong>
                                <span>{{ $customer->waterApplication->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <strong class="text-gray-700 mr-3 w-24">Status:</strong>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($customer->waterApplication->status) }}
                                </span>
                            </div>
                            <div class="flex items-center">
                                <strong class="text-gray-700 mr-3 w-24">Processor:</strong>
                                <span class="truncate">{{ $customer->waterApplication->processedBy->name ?? 'System' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Show message if no application found -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-sm mr-2 mt-0.5"></i>
                            <div>
                                <h3 class="text-sm font-semibold text-yellow-800">No Application Found</h3>
                                <p class="text-yellow-600 text-xs mt-1">This customer was not created through a water connection application.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Document Upload Section -->
                @if(!$customer->waterApplication || ($customer->waterApplication && (!$customer->waterApplication->national_id_file || !$customer->waterApplication->kra_pin_file)))
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h2 class="text-lg md:text-xl font-semibold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-file-upload mr-2 text-blue-600"></i>
                        Upload Missing Documents
                    </h2>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-sm mr-2 mt-0.5"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-800">Documents Required for Activation</h4>
                                <p class="text-yellow-700 text-xs mt-1">Upload the missing documents to activate this customer account.</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.customers.upload-documents', $customer) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        @if(!$customer->waterApplication || !$customer->waterApplication->national_id_file)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                National ID Document <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="national_id_file"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required>
                            <p class="text-xs text-gray-500 mt-1">Max: 2MB • PDF, JPG, JPEG, PNG</p>
                        </div>
                        @endif

                        @if(!$customer->waterApplication || !$customer->waterApplication->kra_pin_file)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                KRA Pin Certificate <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="kra_pin_file"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required>
                            <p class="text-xs text-gray-500 mt-1">Max: 2MB • PDF, JPG, JPEG, PNG</p>
                        </div>
                        @endif

                        @if(!$customer->waterApplication || !$customer->waterApplication->title_document)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title Document <span class="text-gray-500">(Optional)</span>
                            </label>
                            <input type="file" name="title_document"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                accept=".pdf,.jpg,.jpeg,.png">
                            <p class="text-xs text-gray-500 mt-1">Max: 2MB • PDF, JPG, JPEG, PNG</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Notes</label>
                            <textarea name="upload_notes" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                    placeholder="Any notes about these documents..."></textarea>
                        </div>

                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-upload mr-2"></i>
                            <span>Upload Documents</span>
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 border border-gray-100 no-print">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                    Quick Actions
                </h2>

                <div class="space-y-3">
                    @if($customer->meters->count() > 0)
                    <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}"
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Record Meter Reading
                    </a>
                    @endif

                    <a href="{{ route('bills.index') }}?customer={{ $customer->id }}"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-file-invoice mr-2"></i>
                        View All Bills
                    </a>

                    <a href="{{ route('payments.index') }}?customer={{ $customer->id }}"
                       class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-credit-card mr-2"></i>
                        Record Payment
                    </a>

                    <a href="{{ route('admin.customers.edit', $customer) }}"
                       class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Customer
                    </a>

                    <button onclick="window.print()"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>
                        Print Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Meter Assignment Modal -->
<div id="meterAssignmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Assign Meter to {{ $customer->first_name }}</h3>
            <button onclick="closeMeterModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.customers.assign-meter', $customer) }}" id="meterAssignmentForm">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Meter Category</label>
                    <select id="meterCategorySelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Available Meters</label>
                    <div id="availableMetersList" class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        <p class="text-gray-500 text-sm">Select a category to view available meters</p>
                    </div>

                    <input type="hidden" name="meter_id" id="selectedMeterId" required>
                    <div id="meterSelectionError" class="text-red-500 text-sm mt-1 hidden">
                        Please select a meter from the list above.
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Initial Reading</label>
                        <input type="number" step="0.01" name="initial_reading"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.00" value="0" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Balance B/F</label>
                        <input type="number" step="0.01" name="balance_bf"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.00" value="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Installation Date</label>
                    <input type="date" name="installation_date"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ date('Y-m-d') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Any installation notes..."></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeMeterModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" id="assignMeterButton"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Assign Meter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmUnassignMeter(meterNumber) {
    return confirm(`Are you sure you want to unassign meter ${meterNumber}? This action will disconnect the meter from the customer and may affect billing.`);
}

function confirmUnassignAllMeters() {
    return confirm('Are you sure you want to unassign ALL meters from this customer? This action cannot be undone and will affect all billing for this customer.');
}

// Enhanced meter assignment modal for multiple meters
function showMeterAssignment() {
    document.getElementById('meterAssignmentModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    resetMeterSelection();
}

function closeMeterModal() {
    document.getElementById('meterAssignmentModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    resetMeterSelection();
}

function resetMeterSelection() {
    const metersList = document.getElementById('availableMetersList');
    const meterIdInput = document.getElementById('selectedMeterId');
    const errorDiv = document.getElementById('meterSelectionError');
    const assignButton = document.getElementById('assignMeterButton');

    if (metersList) metersList.innerHTML = '<p class="text-gray-500 text-sm">Select a category to view available meters</p>';
    if (meterIdInput) meterIdInput.value = '';
    if (errorDiv) errorDiv.classList.add('hidden');
    if (assignButton) assignButton.disabled = true;

    // Reset category select
    const categorySelect = document.getElementById('meterCategorySelect');
    if (categorySelect) categorySelect.value = '';
}

// Enhanced meter loading with better error handling
document.getElementById('meterCategorySelect')?.addEventListener('change', function() {
    const categoryId = this.value;
    const metersList = document.getElementById('availableMetersList');
    const meterIdInput = document.getElementById('selectedMeterId');
    const errorDiv = document.getElementById('meterSelectionError');
    const assignButton = document.getElementById('assignMeterButton');

    // Reset selection when category changes
    if (meterIdInput) meterIdInput.value = '';
    if (errorDiv) errorDiv.classList.add('hidden');
    if (assignButton) assignButton.disabled = true;

    if (!categoryId) {
        metersList.innerHTML = '<p class="text-gray-500 text-sm">Select a category to view available meters</p>';
        return;
    }

    metersList.innerHTML = `
        <div class="flex items-center justify-center space-x-2 text-gray-500 py-8">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Loading available meters...</span>
        </div>
    `;

    fetch(`/admin/customers/get-available-meters?category_id=${categoryId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(meters => {
            if (meters.length === 0) {
                metersList.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-inbox text-3xl mb-3 opacity-50"></i>
                        <p class="font-medium">No meters available</p>
                        <p class="text-sm mt-1">No available meters found in this category</p>
                    </div>
                `;
                return;
            }

            metersList.innerHTML = meters.map(meter => `
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 cursor-pointer transition duration-200 meter-option"
                     data-meter-id="${meter.id}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="font-semibold text-gray-900">${meter.meter_number}</div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Available
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-tag text-gray-400"></i>
                                    <span>${meter.meter_type}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-cube text-gray-400"></i>
                                    <span>${meter.meter_model || 'Standard'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">${meter.category_name}</div>
                            <div class="text-xs text-gray-500 mt-1">Init: ${meter.initial_reading}m³</div>
                        </div>
                    </div>
                </div>
            `).join('');

            // Add selection handlers
            document.querySelectorAll('.meter-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selection from all options
                    document.querySelectorAll('.meter-option').forEach(opt => {
                        opt.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
                    });

                    // Add selection to clicked option
                    this.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');

                    // Set the hidden input value and enable submit button
                    const meterId = this.dataset.meterId;
                    document.getElementById('selectedMeterId').value = meterId;
                    document.getElementById('meterSelectionError').classList.add('hidden');
                    document.getElementById('assignMeterButton').disabled = false;
                });
            });
        })
        .catch(error => {
            console.error('Error loading meters:', error);
            metersList.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <i class="fas fa-exclamation-triangle text-2xl mb-3"></i>
                    <p class="font-medium">Error loading meters</p>
                    <p class="text-sm mt-1">Please try again later</p>
                </div>
            `;
        });
});

// Form validation before submission
document.getElementById('meterAssignmentForm')?.addEventListener('submit', function(e) {
    const meterId = document.getElementById('selectedMeterId').value;
    const errorDiv = document.getElementById('meterSelectionError');

    if (!meterId) {
        e.preventDefault();
        errorDiv.classList.remove('hidden');
        errorDiv.textContent = 'Please select a meter from the list above.';
        return false;
    }
});

// Close modal when clicking outside
document.getElementById('meterAssignmentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMeterModal();
    }
});

// Status update form handling
document.getElementById('statusSelect').addEventListener('change', function() {
    const status = this.value;
    const reasonField = document.getElementById('statusReasonField');
    const notesField = document.getElementById('statusNotes');

    // Show/Hide reason field
    if (status) {
        reasonField.classList.remove('hidden');
        notesField.placeholder = "Document any additional notes for this status change...";
    } else {
        reasonField.classList.add('hidden');
        notesField.placeholder = "Document the reason for this status change...";
    }
});

// File upload validation
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (file.size > maxSize) {
                alert('File size must be less than 2MB');
                this.value = '';
            }

            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload PDF, JPG, or PNG files only');
                this.value = '';
            }
        }
    });
});
</script>

<style>
    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .print-break-inside-avoid {
            break-inside: avoid;
        }

        .bg-white {
            background: white !important;
        }

        .fixed {
            display: none !important;
        }

        .shadow-md {
            box-shadow: none !important;
        }

        .border {
            border: 1px solid #e5e7eb !important;
        }
    }
</style>
@endsection
