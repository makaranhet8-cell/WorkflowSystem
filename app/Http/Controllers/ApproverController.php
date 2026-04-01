<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\MissionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Http\Request;

class ApproverController extends Controller
{

    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();


        $leaveQuery = LeaveRequest::with('user.departments');
        $missionQuery = MissionRequest::with('user.departments');


        $approverDepts = $user->departments->pluck('name');
        ///

        if ($user->hasRole('system_admin')) {
    // បង្ហាញតែសំណើណាដែលមិនទាន់ Approved និងមិនទាន់ Rejected
    $pendingLeaveRequests = $leaveQuery->whereNotIn('status', ['approved', 'rejected'])->get();
    $pendingMissionRequests = $missionQuery->whereNotIn('status', ['approved', 'rejected'])->get();

    return view('approver.dashboard', compact('pendingLeaveRequests', 'pendingMissionRequests'));
}


        if ($user->hasRole('team_leader')) {
            $pendingLeaveRequests = $leaveQuery->where('status', 'pending_tl')
                ->whereHas('user.departments', function($q) use ($approverDepts) {
                    $q->whereIn('name', $approverDepts);
                })->get();

            $pendingMissionRequests = $missionQuery->where('status', 'pending_tl')
                ->whereHas('user.departments', function($q) use ($approverDepts) {
                    $q->whereIn('name', $approverDepts);
                })->get();
        }
        elseif ($user->hasRole('cfo')) {
            // CFO មើលឃើញតែសំណើពី Sales Dept និង Status pending_cfo
            $pendingLeaveRequests = $leaveQuery->where('status', 'pending_cfo')
                ->whereHas('user.departments', function($q) {
                    $q->where('name', 'Sales Department');
                })->get();

            $pendingMissionRequests = $missionQuery->where('status', 'pending_cfo')
                ->whereHas('user.departments', function($q) {
                    $q->where('name', 'Sales Department');
                })->get();
        }
        elseif ($user->hasRole('hr_manager')) {
            // HR មើលឃើញសំណើដែលមកដល់ដៃខ្លួន (pending_hr) ពីគ្រប់ Dept
            $pendingLeaveRequests = $leaveQuery->where('status', 'pending_hr')->get();
            $pendingMissionRequests = $missionQuery->where('status', 'pending_hr')->get();
        }
        elseif ($user->hasRole('ceo')) {
            // CEO មើលឃើញសំណើបេសកកម្មដែលមកដល់ដៃខ្លួន (pending_ceo)
            $pendingLeaveRequests = collect();
            $pendingMissionRequests = $missionQuery->where('status', 'pending_ceo')->get();
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
        $requester = $leaveRequest->user;
        ///
        if ($user->hasRole('system_admin')) {
        $leaveRequest->update(['status' => 'approved']);
        return back()->with('success', 'អនុម័តដោយ System Admin រួចរាល់');
    }

        $isIT = $requester?->departments->contains('name', 'IT Department') ?? false;
        $isSales = $requester?->departments->contains('name', 'Sales Department') ?? false;


        if ($user->hasRole('team_leader') && $leaveRequest->status === 'pending_tl') {
            if ($isIT) {
                $leaveRequest->update(['status' => 'pending_hr']); // IT: TL -> HR
            } elseif ($isSales) {
                $leaveRequest->update(['status' => 'pending_cfo']); // Sales: TL -> CFO
            }
            return back()->with('success', 'បញ្ជូនទៅដំណាក់កាលបន្ទាប់រួចរាល់');
        }


        if ($user->hasRole('cfo') && $leaveRequest->status === 'pending_cfo' && $isSales) {
            $leaveRequest->update(['status' => 'pending_hr']); // CFO -> HR
            return back()->with('success', 'CFO បានអនុម័ត');
        }


        if ($user->hasRole('hr_manager') && $leaveRequest->status === 'pending_hr') {
            $leaveRequest->update(['status' => 'approved']);
            return back()->with('success', 'អនុម័តជាស្ថាពរដោយ HR');
        }

        return back()->with('error', 'អ្នកមិនមានសិទ្ធិអនុម័តសំណើនេះទេ។');
    }
    public function rejectLeave(LeaveRequest $leaveRequest)
{
    $leaveRequest->update(['status' => 'rejected']);
    return back()->with('success', 'សំណើច្បាប់ត្រូវបានបដិសេធ។');
}

    public function approveMission(MissionRequest $missionRequest)
{
    /** @var User $user */
    $user = Auth::user();
    $missionRequest->load('user.departments');
    $requester = $missionRequest->user;
    $isSales = $requester?->departments->contains('name', 'Sales Department') ?? false;
    ///


    if ($user->hasRole('system_admin')) {
        $missionRequest->update(['status' => 'approved']);
        return back()->with('success', 'បេសកកម្មត្រូវបានអនុម័តដោយ System Admin');
    }


    if ($user->hasRole('team_leader') && $missionRequest->status === 'pending_tl') {
        if ($isSales) {
            // Sales follows: TL -> CFO -> HR -> CEO
            $missionRequest->update(['status' => 'pending_cfo']);
        } else {
            // IT follows: TL -> CEO (FIXED: jump straight to CEO)
            $missionRequest->update(['status' => 'pending_ceo']);
        }
        return back()->with('success', 'TL បានអនុម័ត');
    }


    if ($user->hasRole('cfo') && $missionRequest->status === 'pending_cfo' && $isSales) {
        $missionRequest->update(['status' => 'pending_hr']);
        return back()->with('success', 'CFO បានអនុម័ត');
    }


    if ($user->hasRole('hr_manager') && $missionRequest->status === 'pending_hr' && $isSales) {
        $missionRequest->update(['status' => 'pending_ceo']);
        return back()->with('success', 'HR បានអនុម័ត');
    }


    if ($user->hasRole('ceo') && $missionRequest->status === 'pending_ceo') {
        $missionRequest->update(['status' => 'approved']);
        return back()->with('success', 'CEO បានអនុម័តជាស្ថាពរ');
    }

    return back()->with('error', 'មិនអាចអនុម័តបានទេ។');

}
public function rejectMission(MissionRequest $missionRequest)
{
    $missionRequest->update(['status' => 'rejected']);
    return back()->with('success', 'សំណើបេសកកម្មត្រូវបានបដិសេធ។');
}

}
