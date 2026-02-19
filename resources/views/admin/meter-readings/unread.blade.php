@extends('layouts.app')

@section('title', 'Unread Meters - ' . $monthName)

@php
    use Carbon\Carbon;
@endphp

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Unread Meters</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Meters without readings for <span class="font-semibold text-blue-600">{{ $monthName }}</span>
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('meter-readings.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Reading
                </a>
                <a href="{{ route('meter-readings.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    All Readings
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Unread Meters</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_unread']) }}</p>
                        <p class="text-xs text-gray-500">out of {{ number_format($stats['total_meters']) }} total</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ number_format((1 - $stats['total_unread'] / max($stats['total_meters'], 1)) * 100, 1) }}%
                        </p>
                        <p class="text-xs text-gray-500">meters read this month</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Had Previous Reading</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['with_previous_readings']) }}</p>
                        <p class="text-xs text-gray-500">have history available</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Recent Exceptions</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['with_exceptions']) }}</p>
                        <p class="text-xs text-gray-500">had issues in last 3 months</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Filters</h2>
        </div>
        <form method="GET" action="{{ route('meter-readings.unread') }}" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Month Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Billing Month</label>
                    <select name="month"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @foreach($previousMonths as $value => $label)
                            <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Zone Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                    <select name="zone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ $zoneId == 'all' ? 'selected' : '' }}>All Zones</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ $zoneId == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search meter or customer..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Apply Filters
                    </button>
                    <a href="{{ route('meter-readings.unread') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Unread Meters Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Meter Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Zone
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Reading
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($unreadMeters as $meter)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $meter->meter_number }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($meter->meter_model || $meter->manufacturer)
                                                {{ $meter->meter_model ?? 'N/A' }}
                                                @if($meter->manufacturer)
                                                    ({{ $meter->manufacturer }})
                                                @endif
                                            @else
                                                No model info
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($meter->customer)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $meter->customer->full_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $meter->customer->customer_number }}
                                    </div>
                                    @if($meter->customer->phone)
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $meter->customer->phone }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400 italic">No Customer Assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($meter->zone)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $meter->zone->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">No Zone</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($meter->last_reading_date)
                                    <div class="text-sm text-gray-900">
                                        {{ Carbon::parse($meter->last_reading_date)->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm font-medium">
                                        <span class="{{ $meter->last_reading_status == 'estimated' ? 'text-yellow-600' : 'text-green-600' }}">
                                            {{ number_format($meter->last_reading_value, 2) }} m³
                                        </span>
                                    </div>
                                    @if($meter->last_reading_status == 'estimated')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Estimated
                                        </span>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-500">Initial Reading:</div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($meter->initial_reading, 2) }} m³
                                    </div>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                        New Meter
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">
                                    {{ $meter->meterCategory->name ?? 'N/A' }}
                                </span>
                                @if($meter->meterCategory && $meter->meterCategory->default_rate)
                                    <div class="text-xs text-gray-500">
                                        Rate: {{ number_format($meter->meterCategory->default_rate, 2) }}/m³
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($meter->recent_exceptions > 0)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        {{ $meter->recent_exceptions }} Exception(s)
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        in last 3 months
                                    </div>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Normal
                                    </span>
                                @endif

                                @if($meter->status === 'inactive' || $meter->status === 'damaged')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 block mt-1">
                                        {{ ucfirst($meter->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-2">
                                    <a href="{{ route('meter-readings.create', ['meter' => $meter->id, 'customer' => $meter->customer_id]) }}?reading_date={{ $month }}-01"
                                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900"
                                       title="Add Reading for {{ $monthName }}">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Reading
                                    </a>

                                    <div class="flex space-x-3">
                                        @if($meter->customer)
                                            <a href="{{ route('admin.customers.show', $meter->customer_id) }}"
                                               class="text-gray-600 hover:text-gray-900"
                                               title="View Customer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.meters.show', $meter->id) }}"
                                           class="text-purple-600 hover:text-purple-900"
                                           title="View Meter Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </a>

                                        @if($meter->last_reading_date)
                                            <a href="{{ route('meter-readings.index') }}?meter={{ $meter->id }}"
                                               class="text-green-600 hover:text-green-900"
                                               title="View Reading History">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-4 text-lg font-medium text-gray-900">All caught up!</p>
                                <p class="mt-1 text-sm text-gray-500">All meters have readings for {{ $monthName }}.</p>
                                @if($zoneId !== 'all')
                                    <p class="text-sm text-gray-400 mt-2">Try selecting a different zone or month.</p>
                                @endif

                                @if($stats['total_unread'] == 0)
                                    <div class="mt-6 flex justify-center space-x-4">
                                        <a href="{{ route('admin.meter-readings.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Add New Reading
                                        </a>
                                        <a href="{{ route('admin.meters.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Register New Meter
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($unreadMeters->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $unreadMeters->firstItem() }}</span>
                        to <span class="font-medium">{{ $unreadMeters->lastItem() }}</span>
                        of <span class="font-medium">{{ $unreadMeters->total() }}</span> results
                    </div>
                    <div>
                        {{ $unreadMeters->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>


</div>

<script>
function exportUnreadList() {
    // Get current filters
    const urlParams = new URLSearchParams(window.location.search);
    const month = urlParams.get('month') || '{{ $month }}';
    const zone = urlParams.get('zone') || '{{ $zoneId }}';
    const search = urlParams.get('search') || '';

    // Show loading indicator
    const exportBtn = event.currentTarget;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Exporting...';
    exportBtn.disabled = true;

    // Redirect to export endpoint
    window.location.href = "{{ url('meter-readings.export-unread') }}?month=" + month + "&zone=" + zone + "&search=" + encodeURIComponent(search);

    // Reset button after a short delay (the export will cause page navigation anyway)
    setTimeout(() => {
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
}

// Add keyboard shortcut (Ctrl/Cmd + F to focus search)
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.querySelector('input[name="search"]')?.focus();
    }
});

// Auto-submit form when month or zone changes (optional - uncomment if desired)
/*
document.querySelector('select[name="month"]').addEventListener('change', function() {
    this.form.submit();
});

document.querySelector('select[name="zone"]').addEventListener('change', function() {
    this.form.submit();
});
*/
</script>

<style>
/* Optional: Add smooth transitions */
.hover\:bg-gray-50 {
    transition: background-color 0.15s ease-in-out;
}

/* Pagination styling - if using default Laravel pagination */
.pagination {
    @apply flex space-x-1;
}
.pagination .page-item {
    @apply inline-flex;
}
.pagination .page-link {
    @apply px-3 py-1 rounded border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50;
}
.pagination .active .page-link {
    @apply bg-blue-600 text-white border-blue-600;
}
.pagination .disabled .page-link {
    @apply opacity-50 cursor-not-allowed;
}
</style>
@endsection
