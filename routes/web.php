<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectSupportController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\WorkTypeController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\TechnicianScheduleController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Livewire\ProjectStatusList;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard',
    [DashboardController::class,'index']
)->middleware(['auth','verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ============================================
    // SALES — manage
    // ============================================
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

    Route::middleware('permission:view-sales|manage-sales')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    });

    // ============================================
    // SALES — Meeting & Follow Up
    // ============================================
    Route::middleware('permission:manage-sales')->prefix('sales')->name('sales.')->group(function () {
        Route::resource('meetings', MeetingController::class)->except(['show']);
        Route::get('meetings/{meeting}', [MeetingController::class, 'show'])->name('meetings.show');

        Route::resource('follow-ups', FollowUpController::class)->except(['show']);
        Route::get('follow-ups/{followUp}', [FollowUpController::class, 'show'])->name('follow-ups.show');

        // Follow Up create with optional pre-selected customer/meeting
        Route::get('follow-ups/create/{customer?}', [FollowUpController::class, 'create'])->name('follow-ups.create-with-customer');
    });

    Route::middleware('permission:view-sales|manage-sales')->prefix('sales')->name('sales.')->group(function () {
        Route::get('meetings', [MeetingController::class, 'index'])->name('meetings.index');
        Route::get('follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
    });

    // ============================================
    // TEKNISI — manage
    // ============================================
    Route::middleware('permission:manage-teknisi')->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        Route::get('/projects/{project}/supports/create', [ProjectSupportController::class, 'create'])->name('project-supports.create');
        Route::post('/projects/{project}/supports', [ProjectSupportController::class, 'store'])->name('project-supports.store');
        Route::delete('/project-supports/{projectSupport}', [ProjectSupportController::class, 'destroy'])->name('project-supports.destroy');

        Route::get('/projects/{project}/tasks/create', [ProjectTaskController::class, 'create'])->name('project-tasks.create');
        Route::post('/projects/{project}/tasks', [ProjectTaskController::class, 'store'])->name('project-tasks.store');
        Route::delete('/project-tasks/{projectTask}', [ProjectTaskController::class, 'destroy'])->name('project-tasks.destroy');

        Route::post('/projects/{project}/documents', [ProjectDocumentController::class, 'store'])->name('project-documents.store');
        Route::delete('/project-documents/{document}', [ProjectDocumentController::class, 'destroy'])->name('project-documents.destroy');
        Route::get('/project-documents/{document}/preview', [ProjectDocumentController::class, 'preview'])->name('project-documents.preview');
    });

    Route::middleware('permission:view-teknisi|manage-teknisi')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/projects/{project}/documents', [ProjectDocumentController::class, 'index'])->name('project-documents.index');
        Route::get('/project-documents/{document}/download', [ProjectDocumentController::class, 'download'])->name('project-documents.download');
    });

    // ============================================
    // TEKNISI — Kalender & Jadwal (Google Calendar)
    // ============================================
    Route::middleware('permission:view-teknisi|manage-teknisi')->prefix('teknisi')->name('teknisi.')->group(function () {
        Route::get('/dashboard', [TechnicianDashboardController::class, 'index'])->name('dashboard');
        Route::get('/kalender/events', [TechnicianScheduleController::class, 'events'])->name('kalender.events');
        Route::get('/jadwal/google-events', [CalendarController::class, 'googleEvents'])->name('google-events');
        Route::get('/jadwal', [TechnicianScheduleController::class, 'jadwal'])->name('jadwal');
    });

    Route::middleware('permission:manage-teknisi')->prefix('teknisi')->name('teknisi.')->group(function () {
        Route::post('/schedules', [TechnicianScheduleController::class, 'store'])->name('schedules.store');
        Route::put('/schedules/{schedule}', [TechnicianScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [TechnicianScheduleController::class, 'destroy'])->name('schedules.destroy');
        Route::get('/kalender/connect', [TechnicianScheduleController::class, 'connect'])->name('kalender.connect');
        Route::get('/kalender/callback', [TechnicianScheduleController::class, 'callback'])->name('kalender.callback');
        Route::delete('/kalender/disconnect', [TechnicianScheduleController::class, 'disconnect'])->name('kalender.disconnect');
    });

    // ============================================
    // ADMIN — Trash
    // ============================================
    Route::middleware('permission:manage-admin')->group(function () {
        Route::patch('/trash/customers/{id}/restore', [TrashController::class, 'restoreCustomer'])->name('trash.restore-customer');
        Route::patch('/trash/projects/{id}/restore', [TrashController::class, 'restoreProject'])->name('trash.restore-project');
    });

    Route::middleware('permission:view-admin|manage-admin')->group(function () {
        Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    });

    // ============================================
    // ADMIN — Invoice, PO, Payment
    // ============================================
    Route::middleware('permission:manage-admin')->prefix('admin')->name('admin.')->group(function () {
        // Invoice
        Route::get('/invoices/create', [AdminController::class, 'invoicesCreate'])->name('invoices.create');
        Route::post('/invoices', [AdminController::class, 'invoicesStore'])->name('invoices.store');
        Route::get('/invoices/{invoice}/edit', [AdminController::class, 'invoicesEdit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [AdminController::class, 'invoicesUpdate'])->name('invoices.update');
        Route::delete('/invoices/{invoice}', [AdminController::class, 'invoicesDestroy'])->name('invoices.destroy');

        // PO
        Route::get('/pos/create', [AdminController::class, 'posCreate'])->name('pos.create');
        Route::post('/pos', [AdminController::class, 'posStore'])->name('pos.store');
        Route::get('/pos/{purchaseOrder}/edit', [AdminController::class, 'posEdit'])->name('pos.edit');
        Route::put('/pos/{purchaseOrder}', [AdminController::class, 'posUpdate'])->name('pos.update');
        Route::delete('/pos/{purchaseOrder}', [AdminController::class, 'posDestroy'])->name('pos.destroy');

        // Payment
        Route::get('/payments/create', [AdminController::class, 'paymentsCreate'])->name('payments.create');
        Route::post('/payments', [AdminController::class, 'paymentsStore'])->name('payments.store');
        Route::delete('/payments/{payment}', [AdminController::class, 'paymentsDestroy'])->name('payments.destroy');
    });

    Route::middleware('permission:view-admin|manage-admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/invoices', [AdminController::class, 'invoicesIndex'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [AdminController::class, 'invoicesShow'])->name('invoices.show');
        Route::get('/pos', [AdminController::class, 'posIndex'])->name('pos.index');
        Route::get('/pos/{purchaseOrder}', [AdminController::class, 'posShow'])->name('pos.show');
        Route::get('/payments', [AdminController::class, 'paymentsIndex'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminController::class, 'paymentsShow'])->name('payments.show');
    });

    // ============================================
    // MARKETING — manage
    // ============================================
    Route::middleware('permission:manage-marketing')->group(function () {
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
        Route::patch('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

        // Data Partner (supplier, vendor, kontraktor, partner, distributor)
        Route::get('/partners/create', [PartnerController::class, 'create'])->name('partners.create');
        Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
        Route::get('/partners/{partner}/edit', [PartnerController::class, 'edit'])->name('partners.edit');
        Route::put('/partners/{partner}', [PartnerController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{partner}', [PartnerController::class, 'destroy'])->name('partners.destroy');
    });

    Route::middleware('permission:view-marketing|manage-marketing')->group(function () {
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('/leads/{lead}/documents/{document}/preview', [LeadController::class, 'previewDocument'])->name('leads.documents.preview');
        Route::get('/leads/{lead}/documents/{document}/download', [LeadController::class, 'downloadDocument'])->name('leads.documents.download');

        Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
    });

    // ============================================
    // MONITORING — Manager & Super Admin
    // ============================================
    Route::get('/monitoring',
        [MonitoringController::class,'index']
    )->middleware(['auth','verified','permission:view-monitoring|manage-monitoring'])->name('monitoring.index');

    // ============================================
    // ADMIN PANEL — Super Admin only (settings, config)
    // ============================================
    Route::middleware('permission:manage-monitoring')->prefix('admin-panel')->name('admin-panel.')->group(function () {
        Route::get('/', [AdminPanelController::class, 'index'])->name('index');
        
        // Users
        Route::get('/users/create', [AdminPanelController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminPanelController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminPanelController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminPanelController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminPanelController::class, 'destroyUser'])->name('users.destroy');
        
        // Roles
        Route::get('/roles/create', [AdminPanelController::class, 'createRole'])->name('roles.create');
        Route::post('/roles', [AdminPanelController::class, 'storeRole'])->name('roles.store');
        Route::get('/roles/{role}/edit', [AdminPanelController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{role}', [AdminPanelController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [AdminPanelController::class, 'destroyRole'])->name('roles.destroy');
        
        // Audit Log
        Route::get('/audit-log', [AdminPanelController::class, 'auditLog'])->name('audit-log');

        // Account Manager
        Route::get('/account-managers', [AccountManagerController::class, 'index'])->name('account-managers.index');
        Route::resource('account-managers', AccountManagerController::class)->except(['index']);

        // Work Type
        Route::get('/work-types', [WorkTypeController::class, 'index'])->name('work-types.index');
        Route::resource('work-types', WorkTypeController::class)->except(['index']);

        // Document Categories
        Route::get('/document-categories', [DocumentCategoryController::class, 'index'])->name('document-categories.index');
        Route::get('/document-categories/create', [DocumentCategoryController::class, 'create'])->name('document-categories.create');
        Route::post('/document-categories', [DocumentCategoryController::class, 'store'])->name('document-categories.store');
        Route::get('/document-categories/{documentCategory}/edit', [DocumentCategoryController::class, 'edit'])->name('document-categories.edit');
        Route::put('/document-categories/{documentCategory}', [DocumentCategoryController::class, 'update'])->name('document-categories.update');
        Route::delete('/document-categories/{documentCategory}', [DocumentCategoryController::class, 'destroy'])->name('document-categories.destroy');
        Route::patch('/document-categories/{id}/restore', [DocumentCategoryController::class, 'restore'])->name('document-categories.restore');

        // Project Statuses
        Route::get('/project-statuses', ProjectStatusList::class)->name('project-statuses.index');
        Route::get('/project-statuses/create', [ProjectStatusController::class, 'create'])->name('project-statuses.create');
        Route::post('/project-statuses', [ProjectStatusController::class, 'store'])->name('project-statuses.store');
        Route::get('/project-statuses/{projectStatus}/edit', [ProjectStatusController::class, 'edit'])->name('project-statuses.edit');
        Route::put('/project-statuses/{projectStatus}', [ProjectStatusController::class, 'update'])->name('project-statuses.update');
        Route::delete('/project-statuses/{projectStatus}', [ProjectStatusController::class, 'destroy'])->name('project-statuses.destroy');
    });
});
require __DIR__.'/auth.php';
