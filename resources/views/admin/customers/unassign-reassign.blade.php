@extends('layouts.app')

@section('title', 'Unassign and Reassign Meter - NYAWASCO')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-3 mb-4">
            <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Back to Customer
            </a>
            <span class="text-gray-300">|</span>
            <span class="text-gray-600">Unassign & Reassign Meter</span>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Unassign & Reassign Meter</h1>
                    <p class="text-gray-600 mt-1">
                        Transfer meter <span class="font-semibold text-blue-600">{{ $meter->meter_number }}</span>
                        from <span class="font-semibold">{{ $customer->first_name }} {{ $customer->last_name }}</span>
                        ({{ $customer->customer_number }})
                    </p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg">
                    <i class="fas fa-exchange-alt text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <form method="POST" action="{{ route('admin.customers.unassign-and-reassign', [$customer, $meter]) }}" id="reassignForm">
                    @csrf

                    <!-- Unassignment Reason -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-red-500 mr-2"></i>
                            Unassignment Information
                        </h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for Unassignment <span class="text-red-500">*</span>
                            </label>
                            <select name="unassignment_reason" id="unassignmentReason" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select reason...</option>
                                <option value="customer_request">Customer Request</option>
                                <option value="meter_fault">Meter Fault / Replacement</option>
                                <option value="property_transfer">Property Transfer / Sale</option>
                                <option value="tenant_change">Tenant Change</option>
                                <option value="meter_incorrectly_assigned" class="font-semibold text-red-600 bg-red-50">
                                    Meter Incorrectly Assigned
                                </option>
                                <option value="illegal_connection">Illegal Connection Detected</option>
                                <option value="disconnection">Service Disconnection</option>
                                <option value="other">Other</option>
                            </select>
                            @error('unassignment_reason')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Selection -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user-plus text-green-500 mr-2"></i>
                            Assign To
                        </h3>

                        <div class="space-y-4">
                            <!-- Option 1: Existing Customer -->
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="radio" name="action" id="actionExisting" value="existing" checked
                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <label for="actionExisting" class="font-medium text-gray-900">
                                            Assign to Existing Customer
                                        </label>
                                        <p class="text-sm text-gray-500 mb-3">Search and select a customer from the database</p>

                                        <!-- Customer Search -->
                                        <div id="existingCustomerSection">
                                            <div class="mb-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Search Customer <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <select name="customer_id" id="customerSearch" class="w-full" style="display: none;">
                                                        <option value="">Search for a customer...</option>
                                                    </select>
                                                    <div id="customerSearchLoading" class="absolute right-3 top-3 hidden">
                                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                                    </div>
                                                </div>
                                                <div id="customerMeterAlert" class="mt-2 hidden">
                                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                                        <div class="flex items-start">
                                                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                                                            <div>
                                                                <h4 class="text-sm font-medium text-yellow-800">Customer Has Existing Meter(s)</h4>
                                                                <p id="customerMeterMessage" class="text-xs text-yellow-700 mt-1"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- New Meter Installation Details -->
                                            <div id="installationDetails" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                                <h4 class="text-sm font-medium text-gray-900 mb-3">Meter Installation Details</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Installation Date <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="date" name="new_installation_date"
                                                               value="{{ date('Y-m-d') }}"
                                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                               required>
                                                        @error('new_installation_date')
                                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Initial Reading (m³) <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="number" step="0.01" name="new_initial_reading"
                                                               value="{{ $meter->current_reading }}"
                                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                               required>
                                                        @error('new_initial_reading')
                                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Balance B/F (KSh)
                                                        </label>
                                                        <input type="number" step="0.01" name="new_balance_bf" value="0"
                                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Option 2: New Customer -->
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="radio" name="action" id="actionNew" value="new"
                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <label for="actionNew" class="font-medium text-gray-900">
                                            Create New Customer
                                        </label>
                                        <p class="text-sm text-gray-500 mb-3">Quickly create a new customer and assign the meter</p>

                                        <!-- New Customer Form -->
                                        <div id="newCustomerSection" class="hidden">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        First Name <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" name="new_customer[first_name]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Last Name
                                                    </label>
                                                    <input type="text" name="new_customer[last_name]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Email <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="email" name="new_customer[email]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Phone <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" name="new_customer[phone]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        ID Number <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" name="new_customer[id_number]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        KRA PIN
                                                    </label>
                                                    <input type="text" name="new_customer[kra_pin]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Plot Number <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" name="new_customer[plot_number]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        House Number <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" name="new_customer[house_number]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Estate/Area
                                                    </label>
                                                    <input type="text" name="new_customer[estate]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Property Owner <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" name="new_customer[property_owner]"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Physical Address <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea name="new_customer[physical_address]" rows="2"
                                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Expected Users
                                                    </label>
                                                    <input type="number" name="new_customer[expected_users]" min="1"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-3">
                                                <i class="fas fa-info-circle"></i>
                                                Customer will be created with ACTIVE status and meter will be assigned immediately.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Billing History Transfer -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-file-invoice-dollar text-blue-600 mr-2"></i>
                            Billing History Transfer
                        </h3>

                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox"
                                        name="transfer_billing_history"
                                        id="transferBillingHistory"
                                        value="1"
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <div class="ml-3">
                                    <label for="transferBillingHistory" class="font-medium text-gray-700">
                                        Transfer all bills and meter readings to the new customer
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        If checked, all billing history and meter readings for this meter will be transferred to the new customer.
                                        This is useful when a meter was mistakenly assigned to the wrong customer.
                                    </p>
                                </div>
                            </div>

                            <!-- Warning message for transfer -->
                            <div id="transferWarning" class="hidden bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 text-sm">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                                    <div>
                                        <strong class="font-medium">Warning:</strong>
                                        <span id="transferWarningMessage">
                                            All bills and meter readings for meter {{ $meter->meter_number }} will be moved to the new customer.
                                            The current customer will lose this billing history.
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary of items to be transferred -->
                            @php
                                $billsCount = $meter->bills()->where('customer_id', $customer->id)->count();
                                $billsTotal = $meter->bills()->where('customer_id', $customer->id)->sum('total_amount');
                                $readingsCount = $meter->meterReadings()->where('customer_id', $customer->id)->where('reading_status','initial')->count();
                            @endphp

                            @if($billsCount > 0 || $readingsCount > 0)
                            <div class="mt-2 p-3 bg-white rounded border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Items to be transferred from {{ $customer->first_name }} {{ $customer->last_name }}:</h4>
                                <ul class="text-sm space-y-1">
                                    @if($billsCount > 0)
                                    <li class="flex items-center text-gray-600">
                                        <i class="fas fa-file-invoice mr-2 text-blue-500"></i>
                                        {{ $billsCount }} bill(s) totaling KSh {{ number_format($billsTotal, 2) }}
                                    </li>
                                    @endif
                                    @if($readingsCount > 0)
                                    <li class="flex items-center text-gray-600">
                                        <i class="fas fa-tachometer-alt mr-2 text-green-500"></i>
                                        {{ $readingsCount }} meter reading(s)
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            @else
                            <div class="mt-2 p-3 bg-gray-100 rounded border border-gray-200">
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    No bills or meter readings found for this meter with the current customer.
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- Additional Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes
                        </label>
                        <textarea name="notes" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional information about this meter transfer..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.customers.show', $customer) }}"
                           class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                            Cancel
                        </a>
                        <button type="submit" id="submitButton"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 flex items-center">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            Unassign & Reassign Meter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Meter Information -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tachometer-alt text-blue-600 mr-2"></i>
                    Meter Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Meter Number:</span>
                        <span class="font-semibold text-blue-600">{{ $meter->meter_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Category:</span>
                        <span>{{ $meter->meterCategory->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Type:</span>
                        <span class="capitalize">{{ $meter->meter_type }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Current Reading:</span>
                        <span class="font-semibold">{{ number_format($meter->current_reading, 2) }} m³</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Current Balance:</span>
                        <span class="font-semibold {{ $meter->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            KSh {{ number_format($meter->current_balance, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Installation Date:</span>
                        <span>{{ $meter->installation_date?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Current Customer -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-green-600 mr-2"></i>
                    Current Customer
                </h3>
                <div class="space-y-2">
                    <p class="font-semibold">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                    <p class="text-sm text-gray-600">{{ $customer->customer_number }}</p>
                    <p class="text-sm"><i class="fas fa-phone mr-2 text-gray-400"></i>{{ $customer->phone }}</p>
                    <p class="text-sm"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>{{ $customer->physical_address }}</p>
                    <p class="text-sm"><i class="fas fa-home mr-2 text-gray-400"></i>Plot: {{ $customer->plot_number }}</p>
                    <div class="mt-2 pt-2 border-t">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-tachometer-alt mr-1"></i>
                            {{ $customer->meters()->count() }} meter(s) assigned
                        </span>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Important Notes
                </h3>
                <ul class="space-y-2 text-sm text-yellow-700">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Meter will be unassigned from current customer immediately</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>A new installation record will be created for the new customer</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Initial reading for new assignment will be recorded</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>All meter history remains intact</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Both customers will have notes added about this transfer</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-selection {
        min-height: 42px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
    }
    .select2-selection__rendered {
        padding-top: 8px !important;
        padding-bottom: 8px !important;
    }
    .select2-selection__arrow {
        height: 41px !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Auto-check billing history transfer when meter incorrectly assigned is selected
$('#unassignmentReason').on('change', function() {
    if ($(this).val() === 'meter_incorrectly_assigned') {
        $('#transferBillingHistory').prop('checked', true);
        $('#transferWarning').removeClass('hidden'); // Show warning when checked
    }
});

// Also show warning when checkbox is manually checked
$('#transferBillingHistory').on('change', function() {
    if ($(this).is(':checked')) {
        $('#transferWarning').removeClass('hidden');
    } else {
        $('#transferWarning').addClass('hidden');
    }
});

// Trigger initial state
$('#unassignmentReason').trigger('change');

$(document).ready(function() {
    // Initialize Select2 for customer search
    const $customerSelect = $('#customerSearch');

    $customerSelect.select2({
        placeholder: 'Search for a customer by name, phone, ID, or account number...',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.customers.search") }}',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term,
                    include_meter_info: true
                };
            },
            processResults: function(data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        templateResult: formatCustomer,
        templateSelection: formatCustomerSelection
    });

    function formatCustomer(customer) {
        if (customer.loading) {
            return customer.text;
        }

        var $container = $(
            '<div class="flex items-start py-1">' +
                '<div class="flex-1">' +
                    '<div class="font-semibold text-gray-900">' + customer.text + '</div>' +
                    '<div class="text-xs text-gray-600 mt-1">' +
                        '<span class="mr-3"><i class="fas fa-phone mr-1"></i>' + (customer.phone || 'N/A') + '</span>' +
                        '<span><i class="fas fa-map-marker-alt mr-1"></i>' + (customer.plot || 'N/A') + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="ml-2">' +
                    getMeterBadge(customer) +
                '</div>' +
            '</div>'
        );

        return $container;
    }

    function formatCustomerSelection(customer) {
        return customer.text || customer.id;
    }

    function getMeterBadge(customer) {
        if (customer.has_meter) {
            return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">' +
                   '<i class="fas fa-tachometer-alt mr-1"></i>' + customer.meter_count + ' meter(s)' +
                   '</span>';
        } else {
            return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">' +
                   '<i class="fas fa-check mr-1"></i>No meter' +
                   '</span>';
        }
    }

    // Toggle between existing and new customer sections
    $('input[name="action"]').on('change', function() {
        if ($(this).val() === 'existing') {
            $('#newCustomerSection').addClass('hidden');
            $('#existingCustomerSection').removeClass('hidden');
            $('#installationDetails').removeClass('hidden');

            // Disable new customer fields
            $('#newCustomerSection input, #newCustomerSection textarea').prop('disabled', true);
            $('#existingCustomerSection select, #existingCustomerSection input').prop('disabled', false);

            // Make installation fields required
            $('input[name="new_installation_date"]').prop('required', true);
            $('input[name="new_initial_reading"]').prop('required', true);
        } else {
            $('#newCustomerSection').removeClass('hidden');
            $('#existingCustomerSection').addClass('hidden');
            $('#installationDetails').addClass('hidden');

            // Enable new customer fields
            $('#newCustomerSection input, #newCustomerSection textarea').prop('disabled', false);
            $('#existingCustomerSection select, #existingCustomerSection input').prop('disabled', true);

            // Make installation fields not required
            $('input[name="new_installation_date"]').prop('required', false);
            $('input[name="new_initial_reading"]').prop('required', false);

            // Clear customer selection
            $customerSelect.val(null).trigger('change');
            $('#customerMeterAlert').addClass('hidden');
        }
    });

    // Check if selected customer already has a meter
    $customerSelect.on('select2:select', function(e) {
        const data = e.params.data;

        if (data.has_meter) {
            $('#customerMeterMessage').html(
                `${data.text} already has ${data.meter_count} meter(s) assigned. ` +
                `You are about to assign an additional meter to this customer.`
            );
            $('#customerMeterAlert').removeClass('hidden');
        } else {
            $('#customerMeterAlert').addClass('hidden');
        }
    });

    $customerSelect.on('select2:clear', function() {
        $('#customerMeterAlert').addClass('hidden');
    });

    // Form validation
    $('#reassignForm').on('submit', function(e) {
        const action = $('input[name="action"]:checked').val();

        if (action === 'existing') {
            const customerId = $customerSelect.val();
            if (!customerId) {
                e.preventDefault();
                alert('Please select a customer to assign the meter to.');
                $customerSelect.select2('open');
                return false;
            }
        }
    });

    // Trigger initial state
    $('input[name="action"]:checked').trigger('change');
});
</script>
@endpush
@endsection
