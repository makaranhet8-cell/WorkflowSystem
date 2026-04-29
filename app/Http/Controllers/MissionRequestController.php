<?php
namespace App\Http\Controllers;
use App\Models\MissionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class MissionRequestController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view missionrequests', only: ['index', 'show']),
            new Middleware('permission:create missionrequests', only: ['create', 'store']),
            new Middleware('permission:edit requests', only: ['edit', 'update']),
            new Middleware('permission:delete requests', only: ['destroy']),
        ];
    }
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $query = MissionRequest::with('user.departments')->latest();
        if ($user->hasAnyRole(['system_admin', 'admin'])) {
        }
        elseif ($user->hasAnyRole(['admin_it', 'admin_sale', 'department_admin', 'team_leader','ceo','cfo','hr_manager'])) {
            $adminDeptIds = $user->departments->pluck('id')->toArray();

            $query->whereHas('user.departments', function($q) use ($adminDeptIds) {
                $q->whereIn('departments.id', $adminDeptIds);
            });
        }
        else {
            $query->where('user_id', $user->id);
        }
        $missionRequests = $query->get();
        return view('mission_requests.index', compact('missionRequests'));
    }
    public function create()
{
    /** @var User $user */
    $user = Auth::user();

    if ($user->hasAnyRole(['system_admin', 'admin','admin_it','admin_sale',])) {
        $users = User::role('user')->get();
    } else {
        $users = collect();
    }
    return view('mission_requests.create', compact('users'));
}
public function store(Request $request)
{
    /** @var User $user */
    $user = Auth::user();

    $rules = [
        'destination' => 'required|string|max:255',
        'purpose'     => 'required|string|max:500',
        'start_date'  => 'required|date',
        'end_date'    => 'required|date|after_or_equal:start_date',
    ];
    if ($user->hasAnyRole(['admin','admin_it','admin_sale'])) {
        $rules['user_id'] = 'required|exists:users,id';
        $request->validate($rules);
        $userId = $request->input('user_id');
    } else {
        $request->validate($rules);
        $userId = Auth::id();
    }
    MissionRequest::create([
        'user_id'     => $userId,
        'destination' => $request->destination,
        'purpose'     => $request->purpose,
        'start_date'  => $request->start_date,
        'end_date'    => $request->end_date,
        'status'      => 'pending_tl',
    ]);
    return redirect()->route('mission-requests.index')->with('success', 'Request created successfully.');
}
    public function show( int $id)
{
    $missionRequest = MissionRequest::findOrFail($id);
    return view('mission_requests.show', compact('missionRequest'));
}
public function destroy( int $id)
{
    $missionRequest = MissionRequest::findOrFail($id);
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if ($user->hasAnyRole(['admin', 'system_admin'])) {
        $missionRequest->delete();
        return redirect()->back()->with('success', 'Admin has deleted the request successfully.');
    }
    if ($missionRequest->user_id === $user->id && $missionRequest->status === 'pending_tl') {
        $missionRequest->delete();
        return redirect()->back()->with('success', 'You have deleted your request successfully.');
    }
    return redirect()->back()->with('error', 'You do not have permission to delete this request.');
}
}
