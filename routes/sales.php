<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\FollowUpController;
use Illuminate\Support\Facades\Route;

// Customers - Manage
Route::middleware('permission:manage-sales')->group(function () {
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/customers/{customer}/contacts/create', [CustomerContactController::class, 'create'])->name('customer-contacts.create');
    Route::post('/customers/{customer}/contacts', [CustomerContactController::class, 'store'])->name('customer-contacts.store');
    Route::get('/customer-contacts/{customerContact}/edit', [CustomerContactController::class, 'edit'])->name('customer-contacts.edit');
    Route::put('/customer-contacts/{customerContact}', [CustomerContactController::class, 'update'])->name('customer-contacts.update');
    Route::delete('/customer-contacts/{customerContact}', [CustomerContactController::class, 'destroy'])->name('customer-contacts.destroy');
});

// Customers - View
Route::middleware('permission:view-sales|manage-sales')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
});

// Meetings & Follow-ups - Manage
Route::middleware('permission:manage-sales')->prefix('sales')->name('sales.')->group(function () {
    Route::resource('meetings', MeetingController::class)->except(['show']);
    Route::get('meetings/{meeting}', [MeetingController::class, 'show'])->name('meetings.show');

    Route::resource('follow-ups', FollowUpController::class)->except(['show']);
    Route::get('follow-ups/{followUp}', [FollowUpController::class, 'show'])->name('follow-ups.show');

    Route::get('follow-ups/create/{customer?}', [FollowUpController::class, 'create'])->name('follow-ups.create-with-customer');
});

// Meetings & Follow-ups - View
Route::middleware('permission:view-sales|manage-sales')->prefix('sales')->name('sales.')->group(function () {
    Route::get('meetings', [MeetingController::class, 'index'])->name('meetings.index');
    Route::get('follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
});