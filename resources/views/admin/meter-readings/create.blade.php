@extends('layouts.app')

@section('title', 'Record Meter Reading - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">

    <!-- Header -->
    @php
    $actionButtons = [];



    @endphp

    @include('components.dashboard-header',[
        'title' => 'Record Meter Reading',
        'subtitle' => 'Record new meter reading for customer',
        'actionButtons' => $actionButtons
    ])

    <br>
    <!-- Customer Information Card -->
    @if($customer)
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <h2 class="text-xl font-semibold text-blue-700 mb-4">Customer Information</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Customer Name</p>
                <p class="font-semibold text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Customer ID</p>
                <p class="font-semibold text-blue-600">{{ $customer->customer_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Location</p>
                <p class="font-semibold text-gray-900">{{ $customer->plot_number }}, {{ $customer->house_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Meters</p>
                <p class="font-semibold text-green-600">{{ $customer->meters->count() }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Meter Selection Card -->
    @if($customer && $meters->count() > 0)
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <h2 class="text-xl font-semibold text-green-700 mb-4">Select Meter</h2>

        <div class="space-y-4">
            @foreach($meters as $meterOption)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-green-300 transition duration-200 cursor-pointer meter-option
                        {{ $meter && $meter->id == $meterOption->id ? 'border-green-500 bg-green-50' : '' }}"
                    data-meter-id="{{ $meterOption->id }}"
                    onclick="selectMeter({{ $meterOption->id }})">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $meterOption->meter_number }}</h4>
                        <p class="text-sm text-gray-600">{{ $meterOption->meterCategory->name ?? 'No Category' }} • {{ ucfirst($meterOption->meter_type) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Current Reading: {{ number_format($meterOption->current_reading, 2) }} m³</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ ucfirst($meterOption->status) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">Installed: {{ $meterOption->installation_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Reading Information Card -->
    @if($meter)
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <h2 class="text-xl font-semibold text-green-700 mb-4">Reading Information</h2>

        <!-- Selected Meter Info -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-green-800">Selected Meter</h3>
                    <p class="text-green-700">{{ $meter->meter_number }} • {{ $meter->meterCategory->name ?? 'No Category' }}</p>
                    <p class="text-sm text-green-600">Current: {{ number_format($meter->current_reading, 2) }} m³</p>
                </div>
                <button type="button" onclick="resetMeterSelection()" class="text-green-600 hover:text-green-800 text-sm font-medium">
                    Change Meter
                </button>
            </div>
        </div>

        <!-- Previous Reading Info -->
        @if($lastReading)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">Last Reading</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-blue-600">Reading Date</p>
                    <p class="font-semibold">{{ $lastReading->reading_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-blue-600">Previous Reading</p>
                    <p class="font-semibold">{{ number_format($lastReading->current_reading, 2) }} m³</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-yellow-800 mb-2">Initial Meter Reading</h3>
            <p class="text-sm text-yellow-700">This is the first reading for this meter. Initial reading: <strong>{{ number_format($meter->initial_reading, 2) }} m³</strong></p>
        </div>
        @endif

        <!-- Reading Form -->
        <form action="{{ route('admin.meter-readings.store') }}" method="POST" enctype="multipart/form-data" id="readingForm">
            @csrf

            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <input type="hidden" name="meter_id" id="selected_meter_id" value="{{ $meter->id }}" required>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Current Reading -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Reading (m³) *</label>
                    <input type="number"
                            name="current_reading"
                            id="current_reading"
                            step="0.01"
                            min="{{ $lastReading ? $lastReading->current_reading : ($meter->initial_reading ?? 0) }}"
                            required
                            value="{{ old('current_reading') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                            placeholder="Enter current reading">
                    <div id="readingValidation" class="mt-1 text-sm"></div>
                    @error('current_reading')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reading Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reading Date *</label>
                    <input type="date"
                            name="reading_date"
                            required
                            max="{{ date('Y-m-d') }}"
                            value="{{ old('reading_date', date('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                    @error('reading_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reading Status Selection -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reading Status *</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <input type="radio" id="status_recorded" name="reading_status" value="recorded" 
                                class="hidden peer" checked onchange="toggleReadingFields()">
                            <label for="status_recorded" 
                                class="block p-4 border-2 border-gray-300 rounded-lg cursor-pointer text-center hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition duration-200">
                                <i class="fas fa-check-circle text-xl mb-2 text-blue-600"></i>
                                <div class="font-semibold">Normal Reading</div>
                                <div class="text-sm text-gray-600">Meter accessible and working</div>
                            </label>
                        </div>

                        <div>
                            <input type="radio" id="status_exception" name="reading_status" value="exception" 
                                class="hidden peer" onchange="toggleReadingFields()">
                            <label for="status_exception" 
                                class="block p-4 border-2 border-gray-300 rounded-lg cursor-pointer text-center hover:border-red-400 peer-checked:border-red-500 peer-checked:bg-red-50 transition duration-200">
                                <i class="fas fa-exclamation-triangle text-xl mb-2 text-red-600"></i>
                                <div class="font-semibold">Exception</div>
                                <div class="text-sm text-gray-600">Meter inaccessible or faulty</div>
                            </label>
                        </div>

                        <div>
                            <input type="radio" id="status_estimated" name="reading_status" value="estimated" 
                                class="hidden peer" onchange="toggleReadingFields()">
                            <label for="status_estimated" 
                                class="block p-4 border-2 border-gray-300 rounded-lg cursor-pointer text-center hover:border-yellow-400 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition duration-200">
                                <i class="fas fa-calculator text-xl mb-2 text-yellow-600"></i>
                                <div class="font-semibold">Estimated</div>
                                <div class="text-sm text-gray-600">Meter stuck but water used</div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Exception Details (Hidden by default) -->
                <div id="exceptionFields" class="mt-6 hidden">
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    <strong>Exception Recording:</strong> Meter reading could not be taken normally. Please provide details.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exception Type *</label>
                            <select name="exception_type" id="exception_type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">Select Exception Type</option>
                                <option value="inaccessible">Meter Inaccessible (Gate closed, locked)</option>
                                <option value="faulty">Meter Faulty (Not working)</option>
                                <option value="stuck">Meter Stuck (Not moving but water used)</option>
                                <option value="damaged">Meter Damaged</option>
                                <option value="vandalized">Meter Vandalized</option>
                                <option value="other">Other Reason</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exception Evidence Photo</label>
                            <input type="file" name="exception_evidence" id="exception_evidence" 
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Exception Reason *</label>
                        <textarea name="exception_reason" id="exception_reason" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                placeholder="Detailed explanation of why the meter couldn't be read..."></textarea>
                    </div>
                </div>

                <!-- Estimated Consumption (Hidden by default) -->
                <div id="estimatedFields" class="mt-6 hidden">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calculator text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Estimated Reading:</strong> Meter is stuck but water consumption needs to be estimated.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Consumption (m³) *</label>
                            <input type="number" name="estimated_consumption" id="estimated_consumption" 
                                step="0.01" min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                placeholder="Estimated water consumption">
                            <div class="mt-2 text-sm text-gray-600">
                                <button type="button" onclick="calculateEstimation()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-calculator mr-1"></i> Calculate based on history
                                </button>
                                <span id="estimationResult" class="ml-2 text-green-600 hidden"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimation Notes</label>
                            <textarea name="estimation_notes" id="estimation_notes" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                    placeholder="How the estimation was calculated..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

           <!-- Simple Photo Capture Section -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Meter Reading Photo (Optional)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 transition duration-200">
                    <!-- Camera Preview -->
                    <div id="cameraPreview" class="hidden mb-4">
                        <video id="video" width="100%" height="auto" autoplay class="rounded-lg shadow-md"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                    </div>

                    <!-- Captured Image Preview -->
                    <div id="imagePreview" class="hidden mb-4">
                        <img id="preview" src="" alt="Captured reading" class="max-w-full h-auto rounded-lg mx-auto max-h-64 shadow-md">
                        <p class="text-sm text-green-600 mt-2">✓ Photo captured successfully</p>
                        <button type="button"
                                id="retake"
                                class="mt-2 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Retake Photo
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div id="cameraControls" class="space-y-2">
                        <button type="button"
                                id="startCamera"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Take Photo
                        </button>

                        <div id="captureControls" class="hidden space-y-2">
                            <button type="button"
                                    id="capture"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Capture Photo
                            </button>
                        </div>
                    </div>

                    <!-- File Input Fallback -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-2">Or upload existing photo</p>
                        <input type="file"
                                name="reading_image"
                                id="fileInput"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition duration-200">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes"
                            rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                            placeholder="Any additional notes about this reading...">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-between items-center">
                <div class="flex space-x-3">


                    @if($customer)
                    <a href="{{ route('admin.customers.show', $customer) }}"
                        class="bg-blue-600 me-2 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $customer->first_name }}'s Profile
                    </a>
                    @else
                    <a href="{{ route('admin.meter-readings.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Readings
                    </a>
                    @endif
                </div>

                <div class="flex space-x-4">

                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Record Reading
                    </button>
                </div>
            </div>
        </form>
    </div>
    @else
    <!-- No Meter Selected State -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
        <svg class="w-16 h-16 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-yellow-800 mb-2">No Meter Selected</h3>
        <p class="text-yellow-600 mb-4">Please select a meter from the list above to record a reading.</p>
    </div>
    @endif
</div>


<script>
let selectedMeterId = {{ $meter ? $meter->id : 'null' }};

function selectMeter(meterId) {
    selectedMeterId = meterId;
    document.getElementById('selected_meter_id').value = meterId;

    // Update UI to show selected meter
    document.querySelectorAll('.meter-option').forEach(option => {
        option.classList.remove('border-green-500', 'bg-green-50');
        if (option.dataset.meterId == meterId) {
            option.classList.add('border-green-500', 'bg-green-50');
        }
    });

    // Reload page with selected meter
    const url = new URL(window.location.href);
    url.searchParams.set('meter', meterId);
    window.location.href = url.toString();
}

function resetMeterSelection() {
    const url = new URL(window.location.href);
    url.searchParams.delete('meter');
    window.location.href = url.toString();
}

// Real-time reading validation
document.getElementById('current_reading')?.addEventListener('input', function() {
    const currentReading = parseFloat(this.value);
    const minReading = parseFloat(this.getAttribute('min'));
    const validationDiv = document.getElementById('readingValidation');

    if (currentReading < minReading) {
        validationDiv.innerHTML = `<div class="text-red-600 flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Reading (${currentReading}) is less than previous reading (${minReading})
        </div>`;
    } else {
        const consumption = (currentReading - minReading).toFixed(2);
        validationDiv.innerHTML = `<div class="text-green-600 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            Consumption: ${consumption} m³
        </div>`;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const preview = document.getElementById('preview');
    const startCamera = document.getElementById('startCamera');
    const capture = document.getElementById('capture');
    const retake = document.getElementById('retake');
    const cameraPreview = document.getElementById('cameraPreview');
    const imagePreview = document.getElementById('imagePreview');
    const cameraControls = document.getElementById('cameraControls');
    const captureControls = document.getElementById('captureControls');
    const fileInput = document.getElementById('fileInput');
    let stream = null;

    // Start Camera
    startCamera?.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });
            video.srcObject = stream;
            cameraPreview.classList.remove('hidden');
            captureControls.classList.remove('hidden');
            startCamera.classList.add('hidden');
        } catch (err) {
            console.error('Error accessing camera:', err);
            alert('Unable to access camera. Please check permissions or use file upload.');
        }
    });

    // Capture Photo
    capture?.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert to blob and create file
        canvas.toBlob(function(blob) {
            const file = new File([blob], 'meter-reading-' + Date.now() + '.jpg', { type: 'image/jpeg' });

            // Create a new FileList
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            // Show preview
            preview.src = URL.createObjectURL(blob);
            imagePreview.classList.remove('hidden');
            cameraPreview.classList.add('hidden');
            captureControls.classList.add('hidden');
            startCamera.classList.remove('hidden');

            // Stop camera
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }, 'image/jpeg', 0.8);
    });

    // Retake Photo
    retake?.addEventListener('click', function() {
        imagePreview.classList.add('hidden');
        cameraPreview.classList.remove('hidden');
        preview.src = '';
        fileInput.value = '';

        // Restart camera
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        startCamera.click();
    });

    // Handle file input change
    fileInput?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreview.classList.remove('hidden');

                // Hide camera controls when file is selected
                cameraPreview.classList.add('hidden');
                captureControls.classList.add('hidden');
                startCamera.classList.remove('hidden');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Form validation
    document.getElementById('readingForm')?.addEventListener('submit', function(e) {
        const currentReading = parseFloat(document.getElementById('current_reading').value);
        const minReading = parseFloat(document.getElementById('current_reading').getAttribute('min'));

        if (currentReading < minReading) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Reading',
                text: `Current reading cannot be less than ${minReading.toFixed(2)} m³`,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'OK'
            });
        }
    });

    // Cleanup when leaving page
    window.addEventListener('beforeunload', function() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
});
</script>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Try Again',
            showCloseButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('input[name="current_reading"]')?.focus();
            }
        });
    @endif

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Great!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#059669',
            confirmButtonText: 'Continue',
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true
        });
    @endif

    // Toggle reading fields based on status
    function toggleReadingFields() {
        const statusRecorded = document.getElementById('status_recorded');
        const statusException = document.getElementById('status_exception');
        const statusEstimated = document.getElementById('status_estimated');
        
        const currentReadingField = document.getElementById('current_reading');
        const exceptionFields = document.getElementById('exceptionFields');
        const estimatedFields = document.getElementById('estimatedFields');
        
        // Reset all fields
        currentReadingField.required = true;
        currentReadingField.disabled = false;
        exceptionFields.classList.add('hidden');
        estimatedFields.classList.add('hidden');
        
        // Clear validation messages
        document.getElementById('readingValidation').innerHTML = '';
        
        if (statusRecorded.checked) {
            // Normal reading
            currentReadingField.required = true;
            currentReadingField.disabled = false;
        } else if (statusException.checked) {
            // Exception - no current reading needed
            currentReadingField.required = false;
            currentReadingField.disabled = true;
            currentReadingField.value = '';
            exceptionFields.classList.remove('hidden');
        } else if (statusEstimated.checked) {
            // Estimated reading
            currentReadingField.required = false;
            currentReadingField.disabled = true;
            currentReadingField.value = '';
            estimatedFields.classList.remove('hidden');
            
            // Auto-calculate estimation
            calculateEstimation();
        }
    }

    // Calculate estimation based on history
    async function calculateEstimation() {
        const customerId = document.querySelector('input[name="customer_id"]').value;
        const meterId = document.querySelector('input[name="meter_id"]').value;
        const resultSpan = document.getElementById('estimationResult');
        
        if (!customerId || !meterId) {
            resultSpan.textContent = 'Please select a customer and meter first';
            resultSpan.classList.remove('hidden');
            resultSpan.classList.remove('text-green-600');
            resultSpan.classList.add('text-red-600');
            return;
        }
        
        try {
            const response = await fetch(`/admin/customers/${customerId}/meters/${meterId}/estimate-consumption`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.estimated_consumption) {
                document.getElementById('estimated_consumption').value = data.estimated_consumption;
                resultSpan.textContent = `Estimated: ${data.estimated_consumption} m³ (based on average consumption)`;
                resultSpan.classList.remove('hidden');
                resultSpan.classList.remove('text-red-600');
                resultSpan.classList.add('text-green-600');
                
                // Add to notes
                const notesField = document.querySelector('textarea[name="notes"]');
                if (notesField && !notesField.value.includes('Estimated based on')) {
                    notesField.value = (notesField.value ? notesField.value + '\n' : '') + 
                        `Estimated consumption based on historical average of ${data.estimated_consumption} m³.`;
                }
            } else {
                resultSpan.textContent = 'Could not calculate estimation. Please enter manually.';
                resultSpan.classList.remove('hidden');
                resultSpan.classList.remove('text-green-600');
                resultSpan.classList.add('text-red-600');
            }
        } catch (error) {
            console.error('Estimation error:', error);
            resultSpan.textContent = 'Error calculating estimation';
            resultSpan.classList.remove('hidden');
            resultSpan.classList.remove('text-green-600');
            resultSpan.classList.add('text-red-600');
        }
    }

    // Update form validation
    document.getElementById('readingForm')?.addEventListener('submit', function(e) {
        const readingStatus = document.querySelector('input[name="reading_status"]:checked')?.value;
        const currentReading = document.getElementById('current_reading').value;
        const minReading = parseFloat(document.getElementById('current_reading').getAttribute('min'));
        
        // Normal reading validation
        if (readingStatus === 'recorded') {
            if (!currentReading) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Current Reading Required',
                    text: 'Please enter the current meter reading',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            if (parseFloat(currentReading) < minReading) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Reading',
                    text: `Current reading cannot be less than ${minReading.toFixed(2)} m³`,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'OK'
                });
            }
        }
        
        // Exception validation
        if (readingStatus === 'exception') {
            const exceptionType = document.getElementById('exception_type').value;
            const exceptionReason = document.getElementById('exception_reason').value;
            
            if (!exceptionType || !exceptionReason) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Exception Details Required',
                    text: 'Please provide exception type and reason',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'OK'
                });
            }
        }
        
        // Estimated reading validation
        if (readingStatus === 'estimated') {
            const estimatedConsumption = document.getElementById('estimated_consumption').value;
            
            if (!estimatedConsumption || parseFloat(estimatedConsumption) <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Estimated Consumption Required',
                    text: 'Please enter estimated water consumption',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'OK'
                });
            }
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleReadingFields();
    });
});
</script>
@endsection
