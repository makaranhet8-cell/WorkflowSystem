<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:create user', only: ['create', 'store','index', 'show']),
            new Middleware('permission:edit requests', only: ['edit', 'update']),
            new Middleware('permission:delete requests', only: ['destroy']),
        ];
    }
    public function index() {
    /** @var User $authUser */
    $authUser = Auth::user();
    $query = User::with(['departments', 'roles']);
    if ($authUser->hasAnyRole(['admin_it', 'admin_sale']) && !$authUser->hasRole('admin')) {
        $myDeptIds = $authUser->departments->pluck('id')->toArray();
        $query->whereHas('departments', function($q) use ($myDeptIds) {
            $q->whereIn('departments.id', $myDeptIds);
        });
        $query->whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['admin', 'ceo', 'cfo', 'hr_manager', 'team_leader','admin_sale']);
        });
    }
    $leaveRequests = \App\Models\LeaveRequest::all();
    $missionRequests = \App\Models\MissionRequest::all();
    $allUsers = $query->get();
    return view('dashboard', compact('allUsers', 'leaveRequests', 'missionRequests'));
}
    public function create()
    {
        $departments = Department::all();

        $roles = \Spatie\Permission\Models\Role::all();
        return view('admin.users.create', compact('departments', 'roles'));
    }
    public function store(Request $request)
{
    $request->validate([
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email',
        'password'      => 'required|min:6',
        'role'          => 'required|exists:roles,name',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    $imagePath = null;
    if ($request->hasFile('profile_image')) {

        $imagePath = $request->file('profile_image')->store('profiles', 'public');
    }
    $user = User::create([
        'name'          => $request->name,
        'email'         => $request->email,
        'password'      => Hash::make($request->password),
        'profile_image' => $imagePath,
    ]);
    try {
        $user->assignRole($request->role);
    } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
        return back()->withErrors(['role' => 'Role "' . $request->role . '" Not found in system.']);
    }
    if ($request->filled('department_id')) {
        $user->departments()->sync($request->department_id);
    }
    return redirect()->route('dashboard')->with('success', 'User created successfully!');
}
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $user = User::findOrFail($id);
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $user->profile_image = $request->file('profile_image')->store('profiles', 'public');
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        if ($request->role) {
            $user->syncRoles($request->role);
        }
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }
    public function departmentEdit($id)
    {
        $user = User::with('departments')->findOrFail($id);
        $allDepartments = Department::all();
        return view('admin.users.department_edit', compact('user', 'allDepartments'));
    }
    public function departmentUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $departmentIds = $request->input('department_ids', []);
        $user->departments()->sync($departmentIds);
        return redirect()->route('dashboard')->with('success', 'បានដាក់បញ្ជូលផ្នែកដោយជោគជ័យ');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }
}
