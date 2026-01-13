@extends('layouts.app')

@section('title', 'Meters Management - NYAWASCO')

@section('content')
@can('view meters')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add meters')) {
        $actionButtons[] = [
            'text' => 'Add Meter',
            'onclick' => 'openMeterModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }


    @endphp

    @include('components.dashboard-header',[
        'title' => 'Meters Management',
        'subtitle' => 'Meters Management Platform',
        'actionButtons' => $actionButtons
    ])
    <br>
    <!-- Statistics -->
    <div class="w-full px-2.5 py-4 md:py-8">

    <!-- Stats Cards -->
     @can('view paymentss')
     <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
         <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 md:p-6 text-center hover:shadow-xl transition-all duration-300 hover:border-blue-200">
             <div class="text-lg md:text-2xl lg:text-3xl font-bold text-blue-600 mb-1 md:mb-2">{{ $stats['total'] }}</div>
             <div class="text-gray-700 font-medium text-xs md:text-sm lg:text-base">Total Meters</div>
             <div class="text-xs text-gray-500 mt-1 hidden sm:block">All registered meters</div>
         </div>

         <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 md:p-6 text-center hover:shadow-xl transition-all duration-300 hover:border-green-200">
             <div class="text-lg md:text-2xl lg:text-3xl font-bold text-green-600 mb-1 md:mb-2">{{ $stats['assigned'] }}</div>
             <div class="text-gray-700 font-medium text-xs md:text-sm lg:text-base">Active</div>
             <div class="text-xs text-gray-500 mt-1 hidden sm:block">Currently assigned to customers</div>
         </div>

         <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 md:p-6 text-center hover:shadow-xl transition-all duration-300 hover:border-orange-200">
             <div class="text-lg md:text-2xl lg:text-3xl font-bold text-orange-600 mb-1 md:mb-2">{{ $stats['available'] }}</div>
             <div class="text-gray-700 font-medium text-xs md:text-sm lg:text-base">Available</div>
             <div class="text-xs text-gray-500 mt-1 hidden sm:block">Ready for assignment</div>
         </div>

         <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 md:p-6 text-center hover:shadow-xl transition-all duration-300 hover:border-red-200">
             <div class="text-lg md:text-2xl lg:text-3xl font-bold text-red-600 mb-1 md:mb-2">{{ $stats['faulty'] }}</div>
             <div class="text-gray-700 font-medium text-xs md:text-sm lg:text-base">Faulty</div>
             <div class="text-xs text-gray-500 mt-1 hidden sm:block">Needs maintenance</div>
         </div>
     </div>

    <!-- Quick Actions with Active Filter Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-1 mb-3">
        <a href="{{ route('admin.meters.index', ['filter' => 'all']) }}"
           class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-xl text-center transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 {{ ($filter ?? 'all') == 'all' ? 'ring-2 ring-blue-300 ring-opacity-50' : '' }}">
            <div class="text-xl font-semibold mb-2">All Meters</div>
            <div class="text-sm opacity-90">View all meters</div>
            <div class="mt-3 text-2xl opacity-80">
                <i class="fas fa-tachometer-alt"></i>
            </div>
        </a>

        <a href="{{ route('admin.meters.index', ['filter' => 'available']) }}"
           class="bg-orange-500 hover:bg-orange-600 text-white p-6 rounded-xl text-center transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 {{ ($filter ?? '') == 'available' ? 'ring-2 ring-orange-300 ring-opacity-50' : '' }}">
            <div class="text-xl font-semibold mb-2">Available Meters</div>
            <div class="text-sm opacity-90">View available meters</div>
            <div class="mt-3 text-2xl opacity-80">
                <i class="fas fa-box-open"></i>
            </div>
        </a>

        <a href="{{ route('admin.meters.index', ['filter' => 'active']) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-xl text-center transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 {{ ($filter ?? '') == 'active' ? 'ring-2 ring-green-300 ring-opacity-50' : '' }}">
                <div class="text-xl font-semibold mb-2">Active Meters</div>
                <div class="text-sm opacity-90">View customer meters</div>
                <div class="mt-3 text-2xl opacity-80">
                    <i class="fas fa-user-check"></i>
                </div>
        </a>

        <!-- By Location Card -->
        <a href="{{ route('admin.meters.index', ['filter' => 'location']) }}"
        class="bg-purple-500 hover:bg-purple-600 text-white p-6 rounded-xl text-center transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
            <div class="text-xl font-semibold mb-2">By Location</div>
            <div class="text-sm opacity-90 mb-4">Search meters by address</div>
            <div class="text-3xl opacity-80">
                <i class="fas fa-map-marker-alt"></i>
            </div>
        </a>

        <!-- Categories Card -->
        <a href="{{ route('admin.meter-categories.index') }}"
        class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-xl text-center transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
            <div class="text-xl font-semibold mb-2">Categories</div>
            <div class="text-sm opacity-90 mb-4">Manage categories & pricing</div>
            <div class="text-3xl opacity-80">
                <i class="fas fa-tags"></i>
            </div>
        </a>

    </div>

    <!-- Location Search (only show when location filter is active) -->
    @if(($filter ?? '') == 'location')
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.meters.index') }}" class="flex gap-4">
            <input type="hidden" name="filter" value="location">
            <div class="flex-1">
                <input type="text" name="location" value="{{ request('location') }}"
                       placeholder="Search by estate, plot number, or house number..."
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            @if(request('location'))
            <a href="{{ route('admin.meters.index', ['filter' => 'location']) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                Clear
            </a>
            @endif
        </form>
    </div>
    @endif

    <!-- Category Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">Filter by Category</h3>
            <div class="flex flex-wrap gap-2">
                @php
                    $currentParams = request()->all();
                    $baseParams = array_merge($currentParams, ['category' => null]);
                @endphp
                <a href="{{ route('admin.meters.index', $baseParams) }}"
                   class="px-4 py-2 rounded-lg {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    All Categories
                </a>
                @foreach($categories as $category)
                @php
                    $categoryParams = array_merge($currentParams, ['category' => $category->id]);
                @endphp
                <a href="{{ route('admin.meters.index', $categoryParams) }}"
                   class="px-4 py-2 rounded-lg {{ request('category') == $category->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ $category->name }} ({{ $category->meters_count }})
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endcan
    <!-- Simple Search Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.meters.index') }}" class="flex gap-4">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="hidden" name="category" value="{{ request('category') }}">

            @if($filter === 'location' && request('location'))
                <input type="hidden" name="location" value="{{ request('location') }}">
            @endif

            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search by meter number, customer name, location, etc..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                <i class="fas fa-search mr-2"></i>Search
            </button>

            @if(request('q'))
                <a href="{{ route('admin.meters.index', array_filter([
                    'filter' => $filter,
                    'category' => request('category'),
                    'location' => $filter === 'location' ? request('location') : null
                ])) }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            @endif
        </form>

        @if(request('q'))
            <div class="mt-3">
                <p class="text-sm text-gray-600">
                    Showing results for: <span class="font-semibold text-blue-600">"{{ request('q') }}"</span>
                    @if($meters->total() > 0)
                        <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                            {{ $meters->total() }} result(s) found
                        </span>
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Meters Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
         <!-- Table Stats --> <br>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 text-sm text-gray-600">
            <div>
            </div>
            <div class="flex items-center space-x-4 mt-2 sm:mt-0">
                <!-- Download Button -->

                <a href="{{ route('reports.generate', ['report_type' => 'meter', 'format' => 'excel', 'detail_level' => 'full']) }}"

                    class="download-excel-btn flex items-center space-x-2 bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded text-sm transition duration-200"
                    >
                    <i id="downloadIcon" class="fas fa-file-excel"></i>
                        <span id="downloadText">Export Excel</span>
                        <i id="downloadSpinner" class="fas fa-spinner fa-spin hidden"></i>
                </a>


            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Reading</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($meters as $meter)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $meter->meter_number }}</div>
                            <div class="text-xs text-gray-500">{{ $meter->meter_model ?? 'No model' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($meter->meterCategory)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $meter->meterCategory->name }}
                                </span>
                            @else
                                <span class="text-gray-500 text-sm">Uncategorized</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 capitalize">{{ $meter->meter_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($meter->customer)
                                <a href="{{ route('admin.customers.show', $meter->customer) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ $meter->customer->first_name }} {{ $meter->customer->last_name }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $meter->customer->customer_number }}</div>
                            @else
                                <span class="text-gray-500">Not active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($meter->customer && $meter->customer->estate)
                                    <!-- For assigned meters, use customer's estate as location -->
                                    <i class="fas fa-map-marker-alt text-purple-500 mr-1"></i>
                                    {{ $meter->customer->estate }}
                                    @if($meter->customer->plot_number || $meter->customer->house_number)
                                        <div class="text-xs text-gray-500">
                                            @if($meter->customer->plot_number)Plot {{ $meter->customer->plot_number }}@endif
                                            @if($meter->customer->house_number)@if($meter->customer->plot_number),@endif House {{ $meter->customer->house_number }}@endif
                                        </div>
                                    @endif
                                @elseif($meter->installation_address)
                                    <!-- For unassigned meters, use installation address -->
                                    {{ $meter->installation_address }}
                                @else
                                    <span class="text-gray-500">Location not set</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'Active' => 'bg-green-100 text-green-800',
                                    'Sealed' => 'bg-blue-100 text-blue-800',
                                    'Available' => 'bg-red-100 text-red-800',
                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($meter->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium {{ $meter->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                KSh {{ number_format($meter->current_balance, 2) }}
                            </div>
                            @if($meter->deposit_amount > 0)
                            <div class="text-xs text-gray-500">Deposit: KSh {{ number_format($meter->deposit_amount, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($meter->meterReadings->count() > 0)
                                    {{ number_format($meter->meterReadings->first()->current_reading) }} m³
                                    <div class="text-xs text-gray-500">
                                        {{ $meter->meterReadings->first()->reading_date->format('M d, Y') }}
                                        @if($meter->meterReadings->first()->consumption > 0)
                                            <br><span class="text-green-600">+{{ $meter->meterReadings->first()->consumption }} m³</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-500">No readings</span>
                                    @if($meter->initial_reading > 0)
                                        <div class="text-xs text-gray-400">Initial: {{ $meter->initial_reading }} m³</div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.meters.show', $meter) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="openEditMeterModal({{ $meter->id }})" class="text-green-600 hover:text-green-900" title="Edit Meter">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($meter->customer)
                                <a href="{{ route('admin.meter-readings.create', ['customer' => $meter->customer->id]) }}" class="text-purple-600 hover:text-purple-900" title="Record Reading">
                                    <i class="fas fa-tachometer-alt"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                            @if(($filter ?? '') == 'available')
                                No unassigned meters found.
                            @elseif(($filter ?? '') == 'active')
                                No active meters found.
                            @elseif(($filter ?? '') == 'location' && request('location'))
                                No meters found for location "{{ request('location') }}".
                            @elseif(($filter ?? '') == 'location')
                                Use the search bar above to find meters by location.
                            @else
                                No meters found. <button onclick="openMeterModal()" class="text-blue-600 hover:text-blue-900 font-medium">Register the first meter</button>.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $meters->links() }}
        </div>
    </div>

    <!-- Register Meter Modal -->
    <div id="registerMeterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-bold text-blue-700">Register New Meter</h3>
                    <button type="button" onclick="closeMeterModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="meterRegistrationForm" action="{{ route('admin.meters.store') }}" method="POST" class="p-4 md:p-5">
                    @csrf

                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="modal_meter_number" class="block text-sm font-medium text-gray-700 mb-1">Meter Number *</label>
                                <input type="text" name="meter_number" id="modal_meter_number" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                       placeholder="MTR20241215001">
                            </div>

                            <div>
                                <label for="modal_meter_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                <select name="meter_category_id" id="modal_meter_category_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }} - {{ $category->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="modal_meter_type" class="block text-sm font-medium text-gray-700 mb-1">Meter Type *</label>
                                <select name="meter_type" id="modal_meter_type" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                                    <option value="">Select Meter Type</option>
                                    <option value="domestic">Domestic - Single Phase</option>
                                    <option value="commercial">Commercial - Three Phase</option>
                                    <option value="industrial">Industrial - High Capacity</option>
                                    <option value="institutional">Institutional - Bulk Meter</option>
                                    <option value="smart">Smart Meter - Digital</option>
                                    <option value="mechanical">Mechanical - Analog</option>
                                </select>
                            </div>

                            <div>
                                <label for="modal_meter_model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                <input type="text" name="meter_model" id="modal_meter_model"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                       placeholder="e.g., K-1000, S-2000">
                            </div>
                        </div>



                        <!-- Initial Reading Only -->
                        <div>
                            <label for="modal_initial_reading" class="block text-sm font-medium text-gray-700 mb-1">Initial Reading (m³) *</label>
                            <input type="number" name="initial_reading" id="modal_initial_reading" step="0.01" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                placeholder="0.00"
                                value="0">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="modal_balance_bf" class="block text-sm font-medium text-gray-700 mb-1">Balance B/F</label>
                                <input type="number" name="balance_bf" id="modal_balance_bf" step="0.01" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="0.00"
                                    value="0">
                            </div>

                            <div>
                                <label for="modal_customer_id" class="block text-sm font-medium text-gray-700 mb-1">Assign to Customer (Optional)</label>
                                <select name="customer_id" id="modal_customer_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm customer-select">
                                    <option value="">Select Customer (Leave empty if not assigned)</option>
                                    <!-- Options will be loaded via AJAX -->
                                </select>
                            </div>
                        </div>

                        <div id="customer_installation_fields" class="hidden">
                            <div>
                                <label for="modal_installation_address" class="block text-sm font-medium text-gray-700 mb-1">Installation Address</label>
                                <input type="text" name="installation_address" id="modal_installation_address"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="Customer's installation address">
                            </div>
                        </div>

                        <div>
                            <label for="modal_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea name="notes" id="modal_notes" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                      placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6 pt-4 border-t">
                        <button type="button" onclick="closeMeterModal()" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200 order-2 sm:order-1">
                            Cancel
                        </button>
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition duration-200 order-1 sm:order-2">
                            Register Meter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Meter Modal -->
    <div id="editMeterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl mx-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t bg-gradient-to-r from-green-600 to-emerald-600">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Meter
                    </h3>
                    <button type="button" onclick="closeEditMeterModal()" class="text-white hover:bg-emerald-700 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="editMeterForm" action="" method="POST" class="p-4 md:p-5">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_meter_number" class="block text-sm font-medium text-gray-700 mb-1">Meter Number *</label>
                                <input type="text" name="meter_number" id="edit_meter_number" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                            </div>

                            <div>
                                <label for="edit_meter_type" class="block text-sm font-medium text-gray-700 mb-1">Meter Type *</label>
                                <select name="meter_type" id="edit_meter_type" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                                    <option value="domestic">Domestic - Single Phase</option>
                                    <option value="commercial">Commercial - Three Phase</option>
                                    <option value="industrial">Industrial - High Capacity</option>
                                    <option value="institutional">Institutional - Bulk Meter</option>
                                    <option value="smart">Smart Meter - Digital</option>
                                    <option value="mechanical">Mechanical - Analog</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_meter_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                <select name="meter_category_id" id="edit_meter_category_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }} - {{ $category->code }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="edit_meter_model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                <input type="text" name="meter_model" id="edit_meter_model"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                    placeholder="e.g., K-1000, S-2000">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_manufacturer" class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                                <input type="text" name="manufacturer" id="edit_manufacturer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                    placeholder="Manufacturer name">
                            </div>

                            <div>
                                <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select name="status" id="edit_status" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                                    <option value="available">Available</option>
                                    <option value="active">Active</option>
                                    <option value="terminated">Terminated</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="pending_payment">Pending payment</option>
                                </select>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>
                                Location Information
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_installation_address" class="block text-sm font-medium text-gray-700 mb-1">Installation Address</label>
                                    <input type="text" name="installation_address" id="edit_installation_address"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                        placeholder="Full installation address">
                                </div>

                                    <div>
                                        <label for="edit_customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                                        <select name="customer_id" id="edit_customer_id"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm customer-select">
                                            <option value="">Select Customer</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                    </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label for="edit_zone_id" class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                                    <select name="zone_id" id="edit_zone_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                                        <option value="">Select Zone</option>
                                        @foreach(App\Models\Zone::all() as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="edit_walk_route_id" class="block text-sm font-medium text-gray-700 mb-1">Walk Route</label>
                                    <select name="walk_route_id" id="edit_walk_route_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                                        <option value="">Select Walk Route</option>
                                        @foreach(App\Models\WalkRoute::all() as $route)
                                            <option value="{{ $route->id }}">{{ $route->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label for="edit_latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                    <input type="number" step="any" name="latitude" id="edit_latitude"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                        placeholder="e.g., -1.2921">
                                </div>

                                <div>
                                    <label for="edit_longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                    <input type="number" step="any" name="longitude" id="edit_longitude"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                        placeholder="e.g., 36.8219">
                                    <small class="text-gray-500">Note: Column name is 'longtitude' in database</small>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Information -->
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 text-gray-500"></i>
                                Financial Information
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="edit_initial_reading" class="block text-sm font-medium text-gray-700 mb-1">Initial Reading (m³)</label>
                                    <input type="number" name="initial_reading" id="edit_initial_reading" step="0.01" min="0"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                        value="0">
                                </div>


                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">

                                <div>
                                    <label for="edit_balance_bf" class="block text-sm font-medium text-gray-700 mb-1">Balance B/F</label>
                                    <input type="number" name="balance_bf" id="edit_balance_bf" step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                        value="0">
                                </div>

                                <div>
                                    <label for="edit_current_balance" class="block text-sm font-medium text-gray-700 mb-1">Current Balance</label>
                                    <input type="number" name="current_balance" id="edit_current_balance" step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                        value="0">
                                </div>
                            </div>
                        </div>

                        <!-- Dates Information -->
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>
                                Dates Information
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_installation_date" class="block text-sm font-medium text-gray-700 mb-1">Installation Date</label>
                                    <input type="date" name="installation_date" id="edit_installation_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                                </div>

                                <div>
                                    <label for="edit_last_maintenance_date" class="block text-sm font-medium text-gray-700 mb-1">Last Maintenance Date</label>
                                    <input type="date" name="last_maintenance_date" id="edit_last_maintenance_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="edit_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" id="edit_notes" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                    placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6 pt-4 border-t">
                        <button type="button" onclick="closeEditMeterModal()"
                                class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200 order-2 sm:order-1">
                            Cancel
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition-all duration-200 transform hover:scale-105 order-1 sm:order-2">
                            <i class="fas fa-save mr-2"></i>Update Meter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openMeterModal() {
        document.getElementById('registerMeterModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeMeterModal() {
        document.getElementById('registerMeterModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close modal when clicking outside
    document.getElementById('registerMeterModal').addEventListener('click', function(e) {
        if (e.target.id === 'registerMeterModal') {
            closeMeterModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMeterModal();
        }
    });

    // Auto-generate meter number if empty
    document.getElementById('modal_meter_number').addEventListener('focus', function() {
        if (!this.value) {
            const now = new Date();
            const dateStr = now.toISOString().slice(0,10).replace(/-/g, '');
            const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            this.value = 'MTR' + dateStr + randomNum;
        }
    });

    // Handle customer selection change in registration modal
    document.getElementById('modal_customer_id')?.addEventListener('change', function() {
        const customerFields = document.getElementById('customer_installation_fields');
        const installationAddress = document.getElementById('modal_installation_address');

        if (this.value) {
            if (customerFields) customerFields.classList.remove('hidden');
            // Auto-fill installation address if customer is selected
            fetch(`/admin/customers/${this.value}/address`)
                .then(response => response.json())
                .then(data => {
                    if (data.address && installationAddress) {
                        installationAddress.value = data.address;
                    }
                });
        } else {
            if (customerFields) customerFields.classList.add('hidden');
            if (installationAddress) installationAddress.value = '';
        }
    });

    // Initialize Select2 for customer dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all customer select elements
        $('.customer-select').select2({
            placeholder: 'Search for customer...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '/admin/customers/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    });

    // Handle form submission for registration
    document.getElementById('meterRegistrationForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Registering...';
        submitBtn.disabled = true;

        // Submit the form
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeMeterModal();
                location.reload(); // Refresh to show new meter
            } else {
                alert('Error: ' + (data.message || 'Failed to register meter'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });

    // Edit meter modal functions
    let currentMeterId = null;

    async function openEditMeterModal(meterId) {
        console.log('Opening edit modal for meter ID:', meterId);
        currentMeterId = meterId;

        // Show modal
        const modal = document.getElementById('editMeterModal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Reinitialize Select2 for the edit modal (important!)
        $('#edit_customer_id').select2({
            placeholder: 'Search for customer...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '/admin/customers/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        // Show loading state
        const form = document.getElementById('editMeterForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnContent = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
        submitBtn.disabled = true;

        try {
            console.log('Fetching meter data from endpoint...');

            // Make the API call
            const response = await fetch(`/admin/meters/${meterId}/json`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }

            const meter = await response.json();
            console.log('Meter data received:', meter);

            // Populate form fields
            await populateFormFields(meter); // Made async

            // Update form action
            form.action = `/admin/meters/${meterId}`;

            // Success - restore UI
            submitBtn.innerHTML = originalBtnContent;
            submitBtn.disabled = false;

            console.log('Modal populated successfully');

        } catch (error) {
            console.error('Error loading meter data:', error);

            // Restore UI
            submitBtn.innerHTML = originalBtnContent;
            submitBtn.disabled = false;

            // Show detailed error
            alert(`Failed to load meter data.\n\nError: ${error.message}\n\nCheck browser console (F12) for details.`);

            // Close modal on error
            closeEditMeterModal();
        }
    }

    // Helper function to populate form fields - UPDATED to handle Select2
    async function populateFormFields(meter) {
        console.log('Populating form with data:', meter);

        // Basic fields
        document.getElementById('edit_meter_number').value = meter.meter_number || '';
        document.getElementById('edit_meter_type').value = meter.meter_type || 'domestic';
        document.getElementById('edit_meter_category_id').value = meter.meter_category_id || '';
        document.getElementById('edit_meter_model').value = meter.meter_model || '';
        document.getElementById('edit_manufacturer').value = meter.manufacturer || '';
        document.getElementById('edit_status').value = meter.status || '';
        document.getElementById('edit_installation_address').value = meter.installation_address || '';
        document.getElementById('edit_zone_id').value = meter.zone_id || '';
        document.getElementById('edit_walk_route_id').value = meter.walk_route_id || '';
        document.getElementById('edit_latitude').value = meter.latitude || '';

        // IMPORTANT: Use 'longtitude' from database (note the spelling)
        document.getElementById('edit_longitude').value = meter.longtitude || '';

        // Financial fields
        document.getElementById('edit_initial_reading').value = meter.initial_reading || 0;
        document.getElementById('edit_balance_bf').value = meter.balance_bf || 0;
        document.getElementById('edit_current_balance').value = meter.current_balance || 0;

        // Date fields
        document.getElementById('edit_installation_date').value = meter.installation_date || '';
        document.getElementById('edit_last_maintenance_date').value = meter.last_maintenance_date || '';

        // Notes
        document.getElementById('edit_notes').value = meter.notes || '';

        // Handle customer selection - FIXED for Select2
        const customerId = meter.customer_id;
        const $customerSelect = $('#edit_customer_id');

        if (customerId) {
            try {
                // Fetch customer details
                const response = await fetch(`/admin/customers/${customerId}/details`);
                const customerData = await response.json();

                // Create new option with the fetched data
                const option = new Option(
                    customerData.text || `${customerData.customer_number} - ${customerData.first_name} ${customerData.last_name}`,
                    customerId,
                    true,
                    true
                );

                // Append the option and trigger change
                $customerSelect.append(option).trigger('change');

                console.log('Customer pre-selected:', customerId);
            } catch (error) {
                console.error('Error fetching customer details:', error);
                // Fallback: just set the value
                $customerSelect.val(customerId).trigger('change');
            }
        } else {
            // Clear the selection
            $customerSelect.val(null).trigger('change');
        }
    }

    function closeEditMeterModal() {
        document.getElementById('editMeterModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        currentMeterId = null;

        // Destroy Select2 instance when closing modal to prevent conflicts
        $('#edit_customer_id').select2('destroy');
    }

    // Close modal when clicking outside
    document.getElementById('editMeterModal').addEventListener('click', function(e) {
        if (e.target.id === 'editMeterModal') {
            closeEditMeterModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('editMeterModal').classList.contains('hidden')) {
            closeEditMeterModal();
        }
    });

    // Handle form submission for edit modal
    document.getElementById('editMeterForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData(this);

            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                closeEditMeterModal();
                location.reload(); // Refresh to show updated data
            } else {
                alert('Error: ' + (result.message || 'Failed to update meter'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    // Quick search functionality for main page
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="q"]');
        const searchForm = document.querySelector('form[method="GET"]');

        if (!searchInput || !searchForm) return;

        // Quick search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchForm.submit();
            }
        });

        // Auto-focus search input
        searchInput.focus();

        // Quick clear button
        if (searchInput.value) {
            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600';
            clearBtn.innerHTML = '<i class="fas fa-times"></i>';
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.focus();
            });

            const inputContainer = searchInput.parentElement;
            inputContainer.classList.add('relative');
            inputContainer.appendChild(clearBtn);
        }
    });
</script>
@endcan
@endsection
