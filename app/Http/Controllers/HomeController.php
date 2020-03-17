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

            $tasks = Task::all();
            $data = array();

            foreach($tasks as $t){
                $taskemployee = TaskEmployee::where('task_id', $t->id)->get();
                $read = 0;
                $reader = "Sudah : ";
                $notyet_read ="Belum : ";
                $status = 0;
                $count_read = 0;
                $count_status = 0;
                $already = "Sudah : ";
                $notyet_done = "Belum : ";

                foreach($taskemployee as $te){
                    if($te->read == 1){
                        $read += 1;
                        $reader .= $te->employee->name.", ";
                    }else{
                        $notyet_read .= $te->employee->name.", ";
                    }

                    if($te->status == 1){
                        $status += 1;
                        $already .= $te->employee->name.", ";
                    }else{
                        $notyet_done .= $te->employee->name.", ";
                    }
                }

                $count = TaskEmployee::where('task_id', $t->id)->count();

                if($read == $count){
                    $count_read = "text-success";
                }else{
                    $count_read = "text-danger";
                }

                if($status == $count){
                    $count_status = "text-success";
                }else{
                    $count_status = "text-danger";
                }

                $task = array(
                    'id'          => $t->id,
                    'title'       => $t->title,
                    'description' => $t->description,
                    'created_at'  => $t->created_at,
                    'due_date'    => $t->due_date,
                    'creator'     => Employee::where('id', $t->creator)->first()->name,
                    'read'        => $read,
                    'count_read'  => $count_read,
                    'reader'      => $reader,
                    'notyet_read' => $notyet_read,
                    'status'      => $status,
                    'count_status'=> $count_status,
                    'already'     => $already,
                    'notyet_done' => $notyet_done,
                );
                array_push($data, $task);
            }

            if(session('role') == 'Direktur Utama'){
                // Get Sisa Hutang Piutang
                $hutang = Perusahaan::sisaHutang();
                $piutang = Customer::sisaPiutang();
            }elseif(session('role') == 'Superadmin'){
                // Get Poin Sementara
                $bonus = Salary::currentBonus(session('user_id'));
                // Get Sisa Hutang Piutang
                $hutang = Perusahaan::sisaHutang();
                $piutang = Customer::sisaPiutang();
            }else{
                // Get Poin Sementara
                $hutang = 0;
                $piutang = 0;
                $bonus = Salary::currentBonus(session('user_id'));
            }

            // die("asu");
            return view('welcome.welcome',compact('user','bonus','hutang','piutang', 'data'));
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
