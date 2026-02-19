@extends('layouts.app')

@section('title', 'Meter Reading History - ' . $meter->meter_number)

@php
    use Carbon\Carbon;
@endphp

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900">Meter Reading History</h1>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ $meter->meter_number }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                    Viewing detailed reading and billing history for meter
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('meter-readings.create', ['meter' => $meter->id, 'customer' => $customer->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Reading
                </a>
                <a href="{{ route('meter-readings.meter.export-history', $meter->id) }}"
                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 12l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export to Excel
                </a>
                <a href="{{ route('admin.meters.show', $meter->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Meter Details
                </a>
                <a href="{{ route('meter-readings.unread') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Unread
                </a>
            </div>
        </div>

        <!-- Meter Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Customer</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $customer->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $customer->customer_number }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Zone / Route</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $meter->zone->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $meter->walkRoute->name ?? 'No Route' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Category / Type</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $meter->meterCategory->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($meter->meter_type) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Current Balance</p>
                        <p class="text-lg font-semibold {{ $meter->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            KSh {{ number_format($meter->current_balance, 2) }}
                        </p>
                        <p class="text-xs text-gray-500">Initial: {{ number_format($meter->initial_reading, 2) }} m³</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Total Readings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_readings'] }}</p>
                </div>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                    {{ $stats['recorded_readings'] }} Recorded
                </span>
            </div>
            <div class="mt-2 flex gap-2 text-xs">
                <span class="text-yellow-600">{{ $stats['estimated_readings'] }} Estimated</span>
                <span class="text-red-600">{{ $stats['exception_readings'] }} Exceptions</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Total Consumption</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_consumption'], 2) }} m³</p>
                </div>
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                    Avg: {{ number_format($stats['average_consumption'] ?? 0, 2) }} m³
                </span>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                From {{ $stats['first_reading_date'] ? Carbon::parse($stats['first_reading_date'])->format('M Y') : 'N/A' }}
                to {{ $stats['last_reading_date'] ? Carbon::parse($stats['last_reading_date'])->format('M Y') : 'N/A' }}
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Billing Summary</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $billStats['total_bills'] }} Bills</p>
                </div>
                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-medium">
                    KSh {{ number_format($billStats['total_amount'], 0) }}
                </span>
            </div>
            <div class="mt-2 grid grid-cols-3 gap-1 text-xs">
                <div><span class="text-green-600 font-medium">{{ $billStats['paid_bills'] }}</span> Paid</div>
                <div><span class="text-yellow-600 font-medium">{{ $billStats['partial_bills'] }}</span> Partial</div>
                <div><span class="text-red-600 font-medium">{{ $billStats['unpaid_bills'] }}</span> Unpaid</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600">Collection Status</p>
                    <p class="text-2xl font-bold text-gray-900">
                        @php
                            $collectionRate = $billStats['total_amount'] > 0
                                ? ($billStats['total_paid'] / $billStats['total_amount']) * 100
                                : 0;
                        @endphp
                        {{ number_format($collectionRate, 1) }}%
                    </p>
                </div>
                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs font-medium">
                    KSh {{ number_format($billStats['total_balance'], 0) }} Due
                </span>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ min($collectionRate, 100) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Reading History Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Reading History</h2>
            <span class="text-sm text-gray-600">{{ $readings->count() }} records found</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($readings as $reading)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $reading->reading_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $reading->reading_date->format('l') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $reading->reading_period }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($reading->reading_type) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($reading->previous_reading, 2) }} m³
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($reading->current_reading, 2) }} m³
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reading->reading_status === 'recorded')
                                    <span class="text-sm font-medium text-blue-600">
                                        {{ number_format($reading->consumption, 2) }} m³
                                    </span>
                                @elseif($reading->reading_status === 'estimated')
                                    <span class="text-sm font-medium text-yellow-600">
                                        {{ number_format($reading->estimated_consumption, 2) }} m³
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reading->reading_status === 'recorded')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Recorded
                                    </span>
                                @elseif($reading->reading_status === 'exception')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        {{ $reading->exception_type ?? 'Exception' }}
                                    </span>
                                @elseif($reading->reading_status === 'estimated')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        Estimated
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reading->billed && $reading->bill)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        Billed
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $reading->billed_at ? $reading->billed_at->format('d/m/Y') : '' }}
                                    </div>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        Not Billed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($reading->bill)
                                    <div class="text-sm">
                                        <a href="{{ route('bills.show', $reading->bill->id) }}"
                                           class="text-blue-600 hover:text-blue-900 font-medium">
                                            {{ $reading->bill->bill_number }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        KSh {{ number_format($reading->bill->total_amount, 2) }}
                                    </div>
                                    <div class="text-xs">
                                        <span class="{{ $reading->bill->bill_status === 'paid' ? 'text-green-600' : ($reading->bill->bill_status === 'partial' ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ ucfirst($reading->bill->bill_status) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No bill generated</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.meter-readings.edit', $reading->id) }}"
                                       class="text-yellow-600 hover:text-yellow-900"
                                       title="Edit Reading">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    @if($reading->bill)
                                        <a href="{{ route('bills.show', $reading->bill->id) }}"
                                           class="text-purple-600 hover:text-purple-900"
                                           title="View Bill">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2 text-sm">No readings found for this meter</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bills Summary Table (Optional - if you want to show bills separately) -->
    @if($bills->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Bills Summary</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($bills as $bill)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('bills.show', $bill->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ $bill->bill_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $bill->billing_period_start ? $bill->billing_period_start->format('M Y') : '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($bill->consumption, 2) }} m³
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                KSh {{ number_format($bill->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                KSh {{ number_format($bill->paid_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $bill->balance > 0 ? 'text-red-600' : 'text-gray-600' }}">
                                KSh {{ number_format($bill->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $bill->bill_status === 'paid' ? 'bg-green-100 text-green-800' :
                                       ($bill->bill_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($bill->bill_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $bill->due_date ? $bill->due_date->format('d/m/Y') : '' }}
                                @if($bill->is_overdue)
                                    <span class="ml-2 text-xs text-red-600">Overdue</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
