<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();

        $leaveRequests = LeaveRequest::with(['user.departments'])
            ->when($user instanceof User && ! $user->isApproverOrDepartmentAdmin(), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        return view('leave_requests.index', ['leaveRequests' => $leaveRequests]);
    }
    // ១. បង្ហាញ Form Edit
    public function edit($id)
    {

        
        $leaveRequest = LeaveRequest::findOrFail($id);

    // បើ Status មិនមែនជា pending_tl មិនឱ្យចូលកែទេ
    if ($leaveRequest->status !== 'pending_tl') {
        return redirect()->route('leave-requests.index')
                         ->with('error', 'សំណើនេះត្រូវបាន Approve ឬបញ្ជូនទៅកាន់ HR រួចហើយ មិនអាចកែប្រែបានទេ!');
    }
        return view('leave_requests.edit', compact('leaveRequest'));
    }

    // ២. Update ទិន្នន័យ
    public function update(Request $request, $id)
{
    // ១. ទាញយកទិន្នន័យតាម ID
    $leaveRequest = LeaveRequest::findOrFail($id);
    $leaveRequest->update($request->all());

    // ២. កែប្រែឈ្មោះ User នៅក្នុងតារាង Users (ដាក់នៅត្រង់នេះ)
    // យើងប្រើ $leaveRequest->user ដើម្បីទៅកាន់តារាង User ដែលពាក់ព័ន្ធ
    if ($request->has('user_name')) {
        $leaveRequest->user->update([
            'name' => $request->user_name
        ]);
    }
    $request->validate([
    'start_date' => 'required',
    'end_date'   => 'required',
    'reason'     => 'required',
]);
    // ៣. Update ទិន្នន័យសំណើច្បាប់ (Leave Request)
    $leaveRequest->update([
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
        'reason'    => $request->reason, // បន្ថែម reason ប្រសិនបើមានក្នុង Form
    ]);

    // ៤. Redirect ទៅកាន់ទំព័របញ្ជីឈ្មោះ
    return redirect()->route('leave-requests.index')->with('success', 'កែប្រែទិន្នន័យ និងឈ្មោះអ្នកប្រើប្រាស់បានជោគជ័យ!');
}
    // ៣. លុបទិន្នន័យ
    public function destroy($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->delete();

        return redirect()->back()->with('success', 'លុបបានជោគជ័យ!');
    }
    public function create()
{
    // ទាញយកតែ User ណាដែលមិនមែនជា Admin, CEO, HR, Team Leader, និង Dept Admin
    $users = User::whereNotIn('role', [
        'admin',
        'ceo',
        'hr_manager',
        'team_leader',
        'department_admin',
        'cfo'
    ])->get();

    return view('leave_requests.create', compact('users'));
}

    public function store(Request $request)
    {
        $rules = [
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ];

        /** @var User|null $user */
        $user = Auth::user();
        $userId = Auth::id();

        if ($user instanceof User && $user->isDepartmentAdmin()) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        if ($user instanceof User && $user->isDepartmentAdmin()) {
            $userId = $request->input('user_id');
        }

        LeaveRequest::create([
            'user_id' => $userId,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending_tl',
        ]);

        return redirect()->route('dashboard')->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! ($user instanceof User) || ($leaveRequest->user_id !== $user->id && ! $user->isApproverOrDepartmentAdmin())) {
            abort(403);
        }

        return view('leave_requests.show', ['leaveRequest' => $leaveRequest]);
    }
}
