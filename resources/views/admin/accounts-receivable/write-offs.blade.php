@extends('layouts.app')

@section('title', 'Write-offs Management - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>
<div class="min-h-screen bg-gray-50">
    @php
    $actionButtons = [

    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Write-offs Management',
        'subtitle' => 'Manage Bad Debt and Adjustments',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Buttons</h3>

            <div class="flex flex-wrap gap-3">
                <button onclick="showCreateWriteOffModal()"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center mt-4">
                    <i class="fas fa-plus mr-2"></i>
                   Create Write off
                </button>


            </div>
        </div>
        <!-- Filters -->
        <div class="bg-white/80 mt-2 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <form method="GET" action="{{ route('admin.accounts-receivable.write-offs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="reversed" {{ request('status') == 'reversed' ? 'selected' : '' }}>Reversed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="bad_debt" {{ request('type') == 'bad_debt' ? 'selected' : '' }}>Bad Debt</option>
                        <option value="dispute" {{ request('type') == 'dispute' ? 'selected' : '' }}>Dispute</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        <option value="waiver" {{ request('type') == 'waiver' ? 'selected' : '' }}>Waiver</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-4 flex justify-end space-x-3">
                    <a href="{{ route('admin.accounts-receivable.write-offs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Reset
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Write-offs</p>
                    <p class="text-2xl font-bold text-red-600">
                        KSh {{ number_format($writeOffs->sum('amount'), 2) }}
                    </p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $writeOffs->where('status', 'pending')->count() }}
                    </p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $writeOffs->where('status', 'approved')->count() }}
                    </p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Bad Debt Ratio</p>
                    <p class="text-2xl font-bold text-purple-600">
                        @php
                            $totalReceivables = App\Models\Bill::where('bill_status', '!=', 'paid')->sum('balance');
                            $badDebt = $writeOffs->where('type', 'bad_debt')->where('status', 'approved')->sum('amount');
                            $ratio = $totalReceivables > 0 ? round(($badDebt / $totalReceivables) * 100, 2) : 0;
                        @endphp
                        {{ $ratio }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Write-offs Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Write-offs</h3>
                    <p class="text-sm text-gray-600">Showing {{ $writeOffs->count() }} of {{ $writeOffs->total() }} records</p>
                </div>

                <div class="flex space-x-3 mt-2 sm:mt-0">
                    <button onclick="exportWriteOffs()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export
                    </button>
                </div>
            </div>

            @if($writeOffs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Write-off Details</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        @foreach($writeOffs as $writeOff)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-4 py-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $writeOff->customer->first_name }} {{ $writeOff->customer->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $writeOff->customer->customer_number }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $writeOff->customer->phone }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="min-w-0">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $writeOff->type == 'bad_debt' ? 'bg-red-100 text-red-800' :
                                               ($writeOff->type == 'dispute' ? 'bg-yellow-100 text-yellow-800' :
                                               ($writeOff->type == 'adjustment' ? 'bg-blue-100 text-blue-800' :
                                               'bg-green-100 text-green-800')) }} mr-2">
                                            {{ ucfirst(str_replace('_', ' ', $writeOff->type)) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($writeOff->write_off_date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-700 mt-1 line-clamp-2" title="{{ $writeOff->reason }}">
                                        {{ $writeOff->reason }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-lg font-semibold text-red-600">
                                    KSh {{ number_format($writeOff->amount, 2) }}
                                </div>
                                @if($writeOff->affected_bills && count($writeOff->affected_bills) > 0)
                                <div class="text-xs text-gray-500">
                                    {{ count($writeOff->affected_bills) }} bills
                                </div>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $writeOff->status == 'approved' ? 'bg-green-100 text-green-800' :
                                       ($writeOff->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                       ($writeOff->status == 'rejected' ? 'bg-red-100 text-red-800' :
                                       'bg-gray-100 text-gray-800')) }}">
                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                    {{ ucfirst($writeOff->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                @if($writeOff->approved_by)
                                <div class="text-sm text-gray-900">
                                    {{ $writeOff->approver->name ?? 'System' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($writeOff->approved_at)->format('M d, Y') }}
                                </div>
                                @else
                                <span class="text-sm text-gray-400">Pending</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex space-x-2">
                                    <!-- <button onclick="viewWriteOff({{ $writeOff->id }})"
                                            class="text-blue-600 hover:text-blue-800" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button> -->

                                    @if($writeOff->status == 'pending')
                                    <button onclick="approveWriteOff({{ $writeOff->id }})"
                                            class="text-green-600 hover:text-green-800" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>

                                    <button onclick="rejectWriteOff({{ $writeOff->id }})"
                                            class="text-red-600 hover:text-red-800" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif

                                    @if($writeOff->status == 'approved')
                                    <button onclick="reverseWriteOff({{ $writeOff->id }})"
                                            class="text-purple-600 hover:text-purple-800" title="Reverse">
                                        <i class="fas fa-undo"></i>
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
                {{ $writeOffs->links() }}
            </div>

            @else
            <div class="text-center py-12">
                <i class="fas fa-trash-alt text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No write-offs found</h3>
                <p class="text-gray-500">
                    @if(request()->anyFilled(['status', 'type', 'date_from']))
                        No write-offs match your filters.
                    @else
                        Start by creating your first write-off.
                    @endif
                </p>

                <button onclick="showCreateWriteOffModal()"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center mt-4">
                    <i class="fas fa-plus mr-2"></i>
                    Create Write-off
                </button>

            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Write-off Modal -->
<div id="createWriteOffModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Create New Write-off</h3>
            <button onclick="closeCreateWriteOffModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="createWriteOffForm" method="POST" action="{{ route('admin.accounts-receivable.write-offs.store') }}">
            @csrf
            <div class="space-y-4">
                <!-- Customer Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Customer *</label>
                    <div class="relative">
                        <input type="text"
                            id="customerSearch"
                            name="customer_search"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Search by Meter No, Phone, ID, or Name..."
                            autocomplete="off">
                        <div class="absolute left-3 top-3.5 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="absolute right-3 top-3.5">
                            <span id="searchSpinner" class="hidden">
                                <i class="fas fa-spinner fa-spin text-blue-600"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Search Results Container -->
                    <div id="searchResults"
                        class="hidden mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>

                    <!-- Hidden field for selected customer -->
                    <input type="hidden" name="customer_id" id="selectedCustomerId" required>
                </div>

                <!-- Selected Customer Info Display -->
                <div id="selectedCustomerInfo" class="hidden mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-blue-900" id="selectedCustomerName"></h4>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 mt-2 text-sm">
                                <div class="flex items-center">
                                    <span class="text-gray-600 w-24">Account No:</span>
                                    <span class="font-medium" id="selectedCustomerNumber"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-600 w-24">Phone:</span>
                                    <span class="font-medium" id="selectedCustomerPhone"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-600 w-24">ID No:</span>
                                    <span class="font-medium" id="selectedCustomerIdNumber"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-600 w-24">Balance Due:</span>
                                    <span class="font-medium text-red-600" id="selectedCustomerBalance"></span>
                                </div>
                            </div>
                        </div>
                        <button type="button"
                                onclick="clearCustomerSelection()"
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Write-off Type *</label>
                        <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select Type</option>
                            <option value="bad_debt">Bad Debt</option>
                            <option value="dispute">Dispute</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="waiver">Waiver</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                        <input type="number" step="0.01" name="amount" id="writeOffAmount" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason *</label>
                    <input type="text" name="reason" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Brief reason for write-off..." required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Detailed description..." required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Write-off Date *</label>
                        <input type="date" name="write_off_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <!-- Bills Selection -->
                <div id="billsSection" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Bills to Write-off (Optional)</label>
                    <div id="customerBills" class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        <p class="text-gray-500 text-sm">Select a customer to view their bills</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Write-offs reduce receivables and affect financial statements. Ensure proper authorization.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeCreateWriteOffModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Submit for Approval
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
});

function setupEventListeners() {
    // Customer search functionality
    const customerSearch = document.getElementById('customerSearch');
    if (customerSearch) {
        let searchTimeout;
        customerSearch.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.trim();

            if (searchTerm.length < 2) {
                document.getElementById('searchResults').classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                performCustomerSearch(searchTerm);
            }, 300);
        });
    }

    // Form submission
    const createWriteOffForm = document.getElementById('createWriteOffForm');
    if (createWriteOffForm) {
        createWriteOffForm.addEventListener('submit', function(e) {
            if (!document.getElementById('selectedCustomerId').value) {
                e.preventDefault();
                showToast('Please select a customer first', 'error');
                return false;
            }

            // Validate amount
            const amount = parseFloat(document.getElementById('writeOffAmount').value);
            const customerBalance = parseFloat(document.getElementById('selectedCustomerBalance')?.textContent?.replace(/[^0-9.-]+/g,"") || 0);

            if (amount > customerBalance) {
                e.preventDefault();
                showToast('Write-off amount cannot exceed customer balance', 'error');
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';

            return true;
        });
    }
}

function showCreateWriteOffModal() {
    const modal = document.getElementById('createWriteOffModal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeCreateWriteOffModal() {
    const modal = document.getElementById('createWriteOffModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    resetWriteOffForm();
}

function resetWriteOffForm() {
    document.getElementById('createWriteOffForm').reset();
    document.getElementById('selectedCustomerInfo').classList.add('hidden');
    document.getElementById('searchResults').classList.add('hidden');
    document.getElementById('selectedCustomerId').value = '';
    document.getElementById('billsSection').classList.add('hidden');
    document.getElementById('customerBills').innerHTML = '<p class="text-gray-500 text-sm">Select a customer to view their bills</p>';
}

async function performCustomerSearch(searchTerm) {
    const spinner = document.getElementById('searchSpinner');
    const resultsContainer = document.getElementById('searchResults');

    spinner.classList.remove('hidden');

    try {
        const response = await fetch('{{ route("admin.accounts-receivable.search-customer") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ search: searchTerm })
        });

        const data = await response.json();

        if (data.success && data.customers.length > 0) {
            displaySearchResults(data.customers);
        } else {
            resultsContainer.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-search fa-lg mb-2"></i>
                    <p>No customers found</p>
                </div>
            `;
            resultsContainer.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Search error:', error);
        resultsContainer.innerHTML = `
            <div class="p-4 text-center text-red-500">
                <i class="fas fa-exclamation-triangle fa-lg mb-2"></i>
                <p>Search failed. Please try again.</p>
            </div>
        `;
        resultsContainer.classList.remove('hidden');
    } finally {
        spinner.classList.add('hidden');
    }
}

function displaySearchResults(customers) {
    const resultsContainer = document.getElementById('searchResults');
    let html = '';

    customers.forEach(customer => {
        const balance = parseFloat(customer.balance);
        const balanceClass = balance > 10000 ? 'bg-red-100 text-red-800' :
                            balance > 5000 ? 'bg-orange-100 text-orange-800' :
                            'bg-yellow-100 text-yellow-800';

        html += `
            <div class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                 onclick="selectCustomerForWriteOff(${JSON.stringify(customer).replace(/"/g, '&quot;')})">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <span class="font-medium text-gray-900">${customer.name}</span>
                            <span class="ml-2 text-xs px-2 py-0.5 ${balanceClass} rounded-full">
                                KSh ${balance.toLocaleString('en-US', {minimumFractionDigits: 2})}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-4 text-xs text-gray-600 mt-1">
                            <div class="truncate">
                                <i class="fas fa-hashtag mr-1"></i>
                                ${customer.customer_number}
                            </div>
                            <div class="truncate">
                                <i class="fas fa-phone mr-1"></i>
                                ${customer.phone || 'N/A'}
                            </div>
                            <div class="truncate">
                                <i class="fas fa-id-card mr-1"></i>
                                ${customer.id_number || 'N/A'}
                            </div>
                            <div class="truncate">
                                <i class="fas fa-tachometer-alt mr-1"></i>
                                ${customer.meter_numbers || 'N/A'}
                            </div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 mt-1"></i>
                </div>
            </div>
        `;
    });

    resultsContainer.innerHTML = html;
    resultsContainer.classList.remove('hidden');
}

function selectCustomerForWriteOff(customer) {
    // Set hidden field
    document.getElementById('selectedCustomerId').value = customer.id;

    // Update display
    document.getElementById('selectedCustomerName').textContent = customer.name;
    document.getElementById('selectedCustomerNumber').textContent = customer.customer_number;
    document.getElementById('selectedCustomerPhone').textContent = customer.phone || 'N/A';
    document.getElementById('selectedCustomerIdNumber').textContent = customer.id_number || 'N/A';
    document.getElementById('selectedCustomerBalance').textContent =
        'KSh ' + parseFloat(customer.balance).toLocaleString('en-US', {minimumFractionDigits: 2});

    // Show selected customer info, hide search results
    document.getElementById('selectedCustomerInfo').classList.remove('hidden');
    document.getElementById('searchResults').classList.add('hidden');
    document.getElementById('customerSearch').value = '';

    // Load customer bills
    loadCustomerBills(customer.id);
}

function loadCustomerBills(customerId) {
    const billsContainer = document.getElementById('customerBills');
    const billsSection = document.getElementById('billsSection');

    billsSection.classList.remove('hidden');
    billsContainer.innerHTML = '<div class="flex items-center justify-center space-x-2 text-gray-500 py-8"><i class="fas fa-spinner fa-spin"></i><span>Loading bills...</span></div>';

    fetch(`/admin/accounts-receivable/write-offs/customers/${customerId}/bills`)
        .then(response => response.json())
        .then(bills => {
            if (bills.length === 0) {
                billsContainer.innerHTML = '<p class="text-gray-500 text-sm">No unpaid bills found for this customer</p>';
                return;
            }

            billsContainer.innerHTML = bills.map(bill => `
                <div class="border border-gray-200 rounded-lg p-3 hover:border-blue-300 transition">
                    <div class="flex items-center">
                        <input type="checkbox" name="bill_ids[]" value="${bill.id}" class="mr-3 h-4 w-4 text-blue-600">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">${bill.bill_number}</div>
                            <div class="text-sm text-gray-500">
                                Due: ${bill.due_date} • Balance: KSh ${bill.balance}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading bills:', error);
            billsContainer.innerHTML = '<p class="text-red-500 text-sm">Error loading bills</p>';
        });
}

function clearCustomerSelection() {
    document.getElementById('selectedCustomerId').value = '';
    document.getElementById('selectedCustomerInfo').classList.add('hidden');
    document.getElementById('customerSearch').value = '';
    document.getElementById('billsSection').classList.add('hidden');
    document.getElementById('customerBills').innerHTML = '<p class="text-gray-500 text-sm">Select a customer to view their bills</p>';
}

function viewWriteOff(writeOffId) {
    window.location.href = `/admin/accounts-receivable/write-offs/${writeOffId}`;
}

function approveWriteOff(writeOffId) {
    if (confirm('Are you sure you want to approve this write-off?')) {
        fetch(`/admin/accounts-receivable/write-offs/${writeOffId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Write-off approved successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to approve write-off', 'error');
        });
    }
}

function rejectWriteOff(writeOffId) {
    const reason = prompt('Please enter reason for rejection:');
    if (reason) {
        fetch(`/admin/accounts-receivable/write-offs/${writeOffId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Write-off rejected successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to reject write-off', 'error');
        });
    }
}

function reverseWriteOff(writeOffId) {
    if (confirm('Are you sure you want to reverse this write-off? This action cannot be undone.')) {
        fetch(`/admin/accounts-receivable/write-offs/${writeOffId}/reverse`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Write-off reversed successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to reverse write-off', 'error');
        });
    }
}

function exportWriteOffs() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/admin/accounts-receivable/write-offs/export?${params.toString()}`;
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-50 animate-slide-in ${
        type === 'success' ? 'bg-green-600' :
        type === 'error' ? 'bg-red-600' :
        'bg-blue-600'
    }`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('animate-slide-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateWriteOffModal();
    }
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        showCreateWriteOffModal();
    }
});

// Close modal when clicking outside
document.getElementById('createWriteOffModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateWriteOffModal();
    }
});

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slide-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }

    .animate-slide-out {
        animation: slide-out 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
@endsection
