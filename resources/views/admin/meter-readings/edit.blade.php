@extends('layouts.app')

@section('title', 'Edit Meter Reading')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Meter Reading</h1>
                <p class="text-gray-600 mt-1">Update meter reading details</p>
            </div>
            <a href="{{ route('admin.meter-readings.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                Back to List
            </a>
        </div>
    </div>

    @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
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

    @if(!$meter || !isset($meter->id))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <strong>Error:</strong> Meter not found for this reading. This reading may be orphaned.
                    </p>
                    <div class="mt-2">
                        <p class="text-sm text-red-600">
                            Meter Reading ID: {{ $meterReading->id }}<br>
                            Meter ID in reading: {{ $meterReading->meter_id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <form method="POST" action="{{ route('admin.meter-readings.update', $meterReading) }}" enctype="multipart/form-data" id="updateForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    <!-- Customer & Meter Info -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Meter Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Meter Number</p>
                                <p class="font-medium">{{ $meter->meter_number ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">{{ optional($meter->meterCategory)->name ?? 'No Category' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Zone</p>
                                <p class="font-medium">{{ optional($meter->zone)->name ?? 'No Zone' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Customer</p>
                                @if($customer && isset($customer->id))
                                    <p class="font-medium">{{ $customer->full_name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600">{{ $customer->customer_number ?? 'N/A' }}</p>
                                @else
                                    <p class="font-medium text-red-600">No Customer Assigned</p>
                                    <a href="{{ route('admin.meters.edit', $meter->id) }}"
                                       class="text-sm text-blue-600 hover:underline">
                                        Assign customer to meter
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Reading Context -->
                    <div class="border border-gray-200 rounded-lg p-4 mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Reading Sequence Context</h4>
                        <div class="space-y-3">
                            @if($previousReading)
                                <div class="flex items-center text-sm">
                                    <div class="w-1/3 text-gray-500">Previous Reading:</div>
                                    <div class="w-2/3">
                                        {{ number_format($previousReading->current_reading, 2) }} m³
                                        <span class="text-gray-600 text-xs">({{ $previousReading->reading_date->format('M d, Y') }})</span>
                                    </div>
                                </div>
                            @endif

                            @if($nextReading)
                                <div class="flex items-center text-sm">
                                    <div class="w-1/3 text-gray-500">Next Reading:</div>
                                    <div class="w-2/3">
                                        {{ number_format($nextReading->current_reading, 2) }} m³
                                        <span class="text-gray-600 text-xs">({{ $nextReading->reading_date->format('M d, Y') }})</span>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center text-sm">
                                <div class="w-1/3 text-gray-500">Meter Initial Reading:</div>
                                <div class="w-2/3 font-medium">{{ number_format($meter->initial_reading ?? 0, 2) }} m³</div>
                            </div>
                        </div>
                    </div>

                    <!-- Rest of your form remains the same... -->
                    <!-- ... -->

                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <div>
                        <button type="button"
                                onclick="if(confirm('Are you sure you want to delete this reading?')) { document.getElementById('deleteForm').submit(); }"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Delete Reading
                        </button>
                    </div>
                    <div class="space-x-3">
                        <a href="{{ route('admin.meter-readings.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Update Reading
                        </button>
                    </div>
                </div>
            </form>

            <!-- Delete Form -->
            <form id="deleteForm" action="{{ route('admin.meter-readings.destroy', $meterReading) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    @endif
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const readingStatus = document.getElementById('reading_status');
    const currentReadingField = document.getElementById('current_reading_field');
    const estimatedConsumptionField = document.getElementById('estimated_consumption_field');
    const exceptionFields = document.getElementById('exception_fields');
    const currentReadingInput = document.getElementById('current_reading');
    const estimatedConsumptionInput = document.getElementById('estimated_consumption');
    const exceptionTypeInput = document.getElementById('exception_type');
    const exceptionReasonInput = document.getElementById('exception_reason');

    function toggleFields() {
        const status = readingStatus.value;

        // Hide all fields first
        currentReadingField.style.display = 'none';
        estimatedConsumptionField.style.display = 'none';
        exceptionFields.style.display = 'none';

        // Remove required attributes
        currentReadingInput.removeAttribute('required');
        estimatedConsumptionInput.removeAttribute('required');
        exceptionTypeInput.removeAttribute('required');
        exceptionReasonInput.removeAttribute('required');

        // Show relevant fields and set required attributes
        switch(status) {
            case 'recorded':
                currentReadingField.style.display = 'block';
                currentReadingInput.setAttribute('required', 'required');
                break;
            case 'estimated':
                estimatedConsumptionField.style.display = 'block';
                estimatedConsumptionInput.setAttribute('required', 'required');
                break;
            case 'exception':
                exceptionFields.style.display = 'block';
                exceptionTypeInput.setAttribute('required', 'required');
                exceptionReasonInput.setAttribute('required', 'required');
                break;
        }
    }

    // Initial toggle
    toggleFields();

    // Listen for changes
    readingStatus.addEventListener('change', toggleFields);

    // Form validation
    const updateForm = document.getElementById('updateForm');
    if (updateForm) {
        updateForm.addEventListener('submit', function(e) {
            const status = readingStatus.value;
            let isValid = true;

            if (status === 'recorded' && !currentReadingInput.value) {
                alert('Current reading is required for recorded readings');
                isValid = false;
            } else if (status === 'estimated' && !estimatedConsumptionInput.value) {
                alert('Estimated consumption is required for estimated readings');
                isValid = false;
            } else if (status === 'exception' && (!exceptionTypeInput.value || !exceptionReasonInput.value.trim())) {
                alert('Exception type and reason are required for exception readings');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endsection
