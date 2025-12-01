@extends('layouts.app')

@section('title', 'Add New Customer - NYAWASCO')

@section('content')
@can('add customers')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800">Add New Customer</h1>
                <p class="text-gray-600 mt-2">Create a new customer account with comprehensive details</p>
            </div>
            <a href="{{ route('admin.customers.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Customers
            </a>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">1</div>
                    <span class="font-medium">Personal Info</span>
                </div>
                <div class="h-1 w-16 bg-gray-300"></div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
                    <span class="font-medium text-gray-500">Property Info</span>
                </div>
                <div class="h-1 w-16 bg-gray-300"></div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                    <span class="font-medium text-gray-500">Meter Setup</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-white/20">
            <form method="POST" action="{{ route('admin.customers.store') }}" id="customerForm">
                @csrf

                <!-- Personal Information Section -->
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user-circle mr-3 text-blue-600 text-xl"></i>
                        Personal Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required
                                   placeholder="Enter first name">
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name
                            </label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   placeholder="Enter last name">
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required
                                   placeholder="customer@example.com">
                            @error('email')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">+254</span>
                                </div>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="w-full border border-gray-300 rounded-lg pl-16 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                       required
                                       placeholder="700 123 456">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- ID Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                National ID Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="id_number" value="{{ old('id_number') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required
                                   placeholder="Enter ID number">
                            @error('id_number')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- KRA PIN -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                KRA PIN
                            </label>
                            <input type="text" name="kra_pin" value="{{ old('kra_pin') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   placeholder="A123456789X">
                            @error('kra_pin')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Property Information Section -->
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-home mr-3 text-green-600 text-xl"></i>
                        Property Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Plot Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Plot Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="plot_number" value="{{ old('plot_number') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required
                                   placeholder="Enter plot number">
                            @error('plot_number')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- House Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                House Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="house_number" value="{{ old('house_number') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required
                                   placeholder="Enter house number">
                            @error('house_number')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estate/Area -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Estate/Area
                            </label>
                            <input type="text" name="estate" value="{{ old('estate') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   placeholder="Enter estate name">
                            @error('estate')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Property Owner -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Property Owner <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="property_owner" value="{{ old('property_owner') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required
                                   placeholder="Name of property owner">
                            @error('property_owner')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expected Users -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Expected Users
                            </label>
                            <input type="number" name="expected_users" value="{{ old('expected_users', 1) }}" min="1" max="1000"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   placeholder="Estimated number of users">
                            @error('expected_users')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Zone Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Zone
                            </label>
                            <select name="zone_id" id="zoneSelect"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                <option value="">Select Zone</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zone_id')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Walk Route (Dependent on Zone) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Walk Route
                            </label>
                            <select name="walk_route_id" id="walkRouteSelect"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                <option value="">Select Walk Route</option>
                                @foreach($walkRoutes as $route)
                                    <option value="{{ $route->id }}"
                                            data-zone="{{ $route->zone_id }}"
                                            {{ old('walk_route_id') == $route->id ? 'selected' : '' }}>
                                        {{ $route->name }} ({{ $route->zone->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('walk_route_id')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Physical Address -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Physical Address <span class="text-red-500">*</span>
                            </label>
                            <textarea name="physical_address" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                                      required
                                      placeholder="Full physical address including landmarks">{{ old('physical_address') }}</textarea>
                            @error('physical_address')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Meter Setup Section -->
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-tachometer-alt mr-3 text-orange-600 text-xl"></i>
                        Meter Setup
                        <span class="ml-2 text-sm font-normal text-gray-500">(Optional)</span>
                    </h2>

                    <!-- Meter Selection Tabs -->
                    <div class="mb-6">
                        <div class="flex space-x-4 mb-4">
                            <button type="button" id="assignExistingMeterTab"
                                    class="px-4 py-2 rounded-lg font-medium transition duration-200 bg-blue-600 text-white">
                                <i class="fas fa-check-circle mr-2"></i>
                                Assign Existing Meter
                            </button>
                            <button type="button" id="createNewMeterTab"
                                    class="px-4 py-2 rounded-lg font-medium transition duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Create New Meter
                            </button>
                        </div>
                    </div>

                    <!-- Assign Existing Meter Section -->
                    <div id="assignExistingMeterSection">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <!-- Meter Category Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Meter Category
                                </label>
                                <select id="meterCategorySelect"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                            data-fees='@json($category)'>
                                        {{ $category->name }} ({{ $category->code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category Details Preview -->
                            <div id="categoryDetailsPreview" class="hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="font-medium text-blue-900 mb-2">Category Details</h4>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-600">Installation:</span>
                                            <span id="previewInstallationFee" class="font-semibold text-blue-700 ml-2"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Connection:</span>
                                            <span id="previewConnectionFee" class="font-semibold text-blue-700 ml-2"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Deposit:</span>
                                            <span id="previewDeposit" class="font-semibold text-blue-700 ml-2"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Rate:</span>
                                            <span id="previewRate" class="font-semibold text-blue-700 ml-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Available Meters Section -->
                        <div id="availableMetersSection" class="hidden">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Available Meters</h3>
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <input type="text" id="meterSearch" placeholder="Search by meter number..."
                                            class="text-sm border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute right-3 top-2.5 text-gray-400 text-sm"></i>
                                    </div>
                                </div>
                            </div>

                            <div id="availableMetersList" class="border border-gray-200 rounded-lg bg-gray-50 min-h-[150px] max-h-80 overflow-y-auto">
                                <div class="p-6 text-center text-gray-500">
                                    <i class="fas fa-tachometer-alt text-3xl mb-3 opacity-50"></i>
                                    <p class="text-sm">Select a category to view available meters</p>
                                </div>
                            </div>

                            <input type="hidden" id="selectedMeterId" name="meter_id">
                        </div>
                    </div>

                    <!-- Create New Meter Section -->
                    <div id="createNewMeterSection" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Meter Category for New Meter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Meter Category <span class="text-red-500">*</span>
                                </label>
                                <select name="meter_category_id" id="newMeterCategory"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }} ({{ $category->code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Meter Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Meter Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="meter_number" value="{{ old('meter_number') }}"
                                           id="newMeterNumber"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                           placeholder="Enter meter number">
                                    <div id="meterNumberStatus" class="absolute right-3 top-3 hidden">
                                        <i class="fas fa-check text-green-500"></i>
                                    </div>
                                </div>
                                <div id="meterNumberError" class="text-red-500 text-xs mt-2 hidden"></div>
                            </div>

                            <!-- Meter Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Meter Type
                                </label>
                                <select name="meter_type"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    <option value="domestic">Domestic</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="industrial">Industrial</option>
                                </select>
                            </div>

                            <!-- Initial Reading -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Initial Reading (m³)
                                </label>
                                <input type="number" name="initial_reading" value="{{ old('initial_reading', 0) }}" step="0.01" min="0"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                       placeholder="0.00">
                            </div>

                            <!-- Meter Model -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Meter Model
                                </label>
                                <input type="text" name="meter_model" value="{{ old('meter_model') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                       placeholder="e.g., Smart Meter 2000">
                            </div>

                            <!-- Manufacturer -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Manufacturer
                                </label>
                                <input type="text" name="manufacturer" value="{{ old('manufacturer') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                       placeholder="e.g., Siemens, Itron">
                            </div>
                        </div>
                    </div>

                    <!-- Selected Meter Info -->
                    <div id="selectedMeterInfo" class="mt-4 hidden">
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium text-green-900 text-lg">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Meter Selected
                                </h5>
                                <button type="button" onclick="clearMeterSelection()"
                                        class="text-green-700 hover:text-green-900 text-sm font-medium">
                                    <i class="fas fa-times mr-1"></i> Change Selection
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="text-center">
                                    <div class="text-gray-600 mb-1">Meter Number</div>
                                    <div id="selectedMeterNumber" class="font-bold text-xl text-blue-700"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-gray-600 mb-1">Category</div>
                                    <div id="selectedMeterCategory" class="font-semibold text-green-700"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-gray-600 mb-1">Initial Reading</div>
                                    <div id="selectedMeterInitial" class="font-semibold text-orange-600"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Information Section -->
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-money-bill-wave mr-3 text-green-600 text-xl"></i>
                        Financial Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Balance B/F -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Balance Brought Forward (KES)
                            </label>
                            <input type="number" name="balance_bf" value="{{ old('balance_bf', 0) }}" step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   placeholder="0.00">
                            @error('balance_bf')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fee Payment Options -->
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Fee Payment Status</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="installation_fee_paid" id="installation_fee_paid" value="1"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="installation_fee_paid" class="ml-2 block text-sm text-gray-700">
                                        Installation Fee Paid
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="connection_fee_paid" id="connection_fee_paid" value="1"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="connection_fee_paid" class="ml-2 block text-sm text-gray-700">
                                        Connection Fee Paid
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Generate Initial Bill Option -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="generate_initial_bill" id="generate_initial_bill" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="generate_initial_bill" class="ml-2 block text-sm text-gray-700">
                                    Generate Initial Connection Bill
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 ml-6">
                                Create an initial bill for installation, connection, and deposit fees
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Account Information Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-cog mr-3 text-purple-600 text-xl"></i>
                        Account Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Account Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    required>
                                @foreach(['pending', 'active', 'inactive', 'suspended'] as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Reason -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status Reason
                            </label>
                            <input type="text" name="status_reason" value="{{ old('status_reason') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   placeholder="Reason for the selected status">
                            @error('status_reason')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes
                            </label>
                            <textarea name="notes" rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                                      placeholder="Any additional information about this customer...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-8 border-t border-gray-200">
                    <div>
                        <button type="button" onclick="window.history.back()"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                    </div>
                    <div class="flex space-x-4">
                        <button type="button" onclick="saveAsDraft()"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Save as Draft
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create Customer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Spinner Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 flex flex-col items-center space-y-4">
        <i class="fas fa-spinner fa-spin text-blue-600 text-3xl"></i>
        <p class="text-gray-700 font-medium">Processing...</p>
        <p class="text-sm text-gray-500">Creating customer account</p>
    </div>
</div>

<script>
// Tab switching functionality
document.getElementById('assignExistingMeterTab').addEventListener('click', function() {
    activateTab('assignExistingMeter');
});

document.getElementById('createNewMeterTab').addEventListener('click', function() {
    activateTab('createNewMeter');
});

function activateTab(tabName) {
    // Update tab buttons
    if (tabName === 'assignExistingMeter') {
        document.getElementById('assignExistingMeterTab').classList.remove('bg-gray-200', 'text-gray-700');
        document.getElementById('assignExistingMeterTab').classList.add('bg-blue-600', 'text-white');
        document.getElementById('createNewMeterTab').classList.remove('bg-blue-600', 'text-white');
        document.getElementById('createNewMeterTab').classList.add('bg-gray-200', 'text-gray-700');

        // Show/hide sections
        document.getElementById('assignExistingMeterSection').classList.remove('hidden');
        document.getElementById('createNewMeterSection').classList.add('hidden');

        // Clear new meter inputs
        document.getElementById('newMeterCategory').value = '';
        document.getElementById('newMeterNumber').value = '';
    } else {
        document.getElementById('createNewMeterTab').classList.remove('bg-gray-200', 'text-gray-700');
        document.getElementById('createNewMeterTab').classList.add('bg-blue-600', 'text-white');
        document.getElementById('assignExistingMeterTab').classList.remove('bg-blue-600', 'text-white');
        document.getElementById('assignExistingMeterTab').classList.add('bg-gray-200', 'text-gray-700');

        // Show/hide sections
        document.getElementById('createNewMeterSection').classList.remove('hidden');
        document.getElementById('assignExistingMeterSection').classList.add('hidden');

        // Clear existing meter selection
        clearMeterSelection();
    }
}

// Zone-Walk Route dependency
document.getElementById('zoneSelect').addEventListener('change', function() {
    const zoneId = this.value;
    const walkRouteSelect = document.getElementById('walkRouteSelect');
    const options = walkRouteSelect.querySelectorAll('option');

    // Show all options first
    options.forEach(option => {
        option.style.display = 'block';
    });

    // Hide options that don't belong to selected zone
    if (zoneId) {
        options.forEach(option => {
            if (option.value && option.dataset.zone !== zoneId) {
                option.style.display = 'none';
            }
        });

        // Reset selection if current selection is not in the zone
        const currentValue = walkRouteSelect.value;
        if (currentValue) {
            const currentOption = walkRouteSelect.querySelector(`option[value="${currentValue}"]`);
            if (currentOption && currentOption.dataset.zone !== zoneId) {
                walkRouteSelect.value = '';
            }
        }
    }
});

// Meter category selection
document.getElementById('meterCategorySelect').addEventListener('change', function() {
    const categoryId = this.value;
    const previewDiv = document.getElementById('categoryDetailsPreview');
    const metersSection = document.getElementById('availableMetersSection');

    if (!categoryId) {
        previewDiv.classList.add('hidden');
        metersSection.classList.add('hidden');
        return;
    }

    // Get category details from data attribute
    const selectedOption = this.options[this.selectedIndex];
    const categoryData = JSON.parse(selectedOption.dataset.fees);

    // Update preview
    document.getElementById('previewInstallationFee').textContent = 'KES ' + formatNumber(categoryData.installation_fee);
    document.getElementById('previewConnectionFee').textContent = 'KES ' + formatNumber(categoryData.connection_fee);
    document.getElementById('previewDeposit').textContent = 'KES ' + formatNumber(categoryData.deposit_amount);
    document.getElementById('previewRate').textContent = 'KES ' + formatNumber(categoryData.default_rate) + '/m³';

    previewDiv.classList.remove('hidden');

    // Load available meters
    loadAvailableMeters(categoryId);
    metersSection.classList.remove('hidden');
});

// New meter number availability check
let meterCheckTimeout;
document.getElementById('newMeterNumber').addEventListener('input', function() {
    const meterNumber = this.value.trim();
    const statusDiv = document.getElementById('meterNumberStatus');
    const errorDiv = document.getElementById('meterNumberError');

    if (meterCheckTimeout) {
        clearTimeout(meterCheckTimeout);
    }

    if (!meterNumber) {
        statusDiv.classList.add('hidden');
        errorDiv.classList.add('hidden');
        return;
    }

    meterCheckTimeout = setTimeout(() => {
        checkMeterAvailability(meterNumber);
    }, 500);
});

function checkMeterAvailability(meterNumber) {
    const statusDiv = document.getElementById('meterNumberStatus');
    const errorDiv = document.getElementById('meterNumberError');

    statusDiv.classList.remove('hidden');
    statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500"></i>';

    fetch(`{{ route('admin.customers.check-meter-availability') }}?meter_number=${encodeURIComponent(meterNumber)}`)
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                statusDiv.innerHTML = '<i class="fas fa-check text-green-500"></i>';
                errorDiv.classList.add('hidden');
            } else {
                statusDiv.innerHTML = '<i class="fas fa-times text-red-500"></i>';
                errorDiv.textContent = data.message;
                errorDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error checking meter availability:', error);
            statusDiv.classList.add('hidden');
        });
}

// Load available meters
function loadAvailableMeters(categoryId) {
    const metersList = document.getElementById('availableMetersList');

    metersList.innerHTML = `
        <div class="p-6 text-center">
            <div class="flex items-center justify-center space-x-2 text-gray-500">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading available meters...</span>
            </div>
        </div>
    `;

    fetch(`{{ url('/admin/customers/get-available-meters') }}?category_id=${categoryId}`)
        .then(response => response.json())
        .then(meters => {
            if (meters.length === 0) {
                metersList.innerHTML = `
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-3 opacity-50"></i>
                        <p class="font-medium">No meters available</p>
                        <p class="text-sm mt-1">No available meters found in this category</p>
                    </div>
                `;
                return;
            }

            metersList.innerHTML = meters.map(meter => `
                <div class="border-b border-gray-200 last:border-b-0">
                    <div class="p-4 hover:bg-blue-50 cursor-pointer transition duration-200 meter-option"
                         data-meter-id="${meter.id}"
                         data-meter-number="${meter.meter_number}"
                         data-meter-type="${meter.meter_type}"
                         data-meter-category="${meter.category_name}"
                         data-initial-reading="${meter.initial_reading}">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-1">
                                    <div class="font-semibold text-gray-900">${meter.meter_number}</div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Available
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="capitalize">${meter.meter_type}</span> •
                                    <span>${meter.meter_model || 'Standard'}</span> •
                                    <span>Initial: ${meter.initial_reading} m³</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Select <i class="fas fa-chevron-right ml-1"></i>
                                </button>
                            </div>
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
        })
        .catch(error => {
            console.error('Error loading meters:', error);
            metersList.innerHTML = `
                <div class="p-6 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle text-xl mb-2"></i>
                    <p class="font-medium">Error loading meters</p>
                    <p class="text-sm mt-1">Please try again later</p>
                </div>
            `;
        });
}

// Meter search
document.getElementById('meterSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const meterOptions = document.querySelectorAll('.meter-option');

    meterOptions.forEach(option => {
        const meterNumber = option.dataset.meterNumber.toLowerCase();
        if (meterNumber.includes(searchTerm)) {
            option.parentElement.style.display = 'block';
        } else {
            option.parentElement.style.display = 'none';
        }
    });
});

function selectMeter(optionElement) {
    const meterId = optionElement.dataset.meterId;
    const meterNumber = optionElement.dataset.meterNumber;
    const meterCategory = optionElement.dataset.meterCategory;
    const initialReading = optionElement.dataset.initialReading;

    // Update hidden input
    document.getElementById('selectedMeterId').value = meterId;

    // Update display
    document.getElementById('selectedMeterNumber').textContent = meterNumber;
    document.getElementById('selectedMeterCategory').textContent = meterCategory;
    document.getElementById('selectedMeterInitial').textContent = initialReading + ' m³';

    // Show selected meter info
    document.getElementById('selectedMeterInfo').classList.remove('hidden');
}

function clearMeterSelection() {
    document.getElementById('selectedMeterId').value = '';
    document.getElementById('selectedMeterInfo').classList.add('hidden');
}

// Form submission
document.getElementById('customerForm').addEventListener('submit', function(e) {
    // Show loading modal
    document.getElementById('loadingModal').classList.remove('hidden');
});

// Save as draft functionality
function saveAsDraft() {
    alert('Draft functionality would be implemented here. In a real system, this would save the form data as a draft.');
}

// Helper function to format numbers
function formatNumber(number) {
    return parseFloat(number).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Initialize the form
document.addEventListener('DOMContentLoaded', function() {
    // Trigger zone change to filter walk routes
    const zoneSelect = document.getElementById('zoneSelect');
    if (zoneSelect.value) {
        zoneSelect.dispatchEvent(new Event('change'));
    }

    // Activate first tab by default
    activateTab('assignExistingMeter');
});
</script>

<style>
.meter-option:hover {
    background-color: #eff6ff;
    transform: translateX(2px);
    transition: all 0.2s ease;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    ring-width: 2px;
}

#loadingModal {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
@endcan
@endsection
