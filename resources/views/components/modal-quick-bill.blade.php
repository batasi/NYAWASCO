<!-- Quick Bill Generation Modal -->
<div id="quickBillModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-4 sm:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bolt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-white" id="modal-title">Quick Bill Generation</h3>
                            <p class="text-green-100 text-xs sm:text-sm">Search meter to record reading and generate bill</p>
                        </div>
                    </div>
                    <button type="button"
                            onclick="closeQuickBillModal()"
                            class="text-white hover:text-green-200 transition-colors">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-4 sm:px-6 py-6 sm:py-8">
                <!-- Meter Search Section -->
                <div class="mb-6 sm:mb-8">
                    <div class="text-center mb-6">
                        <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Search Meter by Number</h4>
                    </div>

                    <!-- Search Form -->
                    <form id="meterSearchForm" class="w-full">
                        @csrf
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input
                                type="text"
                                id="meter_number"
                                name="meter_number"
                                placeholder="Enter meter number"
                                class="w-full pl-10 pr-24 py-3 text-base border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300"
                                autocomplete="off"
                                autofocus
                                required>
                            <div class="absolute inset-y-0 right-0 pr-1 flex items-center">
                                <button type="submit"
                                        id="searchButton"
                                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-3 py-2 rounded-lg transition-all duration-200 flex items-center shadow-md hover:shadow-lg text-sm">
                                    <i class="fas fa-search mr-2"></i>
                                    Search
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">
                            Press <kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Enter</kbd> to search
                        </p>
                    </form>
                </div>

                <!-- Search Results -->
                <div id="searchResults" class="hidden">
                    <!-- Loading State -->
                    <div id="loading" class="hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 sm:p-6">
                            <div class="flex items-center justify-center space-x-4">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Searching for meter...</p>
                                    <p class="text-xs text-gray-500">Please wait while we fetch meter details</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div id="error" class="hidden">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 sm:p-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-exclamation-circle text-red-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-red-800" id="errorTitle"></h4>
                                    <p class="text-sm text-red-600 mt-1" id="errorMessage"></p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button onclick="retrySearch()"
                                        class="text-sm bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center w-full sm:w-auto justify-center">
                                    <i class="fas fa-redo mr-2"></i>
                                    Try Again
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Success Results -->
                    <div id="success" class="hidden">
                        <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-lg p-4 sm:p-6">
                            <!-- Meter Header -->
                            <div class="flex items-center justify-between mb-4 sm:mb-6">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-tachometer-alt text-green-600"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-base sm:text-lg font-bold text-gray-900" id="meterNumberDisplay"></h4>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                                <span id="meterStatus"></span>
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-tag text-[8px] mr-1"></i>
                                                <span id="meterCategory"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer & Meter Info -->
                            <div class="space-y-4 mb-4 sm:mb-6">
                                <!-- Customer Info -->
                                <div class="space-y-3">
                                    <h5 class="text-sm font-semibold text-gray-700 flex items-center">
                                        <i class="fas fa-user text-blue-500 mr-2"></i>
                                        Customer Information
                                    </h5>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <div class="text-xs text-gray-500">Customer Name</div>
                                            <div class="text-sm font-medium text-gray-900 truncate" id="customerName"></div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500">Acc Number</div>
                                            <div class="text-sm font-mono text-gray-900" id="customerNumber"></div>
                                        </div>
                                        <div class="col-span-2">
                                            <div class="text-xs text-gray-500">Contact</div>
                                            <div class="text-sm font-medium text-gray-900" id="customerPhone"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Meter Details -->
                                <div class="space-y-3">
                                    <h5 class="text-sm font-semibold text-gray-700 flex items-center">
                                        <i class="fas fa-info-circle text-green-500 mr-2"></i>
                                        Meter Details
                                    </h5>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <div class="text-xs text-gray-500">Type</div>
                                            <div class="text-sm font-medium text-gray-900" id="meterType"></div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500">Zone</div>
                                            <div class="text-sm font-medium text-gray-900" id="zoneName"></div>
                                        </div>
                                    </div>
                                    <div class="pt-3 border-t border-gray-200">
                                        <div class="text-xs text-gray-500">Last Reading</div>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-1">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900" id="lastReadingValue"></div>
                                                <div class="text-xs text-gray-500" id="lastReadingDateDetail"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200 space-y-3 sm:space-y-0">
                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                                    <button onclick="closeQuickBillModal()"
                                            class="w-full sm:w-auto text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                                        Cancel
                                    </button>
                                    <a href="#"
                                       id="proceedButton"
                                       class="w-full sm:w-auto text-sm bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                                        <i class="fas fa-arrow-right mr-2"></i>
                                        Proceed to Record Reading
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div id="quickTips" class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3 sm:mb-4">Quick Tips</h5>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="flex items-start space-x-2">
                            <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-search text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Fast Search</p>
                                <p class="text-xs text-gray-500">Find meters instantly</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-2">
                            <div class="w-6 h-6 bg-green-100 rounded flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bolt text-green-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Auto Bill</p>
                                <p class="text-xs text-gray-500">Bill generated automatically</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-2">
                            <div class="w-6 h-6 bg-purple-100 rounded flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-camera text-purple-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Photo Capture</p>
                                <p class="text-xs text-gray-500">Capture meter reading</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// DOM Elements
const quickBillModal = document.getElementById('quickBillModal');
const meterSearchForm = document.getElementById('meterSearchForm');
const meterNumberInput = document.getElementById('meter_number');
const searchButton = document.getElementById('searchButton');
const searchResults = document.getElementById('searchResults');
const loading = document.getElementById('loading');
const error = document.getElementById('error');
const success = document.getElementById('success');
const errorTitle = document.getElementById('errorTitle');
const errorMessage = document.getElementById('errorMessage');
const proceedButton = document.getElementById('proceedButton');
const quickTips = document.getElementById('quickTips');

// Open modal
function openQuickBillModal() {
    quickBillModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => meterNumberInput.focus(), 100);
}

// Close modal
function closeQuickBillModal() {
    quickBillModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    resetSearch();
}

// Search meter function
async function searchMeter(meterNumber) {
    loading.classList.remove('hidden');
    error.classList.add('hidden');
    success.classList.add('hidden');
    quickTips.classList.add('hidden');
    searchResults.classList.remove('hidden');

    try {
        const response = await fetch('{{ route("bills.quick.find") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ meter_number: meterNumber })
        });

        const data = await response.json();
        loading.classList.add('hidden');

        if (data.success) {
            displayMeterDetails(data.meter);
        } else {
            showError('Meter Not Found', data.message);
        }
    } catch (err) {
        loading.classList.add('hidden');
        showError('Network Error', 'Unable to connect to server. Please check your connection.');
    }
}

// Display meter details
function displayMeterDetails(meter) {
    document.getElementById('meterNumberDisplay').textContent = meter.meter_number;
    document.getElementById('meterStatus').textContent = meter.status || 'Active';
    document.getElementById('meterCategory').textContent = meter.category?.name || 'No Category';

    if (meter.last_reading && meter.last_reading.reading !== null) {
        document.getElementById('lastReadingValue').textContent =
            parseFloat(meter.last_reading.reading).toFixed(2) + ' m³';
        document.getElementById('lastReadingDateDetail').textContent =
            meter.last_reading.date || 'No date available';
    } else {
        document.getElementById('lastReadingValue').textContent =
            parseFloat(meter.current_reading || 0).toFixed(2) + ' m³ (Initial)';
        document.getElementById('lastReadingDateDetail').textContent =
            'No previous reading';
    }

    if (meter.customer) {
        document.getElementById('customerName').textContent = meter.customer.name || 'N/A';
        document.getElementById('customerNumber').textContent = meter.customer.customer_number || '—';
        document.getElementById('customerPhone').textContent = meter.customer.phone || 'N/A';
    } else {
        document.getElementById('customerName').textContent = 'No customer assigned';
        document.getElementById('customerNumber').textContent = '—';
        document.getElementById('customerPhone').textContent = '—';
    }

    document.getElementById('meterType').textContent = meter.meter_type || 'N/A';
    document.getElementById('zoneName').textContent = meter.zone || 'N/A';

    if (meter.id) {
        proceedButton.href = `/bills/quick/create/${meter.id}`;
    }

    success.classList.remove('hidden');
}

// Show error state
function showError(title, message) {
    errorTitle.textContent = title;
    errorMessage.textContent = message;
    error.classList.remove('hidden');
    success.classList.add('hidden');
}

// Reset search
function resetSearch() {
    searchResults.classList.add('hidden');
    quickTips.classList.remove('hidden');
    meterNumberInput.value = '';
    meterNumberInput.focus();
}

// Retry search
function retrySearch() {
    const meterNumber = meterNumberInput.value.trim();
    if (meterNumber) {
        searchMeter(meterNumber);
    }
}

// Form submission
meterSearchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const meterNumber = meterNumberInput.value.trim();
    if (!meterNumber) {
        return;
    }
    searchMeter(meterNumber);
});

// Input validation
meterNumberInput.addEventListener('input', function() {
    const value = this.value.trim();
    searchButton.disabled = !value;
});

// Close modal on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !quickBillModal.classList.contains('hidden')) {
        closeQuickBillModal();
    }
});

// Close modal when clicking outside
quickBillModal.addEventListener('click', function(e) {
    if (e.target === quickBillModal) {
        closeQuickBillModal();
    }
});
</script>
