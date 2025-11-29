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
            <p class="text-gray-600 mt-2">Total: {{ $customers->total() }} customers</p>
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
             <!-- PDF Export Button -->
            <form method="GET" action="{{ route('admin.customers.export-pdf') }}" class="inline">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center shadow-md">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Filters and Search Section -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <!-- Status Filter Tabs -->
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

        <!-- Advanced Search -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Quick Search -->
            <div class="lg:col-span-2">
                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex gap-2">
                    <div class="flex-1 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search customers..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Search
                    </button>
                </form>
            </div>

            <!-- Meter Number Search
            <div>
                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex gap-2">
                    <div class="flex-1 relative">
                        <input type="text" 
                               name="meter_number" 
                               value="{{ request('meter_number') }}" 
                               placeholder="Search by meter..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-tachometer-alt absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Find
                    </button>
                </form>
            </div>

            Account Number Search
            <div>
                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex gap-2">
                    <div class="flex-1 relative">
                        <input type="text" 
                               name="account_number" 
                               value="{{ request('account_number') }}" 
                               placeholder="Search by account..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-id-card absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Find
                    </button>
                </form>
            </div> -->
        </div>

        <!-- Active Filters -->
        @if(request()->anyFilled(['search', 'meter_number', 'account_number', 'status']))
        <div class="flex flex-wrap items-center gap-2 p-3 bg-blue-50 rounded-lg">
            <span class="text-sm font-medium text-blue-800">Active filters:</span>
            
            @if(request('search'))
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                Search: "{{ request('search') }}"
                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">
                    <i class="fas fa-times"></i>
                </a>
            </span>
            @endif

            @if(request('meter_number'))
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                Meter: "{{ request('meter_number') }}"
                <a href="{{ request()->fullUrlWithQuery(['meter_number' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </a>
            </span>
            @endif

            @if(request('account_number'))
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                Account: "{{ request('account_number') }}"
                <a href="{{ request()->fullUrlWithQuery(['account_number' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">
                    <i class="fas fa-times"></i>
                </a>
            </span>
            @endif

            @if(request('status'))
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800">
                Status: {{ ucfirst(request('status')) }}
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-1 text-orange-600 hover:text-orange-800">
                    <i class="fas fa-times"></i>
                </a>
            </span>
            @endif

            <a href="{{ route('admin.customers.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium ml-auto">
                Clear all filters
            </a>
        </div>
        @endif
    </div>

    <!-- Customers Table -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
        @if($customers->count())
        
        <!-- Table Stats -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 text-sm text-gray-600">
            <div>
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
            </div>
            <div class="flex items-center space-x-4 mt-2 sm:mt-0">
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                    Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}
                </span>
            </div>
        </div>

        <!-- Mobile Cards View -->
        <div class="block md:hidden space-y-4">
            @foreach($customers as $customer)
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200">
                <!-- Header -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-semibold text-blue-600">{{ $customer->customer_number }}</div>
                        <div class="font-medium text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                    </div>
                    @php
                        $statusColors = [
                            'new' => 'bg-blue-100 text-blue-800',
                            'active' => 'bg-green-500 text-green-800',
                            'pending_payment' => 'bg-yellow-100 text-yellow-800',
                            'sealed' => 'bg-red-100 text-red-800',
                            'terminated' => 'bg-red-500 text-gray-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$customer->status] }}">
                        {{ ucfirst(str_replace('_', ' ', $customer->status)) }}
                    </span>
                </div>

                <!-- Contact & Location -->
                <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                    <div>
                        <div class="text-gray-500">Phone</div>
                        <div class="font-medium">{{ $customer->phone }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Location</div>
                        <div class="font-medium">{{ $customer->plot_number }}</div>
                    </div>
                </div>

                <!-- Meter Info -->
                <div class="mb-3">
                    <div class="text-sm text-gray-500 mb-1">Meter Information</div>
                    @if($customer->meters->count() > 0)
                        @foreach($customer->meters as $meter)
                        <div class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded mb-1">
                            <div>
                                <span class="font-medium">{{ $meter->meter_number }}</span>
                                <span class="text-gray-500 text-xs ml-2">{{ $meter->meterCategory->name ?? 'No Category' }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-xs {{ $meter->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    KSh {{ number_format($meter->current_balance, 2) }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-sm text-red-600 bg-red-50 p-2 rounded">No meter assigned</div>
                    @endif
                </div>

                <!-- Balance -->
                <div class="flex justify-between items-center mb-3 p-2 bg-blue-50 rounded">
                    <span class="text-sm font-medium text-blue-800">Account Balance</span>
                    <span class="font-semibold {{ $customer->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        KSh {{ number_format(abs($customer->current_balance), 2) }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="{{ route('admin.customers.show', $customer) }}"
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded text-sm transition duration-200">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    
                    @if($customer->meters->count() > 0)
                    <a href="{{ route('admin.meter-readings.create', ['customer' => $customer->id]) }}"
                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded text-sm transition duration-200">
                        <i class="fas fa-tachometer-alt mr-1"></i>Reading
                    </a>
                    @else
                    <button onclick="assignMeter({{ $customer->id }})"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-center py-2 rounded text-sm transition duration-200">
                        <i class="fas fa-plus mr-1"></i>Assign Meter
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meters</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-gray-200">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50/50 transition duration-150">
                        <!-- Customer Info -->
                        <td class="px-4 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-mono text-blue-600 font-semibold truncate">
                                        {{ $customer->customer_number }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $customer->plot_number }} • {{ $customer->id_number }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Contact -->
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900 truncate">{{ $customer->phone }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ $customer->email }}</div>
                            <div class="text-xs text-gray-400 capitalize">{{ $customer->connection_type }}</div>
                        </td>

                        <!-- Meters -->
                        <td class="px-4 py-4">
                            @if($customer->meters->count() > 0)
                                <div class="space-y-1">
                                    @foreach($customer->meters as $meter)
                                    <div class="flex justify-between items-center text-sm">
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $meter->meter_number }}</span>
                                            <span class="text-gray-500 text-xs ml-1">• {{ $meter->meterCategory->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs {{ $meter->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                KSh {{ number_format($meter->current_balance, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-sm text-red-600 font-medium">No Meter</div>
                                <div class="text-xs text-gray-500">Click to assign</div>
                            @endif
                        </td>

                        <!-- Balance -->
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold {{ $customer->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                KSh {{ number_format(abs($customer->current_balance), 2) }}
                            </div>
                            @if($customer->arrears > 0)
                                <div class="text-xs text-red-500 mt-1">
                                    Arrears: KSh {{ number_format($customer->arrears, 2) }}
                                </div>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-4">
                            @php
                                $statusColors = [
                                    'new' => 'bg-blue-100 text-blue-800',
                                    'active' => 'bg-green-500 text-green-800',
                                    'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                    'sealed' => 'bg-red-100 text-red-800',
                                    'terminated' => 'bg-red-500 text-gray-800',
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
                        <td class="px-4 py-4">
                            <div class="flex flex-col space-y-2 min-w-[120px]">
                                <a href="{{ route('admin.customers.show', $customer) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                                
                                @if($customer->meters->count() > 0)
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
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                @if(request()->anyFilled(['search', 'meter_number', 'account_number', 'status']))
                    No customers found
                @else
                    No customers yet
                @endif
            </h3>
            <p class="text-gray-500 mb-4">
                @if(request()->anyFilled(['search', 'meter_number', 'account_number', 'status']))
                    No customers match your search criteria. Try adjusting your filters.
                @else
                    Get started by adding your first customer.
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
    <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
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
                    <div id="availableMetersList" class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        <p class="text-gray-500 text-sm">Select a category to view available meters</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Initial Reading</label>
                        <input type="number" step="0.01" name="initial_reading" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00" value="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Balance B/F</label>
                        <input type="number" step="0.01" name="balance_bf" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00" value="0">
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
    document.body.classList.add('overflow-hidden');
}

function closeMeterModal() {
    const modal = document.getElementById('meterAssignmentModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentCustomerId = null;
    
    // Reset form
    const form = document.getElementById('assignMeterForm');
    form.reset();
    document.getElementById('availableMetersList').innerHTML = '<p class="text-gray-500 text-sm">Select a category to view available meters</p>';
}

// Load available meters when category changes
document.getElementById('meterCategorySelect')?.addEventListener('change', function() {
    const categoryId = this.value;
    const metersList = document.getElementById('availableMetersList');
    
    if (!categoryId) {
        metersList.innerHTML = '<p class="text-gray-500 text-sm">Select a category to view available meters</p>';
        return;
    }
    
    metersList.innerHTML = '<div class="flex items-center justify-center space-x-2 text-gray-500 py-8"><i class="fas fa-spinner fa-spin"></i><span>Loading meters...</span></div>';
    
    fetch(`/admin/customers/get-available-meters?category_id=${categoryId}`)
        .then(response => response.json())
        .then(meters => {
            if (meters.length === 0) {
                metersList.innerHTML = '<div class="text-center text-gray-500 py-8"><i class="fas fa-inbox text-2xl mb-2 opacity-50"></i><p class="text-sm">No available meters in this category</p></div>';
                return;
            }
            
            metersList.innerHTML = meters.map(meter => `
                <div class="border border-gray-200 rounded-lg p-3 hover:border-blue-300 cursor-pointer transition duration-200 meter-option" data-meter-id="${meter.id}">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">${meter.meter_number}</div>
                            <div class="text-sm text-gray-500">${meter.meter_type} • ${meter.meter_model || 'Standard'}</div>
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
                        opt.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
                    });
                    
                    // Add selection to clicked option
                    this.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
                    
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
            metersList.innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p class="text-sm">Error loading meters</p></div>';
            console.error('Error:', error);
        });
});

// Close modal when clicking outside
document.getElementById('meterAssignmentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMeterModal();
    }
});

// Keyboard shortcut for search
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('input[name="search"]').focus();
    }
});
</script>

<style>
.meter-option {
    transition: all 0.2s ease;
}

.meter-option:hover {
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .bg-white\/80 {
        background: rgba(255, 255, 255, 0.95);
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-white\/80 {
        background: white !important;
    }
}
</style>
@endcan
@endsection