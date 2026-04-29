<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
class PermissionController extends Controller
{
    public function index(Request $request) {
        $search = $request->input('search');
        $permissions = Permission::where(function($query) use ($search) {
                if ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                }
            })
            ->paginate(10)
            ->withQueryString();
        return view('permission.list', compact('permissions'));
}    public function create(){
        return view('permission.create');
    }
    public function store(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|unique:permissions|min:3'
    ]);
    if ($validator->passes()) {
        Permission::create(['name'=> $request->name]);
        return redirect()->route('permissions.index')->with('success','permission add Successfuly     ');
    }else{
        return redirect()->route('permissions.create')->withInput()->withErrors($validator);
    }
    }
    public function edit( int $id){
        $permissions = Permission::findOrfail($id);
        return view('permission.edit',['permission'=>$permissions]);
    }
    public function update( int $id, Request $request){
    $permissions = Permission::findOrfail($id);
    $validator = Validator::make($request->all(), [
        'name' => 'required|min:3|unique:permissions,name,'.$id.',id'
    ]);
    if ($validator->passes()) {
        $permissions->name = $request->name;
        $permissions->save();
        return redirect()->route('permissions.index')->with('success','permission add Successfully');
    }else{
        return redirect()->route('permissions.edit',$id)->withInput()->withErrors($validator);
    }
    }
  public function destroy( int $id)
{
    $permission = \Spatie\Permission\Models\Permission::find($id);
    if ($permission == null) {
        return redirect()->route('permissions.index')->with('error', 'Permission not found');
    }
    $permission->delete();
    return redirect()->route('permissions.index')->with('success', 'Permission Deleted Successfully');
}
}
