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
use App\Http\Controllers\GanttController; // <-- Added Gantt Controller Import

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Group all routes that require the user to be logged in
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Profile Management ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Activity Logs ---
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

    // --- Reports & Calendar ---
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/calendar', [ReportController::class, 'calendar'])->name('reports.calendar');
    
    // --- NEW: Save Personal Calendar Notes ---
    Route::post('/calendar/save-note', [ReportController::class, 'saveNote'])->name('reports.saveNote');

    // --- Gantt Timeline ---
    Route::get('/gantt', [GanttController::class, 'index'])->name('gantt.index');

    // --- Notifications ---
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // --- Tasks Management ---
    // 1. Registry
    Route::get('/tasks/registry', [TaskController::class, 'registry'])->name('tasks.registry');
    
    // 2. Custom Task Actions (Claim, Start, Finish, Approve, Reject)
    Route::post('/tasks/{task}/claim', [TaskController::class, 'claim'])->name('tasks.claim');
    
    // Task Start/Finish Buttons
    Route::post('/tasks/{task}/start', [TaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{task}/finish', [TaskController::class, 'finish'])->name('tasks.finish');
    
    // Task Edit Approval Workflow
    Route::post('/tasks/{task}/approve', [TaskController::class, 'approveEdit'])->name('tasks.approve');
    Route::post('/tasks/{task}/reject', [TaskController::class, 'rejectEdit'])->name('tasks.reject');

    // 3. Standard Resource
    Route::resource('tasks', TaskController::class);

   

    // =========================================================================
    //  GROUP 1: ADMIN & MANAGER RESOURCES
    //  (Manage Tab Items: Clients, Categories, Statuses, Systems, Types)
    // =========================================================================
    Route::group(['middleware' => function ($request, $next) {
        if (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Manager')) {
            return $next($request);
        }
        abort(403, 'Unauthorized. Access restricted to Admins and Managers.');
    }], function () {
        Route::resource('clients', ClientController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('statuses', StatusController::class);
        Route::resource('systems', SystemController::class);
        Route::resource('types', TypeController::class);

         // --- Project Management (Systems/Projects main view) ---
        Route::resource('projects', ProjectController::class);
    });

    // =========================================================================
    //  GROUP 2: STRICT ADMIN ONLY RESOURCES
    //  (System Tab Items: Roles, User Accounts)
    // =========================================================================
    Route::group(['middleware' => function ($request, $next) {
        if (Auth::user()->hasRole('Administrator')) {
            return $next($request);
        }
        abort(403, 'Unauthorized. Only Administrators can access this area.');
    }], function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class); 
    });

});

require __DIR__.'/auth.php';