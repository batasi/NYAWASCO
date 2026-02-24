{{-- resources/views/admin/sms/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Send SMS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Send SMS</h1>
                <p class="text-sm text-gray-600 mt-1">Compose and send a new SMS message</p>
            </div>
            <a href="{{ route('admin.sms.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">New Message</h2>
                </div>

                <form action="{{ route('admin.sms.send') }}" method="POST" class="p-6" id="smsForm">
                    @csrf

                    <!-- Recipient Type Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Recipient Type</label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none
                                  @if(old('recipient_type', 'manual') == 'manual') border-blue-600 ring-2 ring-blue-600 @else border-gray-300 @endif">
                                <input type="radio" name="recipient_type" value="manual"
                                       class="sr-only" aria-labelledby="manual-label"
                                       {{ old('recipient_type', 'manual') == 'manual' ? 'checked' : '' }}
                                       onchange="toggleRecipientType()">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span id="manual-label" class="block text-sm font-medium text-gray-900">Manual Entry</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">Enter phone number manually</span>
                                    </span>
                                </span>
                                <span class="pointer-events-none absolute -inset-px rounded-lg border-2 {{ old('recipient_type', 'manual') == 'manual' ? 'border-blue-600' : 'border-transparent' }}" aria-hidden="true"></span>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none
                                  @if(old('recipient_type') == 'customer') border-blue-600 ring-2 ring-blue-600 @else border-gray-300 @endif">
                                <input type="radio" name="recipient_type" value="customer"
                                       class="sr-only" aria-labelledby="customer-label"
                                       {{ old('recipient_type') == 'customer' ? 'checked' : '' }}
                                       onchange="toggleRecipientType()">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span id="customer-label" class="block text-sm font-medium text-gray-900">Select Customer</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">Choose from existing customers</span>
                                    </span>
                                </span>
                                <span class="pointer-events-none absolute -inset-px rounded-lg border-2 {{ old('recipient_type') == 'customer' ? 'border-blue-600' : 'border-transparent' }}" aria-hidden="true"></span>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none
                                  @if(old('recipient_type') == 'meter') border-blue-600 ring-2 ring-blue-600 @else border-gray-300 @endif">
                                <input type="radio" name="recipient_type" value="meter"
                                       class="sr-only" aria-labelledby="meter-label"
                                       {{ old('recipient_type') == 'meter' ? 'checked' : '' }}
                                       onchange="toggleRecipientType()">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span id="meter-label" class="block text-sm font-medium text-gray-900">Select Meter</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">Send to meter's customer</span>
                                    </span>
                                </span>
                                <span class="pointer-events-none absolute -inset-px rounded-lg border-2 {{ old('recipient_type') == 'meter' ? 'border-blue-600' : 'border-transparent' }}" aria-hidden="true"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Manual Phone Input -->
                    <div id="manual-input" class="mb-6 {{ old('recipient_type', 'manual') == 'manual' ? '' : 'hidden' }}">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               value="{{ old('phone', request('phone')) }}"
                               placeholder="e.g., 0712345678 or 254712345678"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Enter Kenyan phone number (will be formatted automatically)</p>
                    </div>

                    <!-- Customer Selection -->
                    <div id="customer-input" class="mb-6 {{ old('recipient_type') == 'customer' ? '' : 'hidden' }}">
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Select Customer</label>
                        <select name="customer_id"
                                id="customer_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                onchange="getCustomerPhone(this.value)">
                            <option value="">-- Select Customer --</option>
                            @foreach($recentCustomers as $customer)
                                <option value="{{ $customer->id }}"
                                        data-phone="{{ $customer->phone }}"
                                        {{ old('customer_id', $customer->id ?? '') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }} ({{ $customer->customer_number }}) - {{ $customer->phone }}
                                </option>
                            @endforeach
                        </select>
                        <div id="customer-phone-display" class="mt-2 text-sm text-gray-600 hidden">
                            Phone: <span class="font-medium"></span>
                        </div>
                    </div>

                    <!-- Meter Selection -->
                    <div id="meter-input" class="mb-6 {{ old('recipient_type') == 'meter' ? '' : 'hidden' }}">
                        <label for="meter_id" class="block text-sm font-medium text-gray-700 mb-2">Select Meter</label>
                        <select name="meter_id"
                                id="meter_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 meter-select"
                                data-url="{{ route('admin.sms.get-meter-customer') }}">
                            <option value="">-- Select Meter --</option>
                            @php
                                $meters = \App\Models\Meter::with('customer')->whereNotNull('customer_id')->limit(100)->get();
                            @endphp
                            @foreach($meters as $meter)
                                <option value="{{ $meter->id }}"
                                        data-customer="{{ $meter->customer ? $meter->customer->full_name : 'No Customer' }}"
                                        data-phone="{{ $meter->customer->phone ?? '' }}"
                                        {{ old('meter_id') == $meter->id ? 'selected' : '' }}>
                                    {{ $meter->meter_number }} - {{ $meter->customer->full_name ?? 'No Customer' }}
                                </option>
                            @endforeach
                        </select>
                        <div id="meter-customer-display" class="mt-2 text-sm text-gray-600 hidden">
                            Customer: <span class="font-medium"></span><br>
                            Phone: <span class="font-medium"></span>
                        </div>
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
                                <span id="remaining-chars" class="ml-2 text-blue-600">160 remaining</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                Max: 1600 characters (10 parts)
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Option -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="schedule" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   onchange="toggleSchedule()">
                            <span class="ml-2 text-sm text-gray-700">Schedule for later</span>
                        </label>
                    </div>

                    <div id="schedule-input" class="mb-6 hidden">
                        <label for="send_at" class="block text-sm font-medium text-gray-700 mb-2">Send Date & Time</label>
                        <input type="datetime-local"
                               name="send_at"
                               id="send_at"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button"
                                onclick="previewMessage()"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Preview
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar - Recent Customers -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold">Recent Customers</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        @forelse($recentCustomers as $customer)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $customer->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $customer->customer_number }}</p>
                                    <p class="text-xs text-gray-600">{{ $customer->phone }}</p>
                                </div>
                                <a href="{{ route('admin.sms.create', ['customer' => $customer->id]) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No recent customers</p>
                        @endforelse
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Message Preview</h3>
            <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p id="preview-text" class="text-gray-800 whitespace-pre-wrap"></p>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                <span id="preview-chars">0</span> characters | <span id="preview-parts">1</span> SMS part(s)
            </div>
        </div>
        <div class="flex justify-end">
            <button onclick="closePreview()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 mr-2">
                Close
            </button>
            <button onclick="document.getElementById('smsForm').submit()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Send Now
            </button>
        </div>
    </div>
</div>

<script>
function toggleRecipientType() {
    const type = document.querySelector('input[name="recipient_type"]:checked').value;

    document.getElementById('manual-input').classList.add('hidden');
    document.getElementById('customer-input').classList.add('hidden');
    document.getElementById('meter-input').classList.add('hidden');

    if (type === 'manual') {
        document.getElementById('manual-input').classList.remove('hidden');
    } else if (type === 'customer') {
        document.getElementById('customer-input').classList.remove('hidden');
    } else if (type === 'meter') {
        document.getElementById('meter-input').classList.remove('hidden');
    }
}

function getCustomerPhone(customerId) {
    if (!customerId) {
        document.getElementById('customer-phone-display').classList.add('hidden');
        return;
    }

    const select = document.getElementById('customer_id');
    const option = select.options[select.selectedIndex];
    const phone = option.dataset.phone;

    if (phone) {
        document.getElementById('customer-phone-display').querySelector('span').textContent = phone;
        document.getElementById('customer-phone-display').classList.remove('hidden');
    } else {
        document.getElementById('customer-phone-display').classList.add('hidden');
    }
}

// Initialize if customer pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');
    if (customerSelect && customerSelect.value) {
        getCustomerPhone(customerSelect.value);
    }
});

// Meter selection with AJAX
document.querySelectorAll('.meter-select').forEach(select => {
    select.addEventListener('change', function() {
        const meterId = this.value;
        const display = document.getElementById('meter-customer-display');

        if (!meterId) {
            display.classList.add('hidden');
            return;
        }

        const option = this.options[this.selectedIndex];
        const customerName = option.dataset.customer;
        const phone = option.dataset.phone;

        if (customerName && phone) {
            display.querySelectorAll('span')[0].textContent = customerName;
            display.querySelectorAll('span')[1].textContent = phone;
            display.classList.remove('hidden');
        } else {
            display.classList.add('hidden');
        }
    });
});

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
}

function updateCharCount() {
    const message = document.getElementById('message').value;
    const count = message.length;
    const smsCount = Math.ceil(count / 160) || 1;
    const remaining = 160 - (count % 160 || 160);

    document.getElementById('char-count').textContent = count;
    document.getElementById('sms-count').textContent = smsCount;
    document.getElementById('remaining-chars').textContent = remaining + ' remaining';

    if (count > 1600) {
        document.getElementById('message').value = message.substring(0, 1600);
    }
}

function toggleSchedule() {
    const scheduleInput = document.getElementById('schedule-input');
    if (document.querySelector('input[name="schedule"]').checked) {
        scheduleInput.classList.remove('hidden');
    } else {
        scheduleInput.classList.add('hidden');
    }
}

function previewMessage() {
    const message = document.getElementById('message').value;
    if (!message.trim()) {
        alert('Please enter a message to preview');
        return;
    }

    document.getElementById('preview-text').textContent = message;
    document.getElementById('preview-chars').textContent = message.length;
    document.getElementById('preview-parts').textContent = Math.ceil(message.length / 160) || 1;

    document.getElementById('preview-modal').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}

// Initialize on page load
updateCharCount();
</script>
@endsection
