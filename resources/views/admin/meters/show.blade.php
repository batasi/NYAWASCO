@extends('layouts.app')

@section('title', 'Meter Details - NYAWASCO')

@section('content')
@can('view meters')
<div class="container mx-auto px-4 py-8">
    <!-- Modern Header -->
    @php
    $actionButtons = [
        [
            'text' => 'Back to Meters',
            'href' => url('/admin/meters'),
            'icon' => 'fas fa-arrow-left',
            'color' => 'bg-gradient-to-r from-red-600 to-blue-700 hover:from-gray-700 hover:to-gray-800'
        ]
    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Meter Details',
        'subtitle' => 'Meter Number: ' . $meter->meter_number,
        'actionButtons' => $actionButtons
    ])

    <!-- Modern Grid Layout -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <!-- Basic Information Card -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Meter Overview Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Meter Overview
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-600 uppercase tracking-wide">Meter Number</label>
                            <p class="text-xl font-bold text-gray-900 bg-blue-50 px-3 py-2 rounded-lg">{{ $meter->meter_number }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-600 uppercase tracking-wide">Meter Type</label>
                            <p class="text-xl font-bold text-gray-900 capitalize bg-gray-50 px-3 py-2 rounded-lg">{{ $meter->meter_type }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-600 uppercase tracking-wide">Category</label>
                            <p class="mt-1">
                                @if($meter->meterCategory)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm">
                                        <i class="fas fa-tag mr-2"></i>
                                        {{ $meter->meterCategory->name }}
                                    </span>
                                @else
                                    <span class="text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">Uncategorized</span>
                                @endif
                            </p>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-600 uppercase tracking-wide">Model</label>
                            <p class="text-lg font-semibold text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $meter->meter_model ?? 'Not specified' }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-600 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                @php
                                    $statusColors = [
                                        'available' => 'from-green-500 to-green-600',
                                        'assigned' => 'from-blue-500 to-blue-600',
                                        'faulty' => 'from-red-500 to-red-600',
                                        'maintenance' => 'from-yellow-500 to-orange-500',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r {{ $statusColors[$meter->status] ?? 'from-gray-500 to-gray-600' }} text-white shadow-sm">
                                    <i class="fas fa-circle mr-2 text-xs"></i>
                                    {{ ucfirst($meter->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-600 uppercase tracking-wide">Installation Date</label>
                            <p class="text-lg font-semibold text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">
                                {{ $meter->installation_date ? $meter->installation_date->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Information Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Location Information
                    </h3>
                </div>
                <div class="p-6">
                    @if($meter->customer && $meter->customer->estate)
                        <div class="flex items-start space-x-4 p-4 bg-purple-50 rounded-xl">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-map-marker-alt text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $meter->customer->estate }}</h4>
                                <div class="space-y-2 text-sm text-gray-700">
                                    @if($meter->customer->plot_number)
                                        <div class="flex items-center">
                                            <i class="fas fa-home mr-2 text-purple-500"></i>
                                            <span class="font-medium">Plot:</span>
                                            <span class="ml-1">{{ $meter->customer->plot_number }}</span>
                                        </div>
                                    @endif
                                    @if($meter->customer->house_number)
                                        <div class="flex items-center">
                                            <i class="fas fa-building mr-2 text-purple-500"></i>
                                            <span class="font-medium">House:</span>
                                            <span class="ml-1">{{ $meter->customer->house_number }}</span>
                                        </div>
                                    @endif
                                    @if($meter->customer->physical_address)
                                        <div class="flex items-start">
                                            <i class="fas fa-location-dot mr-2 text-purple-500 mt-1"></i>
                                            <span>{{ $meter->customer->physical_address }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($meter->installation_address)
                        <div class="flex items-start space-x-4 p-4 bg-purple-50 rounded-xl">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-map-marker-alt text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-gray-900 mb-2">Installation Address</h4>
                                <p class="text-gray-700">{{ $meter->installation_address }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-map-marker-alt text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-lg">Location information not available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Cards -->
        <div class="space-y-6">
            <!-- Customer Information Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        Customer Information
                    </h3>
                </div>
                <div class="p-6">
                    @if($meter->customer)
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-user text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-1">
                                {{ $meter->customer->first_name }} {{ $meter->customer->last_name }}
                            </h4>
                            <p class="text-indigo-600 font-semibold mb-3">{{ $meter->customer->customer_number }}</p>
                            <div class="space-y-1 text-sm text-gray-600 mb-4">
                                <p class="flex items-center justify-center">
                                    <i class="fas fa-envelope mr-2 text-indigo-500"></i>
                                    {{ $meter->customer->email }}
                                </p>
                                <p class="flex items-center justify-center">
                                    <i class="fas fa-phone mr-2 text-indigo-500"></i>
                                    {{ $meter->customer->phone }}
                                </p>
                            </div>
                            <a href="{{ url('/admin/customers/' . $meter->customer->id) }}" 
                               class="inline-flex items-center bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                View Customer
                            </a>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-lg mb-2">Not assigned to any customer</p>
                            <p class="text-gray-400 text-sm">This meter is available for assignment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Information Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-chart-line mr-2"></i>
                        Financial Summary
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-gray-50 to-white rounded-xl border">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-money-bill-wave text-white text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Current Balance</span>
                            </div>
                        </div>
                        <span class="text-xl font-bold {{ $meter->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            KSh {{ number_format($meter->current_balance, 2) }}
                        </span>
                    </div>
                    
                    @if($meter->deposit_amount > 0)
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-white rounded-xl border">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-shield-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Security Deposit</span>
                            </div>
                        </div>
                        <span class="text-xl font-bold text-blue-600">
                            KSh {{ number_format($meter->deposit_amount, 2) }}
                        </span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-orange-50 to-white rounded-xl border">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-tools text-white text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Installation Fee</span>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-orange-600">
                            KSh {{ number_format($meter->installation_fee, 2) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-purple-50 to-white rounded-xl border">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-plug text-white text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Connection Fee</span>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-purple-600">
                            KSh {{ number_format($meter->connection_fee, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <!-- Recent Readings Card -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Recent Readings
                </h3>
                @if($meter->customer)
                <a href="{{ url('/admin/meter-readings/create?customer=' . $meter->customer->id) }}" 
                   class="bg-white text-cyan-700 hover:bg-cyan-50 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-plus mr-1"></i> Add Reading
                </a>
                @endif
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($meter->meterReadings as $reading)
                <div class="p-4 hover:bg-cyan-50 transition-colors duration-200">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-cyan-100 to-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-water text-cyan-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($reading->current_reading) }} <span class="text-sm text-gray-500">m³</span></p>
                                <p class="text-sm text-gray-500 flex items-center mt-1">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $reading->reading_date->format('M d, Y') }}
                                </p>
                                @if($reading->consumption > 0)
                                    <p class="text-xs text-green-600 font-semibold mt-1 flex items-center">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        +{{ $reading->consumption }} m³ consumption
                                    </p>
                                @endif
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800">
                            <i class="fas fa-circle mr-1 text-xs"></i>
                            {{ ucfirst($reading->reading_type) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tachometer-alt text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-gray-500 text-lg mb-2">No readings recorded</p>
                    <p class="text-gray-400 text-sm">Start by adding the first meter reading</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Bills Card -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Recent Bills
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($meter->bills as $bill)
                <div class="p-4 hover:bg-amber-50 transition-colors duration-200">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-receipt text-amber-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">KSh {{ number_format($bill->total_amount, 2) }}</p>
                                <p class="text-sm text-gray-500 font-mono">{{ $bill->bill_number }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $bill->billing_period_start?->format('M d') }} - {{ $bill->billing_period_end?->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                            {{ $bill->bill_status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($bill->bill_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            <i class="fas fa-circle mr-1 text-xs"></i>
                            {{ ucfirst($bill->bill_status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-invoice text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-gray-500 text-lg mb-2">No bills generated</p>
                    <p class="text-gray-400 text-sm">Bills will appear after meter readings</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    @if($meter->notes)
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
            <h3 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-sticky-note mr-2"></i>
                Additional Notes
            </h3>
        </div>
        <div class="p-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                <p class="text-gray-800 whitespace-pre-line leading-relaxed">{{ $meter->notes }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endcan
@endsection