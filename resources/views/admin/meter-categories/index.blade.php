@extends('layouts.app')

@section('title', 'Meter Categories - NYAWASCO')

@section('content')

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add meters')) {
        $actionButtons[] = [
            'text' => 'Add Category',
            'onclick' => 'openCategoryModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }

    if (auth()->user()->can('view meters')) {
        $actionButtons[] = [
            'text' => 'Back to Meters',
            'href' => route('admin.meters.index'),
            'icon' => 'fas fa-arrow-left',
            'color' => 'bg-blue-600 hover:bg-blue-700'
        ];
    }
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Meter Categories Management',
        'subtitle' => 'Manage meter categories and pricing tiers',
        'actionButtons' => $actionButtons
    ])

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $categories->total() }}</div>
            <div class="text-gray-600">Total Categories</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $categories->where('is_active', true)->count() }}</div>
            <div class="text-gray-600">Active Categories</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-purple-600">{{ $categories->where('has_tiers', true)->count() }}</div>
            <div class="text-gray-600">Tiered Pricing</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-orange-600">{{ $categories->sum('meters_count') }}</div>
            <div class="text-gray-600">Total Meters</div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($categories as $category)
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 hover:shadow-xl transition duration-300">
            <!-- Category Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-lg p-4 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-bold">{{ $category->name }}</h3>
                        <p class="text-blue-100 text-sm">{{ $category->code }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($category->has_tiers)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Tiered
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Category Body -->
            <div class="p-4">
                <p class="text-gray-600 text-sm mb-4">{{ $category->description ?? 'No description' }}</p>
                
                <!-- Pricing Information -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Base Charge:</span>
                        <span class="font-semibold">KSh {{ number_format($category->base_charge, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Meter Rent:</span>
                        <span class="font-semibold">KSh {{ number_format($category->meter_rent, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Default Rate:</span>
                        <span class="font-semibold">KSh {{ number_format($category->default_rate, 4) }}/m³</span>
                    </div>
                </div>

                <!-- Tiers Preview -->
                @if($category->has_tiers && $category->pricingTiers->count() > 0)
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Pricing Tiers:</h4>
                    <div class="space-y-1">
                        @foreach($category->pricingTiers->take(2) as $tier)
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">{{ $tier->consumption_range }}:</span>
                            <span class="font-medium">KSh {{ number_format($tier->rate_per_unit, 4) }}/m³</span>
                        </div>
                        @endforeach
                        @if($category->pricingTiers->count() > 2)
                        <div class="text-xs text-gray-500 text-center">
                            +{{ $category->pricingTiers->count() - 2 }} more tiers
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Additional Charges -->
                @if($category->additional_charges)
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Additional Charges:</h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @if($category->additional_charges['installation_fee'] ?? 0)
                        <div class="text-gray-600">Installation:</div>
                        <div class="font-medium">KSh {{ number_format($category->additional_charges['installation_fee'], 2) }}</div>
                        @endif
                        @if($category->additional_charges['connection_fee'] ?? 0)
                        <div class="text-gray-600">Connection:</div>
                        <div class="font-medium">KSh {{ number_format($category->additional_charges['connection_fee'], 2) }}</div>
                        @endif
                        @if($category->additional_charges['deposit'] ?? 0)
                        <div class="text-gray-600">Deposit:</div>
                        <div class="font-medium">KSh {{ number_format($category->additional_charges['deposit'], 2) }}</div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Meters Count -->
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">{{ $category->meters_count }} meters</span>
                    <span class="text-gray-500">Order: {{ $category->sort_order }}</span>
                </div>
            </div>

            <!-- Category Footer -->
            <div class="border-t border-gray-200 px-4 py-3 bg-gray-50 rounded-b-lg">
                <div class="flex justify-between items-center">
                    <a href="{{ route('admin.meter-categories.show', $category) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                        <i class="fas fa-eye mr-1"></i> View Details
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.meter-categories.edit', $category) }}" 
                           class="text-green-600 hover:text-green-800">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.meter-categories.destroy', $category) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this category?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 rounded-lg shadow">
        {{ $categories->links() }}
    </div>
    @endif

    <!-- Empty State -->
    @if($categories->count() == 0)
    <div class="text-center py-12">
        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-tags text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
        <p class="text-gray-500 mb-6">Get started by creating your first meter category.</p>
        <button onclick="openCategoryModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Create First Category
        </button>
    </div>
    @endif
</div>

<!-- Create Category Modal -->
<div id="createCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-xl font-bold text-blue-700">Create New Category</h3>
                <button type="button" onclick="closeCategoryModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('admin.meter-categories.store') }}" method="POST" class="p-4 md:p-5">
                @csrf
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                            <input type="text" name="name" id="name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                   placeholder="e.g., Domestic, Commercial">
                        </div>

                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Category Code *</label>
                            <input type="text" name="code" id="code" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                   placeholder="e.g., DOM, COM" maxlength="10">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                  placeholder="Brief description of this category..."></textarea>
                    </div>

                    <!-- Pricing Configuration -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Pricing Configuration</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="base_charge" class="block text-sm font-medium text-gray-700 mb-1">Base Charge (KSh) *</label>
                                <input type="number" name="base_charge" id="base_charge" step="0.01" min="0" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                       placeholder="0.00" value="100.00">
                            </div>

                            <div>
                                <label for="meter_rent" class="block text-sm font-medium text-gray-700 mb-1">Meter Rent (KSh) *</label>
                                <input type="number" name="meter_rent" id="meter_rent" step="0.01" min="0" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                       placeholder="0.00" value="50.00">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="default_rate" class="block text-sm font-medium text-gray-700 mb-1">Default Rate (KSh/m³) *</label>
                                <input type="number" name="default_rate" id="default_rate" step="0.0001" min="0" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                       placeholder="0.0000" value="0.0800">
                            </div>

                            <div class="flex items-center">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="has_tiers" id="has_tiers" value="1"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <label for="has_tiers" class="ml-2 text-sm text-gray-700">
                                    Enable Tiered Pricing
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Charges -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Additional Charges</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="installation_fee" class="block text-sm font-medium text-gray-700 mb-1">Installation Fee (KSh)</label>
                                <input type="number" name="installation_fee" id="installation_fee" step="0.01" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                       placeholder="0.00" value="2500.00">
                            </div>

                            <div>
                                <label for="connection_fee" class="block text-sm font-medium text-gray-700 mb-1">Connection Fee (KSh)</label>
                                <input type="number" name="connection_fee" id="connection_fee" step="0.01" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                       placeholder="0.00" value="2500.00">
                            </div>

                            <div>
                                <label for="deposit" class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount (KSh)</label>
                                <input type="number" name="deposit" id="deposit" step="0.01" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                       placeholder="0.00" value="2500.00">
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                   placeholder="0" value="0">
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <label for="is_active" class="ml-2 text-sm text-gray-700">
                                Active Category
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6 pt-4 border-t">
                    <button type="button" onclick="closeCategoryModal()" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200 order-2 sm:order-1">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition duration-200 order-1 sm:order-2">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCategoryModal() {
    document.getElementById('createCategoryModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeCategoryModal() {
    document.getElementById('createCategoryModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Close modal when clicking outside
document.getElementById('createCategoryModal').addEventListener('click', function(e) {
    if (e.target.id === 'createCategoryModal') {
        closeCategoryModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCategoryModal();
    }
});
</script>
@endsection