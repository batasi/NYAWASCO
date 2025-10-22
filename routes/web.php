<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\AttendeeController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\VotingCategoryController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\PesapalController;

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION (JETSTREAM / FORTIFY)
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/category/{category}', [EventController::class, 'byCategory'])->name('events.byCategory');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/tickets', [EventController::class, 'tickets'])->name('events.tickets');

Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
Route::get('/voting/{contest}', [VotingController::class, 'show'])->name('voting.show');
Route::get('/voting/category/{category}', [VotingController::class, 'byCategory'])->name('voting.byCategory');

// Conditional Organizer Modal Logic (Frontend handles redirection if not authenticated)
Route::get('/organizers', [OrganizerController::class, 'index'])->name('organizers.index');
Route::get('/organizers/{organizer}', [OrganizerController::class, 'show'])->name('organizers.show');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Static Pages
Route::view('/about', 'static.about', ['title' => 'About Us - EventSphere'])->name('about');
Route::view('/contact', 'static.contact', ['title' => 'Contact Us - EventSphere'])->name('contact');
Route::view('/privacy', 'static.privacy', ['title' => 'Privacy Policy - EventSphere'])->name('privacy');
Route::view('/terms', 'static.terms', ['title' => 'Terms of Service - EventSphere'])->name('terms');
Route::view('/help', 'static.help', ['title' => 'Help Center - EventSphere'])->name('help');

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/mpesa/stkpush', [MpesaController::class, 'stkPush'])->name('mpesa.stkpush');
Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');

Route::post('/pesapal/stkpush', [PesapalController::class, 'stkPush'])->name('pesapal.stkpush');
Route::post('/pesapal/callback', [PesapalController::class, 'callback'])->name('pesapal.callback');
Route::get('/pesapal/register-ipn', [PesapalController::class, 'registerIpn']);

Route::prefix('api')->group(function () {
    Route::get('/live-activities', [LiveActivityController::class, 'index'])->name('api.live-activities');
    Route::get('/search', [SearchApiController::class, 'search'])->name('api.search');
    Route::get('/events/featured', [EventController::class, 'featured'])->name('api.events.featured');
    Route::get('/voting/featured', [VotingController::class, 'featured'])->name('api.voting.featured');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::post('/events/{event}/tickets/purchase', [TicketController::class, 'purchase'])->name('tickets.purchase');

    // Voting
    Route::post('/voting/{contest}/vote', [VotingController::class, 'vote'])->name('voting.vote');
    Route::get('/my-votes', [VotingController::class, 'myVotes'])->name('voting.myVotes');
});

/*
|--------------------------------------------------------------------------
| ORGANIZER ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:organizer'])->prefix('organizer')->group(function () {
    Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('organizer.dashboard');

    // Event Management
    Route::get('/events', [OrganizerController::class, 'events'])->name('organizer.events');
    Route::get('/events/create', [OrganizerController::class, 'createEvent'])->name('organizer.events.create');
    Route::post('/events', [OrganizerController::class, 'storeEvent'])->name('organizer.events.store');
    Route::get('/events/{event}/edit', [OrganizerController::class, 'editEvent'])->name('organizer.events.edit');
    Route::put('/events/{event}', [OrganizerController::class, 'updateEvent'])->name('organizer.events.update');
    Route::delete('/events/{event}', [OrganizerController::class, 'destroyEvent'])->name('organizer.events.destroy');

    // Voting Management
    Route::get('/voting', [OrganizerController::class, 'voting'])->name('organizer.voting');
    Route::get('/voting/create', [OrganizerController::class, 'createVoting'])->name('organizer.voting.create');
    Route::post('/voting', [OrganizerController::class, 'storeVoting'])->name('organizer.voting.store');
    Route::get('/voting/{contest}/edit', [OrganizerController::class, 'editVoting'])->name('organizer.voting.edit');
    Route::put('/voting/{contest}', [OrganizerController::class, 'updateVoting'])->name('organizer.voting.update');
    Route::delete('/voting/{contest}', [OrganizerController::class, 'destroyVoting'])->name('organizer.voting.destroy');
    Route::post('/voting/store', [VotingController::class, 'store'])->name('voting.store');
    Route::post('/categories/store', [VotingCategoryController::class, 'store'])
        ->name('categories.store');
    Route::post('/organizer/voting-category/store', [VotingCategoryController::class, 'store'])
    ->name('voting-category.store');

    // Analytics
    Route::get('/analytics', [OrganizerController::class, 'analytics'])->name('organizer.analytics');
    Route::get('/analytics/events/{event}', [OrganizerController::class, 'eventAnalytics'])->name('organizer.analytics.event');
    Route::get('/analytics/voting/{contest}', [OrganizerController::class, 'votingAnalytics'])->name('organizer.analytics.voting');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('dashboard', [UserController::class, 'index'])->name('dashboard');

    // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'usersIndex'])->name('index');
        Route::get('/data', [UserController::class, 'getUsersData'])->name('data');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // User Permissions & Status
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

    Route::get('/attendees', [AttendeeController::class, 'index'])->name('attendees.index');
    Route::get('/attendees/{id}', [AttendeeController::class, 'show'])->name('attendees.show');
    Route::get('/attendees/{userId}/ticket-purchases', [AttendeeController::class, 'ticketPurchases'])->name('attendees.ticket-purchases');
    Route::get('/admin/attendees/data', [AttendeeController::class, 'getAttendeesData'])->name('attendees.data');
    Route::get('/admin/attendees/stats', [AttendeeController::class, 'getAttendeesStats'])->name('attendees.stats');

    // Vendor Management
    Route::prefix('vendors')->name('vendors.')->group(function () {
        // List all vendors
        Route::get('/', [VendorController::class, 'index'])->name('index');

        Route::get('/data', [VendorController::class, 'getVendorsData'])->name('data');
        Route::get('/stats', [VendorController::class, 'getVendorsStats'])->name('stats');
        Route::get('/{id}/details', [VendorController::class, 'getVendorDetails'])->name('details');

        // Optional: dashboard route for vendor admin overview
        Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
    });


    // Other admin management views
    Route::prefix('management')->name('management.')->group(function () {
        Route::view('/events', 'admin.events', ['title' => 'Event Management - EventSphere'])->name('events');
        Route::view('/voting', 'admin.voting', ['title' => 'Voting Management - EventSphere'])->name('voting');
        Route::view('/tickets', 'admin.tickets', ['title' => 'Ticket Management - EventSphere'])->name('tickets');
        Route::view('/analytics', 'admin.analytics', ['title' => 'Analytics - EventSphere'])->name('analytics');
    });
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
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
