<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Member;

class TestController extends Controller
{
    public function index(){
        $dir = 'D:/DATA/Kerja/RWH/KERAJ/mv/new ktp/ktp';
        if (is_dir($dir)){
            $files = scandir($dir);
            // echo "<pre>";
            // print_r($files);
            $filecount = count($files);
            
            for ($i=0; $i < $filecount ; $i++) { 
                if ($files[$i] != '.' && $files[$i] != '..') {
                    $subdir = $dir."/".$files[$i];
                    $filebaru = $subdir."/".$files[$i].".jpg";
                    $subfiles = array_values(array_diff(scandir($subdir), array('..', '.')));
                    if(is_array($subfiles) && $subfiles <> null){
                        $member = Member::where('ktp',$files[$i])->first();
                        if($member != null){
                            $scanktp = $files[$i].".jpg";
                            $member->scanktp = $scanktp;
                            $member->update();
                        }
                    }
                }
            }
        }
    }

    public function index2(Request $request){
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

    public function index3(){
        $member = Mem2::all();
        foreach ($member as $key) {
            if($key->tgllhr != "-" || $key->tgllhr !="---"){
                $newDate = date("Y-m-d", strtotime($key->tgllhr));
                $key->tgllhr = $newDate;
                $key->update();
            }
            
        }
        
    }
}   
