<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectSupportController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\DocumentCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profile - tetap bisa diakses semua role (akun sendiri)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ============================================
    // CUSTOMERS
    // PENTING: /create dan /{id}/edit HARUS didaftarkan
    // sebelum /{id}, walau beda middleware grup.
    // ============================================
    Route::middleware('can.edit')->group(function () {
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::get('/customers/{customer}/contacts/create', [CustomerContactController::class, 'create'])->name('customer-contacts.create');
        Route::post('/customers/{customer}/contacts', [CustomerContactController::class, 'store'])->name('customer-contacts.store');
    });
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    // ============================================
    // COMPANIES
    // ============================================
    // Route::middleware('can.edit')->group(function () {
    //     Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    //     Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    //     Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    //     Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    //     Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    // });
    // Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    // Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');

    // ============================================
    // PROJECTS
    // ============================================
    Route::middleware('can.edit')->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        Route::get('/projects/{project}/supports/create', [ProjectSupportController::class, 'create'])->name('project-supports.create');
        Route::post('/projects/{project}/supports', [ProjectSupportController::class, 'store'])->name('project-supports.store');
        Route::delete('/project-supports/{projectSupport}', [ProjectSupportController::class, 'destroy'])->name('project-supports.destroy');

        Route::get('/projects/{project}/documents/create', [ProjectDocumentController::class, 'create'])->name('project-documents.create');
        Route::post('/projects/{project}/documents', [ProjectDocumentController::class, 'store'])->name('project-documents.store');

        Route::get('/projects/{project}/tasks/create', [ProjectTaskController::class, 'create'])->name('project-tasks.create');
        Route::post('/projects/{project}/tasks', [ProjectTaskController::class, 'store'])->name('project-tasks.store');
        Route::delete('/project-tasks/{projectTask}', [ProjectTaskController::class, 'destroy'])->name('project-tasks.destroy');    

    });
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // ============================================
    // DOCUMENT CATEGORIES
    // ============================================
    Route::middleware('can.edit')->group(function () {
        Route::get('/document-categories', [DocumentCategoryController::class, 'index'])->name('document-categories.index');
        Route::get('/document-categories/create', [DocumentCategoryController::class, 'create'])->name('document-categories.create');
        Route::post('/document-categories', [DocumentCategoryController::class, 'store'])->name('document-categories.store');
        Route::get('/document-categories/{documentCategory}/edit', [DocumentCategoryController::class, 'edit'])->name('document-categories.edit');
        Route::put('/document-categories/{documentCategory}', [DocumentCategoryController::class, 'update'])->name('document-categories.update');
        Route::delete('/document-categories/{documentCategory}', [DocumentCategoryController::class, 'destroy'])->name('document-categories.destroy');
        Route::patch('/document-categories/{id}/restore', [DocumentCategoryController::class, 'restore'])->name('document-categories.restore');
    });
});

require __DIR__.'/auth.php';

Route::middleware('can.edit')->group(function () {
    Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    Route::patch('/trash/customers/{id}/restore', [TrashController::class, 'restoreCustomer'])->name('trash.restore-customer');
    // Route::patch('/trash/companies/{id}/restore', [TrashController::class, 'restoreCompany'])->name('trash.restore-company');
    Route::patch('/trash/projects/{id}/restore', [TrashController::class, 'restoreProject'])->name('trash.restore-project');
});

Route::middleware('super.admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    });