<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Api\LiveActivityController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\AttendeeController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\VotingCategoryController;
use App\Http\Controllers\PesapalController;
use App\Http\Controllers\NomineeCategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MpesaPaymentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MeterController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\QuickBillController;
use App\Http\Controllers\Admin\MeterReadingController;
use App\Http\Controllers\Admin\WaterConnectionController;
use App\Http\Controllers\Admin\MeterCategoryController;
use App\Http\Controllers\Admin\SystemManagementController;
use App\Http\Controllers\Admin\PaymentAllocationController;
use App\Http\Controllers\Admin\AccountsReceivableController;
use App\Http\Controllers\Admin\PaymentDashboardController;
use App\Models\Customer;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [HomeController::class, 'index'])->name('login');

// Static Pages
Route::view('/about', 'about.index')->name('about');
Route::view('/about/management', 'about.management')->name('management');
Route::view('/projects', 'projects.index')->name('projects');
Route::view('/pricing', 'static.pricing', ['title' => 'Pricing - NYAWASCO'])->name('pricing');
Route::view('/contact', 'static.contact', ['title' => 'Contact Us - NYAWASCO'])->name('contact');
Route::view('/privacy', 'static.privacy', ['title' => 'Privacy Policy - NYAWASCO'])->name('privacy');
Route::view('/terms', 'static.terms', ['title' => 'Terms of Service - NYAWASCO'])->name('terms');
Route::view('/help', 'static.help', ['title' => 'Help Center - NYAWASCO'])->name('help');
Route::get('/board-of-directors', function () {
    return view('board');
})->name('board-of-directors');
// routes/web.php
Route::get('/publications', function () {
    return view('publications');
})->name('publications');

// Route for single publication
Route::get('/publications/{slug}', function ($slug) {
    // For now, we'll use a simple approach
    // In a real application, you'd fetch the publication from database
    return view('publication-single', [
        'publication' => [
            'title' => 'Nyamira County Unveils NYAWASCO Board, Paves Way for Universal Water Access',
            'date' => '8th January, 2026',
            'type' => 'Press Release',
            'content' => '...', // Your full content here
            'slug' => $slug
        ]
    ]);
})->name('publication.show');
// Service Routes
Route::view('/services', 'services.index')->name('services');
Route::view('/services/water-supply', 'services.water-supply')->name('water-supply');
Route::view('/services/sewerage', 'services.sewerage')->name('sewerage');
Route::view('/services/new-connections', 'services.new-connections')->name('new-connections');
Route::view('/services/payments', 'services.payments')->name('payments');
Route::view('/services/water-connection', 'services.water-connection')->name('water-connection');
Route::view('/services/sewer-connection', 'services.sewer-connection')->name('sewer-connection');
Route::view('/services/bill-payment', 'services.bill-payment')->name('bill-payment');

// Information Routes
Route::view('/support', 'support')->name('support');
Route::view('/complaints/create', 'complaints.create')->name('complaints.create');
Route::view('/report-corruption', 'report-corruption')->name('corruption-report');
Route::view('/tenders', 'tenders.index')->name('tenders');
Route::view('/careers', 'careers.index')->name('careers');
Route::view('/reports', 'reports.index')->name('reports');
Route::view('/downloads', 'downloads.index')->name('downloads');
Route::view('/documentary', 'documentary')->name('documentary');

// News Routes
Route::get('/news', function () {
    return view('news.index');
})->name('news');
Route::get('/news/{slug}', function ($slug) {
    return view('news.show', compact('slug'));
})->name('news.show');

// Contact Form
Route::post('/contact/submit', function (Request $request) {
    return back()->with('success', 'Your message has been sent successfully!');
})->name('contact.submit');

// Water Connection Application
Route::get('/water-connection/apply', [WaterConnectionController::class, 'create'])->name('water-connection.apply');
Route::post('/water-connection/submit', [WaterConnectionController::class, 'store'])->name('water-connection.submit');

// Events & Voting (Public)
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/category/{category}', [EventController::class, 'byCategory'])->name('events.byCategory');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/tickets', [TicketController::class, 'show'])->name('tickets.purchase.show');

Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
Route::get('/voting/{contest}', [VotingController::class, 'show'])->name('voting.show');
Route::get('/voting/category/{category}', [VotingController::class, 'byCategory'])->name('voting.byCategory');

// Organizers
Route::get('/organizers', [OrganizerController::class, 'index'])->name('organizers.index');
Route::get('/organizers/{organizer}', [OrganizerController::class, 'show'])->name('organizers.show');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES
|--------------------------------------------------------------------------
*/


Route::prefix('api')->group(function () {
    Route::get('/live-activities', [LiveActivityController::class, 'index'])->name('api.live-activities');
    Route::get('/search', [SearchApiController::class, 'search'])->name('api.search');
    Route::get('/events/featured', [EventController::class, 'featured'])->name('api.events.featured');
    Route::get('/voting/featured', [VotingController::class, 'featured'])->name('api.voting.featured');
    Route::get('/customers/search', [App\Http\Controllers\Api\CustomerSearchController::class, 'search'])->name('api.customers.search');
    Route::get('/bills/search', [BillController::class, 'search'])->name('api.bills.search');
});
// routes/web.php
Route::get('/admin/customers/search', [CustomerController::class, 'searchCustomers'])
    ->name('admin.customers.search');
Route::get('/admin/customers/{customer}/details', [CustomerController::class, 'getCustomerDetails'])
    ->name('admin.customers.details');

// Payment Callbacks (No Auth Required)
Route::post('/pesapal/stkpush', [PesapalController::class, 'stkPush'])->name('pesapal.stkpush');
Route::post('/pesapal/callback', [PesapalController::class, 'callback'])->name('pesapal.callback');
Route::get('/pesapal/register-ipn', [PesapalController::class, 'registerIpn']);
Route::get('/pesapal/callback', [PesapalController::class, 'callbackReturn'])->name('pesapal.callback.return');
Route::post('/pesapal/ipn', [PesapalController::class, 'ipn'])->name('pesapal.ipn');
Route::post('/payments/callback', [MpesaPaymentController::class, 'handleCallback'])->name('mpesa.callback');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Email Verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->intended('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/admin/meters/{meter}/json', [App\Http\Controllers\Admin\MeterController::class, 'getJson'])
    ->name('admin.meters.json')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('admin/customers/{customer}/statement', [CustomerController::class, 'statement'])->name('admin.customers.statement');
    Route::get('admin/customers/{customer}/statement/pdf', [CustomerController::class, 'statementPdf'])->name('admin.customers.statement.pdf');

      Route::prefix('profile')->name('profile.')->group(function () {
        // Main profile page
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit'); // Alternative URL

        // Update profile info
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');

        // Password management
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        // Preferences
        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::patch('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');

        // Activity
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');

        // Account deletion
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Payments
    Route::get('/payments/search', [PaymentController::class, 'search'])->name('payments.search');
    Route::post('/payments/import', [PaymentController::class, 'import'])
        ->name('payments.import');

    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    });
        // Meter details route for AJAX
    Route::get('/payments/meter-details/{meterNumber}', [PaymentController::class, 'getMeterDetails'])
        ->name('payments.meter-details')
        ->middleware('auth');

    Route::get('/payments/search-meters', [PaymentController::class, 'searchMeters'])
        ->name('payments.search-meters')
        ->middleware('auth');



    // Payment Processing
    Route::get('/payment/{type}/{id}', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/payment/{type}/{id}/complete', [PaymentController::class, 'complete'])->name('payment.complete');

    // M-PESA Payments
    Route::post('/pay/mpesa/initiate', [MpesaPaymentController::class, 'initiatePayment'])->name('mpesa.initiate');
    Route::post('/pay/mpesa/check-status', [MpesaPaymentController::class, 'checkPaymentStatus'])->name('mpesa.checkStatus');

    // Bills
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::post('/bills', [BillController::class, 'store'])->name('bills.store');
    Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.edit');
    Route::delete('/bills/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');
    Route::get('/bills/search', [BillController::class, 'search'])->name('bills.search');

    Route::get('/bills/quick', [QuickBillController::class, 'selectMeter'])->name('bills.quick');
    Route::post('/bills/quick/find-meter', [QuickBillController::class, 'findMeter'])->name('bills.quick.find');
    Route::get('/bills/quick/create/{meter}', [QuickBillController::class, 'createReading'])->name('bills.quick.create');
    Route::get('bills/{bill}/print', [BillController::class, 'printReceipt'])
    ->name('bills.print');
    Route::get('/bills/{bill}/receipt', [BillController::class, 'generateReceipt'])->name('bills.receipt');

      // Customer Management
    Route::prefix('customers')->name('customers.')->group(function () {
        // AJAX endpoints (should come BEFORE dynamic routes)
        Route::get('/get-available-meters', [CustomerController::class, 'getAvailableMeters'])->name('get-available-meters');
        Route::get('/check-meter-availability', [CustomerController::class, 'checkMeterAvailability'])->name('check-meter-availability');
        Route::get('/meter-category/{id}/details', [CustomerController::class, 'getMeterCategoryDetails'])->name('meter-category.details');
        Route::get('/export-pdf', [CustomerController::class, 'exportPDF'])->name('export-pdf');

        // Customer CRUD
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');

        // Customer-specific routes (these come AFTER the AJAX routes)
        Route::prefix('{customer}')->group(function () {
            // Customer details
            Route::get('/', [CustomerController::class, 'show'])->name('show');
            Route::get('/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/', [CustomerController::class, 'update'])->name('update');
            Route::delete('/', [CustomerController::class, 'destroy'])->name('destroy');

            // Status Management
            Route::patch('/update-status', [CustomerController::class, 'updateStatus'])->name('update-status');

            // Meter Assignment
            Route::post('/assign-meter', [CustomerController::class, 'assignMeter'])->name('assign-meter');
            Route::post('/unassign-meter/{meter}', [CustomerController::class, 'unassignMeter'])->name('unassign-meter');
            Route::post('/unassign-all-meters', [CustomerController::class, 'unassignAllMeters'])->name('unassign-all-meters');

            // Document Upload
            Route::post('/upload-documents', [CustomerController::class, 'uploadDocuments'])->name('upload-documents');

            // Meter Readings
            Route::get('/readings', [CustomerController::class, 'meterReadings'])->name('meter-readings');
            Route::get('/readings/create', [CustomerController::class, 'createReading'])->name('readings.create');
            Route::post('/readings', [CustomerController::class, 'storeReading'])->name('readings.store');

            // Billing
            Route::post('/readings/{reading}/generate-bill', [CustomerController::class, 'generateBill'])->name('generate-bill');
            Route::get('/bills', [CustomerController::class, 'bills'])->name('bills');

            // AJAX endpoints for specific customer
            Route::get('/get-address', [CustomerController::class, 'getCustomerAddress'])->name('get-address');
            Route::get('/meters', [CustomerController::class, 'getCustomerMeters'])->name('get-customer-meters');
        });
    });
    // Meter Management

    Route::get('/admin/meters/next-meter-number', [MeterController::class, 'getNextMeterNumber'])
        ->name('admin.meters.next-meter-number');
    Route::prefix('meters')->name('meters.')->group(function () {

        Route::get('/', [MeterController::class, 'index'])->name('index');
        Route::post('/', [MeterController::class, 'store'])->name('store');
        Route::get('/available', [MeterController::class, 'availableMeters'])->name('available');
        Route::get('/assigned', [MeterController::class, 'assignedMeters'])->name('assigned');
        Route::get('/by-location', [MeterController::class, 'metersByLocation'])->name('by-location');
        Route::get('/available-json', [MeterController::class, 'getAvailableMeters'])->name('available.json');
        Route::get('/{meter}', [MeterController::class, 'show'])->name('show');
        Route::get('/{meter}/edit', [MeterController::class, 'edit'])->name('edit');
        Route::put('/{meter}', [MeterController::class, 'update'])->name('update');
        Route::post('/{meter}/assign', [MeterController::class, 'assignToCustomer'])->name('assign');
        Route::post('/{meter}/unassign', [MeterController::class, 'unassign'])->name('unassign');
        Route::get('/search', [MeterController::class, 'search'])->name('search');
    });



    // Meter Readings Management
    Route::prefix('meter-readings')->name('meter-readings.')->group(function () {
        Route::get('/', [MeterReadingController::class, 'index'])->name('index');
        Route::get('/create', [MeterReadingController::class, 'create'])->name('create');
        Route::post('/', [MeterReadingController::class, 'store'])->name('store');
        Route::get('/last-reading', [MeterReadingController::class, 'getLastReading'])->name('last-reading');

    });

    // Meter reading estimation
    Route::get('/customers/{customer}/meters/{meter}/estimate-consumption',
        [MeterReadingController::class, 'estimateConsption'])
        ->name('admin.meter-readings.estimate');
    Route::get('/meter-readings/exceptions', [MeterReadingController::class, 'exceptions'])->name('meter-readings.exceptions');
    Route::get('/meter-readings/{meter_reading}/edit', [MeterReadingController::class, 'edit'])
        ->name('admin.meter-readings.edit');
    Route::put('/meter-readings/{meter_reading}', [MeterReadingController::class, 'update'])
    ->name('admin.meter-readings.update');

    Route::delete('/meter-readings/{meter_reading}', [MeterReadingController::class, 'destroy'])
        ->name('admin.meter-readings.destroy');
    // Meter Categories Management
    Route::prefix('meter-categories')->name('meter-categories.')->group(function () {
        Route::get('/', [MeterCategoryController::class, 'index'])->name('index');
        Route::get('/create', [MeterCategoryController::class, 'create'])->name('create');
        Route::post('/', [MeterCategoryController::class, 'store'])->name('store');
        Route::get('/{meterCategory}', [MeterCategoryController::class, 'show'])->name('show');
        Route::get('/{meterCategory}/edit', [MeterCategoryController::class, 'edit'])->name('edit');
        Route::put('/{meterCategory}', [MeterCategoryController::class, 'update'])->name('update');
        Route::delete('/{meterCategory}', [MeterCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{meterCategory}/calculate', [MeterCategoryController::class, 'calculateCharge'])->name('calculate');

        // Pricing Tiers
        Route::post('/{meterCategory}/tiers', [MeterCategoryController::class, 'storeTier'])->name('tiers.store');
        Route::put('/{meterCategory}/tiers/{pricingTier}', [MeterCategoryController::class, 'updateTier'])->name('tiers.update');
        Route::delete('/{meterCategory}/tiers/{pricingTier}', [MeterCategoryController::class, 'destroyTier'])->name('tiers.destroy');
    });

    // Water Applications
    Route::prefix('water-applications')->name('water-applications.')->group(function () {
        Route::get('/', [WaterConnectionController::class, 'index'])->name('index');
        Route::get('/{application}', [WaterConnectionController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [WaterConnectionController::class, 'approve'])->name('approve');
        Route::post('/{application}/decline', [WaterConnectionController::class, 'decline'])->name('decline');
    });

    // System Administration Routes
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/management', [SystemManagementController::class, 'index'])->name('management');
        Route::post('/management/clear-cache', [SystemManagementController::class, 'clearCache'])->name('management.clear-cache');
        Route::post('/management/clear-app-cache', [SystemManagementController::class, 'clearApplicationCache'])->name('management.clear-app-cache');
        Route::post('/management/clear-route-cache', [SystemManagementController::class, 'clearRouteCache'])->name('management.clear-route-cache');
        Route::post('/management/clear-config-cache', [SystemManagementController::class, 'clearConfigCache'])->name('management.clear-config-cache');
        Route::post('/management/clear-view-cache', [SystemManagementController::class, 'clearViewCache'])->name('management.clear-view-cache');
        Route::post('/management/optimize-db', [SystemManagementController::class, 'optimizeDatabase'])->name('management.optimize-db');
        Route::post('/management/restart-services', [SystemManagementController::class, 'restartServices'])->name('management.restart-services');
        Route::post('/management/save-config', [SystemManagementController::class, 'saveConfiguration'])->name('management.save-config');

        Route::get('/user-management', function () {
            return view('user-management');
        })->name('user.management');

        Route::get('/sessions-logs', function () {
            return view('sessions-logs');
        })->name('sessions.logs');

        Route::get('/analysis', function () {
            return view('analysis');
        })->name('analysis');

        Route::get('/backups', function () {
            return view('system.backups');
        })->name('backups');
    });

    Route::post('/admin/accounts-receivable/search-customer',
        [AccountsReceivableController::class, 'searchCustomer'])
        ->name('admin.accounts-receivable.search-customer');
        // Collections Tracking Routes
        Route::prefix('collections')->name('collections.')->group(function () {
            Route::get('/tracking', [AccountsReceivableController::class, 'collectionsTracking'])->name('tracking');
            Route::post('/activities', [AccountsReceivableController::class, 'storeCollectionActivity'])->name('activities.store');
            Route::get('/activities/create', [AccountsReceivableController::class, 'createCollectionActivity'])->name('activities.create');
            Route::get('/customers/search', [AccountsReceivableController::class, 'searchCustomer'])->name('customers.search');
        });
        // Aging Report Export
        Route::get('/accounts-receivable/aging-report/export', [AccountsReceivableController::class, 'exportAgingReport'])->name('admin.accounts-receivable.aging-report.export');
        Route::prefix('admin/accounts-receivable')->name('admin.accounts-receivable.')->group(function () {
        // Existing routes...
        Route::get('/dashboard', [AccountsReceivableController::class, 'dashboard'])->name('dashboard');

        Route::get('/aging-report', [AccountsReceivableController::class, 'agingReport'])->name('aging-report');
        Route::get('/collections-tracking', [AccountsReceivableController::class, 'collectionsTracking'])->name('collections-tracking');
        Route::get('/write-offs', [AccountsReceivableController::class, 'writeOffs'])->name('write-offs.index');
        Route::get('/write-offs/create/{customer}', [AccountsReceivableController::class, 'createWriteOff'])->name('write-offs.create');
        Route::post('/write-offs', [AccountsReceivableController::class, 'storeWriteOff'])->name('write-offs.store');
        Route::post('/write-offs/{writeOff}/approve', [AccountsReceivableController::class, 'approveWriteOff'])->name('write-offs.approve');

        // Add these new routes
        Route::post('/collection-activities', [AccountsReceivableController::class, 'storeCollectionActivity'])->name('collection-activities.store');
        Route::get('/refresh-dashboard', [AccountsReceivableController::class, 'refreshDashboard'])->name('refresh-dashboard');
        Route::post('/quick-log-activity', [AccountsReceivableController::class, 'quickLogActivity'])->name('quick-log-activity');
        Route::get('/export-dashboard', [AccountsReceivableController::class, 'exportDashboard'])->name('export-dashboard');
        Route::get('/aging-chart-data', [AccountsReceivableController::class, 'getAgingChartData'])->name('aging-chart-data');
        Route::get('/performance-metrics', [AccountsReceivableController::class, 'getPerformanceMetrics'])->name('performance-metrics');

        // Other existing routes...
        Route::get('/customer-balances', [AccountsReceivableController::class, 'customerBalances'])->name('customer-balances');
        Route::get('/collection-performance', [AccountsReceivableController::class, 'collectionPerformance'])->name('collection-performance');
    });
    // Payment Allocation Routes
    Route::prefix('admin/payments')->name('admin.payments.')->group(function () {
        Route::get('/unallocated', [PaymentAllocationController::class, 'unallocatedPayments'])->name('unallocated');
        Route::get('/{payment}/allocate', [PaymentAllocationController::class, 'showAllocationForm'])->name('allocate.form');
        Route::post('/{payment}/allocate', [PaymentAllocationController::class, 'allocatePayment'])->name('allocate');
        Route::post('/{payment}/auto-allocate', [PaymentAllocationController::class, 'autoAllocate'])->name('auto-allocate');
        Route::get('/methods-report', [PaymentAllocationController::class, 'paymentMethodsReport'])->name('methods-report');
    });
    Route::get('admin/payments/dashboard', [PaymentDashboardController::class, 'index'])
        ->name('admin.payments.dashboard');

    Route::get('admin/payments/dashboard/realtime', [PaymentDashboardController::class, 'realtimeData'])
        ->name('admin.payments.dashboard.realtime');

    Route::post('admin/payments/dashboard/export', [PaymentDashboardController::class, 'exportDashboard'])
        ->name('admin.payments.dashboard.export');

});

// Bill Information Routes (Public)
Route::get('/bills/{bill}/info', [BillController::class, 'info']);
Route::get('/bills/info/customer/{customer}', [BillController::class, 'infoByCustomer']);
Route::get('/bills/info/meter/{meter}', [BillController::class, 'infoByMeter']);

// Accounts Receivable Routes


// API Routes for AJAX calls
Route::prefix('admin/api')->name('admin.api.')->group(function () {
    // For write-offs
    Route::get('/customers-with-balance', function() {
        $customers = Customer::withSum('bills', 'balance')
            ->whereHas('bills', function($query) {
                $query->where('bill_status', '!=', 'paid');
            })
            ->orderByDesc('bills_sum_balance')
            ->limit(50)
            ->get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'customer_number' => $customer->customer_number,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'balance' => $customer->bills_sum_balance
                ];
            });

        return response()->json($customers);
    });

    Route::get('/customers/{customer}/bills', function(Customer $customer) {
        $bills = $customer->bills()
            ->where('bill_status', '!=', 'paid')
            ->orderBy('due_date')
            ->get()
            ->map(function($bill) {
                return [
                    'id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'due_date' => $bill->due_date,
                    'balance' => $bill->balance,
                    'late_fee' => $bill->late_fee
                ];
            });

        return response()->json($bills);
    });

    // For collection activities
    Route::get('/delinquent-customers', function() {
        $customers = Customer::with(['bills' => function($query) {
                $query->where('due_date', '<', now())
                      ->where('bill_status', '!=', 'paid');
            }])
            ->whereHas('bills', function($query) {
                $query->where('due_date', '<', now())
                      ->where('bill_status', '!=', 'paid');
            })
            ->withSum(['bills as total_due' => function($query) {
                $query->where('due_date', '<', now())
                      ->where('bill_status', '!=', 'paid');
            }], 'balance')
            ->orderByDesc('total_due')
            ->limit(50)
            ->get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'customer_number' => $customer->customer_number,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'total_due' => $customer->total_due
                ];
            });

        return response()->json($customers);
    });
});
/*
|--------------------------------------------------------------------------
| ORGANIZER ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:organizer'])->prefix('organizer')->group(function () {
    Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('organizer.dashboard');
    Route::get('/ticket-sales', [OrganizerController::class, 'ticketSales'])->name('organizer.ticket-sales');
    Route::get('/bookings', [OrganizerController::class, 'bookings'])->name('organizer.bookings');
    Route::get('/analytics', [OrganizerController::class, 'analytics'])->name('organizer.analytics');
    Route::get('/analytics/events/{event}', [OrganizerController::class, 'eventAnalytics'])->name('organizer.analytics.event');
    Route::get('/analytics/voting/{contest}', [OrganizerController::class, 'votingAnalytics'])->name('organizer.analytics.voting');

    // Event Management
    Route::get('/events', [OrganizerController::class, 'events'])->name('organizer.events');
    Route::get('/events/create', [OrganizerController::class, 'createEvent'])->name('organizer.events.create');
    Route::post('/events', [OrganizerController::class, 'storeEvent'])->name('events.store');
    Route::get('/events/{event}/edit', [OrganizerController::class, 'editEvent'])->name('organizer.events.edit');
    Route::put('/events/{event}', [OrganizerController::class, 'updateEvent'])->name('organizer.events.update');
    Route::get('/events/{event}', [OrganizerController::class, 'showEvent'])->name('organizer.events.show');
    Route::delete('/events/{event}', [OrganizerController::class, 'destroyEvent'])->name('organizer.events.destroy');
    Route::patch('/events/{event}/status', [OrganizerController::class, 'updateEventStatus'])->name('organizer.events.update-status');
    Route::post('/event-categories', [OrganizerController::class, 'storeEventCategory'])->name('event-categories.store');

    // Voting Management
    Route::get('/voting', [OrganizerController::class, 'voting'])->name('organizer.voting');
    Route::get('/voting/create', [OrganizerController::class, 'createVoting'])->name('organizer.voting.create');
    Route::post('/voting', [OrganizerController::class, 'storeVoting'])->name('organizer.voting.store');
    Route::get('/voting/{contest}/edit', [OrganizerController::class, 'editVoting'])->name('organizer.voting.edit');
    Route::put('/voting/{contest}', [OrganizerController::class, 'updateVoting'])->name('organizer.voting.update');
    Route::delete('/voting/{contest}', [OrganizerController::class, 'destroyVoting'])->name('organizer.voting.destroy');
    Route::post('/voting/store', [VotingController::class, 'store'])->name('voting.store');

    // Categories
    Route::post('/categories/store', [VotingCategoryController::class, 'store'])->name('categories.store');
    Route::post('/voting-category/store', [VotingCategoryController::class, 'store'])->name('voting-category.store');

    // Nominee Categories
    Route::resource('nominee-categories', NomineeCategoryController::class);
});

/*
|--------------------------------------------------------------------------
| VENDOR ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/


Route::middleware(['auth', 'verified', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'index'])->name('dashboard');
    Route::view('/services', 'vendor.services')->name('services');
    Route::view('/bookings', 'vendor.bookings')->name('bookings');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/
// User management routes - Single group with all necessary middleware
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('dashboard');

    // User management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Additional user routes
    Route::get('users/{user}/permissions', [\App\Http\Controllers\Admin\UserController::class, 'getPermissions'])->name('users.permissions');
    Route::post('users/{user}/permissions', [\App\Http\Controllers\Admin\UserController::class, 'updatePermissions'])->name('users.permissions.update');

    // Permissions route
    Route::get('permissions', [\App\Http\Controllers\Admin\UserController::class, 'getAllPermissions'])->name('permissions.index');

    // Role management
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
});
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Role Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::get('/{role}/permissions', [RoleController::class, 'getPermissions'])->name('permissions');
    });

    // Customer Management
    Route::prefix('customers')->name('customers.')->group(function () {
        // AJAX endpoints (should come BEFORE dynamic routes)
        Route::get('/get-available-meters', [CustomerController::class, 'getAvailableMeters'])->name('get-available-meters');
        Route::get('/check-meter-availability', [CustomerController::class, 'checkMeterAvailability'])->name('check-meter-availability');
        Route::get('/meter-category/{id}/details', [CustomerController::class, 'getMeterCategoryDetails'])->name('meter-category.details');
        Route::get('/export-pdf', [CustomerController::class, 'exportPDF'])->name('export-pdf');

        // Customer CRUD
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');

        // Customer-specific routes (these come AFTER the AJAX routes)
        Route::prefix('{customer}')->group(function () {
            // Customer details
            Route::get('/', [CustomerController::class, 'show'])->name('show');
            Route::get('/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/', [CustomerController::class, 'update'])->name('update');
            Route::delete('/', [CustomerController::class, 'destroy'])->name('destroy');

            // Status Management
            Route::patch('/update-status', [CustomerController::class, 'updateStatus'])->name('update-status');

            // Meter Assignment
            Route::post('/assign-meter', [CustomerController::class, 'assignMeter'])->name('assign-meter');
            Route::post('/unassign-meter/{meter}', [CustomerController::class, 'unassignMeter'])->name('unassign-meter');
            Route::post('/unassign-all-meters', [CustomerController::class, 'unassignAllMeters'])->name('unassign-all-meters');

            // Unassign and reassign meter with customer search/creation
            Route::get('/unassign-and-reassign/{meter}', [CustomerController::class, 'showUnassignReassignForm'])->name('unassign-reassign-form');
            Route::post('/unassign-and-reassign/{meter}', [CustomerController::class, 'unassignAndReassign'])->name('unassign-and-reassign');

            // NEW: Quick customer creation for meter assignment
            Route::get('/quick-create-form', [CustomerController::class, 'quickCreateForm'])->name('quick-create-form');
            Route::post('/quick-create', [CustomerController::class, 'quickCreate'])->name('quick-create');

            // Document Upload
            Route::post('/upload-documents', [CustomerController::class, 'uploadDocuments'])->name('upload-documents');

            // Meter Readings
            Route::get('/readings', [CustomerController::class, 'meterReadings'])->name('meter-readings');
            Route::get('/readings/create', [CustomerController::class, 'createReading'])->name('readings.create');
            Route::post('/readings', [CustomerController::class, 'storeReading'])->name('readings.store');

            // Billing
            Route::post('/readings/{reading}/generate-bill', [CustomerController::class, 'generateBill'])->name('generate-bill');
            Route::get('/bills', [CustomerController::class, 'bills'])->name('bills');

            // AJAX endpoints for specific customer
            Route::get('/get-address', [CustomerController::class, 'getCustomerAddress'])->name('get-address');
            Route::get('/meters', [CustomerController::class, 'getCustomerMeters'])->name('get-customer-meters');
        });
    });

    Route::prefix('admin/customers')->name('admin.customers.')->group(function () {

        // Customer search for AJAX
        Route::get('/search', [CustomerController::class, 'searchCustomers'])->name('search');

        // Check customer meters
        Route::get('/{customer}/check-meters', [CustomerController::class, 'checkCustomerMeters'])->name('check-meters');
    });
    // Meter Management
    Route::prefix('meters')->name('meters.')->group(function () {
        Route::get('/', [MeterController::class, 'index'])->name('index');
        Route::post('/', [MeterController::class, 'store'])->name('store');
        Route::get('/available', [MeterController::class, 'availableMeters'])->name('available');
        Route::get('/assigned', [MeterController::class, 'assignedMeters'])->name('assigned');
        Route::get('/by-location', [MeterController::class, 'metersByLocation'])->name('by-location');
        Route::get('/available-json', [MeterController::class, 'getAvailableMeters'])->name('available.json');
        Route::get('/{meter}', [MeterController::class, 'show'])->name('show');
        Route::get('/{meter}/edit', [MeterController::class, 'edit'])->name('edit');
        Route::put('/{meter}', [MeterController::class, 'update'])->name('update');
        Route::post('/{meter}/assign', [MeterController::class, 'assignToCustomer'])->name('assign');
        Route::post('/{meter}/unassign', [MeterController::class, 'unassign'])->name('unassign');
        Route::get('/search', [MeterController::class, 'search'])->name('search');
    });



    // Meter Readings Management
    Route::prefix('meter-readings')->name('meter-readings.')->group(function () {
        Route::get('/', [MeterReadingController::class, 'index'])->name('index');
        Route::get('/create', [MeterReadingController::class, 'create'])->name('create');
        Route::post('/', [MeterReadingController::class, 'store'])->name('store');
        Route::get('/last-reading', [MeterReadingController::class, 'getLastReading'])->name('last-reading');

    });


    // Meter reading estimation
    Route::get('/customers/{customer}/meters/{meter}/estimate-consumption',
        [MeterReadingController::class, 'estimateConsption'])
        ->name('admin.meter-readings.estimate');
    Route::get('/meter-readings/exceptions', [MeterReadingController::class, 'exceptions'])->name('meter-readings.exceptions');
    // Meter Categories Management
    Route::prefix('meter-categories')->name('meter-categories.')->group(function () {
        Route::get('/', [MeterCategoryController::class, 'index'])->name('index');
        Route::get('/create', [MeterCategoryController::class, 'create'])->name('create');
        Route::post('/', [MeterCategoryController::class, 'store'])->name('store');
        Route::get('/{meterCategory}', [MeterCategoryController::class, 'show'])->name('show');
        Route::get('/{meterCategory}/edit', [MeterCategoryController::class, 'edit'])->name('edit');
        Route::put('/{meterCategory}', [MeterCategoryController::class, 'update'])->name('update');
        Route::delete('/{meterCategory}', [MeterCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{meterCategory}/calculate', [MeterCategoryController::class, 'calculateCharge'])->name('calculate');

        // Pricing Tiers
        Route::post('/{meterCategory}/tiers', [MeterCategoryController::class, 'storeTier'])->name('tiers.store');
        Route::put('/{meterCategory}/tiers/{pricingTier}', [MeterCategoryController::class, 'updateTier'])->name('tiers.update');
        Route::delete('/{meterCategory}/tiers/{pricingTier}', [MeterCategoryController::class, 'destroyTier'])->name('tiers.destroy');
    });

    // Water Applications
    Route::prefix('water-applications')->name('water-applications.')->group(function () {
        Route::get('/', [WaterConnectionController::class, 'index'])->name('index');
        Route::get('/{application}', [WaterConnectionController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [WaterConnectionController::class, 'approve'])->name('approve');
        Route::post('/{application}/decline', [WaterConnectionController::class, 'decline'])->name('decline');
    });
});
