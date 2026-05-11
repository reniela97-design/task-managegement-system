<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\NotificationController; 
use App\Http\Controllers\GanttController; 

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Group all routes that require the user to be logged in
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Profile Management ---
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
    
    // --- Activity Logs ---
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

    // --- Reports & Calendar ---
    Route::controller(ReportController::class)->group(function () {
        Route::get('/reports', 'index')->name('reports.index');
        Route::get('/calendar', 'calendar')->name('reports.calendar');
        
        // --- Save Personal Calendar Notes ---
        Route::post('/calendar/save-note', 'saveNote')->name('reports.saveNote');
    });

    // --- Gantt Timeline ---
    Route::get('/gantt', [GanttController::class, 'index'])->name('gantt.index');

    // --- Notifications ---
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/read', 'markAsRead')->name('read');
        Route::post('/read-all', 'markAllRead')->name('readAll');
    });

    // --- Tasks Management ---
    Route::controller(TaskController::class)->prefix('tasks')->name('tasks.')->group(function () {
        // 1. Registry
        Route::get('/registry', 'registry')->name('registry');
        // 2. Custom Task Actions
        Route::post('/{task}/claim', 'claim')->name('claim');
        Route::post('/{task}/start', 'start')->name('start');
        Route::post('/{task}/finish', 'finish')->name('finish');
        // Task Edit Approval Workflow
        Route::post('/{task}/approve', 'approveEdit')->name('approve');
        Route::post('/{task}/reject', 'rejectEdit')->name('reject');
    });

    // 3. Standard Resource
    Route::resource('tasks', TaskController::class);

    // =========================================================================
    //  GROUP 1: ADMIN & MANAGER RESOURCES
    //  (Fixed: Using 'role' middleware alias instead of inline function)
    // =========================================================================
    Route::middleware(['role:Administrator,Manager'])->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('statuses', StatusController::class);
        Route::resource('systems', SystemController::class);
        Route::resource('types', TypeController::class);

        // --- Project Management ---
        Route::resource('projects', ProjectController::class);
    });

    // =========================================================================
    //  GROUP 2: STRICT ADMIN ONLY RESOURCES
    //  (Fixed: Using 'role' middleware alias for route caching compatibility)
    // =========================================================================
    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class); 
    });

});

require __DIR__.'/auth.php';