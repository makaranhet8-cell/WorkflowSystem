<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Controllers
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\MissionRequestController;
use App\Http\Controllers\ApproverController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Public Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));
Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
    });
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'showRegistrationForm')->name('register');
        Route::post('/register', 'register');
    });
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
/*
|--------------------------------------------------------------------------
| Authenticated Routes (Staff & Admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Resource Routes សម្រាប់ Leave និង Mission Requests
    Route::resource('leave-requests', LeaveRequestController::class);
    Route::resource('mission-requests', MissionRequestController::class);
    Route::get('/users', [UserController::class, 'index'])->name('users.list');
    /*
    |--------------------------------------------------------------------------
    | Admin & Approver Only Routes
    |--------------------------------------------------------------------------
    */
    // ប្រើ Middleware 'role' ឬ 'permission' របស់ Spatie ផ្ទាល់តែម្តង
    Route::middleware(['role:admin|admin_it|admin_sale|approver|team_leader|hr_manager|ceo|cfo'])->group(function () {

    // User Management (Admin)
        Route::prefix('admin/users')->name('admin.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

            // Department Allocation
            Route::get('/{user}/department/edit', [UserController::class, 'departmentEdit'])->name('department.edit');
            Route::put('/{user}/department', [UserController::class, 'departmentUpdate'])->name('department.update');
        });

        // Approver Actions
        Route::prefix('approver')->name('approver.')->group(function () {
            Route::get('/dashboard', [ApproverController::class, 'dashboard'])->name('dashboard');

            // Approval Actions
            Route::post('/leave-requests/{leaveRequest}/approve', [ApproverController::class, 'approveLeave'])->name('leave.approve');
            Route::post('/leave-requests/{leaveRequest}/reject', [ApproverController::class, 'rejectLeave'])->name('leave.reject');
            Route::post('/mission-requests/{missionRequest}/approve', [ApproverController::class, 'approveMission'])->name('mission.approve');
            Route::post('/mission-requests/{missionRequest}/reject', [ApproverController::class, 'rejectMission'])->name('mission.reject');
        });
    });
    /*
|--------------------------------------------------------------------------
| System Settings (Roles & Permissions)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|admin_it|admin_sales'])->group(function () {
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
});
});
