@extends('layouts.app')

@section('title', $meterCategory->name . ' - NYAWASCO')

@section('content')
@can('manage categories')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('admin.meter-categories.index') }}" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Back to Categories
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="text-gray-400">/</span>
                            <span class="ml-4 text-sm font-medium text-gray-500">{{ $meterCategory->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $meterCategory->name }}</h1>
            <p class="text-gray-600 mt-2">{{ $meterCategory->description ?? 'No description' }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.meter-categories.edit', $meterCategory) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Category
            </a>
            <a href="{{ route('admin.meters.index', ['category' => $meterCategory->id]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                <i class="fas fa-tachometer-alt mr-2"></i>
                View Meters
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Category Information -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-xl font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Category Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Category Code</label>
                            <p class="text-lg font-semibold text-blue-600">{{ $meterCategory->code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $meterCategory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $meterCategory->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Pricing Type</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $meterCategory->has_tiers ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $meterCategory->has_tiers ? 'Tiered Pricing' : 'Flat Rate' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Sort Order</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $meterCategory->sort_order }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Total Meters</label>
                            <p class="text-lg font-semibold text-orange-600">{{ $meterCategory->meters_count }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Created</label>
                            <p class="text-sm text-gray-900">{{ $meterCategory->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-xl font-semibold text-green-700 mb-4 flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>
                    Pricing Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">KSh {{ number_format($meterCategory->base_charge, 2) }}</div>
                        <div class="text-sm text-gray-600">Base Charge</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">KSh {{ number_format($meterCategory->meter_rent, 2) }}</div>
                        <div class="text-sm text-gray-600">Meter Rent</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">KSh {{ number_format($meterCategory->default_rate, 4) }}</div>
                        <div class="text-sm text-gray-600">Default Rate/m³</div>
                    </div>
                </div>

                <!-- Additional Charges -->
                @if($meterCategory->additional_charges && array_sum($meterCategory->additional_charges) > 0)
                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Additional Charges</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($meterCategory->additional_charges['installation_fee'] ?? 0)
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <div class="text-lg font-bold text-orange-600">KSh {{ number_format($meterCategory->additional_charges['installation_fee'], 2) }}</div>
                            <div class="text-sm text-gray-600">Installation Fee</div>
                        </div>
                        @endif
                        @if($meterCategory->additional_charges['connection_fee'] ?? 0)
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <div class="text-lg font-bold text-red-600">KSh {{ number_format($meterCategory->additional_charges['connection_fee'], 2) }}</div>
                            <div class="text-sm text-gray-600">Connection Fee</div>
                        </div>
                        @endif
                        @if($meterCategory->additional_charges['deposit'] ?? 0)
                        <div class="text-center p-3 bg-indigo-50 rounded-lg">
                            <div class="text-lg font-bold text-indigo-600">KSh {{ number_format($meterCategory->additional_charges['deposit'], 2) }}</div>
                            <div class="text-sm text-gray-600">Deposit Amount</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Pricing Tiers -->
            @if($meterCategory->has_tiers)
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-purple-700 flex items-center">
                        <i class="fas fa-layer-group mr-2 text-purple-600"></i>
                        Pricing Tiers
                    </h2>
                    <button onclick="openTierModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center text-sm">
                        <i class="fas fa-plus mr-2"></i>
                        Add Tier
                    </button>
                </div>

                @if($meterCategory->pricingTiers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption Range</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate (KSh/m³)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($meterCategory->pricingTiers as $tier)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $tier->name }}</div>
                                    @if($tier->description)
                                    <div class="text-xs text-gray-500">{{ $tier->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tier->consumption_range }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">KSh {{ number_format($tier->rate_per_unit, 4) }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tier->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tier->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="editTier({{ $tier->id }})" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.meter-categories.tiers.destroy', [$meterCategory, $tier]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this tier?')" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-layer-group text-4xl text-gray-400 mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">No Pricing Tiers</h3>
                    <p class="text-gray-500">Add pricing tiers to enable tiered pricing for this category.</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Associated Meters -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-orange-700 flex items-center">
                        <i class="fas fa-tachometer-alt mr-2 text-orange-600"></i>
                        Associated Meters
                    </h2>
                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $meterCategory->meters_count }} meters
                    </span>
                </div>

                @if($meterCategory->meters->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($meterCategory->meters->take(5) as $meter)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.meters.show', $meter) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        {{ $meter->meter_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($meter->customer)
                                    <a href="{{ route('admin.customers.show', $meter->customer) }}" class="text-gray-900 hover:text-blue-600">
                                        {{ $meter->customer->first_name }} {{ $meter->customer->last_name }}
                                    </a>
                                    @else
                                    <span class="text-gray-500">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'available' => 'bg-green-100 text-green-800',
                                            'assigned' => 'bg-blue-100 text-blue-800',
                                            'faulty' => 'bg-red-100 text-red-800',
                                            'maintenance' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($meter->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium {{ $meter->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    KSh {{ number_format($meter->current_balance, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($meterCategory->meters_count > 5)
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.meters.index', ['category' => $meterCategory->id]) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        View all {{ $meterCategory->meters_count }} meters ›
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-8">
                    <i class="fas fa-tachometer-alt text-4xl text-gray-400 mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">No Meters</h3>
                    <p class="text-gray-500">No meters are currently assigned to this category.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                    Quick Actions
                </h2>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.meters.index', ['category' => $meterCategory->id]) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        View All Meters
                    </a>
                    
                    <a href="{{ route('admin.meter-categories.edit', $meterCategory) }}" 
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Category
                    </a>

                    @if($meterCategory->has_tiers)
                    <button onclick="openTierModal()" 
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Pricing Tier
                    </button>
                    @endif

                    <a href="{{ route('admin.meters.create') }}" 
                       class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Add New Meter
                    </a>
                </div>
            </div>

            <!-- Rate Calculator -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-lg font-semibold text-green-700 mb-4 flex items-center">
                    <i class="fas fa-calculator mr-2 text-green-600"></i>
                    Rate Calculator
                </h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consumption (m³)</label>
                        <input type="number" id="calcConsumption" step="0.01" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm"
                               placeholder="Enter consumption" value="10">
                    </div>
                    
                    <button onclick="calculateCharge()" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Calculate Charge
                    </button>
                    
                    <div id="calcResult" class="hidden p-3 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600">Total Charge:</div>
                        <div id="calcAmount" class="text-lg font-bold text-green-600"></div>
                        <div id="calcBreakdown" class="text-xs text-gray-500 mt-1"></div>
                    </div>
                </div>
            </div>

            <!-- Category Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Category Status</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Active Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $meterCategory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $meterCategory->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pricing Type</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $meterCategory->has_tiers ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $meterCategory->has_tiers ? 'Tiered' : 'Flat Rate' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Meter Count</span>
                        <span class="text-sm font-medium text-orange-600">{{ $meterCategory->meters_count }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Last Updated</span>
                        <span class="text-sm text-gray-900">{{ $meterCategory->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Tier Modal -->
@if($meterCategory->has_tiers)
<div id="tierModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-xl font-bold text-purple-700" id="tierModalTitle">Add Pricing Tier</h3>
                <button type="button" onclick="closeTierModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="tierForm" method="POST" class="p-4 md:p-5">
                @csrf
                <div id="tierFormFields">
                    <!-- Form fields will be loaded here via JavaScript -->
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6 pt-4 border-t">
                    <button type="button" onclick="closeTierModal()" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200 order-2 sm:order-1">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition duration-200 order-1 sm:order-2">
                        Save Tier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function openTierModal(tierId = null) {
    const modal = document.getElementById('tierModal');
    const title = document.getElementById('tierModalTitle');
    const form = document.getElementById('tierForm');
    const formFields = document.getElementById('tierFormFields');
    
    if (tierId) {
        title.textContent = 'Edit Pricing Tier';
        // Load tier data via AJAX
        fetch(`/admin/meter-categories/{{ $meterCategory->id }}/tiers/${tierId}/edit`)
            .then(response => response.text())
            .then(html => {
                formFields.innerHTML = html;
            });
        form.action = `/admin/meter-categories/{{ $meterCategory->id }}/tiers/${tierId}`;
        form.method = 'POST';
        form.innerHTML += '<input type="hidden" name="_method" value="PUT">';
    } else {
        title.textContent = 'Add Pricing Tier';
        formFields.innerHTML = `
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tier Name *</label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                           placeholder="e.g., Tier 1 - Basic">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="min_consumption" class="block text-sm font-medium text-gray-700 mb-1">Min Consumption *</label>
                        <input type="number" name="min_consumption" id="min_consumption" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               placeholder="0.00" value="0">
                    </div>
                    
                    <div>
                        <label for="max_consumption" class="block text-sm font-medium text-gray-700 mb-1">Max Consumption</label>
                        <input type="number" name="max_consumption" id="max_consumption" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               placeholder="Leave empty for unlimited">
                    </div>
                </div>
                
                <div>
                    <label for="rate_per_unit" class="block text-sm font-medium text-gray-700 mb-1">Rate per m³ *</label>
                    <input type="number" name="rate_per_unit" id="rate_per_unit" step="0.0001" min="0" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                           placeholder="0.0000" value="0.0450">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                              placeholder="Brief description of this tier..."></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               placeholder="0" value="0">
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                   class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        </div>
                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                            Active Tier
                        </label>
                    </div>
                </div>
            </div>
        `;
        form.action = '{{ route("admin.meter-categories.tiers.store", $meterCategory) }}';
        form.method = 'POST';
    }
    
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeTierModal() {
    document.getElementById('tierModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function editTier(tierId) {
    openTierModal(tierId);
}

function calculateCharge() {
    const consumption = parseFloat(document.getElementById('calcConsumption').value) || 0;
    
    fetch('{{ route("admin.meter-categories.calculate", $meterCategory) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ consumption: consumption })
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('calcResult');
        const amountDiv = document.getElementById('calcAmount');
        const breakdownDiv = document.getElementById('calcBreakdown');
        
        if (data.success) {
            amountDiv.textContent = 'KSh ' + data.total_charge.toFixed(2);
            breakdownDiv.textContent = `Base: KSh ${data.base_charge.toFixed(2)} + Rent: KSh ${data.meter_rent.toFixed(2)} + Consumption: KSh ${data.consumption_charge.toFixed(2)}`;
            resultDiv.classList.remove('hidden');
        } else {
            amountDiv.textContent = 'Error calculating charge';
            breakdownDiv.textContent = '';
            resultDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Close modals when clicking outside
document.getElementById('tierModal')?.addEventListener('click', function(e) {
    if (e.target.id === 'tierModal') {
        closeTierModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTierModal();
    }
});
</script>
@endcan
@endsection