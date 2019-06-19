<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Employee;

class HomeController extends Controller
{
    public function index(Request $request){
        if ($request->session()->has('isLoggedIn')) {
            return view('home.home');
        }else{
            return view('login.login');
        }
    }

    public function index2(Request $request){
        if ($request->session()->has('isLoggedIn')) {
            return view('home.home');
        }else{
            return view('login.login');
        }
    }

    public function login(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        
        // IF Validation fail
        if ($validator->fails()) {

            return redirect()->back();

        }else{
            $user = Employee::where('username',$request->username)->first();

            // FOUND
            if($user && Hash::check($request->password, $user->password)){
                $request->session()->put('username', $request->username);
                $request->session()->put('user_id', $user->id);
                $request->session()->put('foto', $user->scanfoto);
                $request->session()->put('isLoggedIn', 'Ya');

                return redirect()->route('getHome');
            
            // NOT FOUND 
            }else{
                return redirect()->back();
            }
        }
        
    }

    public function logout(Request $request){
        $request->session()->flush();

        return redirect()->route('getHome');
    }
}
