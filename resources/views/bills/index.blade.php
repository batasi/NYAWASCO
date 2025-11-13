@extends('layouts.app')



@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">
                        Billings Management
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                      Financial Management Platform
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <button onclick="openBillModal()"
                        class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Bill
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Filters and Search Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Bills</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" placeholder="Search by bill number, location..."
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 pr-12 py-3 text-sm border-gray-300 rounded-md">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Location Filter -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" id="location" placeholder="Subcounty, village..."
                           class="focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <!-- Additional Filters -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="verified" class="block text-sm font-medium text-gray-700 mb-2">Verification</label>
                    <select id="verified" name="verified" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="all">All Customers</option>
                        <option value="verified">Verified Only</option>
                        <option value="featured">Featured Only</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sort" name="sort" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">

                        <option value="newest">Newest First</option>

                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" id="applyFilters" class="w-full bg-blue-600 text-white px-4 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-50 border-b border-blue-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Bill #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Billing Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Consumption</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-blue-100">
                    @forelse ($bills as $bill)
                    <tr class="hover:bg-blue-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $bill->bill_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $bill->user?->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $bill->user?->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $bill->billing_period_start ? $bill->billing_period_start->format('M d') : '—' }} -
                            {{ $bill->billing_period_end ? $bill->billing_period_end->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($bill->consumption, 2) }} m³</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">KSh {{ number_format($bill->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'unpaid' => 'bg-blue-100 text-blue-800',
                                    'partial' => 'bg-blue-100 text-blue-800'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bill->bill_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($bill->bill_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bill->due_date ? $bill->due_date->format('M d, Y') : '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <!-- View Button -->
                                <a href="{{ route('bills.show', $bill->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <!-- Edit Button -->
                                <a href="{{ route('bills.edit', $bill->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <!-- Delete Button -->
                                <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" onsubmit="return confirm('Delete this bill?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors duration-200">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No bills found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-blue-100 bg-blue-50">
            {{ $bills->links() }}
        </div>


    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const applyFilters = document.getElementById('applyFilters');
    const searchInput = document.getElementById('search');
    const locationInput = document.getElementById('location');
    const verifiedSelect = document.getElementById('verified');
    const sortSelect = document.getElementById('sort');

    function applyOrganizerFilters() {
        const params = new URLSearchParams();

        if (searchInput.value) params.append('search', searchInput.value);
        if (locationInput.value) params.append('location', locationInput.value);
        if (verifiedSelect.value !== 'all') params.append('verified', verifiedSelect.value);
        if (sortSelect.value !== 'popular') params.append('sort', sortSelect.value);

        window.location.href = '{{ route('organizers.index') }}?' + params.toString();
    }

    applyFilters.addEventListener('click', applyOrganizerFilters);

    // Enter key support for search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyOrganizerFilters();
        }
    });

    // Auto-apply sort
    sortSelect.addEventListener('change', applyOrganizerFilters);
});
</script>

<style>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}
</style>
<!-- Modal -->
<div id="billModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-2xl mx-auto my-8">
        <!-- Header -->
        <div class="flex justify-between items-center border-b px-6 py-4 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-semibold text-gray-800">Create New Bill</h3>
            <button onclick="closeBillModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        <!-- Scrollable content -->
        <div class="max-h-[75vh] overflow-y-auto px-6 pb-6">
            <form action="{{ route('bills.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <!-- User -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User</label>
                        <select name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                        </select>
                    </div>

                    <!-- Meter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meter</label>
                        <select name="meter_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                        </select>
                    </div>

                    <!-- Billing Period -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Billing Period Start</label>
                        <input type="date" name="billing_period_start" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Billing Period End</label>
                        <input type="date" name="billing_period_end" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <!-- Charges -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Consumption (m³)</label>
                        <input type="number" step="0.01" name="consumption" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Base Charge</label>
                        <input type="number" step="0.01" name="base_charge" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Consumption Charge</label>
                        <input type="number" step="0.01" name="consumption_charge" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tax Amount</label>
                        <input type="number" step="0.01" name="tax_amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Late Fee</label>
                        <input type="number" step="0.01" name="late_fee" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <!-- Totals -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="date" name="due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="bill_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                        <input type="date" name="payment_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <!-- Notes -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-6 flex justify-end space-x-2 border-t pt-4 sticky bottom-0 bg-white">
                    <button type="button" onclick="closeBillModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Bill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openBillModal() {
        const modal = document.getElementById('billModal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeBillModal() {
        const modal = document.getElementById('billModal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close when clicking outside modal
    window.addEventListener('click', function (event) {
        const modal = document.getElementById('billModal');
        if (event.target === modal) {
            closeBillModal();
        }
    });
</script>

@endsection
