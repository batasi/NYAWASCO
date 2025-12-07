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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

   <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 text-center hover:shadow-xl transition-all duration-300 hover:border-blue-200">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['total'] }}</div>
            <div class="text-gray-700 font-medium">Total Meters</div>
            <div class="text-xs text-gray-500 mt-1">All registered meters</div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 text-center hover:shadow-xl transition-all duration-300 hover:border-green-200">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ $stats['assigned'] }}</div>
            <div class="text-gray-700 font-medium">Active</div>
            <div class="text-xs text-gray-500 mt-1">Currently assigned to customers</div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 text-center hover:shadow-xl transition-all duration-300 hover:border-orange-200">
            <div class="text-3xl font-bold text-orange-600 mb-2">{{ $stats['unassigned'] }}</div>
            <div class="text-gray-700 font-medium">Available</div>
            <div class="text-xs text-gray-500 mt-1">Ready for assignment</div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 text-center hover:shadow-xl transition-all duration-300 hover:border-red-200">
            <div class="text-3xl font-bold text-red-600 mb-2">{{ $stats['faulty'] }}</div>
            <div class="text-gray-700 font-medium">Faulty</div>
            <div class="text-xs text-gray-500 mt-1">Needs maintenance</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <!-- Meters Card with Inline Filters -->
        <div class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-xl transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
            <div class="text-xl font-semibold mb-2">Meters</div>
            <div class="text-sm opacity-90 mb-4">View or filter meters</div>
            <div class="flex justify-center gap-2 mb-4">
                <a href="{{ route('admin.meters.index', ['filter' => 'all']) }}"
                class="px-3 py-1 rounded-full {{ ($filter ?? 'all') == 'all' ? 'bg-white text-blue-600 font-semibold' : 'bg-blue-700 text-white' }}">
                    All
                </a>
                <a href="{{ route('admin.meters.index', ['filter' => 'available']) }}"
                class="px-3 py-1 rounded-full {{ ($filter ?? '') == 'available' ? 'bg-white text-blue-600 font-semibold' : 'bg-blue-700 text-white' }}">
                    Available
                </a>
                <a href="{{ route('admin.meters.index', ['filter' => 'active']) }}"
                class="px-3 py-1 rounded-full {{ ($filter ?? '') == 'assigned' ? 'bg-white text-blue-600 font-semibold' : 'bg-blue-700 text-white' }}">
                    Active
                </a>
            </div>
            <div class="text-3xl opacity-80 text-center">
                <i class="fas fa-tachometer-alt"></i>
            </div>
        </div>

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

    <!-- Meters Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
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
                                <span class="text-gray-500">Not assigned</span>
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
                            @elseif(($filter ?? '') == 'assigned')
                                No assigned meters found.
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
                                        <option value="{{ $category->id }}" data-fees="{{ json_encode($category->additional_charges) }}">
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

                        <!-- Charges Section -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Installation Charges</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label for="modal_installation_fee" class="block text-sm font-medium text-gray-700 mb-1">Installation Fee</label>
                                    <input type="number" name="installation_fee" id="modal_installation_fee" step="0.01" min="0"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                        placeholder="0.00">
                                </div>

                                <div>
                                    <label for="modal_connection_fee" class="block text-sm font-medium text-gray-700 mb-1">Connection Fee</label>
                                    <input type="number" name="connection_fee" id="modal_connection_fee" step="0.01" min="0"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                        placeholder="0.00">
                                </div>

                                <div>
                                    <label for="modal_deposit_amount" class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount</label>
                                    <input type="number" name="deposit_amount" id="modal_deposit_amount" step="0.01" min="0"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                        placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Initial Reading and Installation Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="modal_initial_reading" class="block text-sm font-medium text-gray-700 mb-1">Initial Reading (m³) *</label>
                                <input type="number" name="initial_reading" id="modal_initial_reading" step="0.01" min="0" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="0.00"
                                    value="0">
                            </div>

                            <div>
                                <label for="modal_installation_date" class="block text-sm font-medium text-gray-700 mb-1">Installation Date</label>
                                <input type="date" name="installation_date" id="modal_installation_date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    value="{{ date('Y-m-d') }}">
                            </div>
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
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                                    <option value="">Select Customer (Leave empty if not assigned)</option>
                                    @foreach(App\Models\Customer::active()->get() as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->customer_number }} - {{ $customer->first_name }} {{ $customer->last_name }}
                                        </option>
                                    @endforeach
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
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                                    <option value="">Select Customer</option>
                                    @foreach(App\Models\Customer::active()->get() as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->customer_number }} - {{ $customer->first_name }} {{ $customer->last_name }}
                                        </option>
                                    @endforeach
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                            <div>
                                <label for="edit_additional_charges" class="block text-sm font-medium text-gray-700 mb-1">Additional Charges</label>
                                <input type="text" name="additional_charges" id="edit_additional_charges"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                                       placeholder='JSON format: {"charge1": 100, "charge2": 200}'>
                                <small class="text-gray-500">Enter as JSON object</small>
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

    // Handle category change to auto-fill fees
    document.getElementById('modal_meter_category_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const fees = selectedOption ? JSON.parse(selectedOption.getAttribute('data-fees') || '{}') : {};

        document.getElementById('modal_installation_fee').value = fees.installation_fee || '';
        document.getElementById('modal_connection_fee').value = fees.connection_fee || '';
        document.getElementById('modal_deposit_amount').value = fees.deposit || '';
    });

    // Handle customer selection
    document.getElementById('modal_customer_id').addEventListener('change', function() {
        const customerFields = document.getElementById('customer_installation_fields');
        const installationAddress = document.getElementById('modal_installation_address');

        if (this.value) {
            customerFields.classList.remove('hidden');
            // Auto-fill installation address if customer is selected
            fetch(`/admin/customers/${this.value}/address`)
                .then(response => response.json())
                .then(data => {
                    if (data.address) {
                        installationAddress.value = data.address;
                    }
                });
        } else {
            customerFields.classList.add('hidden');
            installationAddress.value = '';
        }
    });

    // Handle form submission
    document.getElementById('meterRegistrationForm').addEventListener('submit', function(e) {
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

    // Set today's date as default for installation date
    document.getElementById('modal_installation_date').value = new Date().toISOString().split('T')[0];


    //edit meter modal functions

    let currentMeterId = null;


    async function openEditMeterModal(meterId) {
        console.log('Opening edit modal for meter ID:', meterId);
        currentMeterId = meterId;

        // Show modal
        const modal = document.getElementById('editMeterModal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

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
            populateFormFields(meter);

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

    // Helper function to populate form fields
    function populateFormFields(meter) {
        console.log('Populating form with data:', meter);

        // Basic fields
        document.getElementById('edit_meter_number').value = meter.meter_number || '';
        document.getElementById('edit_meter_type').value = meter.meter_type || 'domestic';
        document.getElementById('edit_meter_category_id').value = meter.meter_category_id || '';
        document.getElementById('edit_meter_model').value = meter.meter_model || '';
        document.getElementById('edit_manufacturer').value = meter.manufacturer || '';
        document.getElementById('edit_status').value = meter.status || '';
        document.getElementById('edit_installation_address').value = meter.installation_address || '';
        document.getElementById('edit_customer_id').value = meter.customer_id || '';
        document.getElementById('edit_zone_id').value = meter.zone_id || '';
        document.getElementById('edit_walk_route_id').value = meter.walk_route_id || '';
        document.getElementById('edit_latitude').value = meter.latitude || '';

        // IMPORTANT: Use 'longtitude' from database (note the spelling)
        document.getElementById('edit_longitude').value = meter.longtitude || '';

        // Financial fields
        document.getElementById('edit_initial_reading').value = meter.initial_reading || 0;
        // document.getElementById('edit_installation_fee').value = meter.installation_fee || 0;
        // document.getElementById('edit_connection_fee').value = meter.connection_fee || 0;
        // document.getElementById('edit_deposit_amount').value = meter.deposit_amount || 0;
        document.getElementById('edit_balance_bf').value = meter.balance_bf || 0;
        document.getElementById('edit_current_balance').value = meter.current_balance || 0;

        // Date fields - use the formatted dates directly
        document.getElementById('edit_installation_date').value = meter.installation_date || '';
        document.getElementById('edit_last_maintenance_date').value = meter.last_maintenance_date || '';

        // Additional charges (JSON to string)
        if (meter.additional_charges) {
            if (typeof meter.additional_charges === 'object' && meter.additional_charges !== null) {
                document.getElementById('edit_additional_charges').value = JSON.stringify(meter.additional_charges, null, 2);
            } else if (typeof meter.additional_charges === 'string' && meter.additional_charges.trim() !== '') {
                try {
                    const parsed = JSON.parse(meter.additional_charges);
                    document.getElementById('edit_additional_charges').value = JSON.stringify(parsed, null, 2);
                } catch (e) {
                    document.getElementById('edit_additional_charges').value = meter.additional_charges;
                }
            } else {
                document.getElementById('edit_additional_charges').value = '';
            }
        } else {
            document.getElementById('edit_additional_charges').value = '';
        }

        // Notes
        document.getElementById('edit_notes').value = meter.notes || '';
    }



    function closeEditMeterModal() {
        document.getElementById('editMeterModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        currentMeterId = null;
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

    // Handle form submission
    document.getElementById('editMeterForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData(this);

            // Convert additional_charges from string to JSON if needed
            const additionalCharges = formData.get('additional_charges');
            if (additionalCharges) {
                try {
                    // Try to parse as JSON to validate
                    JSON.parse(additionalCharges);
                    // If valid, keep as string (Laravel will handle JSON casting)
                } catch (e) {
                    // If not valid JSON, set to empty
                    formData.set('additional_charges', '{}');
                }
            }

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
    </script>

@endcan
@endsection
