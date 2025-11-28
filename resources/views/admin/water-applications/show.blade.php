@extends('layouts.app')

@php
    // Ensure categories variable exists
    $categories = $categories ?? collect();
@endphp

@section('title', 'Application Review - NYAWASCO')

@section('content')
@can('view applications')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-blue-800">Application Review</h1>
            <p class="text-gray-600 mt-2">Application #{{ $application->id }} - {{ $application->full_name }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.water-applications.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Applications
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Application Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Application Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                    Applicant Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $application->full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-gray-900">{{ $application->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-gray-900">{{ $application->phone }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">National ID</label>
                            <p class="mt-1 text-gray-900">{{ $application->national_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">KRA Pin</label>
                            <p class="mt-1 text-gray-900">{{ $application->kra_pin ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Property Owner</label>
                            <p class="mt-1 text-gray-900">{{ $application->property_owner }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-semibold text-green-700 mb-4 flex items-center">
                    <i class="fas fa-home mr-2 text-green-600"></i>
                    Property Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Plot Number</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $application->plot_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">House Number</label>
                            <p class="mt-1 text-gray-900">{{ $application->house_number }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estate/Area</label>
                            <p class="mt-1 text-gray-900">{{ $application->estate ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Expected Users</label>
                            <p class="mt-1 text-gray-900">{{ $application->expected_users ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Physical Address</label>
                    <p class="mt-1 text-gray-900">{{ $application->plot_number }}, {{ $application->house_number }}{{ $application->estate ? ', ' . $application->estate : '' }}</p>
                </div>
            </div>

            <!-- Application Documents -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-semibold text-purple-700 mb-4 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-purple-600"></i>
                    Application Documents
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- National ID Document -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-gray-700">National ID</h3>
                            @if($application->national_id_file)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-check mr-1 text-xs"></i>
                                    Uploaded
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-times mr-1 text-xs"></i>
                                    Missing
                                </span>
                            @endif
                        </div>
                        @if($application->national_id_file)
                            <div class="flex flex-col space-y-2">
                                <a href="{{ asset('storage/' . $application->national_id_file) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Document
                                </a>
                                <a href="{{ asset('storage/' . $application->national_id_file) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                    <i class="fas fa-download mr-2"></i>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic text-center py-2">Document not uploaded</p>
                        @endif
                    </div>

                    <!-- KRA Pin Certificate -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-gray-700">KRA Pin Certificate</h3>
                            @if($application->kra_pin_file)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-check mr-1 text-xs"></i>
                                    Uploaded
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-times mr-1 text-xs"></i>
                                    Missing
                                </span>
                            @endif
                        </div>
                        @if($application->kra_pin_file)
                            <div class="flex flex-col space-y-2">
                                <a href="{{ asset('storage/' . $application->kra_pin_file) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Document
                                </a>
                                <a href="{{ asset('storage/' . $application->kra_pin_file) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                    <i class="fas fa-download mr-2"></i>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic text-center py-2">Document not uploaded</p>
                        @endif
                    </div>

                    <!-- Title Document -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-gray-700">Title Document</h3>
                            @if($application->title_document)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-check mr-1 text-xs"></i>
                                    Uploaded
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded flex items-center">
                                    <i class="fas fa-times mr-1 text-xs"></i>
                                    Missing
                                </span>
                            @endif
                        </div>
                        @if($application->title_document)
                            <div class="flex flex-col space-y-2">
                                <a href="{{ asset('storage/' . $application->title_document) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Document
                                </a>
                                <a href="{{ asset('storage/' . $application->title_document) }}" 
                                   download
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm flex items-center justify-center transition duration-200">
                                    <i class="fas fa-download mr-2"></i>
                                    Download
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic text-center py-2">Document not uploaded</p>
                        @endif
                    </div>
                </div>

                <!-- Document Status Summary -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    @php
                        $uploadedDocs = 0;
                        $totalDocs = 3;
                        if ($application->national_id_file) $uploadedDocs++;
                        if ($application->kra_pin_file) $uploadedDocs++;
                        if ($application->title_document) $uploadedDocs++;
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Document Completion</span>
                        <span class="text-sm font-semibold {{ $uploadedDocs === $totalDocs ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $uploadedDocs }}/{{ $totalDocs }} documents
                        </span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $uploadedDocs === $totalDocs ? 'green' : 'yellow' }}-600 h-2 rounded-full" 
                             style="width: {{ ($uploadedDocs / $totalDocs) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Status -->
        <div class="space-y-6">
            <!-- Application Status -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Application Status
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Status</label>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'declined' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$application->status] }}">
                            <i class="fas fa-circle mr-2 text-xs"></i>
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Application Date</label>
                        <p class="text-gray-900">{{ $application->created_at->format('M d, Y') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reference ID</label>
                        <p class="text-blue-600 font-mono font-semibold">#WC{{ $application->id }}</p>
                    </div>
                    
                    @if($application->processed_by && $application->processed_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Processed By</label>
                        <p class="text-gray-900">{{ $application->processedBy->name ?? 'System' }}</p>
                        <p class="text-sm text-gray-500">{{ $application->processed_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                    Quick Actions
                </h2>
                
                <div class="space-y-3">
                    @if($application->status === 'pending')
                    <button onclick="showApproveModal()" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-check mr-2"></i>
                        Approve Application
                    </button>
                    
                    <button onclick="showDeclineModal()" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Decline Application
                    </button>
                    @endif
                    
                    @if($application->customer)
                    <a href="{{ route('admin.customers.show', $application->customer) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-user mr-2"></i>
                        View Customer
                    </a>
                    @endif
                </div>
            </div>

            <!-- Document Checklist -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clipboard-check mr-2 text-green-600"></i>
                    Document Checklist
                </h2>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">National ID</span>
                        @if($application->national_id_file)
                        <i class="fas fa-check-circle text-green-500"></i>
                        @else
                        <i class="fas fa-times-circle text-red-500"></i>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">KRA Pin Certificate</span>
                        @if($application->kra_pin_file)
                        <i class="fas fa-check-circle text-green-500"></i>
                        @else
                        <i class="fas fa-times-circle text-red-500"></i>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Title Document</span>
                        @if($application->title_document)
                        <i class="fas fa-check-circle text-green-500"></i>
                        @else
                        <i class="fas fa-times-circle text-red-500"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Application Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Approve Application</h3>
            <button onclick="closeApproveModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.water-applications.approve', $application) }}" id="approveForm">
            @csrf
            <div class="space-y-4">
                <!-- Connection Information -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-3">Connection Information</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Connection Date *</label>
                            <input type="date" name="connection_date" value="{{ date('Y-m-d') }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                </div>

                <!-- Meter Assignment Section -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-3">Meter Assignment (Optional)</h4>
                    
                    <!-- Meter Category Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Meter Category</label>
                        <select id="meterCategorySelect" name="meter_category_id" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a category (Optional)</option>
                            @if($categories && $categories->count() > 0)
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }} - {{ $category->code }}
                                </option>
                                @endforeach
                            @else
                                <option value="" disabled>No categories available</option>
                            @endif
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($categories && $categories->count() > 0)
                                Select a category to view available meters (shows first 5 available)
                            @else
                                No meter categories available. Please create categories first.
                            @endif
                        </p>
                    </div>

                    <!-- Available Meters -->
                    <div id="availableMetersSection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Available Meters</label>
                        <div id="availableMetersList" class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                            <!-- Meters will be loaded here via AJAX -->
                        </div>
                        <input type="hidden" id="selectedMeterId" name="meter_id">
                    </div>

                    <!-- Selected Meter Info -->
                    <div id="selectedMeterInfo" class="hidden mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <h5 class="font-medium text-blue-900 mb-2">Selected Meter</h5>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600">Meter Number:</span>
                                <span id="selectedMeterNumber" class="font-semibold text-blue-700"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Type:</span>
                                <span id="selectedMeterType" class="font-semibold"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Model:</span>
                                <span id="selectedMeterModel" class="font-semibold"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Initial Reading:</span>
                                <span id="selectedMeterReading" class="font-semibold"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Approval Notes</label>
                    <textarea name="notes" rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Any additional notes for this approval..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeApproveModal()" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <i class="fas fa-check mr-2"></i>
                    Approve Application
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Decline Application Modal -->
<div id="declineModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Decline Application</h3>
            <button onclick="closeDeclineModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.water-applications.decline', $application) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Decline *</label>
                    <textarea name="reason" rows="4" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Please provide the reason for declining this application..." required></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeDeclineModal()" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Decline Application
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal Functions
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    resetMeterSelection();
}

function showDeclineModal() {
    document.getElementById('declineModal').classList.remove('hidden');
}

function closeDeclineModal() {
    document.getElementById('declineModal').classList.add('hidden');
}

// Meter Assignment Logic
function resetMeterSelection() {
    const metersSection = document.getElementById('availableMetersSection');
    const meterInfo = document.getElementById('selectedMeterInfo');
    const meterIdInput = document.getElementById('selectedMeterId');
    
    if (metersSection) metersSection.classList.add('hidden');
    if (meterInfo) meterInfo.classList.add('hidden');
    if (meterIdInput) meterIdInput.value = '';
}

// Load available meters when category changes
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('meterCategorySelect');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            const metersSection = document.getElementById('availableMetersSection');
            const metersList = document.getElementById('availableMetersList');
            
            console.log('Category changed to:', categoryId);
            
            if (!categoryId) {
                resetMeterSelection();
                return;
            }
            
            metersList.innerHTML = '<p class="text-gray-500 text-sm">Loading available meters...</p>';
            metersSection.classList.remove('hidden');
            
            // Use the correct path - this should match your route
            const url = `{{ route('admin.customers.get-available-meters') }}?category_id=${categoryId}`;
            console.log('Fetching from URL:', url);
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(meters => {
                console.log('Meters received:', meters);
                
                if (!meters || meters.length === 0) {
                    metersList.innerHTML = '<p class="text-red-500 text-sm">No available meters found in this category</p>';
                    return;
                }
                
                if (meters.error) {
                    metersList.innerHTML = '<p class="text-red-500 text-sm">Error: ' + meters.error + '</p>';
                    return;
                }
                
                metersList.innerHTML = meters.map(meter => `
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer meter-option" 
                         data-meter-id="${meter.id}"
                         data-meter-number="${meter.meter_number}"
                         data-meter-type="${meter.meter_type}"
                         data-meter-model="${meter.meter_model}"
                         data-initial-reading="${meter.initial_reading}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-medium text-gray-900">${meter.meter_number}</div>
                                <div class="text-sm text-gray-500">${meter.meter_type} • ${meter.meter_model}</div>
                                <div class="text-xs text-gray-400">${meter.category_name}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-green-600">Available</div>
                                <div class="text-xs text-gray-500">Init: ${meter.initial_reading}m³</div>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                // Add click handlers to meter options
                document.querySelectorAll('.meter-option').forEach(option => {
                    option.addEventListener('click', function() {
                        // Remove previous selection
                        document.querySelectorAll('.meter-option').forEach(opt => {
                            opt.classList.remove('border-blue-500', 'bg-blue-50');
                        });
                        
                        // Add selection to clicked option
                        this.classList.add('border-blue-500', 'bg-blue-50');
                        
                        // Update selected meter info
                        const meterId = this.dataset.meterId;
                        const meterNumber = this.dataset.meterNumber;
                        const meterType = this.dataset.meterType;
                        const meterModel = this.dataset.meterModel;
                        const initialReading = this.dataset.initialReading;
                        
                        document.getElementById('selectedMeterId').value = meterId;
                        document.getElementById('selectedMeterNumber').textContent = meterNumber;
                        document.getElementById('selectedMeterType').textContent = meterType;
                        document.getElementById('selectedMeterModel').textContent = meterModel;
                        document.getElementById('selectedMeterReading').textContent = initialReading + ' m³';
                        
                        // Show selected meter info
                        document.getElementById('selectedMeterInfo').classList.remove('hidden');
                    });
                });
            })
            .catch(error => {
                console.error('Fetch error:', error);
                metersList.innerHTML = '<p class="text-red-500 text-sm">Error loading meters: ' + error.message + '</p>';
            });
        });
    }

    // Form validation for approve form
    const approveForm = document.getElementById('approveForm');
    if (approveForm) {
        approveForm.addEventListener('submit', function(e) {
            const categorySelected = document.getElementById('meterCategorySelect').value;
            const meterSelected = document.getElementById('selectedMeterId').value;
            
            if (categorySelected && !meterSelected) {
                e.preventDefault();
                alert('Please select a meter from the available options or clear the category selection.');
                return false;
            }
        });
    }
});

// Close modals when clicking outside
document.getElementById('approveModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});

document.getElementById('declineModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeclineModal();
    }
});
</script>

<style>
.meter-option {
    transition: all 0.2s ease;
}

.meter-option:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endcan
@endsection