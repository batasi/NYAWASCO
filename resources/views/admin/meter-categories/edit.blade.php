@extends('layouts.app')

@section('title', 'Edit ' . $meterCategory->name . ' - NYAWASCO')

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
                            <a href="{{ route('admin.meter-categories.show', $meterCategory) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                {{ $meterCategory->name }}
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="text-gray-400">/</span>
                            <span class="ml-4 text-sm font-medium text-gray-500">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit {{ $meterCategory->name }}</h1>
            <p class="text-gray-600 mt-2">Update category information and pricing</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.meter-categories.show', $meterCategory) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                <i class="fas fa-eye mr-2"></i>
                View Category
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.meter-categories.update', $meterCategory) }}" method="POST" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h2 class="text-lg font-semibold text-blue-800 mb-4">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $meterCategory->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Category Code *</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $meterCategory->code) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                   maxlength="10">
                            @error('code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                  placeholder="Brief description of this category...">{{ old('description', $meterCategory->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pricing Configuration -->
                <div class="border-t pt-6">
                    <h2 class="text-lg font-semibold text-green-700 mb-4">Pricing Configuration</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="base_charge" class="block text-sm font-medium text-gray-700 mb-1">Base Charge (KSh) *</label>
                            <input type="number" name="base_charge" id="base_charge" step="0.01" min="0" required
                                   value="{{ old('base_charge', $meterCategory->base_charge) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                            @error('base_charge')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meter_rent" class="block text-sm font-medium text-gray-700 mb-1">Meter Rent (KSh) *</label>
                            <input type="number" name="meter_rent" id="meter_rent" step="0.01" min="0" required
                                   value="{{ old('meter_rent', $meterCategory->meter_rent) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                            @error('meter_rent')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="default_rate" class="block text-sm font-medium text-gray-700 mb-1">Default Rate (KSh/m³) *</label>
                            <input type="number" name="default_rate" id="default_rate" step="0.0001" min="0" required
                                value="{{ old('default_rate', $meterCategory->default_rate) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition text-sm">
                            @error('default_rate')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="has_tiers" id="has_tiers" value="1" 
                                    {{ old('has_tiers', $meterCategory->has_tiers) ? 'checked' : '' }}
                                    class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                    onchange="toggleTieredPricing(this.checked)">
                            </div>
                            <label for="has_tiers" class="ml-2 text-sm text-gray-700">
                                Enable Tiered Pricing
                            </label>
                        </div>
                    </div>

                    <!-- Tiered Pricing Container -->
                    <div id="tieredPricingContainer" class="{{ $meterCategory->has_tiers ? '' : 'hidden' }} mt-6 border-t pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-md font-semibold text-gray-700">Pricing Tiers</h3>
                            <button type="button" onclick="addTier()" 
                                    class="text-sm bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded flex items-center">
                                <i class="fas fa-plus mr-1"></i>Add Tier
                            </button>
                        </div>
                        
                        <div id="tiersContainer" class="space-y-4">
                            @php
                                $tiers = old('tiers', $meterCategory->pricingTiers->toArray());
                            @endphp
                            
                            @if(count($tiers) > 0)
                                @foreach($tiers as $index => $tier)
                                    <div class="tier-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="font-medium text-gray-700">Tier {{ $loop->iteration }}</h4>
                                            <button type="button" onclick="removeTier(this)" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Tier Name *</label>
                                                <input type="text" name="tiers[{{ $index }}][name]" 
                                                    value="{{ old('tiers.' . $index . '.name', $tier['name'] ?? '') }}"
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                    placeholder="e.g., Tier 1 - Basic" required>
                                                @error('tiers.' . $index . '.name')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                                                <input type="number" name="tiers[{{ $index }}][sort_order]" 
                                                    value="{{ old('tiers.' . $index . '.sort_order', $tier['sort_order'] ?? $loop->iteration) }}"
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                    min="0">
                                                @error('tiers.' . $index . '.sort_order')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3 mt-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Min Consumption (m³) *</label>
                                                <input type="number" name="tiers[{{ $index }}][min_consumption]" 
                                                    step="0.01" min="0" required
                                                    value="{{ old('tiers.' . $index . '.min_consumption', $tier['min_consumption'] ?? '') }}"
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                    placeholder="0.00">
                                                @error('tiers.' . $index . '.min_consumption')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Max Consumption (m³)</label>
                                                <input type="number" name="tiers[{{ $index }}][max_consumption]" 
                                                    step="0.01" min="0"
                                                    value="{{ old('tiers.' . $index . '.max_consumption', $tier['max_consumption'] ?? '') }}"
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                    placeholder="Leave empty for unlimited">
                                                @error('tiers.' . $index . '.max_consumption')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Rate per m³ (KSh) *</label>
                                            <input type="number" name="tiers[{{ $index }}][rate_per_unit]" 
                                                step="0.0001" min="0" required
                                                value="{{ old('tiers.' . $index . '.rate_per_unit', $tier['rate_per_unit'] ?? '') }}"
                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                placeholder="0.0000">
                                            @error('tiers.' . $index . '.rate_per_unit')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div class="mt-2">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                            <textarea name="tiers[{{ $index }}][description]" rows="2"
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                    placeholder="Brief description of this tier...">{{ old('tiers.' . $index . '.description', $tier['description'] ?? '') }}</textarea>
                                            @error('tiers.' . $index . '.description')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div class="mt-2 flex items-center">
                                            <input type="checkbox" name="tiers[{{ $index }}][is_active]" value="1" 
                                                {{ old('tiers.' . $index . '.is_active', $tier['is_active'] ?? true) ? 'checked' : '' }}
                                                class="w-3 h-3 text-blue-600 border-gray-300 rounded">
                                            <label class="ml-2 text-xs text-gray-600">Active Tier</label>
                                        </div>
                                        
                                        <!-- Include tier ID if editing existing tier -->
                                        @if(isset($tier['id']))
                                            <input type="hidden" name="tiers[{{ $index }}][id]" value="{{ $tier['id'] }}">
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-500 mt-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tiers are processed in order. Make sure consumption ranges don't overlap.
                        </div>
                    </div>
                </div>

                <!-- Additional Charges -->
                <div class="border-t pt-6">
                    <h2 class="text-lg font-semibold text-orange-700 mb-4">Additional Charges</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="installation_fee" class="block text-sm font-medium text-gray-700 mb-1">Installation Fee (KSh)</label>
                            <input type="number" name="installation_fee" id="installation_fee" step="0.01" min="0"
                                   value="{{ old('installation_fee', $meterCategory->additional_charges['installation_fee'] ?? 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition text-sm">
                        </div>

                        <div>
                            <label for="connection_fee" class="block text-sm font-medium text-gray-700 mb-1">Connection Fee (KSh)</label>
                            <input type="number" name="connection_fee" id="connection_fee" step="0.01" min="0"
                                   value="{{ old('connection_fee', $meterCategory->additional_charges['connection_fee'] ?? 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition text-sm">
                        </div>

                        <div>
                            <label for="deposit" class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount (KSh)</label>
                            <input type="number" name="deposit" id="deposit" step="0.01" min="0"
                                   value="{{ old('deposit', $meterCategory->additional_charges['deposit'] ?? 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition text-sm">
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="border-t pt-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Settings</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" min="0"
                                   value="{{ old('sort_order', $meterCategory->sort_order) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 outline-none transition text-sm">
                            @error('sort_order')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $meterCategory->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <label for="is_active" class="ml-2 text-sm text-gray-700">
                                Active Category
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="border-t pt-6">
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.meter-categories.show', $meterCategory) }}" 
                           class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center justify-center order-2 sm:order-1">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition duration-200 flex items-center justify-center order-1 sm:order-2">
                            <i class="fas fa-save mr-2"></i>
                            Update Category
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
let tierCounter = {{ count($meterCategory->pricingTiers) }};

function toggleTieredPricing(enabled) {
    const container = document.getElementById('tieredPricingContainer');
    if (enabled) {
        container.classList.remove('hidden');
        // Add first tier if none exist
        if (document.querySelectorAll('.tier-item').length === 0) {
            addTier();
        }
    } else {
        container.classList.add('hidden');
    }
}

function addTier() {
    tierCounter++;
    const tiersContainer = document.getElementById('tiersContainer');
    
    const tierHtml = `
        <div class="tier-item bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="flex justify-between items-start mb-3">
                <h4 class="font-medium text-gray-700">Tier ${tierCounter}</h4>
                <button type="button" onclick="removeTier(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tier Name *</label>
                    <input type="text" name="tiers[${tierCounter}][name]" 
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                           placeholder="e.g., Tier 1 - Basic" required>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                    <input type="number" name="tiers[${tierCounter}][sort_order]" 
                           value="${tierCounter}"
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                           min="0">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mt-2">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Min Consumption (m³) *</label>
                    <input type="number" name="tiers[${tierCounter}][min_consumption]" 
                           step="0.01" min="0" required
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Max Consumption (m³)</label>
                    <input type="number" name="tiers[${tierCounter}][max_consumption]" 
                           step="0.01" min="0"
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                           placeholder="Leave empty for unlimited">
                </div>
            </div>
            
            <div class="mt-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Rate per m³ (KSh) *</label>
                <input type="number" name="tiers[${tierCounter}][rate_per_unit]" 
                       step="0.0001" min="0" required
                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                       placeholder="0.0000">
            </div>
            
            <div class="mt-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <textarea name="tiers[${tierCounter}][description]" rows="2"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                          placeholder="Brief description of this tier..."></textarea>
            </div>
            
            <div class="mt-2 flex items-center">
                <input type="checkbox" name="tiers[${tierCounter}][is_active]" value="1" 
                       checked class="w-3 h-3 text-blue-600 border-gray-300 rounded">
                <label class="ml-2 text-xs text-gray-600">Active Tier</label>
            </div>
        </div>
    `;
    
    tiersContainer.insertAdjacentHTML('beforeend', tierHtml);
}

function removeTier(button) {
    const tierItem = button.closest('.tier-item');
    tierItem.remove();
    
    // Update tier numbers
    const tierItems = document.querySelectorAll('.tier-item');
    tierItems.forEach((item, index) => {
        const tierNumber = index + 1;
        const title = item.querySelector('h4');
        const sortOrderInput = item.querySelector('input[name$="[sort_order]"]');
        
        if (title) title.textContent = `Tier ${tierNumber}`;
        if (sortOrderInput) sortOrderInput.value = tierNumber;
        
        // Update all input names with new index
        item.querySelectorAll('input, textarea').forEach(input => {
            const name = input.name;
            if (name.includes('tiers[')) {
                const newName = name.replace(/tiers\[\d+\]/, `tiers[${tierNumber}]`);
                input.name = newName;
            }
        });
    });
    
    tierCounter = tierItems.length;
}
</script>
@endcan
@endsection