<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
{
    // ១. Validate (កុំដាក់ department ព្រោះក្នុង Blade យើងបាន comment ចោល)
    $validated = $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
    ]);

    // ២. Update ទិន្នន័យ
    $user = User::findOrFail($id);
    $user->update($validated);

    // ៣. ប្តូរការ Redirect (សំខាន់បំផុតគឺពាក្យ return)
    // សាកល្បង redirect ទៅ 'admin.users.index' វិញព្រោះវាជាទំព័រក្នុងរូបភាពរបស់អ្នក
    return redirect()->route('dashboard')->with('success', 'User updated successfully!');
}
    public function departmentEdit($id)
{
    $user = User::with('departments')->findOrFail($id);

    // បន្ថែមជួរនេះ ដើម្បីទាញយក Department ទាំងអស់ពី Database
    $allDepartments = Department::all();

    return view('admin.users.department_edit', compact('user', 'allDepartments'));
}

    public function departmentUpdate(Request $request, $id)
{
    $user = User::findOrFail($id);

    // ទទួលយក Array នៃ IDs (ឧទាហរណ៍៖ [1, 2])
    $departmentIds = $request->input('department_ids', []);

    // រក្សាទុកទិន្នន័យទៅក្នុង Table department_user
    $user->departments()->sync($departmentIds);

    return redirect()->route('dashboard')->with('success', 'បានដាក់បញ្ជូលផ្នែកដោយជោគជ័យ');
}

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
            'password' => Hash::make($request->password), // ប្រើ Hash::make ជំនួស bcrypt (Standard ជាង)
            'role'     => $request->role,
        ]);

        if ($request->department_id) {
            $user->departments()->attach($request->department_id);
        }

        return redirect()->route('dashboard')->with('success', 'User created successfully!');
    }
}
