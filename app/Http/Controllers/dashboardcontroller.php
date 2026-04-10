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

        // --- ១. រៀបចំ Query សម្រាប់ User (បង្ហាញក្នុង Table) ---
        $userQuery = User::with(['departments', 'roles']);

        // បើមិនមែន Super Admin ទេ គឺ Filter យកតែអ្នកក្នុង Dept ខ្លួនឯង និងដក Role ធំៗចេញ
        if (!$admin->hasRole('admin') && $adminDeptIds->isNotEmpty()) {
            $userQuery->whereHas('departments', function($q) use ($adminDeptIds) {
                $q->whereIn('departments.id', $adminDeptIds);
            })
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['admin', 'ceo', 'cfo', 'hr_manager', 'team_leader']);
            });
        }

        // ដកខ្លួនឯងចេញពីបញ្ជី
        $userQuery->where('id', '!=', $admin->id);

        // ទាញយកទិន្នន័យ User សម្រាប់បង្ហាញក្នុង Table និងរាប់ចំនួនសម្រាប់ Card
        $allUsers = $userQuery->latest()->get();
        $allUsersCount = $allUsers->count(); // លេខនេះនឹងបង្ហាញ 5 ក្នុង Card ពណ៌ខៀវ

        // --- ២. រៀបចំ Query សម្រាប់ Leave & Mission Requests ---
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

        return view('dashboard', [
            'allUsers'        => $allUsers,
            'allUsersCount'   => $allUsersCount, // ប្រើក្នុង Card: {{ $allUsersCount }}
            'leaveRequests'   => $leaveQuery->get(),
            'missionRequests' => $missionQuery->get(),
        ]);
    }
}
