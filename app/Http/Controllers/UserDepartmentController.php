<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class UserDepartmentController extends Controller
{
    public function edit(User $user)
    {
        $departments = Department::all();
        return view('admin.users.department', compact('user', 'departments'));
    }
    public function update(Request $request, User $user)
    {
        $departments = $request->input('departments', []);
        $user->departments()->sync($request->departments);

        return redirect()->route('dashboard')->with('success', 'User departments updated successfully!');
    }
}

