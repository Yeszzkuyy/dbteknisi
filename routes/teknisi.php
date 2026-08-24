<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectSupportController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Http\Controllers\TechnicianScheduleController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

// Projects - Manage
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

// Projects - View
Route::middleware('permission:view-teknisi|manage-teknisi')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/documents', [ProjectDocumentController::class, 'index'])->name('project-documents.index');
    Route::get('/project-documents/{document}/download', [ProjectDocumentController::class, 'download'])->name('project-documents.download');
});

// Technician Dashboard & Calendar - View
Route::middleware('permission:view-teknisi|manage-teknisi')->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/dashboard', [TechnicianDashboardController::class, 'index'])->name('dashboard');
    Route::get('/kalender/events', [TechnicianScheduleController::class, 'events'])->name('kalender.events');
    Route::get('/jadwal/google-events', [CalendarController::class, 'googleEvents'])->name('google-events');
    Route::get('/jadwal', [TechnicianScheduleController::class, 'jadwal'])->name('jadwal');
    Route::post('/heartbeat', [TechnicianDashboardController::class, 'heartbeat'])->name('heartbeat');
});

// Technician Calendar - Manage
Route::middleware('permission:manage-teknisi')->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::post('/schedules', [TechnicianScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{schedule}', [TechnicianScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [TechnicianScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::get('/kalender/connect', [TechnicianScheduleController::class, 'connect'])->name('kalender.connect');
    Route::get('/kalender/callback', [TechnicianScheduleController::class, 'callback'])->name('teknisi.kalender.callback');
    Route::delete('/kalender/disconnect', [TechnicianScheduleController::class, 'disconnect'])->name('kalender.disconnect');
});