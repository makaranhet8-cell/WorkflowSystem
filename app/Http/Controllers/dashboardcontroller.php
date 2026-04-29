<?php
namespace App\Http\Controllers;
use App\Models\LeaveRequest;
use App\Models\MissionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view dashboard', only: ['index']),
            new Middleware('permission:create user', only: ['create', 'store']),
            new Middleware('permission:create leaverequests', only: ['create', 'store']),
            new Middleware('permission:edit requests', only: ['edit', 'update']),
            new Middleware('permission:delete requests', only: ['destroy']),
        ];
    }
    public function index()
    {
        /** @var User $admin */
        $admin = Auth::user();
        $adminDeptIds = $admin->departments->pluck('id');
        $userQuery = User::with(['departments', 'roles']);
        if (!$admin->hasRole('admin') && $adminDeptIds->isNotEmpty()) {
            $userQuery->whereHas('departments', function($q) use ($adminDeptIds) {
                $q->whereIn('departments.id', $adminDeptIds);
            })
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['admin', 'ceo', 'cfo', 'hr_manager', 'team_leader']);
            });
        }
        $userQuery->where('id', '!=', $admin->id);
        $allUsers = $userQuery->latest()->get();
        $allUsersCount = $allUsers->count();
        $leaveQuery = LeaveRequest::with('user.departments')->latest();
        $missionQuery = MissionRequest::with('user.departments')->latest();
        if (!$admin->hasRole('admin') && $adminDeptIds->isNotEmpty()) {
            $leaveQuery->whereHas('user.departments', function($q) use ($adminDeptIds) {
                $q->whereIn('departments.id', $adminDeptIds);
            });
            $missionQuery->whereHas('user.departments', function($q) use ($adminDeptIds) {
                $q->whereIn('departments.id', $adminDeptIds);
            });
        }
        dump($allUsers, $leaveQuery->get(), $missionQuery->get());
        return view('dashboard', [
            'allUsers'        => $allUsers,
            'allUsersCount'   => $allUsersCount,
            'leaveRequests'   => $leaveQuery->get(),
            'missionRequests' => $missionQuery->get(),
        ]);
    }   
}
