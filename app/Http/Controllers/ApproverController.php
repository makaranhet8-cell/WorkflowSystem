<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\MissionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ApproverController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // new Middleware('permission:view requests', only: ['dashboard']),

            new Middleware('permission:approve requests', only: [
                'approveLeave', 'approveMission', 'rejectLeave', 'rejectMission','dashboard'
            ]),
        ];
    }

    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        $leaveQuery = LeaveRequest::with('user.departments')->latest();
        $missionQuery = MissionRequest::with('user.departments')->latest();
        $approverDepts = $user->departments->pluck('name');

        if ($user->hasRole('admin')) {
            $pendingLeaveRequests = $leaveQuery->whereNotIn('status', ['approved', 'rejected'])->get();
            $pendingMissionRequests = $missionQuery->whereNotIn('status', ['approved', 'rejected'])->get();
        }
        elseif ($user->hasRole('team_leader')) {
            $pendingLeaveRequests = (clone $leaveQuery)->where('status', 'pending_tl')
                ->whereHas('user.departments', fn($q) => $q->whereIn('name', $approverDepts))->get();

            $pendingMissionRequests = (clone $missionQuery)->where('status', 'pending_tl')
                ->whereHas('user.departments', fn($q) => $q->whereIn('name', $approverDepts))->get();
        }
        elseif ($user->hasRole('cfo')) {
            $pendingLeaveRequests = (clone $leaveQuery)->where('status', 'pending_cfo')
                ->whereHas('user.departments', fn($q) => $q->where('name', 'Sales Department'))->get();

            $pendingMissionRequests = (clone $missionQuery)->where('status', 'pending_cfo')
                ->whereHas('user.departments', fn($q) => $q->where('name', 'Sales Department'))->get();
        }
        elseif ($user->hasRole('hr_manager')) {
            $pendingLeaveRequests = (clone $leaveQuery)->where('status', 'pending_hr')->get();
            $pendingMissionRequests = (clone $missionQuery)->where('status', 'pending_hr')->get();
        }
        elseif ($user->hasRole('ceo')) {
            $pendingLeaveRequests = collect();
            $pendingMissionRequests = (clone $missionQuery)->where('status', 'pending_ceo')->get();
        }
        else {
            $pendingLeaveRequests = collect();
            $pendingMissionRequests = collect();
        }

        return view('approver.dashboard', compact('pendingLeaveRequests', 'pendingMissionRequests'));
    }


    public function approveLeave(LeaveRequest $leaveRequest)
    {
        /** @var User $user */
        $user = Auth::user();
        $leaveRequest->load('user.departments');
        $requester = $leaveRequest->user;

        if ($user->hasRole('admin')) {
            $leaveRequest->update(['status' => 'approved']);
            return back()->with('success', 'អនុម័តដោយ Admin');
        }

        $isIT = $requester?->departments->contains('name', 'IT Department');
        $isSales = $requester?->departments->contains('name', 'Sales Department');

        if ($user->hasRole('team_leader') && $leaveRequest->status === 'pending_tl') {
            $nextStatus = $isIT ? 'pending_hr' : ($isSales ? 'pending_cfo' : 'approved');
            $leaveRequest->update(['status' => $nextStatus]);
        }
        elseif ($user->hasRole('cfo') && $leaveRequest->status === 'pending_cfo' && $isSales) {
            $leaveRequest->update(['status' => 'pending_hr']);
        }
        elseif ($user->hasRole('hr_manager') && $leaveRequest->status === 'pending_hr') {
            $leaveRequest->update(['status' => 'approved']);
        }
        else {
            return back()->with('error', 'អ្នកមិនមានសិទ្ធិអនុម័តនៅដំណាក់កាលនេះទេ។');
        }
        return back()->with('success', 'សំណើត្រូវបានអនុម័ត');
    }


    public function approveMission(MissionRequest $missionRequest)
{
    /** @var User $user */
    $user = Auth::user();
    $missionRequest->load('user.departments');
    $requester = $missionRequest->user;


    $isSales = $requester?->departments->contains('name', 'Sales Department');
    $isIT    = $requester?->departments->contains('name', 'IT Department');


    if ($user->hasRole('admin')) {
        $missionRequest->update(['status' => 'approved']);
        return back()->with('success', 'អនុម័តដោយ Admin រួចរាល់');
    }


    if ($user->hasRole('team_leader') && $missionRequest->status === 'pending_tl') {


    if ($isSales) {
        $nextStatus = 'pending_cfo';
    } elseif ($isIT) {
        $nextStatus = 'pending_ceo';
    } else {
        $nextStatus = 'pending_hr';
    }

    $missionRequest->update(['status' => $nextStatus]);


    $target = str_replace('pending_', '', $nextStatus);
    return back()->with('success', "Team Leader បានអនុម័ត និងបញ្ជូនទៅកាន់ " . strtoupper($target));
}

    // ៣. Logic សម្រាប់ CFO
    elseif ($user->hasRole('cfo') && $missionRequest->status === 'pending_cfo' && $isSales) {
        $missionRequest->update(['status' => 'pending_hr']);
        return back()->with('success', 'CFO បានអនុម័ត និងបញ្ជូនទៅ HR');
    }

    // ៤. Logic សម្រាប់ HR Manager
    elseif ($user->hasRole('hr_manager') && $missionRequest->status === 'pending_hr') {
        $missionRequest->update(['status' => 'pending_ceo']);
        return back()->with('success', 'HR បានអនុម័ត និងបញ្ជូនទៅ CEO');
    }

    // ៥. Logic សម្រាប់ CEO
    elseif ($user->hasRole('ceo') && $missionRequest->status === 'pending_ceo') {
        $missionRequest->update(['status' => 'approved']);
        return back()->with('success', 'CEO បានអនុម័តសំណើទាំងស្រុង');
    }

    return back()->with('error', 'អ្នកមិនមានសិទ្ធិអនុម័តក្នុងដំណាក់កាលនេះទេ ឬសំណើស្ថិតក្នុងស្ថានភាពមិនត្រឹមត្រូវ។');
}


    public function rejectLeave(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'comment' => 'nullable|string|max:255'
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'admin_comment' => $request->comment
        ]);

        return back()->with('error', 'សំណើច្បាប់សម្រាកត្រូវបានបដិសេធ។');
    }

    public function rejectMission(Request $request, MissionRequest $missionRequest)
    {
        $request->validate([
            'comment' => 'nullable|string|max:255'
        ]);

        $missionRequest->update([
            'status' => 'rejected',
            'admin_comment' => $request->comment
        ]);

        return back()->with('error', 'សំណើបេសកកម្មត្រូវបានបដិសេធ។');
    }
}
