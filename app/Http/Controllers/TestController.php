<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\User;
use App\BankMember;
use App\BankMember_copy;
use App\PerusahaanMember_copy;
use App\Member_copy;
use App\Product;
use App\PriceDet;
use App\Jurnal;
use App\Salary;
use App\Employee;
use App\Sales;
use App\Koordinator;
use App\Perusahaan;
use App\SubKoordinator;

use Carbon\Carbon;

class TestController extends Controller
{

    public function indexfd(){
        dd(Employee::select('id','username')->join('tblemployeerole as er','er.username','=','tblemployee.username')->join('tblrole as r','r.id','er.role_id')->where('r.role_name','LIKE','Staff%')->select('tblemployee.id','tblemployee.username')->get());
    }

    public function indextime(){
        $date = '2019-11-17';
        $date = Carbon::createFromFormat('Y-m-d H:i:s',$date.'00:00:00');
        echo $date."<br>";
        $today = Carbon::now();
        echo $today."<br>";
        
        $interval = date_diff($today, $date);
        $selisih = intval($interval->format('%R%a'));
        echo $interval->format('%R%a days')."<br>";
        echo $selisih."<br>";

        if($interval->days > 2){
            echo "lebih";
        }else{
            echo "masih bisa";
        }
        
    }

    public function indexwq(){
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

    public function indexktp(){
        // ktp
        $dir = 'D:/DATA/Kerja/RWH/KERAJ/mv/atm';
        if (is_dir($dir)){
            $files = scandir($dir);
            $filecount = count($files);
            for ($i=0; $i < $filecount ; $i++) { 
                if ($files[$i] != '.' && $files[$i] != '..') {
                    $subdir = $dir."/".$files[$i];
                    // $filebaru = $subdir."/".$files[$i].".jpg";
                    $subfiles = array_values(array_diff(scandir($subdir), array('..', '.')));
                    if(is_array($subfiles) && $subfiles <> null){
                        
                        // echo "<pre>";
                        // print_r($subdir."/".$subfiles[0]);
                        $old = $subdir."/".$subfiles[0];
                        $new = $subdir."/".$files[$i].".jpg";
                        // echo "<pre>";
                        // print_r($old);
                        // echo "<pre>";
                        // print_r($new);
                        $member = Member_copy::where('ktp',$files[$i])->first();
                        if($member){
                            rename($old,$new);
                            $member->scanktp = $files[$i].".jpg";
                            $member->save();
                        //     // $files_next = scandir($subdir);
                        //     echo $files[$i]." Ada<br>";
                        }else{
                        //     echo $files[$i]." Tidak Ada<br>";
                        }
                        // $member = Member::where('ktp',$files[$i])->first();
                        // if($member != null){
                        //     $scanktp = $files[$i].".jpg";
                        //     $member->scanktp = $scanktp;
                        //     $member->update();
                        // }
                    }
                }
            }
            die();
        }
    }

    public function indexz(){
        $dir = 'D:/DATA/Kerja/RWH/KERAJ/mv/ktp';
        if (is_dir($dir)){
            $files = scandir($dir);
            $filecount = count($files);

            for ($i=0; $i < $filecount ; $i++) {
                if ($files[$i] != '.' && $files[$i] != '..') {
                    $subdir = $dir."/".$files[$i];
                    $member = Member_copy::where('ktp',$files[$i])->first();
                    if($member){
                        $files_next = scandir($subdir);
                        echo "<pre>";
                        print_r($files_next);
                    }else{
                        echo "Ga ada Cuk <br>";
                    }
                    echo "----------- <br>";
                }
            }
            die();
        }
    }

    public function indexd(){
        // buat ganti koor dan sub koor
        $perusahaans = Perusahaan::all();
        foreach($perusahaans as $per){
            echo "<pre>";
            print_r($per->nama);
        print_r(DB::table('perusahaanmember_copy')->where('perusahaan_id',$per->nama)->get());
            // dd(DB::table('tbl_member_copy_copy1')->get());
            // $do = DB::Raw("UPDATE tblmember_copy_copy1 SET subkoor = $koor->id WHERE subkoor = '$koor->nama'");per
            // DB::table('perusahaanmember_copy')->where('perusahaan_id',$per->nama)->update(['perusahaan_id' => $per->id]);
        }
    }

    public function index(){
        $bankmember = BankMember_copy::all();

        foreach ($bankmember as $key) {
            $dir = 'D:/DATA/Kerja/RWH/KERAJ/mv/tabungan/'.str_replace(' ', '', $key->ktp).'/'.str_replace(' ', '', $key->norek);

            if(!is_dir($dir)){
            }else{
                
                $filebaru = $dir."/".str_replace(' ', '', $key->norek).".jpg";
                $subfiles = array_values(array_diff(scandir($dir), array('..', '.')));
                if(is_array($subfiles) && $subfiles <> null){
                    // echo "<pre>";
                    // print_r($subfiles);
                    rename($dir."/".$subfiles[0],$filebaru);
                    // echo $subfiles[0]."<br>";
                    // echo "<pre>";
                    // print_r($subfiles);
                    // $atm = BankMember::where('ktp',str_replace(' ', '', $key->ktp))->first();
                    $key->scantabungan = str_replace(' ', '', $key->norek).".jpg";
                    $key->update();
                }
                
                // echo "<pre>";
                // print_r($subfiles);
                // echo "enggak<br>";
            }
        }
    }

    public function indexpm(){
        foreach(PerusahaanMember_copy::all() as $key){
            $member = Member_copy::where('ktp',$key->ktp)->first();
            if($member){
                echo $member->ktp."<br>";
            }else{
                // $key->delete();
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
        $member = Member_copy::all();
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
