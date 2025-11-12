@extends('layouts.app')

@section('title', 'Add New Customer - NYAWASCO')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-700">Add New Customer</h1>
            <a href="{{ route('admin.customers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                Back to Customers
            </a>
        </div>

        @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        There were {{ $errors->count() }} errors with your submission
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                
                <!-- Customer Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-blue-700 mb-4 border-b pb-2">Customer Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" name="first_name" id="first_name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   value="{{ old('first_name') }}">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   value="{{ old('last_name') }}">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" id="email" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   value="{{ old('email') }}">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" name="phone" id="phone" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   value="{{ old('phone') }}">
                        </div>
                        
                        <div>
                            <label for="id_number" class="block text-sm font-medium text-gray-700 mb-2">ID Number *</label>
                            <input type="text" name="id_number" id="id_number" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   value="{{ old('id_number') }}">
                        </div>
                        
                        <div>
                            <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-2">Connection Type *</label>
                            <select name="connection_type" id="connection_type" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                <option value="">Select Connection Type</option>
                                <option value="residential" {{ old('connection_type') == 'residential' ? 'selected' : '' }}>Residential</option>
                                <option value="commercial" {{ old('connection_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="industrial" {{ old('connection_type') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                                <option value="public" {{ old('connection_type') == 'public' ? 'selected' : '' }}>Public Institution</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="physical_address" class="block text-sm font-medium text-gray-700 mb-2">Physical Address *</label>
                        <textarea name="physical_address" id="physical_address" required rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">{{ old('physical_address') }}</textarea>
                    </div>
                </div>

                <!-- Meter Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-blue-700 mb-4 border-b pb-2">Meter Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="meter_number" class="block text-sm font-medium text-gray-700 mb-2">Meter Number *</label>
                            <input type="text" name="meter_number" id="meter_number" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   value="{{ old('meter_number') }}"
                                   placeholder="Will be auto-generated if left empty">
                        </div>
                        
                        <div>
                            <label for="meter_type" class="block text-sm font-medium text-gray-700 mb-2">Meter Type *</label>
                            <select name="meter_type" id="meter_type" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                <option value="">Select Meter Type</option>
                                <option value="domestic" {{ old('meter_type') == 'domestic' ? 'selected' : '' }}>Domestic</option>
                                <option value="commercial" {{ old('meter_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="industrial" {{ old('meter_type') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="connection_date" class="block text-sm font-medium text-gray-700 mb-2">Connection Date *</label>
                            <input type="date" name="connection_date" id="connection_date" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   value="{{ old('connection_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.customers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-200">
                        Create Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate meter number if empty
    const meterNumberInput = document.getElementById('meter_number');
    if (meterNumberInput && !meterNumberInput.value) {
        meterNumberInput.placeholder = 'MTR' + new Date().toISOString().slice(0,10).replace(/-/g, '') + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    }
});
</script>
@endsection