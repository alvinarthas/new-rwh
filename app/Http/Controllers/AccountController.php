<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Handler;

use App\Employee;

class AccountController extends Controller
{
    public function getchange_foto(){
        $user = Employee::where('id',session('user_id'))->first();
        return view('account.change_foto',compact('user'));
    }

    public function getchange_pass(){

        return view('account.change_pass');
    }

    public function change_foto(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'scanfoto' => 'required|file',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $user = Employee::where('id',session('user_id'))->first();

            if($user){
                // Upload Foto
                if($request->scanfoto <> NULL|| $request->scanfoto <> ''){

                    if (file_exists(public_path('assets/images/employee/foto/').$user->scanfoto)) {
                        unlink(public_path('assets/images/employee/foto/').$user->scanfoto);
                    }

                    $scanfoto = $user->nip.'.'.$request->scanfoto->getClientOriginalExtension();
                    $request->scanfoto->move(public_path('assets/images/employee/foto/'),$scanfoto);

                    $user->scanfoto = $scanfoto;
                    $user->save();
                }
            }
            return redirect()->back()->with('status', 'Foto berhasil dirubah');;
        }
    }

    public function change_pass(Request $request){
        // Validate 
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $user = Employee::where('id',session('user_id'))->first();

            $user->password = Hash::make($request->password);
            $user->bck_pass = $request->password;

            $user->save();

            return redirect()->back()->with('status', 'Password berhasil dirubah');
        }
    }

    public function profile(){
        $user = Employee::where('id',session('user_id'))->first();

        return view('account.profile',compact('user'));
    }
}
