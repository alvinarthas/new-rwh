<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DemoDevice;
use App\DemoFinger;
use App\DemoLog;
use App\DemoUser;

class AbsensiController extends Controller
{
    public function register(){
        $users = DemoUser::all();

        return view('absensi.register',compact('users'));
    }

    public function kehadiran(){
        $users = DemoUser::all();
        return view('absensi.kehadiran',compact('users'));
    }

    public function log(){
        return view('absensi.log');
    }
}
