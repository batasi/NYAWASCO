@extends('layouts.app')

@section('title', 'Collections Tracking - NYAWASCO')

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
        ]
    ];


        $actionButtons[] = [
            'onclick' => 'showNewActivityModal()',
            'icon' => 'fas fa-plus',
            'label' => 'New Activity',
            'color' => 'bg-green-600'
        ];

    @endphp

    @include('components.dashboard-header',[
        'title' => 'Collections Tracking',
        'subtitle' => 'Track Collection Activities and Follow-ups',
        'actionButtons' => $actionButtons
    ])

    <div class="w-full px-2.5 py-8 relative z-10">
        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <form method="GET" action="#" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <p class="text-sm text-gray-600">Showing {{ $activities->count() }} activities</p>
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
                                            {{ \Carbon\Carbon::parse($activity->activity_date)->format('M d, Y') }}
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

<!-- New Activity Modal -->
<div id="newActivityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Log New Collection Activity</h3>
            <button onclick="closeNewActivityModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="newActivityForm" method="POST" action="#">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                    <select name="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select Customer</option>
                        <!-- Will be populated via AJAX -->
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Type *</label>
                        <select name="activity_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select Type</option>
                            <option value="call">Phone Call</option>
                            <option value="visit">Site Visit</option>
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="letter">Letter</option>
                            <option value="promise_to_pay">Promise to Pay</option>
                            <option value="payment_arrangement">Payment Arrangement</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date *</label>
                        <input type="date" name="activity_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes *</label>
                    <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describe the activity..." required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Outcome</label>
                        <select name="outcome" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Outcome</option>
                            <option value="contacted">Contacted</option>
                            <option value="promise_to_pay">Promise to Pay</option>
                            <option value="payment_made">Payment Made</option>
                            <option value="no_answer">No Answer</option>
                            <option value="disconnected">Disconnected</option>
                            <option value="dispute">Dispute</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                        <input type="date" name="follow_up_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div id="promiseSection" class="hidden">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Promised Amount</label>
                            <input type="number" step="0.01" name="promised_amount" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Promised Date</label>
                            <input type="date" name="promised_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        Collection activities help track customer interactions and improve recovery rates.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeNewActivityModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Save Activity
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showNewActivityModal() {
    const modal = document.getElementById('newActivityModal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    loadCustomersForActivity();
}

function closeNewActivityModal() {
    const modal = document.getElementById('newActivityModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function loadCustomersForActivity() {
    const select = document.querySelector('select[name="customer_id"]');
    if (select.options.length > 1) return; // Already loaded

    fetch('/admin/api/delinquent-customers')
        .then(response => response.json())
        .then(customers => {
            select.innerHTML = '<option value="">Select Customer</option>';
            customers.forEach(customer => {
                const option = document.createElement('option');
                option.value = customer.id;
                option.textContent = `${customer.customer_number} - ${customer.first_name} ${customer.last_name} (KSh ${customer.total_due})`;
                select.appendChild(option);
            });
        });
}

// Show/hide promise fields based on outcome
document.querySelector('select[name="outcome"]')?.addEventListener('change', function() {
    const promiseSection = document.getElementById('promiseSection');
    if (this.value === 'promise_to_pay') {
        promiseSection.classList.remove('hidden');
    } else {
        promiseSection.classList.add('hidden');
    }
});

function viewActivity(activityId) {
    // Implement view activity details
    alert('View activity: ' + activityId);
}

function editActivity(activityId) {
    // Implement edit activity
    alert('Edit activity: ' + activityId);
}

function logFollowUp(activityId) {
    // Implement follow-up logging
    alert('Log follow-up for activity: ' + activityId);
}

function exportActivities() {
    // Implement export to Excel/PDF
    alert('Export feature will be implemented');
}

// Close modal when clicking outside
document.getElementById('newActivityModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeNewActivityModal();
    }
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
