@extends('layouts.app')

@section('title', 'Reports - NYAWASCO')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [];
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Reports & Analytics',
        'subtitle' => 'Generate comprehensive system reports',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="w-full px-0 py-8">
        <!-- Report Selection Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Generate Report</h2>

            <form id="reportForm" action="{{ route('reports.generate') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Report Type Selection -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Report Type *
                        </label>
                        <select id="report_type" name="report_type" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="">Select a report type</option>
                            <option value="revenue">Revenue Report</option>
                            <option value="customer">Customer Report</option>
                            <option value="meter">Meter Report</option>
                            <option value="consumption">Consumption Report</option>
                            <option value="collection">Collection Report</option>
                            <option value="arrears">Arrears Report</option>
                            <option value="statement">Customer Statement</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date Range (Optional)
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <input type="date" id="start_date" name="start_date"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-xs text-gray-500">Start Date</span>
                            </div>
                            <div>
                                <input type="date" id="end_date" name="end_date"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-xs text-gray-500">End Date</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Selection (for Statement only) -->
                <div id="customerSelection" class="mt-6 hidden">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Customer *
                    </label>
                    <select id="customer_id" name="customer_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Select a customer...</option>
                        <!-- Customers will be loaded dynamically -->
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Required for customer statement reports</p>
                </div>

                <!-- Export Format Section - Only Excel/CSV -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Export Format *
                    </label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="excel" required
                                class="text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Download Excel (.xlsx)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="format" value="csv"
                                class="text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Download CSV (.csv)</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Reports are only available for download in spreadsheet formats</p>
                </div>

                <!-- Detail Level - Default to Full Report -->
                <div class="mt-6">
                    <label for="detail_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Detail Level
                    </label>
                    <select id="detail_level" name="detail_level"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="summary">Summary Only</option>
                        <option value="detailed">Detailed Data</option>
                        <option value="full" selected>Full Report</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Full Report includes all data fields with comprehensive details</p>
                </div>

                <!-- Generate Button with Loading Animation -->
                <div class="mt-8 flex justify-end">
                    <button type="submit" id="generateBtn"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-300 flex items-center justify-center min-w-[160px] disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-blue-400">
                        <span id="btnText">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Generate Report
                        </span>
                        <span id="btnLoading" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Types Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Revenue Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-green-100">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-800">
                        Financial
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Revenue Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Detailed analysis of billing revenue, payments, and outstanding balances
                </p>
                <button onclick="selectReport('revenue')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Customer Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-blue-100">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                        Customer
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Customer Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Customer demographics, status analysis, and consumption patterns
                </p>
                <button onclick="selectReport('customer')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Meter Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-purple-100">
                        <i class="fas fa-tachometer-alt text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-purple-100 text-purple-800">
                        Assets
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Meter Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Meter inventory, status, location, and performance metrics
                </p>
                <button onclick="selectReport('meter')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Consumption Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-cyan-100">
                        <i class="fas fa-water text-cyan-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-cyan-100 text-cyan-800">
                        Usage
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Consumption Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Water consumption patterns, trends, and analysis by category
                </p>
                <button onclick="selectReport('consumption')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Collection Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-yellow-100">
                        <i class="fas fa-cash-register text-yellow-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                        Payments
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Collection Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Payment collection analysis by method, date, and collector
                </p>
                <button onclick="selectReport('collection')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Arrears Report Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-red-100 text-red-800">
                        Overdue
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Arrears Report</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Outstanding debt analysis, age analysis, and top debtors
                </p>
                <button onclick="selectReport('arrears')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>

            <!-- Customer Statement Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-indigo-100">
                        <i class="fas fa-file-invoice text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-indigo-100 text-indigo-800">
                        Account
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Customer Statement</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Comprehensive customer account statement with billing and payment history
                </p>
                <button onclick="selectReport('statement')"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Select Report →
                </button>
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Downloads</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('reports.generate', ['report_type' => 'revenue', 'format' => 'excel', 'detail_level' => 'full']) }}"
                   class="bg-green-50 hover:bg-green-100 p-4 rounded-lg border border-green-200 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-green-800">Revenue Excel</p>
                            <p class="text-xs text-green-600">Full Report</p>
                        </div>
                        <i class="fas fa-file-excel text-green-600"></i>
                    </div>
                </a>

                <a href="{{ route('reports.generate', ['report_type' => 'arrears', 'format' => 'excel', 'detail_level' => 'full']) }}"
                   class="bg-red-50 hover:bg-red-100 p-4 rounded-lg border border-red-200 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-red-800">Arrears Excel</p>
                            <p class="text-xs text-red-600">Full Report</p>
                        </div>
                        <i class="fas fa-file-excel text-red-600"></i>
                    </div>
                </a>

                <a href="{{ route('reports.generate', ['report_type' => 'collection', 'format' => 'csv', 'detail_level' => 'full']) }}"
                   class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg border border-blue-200 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-blue-800">Collection CSV</p>
                            <p class="text-xs text-blue-600">Full Report</p>
                        </div>
                        <i class="fas fa-file-csv text-blue-600"></i>
                    </div>
                </a>

                <a href="{{ route('reports.generate', ['report_type' => 'customer', 'format' => 'excel', 'detail_level' => 'full']) }}"
                   class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg border border-purple-200 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-purple-800">Customer Excel</p>
                            <p class="text-xs text-purple-600">Full Report</p>
                        </div>
                        <i class="fas fa-file-excel text-purple-600"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function selectReport(type) {
    const reportTypeSelect = document.getElementById('report_type');
    reportTypeSelect.value = type;
    reportTypeSelect.scrollIntoView({ behavior: 'smooth' });
    reportTypeSelect.focus();

    // Trigger change event to show/hide customer selection
    const event = new Event('change');
    reportTypeSelect.dispatchEvent(event);
}

// Set default dates and format
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const firstDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 2)
        .toISOString().split('T')[0];

    document.getElementById('start_date').value = firstDayOfMonth;
    document.getElementById('end_date').value = today;

    // Set Excel as default format
    const excelRadio = document.querySelector('input[value="excel"]');
    if (excelRadio) {
        excelRadio.checked = true;
    }

    // Set default detail level to Full Report
    const detailLevel = document.getElementById('detail_level');
    if (detailLevel) {
        detailLevel.value = 'full';
    }

    // Load customers for statement report
    loadCustomers();

    // Show/hide customer selection based on report type
    const reportTypeSelect = document.getElementById('report_type');
    const customerSelectionDiv = document.getElementById('customerSelection');
    const customerIdSelect = document.getElementById('customer_id');

    reportTypeSelect.addEventListener('change', function() {
        if (this.value === 'statement') {
            customerSelectionDiv.classList.remove('hidden');
            customerIdSelect.setAttribute('required', 'required');
        } else {
            customerSelectionDiv.classList.add('hidden');
            customerIdSelect.removeAttribute('required');
            customerIdSelect.value = '';
        }
    });
});

// Load customers dynamically
async function loadCustomers() {
    try {
        const response = await fetch('/api/customers/active');
        if (!response.ok) {
            throw new Error('Failed to load customers');
        }

        const customers = await response.json();
        const customerSelect = document.getElementById('customer_id');

        // Clear existing options except the first one
        customerSelect.innerHTML = '<option value="">Select a customer...</option>';

        // Add customer options
        customers.forEach(customer => {
            const option = document.createElement('option');
            option.value = customer.id;
            option.textContent = `${customer.customer_number} - ${customer.first_name} ${customer.last_name}`;
            customerSelect.appendChild(option);
        });

        // Enable select
        customerSelect.disabled = false;
    } catch (error) {
        console.error('Error loading customers:', error);
        const customerSelect = document.getElementById('customer_id');
        customerSelect.innerHTML = '<option value="">Error loading customers</option>';
        customerSelect.disabled = true;
    }
}

// Form submission handler with loading animation
document.getElementById('reportForm').addEventListener('submit', function(e) {
    const generateBtn = document.getElementById('generateBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');

    // Validate format is selected
    const formatSelected = document.querySelector('input[name="format"]:checked');
    if (!formatSelected) {
        e.preventDefault();
        alert('Please select an export format (Excel or CSV)');
        return;
    }

    // Validate customer selection for statement report
    const reportType = document.getElementById('report_type').value;
    if (reportType === 'statement') {
        const customerId = document.getElementById('customer_id').value;
        if (!customerId) {
            e.preventDefault();
            alert('Please select a customer for the statement report');
            return;
        }
    }

    // Show loading animation
    generateBtn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');

    // The form will now submit normally, animation will stop when page reloads
});

// Optional: Add keyboard shortcut for generating report
document.addEventListener('keydown', function(e) {
    // Ctrl + G or Cmd + G to generate report
    if ((e.ctrlKey || e.metaKey) && e.key === 'g') {
        e.preventDefault();
        document.getElementById('reportForm').submit();
    }
});

// Add search functionality to customer select (if needed)
function initCustomerSearch() {
    const customerSelect = document.getElementById('customer_id');
    if (customerSelect) {
        // Create a search input
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Search customers...';
        searchInput.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 mb-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = customerSelect.options;

            for (let i = 1; i < options.length; i++) { // Skip first option
                const option = options[i];
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });

        // Insert search input before the select
        customerSelect.parentNode.insertBefore(searchInput, customerSelect);
    }
}

// Initialize customer search when document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize after a short delay to ensure select is populated
    setTimeout(initCustomerSearch, 1000);
});
</script>

<style>
/* Smooth transition for button states */
#generateBtn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Spin animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Optional: Add subtle pulse animation to button before click */
@keyframes subtle-pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0);
    }
}

#generateBtn:not(:disabled):hover {
    animation: subtle-pulse 2s infinite;
}

/* Style for customer statement card */
.hover\:shadow-md:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.transition-shadow {
    transition: box-shadow 0.3s ease;
}

/* Loading state for customer select */
select:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid-cols-1 {
        grid-template-columns: 1fr;
    }

    .flex.space-x-4 {
        flex-direction: column;
        gap: 0.5rem;
    }

    .flex.space-x-4 label {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection
