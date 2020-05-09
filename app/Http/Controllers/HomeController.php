<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Employee;
use App\Salary;
use App\Perusahaan;
use App\Customer;
use App\Task;
use App\TaskEmployee;

class HomeController extends Controller
{
    public function index(Request $request){
        if ($request->session()->has('isLoggedIn')) {
            // Get User Profile
            $user = Employee::where('id',session('user_id'))->first();

            $page = "dashboard";
            $data = Task::getTask($page);

            $birth = Customer::getBirthday();
            if(session('role') == 'Direktur Utama'){
                // Get Sisa Hutang Piutang
                $hutang = Perusahaan::sisaHutang();
                $piutang = Customer::sisaPiutang();
                // Deposit
                $deppurchase = Perusahaan::getDeposit();
                $depsales = Customer::getDeposit();
            }elseif(session('role') == 'Superadmin'){
                // Get Poin Sementara
                $bonus = Salary::currentBonus(session('user_id'));
                // Get Sisa Hutang Piutang
                $hutang = Perusahaan::sisaHutang();
                $piutang = Customer::sisaPiutang();
                // Deposit
                $deppurchase = Perusahaan::getDeposit();
                $depsales = Customer::getDeposit();
            }else{
                // Get Poin Sementara
                $hutang = 0;
                $piutang = 0;
                $deppurchase = 0;
                $depsales = 0;
                $bonus = Salary::currentBonus(session('user_id'));
            }

            // die("asu");
            return view('welcome.welcome',compact('user','bonus','hutang','piutang','data','deppurchase','depsales','birth'));
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

                if(empty($user->rolemapping()->first())){
                    $request->session()->put('role',"null");
                }else{
                    $request->session()->put('role', $user->rolemapping()->first()->role()->first()->role_name);
                }
                $request->session()->put('name', $user->name);
                $request->session()->put('user_id', $user->id);
                $request->session()->put('nip', $user->nip);
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
