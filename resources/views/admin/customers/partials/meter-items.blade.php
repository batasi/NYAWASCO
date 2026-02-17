@foreach($meters as $meter)
<div class="meter-item border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition duration-200"
     data-meter-id="{{ $meter->id }}">
    <div class="flex justify-between items-start mb-3">
        <div>
            <h4 class="font-semibold text-gray-900 text-lg">{{ $meter->meter_number }}</h4>
            <p class="text-sm text-gray-600">{{ $meter->meterCategory->name ?? 'No Category' }} • {{ ucfirst($meter->meter_type) }}</p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $meter->status === 'active' ? 'bg-green-100 text-green-800' :
                ($meter->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' :
                ($meter->status === 'faulty' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
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

        <a href="{{ route('admin.customers.unassign-reassign-form', [$customer, $meter]) }}"
        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center">
            <i class="fas fa-exchange-alt mr-1"></i>
            Unassign & Reassign
        </a>
    </div>
</div>
@endforeach

@if($meters->isEmpty())
<div class="text-center py-8 border border-dashed border-gray-300 rounded-lg">
    <i class="fas fa-search text-4xl text-gray-400 mb-3"></i>
    <h3 class="text-lg font-medium text-gray-900">No Matching Meters</h3>
    <p class="text-gray-500">No meters match your search criteria. Try adjusting your filters.</p>
</div>
@endif
