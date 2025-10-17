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


// =====================================================
//  AUTHENTICATION & EMAIL VERIFICATION ROUTES
// =====================================================

// ---------------------------
// Jetstream-Compatible Email Verification Routes
// ---------------------------

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

// =====================================================
//  PUBLIC ROUTES
// =====================================================

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/category/{category}', [EventController::class, 'byCategory'])->name('events.byCategory');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/tickets', [EventController::class, 'tickets'])->name('events.tickets');

Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
Route::get('/voting/{contest}', [VotingController::class, 'show'])->name('voting.show');
Route::get('/voting/category/{category}', [VotingController::class, 'byCategory'])->name('voting.byCategory');

Route::get('/organizers', [OrganizerController::class, 'index'])->name('organizers.index');
Route::get('/organizers/{organizer}', [OrganizerController::class, 'show'])->name('organizers.show');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');

Route::view('/about', 'static.about', ['title' => 'About Us - EventSphere'])->name('about');
Route::view('/contact', 'static.contact', ['title' => 'Contact Us - EventSphere'])->name('contact');
Route::view('/privacy', 'static.privacy', ['title' => 'Privacy Policy - EventSphere'])->name('privacy');
Route::view('/terms', 'static.terms', ['title' => 'Terms of Service - EventSphere'])->name('terms');
Route::view('/help', 'static.help', ['title' => 'Help Center - EventSphere'])->name('help');

// =====================================================
//  API ROUTES
// =====================================================

Route::prefix('api')->group(function () {
    Route::get('/live-activities', [LiveActivityController::class, 'index'])->name('api.live-activities');
    Route::get('/search', [SearchApiController::class, 'search'])->name('api.search');
    Route::get('/events/featured', [EventController::class, 'featured'])->name('api.events.featured');
    Route::get('/voting/featured', [VotingController::class, 'featured'])->name('api.voting.featured');
});

// =====================================================
//  AUTHENTICATED USER ROUTES
// =====================================================

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

// =====================================================
//  ORGANIZER ROUTES
// =====================================================

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

    // Analytics
    Route::get('/analytics', [OrganizerController::class, 'analytics'])->name('organizer.analytics');
    Route::get('/analytics/events/{event}', [OrganizerController::class, 'eventAnalytics'])->name('organizer.analytics.event');
    Route::get('/analytics/voting/{contest}', [OrganizerController::class, 'votingAnalytics'])->name('organizer.analytics.voting');
});

// =====================================================
//  ADMIN ROUTES
// =====================================================

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    Route::view('/dashboard', 'admin.dashboard', ['title' => 'Admin Dashboard - EventSphere'])->name('admin.dashboard');
    Route::view('/users', 'admin.users', ['title' => 'User Management - EventSphere'])->name('admin.users');
    Route::view('/events', 'admin.events', ['title' => 'Event Management - EventSphere'])->name('admin.events');
    Route::view('/voting', 'admin.voting', ['title' => 'Voting Management - EventSphere'])->name('admin.voting');
});

// =====================================================
//  VENDOR ROUTES
// =====================================================

Route::middleware(['auth', 'verified', 'role:vendor'])->prefix('vendor')->group(function () {
    Route::view('/dashboard', 'vendor.dashboard', ['title' => 'Vendor Dashboard - EventSphere'])->name('vendor.dashboard');
    Route::view('/services', 'vendor.services', ['title' => 'My Services - EventSphere'])->name('vendor.services');
    Route::view('/bookings', 'vendor.bookings', ['title' => 'Bookings - EventSphere'])->name('vendor.bookings');
});
