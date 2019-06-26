<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;   

class TestController extends Controller
{
    public function index(Request $request){
        $keyword = $request->get('search');
        $datas = User::where('name', 'LIKE',$keyword . '%')
            ->paginate();
        $data = collect();
        $i=1;
        foreach ($datas as $key) {
            $memcollect = collect();
            $memcollect->put('no',$i);
            $memcollect->put('ktp',$key->name);
            $memcollect->put('nama',$key->email);
            $data->push($memcollect);
            $i++;
        }
        $data2 = $datas->links();
        $datas->withPath('yourPath');
        $datas->appends($request->all());

        echo "<pre>";
        print_r($datas);die();
        if ($request->ajax()) {
            return response()->json(view('test.list',compact('data','datas','data2'))->render());
        }
        return view('test.index',compact('data', 'keyword','data2'));
    }
}   
