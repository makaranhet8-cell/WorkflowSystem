<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LeaveRequestController extends Controller implements HasMiddleware
{
    /**
     * កំណត់ Middleware ដើម្បីឆែក Permission (Fix Error 403)
     */
    public static function middleware(): array
    {
        return [

            new Middleware('permission:view leaverequests', only: ['index', 'show']),
            new Middleware('permission:create leaverequests', only: ['create', 'store']),
            new Middleware('permission:edit requests', only: ['edit', 'update']),
            new Middleware('permission:delete requests', only: ['destroy']),
    ];
    }

    public function index()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    // ចាប់ផ្ដើម Query ជាមួយ Eager Loading ដើម្បីឱ្យដើរលឿន
    $query = LeaveRequest::with(['user.departments'])->latest();

    // ១. បើជា Admin ធំ (Super Admin) គឺមិនបាច់ Filter ទេ ឱ្យឃើញទាំងអស់
    if ($user->hasRole('admin')) {
        // Do nothing, see all
    }

    // ២. បើជា Admin តាមផ្នែក (IT, Sale) ឬថ្នាក់ដឹកនាំផ្សេងៗ
    // បន្ថែម 'admin_it' និង 'admin_sale' ទៅក្នុងបញ្ជីនេះ
    elseif ($user->hasAnyRole(['admin_it', 'admin_sale', 'department_admin', 'team_leader', 'hr_manager', 'cfo'])) {

        $adminDeptIds = $user->departments->pluck('id')->toArray();

        $query->whereHas('user.departments', function($q) use ($adminDeptIds) {
            $q->whereIn('departments.id', $adminDeptIds);
        });
    }

    // ៣. បើជាបុគ្គលិកធម្មតា ឱ្យឃើញតែសំណើរបស់ខ្លួនឯងប៉ុណ្ណោះ
    else {
        $query->where('user_id', $user->id);
    }

    $leaveRequests = $query->get();

    return view('leave_requests.index', compact('leaveRequests'));
}

    /**
     * បង្ហាញ Form បង្កើតសំណើ (Fix cite: image_990ccc, image_99d3a2)
     */
    public function create()
    {
        // ទាញយក staff ដើម្បីជ្រើសរើសក្នុង dropdown (បើជា Admin បង្កើតឱ្យ staff)
        $users = User::role('user')->get();
        return view('leave_requests.create', compact('users'));
    }

    public function store(Request $request)
{
    $request->validate([
        'user_id'    => 'required|exists:users,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date'   => 'required|date|after_or_equal:start_date',
        'reason'     => 'required|string|max:500',
    ]);

    LeaveRequest::create([
        'user_id'    => $request->user_id,
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
        'reason'     => $request->reason,
        'status'     => 'pending_tl',
    ]);

    return redirect()->route('leave-requests.index')->with('success', 'សំណើច្បាប់ត្រូវបានបញ្ជូនជោគជ័យ។');
}

    public function edit($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        // អនុញ្ញាតឱ្យកែតែសំណើដែលកំពុង Pending
        if ($leaveRequest->status !== 'pending_tl') {
            return redirect()->route('leave-requests.index')->with('error', 'មិនអាចកែប្រែបានទេ ព្រោះសំណើត្រូវបានពិនិត្យរួចហើយ!');
        }

        return view('leave_requests.edit', compact('leaveRequest'));
    }

   public function update(Request $request, $id)
{
    $leaveRequest = LeaveRequest::findOrFail($id);

    // Update ឈ្មោះក្នុង Table Users
    $user = $leaveRequest->user;
    $user->update([
        'name' => $request->user_name // ចាប់យកពី name="user_name" ក្នុង Form
    ]);

    // Update ទិន្នន័យច្បាប់
    $leaveRequest->update([
        'reason' => $request->reason,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
    ]);

    return redirect()->route('leave-requests.index')->with('success', 'កែប្រែបានជោគជ័យ!');
}
   public function destroy($id)
{
    $leaveRequest = LeaveRequest::findOrFail($id);
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if ($user->hasAnyRole(['admin', 'system_admin'])) {
        $leaveRequest->delete();
        return redirect()->back()->with('success', 'Admin បានលុបសំណើដោយជោគជ័យ។');
    }


    if ($leaveRequest->user_id === $user->id && $leaveRequest->status === 'pending_tl') {
        $leaveRequest->delete();
        return redirect()->back()->with('success', 'អ្នកបានលុបសំណើរបស់អ្នកជោគជ័យ។');
    }

    return redirect()->back()->with('error', 'អ្នកមិនមានសិទ្ធិលុបសំណើដែលបាន Approve រួចហើយនោះទេ។');
}

    public function show(LeaveRequest $leaveRequest)
{

    $leaveRequest->load(['user.departments']);

    return view('leave_requests.show', compact('leaveRequest'));
}
}
