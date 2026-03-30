<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\MissionRequestController;
use App\Http\Controllers\ApproverController;
use App\Http\Controllers\LoginController;
use App\Models\LeaveRequest;
use App\Models\MissionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserDepartmentController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\EnsureUserIsApprover;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('auth.login');
});

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        /** @var User|null $user */
        $user = Auth::user();

        $allUsers = [];
        $leaveQuery = LeaveRequest::with('user.departments')->latest();
        $missionQuery = MissionRequest::with('user.departments')->latest();

        if ($user && $user->isApproverOrDepartmentAdmin()) {
            // ១. ទាញយក User តែក្នុងផ្នែកជាមួយ Admin និងលាក់ Role ធំៗ
            $userQuery = User::with('departments');

            // បន្ថែមលក្ខខណ្ឌឆែក Department បើគាត់ជា Admin ផ្នែក
            if ($user->role === 'admin') {
                $adminDeptIds = $user->departments->pluck('id');
                $userQuery->whereHas('departments', function($q) use ($adminDeptIds) {
                    $q->whereIn('departments.id', $adminDeptIds);
                });
            }

            $allUsers = $userQuery->whereNotIn('role', ['admin', 'team_leader', 'hr_manager', 'ceo', 'cfo'])
                                  ->get();

            // ២. ចម្រាញ់ Leave & Mission ឱ្យឃើញតែក្នុងផ្នែកខ្លួនឯងដែរ
            if ($user->role === 'admin') {
                $adminDeptIds = $user->departments->pluck('id');
                $leaveQuery->whereHas('user.departments', function($q) use ($adminDeptIds) {
                    $q->whereIn('departments.id', $adminDeptIds);
                });
                $missionQuery->whereHas('user.departments', function($q) use ($adminDeptIds) {
                    $q->whereIn('departments.id', $adminDeptIds);
                });
            }
        } else {
            // សម្រាប់ Staff ធម្មតា ឱ្យឃើញតែសំណើខ្លួនឯង
            $leaveQuery->where('user_id', $user?->id);
            $missionQuery->where('user_id', $user?->id);
        }

        return view('dashboard', [
            'leaveRequests' => $leaveQuery->get(),
            'missionRequests' => $missionQuery->get(),
            'allUsers' => $allUsers,
        ]);
    })->name('dashboard');
    // Ensure this matches the controller where you just updated the search code
    Route::middleware(['auth'])->group(function () {
    // សម្រាប់បង្ហាញទំព័រ Edit
    Route::get('/leave-requests/{id}/edit', [LeaveRequestController::class, 'edit'])->name('leave-requests.edit');

    // សម្រាប់ Update ទិន្នន័យ
    Route::put('/leave-requests/{id}', [LeaveRequestController::class, 'update'])->name('leave-requests.update');

    // សម្រាប់លុបទិន្នន័យ (Delete)
    Route::delete('/leave-requests/{id}', [LeaveRequestController::class, 'destroy'])->name('leave-requests.destroy');

    Route::resource('leave-requests', LeaveRequestController::class)->only(['index','create', 'store', 'show']);



    Route::get('/mission-requests/{id}/edit', [MissionRequestController::class, 'edit'])->name('mission-requests.edit');

    // សម្រាប់ Update ទិន្នន័យ
    Route::put('/mission-requests/{id}', [MissionRequestController::class, 'update'])->name('mission-requests.update');

    // សម្រាប់លុបទិន្នន័យ (Delete)
    Route::delete('/mission-requests/{id}', [MissionRequestController::class, 'destroy'])->name('mission-requests.destroy');

    Route::resource('mission-requests', MissionRequestController::class);

});



    Route::middleware(['auth', EnsureUserIsApprover::class])
    ->prefix('admin/users')
    ->name('admin.users.') // បន្ថែម 'admin.' នៅទីនេះ
    ->group(function () {

        // សម្រាប់ Edit: ឈ្មោះពេញនឹងក្លាយជា admin.users.edit
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');

        // សម្រាប់ Update
        Route::put('/{id}', [UserController::class, 'update'])->name('update');

        // សម្រាប់ Create
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');

        // បន្ថែម Route សម្រាប់ Department Edit ប្រសិនបើអ្នកមានប៊ូតុង Allocate (បន្ទាត់ ១៦៣)
        Route::get('/{id}/department/edit', [UserController::class, 'departmentEdit'])->name('department.edit');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('destroy');
    });



    // Approver routes
    Route::middleware(['auth',EnsureUserIsApprover::class])
        ->prefix('approver')
        ->name('approver.')
        ->group(function () {
            Route::get('/dashboard', [ApproverController::class, 'dashboard'])->name('dashboard');
            Route::post('/leave-requests/{leaveRequest}/approve', [ApproverController::class, 'approveLeave'])->name('leave.approve');
            Route::post('/leave-requests/{leaveRequest}/reject', [ApproverController::class, 'rejectLeave'])->name('leave.reject');
            Route::post('/mission-requests/{missionRequest}/approve', [ApproverController::class, 'approveMission'])->name('mission.approve');
            Route::post('/mission-requests/{missionRequest}/reject', [ApproverController::class, 'rejectMission'])->name('mission.reject');

    });
    Route::middleware(['auth'])->group(function () {

    Route::get('/users/{user}/departments/edit', [UserDepartmentController::class, 'edit'])
        ->name('admin.users.department.edit');

    Route::put('/users/{user}/departments/update', [UserDepartmentController::class, 'update'])
        ->name('admin.users.department.update');



});
// 1. Route to show the checkbox form
Route::get('/admin/users/{id}/department/edit', [UserController::class, 'departmentEdit'])
    ->name('admin.users.department.edit');

// 2. Route to handle the form submission (Saving)
Route::put('/admin/users/{id}/department', [UserController::class, 'departmentUpdate'])
    ->name('users.department.update');
// Check the ->name(...) part here
Route::get('/admin/users', [UserController::class, 'index'])->name('dashboard');
});
