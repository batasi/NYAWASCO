<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\ProfileController;
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

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Pages
Route::view('/about', 'about.index')->name('about');
Route::view('/about/management', 'about.management')->name('management');
Route::view('/projects', 'projects.index')->name('projects');
Route::view('/pricing', 'static.pricing', ['title' => 'Pricing - NYAWASCO'])->name('pricing');
Route::view('/contact', 'static.contact', ['title' => 'Contact Us - NYAWASCO'])->name('contact');
Route::view('/privacy', 'static.privacy', ['title' => 'Privacy Policy - NYAWASCO'])->name('privacy');
Route::view('/terms', 'static.terms', ['title' => 'Terms of Service - NYAWASCO'])->name('terms');
Route::view('/help', 'static.help', ['title' => 'Help Center - NYAWASCO'])->name('help');

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

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tickets & Bookings
    Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.my-tickets');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticketPurchase}', [TicketController::class, 'showTicket'])->name('tickets.show');
    Route::get('/tickets/{ticketPurchase}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::get('/tickets/{ticketPurchase}/view', [TicketController::class, 'view'])->name('tickets.view');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/bookings/{booking}', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Voting
    Route::post('/voting/{contest}/vote', [VotingController::class, 'vote'])->name('voting.vote');
    Route::get('/my-votes', [VotingController::class, 'myVotes'])->name('voting.myVotes');

    // Event Ticket Purchase
    Route::post('/events/{event}/tickets/purchase', [TicketController::class, 'purchase'])->name('tickets.purchase');

    // Payments
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
    Route::get('bills/{bill}/receipt/print', [BillController::class, 'printReceipt'])
        ->name('bills.receipt.print');
    Route::get('/bills/{bill}/receipt', [BillController::class, 'generateReceipt'])->name('bills.receipt');
});

// Bill Information Routes (Public)
Route::get('/bills/{bill}/info', [BillController::class, 'info']);
Route::get('/bills/info/customer/{customer}', [BillController::class, 'infoByCustomer']);
Route::get('/bills/info/meter/{meter}', [BillController::class, 'infoByMeter']);

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

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'usersIndex'])->name('index');
        Route::get('/data', [UserController::class, 'getUsersData'])->name('data');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{user}/permissions', [UserController::class, 'getPermissions'])->name('permissions');
        Route::post('/{user}/permissions', [UserController::class, 'updatePermissions'])->name('permissions.update');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/toggle-verification', [UserController::class, 'toggleVerification'])->name('toggle-verification');
        Route::get('/{user}/stats', [UserController::class, 'getUserStats'])->name('stats');
    });

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

    });



    // Meter Readings Management
    Route::prefix('meter-readings')->name('meter-readings.')->group(function () {
        Route::get('/', [MeterReadingController::class, 'index'])->name('index');
        Route::get('/create', [MeterReadingController::class, 'create'])->name('create');
        Route::post('/', [MeterReadingController::class, 'store'])->name('store');
        Route::get('/last-reading', [MeterReadingController::class, 'getLastReading'])->name('last-reading');

    });

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

    // Attendee Management
    Route::prefix('attendees')->name('attendees.')->group(function () {
        Route::get('/', [AttendeeController::class, 'index'])->name('index');
        Route::get('/{id}', [AttendeeController::class, 'show'])->name('show');
        Route::get('/{userId}/ticket-purchases', [AttendeeController::class, 'ticketPurchases'])->name('ticket-purchases');
        Route::get('/data', [AttendeeController::class, 'getAttendeesData'])->name('data');
        Route::get('/stats', [AttendeeController::class, 'getAttendeesStats'])->name('stats');
    });

    // Vendor Management
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
        Route::get('/data', [VendorController::class, 'getVendorsData'])->name('data');
        Route::get('/stats', [VendorController::class, 'getVendorsStats'])->name('stats');
        Route::get('/{id}/details', [VendorController::class, 'getVendorDetails'])->name('details');
    });

    // Event Approval
    Route::post('/events/{event}/approve', [OrganizerController::class, 'approveEvent'])->name('events.approve');

    // Management Views
    Route::prefix('management')->name('management.')->group(function () {
        Route::view('/events', 'admin.events')->name('events');
        Route::view('/voting', 'admin.voting')->name('voting');
        Route::view('/tickets', 'admin.tickets')->name('tickets');
        Route::view('/analytics', 'admin.analytics')->name('analytics');
    });
});
