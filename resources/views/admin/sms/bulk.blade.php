{{-- resources/views/admin/sms/bulk.blade.php --}}

@extends('layouts.app')

@section('title', 'Bulk SMS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bulk SMS</h1>
                <p class="text-sm text-gray-600 mt-1">Send SMS to multiple recipients at once</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.sms.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Single SMS
                </a>
                <a href="{{ route('admin.sms.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Warning for large sends -->
    @if(session('warning'))
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        {{ session('warning') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Bulk Message Configuration</h2>
                </div>

                <form action="{{ route('admin.sms.bulk.send') }}" method="POST" class="p-6" id="bulkForm">
                    @csrf

                    <!-- Recipient Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Select Recipients</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                                  @if(old('recipient_filter', 'all_customers') == 'all_customers') border-blue-500 bg-blue-50 @endif">
                                <input type="radio" name="recipient_filter" value="all_customers"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                       {{ old('recipient_filter', 'all_customers') == 'all_customers' ? 'checked' : '' }}
                                       onchange="toggleFilterOptions()">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">All Active Customers</span>
                                    <span class="block text-sm text-gray-500">Send to all customers with valid phone numbers</span>
                                </span>
                            </label>

                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                                  @if(old('recipient_filter') == 'zone') border-blue-500 bg-blue-50 @endif">
                                <input type="radio" name="recipient_filter" value="zone"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                       {{ old('recipient_filter') == 'zone' ? 'checked' : '' }}
                                       onchange="toggleFilterOptions()">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Specific Zone</span>
                                    <span class="block text-sm text-gray-500">Send to customers in a selected zone</span>
                                </span>
                            </label>

                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                                  @if(old('recipient_filter') == 'unpaid_bills') border-blue-500 bg-blue-50 @endif">
                                <input type="radio" name="recipient_filter" value="unpaid_bills"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                       {{ old('recipient_filter') == 'unpaid_bills' ? 'checked' : '' }}
                                       onchange="toggleFilterOptions()">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Customers with Unpaid Bills</span>
                                    <span class="block text-sm text-gray-500">Send to customers who have outstanding bills</span>
                                </span>
                            </label>

                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                                  @if(old('recipient_filter') == 'overdue_bills') border-blue-500 bg-blue-50 @endif">
                                <input type="radio" name="recipient_filter" value="overdue_bills"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                       {{ old('recipient_filter') == 'overdue_bills' ? 'checked' : '' }}
                                       onchange="toggleFilterOptions()">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Customers with Overdue Bills</span>
                                    <span class="block text-sm text-gray-500">Send to customers with past due bills</span>
                                </span>
                            </label>

                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                                  @if(old('recipient_filter') == 'custom_list') border-blue-500 bg-blue-50 @endif">
                                <input type="radio" name="recipient_filter" value="custom_list"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                       {{ old('recipient_filter') == 'custom_list' ? 'checked' : '' }}
                                       onchange="toggleFilterOptions()">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Custom Phone List</span>
                                    <span class="block text-sm text-gray-500">Enter phone numbers manually (one per line)</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Zone Selection -->
                    <div id="zone-filter" class="mb-6 {{ old('recipient_filter') == 'zone' ? '' : 'hidden' }}">
                        <label for="zone_id" class="block text-sm font-medium text-gray-700 mb-2">Select Zone</label>
                        <select name="zone_id"
                                id="zone_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select Zone --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Custom Phone List -->
                    <div id="custom-list-filter" class="mb-6 {{ old('recipient_filter') == 'custom_list' ? '' : 'hidden' }}">
                        <label for="custom_phones" class="block text-sm font-medium text-gray-700 mb-2">Phone Numbers</label>
                        <textarea name="custom_phones"
                                  id="custom_phones"
                                  rows="5"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="0712345678&#10;0723456789&#10;0734567890">{{ old('custom_phones') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Enter one phone number per line. Kenyan numbers only.</p>
                        <div class="mt-2 text-sm" id="phone-count">0 numbers entered</div>
                    </div>

                    <!-- Template Selection -->
                    <div class="mb-6">
                        <label for="template_id" class="block text-sm font-medium text-gray-700 mb-2">Use Template (Optional)</label>
                        <select name="template_id"
                                id="template_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                onchange="loadTemplate()">
                            <option value="">-- No Template (Write Custom Message) --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}"
                                        data-message="{{ $template->message }}"
                                        data-placeholders="{{ json_encode($template->placeholders) }}"
                                        {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }} ({{ $template->category }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Placeholders Info -->
                    <div id="placeholders-info" class="mb-6 p-4 bg-blue-50 rounded-lg hidden">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Template Placeholders</h4>
                        <p class="text-xs text-blue-600 mb-2">Available variables to use in your message:</p>
                        <div id="placeholders-list" class="flex flex-wrap gap-2"></div>
                        <p class="text-xs text-blue-600 mt-2">Note: Placeholders will be replaced with actual data when sending</p>
                    </div>

                    <!-- Message Input -->
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message"
                                  id="message"
                                  rows="6"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Type your message here..."
                                  onkeyup="updateCharCount()">{{ old('message') }}</textarea>
                        <div class="mt-2 flex justify-between items-center">
                            <div class="text-xs text-gray-500">
                                <span id="char-count">0</span> characters |
                                <span id="sms-count">1</span> SMS part(s)
                            </div>
                            <div class="text-xs text-gray-500">
                                Max: 1600 characters (10 parts)
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation Checkbox for Large Sends -->
                    <div id="confirmation-section" class="mb-6 p-4 bg-yellow-50 rounded-lg hidden">
                        <label class="flex items-center">
                            <input type="checkbox" name="confirmed" value="1" class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-yellow-700">
                                I confirm that I want to send this message to <span id="recipient-count">0</span> recipients
                            </span>
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button"
                                onclick="previewBulk()"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Preview & Estimate
                        </button>
                        <button type="submit"
                                id="submitBtn"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Send Bulk SMS
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar - Info & Stats -->
        <div class="lg:col-span-1">
            <!-- Estimated Recipients -->
            <div class="bg-white rounded-lg shadow mb-4">
                <div class="p-4 border-b">
                    <h3 class="font-semibold">Estimated Recipients</h3>
                </div>
                <div class="p-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600" id="estimate-count">0</div>
                        <p class="text-sm text-gray-500 mt-1">will receive this message</p>
                    </div>

                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estimated SMS parts:</span>
                            <span class="font-medium" id="estimate-parts">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estimated cost:</span>
                            <span class="font-medium" id="estimate-cost">KSh 0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold">System Stats</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Active Customers:</span>
                            <span class="font-medium">{{ \App\Models\Customer::where('status', 'active')->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">With Phone Numbers:</span>
                            <span class="font-medium">{{ \App\Models\Customer::whereNotNull('phone')->where('phone', '!=', '')->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Unpaid Bills:</span>
                            <span class="font-medium">{{ \App\Models\Bill::where('bill_status', 'unpaid')->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Overdue Bills:</span>
                            <span class="font-medium">{{ \App\Models\Bill::where('bill_status', 'unpaid')->where('due_date', '<', now())->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-white rounded-lg shadow mt-4">
                <div class="p-4 border-b">
                    <h3 class="font-semibold">Bulk SMS Tips</h3>
                </div>
                <div class="p-4">
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 text-purple-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Personalize messages using templates
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 text-purple-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Large sends (>50) require confirmation
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 text-purple-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sending may take a few minutes for large lists
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Bulk SMS Preview</h3>
            <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <div class="bg-gray-50 p-4 rounded-lg max-h-60 overflow-y-auto">
                <p id="preview-text" class="text-gray-800 whitespace-pre-wrap"></p>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Recipients:</span>
                    <span class="font-medium" id="preview-recipients">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Total SMS parts:</span>
                    <span class="font-medium" id="preview-total-parts">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Estimated cost:</span>
                    <span class="font-medium" id="preview-total-cost">KSh 0.00</span>
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <button onclick="closePreview()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 mr-2">
                Cancel
            </button>
            <button onclick="document.getElementById('bulkForm').submit()"
                    class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                Proceed to Send
            </button>
        </div>
    </div>
</div>

<script>
function toggleFilterOptions() {
    const filter = document.querySelector('input[name="recipient_filter"]:checked').value;

    document.getElementById('zone-filter').classList.add('hidden');
    document.getElementById('custom-list-filter').classList.add('hidden');

    if (filter === 'zone') {
        document.getElementById('zone-filter').classList.remove('hidden');
    } else if (filter === 'custom_list') {
        document.getElementById('custom-list-filter').classList.remove('hidden');
        updatePhoneCount();
    }

    // Auto-update estimate
    updateEstimate();
}

function updatePhoneCount() {
    const phones = document.getElementById('custom_phones')?.value || '';
    const count = phones.split('\n').filter(p => p.trim()).length;
    document.getElementById('phone-count').textContent = count + ' numbers entered';
    return count;
}

function loadTemplate() {
    const select = document.getElementById('template_id');
    const messageArea = document.getElementById('message');
    const placeholdersInfo = document.getElementById('placeholders-info');
    const placeholdersList = document.getElementById('placeholders-list');

    if (!select.value) {
        placeholdersInfo.classList.add('hidden');
        return;
    }

    const option = select.options[select.selectedIndex];
    const templateMessage = option.dataset.message;
    const placeholders = JSON.parse(option.dataset.placeholders || '[]');

    messageArea.value = templateMessage;
    updateCharCount();

    if (placeholders.length > 0) {
        placeholdersList.innerHTML = '';
        placeholders.forEach(placeholder => {
            const badge = document.createElement('span');
            badge.className = 'px-2 py-1 bg-white text-blue-600 text-xs rounded-full border border-blue-200';
            badge.textContent = '{{' + placeholder + '}}';
            placeholdersList.appendChild(badge);
        });
        placeholdersInfo.classList.remove('hidden');
    } else {
        placeholdersInfo.classList.add('hidden');
    }

    updateEstimate();
}

function updateCharCount() {
    const message = document.getElementById('message').value;
    const count = message.length;
    const smsCount = Math.ceil(count / 160) || 1;

    document.getElementById('char-count').textContent = count;
    document.getElementById('sms-count').textContent = smsCount;

    updateEstimate();
}

function updateEstimate() {
    const filter = document.querySelector('input[name="recipient_filter"]:checked')?.value;
    let recipientCount = 0;

    // Estimate recipients based on filter
    if (filter === 'all_customers') {
        recipientCount = {{ \App\Models\Customer::whereNotNull('phone')->where('phone', '!=', '')->count() }};
    } else if (filter === 'zone') {
        const zoneId = document.getElementById('zone_id')?.value;
        // This is an estimate - actual count will be calculated on server
        recipientCount = 50; // Placeholder
    } else if (filter === 'unpaid_bills') {
        recipientCount = {{ \App\Models\Bill::where('bill_status', 'unpaid')->whereHas('customer', function($q) { $q->whereNotNull('phone'); })->count() }};
    } else if (filter === 'overdue_bills') {
        recipientCount = {{ \App\Models\Bill::where('bill_status', 'unpaid')->where('due_date', '<', now())->whereHas('customer', function($q) { $q->whereNotNull('phone'); })->count() }};
    } else if (filter === 'custom_list') {
        recipientCount = updatePhoneCount();
    }

    const smsParts = parseInt(document.getElementById('sms-count').textContent) || 1;
    const totalParts = recipientCount * smsParts;
    const costPerSms = 0.50; // Example cost - adjust based on your actual rate
    const totalCost = totalParts * costPerSms;

    document.getElementById('estimate-count').textContent = recipientCount;
    document.getElementById('estimate-parts').textContent = totalParts;
    document.getElementById('estimate-cost').textContent = 'KSh ' + totalCost.toFixed(2);

    // Show confirmation for large sends
    const confirmationSection = document.getElementById('confirmation-section');
    if (recipientCount > 50) {
        document.getElementById('recipient-count').textContent = recipientCount;
        confirmationSection.classList.remove('hidden');
    } else {
        confirmationSection.classList.add('hidden');
    }
}

function previewBulk() {
    const message = document.getElementById('message').value;
    if (!message.trim()) {
        alert('Please enter a message to preview');
        return;
    }

    const filter = document.querySelector('input[name="recipient_filter"]:checked')?.value;
    let recipientCount = 0;

    if (filter === 'all_customers') {
        recipientCount = {{ \App\Models\Customer::whereNotNull('phone')->where('phone', '!=', '')->count() }};
    } else if (filter === 'custom_list') {
        recipientCount = updatePhoneCount();
    } else {
        recipientCount = parseInt(document.getElementById('estimate-count').textContent) || 0;
    }

    const smsParts = parseInt(document.getElementById('sms-count').textContent) || 1;
    const totalParts = recipientCount * smsParts;
    const costPerSms = 0.50;
    const totalCost = totalParts * costPerSms;

    document.getElementById('preview-text').textContent = message;
    document.getElementById('preview-recipients').textContent = recipientCount;
    document.getElementById('preview-total-parts').textContent = totalParts;
    document.getElementById('preview-total-cost').textContent = 'KSh ' + totalCost.toFixed(2);

    document.getElementById('preview-modal').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    toggleFilterOptions();
    updateCharCount();

    // Add change listeners for auto-update
    document.getElementById('zone_id')?.addEventListener('change', updateEstimate);
    document.getElementById('custom_phones')?.addEventListener('keyup', function() {
        updatePhoneCount();
        updateEstimate();
    });
});
</script>
@endsection
