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
    $query = LeaveRequest::with(['user.departments'])->latest();
    if ($user->hasRole('admin')) {
    }
    elseif ($user->hasAnyRole(['admin_it', 'admin_sale', 'department_admin', 'team_leader', 'hr_manager', 'cfo'])) {
        $adminDeptIds = $user->departments->pluck('id')->toArray();
        $query->whereHas('user.departments', function($q) use ($adminDeptIds) {
            $q->whereIn('departments.id', $adminDeptIds);
        });
    }
    else {
        $query->where('user_id', $user->id);
    }
    $leaveRequests = $query->get();
    return view('leave_requests.index', compact('leaveRequests'));
}
    public function create()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if ($user->hasAnyRole(['admin','admin_it','admin_sale'])) {
        $users = User::role('user')->get();
    } else {
        $users = collect();
    }
    return view('leave_requests.create', compact('users'));
}
public function store(Request $request)
{
    /** @var \App\Models\User $authUser */
    $authUser = Auth::user();
    if (!$authUser->hasAnyRole(['admin','admin_it','admin_sale'])) {
        $request->merge(['user_id' => $authUser->id]);
    }
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
    return redirect()->route('leave-requests.index')->with('success', 'Request created successfully.');
}
    public function edit($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        if ($leaveRequest->status !== 'pending_tl') {
            return redirect()->route('leave-requests.index')->with('error', 'This request cannot be modified because it has already been reviewed!');
        }
        return view('leave_requests.edit', compact('leaveRequest'));
    }
   public function update(Request $request, $id)
{
    $leaveRequest = LeaveRequest::findOrFail($id);
    $user = $leaveRequest->user;
    $user->update([
        'name' => $request->user_name,
    ]);
    $leaveRequest->update([
        'reason' => $request->reason,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
    ]);
    return redirect()->route('leave-requests.index')->with('success', 'Request updated successfully!');
}
   public function destroy($id)
{
    $leaveRequest = LeaveRequest::findOrFail($id);
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if ($user->hasAnyRole(['admin', 'system_admin'])) {
        $leaveRequest->delete();
        return redirect()->back()->with('success', 'Admin has deleted the request successfully.');
    }
    if ($leaveRequest->user_id === $user->id && $leaveRequest->status === 'pending_tl') {
        $leaveRequest->delete();
        return redirect()->back()->with('success', 'You have deleted your request successfully.');
    }
    return redirect()->back()->with('error', 'You do not have permission to delete this request.');
}
    public function show(LeaveRequest $leaveRequest)
{
    $leaveRequest->load(['user.departments']);
    return view('leave_requests.show', compact('leaveRequest'));
}
}

