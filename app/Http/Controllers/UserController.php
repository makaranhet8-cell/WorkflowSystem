<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
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

        // ១. យកតែអ្នកក្នុង Department ខ្លួនឯង
        $query->whereHas('departments', function($q) use ($myDeptIds) {
            $q->whereIn('departments.id', $myDeptIds);
        });

        // ២. កាត់ Role ធំៗចេញ (CEO, Admin...) ដើម្បីឱ្យសល់តែ ៥ នាក់
        $query->whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['admin', 'ceo', 'cfo', 'hr_manager', 'team_leader','admin_sale']);
        });
    }
    $leaveRequests = \App\Models\LeaveRequest::all();
    $missionRequests = \App\Models\MissionRequest::all();
    $allUsers = $query->get();

    // ... code ផ្សេងៗទៀត ...
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
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role'     => 'required|exists:roles,name',
    ]);


    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);

    try {
        $user->assignRole($request->role);
    } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
        return back()->withErrors(['role' => 'Role "' . $request->role . '" មិនមានក្នុងប្រព័ន្ធទេ។']);
    }

    if ($request->filled('department_id')) {
        $user->departments()->sync($request->department_id);
    }

    return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
}

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);
        $user->update($validated);

        return redirect()->route('dashboard')->with('success', 'User updated successfully!');
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
