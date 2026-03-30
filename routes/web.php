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
// use App\Http\Controllers\UserDepartmentController;

// Models
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\MissionRequest;

// Middleware
use App\Http\Middleware\EnsureUserIsApprover;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('auth.login'));

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Staff & Admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // --- Dashboard logic ---
    Route::get('/dashboard', function () {
        /** @var User|null $user */
        $user = Auth::user();
        $allUsers = [];
        $leaveQuery = LeaveRequest::with('user.departments')->latest();
        $missionQuery = MissionRequest::with('user.departments')->latest();

        if ($user && $user->isApproverOrDepartmentAdmin()) {
            $userQuery = User::with('departments');

            if ($user->role === 'admin') {
                $adminDeptIds = $user->departments->pluck('id');
                $userQuery->whereHas('departments', fn($q) => $q->whereIn('departments.id', $adminDeptIds));
                $leaveQuery->whereHas('user.departments', fn($q) => $q->whereIn('departments.id', $adminDeptIds));
                $missionQuery->whereHas('user.departments', fn($q) => $q->whereIn('departments.id', $adminDeptIds));
            }

            $allUsers = $userQuery->whereNotIn('role', ['admin', 'team_leader', 'hr_manager', 'ceo', 'cfo'])->get();
        } else {
            $leaveQuery->where('user_id', $user?->id);
            $missionQuery->where('user_id', $user?->id);
        }

        return view('dashboard', [
            'leaveRequests' => $leaveQuery->get(),
            'missionRequests' => $missionQuery->get(),
            'allUsers' => $allUsers,
        ]);
    })->name('dashboard');

    // --- Requests Management (Leave & Mission) ---
    Route::resource('leave-requests', LeaveRequestController::class);
    Route::resource('mission-requests', MissionRequestController::class);

    // --- User List for General Staff ---
    Route::get('/users', [UserController::class, 'index'])->name('users.list');

    /*
    |--------------------------------------------------------------------------
    | Admin & Approver Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(EnsureUserIsApprover::class)->group(function () {

        // User Management (Admin)
        Route::prefix('admin/users')->name('admin.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index'); // View all users
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');

            // Department Allocation
            Route::get('/{id}/department/edit', [UserController::class, 'departmentEdit'])->name('department.edit');
            Route::put('/{id}/department', [UserController::class, 'departmentUpdate'])->name('department.update');
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
});
