<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;

use App\MenuMapping;
use App\SubModul;
use App\Employee;
use App\PurchaseMap;

class MenuController extends Controller
{

    // Menu Mapping
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

    // Purchase Mapping

    public function PurMapIndex(){
        $users = Employee::all();
        $page = MenuMapping::getMap(session('user_id'),"MRPM");

        return view('purchase.map.index',compact('users','page'));
    }

    public function PurMapShow($id){
        $currents = PurchaseMap::where('employee_id',$id)->get();
        $rests = DB::select("SELECT id,nama FROM tblperusahaan WHERE id NOT IN (SELECT supplier_id FROM map_purchase WHERE employee_id = $id)");
        $rests = json_decode (json_encode($rests),FALSE);
        return view('purchase.map.form',compact('currents','rests','id'));
    }

    public function PurMapStore(Request $request){
        $checkbox = $request->rest;
        foreach($checkbox as $rest){
            $store = new PurchaseMap(array(
                'employee_id' => $request->user_id,
                'supplier_id' => $rest
            ));
            $store->save();
        }

        return redirect()->back();
    }

    public function PurMapDelete(Request $request){
        $checkbox = $request->current;

        foreach($checkbox as $current){
            $store = PurchaseMap::where('supplier_id',$current)->where('employee_id',$request->user_id)->first();

            $store->delete();
        }

        return redirect()->back();
    }
}
