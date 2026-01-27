@extends('layouts.app')

@section('title', 'Collections Tracking - NYAWASCO')

@section('content')
@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
@endphp
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
        'title' => 'Collections Tracking',
        'subtitle' => 'Track Collection Activities and Follow-ups',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Buttons</h3>

            <div class="flex flex-wrap gap-3">
                <!-- Log Call -->
                <button
                    onclick="showCollectionActivityModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                        bg-blue-600 text-white hover:bg-blue-200
                        transition font-medium shadow-sm"
                >
                    <i class="fas fa-phone-alt"></i>
                   Record new Activity
                </button>


            </div>
        </div>
        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-sm mt-2 rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <form method="GET" action="{{ route('admin.accounts-receivable.collections-tracking') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Collection Agent</label>
                    <select name="agent_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Agents</option>
                        @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Activity Type</label>
                    <select name="activity_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="call" {{ request('activity_type') == 'call' ? 'selected' : '' }}>Phone Call</option>
                        <option value="visit" {{ request('activity_type') == 'visit' ? 'selected' : '' }}>Site Visit</option>
                        <option value="email" {{ request('activity_type') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="sms" {{ request('activity_type') == 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="letter" {{ request('activity_type') == 'letter' ? 'selected' : '' }}>Letter</option>
                        <option value="promise_to_pay" {{ request('activity_type') == 'promise_to_pay' ? 'selected' : '' }}>Promise to Pay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Outcome</label>
                    <select name="outcome" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Outcomes</option>
                        <option value="contacted" {{ request('outcome') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="promise_to_pay" {{ request('outcome') == 'promise_to_pay' ? 'selected' : '' }}>Promise to Pay</option>
                        <option value="payment_made" {{ request('outcome') == 'payment_made' ? 'selected' : '' }}>Payment Made</option>
                        <option value="no_answer" {{ request('outcome') == 'no_answer' ? 'selected' : '' }}>No Answer</option>
                        <option value="disconnected" {{ request('outcome') == 'disconnected' ? 'selected' : '' }}>Disconnected</option>
                        <option value="dispute" {{ request('outcome') == 'dispute' ? 'selected' : '' }}>Dispute</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Follow-up Status</label>
                    <select name="follow_up" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="pending" {{ request('follow_up') == 'pending' ? 'selected' : '' }}>Pending Follow-up</option>
                        <option value="completed" {{ request('follow_up') == 'completed' ? 'selected' : '' }}>Follow-up Completed</option>
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

                <div class="md:col-span-2 flex items-end space-x-3">
                    <a href="{{ route('admin.accounts-receivable.collections-tracking') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Reset
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Activities</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $activities->total() }}</p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Promises to Pay</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $activities->where('outcome', 'promise_to_pay')->count() }}
                    </p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Pending Follow-ups</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $activities->whereNotNull('follow_up_date')->where('follow_up_date', '>=', now())->count() }}
                    </p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow p-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Contact Rate</p>
                    <p class="text-2xl font-bold text-purple-600">
                        @php
                            $total = $activities->count();
                            $contacted = $activities->whereIn('outcome', ['contacted', 'promise_to_pay', 'payment_made'])->count();
                            $rate = $total > 0 ? round(($contacted / $total) * 100) : 0;
                        @endphp
                        {{ $rate }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Activities Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-white/20">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Collection Activities</h3>
                    <p class="text-sm text-gray-600">Showing {{ $activities->count() }} of {{ $activities->total() }} activities</p>
                </div>

                <div class="flex space-x-3 mt-2 sm:mt-0">
                    <button onclick="exportActivities()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export
                    </button>
                </div>
            </div>

            @if($activities->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Notes</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outcome</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Follow-up</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        @foreach($activities as $activity)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-4 py-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $activity->customer->first_name }} {{ $activity->customer->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $activity->customer->customer_number }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $activity->customer->phone }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-lg bg-blue-100 mr-3">
                                        <i class="fas fa-{{ $activity->activity_icon }} text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 capitalize">
                                            {{ str_replace('_', ' ', $activity->activity_type) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($activity->activity_date)->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $activity->agent->name ?? 'System' }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 line-clamp-2" title="{{ $activity->notes }}">
                                    {{ Str::limit($activity->notes, 100) }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                @if($activity->outcome)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $activity->outcome == 'payment_made' ? 'bg-green-100 text-green-800' :
                                       ($activity->outcome == 'promise_to_pay' ? 'bg-blue-100 text-blue-800' :
                                       ($activity->outcome == 'contacted' ? 'bg-yellow-100 text-yellow-800' :
                                       'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $activity->outcome)) }}
                                </span>
                                @if($activity->promised_amount)
                                <div class="text-xs text-gray-600 mt-1">
                                    KSh {{ number_format($activity->promised_amount, 2) }}
                                </div>
                                @endif
                                @else
                                <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @if($activity->follow_up_date)
                                <div class="text-sm {{ $activity->follow_up_date < now() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ \Carbon\Carbon::parse($activity->follow_up_date)->format('M d, Y') }}
                                </div>
                                @if($activity->follow_up_date < now())
                                <div class="text-xs text-red-500">Overdue</div>
                                @endif
                                @else
                                <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex space-x-2">
                                    <button onclick="viewActivity({{ $activity->id }})"
                                            class="text-blue-600 hover:text-blue-800" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button onclick="editActivity({{ $activity->id }})"
                                            class="text-green-600 hover:text-green-800" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button onclick="logFollowUp({{ $activity->id }})"
                                            class="text-purple-600 hover:text-purple-800" title="Log Follow-up">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $activities->links() }}
            </div>

            @else
            <div class="text-center py-12">
                <i class="fas fa-phone-alt text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No collection activities found</h3>
                <p class="text-gray-500">
                    @if(request()->anyFilled(['agent_id', 'activity_type', 'outcome', 'date_from']))
                        No activities match your filters.
                    @else
                        Start by logging your first collection activity.
                    @endif
                </p>

                <button onclick="showNewActivityModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 inline-flex items-center mt-4">
                    <i class="fas fa-plus mr-2"></i>
                    Log New Activity
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Collection Activity Modal -->
<div id="collectionActivityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Log Collection Activity</h3>
            <button onclick="closeActivityModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="collectionActivityForm" method="POST" action="{{ route('admin.accounts-receivable.collection-activities.store') }}">   @csrf
            <div class="space-y-4">
                <!-- Replace the existing select with this -->
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
                    <input type="hidden" name="customer_id" id="selectedCustomerId">
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
                                    <span class="text-gray-600 w-24">Meters:</span>
                                    <span class="font-medium" id="selectedCustomerMeters"></span>
                                </div>
                                <div class="col-span-2 flex items-center">
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Activity Type *</label>
                    <select name="activity_type" id="activityType" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="call"> Phone Call</option>
                        <option value="visit"> Site Visit</option>
                        <option value="email"> Email</option>
                        <option value="sms"> SMS</option>
                        <option value="letter"> Letter</option>
                        <option value="promise_to_pay"> Promise to Pay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes *</label>
                    <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describe the activity..." required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date *</label>
                        <input type="datetime-local" name="activity_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                        <input type="datetime-local" name="follow_up_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Outcome</label>
                        <select name="outcome" id="outcomeSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Outcome</option>
                            <option value="contacted">Contacted</option>
                            <option value="promise_to_pay">Promise to Pay</option>
                            <option value="payment_made">Payment Made</option>
                            <option value="no_answer">No Answer</option>
                            <option value="disconnected">Disconnected</option>
                            <option value="dispute">Dispute</option>
                        </select>
                    </div>
                    <div id="promisedAmountContainer" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promised Amount</label>
                        <input type="number" step="0.01" name="promised_amount" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="activityTips">Select a customer and activity type to see tips</span>
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeActivityModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Save Activity
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

function updateActivityTips(activityType) {
    const tips = {
        'call': 'Tips: Have the customer\'s account information ready. Be polite but firm. Document all promises made.',
        'visit': 'Tips: Schedule visits during business hours. Bring necessary documentation. Confirm customer identity.',
        'email': 'Tips: Use a professional subject line. Include account details. Request read receipt.',
        'sms': 'Tips: Keep messages brief. Include reference number. Avoid sensitive information.',
        'letter': 'Tips: Use official letterhead. Include all relevant details. Send via registered mail.',
        'promise_to_pay': 'Tips: Document exact amount and date. Follow up before due date. Update account notes.'
    };

    document.getElementById('activityTips').textContent = tips[activityType] || 'Select an activity type for tips';
}

function updateCustomerTips(option) {
    if (!option.value) return;

    const balance = option.dataset.balance || 0;
    const tips = document.getElementById('activityTips');
    tips.textContent = `Customer has KSh ${parseFloat(balance).toLocaleString('en-US', {minimumFractionDigits: 2})} overdue. Consider discussing payment plan options.`;
}

function showCollectionActivityModal(type = 'call') {
    const modal = document.getElementById('collectionActivityModal');
    if (type) {
        document.getElementById('activityType').value = type;
        updateActivityTips(type);
    }
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeActivityModal() {
    const modal = document.getElementById('collectionActivityModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('collectionActivityForm').reset();
    document.getElementById('promisedAmountContainer').classList.add('hidden');
}

function filterByWriteOffType(type) {
    window.location.href = `{{ route('admin.accounts-receivable.write-offs.index') }}?type=${type}`;
}

function exportWriteOffSummary() {
    // Implement export functionality
    alert('Export feature will be implemented');
}

function refreshSummary() {
    const lastUpdated = document.getElementById('lastUpdated');
    lastUpdated.textContent = 'Refreshing...';

    // In a real implementation, this would fetch updated data
    setTimeout(() => {
        lastUpdated.textContent = new Date().toLocaleTimeString();
        alert('Dashboard data refreshed!');
    }, 1000);
}

function startAutoRefresh() {
    // Auto-refresh every 5 minutes
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            refreshSummary();
        }
    }, 300000); // 5 minutes
}

// Close modal when clicking outside
document.getElementById('collectionActivityModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeActivityModal();
    }
});
// form submission to handle both AJAX and regular form submission
document.getElementById('collectionActivityForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

    try {
        const formData = new FormData(form);

        // Convert form data to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Send AJAX request
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            // Success
            showToast('Activity logged successfully!', 'success');
            closeActivityModal();

            // Refresh dashboard data
            refreshDashboard();
        } else {
            // Error
            showToast(result.message || 'Failed to log activity', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error. Please try again.', 'error');
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
// Add these functions to your existing JavaScript
let searchTimeout;

document.getElementById('customerSearch').addEventListener('input', function(e) {
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

async function performCustomerSearch(searchTerm) {
    const spinner = document.getElementById('searchSpinner');
    const resultsContainer = document.getElementById('searchResults');

    spinner.classList.remove('hidden');

    try {
        const response = await fetch('{{ route("admin.accounts-receivable.search-customer") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                 onclick="selectCustomer(${JSON.stringify(customer).replace(/"/g, '&quot;')})">
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

function selectCustomer(customer) {
    // Set hidden field
    document.getElementById('selectedCustomerId').value = customer.id;

    // Update display
    document.getElementById('selectedCustomerName').textContent = customer.name;
    document.getElementById('selectedCustomerNumber').textContent = customer.customer_number;
    document.getElementById('selectedCustomerPhone').textContent = customer.phone || 'N/A';
    document.getElementById('selectedCustomerIdNumber').textContent = customer.id_number || 'N/A';
    document.getElementById('selectedCustomerMeters').textContent = customer.meter_numbers || 'N/A';
    document.getElementById('selectedCustomerBalance').textContent =
        'KSh ' + parseFloat(customer.balance).toLocaleString('en-US', {minimumFractionDigits: 2});

    // Show selected customer info, hide search results
    document.getElementById('selectedCustomerInfo').classList.remove('hidden');
    document.getElementById('searchResults').classList.add('hidden');
    document.getElementById('customerSearch').value = '';

    // Update activity tips
    document.getElementById('activityTips').textContent =
        `Customer has KSh ${parseFloat(customer.balance).toLocaleString('en-US', {minimumFractionDigits: 2})} overdue. ` +
        `Consider discussing payment plan options. Last bill due: ${customer.latest_bill_due || 'N/A'}`;
}

function clearCustomerSelection() {
    document.getElementById('selectedCustomerId').value = '';
    document.getElementById('selectedCustomerInfo').classList.add('hidden');
    document.getElementById('activityTips').textContent =
        'Select a customer and activity type to see tips';
}

// Add route to modal form submission
document.getElementById('collectionActivityForm').addEventListener('submit', function(e) {
    if (!document.getElementById('selectedCustomerId').value) {
        e.preventDefault();
        showToast('Please select a customer first', 'error');
        return false;
    }
});
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

    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.add('animate-slide-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

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
// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeActivityModal();
    }
    if (e.ctrlKey && e.key === 'l') {
        e.preventDefault();
        showCollectionActivityModal();
    }
});
</script>

<style>
/* Custom styles for better UX */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #555;
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
@endsection
