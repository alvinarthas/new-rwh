<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DemoDevice;
use App\DemoFinger;
use App\DemoLog;
use App\DemoUser;
use App\MenuMapping;

class AbsensiController extends Controller
{
    public function register(){
        $users = DemoUser::all();

        return view('absensi.register',compact('users'));
    }

    public function kehadiran(){
        $users = DemoUser::all();
        $page = MenuMapping::getMap(session('user_id'),"ABKE");
        $page2 = MenuMapping::getMap(session('user_id'),"ABLO");
        return view('absensi.kehadiran',compact('users','page','page2'));
    }

    public function log(){
        return view('absensi.log');
    }
}
