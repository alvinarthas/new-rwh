<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\MenuMapping;
use App\SubModul;
use App\Employee;

class MenuController extends Controller
{
    public function index(){
        $users = Employee::all();
        $page = MenuMapping::getMap(session('user_id'),"MRMM");

        return view('menumapping.index',compact('users','page'));
    }

    public function show($id){
        $currents = json_decode (json_encode(MenuMapping::current($id)),FALSE);
        $rests = json_decode (json_encode(MenuMapping::rest($id)),FALSE);
        return view('menumapping.form',compact('currents','rests','id'));
    }

    public function store(Request $request){
        $checkbox = $request->rest;
        foreach($checkbox as $rest){
            $store = new MenuMapping(array(
                'user_id' => $request->user_id,
                'submapping_id' => $rest
            ));
            $store->save();
        }

        return redirect()->back();
    }

    public function delete(Request $request){
        $checkbox = $request->current;

        foreach($checkbox as $current){
            $store = MenuMapping::where('submapping_id',$current)->where('user_id',$request->user_id)->first();

            $store->delete();
        }

        return redirect()->back();
    }
}
