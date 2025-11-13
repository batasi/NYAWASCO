@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp
@section('title', 'Customer Details - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-blue-700">Customer Profile</h1>
            <p class="text-gray-600">Complete customer information and reading history</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Customers
        </a>
    </div>

    <!-- Customer Information -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <h2 class="text-xl font-semibold text-blue-700 mb-4">Customer Information</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Personal Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Customer ID</label>
                        <p class="font-medium text-blue-600">{{ $customer->customer_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Full Name</label>
                        <p class="font-medium">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email</label>
                        <p class="font-medium">{{ $customer->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Phone</label>
                        <p class="font-medium">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Account Status</label>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Property Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Property Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Plot Number</label>
                        <p class="font-medium">{{ $customer->plot_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">House Number</label>
                        <p class="font-medium">{{ $customer->house_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Estate</label>
                        <p class="font-medium">{{ $customer->estate ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Address</label>
                        <p class="font-medium">{{ $customer->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meter Information -->
    @if($customer->meter)
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <h2 class="text-xl font-semibold text-green-700 mb-4">Meter Information</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Meter Details</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Meter Number</label>
                        <p class="font-medium text-blue-600">{{ $customer->meter->meter_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Meter Type</label>
                        <p class="font-medium capitalize">{{ $customer->meter->meter_type }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Connection Type</label>
                        <p class="font-medium capitalize">{{ $customer->meter->connection_type }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Connection Date</label>
                        <p class="font-medium">{{ $customer->meter->connection_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Current Status</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Current Reading</label>
                        <p class="font-medium text-green-600">{{ number_format($customer->meter->current_reading, 2) }} m³</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Initial Reading</label>
                        <p class="font-medium">{{ number_format($customer->meter->initial_reading, 2) }} m³</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Total Consumption</label>
                        <p class="font-medium text-blue-600">{{ number_format($customer->meter->current_reading - $customer->meter->initial_reading, 2) }} m³</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Meter Status</label>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                            Active
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($customer->meter->notes)
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-700 mb-2">Installation Notes</h4>
            <p class="text-gray-600">{{ $customer->meter->notes }}</p>
        </div>
        @endif
    </div>
    @else
    <div class="bg-yellow-50/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-yellow-200">
        <div class="flex items-center">
            <svg class="w-8 h-8 text-yellow-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-yellow-800">No Meter Assigned</h3>
                <p class="text-yellow-600">This customer does not have a water meter assigned yet.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Reading Statistics -->
    @if($customer->meter && $readingStats['total_readings'] > 0)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-blue-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">Total Readings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $readingStats['total_readings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-green-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-600">Last Reading</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($readingStats['last_reading']->current_reading, 2) }} m³</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-purple-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-600">Avg Consumption</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($readingStats['average_consumption'], 2) }} m³</p>
                </div>
            </div>
        </div>

        <div class="bg-orange-50/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-orange-200">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-orange-600">Total Consumption</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($readingStats['total_consumption'], 2) }} m³</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Reading History -->
    @if($customer->meter)
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-blue-700">Meter Reading History</h2>
            <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Record New Reading
            </a>
        </div>

        @if($customer->meterReadings->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reading Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Reading</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Reading</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reader</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-gray-200">
                    @foreach($customer->meterReadings as $reading)
                    <tr class="hover:bg-gray-50/50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $reading->reading_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $reading->reading_date->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $reading->reading_period }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($reading->previous_reading, 2) }} m³</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-green-600">{{ number_format($reading->current_reading, 2) }} m³</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-blue-600">{{ number_format($reading->consumption, 2) }} m³</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $reading->reader->name ?? 'System' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reading->reading_image)
                            <a href="{{ Storage::url($reading->reading_image) }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">No photo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reading->billed)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                Billed
                            </span>
                            @else
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
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
            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No Reading History</h3>
            <p class="mt-2 text-gray-500">No meter readings have been recorded for this customer yet.</p>
            <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}" 
               class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-200">
                Record First Reading
            </a>
        </div>
        @endif
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Actions</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.customers.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Customers
            </a>
            
            @if($customer->meter)
            <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Record Meter Reading
            </a>
            @endif
            
            <button onclick="window.print()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Profile
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    .bg-white\/80, .backdrop-blur-sm {
        background: white !important;
        backdrop-filter: none !important;
    }
    .fixed {
        display: none !important;
    }
    .flex.justify-between.items-center.mb-8 .bg-gray-500 {
        display: none !important;
    }
    .bg-white\/80.backdrop-blur-sm.rounded-2xl.shadow-lg.p-6.border.border-white\/20:last-child {
        display: none !important;
    }
}
</style>
@endsection