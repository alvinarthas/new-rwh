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

        return view('menumapping.index',compact('users'));
    }

    public function show($id){
        $currents = MenuMapping::current($id);
        $rests = MenuMapping::rest($id);

        return view('menumapping.form',compact('currents','rests','id'));
    }

    public function store(Request $request){
        $checkbox = $request->rest;

        foreach($checkbox as $rest){
            $store = new MenuMapping(array(
                'user_id' => $request->user_id,
                'submodul_id' => $rest
            ));

            $store->save();
        }

        return redirect()->back();
    }

    public function delete(Request $request){
        $checkbox = $request->current;

        foreach($checkbox as $current){
            $store = MenuMapping::where('id',$current)->first();

            $store->delete();
        }

        return redirect()->back();
    }
}
