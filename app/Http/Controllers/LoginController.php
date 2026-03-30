<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $role = 'user';
        if ($request->email === 'admin@example.com') {
            $role = 'approver';
        } elseif ($request->email === 'deptadmin@example.com') {
            $role = 'admin';
        }

        $user = User::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name ?: 'Demo User', 'password' => Hash::make($request->password), 'role' => $role]
        );

        if (in_array($request->email, ['admin@example.com', 'deptadmin@example.com'], true) && $user->role !== $role) {
            $user->role = $role;
            $user->save();
        }

        if (!($request->name) && $user->name !== $request->name) {
            $user->name = $request->name;
            $user->save();
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }
    public function register(Request $request)
{


    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);

    return redirect('/dashboard')->with('success', 'ចុះឈ្មោះជោគជ័យ!');
}

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
