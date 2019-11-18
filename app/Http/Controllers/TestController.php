<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\User;
use App\BankMember;
use App\Product;
use App\PriceDet;
use App\Jurnal;
use App\Salary;
use App\Employee;
use App\Sales;

class TestController extends Controller
{
    public function index(){
        dd(session('role'));
        $sales = Sales::all();
        $collect = collect();
        foreach($sales as $sale){
            $sale->put('status',1);
            // $colect = collect();
            // $colect->push($sale);
            // $colect->put('status',1);
            // $collect->push($colect);
            dd($sale);
        }
        dd($collect);
    }
    public function indexxxxx(){
        $collection = collect([
            ['id' => 1, 'value' => 10],
            ['id' => 2, 'value' => 20],
            ['id' => 3, 'value' => 100],
            ['id' => 4, 'value' => 250],
            ['id' => 5, 'value' => 150],
        ]);
        $sorted = $collection->sortByDesc('value');
        // 5.1
        dd($sorted->values()->first());
    }

    public function indexs(){
        echo $test = Hash::make("canik123");
        // foreach(PriceDet::groupBy('prod_id')->distinct()->get() as $key){
        //     $product = Product::where('prod_id',$key->prod_id)->first();
        //     if($product){
        //         echo $key->prod_id." ADA <br>";
        //     }else{
        //         PriceDet::where('prod_id',$key->prod_id)->delete();
        //         echo $key->prod_id." Ga Ada <br>";
        //     }
        // }
    }

    public function index5(){
        $dir = 'D:/DATA/Kerja/RWH/KERAJ/mv/atm';
        if (is_dir($dir)){
            $files = scandir($dir);
            // echo "<pre>";
            // print_r($files);
            $filecount = count($files);
            
            for ($i=0; $i < $filecount ; $i++) { 
                if ($files[$i] != '.' && $files[$i] != '..') {
                    $subdir = $dir."/".$files[$i];
                    // $filebaru = $subdir."/".$files[$i].".jpg";
                    $subfiles = array_values(array_diff(scandir($subdir), array('..', '.')));
                    echo "<pre>";
                    print_r($subfiles);
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

    public function index6(){
        $bankmember = BankMember::all();

        foreach ($bankmember as $key) {
            $dir = 'D:/DATA/Kerja/RWH/KERAJ/mv/tabungan/'.str_replace(' ', '', $key->ktp).'/'.str_replace(' ', '', $key->norek);

            if(!is_dir($dir)){
            }else{
                $filebaru = $dir."/".str_replace(' ', '', $key->norek).".jpg";
                $subfiles = array_values(array_diff(scandir($dir), array('..', '.')));
                if(is_array($subfiles) && $subfiles <> null){
                    // rename($dir."/".$subfiles[0],$filebaru);
                    echo $subfiles[0]."<br>";
                    // echo "<pre>";
                    // print_r($subfiles);
                    // $atm = BankMember::where('ktp',str_replace(' ', '', $key->ktp))->first();
                    // $atm->scantabungan = str_replace(' ', '', $key->norek).".jpg";
                    // $atm->update();
                }
                
                // echo "<pre>";
                // print_r($subfiles);
                // echo "enggak<br>";
            }
        }
    }

    public function index4(){
        foreach(BM2::all() as $key){
            $member = Member::where('ktp',$key->ktp)->first();
            if($member){
                echo $member->ktp."<br>";
            }else{
                echo "Ga ada Cuk <br>";
            }
            echo "----------- <br>";
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

    public function indexaa(){
        // foreach(ManageHarga2::groupBy('prod_id')->distinct()->get() as $key){
        //     $product = Product::where('prod_id',$key->prod_id)->first();
        //     if($product){
        //         echo $key->prod_id." ADA <br>";
        //     }else{
        //         ManageHarga2::where('prod_id',$key->prod_id)->delete();
        //         echo $key->prod_id." Ga Ada <br>";
        //     }
        // }
        $encrypted = Crypt::encryptString('Belajar Laravel Di malasngoding.com');
		$decrypted = Crypt::decryptString('$2y$10$Rchoh5O7de3roYe84yGfweyAQFkMHm3SYrevYfBk/oBXzV7A4P4p2');
 
		echo "Hasil Enkripsi : " . $encrypted;
		echo "<br/>";
		echo "<br/>";
		echo "Hasil Dekripsi : " . $decrypted;
    }
}   
