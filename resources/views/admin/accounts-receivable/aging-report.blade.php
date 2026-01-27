@extends('layouts.app')

@section('title', 'Aging Report - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>
<div class="min-h-screen bg-gray-50">
    @php
    $actionButtons = [
        [
            'route' => 'admin.accounts-receivable.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'label' => 'Dashboard',
            'color' => 'bg-blue-600'
    ];
    @endphp

    @include('components.dashboard-header',[
        'title' => 'Aging Report',
        'subtitle' => 'Receivables Aging Analysis',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <form method="GET" action="{{ route('admin.accounts-receivable.aging-report') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">As of Date</label>
                    <input type="date" name="as_of_date" value="{{ $date }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zone</label>
                    <select name="zone_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Zones</option>
                        <!-- Populate zones from database -->
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meter Category</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        <!-- Populate categories from database -->
                    </select>
                </div>

                <div class="md:col-span-3 flex justify-end space-x-3">
                    <a href="{{ route('admin.accounts-receivable.aging-report') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Reset
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            @foreach($summary as $bucketName => $data)
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4 border border-white/20">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600 mb-1">{{ $bucketName }}</p>
                    <p class="text-xl font-bold text-gray-900 mb-1">
                        KSh {{ number_format($data['total_amount'], 2) }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ number_format($data['customer_count']) }} customers
                    </p>
                    <div class="mt-2 text-xs">
                        <span class="inline-block px-2 py-1 rounded-full {{ $data['percentage'] > 30 ? 'bg-red-100 text-red-800' : ($data['percentage'] > 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ number_format($data['percentage'], 1) }}%
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Aging Report Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Aging Report Details</h3>
                    <p class="text-sm text-gray-600">As of {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
                </div>

                <div class="flex space-x-3 mt-2 sm:mt-0">
                    <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>

                    <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </button>
                </div>
            </div>

            @if(count($agingData) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Due</th>
                            @foreach($agingBuckets as $bucket)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: {{ $bucket->color }}">
                                {{ $bucket->name }}
                            </th>
                            @endforeach
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        @foreach($agingData as $customer)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-4 py-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $customer->customer_number }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $customer->phone }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">
                                    KSh {{ number_format($customer->total_due, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $customer->bill_count }} bills
                                </div>
                            </td>

                            @foreach($agingBuckets as $bucket)
                            <td class="px-4 py-4">
                                @php
                                    $bucketAmount = $customer->buckets->where('bucket_id', $bucket->id)->first()->amount ?? 0;
                                @endphp
                                @if($bucketAmount > 0)
                                <div class="text-sm font-medium text-gray-900">
                                    KSh {{ number_format($bucketAmount, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $customer->buckets->where('bucket_id', $bucket->id)->first()->bill_count ?? 0 }} bills
                                </div>
                                @else
                                <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            @endforeach

                            <td class="px-4 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}"
                                       class="text-blue-600 hover:text-blue-800" title="View Customer">
                                        <i class="fas fa-eye"></i>
                                    </a>


                                    <a href="#" onclick="logActivityForCustomer({{ $customer->id }})"
                                       class="text-green-600 hover:text-green-800" title="Log Activity">
                                        <i class="fas fa-phone-alt"></i>
                                    </a>

                                    <a href="{{ route('admin.accounts-receivable.write-offs.create', $customer->id) }}"
                                       class="text-red-600 hover:text-red-800" title="Write-off">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <!-- Footer Totals -->
                    <tfoot class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">Totals</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">
                                KSh {{ number_format(collect($agingData)->sum('total_due'), 2) }}
                            </th>
                            @foreach($agingBuckets as $bucket)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">
                                @php
                                    $total = 0;
                                    foreach($agingData as $customer) {
                                        $total += $customer->buckets->where('bucket_id', $bucket->id)->first()->amount ?? 0;
                                    }
                                @endphp
                                KSh {{ number_format($total, 2) }}
                            </th>
                            @endforeach
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{-- {{ $agingData->links() }} --}}
            </div>

            @else
            <div class="text-center py-12">
                <i class="fas fa-file-invoice text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No aging data found</h3>
                <p class="text-gray-500">Adjust your filters or try a different date.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function printReport() {
    window.print();
}

function exportToExcel() {
    // Implement Excel export logic
    alert('Excel export feature will be implemented');
}

function logActivityForCustomer(customerId) {
    // Implement logic to log activity for specific customer
    alert('Log activity for customer: ' + customerId);
}

// Filter by bucket when clicking on bucket headers
document.addEventListener('DOMContentLoaded', function() {
    const bucketHeaders = document.querySelectorAll('th[style*="color"]');
    bucketHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const bucketName = this.textContent.trim();
            alert('Filter by ' + bucketName + ' bucket');
        });
    });
});
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }

    .bg-white\/80 {
        background: white !important;
    }

    .rounded-2xl {
        border-radius: 0 !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
