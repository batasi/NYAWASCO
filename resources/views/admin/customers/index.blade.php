@extends('layouts.app')

@section('title', 'Customers - NYAWASCO')

@section('content')
@can('view customers')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-blue-800">Customer Management</h1>
            <p class="text-gray-600 mt-2">Manage all customer accounts, statuses, and meter assignments</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            @can('add customers')
            <a href="{{ route('admin.customers.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i>
                Add Customer
            </a>
            @endcan
            
            <a href="{{ route('admin.water-applications.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center shadow-md">
                <i class="fas fa-folder-open mr-2"></i>
                View Applications
            </a>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('admin.customers.index') }}" 
               class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                All <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs ml-1">{{ $statusCounts['all'] }}</span>
            </a>
            @foreach(['new', 'active', 'pending_payment', 'sealed', 'terminated'] as $status)
            <a href="{{ route('admin.customers.index', ['status' => $status]) }}" 
               class="px-4 py-2 rounded-lg font-medium transition duration-200 {{ request('status') == $status ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                {{ ucfirst(str_replace('_', ' ', $status)) }} 
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs ml-1">{{ $statusCounts[$status] }}</span>
            </a>
            @endforeach
        </div>

        <!-- Search Box -->
        <div class="mb-6">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex gap-2">
                <div class="flex-1 relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name, account number, phone, meter number..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                    Search
                </button>
                @if(request('search') || request('status'))
                <a href="{{ route('admin.customers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    Clear
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
        @if($customers->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter & Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-gray-200">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50/50 transition duration-150">
                        <!-- Account Info -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-mono text-blue-600 font-semibold">{{ $customer->customer_number }}</div>
                            <div class="text-sm font-medium text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $customer->plot_number }}, {{ $customer->house_number }}</div>
                            <div class="text-xs text-gray-400">{{ $customer->id_number }}</div>
                        </td>

                        <!-- Contact -->
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $customer->phone }}</div>
                            <div class="text-sm text-gray-500 break-all">{{ $customer->email }}</div>
                            <div class="text-xs text-gray-400">{{ $customer->connection_type }}</div>
                        </td>

                        <!-- Meter & Category -->
                        <td class="px-6 py-4">
                            @if($customer->meter)
                                <div class="text-sm font-medium text-gray-900">{{ $customer->meter->meter_number }}</div>
                                <div class="text-sm text-gray-500">{{ $customer->meter->meterCategory->name ?? 'No Category' }}</div>
                                <div class="text-xs text-gray-400 capitalize">{{ $customer->meter->meter_type }}</div>
                            @else
                                <div class="text-sm text-red-600 font-medium">No Meter</div>
                                <div class="text-xs text-gray-500">Not assigned</div>
                            @endif
                        </td>

                        <!-- Balance -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold {{ $customer->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                KSh {{ number_format(abs($customer->current_balance), 2) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                B/F: KSh {{ number_format($customer->balance_bf, 2) }}
                            </div>
                            @if($customer->arrears > 0)
                                <div class="text-xs text-red-500">
                                    Arrears: KSh {{ number_format($customer->arrears, 2) }}
                                </div>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'new' => 'bg-blue-100 text-blue-800',
                                    'active' => 'bg-green-100 text-green-800',
                                    'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                    'sealed' => 'bg-red-100 text-red-800',
                                    'terminated' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$customer->status] }}">
                                <i class="fas fa-circle mr-1 text-xs"></i>
                                {{ ucfirst(str_replace('_', ' ', $customer->status)) }}
                            </span>
                            
                            @if($customer->status === 'new' && !$customer->canBeActivated())
                                <div class="mt-1 text-xs text-red-600">
                                    Requirements pending
                                </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('admin.customers.show', $customer) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                                
                                @if($customer->meter)
                                <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-tachometer-alt mr-1"></i>
                                    Reading
                                </a>
                                @else
                                <button onclick="assignMeter({{ $customer->id }})"
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-plus mr-1"></i>
                                    Assign Meter
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $customers->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                @if(request('search'))
                    No customers found
                @else
                    No customers in this status
                @endif
            </h3>
            <p class="text-gray-500 mb-4">
                @if(request('search'))
                    No customers match your search criteria.
                @else
                    There are no customers with the selected status.
                @endif
            </p>
            @can('add customers')
            <a href="{{ route('admin.customers.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add New Customer
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>

<!-- Meter Assignment Modal -->
<div id="meterAssignmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Assign Meter to Customer</h3>
            <button onclick="closeMeterModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="assignMeterForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Meter Category</label>
                    <select id="meterCategorySelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                        @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Available Meters</label>
                    <div id="availableMetersList" class="space-y-2 max-h-60 overflow-y-auto">
                        <p class="text-gray-500 text-sm">Select a category to view available meters</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Initial Reading</label>
                        <input type="number" step="0.01" name="initial_reading" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Balance B/F</label>
                        <input type="number" step="0.01" name="balance_bf" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Installation Date</label>
                    <input type="date" name="installation_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ date('Y-m-d') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any installation notes..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeMeterModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Assign Meter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentCustomerId = null;

function assignMeter(customerId) {
    currentCustomerId = customerId;
    const modal = document.getElementById('meterAssignmentModal');
    const form = document.getElementById('assignMeterForm');
    
    form.action = `/admin/customers/${customerId}/assign-meter`;
    modal.classList.remove('hidden');
}

function closeMeterModal() {
    const modal = document.getElementById('meterAssignmentModal');
    modal.classList.add('hidden');
    currentCustomerId = null;
}

// Load available meters when category changes
document.getElementById('meterCategorySelect')?.addEventListener('change', function() {
    const categoryId = this.value;
    const metersList = document.getElementById('availableMetersList');
    
    if (!categoryId) {
        metersList.innerHTML = '<p class="text-gray-500 text-sm">Select a category to view available meters</p>';
        return;
    }
    
    metersList.innerHTML = '<p class="text-gray-500 text-sm">Loading meters...</p>';
    
    fetch(`/admin/customers/get-available-meters?category_id=${categoryId}`)
        .then(response => response.json())
        .then(meters => {
            if (meters.length === 0) {
                metersList.innerHTML = '<p class="text-red-500 text-sm">No available meters in this category</p>';
                return;
            }
            
            metersList.innerHTML = meters.map(meter => `
                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer meter-option" data-meter-id="${meter.id}">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-medium text-gray-900">${meter.meter_number}</div>
                            <div class="text-sm text-gray-500">${meter.meter_type} • ${meter.meter_model}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-green-600">Available</div>
                            <div class="text-xs text-gray-500">Init: ${meter.initial_reading}m³</div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Add click handlers
            document.querySelectorAll('.meter-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Remove previous selection
                    document.querySelectorAll('.meter-option').forEach(opt => {
                        opt.classList.remove('border-blue-500', 'bg-blue-50');
                    });
                    
                    // Add selection to clicked option
                    this.classList.add('border-blue-500', 'bg-blue-50');
                    
                    // Add hidden input for meter_id
                    let meterInput = document.querySelector('input[name="meter_id"]');
                    if (!meterInput) {
                        meterInput = document.createElement('input');
                        meterInput.type = 'hidden';
                        meterInput.name = 'meter_id';
                        document.getElementById('assignMeterForm').appendChild(meterInput);
                    }
                    meterInput.value = this.dataset.meterId;
                });
            });
        })
        .catch(error => {
            metersList.innerHTML = '<p class="text-red-500 text-sm">Error loading meters</p>';
            console.error('Error:', error);
        });
});

// Close modal when clicking outside
document.getElementById('meterAssignmentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMeterModal();
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