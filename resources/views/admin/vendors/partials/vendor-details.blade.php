<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Basic Information -->
    <div class="space-y-4">
        <h4 class="text-lg font-medium text-gray-900">Basic Information</h4>

        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                @if($vendor->avatar)
                <img class="h-16 w-16 rounded-full" src="{{ asset($vendor->avatar) }}" alt="{{ $vendor->name }}">
                @else
                <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                    <span class="text-gray-600 font-medium text-xl">{{ strtoupper(substr($vendor->name, 0, 1)) }}</span>
                </div>
                @endif
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $vendor->business_name ?? $vendor->name }}</h3>
                <p class="text-sm text-gray-500">Contact: {{ $vendor->name }}</p>
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500">Email:</span>
                <span class="text-sm text-gray-900">{{ $vendor->email }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500">Phone:</span>
                <span class="text-sm text-gray-900">{{ $vendor->contact_number ?? $vendor->phone ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500">Status:</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500">Verified:</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendor->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $vendor->is_verified ? 'Verified' : 'Unverified' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Business Details -->
    <div class="space-y-4">
        <h4 class="text-lg font-medium text-gray-900">Business Details</h4>

        <div class="space-y-2">
            @if($vendor->website)
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500">Website:</span>
                <a href="{{ $vendor->website }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-500">{{ $vendor->website }}</a>
            </div>
            @endif

            @if($vendor->services_offered)
            <div>
                <span class="text-sm font-medium text-gray-500">Services Offered:</span>
                <p class="text-sm text-gray-900 mt-1">{{ $vendor->services_offered }}</p>
            </div>
            @endif

            @if($vendor->address || $vendor->city || $vendor->country)
            <div>
                <span class="text-sm font-medium text-gray-500">Address:</span>
                <p class="text-sm text-gray-900 mt-1">
                    {{ $vendor->address ? $vendor->address . ', ' : '' }}
                    {{ $vendor->city ? $vendor->city . ', ' : '' }}
                    {{ $vendor->country ?? '' }}
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Additional Information -->
    @if($vendor->about || $vendor->business_hours)
    <div class="lg:col-span-2 space-y-4">
        <h4 class="text-lg font-medium text-gray-900">Additional Information</h4>

        @if($vendor->about)
        <div>
            <span class="text-sm font-medium text-gray-500">About:</span>
            <p class="text-sm text-gray-900 mt-1">{{ $vendor->about }}</p>
        </div>
        @endif

        @if($vendor->business_hours)
        <div>
            <span class="text-sm font-medium text-gray-500">Business Hours:</span>
            <pre class="text-sm text-gray-900 mt-1 whitespace-pre-wrap">{{ json_encode($vendor->business_hours, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>
    @endif

    <!-- Recent Bookings -->
    @if($vendor->bookings->count() > 0)
    <div class="lg:col-span-2 space-y-4">
        <h4 class="text-lg font-medium text-gray-900">Recent Bookings ({{ $vendor->bookings->count() }})</h4>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="space-y-3">
                @foreach($vendor->bookings as $booking)
                <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-b-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Booking #{{ $booking->id }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' :
                           ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>