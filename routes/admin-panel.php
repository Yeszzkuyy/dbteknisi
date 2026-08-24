<?php

use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\WorkTypeController;
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\ProjectStatusController;
use App\Livewire\ProjectStatusList;
use Illuminate\Support\Facades\Route;

// Admin Panel - Super Admin only
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