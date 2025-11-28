<!-- Recording Search Modal -->
<div id="billModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl mx-auto my-8">
        <!-- Header -->
        <div class="flex justify-between items-center border-b px-6 py-4 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-semibold text-gray-800">Record Meter Reading & Generate Bill</h3>
            <button onclick="closeBillModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        <!-- Scrollable content -->
        <div class="max-h-[75vh] overflow-y-auto px-6 pb-6">
            <!-- Step 1: Customer Search -->
            <div id="searchStep" class="step active">
                <h4 class="text-md font-semibold text-gray-700 mb-4">Step 1: Find Customer</h4>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Customer</label>
                    <div class="relative">
                        <input type="text" 
                               id="modalCustomerSearch" 
                               placeholder="Start typing name, meter number, customer number, phone..."
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                               autocomplete="off">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Search by: Customer Name, Meter Number, Customer Number, Phone Number, ID Number
                    </p>
                </div>

                <!-- Live Search Results -->
                <div id="modalSearchResults" class="mt-4 space-y-2 hidden">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Matching Customers</h5>
                    <div id="modalResultsList" class="space-y-2 max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-2 bg-gray-50"></div>
                </div>

                <!-- No Results Message -->
                <div id="noResultsMessage" class="hidden mt-4 text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                    <i class="fas fa-search text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 font-medium">No customers found</p>
                    <p class="text-gray-400 text-sm mt-1">Try searching with different terms</p>
                </div>

                <!-- Search Tips -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h5 class="text-sm font-semibold text-blue-800 mb-2">Search Tips</h5>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <li>• Start typing to see instant results</li>
                        <li>• Search by customer name, meter number, or phone</li>
                        <li>• Results update as you type</li>
                        <li>• Click on a customer to select</li>
                    </ul>
                </div>
            </div>

            <!-- Step 2: Record Reading -->
            <div id="readingStep" class="step hidden">
                <h4 class="text-md font-semibold text-gray-700 mb-4">Step 2: Select Meter & Record Reading</h4>
                
                <!-- Customer Info Display -->
                <div id="customerInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <!-- Customer details will be populated here -->
                </div>

                <!-- Meter Selection -->
                <div id="meterSelection" class="mb-6 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Meter *</label>
                    <div id="meterList" class="space-y-3">
                        <!-- Meter options will be populated here -->
                    </div>
                    <div id="noMeterMessage" class="hidden text-center py-6 border-2 border-dashed border-gray-300 rounded-lg">
                        <i class="fas fa-tachometer-alt text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500 font-medium">No meters assigned</p>
                        <p class="text-gray-400 text-sm mt-1">This customer doesn't have any meters assigned yet.</p>
                    </div>
                </div>

                <form id="readingForm" action="{{ route('admin.meter-readings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="customer_id" id="selectedCustomerId">
                    <input type="hidden" name="meter_id" id="selectedMeterId">
                    <input type="hidden" name="redirect_to" value="bills_index">

                    <div id="readingFields" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Selected Meter Info -->
                            <div class="md:col-span-2">
                                <div id="selectedMeterInfo" class="bg-green-50 border border-green-200 rounded-lg p-4 hidden">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h5 class="font-semibold text-green-800 text-sm">Selected Meter</h5>
                                            <p class="text-green-700 text-sm" id="selectedMeterDetails"></p>
                                            <p class="text-green-600 text-xs mt-1" id="selectedMeterStats"></p>
                                        </div>
                                        <div class="text-right">
                                            <button type="button" onclick="showMeterSelection()" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                <i class="fas fa-edit mr-1"></i>Change Meter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Reading -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Reading (m³) *</label>
                                <input type="number" 
                                    name="current_reading" 
                                    id="current_reading"
                                    step="0.01" 
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="Enter current reading">
                                <div id="readingValidation" class="mt-1 text-sm hidden"></div>
                            </div>

                            <!-- Reading Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reading Date *</label>
                                <input type="date" 
                                    name="reading_date" 
                                    required
                                    max="{{ date('Y-m-d') }}"
                                    value="{{ date('Y-m-d') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <!-- Camera Capture Section -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meter Reading Photo (Optional)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-400 transition duration-200">
                                <!-- Your existing camera code remains the same -->
                                <!-- Camera Preview -->
                                <div id="cameraPreview" class="hidden mb-4">
                                    <div class="relative">
                                        <video id="video" width="100%" height="auto" autoplay class="rounded-lg shadow-md"></video>
                                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                            <i class="fas fa-camera mr-1"></i> Point camera at meter
                                        </div>
                                    </div>
                                    <canvas id="canvas" class="hidden"></canvas>
                                </div>
                                
                                <!-- Captured Image Preview -->
                                <div id="imagePreview" class="hidden mb-4">
                                    <div class="relative inline-block">
                                        <img id="preview" src="" alt="Captured meter" class="max-w-full h-auto rounded-lg mx-auto max-h-64 shadow-md">
                                        <div id="meterValidation" class="absolute top-2 right-2"></div>
                                    </div>
                                    <div id="ocrResult" class="mt-3"></div>
                                </div>

                                <!-- Action Buttons -->
                                <div id="cameraControls" class="space-y-2">
                                    <button type="button" 
                                            id="startCamera" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                        <i class="fas fa-camera mr-2"></i>
                                        Open Camera
                                    </button>
                                    
                                    <div id="captureControls" class="hidden space-y-2">
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-2">
                                            <p class="text-sm text-blue-700 text-left">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                <strong>Tips for better detection:</strong>
                                            </p>
                                            <ul class="text-xs text-blue-600 text-left mt-1 space-y-1">
                                                <li>• Ensure good lighting on the meter</li>
                                                <li>• Capture the entire meter display</li>
                                                <li>• Hold camera steady</li>
                                                <li>• Avoid glare and reflections</li>
                                            </ul>
                                        </div>
                                        
                                        <button type="button" 
                                                id="capture" 
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                            <i class="fas fa-camera-retro mr-2"></i>
                                            Capture Meter Photo
                                        </button>
                                        
                                        <button type="button" 
                                                id="retake" 
                                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                            <i class="fas fa-redo mr-2"></i>
                                            Retake Photo
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

                        <!-- Important Notice -->
                        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Automatic Billing</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Submitting this reading will automatically generate a bill for the selected meter based on the consumption.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center border-t px-6 py-4 sticky bottom-0 bg-white">
            <button type="button" onclick="previousStep()" id="prevBtn" class="hidden px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Search
            </button>
            <div class="flex space-x-2">
                <button type="button" onclick="closeBillModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">Cancel</button>
                <button type="button" onclick="nextStep()" id="nextBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    Next<i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="button" onclick="submitReading()" id="submitBtn" class="hidden px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-check mr-2"></i>Record Reading & Generate Bill
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables
let currentCustomerId = null;
let customerMeters = [];

// Function to load customer meters
function loadCustomerMeters(customerId) {
    fetch(`/admin/customers/${customerId}/meters`)
        .then(response => response.json())
        .then(meters => {
            customerMeters = meters;
            displayMeterSelection(meters);
        })
        .catch(error => {
            console.error('Error loading meters:', error);
            showNoMetersMessage();
        });
}

// Function to display meter selection
function displayMeterSelection(meters) {
    const meterList = document.getElementById('meterList');
    const noMeterMessage = document.getElementById('noMeterMessage');
    const meterSelection = document.getElementById('meterSelection');

    if (meters.length === 0) {
        meterSelection.classList.add('hidden');
        noMeterMessage.classList.remove('hidden');
        return;
    }

    meterSelection.classList.remove('hidden');
    noMeterMessage.classList.add('hidden');

    meterList.innerHTML = meters.map(meter => `
        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 cursor-pointer transition duration-200 meter-option" 
             data-meter-id="${meter.id}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="font-semibold text-gray-900">${meter.meter_number}</div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ${meter.status}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-tag text-gray-400"></i>
                            <span>${meter.meter_type}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-cube text-gray-400"></i>
                            <span>${meter.meter_model || 'Standard'}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">${meter.category_name}</div>
                    <div class="text-xs text-gray-500 mt-1">Current: ${meter.current_reading} m³</div>
                    <div class="text-xs text-blue-600 font-medium mt-1">Total: ${(meter.current_reading - meter.initial_reading).toFixed(2)} m³</div>
                </div>
            </div>
        </div>
    `).join('');

    // Add click handlers
    document.querySelectorAll('.meter-option').forEach(option => {
        option.addEventListener('click', function() {
            selectMeter(this.dataset.meterId);
        });
    });
}

// Function to select a meter
function selectMeter(meterId) {
    const meter = customerMeters.find(m => m.id == meterId);
    if (!meter) return;

    // Update hidden input
    document.getElementById('selectedMeterId').value = meterId;

    // Show selected meter info
    const selectedMeterInfo = document.getElementById('selectedMeterInfo');
    const selectedMeterDetails = document.getElementById('selectedMeterDetails');
    const selectedMeterStats = document.getElementById('selectedMeterStats');
    const readingFields = document.getElementById('readingFields');

    selectedMeterDetails.textContent = `${meter.meter_number} • ${meter.meter_type} • ${meter.category_name}`;
    selectedMeterStats.textContent = `Current: ${meter.current_reading} m³ | Total Consumption: ${(meter.current_reading - meter.initial_reading).toFixed(2)} m³`;
    
    selectedMeterInfo.classList.remove('hidden');
    readingFields.classList.remove('hidden');
    document.getElementById('meterSelection').classList.add('hidden');

    // Set current reading placeholder with previous reading
    document.getElementById('current_reading').placeholder = `Previous: ${meter.current_reading} m³`;
    
    // Validate reading input
    document.getElementById('current_reading').addEventListener('input', function() {
        validateReading(this.value, meter.current_reading);
    });
}

// Function to show meter selection again
function showMeterSelection() {
    document.getElementById('meterSelection').classList.remove('hidden');
    document.getElementById('selectedMeterInfo').classList.add('hidden');
    document.getElementById('readingFields').classList.add('hidden');
    document.getElementById('selectedMeterId').value = '';
}

// Function to validate reading
function validateReading(newReading, previousReading) {
    const validationDiv = document.getElementById('readingValidation');
    
    if (newReading < previousReading) {
        validationDiv.innerHTML = `<div class="text-red-600 flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Reading (${newReading}) is less than previous reading (${previousReading})
        </div>`;
        validationDiv.classList.remove('hidden');
        return false;
    } else {
        const consumption = (newReading - previousReading).toFixed(2);
        validationDiv.innerHTML = `<div class="text-green-600 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            Consumption: ${consumption} m³
        </div>`;
        validationDiv.classList.remove('hidden');
        return true;
    }
}

// Update the customer selection function
function selectCustomer(customerId, customerData) {
    currentCustomerId = customerId;
    document.getElementById('selectedCustomerId').value = customerId;
    
    // Update customer info display
    const customerInfo = document.getElementById('customerInfo');
    customerInfo.innerHTML = `
        <div class="flex justify-between items-start">
            <div>
                <h5 class="font-semibold text-blue-800">${customerData.first_name} ${customerData.last_name}</h5>
                <p class="text-blue-700 text-sm">Customer #: ${customerData.customer_number}</p>
                <p class="text-blue-600 text-xs">Phone: ${customerData.phone} | Address: ${customerData.physical_address}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    ${customerData.meters_count || 0} meter(s)
                </span>
            </div>
        </div>
    `;

    // Load meters for this customer
    loadCustomerMeters(customerId);
    
    // Move to next step
    nextStep();
}

// Update the nextStep function
function nextStep() {
    const searchStep = document.getElementById('searchStep');
    const readingStep = document.getElementById('readingStep');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    if (searchStep.classList.contains('active')) {
        // Moving from search to reading step
        if (!currentCustomerId) {
            alert('Please select a customer first');
            return;
        }
        
        searchStep.classList.remove('active');
        searchStep.classList.add('hidden');
        readingStep.classList.remove('hidden');
        readingStep.classList.add('active');
        
        prevBtn.classList.remove('hidden');
        nextBtn.classList.add('hidden');
        submitBtn.classList.remove('hidden');
    }
}

// Update the previousStep function
function previousStep() {
    const searchStep = document.getElementById('searchStep');
    const readingStep = document.getElementById('readingStep');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    readingStep.classList.remove('active');
    readingStep.classList.add('hidden');
    searchStep.classList.remove('hidden');
    searchStep.classList.add('active');
    
    prevBtn.classList.add('hidden');
    nextBtn.classList.remove('hidden');
    submitBtn.classList.add('hidden');
    
    // Reset meter selection
    showMeterSelection();
    document.getElementById('selectedMeterId').value = '';
    document.getElementById('readingFields').classList.add('hidden');
}

// Function to show no meters message
function showNoMetersMessage() {
    document.getElementById('meterSelection').classList.add('hidden');
    document.getElementById('noMeterMessage').classList.remove('hidden');
    document.getElementById('readingFields').classList.add('hidden');
}
</script>