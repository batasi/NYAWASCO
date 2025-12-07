@extends('layouts.app')

@section('title', 'Meter Reading Exceptions - NYAWASCO')

@section('content')
@can('view meter-readings')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    @include('components.dashboard-header',[
        'title' => 'Meter Reading Exceptions',
        'subtitle' => 'View and manage exceptional meter readings',
        'actionButtons' => []
    ])

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['total'] }}</div>
                <div class="text-gray-700 font-medium">Total Exceptions</div>
                <div class="text-xs text-gray-500">All time</div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-3xl font-bold text-red-600 mb-2">{{ $stats['inaccessible'] }}</div>
                <div class="text-gray-700 font-medium">Inaccessible</div>
                <div class="text-xs text-gray-500">Gates closed/locked</div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-3xl font-bold text-orange-600 mb-2">{{ $stats['faulty'] }}</div>
                <div class="text-gray-700 font-medium">Faulty Meters</div>
                <div class="text-xs text-gray-500">Not working</div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $stats['stuck'] }}</div>
                <div class="text-gray-700 font-medium">Stuck Meters</div>
                <div class="text-xs text-gray-500">Not moving</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Exception Type</label>
                    <select name="exception_type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">All Types</option>
                        <option value="inaccessible" {{ request('exception_type') == 'inaccessible' ? 'selected' : '' }}>Inaccessible</option>
                        <option value="faulty" {{ request('exception_type') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                        <option value="stuck" {{ request('exception_type') == 'stuck' ? 'selected' : '' }}>Stuck</option>
                        <option value="damaged" {{ request('exception_type') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="vandalized" {{ request('exception_type') == 'vandalized' ? 'selected' : '' }}>Vandalized</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Start Date">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg w-full">
                        Filter
                    </button>
                    @if(request()->anyFilled(['exception_type', 'start_date']))
                    <a href="{{ route('admin.meter-readings.exceptions') }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Exceptions Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exception Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reader</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($exceptions as $reading)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $reading->reading_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $reading->reading_period }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.customers.show', $reading->customer) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $reading->customer->first_name }} {{ $reading->customer->last_name }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $reading->customer->customer_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $reading->meter->meter_number }}</div>
                                <div class="text-xs text-gray-500">{{ $reading->meter->meterCategory->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $reading->exception_type == 'inaccessible' ? 'bg-red-100 text-red-800' : 
                                       $reading->exception_type == 'faulty' ? 'bg-orange-100 text-orange-800' : 
                                       $reading->exception_type == 'stuck' ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-gray-100 text-gray-800' }}">
                                    {{ $reading->exception_type_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($reading->exception_reason, 50) }}</div>
                                @if($reading->exception_evidence)
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-image mr-1"></i>Evidence attached
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $reading->reader->name ?? 'System' }}</div>
                                <div class="text-xs text-gray-500">{{ $reading->created_at->format('M d, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.customers.show', $reading->customer) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($reading->exception_evidence)
                                <a href="{{ Storage::url($reading->exception_evidence) }}" target="_blank" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-image"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No exceptions found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $exceptions->links() }}
            </div>
        </div>
    </div>
</div>
@endcan
@endsection