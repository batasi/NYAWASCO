@extends('layouts.app')

@section('title', 'Record Meter Reading - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-700">Record Meter Reading</h1>
                <p class="text-gray-600">Record new meter reading for customer</p>
            </div>
            <a href="{{ $customer ? route('admin.customers.show', $customer) : route('admin.meter-readings.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>

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
                    <p class="text-sm text-gray-500">Meter Number</p>
                    <p class="font-semibold text-green-600">{{ $meter->meter_number ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Reading Information Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <h2 class="text-xl font-semibold text-green-700 mb-4">Reading Information</h2>
            
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
            @elseif($meter)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-yellow-800 mb-2">Initial Meter Reading</h3>
                <p class="text-sm text-yellow-700">This is the first reading for this meter. Initial reading: <strong>{{ number_format($meter->initial_reading, 2) }} m³</strong></p>
            </div>
            @endif

            <!-- Reading Form -->
            <form action="{{ route('admin.meter-readings.store') }}" method="POST" enctype="multipart/form-data" id="readingForm">
                @csrf
                
                <input type="hidden" name="customer_id" value="{{ $customer->id ?? '' }}">

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Current Reading -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Reading (m³) *</label>
                        <input type="number" 
                               name="current_reading" 
                               step="0.01" 
                               min="0"
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Enter current reading">
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
                               value="{{ old('reading_date', date('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <!-- Camera Capture Section -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meter Reading Photo</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <!-- Camera Preview -->
                        <div id="cameraPreview" class="hidden mb-4">
                            <video id="video" width="100%" height="auto" autoplay class="rounded-lg"></video>
                            <canvas id="canvas" class="hidden"></canvas>
                        </div>
                        
                        <!-- Captured Image Preview -->
                        <div id="imagePreview" class="hidden mb-4">
                            <img id="preview" src="" alt="Captured reading" class="max-w-full h-auto rounded-lg mx-auto max-h-64">
                        </div>

                        <!-- Action Buttons -->
                        <div id="cameraControls" class="space-y-2">
                            <button type="button" 
                                    id="startCamera" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Open Camera
                            </button>
                            
                            <div id="captureControls" class="hidden space-y-2">
                                <button type="button" 
                                        id="capture" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Capture Photo
                                </button>
                                
                                <button type="button" 
                                        id="retake" 
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Retake Photo
                                </button>
                            </div>
                        </div>

                        <!-- File Input Fallback -->
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-2">Or upload existing photo</p>
                            <input type="file" 
                                   name="reading_image" 
                                   id="fileInput"
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                              placeholder="Any additional notes about this reading..."></textarea>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end">
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Record Reading
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Camera JavaScript -->
<script>
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
    startCamera.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } // Use back camera
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
    capture.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert to blob and create file
        canvas.toBlob(function(blob) {
            const file = new File([blob], 'meter-reading.jpg', { type: 'image/jpeg' });
            
            // Create a new FileList (simulate file input)
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
    retake.addEventListener('click', function() {
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
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
@endsection