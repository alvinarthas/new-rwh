<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\RoleMapping;
use App\Role;
use App\Employee;

class RoleMappingController extends Controller
{
    public function index(){
        $users = Employee::all();
        return view('rolemapping.index',compact('users'));
    }

    public function edit($id){
        $user = RoleMapping::where('username',$id)->first();
        $roles = Role::all();
        if($user){
            return view('rolemapping.form',compact('roles','user','id'));
        }else{
            return view('rolemapping.form',compact('roles','id'));
        }
        
    }

    public function update(Request $request, $id){
        $mapping = RoleMapping::where('username',$id)->first();
        if($mapping){
            $mapping->role_id = $request->role_id;
        }else{
            $mapping = new RoleMapping(array(
                'username' => $id,
                'company_id' => 1,
                'role_id' => $request->role_id,
            ));
        }

        $mapping->save();

        return redirect()->route('getRoleMapping');
    }

    public function destroy($id){
        $user = RoleMapping::where('username',$id)->first();

        $user->delete();

        return redirect()->back();
    
    }
}
