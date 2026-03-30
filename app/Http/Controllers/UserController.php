<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() {

        /** @var User $user */
        $user = Auth::user();

        $query = User::with('departments')->whereNotIn('role', ['admin', 'ceo', 'cfo', 'hr_manager']);

        if ($user->role === 'admin') {
            $myDeptIds = $user->departments->pluck('id');
            $query->whereHas('departments', function($q) use ($myDeptIds) {
                $q->whereIn('departments.id', $myDeptIds);
            });
        }

        $users = $query->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {

        $departments = Department::all();

        return view('admin.users.create', compact('departments'));
    }
    // Show the form to edit user details
public function edit($id)
{
    $user = User::findOrFail($id);
    return view('admin.users.edit', compact('user'));
}

// Update the user in the database
public function update(Request $request, $id)
{
    // 1. Validate the input
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'department' => 'required'
    ]);

    // 2. Find and Update
    $user = User::findOrFail($id);
    $user->update($validated);

    // 3. Redirect back to the table with a success message
    return redirect()->route('dashboard')->with('success', 'User updated successfully!');
}
public function departmentEdit($id)
{
    // Fetch the user with their current departments
    $user = User::with('departments')->findOrFail($id);

    // Fetch all departments for the checkboxes
    $departments = Department::all();

    return view('admin.users.department_edit', compact('user', 'departments'));
}
public function departmentUpdate(Request $request, $id)
{
    // 1. Validate the input
    $request->validate([
        'department_id' => 'required|exists:departments,id',
    ]);

    // 2. Find the user
    $user = User::findOrFail($id);

    // 3. Sync the department (Many-to-Many)
    // Using sync replaces any old department with the new one
    $user->departments()->sync([$request->department_id]);

    // 4. Redirect back to the list
    return redirect()->route('dashboard')->with('success', 'Department updated!');
}
// Remove the user from the database
public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->back()->with('success', 'User deleted successfully');
}

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'department_id' => 'required|exists:departments,id',
            'role'          => 'required'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
        ]);


        if ($request->department_id) {
            $user->departments()->attach($request->department_id);
        }

        return redirect()->route('dashboard')->with('success', 'User created with department successfully!');
    }

    public function showDepartments($id) {
        return view('admin.users.departments');
    }
}
