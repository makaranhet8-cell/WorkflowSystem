<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\MissionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::user();

        $userQuery = User::query();

        if ($admin->role === 'admin') {
            $adminDeptIds = $admin->departments->pluck('id');
            $userQuery->whereHas('departments', function($q) use ($adminDeptIds) {
                $q->whereIn('departments.id', $adminDeptIds);
            });
        }

        $recentUsers = $userQuery->whereNotIn('role', ['admin', 'ceo', 'cfo', 'hr_manager'])
                                 ->latest()
                                 ->take(5)
                                 ->get();

        $leaveQuery = LeaveRequest::with('user.departments');
        $missionQuery = MissionRequest::with('user.departments');

        if ($admin->role === 'admin') {
            $adminDeptIds = $admin->departments->pluck('id');

            $leaveQuery->whereHas('user.departments', function($q) use ($adminDeptIds) {
                $q->whereIn('departments.id', $adminDeptIds);
            });

            $missionQuery->whereHas('user.departments', function($q) use ($adminDeptIds) {
                $q->whereIn('departments.id', $adminDeptIds);
            });
        }

        $leaveRequests = $leaveQuery->get();
        $missionRequests = $missionQuery->get();

        return view('dashboard', [
            'recentUsers' => $recentUsers,
            'leaveRequests' => $leaveRequests,
            'missionRequests' => $missionRequests,
            'allUsersCount' => $recentUsers->count(), // សម្រាប់បង្ហាញក្នុង Box ពណ៌បៃតង
        ]);
    }
}
