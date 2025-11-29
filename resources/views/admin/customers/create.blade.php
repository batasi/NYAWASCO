@extends('layouts.app')

@section('title', 'Add New Customer - NYAWASCO')

@section('content')
@can('add customers')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800">Add New Customer</h1>
                <p class="text-gray-600 mt-2">Create a new customer account with meter assignment</p>
            </div>
            <a href="{{ route('admin.customers.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Customers
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-white/20">
            <form method="POST" action="{{ route('admin.customers.store') }}" id="customerForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="md:col-span-2">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                            Personal Information
                        </h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('first_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('last_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID Number *</label>
                        <input type="text" name="id_number" value="{{ old('id_number') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('id_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">KRA Pin</label>
                        <input type="text" name="kra_pin" value="{{ old('kra_pin') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('kra_pin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Property Information -->
                    <div class="md:col-span-2 mt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-home mr-2 text-green-600"></i>
                            Property Information
                        </h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Plot Number *</label>
                        <input type="text" name="plot_number" value="{{ old('plot_number') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('plot_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">House Number *</label>
                        <input type="text" name="house_number" value="{{ old('house_number') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('house_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estate/Area</label>
                        <input type="text" name="estate" value="{{ old('estate') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('estate')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property Owner *</label>
                        <input type="text" name="property_owner" value="{{ old('property_owner') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('property_owner')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Physical Address *</label>
                        <textarea name="physical_address" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>{{ old('physical_address') }}</textarea>
                        @error('physical_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Connection Information -->
                    <div class="md:col-span-2 mt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-plug mr-2 text-purple-600"></i>
                            Connection Information
                        </h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Connection Type *</label>
                        <select name="connection_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select Type</option>
                            @foreach(['residential', 'commercial', 'industrial', 'public'] as $type)
                                <option value="{{ $type }}" {{ old('connection_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('connection_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Users</label>
                        <input type="number" name="expected_users" value="{{ old('expected_users') }}" min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('expected_users')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Connection Date</label>
                        <input type="date" name="connection_date" value="{{ old('connection_date', date('Y-m-d')) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('connection_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Initial Status *</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            @foreach(['new', 'active', 'pending_payment', 'sealed', 'terminated'] as $status)
                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Balance Information -->
                    <div class="md:col-span-2 mt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>
                            Balance Information
                        </h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Balance Brought Forward</label>
                        <input type="number" step="0.01" name="balance_bf" value="{{ old('balance_bf', 0) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('balance_bf')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    
                    <!-- Meter Assignment -->
                    <div class="md:col-span-2 mt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-tachometer-alt mr-2 text-orange-600"></i>
                            Meter Assignment (Optional)
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">You can assign a meter now or later</p>
                    </div>

                    <!-- Meter Category Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Meter Category</label>
                        <select id="meterCategorySelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a category to view available meters</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }} - {{ $category->code }} ({{ $category->default_rate }} per m³)
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Available Meters Section -->
                    <div class="md:col-span-2" id="availableMetersSection" style="display: none;">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700">Available Meters</label>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <input type="text" id="meterSearch" placeholder="Search meters..." 
                                        class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-40">
                                    <i class="fas fa-search absolute right-2 top-2 text-gray-400 text-xs"></i>
                                </div>
                                <span id="metersCount" class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded"></span>
                            </div>
                        </div>
                        
                        <div id="availableMetersList" class="border border-gray-200 rounded-lg bg-gray-50 min-h-[200px] max-h-96 overflow-y-auto">
                            <div class="p-4 text-center text-gray-500">
                                <i class="fas fa-tachometer-alt text-2xl mb-2 opacity-50"></i>
                                <p class="text-sm">Select a category to view available meters</p>
                            </div>
                        </div>
                        
                        <!-- Meter Pagination -->
                        <div id="metersPagination" class="mt-2 flex justify-between items-center text-sm text-gray-600 hidden">
                            <button id="prevPage" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-1"></i> Previous
                            </button>
                            <span id="pageInfo" class="text-xs"></span>
                            <button id="nextPage" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                                Next <i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                        
                        <input type="hidden" id="selectedMeterId" name="meter_id">
                    </div>

                    <!-- Selected Meter Info -->
                    <div class="md:col-span-2" id="selectedMeterInfo" style="display: none;">
                        <div class="mt-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex justify-between items-center mb-2">
                                <h5 class="font-medium text-blue-900">Selected Meter</h5>
                                <button type="button" onclick="clearMeterSelection()" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-times mr-1"></i> Change
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600 block text-xs">Meter Number:</span>
                                    <span id="selectedMeterNumber" class="font-semibold text-blue-700"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600 block text-xs">Type:</span>
                                    <span id="selectedMeterType" class="font-semibold"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600 block text-xs">Model:</span>
                                    <span id="selectedMeterModel" class="font-semibold"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600 block text-xs">Category:</span>
                                    <span id="selectedMeterCategory" class="font-semibold"></span>
                                </div>
                                <div class="md:col-span-2 lg:col-span-4">
                                    <span class="text-gray-600 block text-xs">Initial Reading:</span>
                                    <span id="selectedMeterInitial" class="font-semibold text-green-600"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2 mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="4" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.customers.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Create Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentMeters = [];
let currentPage = 1;
const metersPerPage = 10;
let currentSearchTerm = '';

// Load available meters when category changes
document.getElementById('meterCategorySelect').addEventListener('change', function() {
    const categoryId = this.value;
    const metersSection = document.getElementById('availableMetersSection');
    
    if (!categoryId) {
        metersSection.style.display = 'none';
        document.getElementById('selectedMeterInfo').style.display = 'none';
        document.getElementById('selectedMeterId').value = '';
        return;
    }
    
    loadMeters(categoryId, 1, '');
});

// Search meters
document.getElementById('meterSearch').addEventListener('input', function() {
    currentSearchTerm = this.value;
    const categoryId = document.getElementById('meterCategorySelect').value;
    loadMeters(categoryId, 1, currentSearchTerm);
});

// Pagination handlers
document.getElementById('prevPage').addEventListener('click', function() {
    if (currentPage > 1) {
        const categoryId = document.getElementById('meterCategorySelect').value;
        loadMeters(categoryId, currentPage - 1, currentSearchTerm);
    }
});

document.getElementById('nextPage').addEventListener('click', function() {
    const categoryId = document.getElementById('meterCategorySelect').value;
    const totalPages = Math.ceil(currentMeters.length / metersPerPage);
    if (currentPage < totalPages) {
        loadMeters(categoryId, currentPage + 1, currentSearchTerm);
    }
});

function loadMeters(categoryId, page = 1, searchTerm = '') {
    const metersList = document.getElementById('availableMetersList');
    const metersSection = document.getElementById('availableMetersSection');
    
    metersList.innerHTML = `
        <div class="p-6 text-center">
            <div class="flex items-center justify-center space-x-2 text-gray-500">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading available meters...</span>
            </div>
        </div>
    `;
    
    metersSection.style.display = 'block';
    currentPage = page;
    
    // Use absolute URL to avoid any routing issues
    const url = `{{ url('/admin/customers/get-available-meters') }}?category_id=${categoryId}&search=${encodeURIComponent(searchTerm)}`;
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(meters => {
            currentMeters = meters;
            displayMetersPage(meters, page, searchTerm);
        })
        .catch(error => {
            console.error('Error:', error);
            metersList.innerHTML = `
                <div class="p-6 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle text-xl mb-2"></i>
                    <p class="font-medium">Error loading meters</p>
                    <p class="text-sm mt-1">Please try again later</p>
                </div>
            `;
            document.getElementById('metersPagination').classList.add('hidden');
        });
}

function displayMetersPage(meters, page, searchTerm = '') {
    const metersList = document.getElementById('availableMetersList');
    const metersCount = document.getElementById('metersCount');
    const pagination = document.getElementById('metersPagination');
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (meters.length === 0) {
        metersList.innerHTML = `
            <div class="p-6 text-center text-gray-500">
                <i class="fas fa-inbox text-3xl mb-3 opacity-50"></i>
                <p class="font-medium">No meters available</p>
                <p class="text-sm mt-1">
                    ${searchTerm ? 'No meters match your search' : 'No available meters found in this category'}
                </p>
            </div>
        `;
        metersCount.textContent = '0 meters';
        pagination.classList.add('hidden');
        return;
    }
    
    // Calculate pagination
    const startIndex = (page - 1) * metersPerPage;
    const endIndex = Math.min(startIndex + metersPerPage, meters.length);
    const paginatedMeters = meters.slice(startIndex, endIndex);
    const totalPages = Math.ceil(meters.length / metersPerPage);
    
    // Update counters
    metersCount.textContent = `${meters.length} meter${meters.length !== 1 ? 's' : ''}`;
    if (searchTerm) {
        metersCount.textContent += ` matching "${searchTerm}"`;
    }
    
    // Display meters
    metersList.innerHTML = paginatedMeters.map(meter => `
        <div class="border border-gray-200 rounded-lg p-4 m-2 hover:border-blue-300 cursor-pointer transition duration-200 meter-option bg-white" 
             data-meter-id="${meter.id}"
             data-meter-number="${meter.meter_number}"
             data-meter-type="${meter.meter_type}"
             data-meter-model="${meter.meter_model || 'N/A'}"
             data-meter-category="${meter.category_name}"
             data-initial-reading="${meter.initial_reading}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="font-semibold text-gray-900 text-lg">${meter.meter_number}</div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Available
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-tag text-gray-400"></i>
                            <span class="capitalize">${meter.meter_type}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-cube text-gray-400"></i>
                            <span>${meter.meter_model || 'Standard'}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-tachometer-alt text-gray-400"></i>
                            <span>Init: ${meter.initial_reading} m³</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-tags text-gray-400"></i>
                            <span>${meter.category_name}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">${meter.category_name}</div>
                    <div class="text-xs text-gray-500 mt-1">Click to select</div>
                </div>
            </div>
        </div>
    `).join('');
    
    // Add click handlers
    document.querySelectorAll('.meter-option').forEach(option => {
        option.addEventListener('click', function() {
            selectMeter(this);
        });
    });
    
    // Update pagination
    if (totalPages > 1) {
        pageInfo.textContent = `Page ${page} of ${totalPages}`;
        prevBtn.disabled = page === 1;
        nextBtn.disabled = page === totalPages;
        pagination.classList.remove('hidden');
    } else {
        pagination.classList.add('hidden');
    }
}

function selectMeter(optionElement) {
    // Remove previous selection
    document.querySelectorAll('.meter-option').forEach(opt => {
        opt.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
    });
    
    // Add selection to clicked option
    optionElement.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
    
    // Update selected meter info
    const meterId = optionElement.dataset.meterId;
    const meterNumber = optionElement.dataset.meterNumber;
    const meterType = optionElement.dataset.meterType;
    const meterModel = optionElement.dataset.meterModel;
    const meterCategory = optionElement.dataset.meterCategory;
    const initialReading = optionElement.dataset.initialReading;
    
    document.getElementById('selectedMeterId').value = meterId;
    document.getElementById('selectedMeterNumber').textContent = meterNumber;
    document.getElementById('selectedMeterType').textContent = meterType;
    document.getElementById('selectedMeterModel').textContent = meterModel;
    document.getElementById('selectedMeterCategory').textContent = meterCategory;
    document.getElementById('selectedMeterInitial').textContent = initialReading + ' m³';
    
    // Show selected meter info
    document.getElementById('selectedMeterInfo').style.display = 'block';
    
    // Scroll to selected meter info
    document.getElementById('selectedMeterInfo').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'nearest' 
    });
}

function clearMeterSelection() {
    document.getElementById('selectedMeterId').value = '';
    document.getElementById('selectedMeterInfo').style.display = 'none';
    
    // Clear visual selection
    document.querySelectorAll('.meter-option').forEach(opt => {
        opt.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
    });
    
    // Reset to first page
    const categoryId = document.getElementById('meterCategorySelect').value;
    if (categoryId) {
        loadMeters(categoryId, 1, currentSearchTerm);
    }
}

// Handle form submission to ensure meter selection is valid
document.getElementById('customerForm').addEventListener('submit', function(e) {
    const selectedMeterId = document.getElementById('selectedMeterId').value;
    const meterCategorySelected = document.getElementById('meterCategorySelect').value;
    
    // If a category is selected but no meter is chosen, show warning
    if (meterCategorySelected && !selectedMeterId) {
        e.preventDefault();
        alert('Please select a meter from the available options or clear the category selection.');
        document.getElementById('availableMetersSection').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
        return false;
    }
});
</script>

<style>
.meter-option {
    transition: all 0.2s ease;
}

.meter-option:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endcan
@endsection