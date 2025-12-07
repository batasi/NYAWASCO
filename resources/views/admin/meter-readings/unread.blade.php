{{-- Create: views/admin/meter-readings/unread.blade.php --}}
@extends('layouts.app')

@section('title', 'Unread Meters - NYAWASCO')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    @php
    $actionButtons = [];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Unread Meters',
        'subtitle' => 'Meters not read in selected reading period',
        'actionButtons' => $actionButtons
    ])

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Period Selection -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Reading Period</h3>
            <form method="GET" action="{{ route('admin.meter-readings.unread') }}" class="flex gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reading Period</label>
                    <select name="reading_period" class="w-64 border border-gray-300 rounded-lg px-3 py-2">
                        @foreach($readingPeriods as $period)
                        <option value="{{ $period }}" {{ $selectedPeriod == $period ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($period)->format('F Y') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        @if($selectedPeriod)
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-2xl font-bold text-blue-600 mb-2">{{ $totalMeters }}</div>
                <div class="text-sm text-gray-600">Total Meters</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-2xl font-bold text-green-600 mb-2">{{ $readMeters }}</div>
                <div class="text-sm text-gray-600">Read Meters</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-2xl font-bold text-red-600 mb-2">{{ $unreadMeters }}</div>
                <div class="text-sm text-gray-600">Unread Meters</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-2xl font-bold text-yellow-600 mb-2">{{ $exceptionMeters }}</div>
                <div class="text-sm text-gray-600">Exception Readings</div>
            </div>
        </div>

        <!-- Unread Meters Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    Unread Meters for {{ \Carbon\Carbon::parse($selectedPeriod)->format('F Y') }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Click on any meter to record a reading with exception reason
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Reading</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($unreadMetersList as $meter)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $meter->customer->first_name }} {{ $meter->customer->last_name }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $meter->customer->customer_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $meter->meter_number }}</div>
                                <div class="text-sm text-gray-500">{{ $meter->meterCategory->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $meter->customer->plot_number }}, {{ $meter->customer->house_number }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $meter->customer->estate }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($meter->latestReading)
                                <div class="text-sm text-gray-900">
                                    {{ number_format($meter->latestReading->current_reading, 2) }} m³
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $meter->latestReading->reading_date->format('M d, Y') }}
                                </div>
                                @else
                                <div class="text-sm text-gray-500">No reading</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $lastReading = $meter->meterReadings()
                                        ->where('reading_period', $selectedPeriod)
                                        ->first();
                                @endphp
                                @if($lastReading)
                                    @if($lastReading->isException())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $lastReading->exception_type_text }}
                                    </span>
                                    @elseif($lastReading->isEstimated())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-calculator mr-1"></i>
                                        Estimated
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Read
                                    </span>
                                    @endif
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Not Read
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.meter-readings.create', ['customer' => $meter->customer->id, 'meter' => $meter->id, 'period' => $selectedPeriod]) }}"
                                   class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded transition duration-200">
                                    <i class="fas fa-edit"></i> Record Reading
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-check-circle text-4xl mb-3"></i>
                                    <p class="text-lg font-medium text-gray-900">All meters read!</p>
                                    <p class="text-gray-500">All meters have been read for {{ \Carbon\Carbon::parse($selectedPeriod)->format('F Y') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection