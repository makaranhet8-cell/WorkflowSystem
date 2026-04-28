<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class RoleController extends Controller
{
    public function index(Request $request) {
    $search = $request->input('search');
    $roles = Role::where(function($query) use ($search) {
            if ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            }
        })
        ->paginate(5)
        ->withQueryString();
    return view('roles.list', compact('roles'));
}
    public function create() {
        $permissions = Permission::get();
        return view('roles.create', compact('permissions'));
    }
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3'
        ]);
        if ($validator->fails()) {
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
        $role = Role::create(['name' => $request->name]);
        if (!empty($request->permission)) {
            $role->syncPermissions($request->permission);
        }
        return redirect()->route('roles.index')->with('success', 'Role added successfully');
    }
    public function edit($id) {
        $role = Role::findOrFail($id);
        $permissions = Permission::orderBy('name', 'ASC')->get();
        $hasPermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'hasPermissions' => $hasPermissions
        ]);
    }
    public function update($id, Request $request) {
        $role = Role::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:roles,name,' . $id . ',id'
        ]);
        if ($validator->fails()) {
            return redirect()->route('roles.edit', $id)->withInput()->withErrors($validator);
        }
        $role->name = $request->name;
        $role->save();
        if ($request->has('permission')) {
            $role->syncPermissions($request->permission);
        } else {
            $role->syncPermissions([]);
        }
        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }
    public function destroy($id) {
        $role = Role::find($id);
        if ($role == null) {
            return redirect()->route('roles.index')->with('error', 'Role not found');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
