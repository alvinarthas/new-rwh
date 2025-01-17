<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Role;
use App\MenuMapping;
use App\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        $page = MenuMapping::getMap(session('user_id'),"EMRO");
        return view('role.index',compact('roles','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        return view('role.form',compact('jenis'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'role_name' => 'required',
            'gaji_pokok' => 'required|integer',
            'tunjangan_jabatan' => 'required|integer',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $role = new Role(array(
                // Informasi Pribadi
                'role_name' => $request->role_name,
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan_jabatan' => $request->tunjangan_jabatan,
                'company_id' => 1,
                'creator' => session('user_id'),
            ));
            try {
                $role->save();
                Log::setLog('EMROC','Create Role:'.$request->role_name);
                return redirect()->route('role.index')->with('status','Role berhasil ditambahkan');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::where('id',$id)->first();
        $jenis = "edit";
        return view('role.form',compact('jenis','role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'role_name' => 'required',
            'gaji_pokok' => 'required|integer',
            'tunjangan_jabatan' => 'required|integer',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $role = Role::where('id',$id)->first();

            $role->role_name = $request->role_name;
            $role->gaji_pokok = $request->gaji_pokok;
            $role->tunjangan_jabatan = $request->tunjangan_jabatan;
            $role->creator = session('user_id');

            try {
                $role->save();
                Log::setLog('EMROU','Update Role:'.$request->role_name);
                return redirect()->route('role.index')->with('status','Role berhasil diubah');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
            $role->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            Role::where('id',$id)->delete();
            Log::setLog('EMROD','Delete Role:'.$id);
            return "true";
        // fail
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->errorInfo);
        }
    }
}
