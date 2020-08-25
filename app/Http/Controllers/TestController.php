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
use App\PerusahaanMember;
use App\Member;
use App\Product;
use App\Coa;
use App\Jurnal;
use App\Salary;
use App\SalaryDet;
use App\Employee;
use App\Sales;
use App\Koordinator;
use App\Perusahaan;
use App\SubKoordinator;
use App\PurchaseDetail;
use App\Purchase;
use App\Customer;
use App\SalesDet;
use App\DeliveryOrder;
use App\DeliveryDetail;
use App\Role;
use App\ReceiveDet;
use App\Bonus;
use App\BonusBayar;
use App\TopUpBonus;
use GuzzleHttp\Client;
use App\Ecommerce;
use App\Transaksi;

use Carbon\Carbon;
use Excel;
use App\Exports\DuplicateMemberExport;

class TestController extends Controller
{
    public function baruu(){
        echo "TILIK kucing";
    }

    public function index(){
        $jurnal = Jurnal::whereBetween('date', ['2020-05-01', '2020-08-11'])->orderBy('date', 'asc')->get();
        $no = 1;
        foreach($jurnal as $jn){
            $debet = Jurnal::where('id_jurnal', $jn->id_jurnal)->where('AccPos', 'Debet')->sum('Amount');
            $credit = Jurnal::where('id_jurnal', $jn->id_jurnal)->where('AccPos', 'Credit')->sum('Amount');

            if($debet != $credit){
                if($debet == 0 || $credit == 0){
                    // echo $no++.". ".$jn->date." - ".$jn->id_jurnal.", Debet:".$debet.", Credit:".$credit."<br>";
                    Jurnal::where('id_jurnal', $jn->id_jurnal)->delete();
                }else{
                    echo $no++.". ".$jn->date." - ".$jn->id_jurnal.", Debet:".$debet.", Credit:".$credit."<br>";

                }
            }
        }
    }

    public function indexdeletetopup(){
        $count = 0;
        $countjurnal = 0;
        $topup = TopUpBonus::where('AccNo', "1.1.1.2.2.000003")->where('tgl', "2020-08-06")->get();
        foreach($topup as $t){
            $jurnal = Jurnal::where('id_jurnal', $t->id_jurnal)->delete();
            $t->delete();
        }
    }

    public function indexexcel(){
        ini_set('max_execution_time', 3000);
        $datas = array();

        $no = 1;
        $perusahaanmember = PerusahaanMember::groupBy('noid')->get();
        foreach($perusahaanmember as $pm){
            $count = PerusahaanMember::where('noid', $pm->noid)->count();
            if($count > 1){
                $data = PerusahaanMember::join('tblmember', 'perusahaanmember.ktp', 'tblmember.ktp')->where('noid', $pm->noid)->select('perusahaanmember.noid', 'tblmember.ktp', 'perusahaanmember.perusahaan_id', 'tblmember.nama', 'tblmember.member_id')->get();
                // $print = $no++.". noid : ".$pm->noid.", count : ".$count." | ";
                foreach($data as $key){
                    $array = array(
                        'No' => $no++,
                        'No ID / No Rek' => $pm->noid,
                        'Perusahaan / Bank' => $key->perusahaan->nama,
                        'Nama' => $key->nama,
                        'ID Member' => $key->member_id,
                        'No KTP' => $key->ktp,
                    );
                    array_push($datas, $array);
                    // $print .= $key->ktp." - ".$key->nama.", ";
                }
                // echo $print."<br><br>";
            }
        }

        $bankmember = BankMember::groupBy('norek')->get();
        foreach($bankmember as $bm){
            $count = BankMember::where('norek', $bm->norek)->count();
            if($count > 1){
                $data = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->where('norek', $bm->norek)->get();
                // $print = $nomor++.". norek : ".$bm->norek.", count : ".$count." | ";
                foreach($data as $key){
                    $array = array(
                        'No' => $no++,
                        'No ID / No Rek' => $bm->norek,
                        'Perusahaan / Bank' => $key->bank->nama,
                        'Nama' => $key->nama,
                        'ID Member' => $key->member_id,
                        'No KTP' => $key->ktp,
                    );
                    array_push($datas, $array);
                    // $print .= $key->ktp." - ".$key->nama.", ";
                }
                // echo $print."<br><br>";
            }
        }
        $export = new DuplicateMemberExport($datas);
        return Excel::download($export, 'Duplicate NOID NoRek.xlsx');
    }
    public function indexduplicatenoidnorek(){
        $no = 1;
        $perusahaanmember = PerusahaanMember::groupBy('noid')->get();
        foreach($perusahaanmember as $pm){
            $count = PerusahaanMember::where('noid', $pm->noid)->count();
            if($count > 1){
                $data = PerusahaanMember::join('tblmember', 'perusahaanmember.ktp', 'tblmember.ktp')->where('noid', $pm->noid)->get();
                $print = $no++.". noid : ".$pm->noid.", count : ".$count." | ";
                foreach($data as $key){
                    $print .= $key->ktp." - ".$key->nama.", ";
                }
                echo $print."<br><br>";
            }
        }

        $nomor = 1;
        $bankmember = BankMember::groupBy('norek')->get();
        foreach($bankmember as $bm){
            $count = BankMember::where('norek', $bm->norek)->count();
            if($count > 1){
                $data = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->where('norek', $bm->norek)->get();
                $print = $nomor++.". norek : ".$bm->norek.", count : ".$count." | ";
                foreach($data as $key){
                    $print .= $key->ktp." - ".$key->nama.", ";
                }
                echo $print."<br><br>";
            }
        }
    }

    public function indexcheckstatusrek(){
        $bankmember = BankMember::all();

        $countAktif=0;
        $countTidak=0;
        $countElse=0;

        foreach($bankmember as $bm){
            if($bm->status == "Aktif"){
                // $bm->status = 1;
                // $bm->save();
                $countAktif++;
            }elseif($bm->status == "Tidak Aktif"){
                // $bm->status = 2;
                // $bm->save();
                $countTidak++;
            }else{
                // $bm->status = 3;
                // $bm->save();
                $countElse++;
            }
        }
        echo $countAktif."<br>";
        echo $countTidak."<br>";
        echo $countElse."<br>";
    }

    public function indexcheckduplicateid(){
        $bonus = Bonus::all();
        $no = 1;
        foreach($bonus as $b){
            $count_noid = PerusahaanMember::where('noid', $b->noid)->count();
            if($count_noid == 0){
                echo $no++.". ".$b->noid." (noid tidak ditemukan) (".$b->id_jurnal.")<br>";
            }elseif($count_noid > 1){
                echo $no++.". ".$b->noid." (noid ditemukan ".$count_noid.") (".$b->id_jurnal.")<br>";
            }
        }

        $bonusbayar = BonusBayar::all();
        $nomor = 1;
        foreach($bonusbayar as $bb){
            $count_norek = BankMember::where('norek', $bb->no_rek)->count();
            if($count_norek == 0){
                echo $nomor++.". ".$bb->no_rek." (norek tidak ditemukan) (".$bb->id_jurnal.")<br>";
            }elseif($count_norek > 1){
                echo $nomor++.". ".$bb->no_rek." (norek ditemukan ".$count_norek.") (".$bb->id_jurnal.")<br>";
            }
        }
    }

    public function indextime(){

        $time_start = microtime(true);
        $jurnal = Jurnal::all();
        // $member = Member::select('nama', 'ktp')->limit(30);
        // $pm = 0;
        // $bm = 0;
        $count = 0;
        foreach($jurnal as $jur){
            $count++;
            echo $jur->id."<br>";
            if($count == 100){
            break;
            }
        }

        // foreach($member as $m){
        //     $perusahaanmember = PerusahaanMember::where('ktp', $m->ktp)->count();
        //     $bankmember = BankMember::where('ktp', $m->ktp)->count();

        //     if($perusahaanmember == 0 AND $bankmember > 0){
        //         $pm++;
        //     }elseif($perusahaanmember > 0 AND $bankmember == 0){
        //         $bm++;
        //     }
        // }
        // echo "tanpa NOID tapi ada rekening : ".$pm."<br>";
        // echo "tanpa rekening tapi ada NOID : ".$bm."<br>";
        $time_end = microtime(true);

        // //dividing with 60 will give the execution time in minutes otherwise seconds
        $execution_time = ($time_end - $time_start)/60;

        // //execution time of the script
        echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
        die();
    }

    public function indexcheckbonussalahrek(){
        $nomor = 1;
        $bonusbayar = BonusBayar::where('id_jurnal', 'BB.228')->get();
        foreach($bonusbayar as $key){
            $bm = Bankmember::where('norek', $key->no_rek)->count();
            if($bm == 0){
                // echo $nomor++.". ".$key->no_rek." - Rp ".number_format($key->bonus, 2, ",", ".")."<br>";
                $key->no_rek = '0'.$key->no_rek;
                $key->save();
            }
        }
    }

    // querying from eloquent relationship
    public function indextai(Request $request){
        $keyword = $request->keyword;
        $tempCount = 0;

        // GET Char of TRX ID
        $charID = preg_replace("/[^a-zA-Z]/", "", $keyword);
        $numID = preg_replace('/[^0-9]/', '', $keyword);

        // Sales Initialization
        $sales = Sales::select('id','trx_date','creator','ttl_harga','customer_id','ongkir','approve','method','online_id', DB::raw('SUM(ttl_harga+ongkir) as ttl_trx'));

        // or Search Based on Total Transaction
        if (is_numeric($numID) && $charID==''){
            $tempSales = $sales->replicate();
            $tempSales->push();
            $tempSales = $tempSales->groupBy('id')->orHavingRaw('SUM(ttl_harga+ongkir) = ?',[$numID]);
            $tempCount = $tempSales->count();
            echo $tempCount;
        }

        if ($tempCount > 0){
            $sales = $tempSales;
        }else{
            dd($sales->limit(10)->toSql());
            // Search Based on Customer Name
            $sales = $sales->orWhereHas('customer', function ($query) use ($keyword) {
                $query->where('apname', 'like', $keyword.'%');
            });
            // or Search Based on Creator Name
            $sales = $sales->orWhereHas('creator', function ($query) use ($keyword) {
                $query->where('name', 'like', $keyword.'%');
            });

            // or Search Based on TRX DATE
            $sales = $sales->orWhere('trx_date','like',$keyword.'%');

            // or Search Based on TRX ID
                // Check in TOKO ONLINE
                if (is_numeric($numID) && $charID!=''){
                    $ecom = Ecommerce::select('id','kode_trx')->where('kode_trx','like',$charID.'%');

                    if ($ecom->count() > 0) {
                        $ecom = $ecom->first();

                        $sales = $sales->where('method',$ecom->id)->orWhere('online_id',$numID);
                    }
                }

            $sales = $sales->orWhere('id',$numID);
        }

        dd($sales->limit(10)->toSql());
        // dd($sales->groupBy('id')->orderBy('ttl_trx','desc')->limit(10)->get());
    }
    // Check Missing Receive Detail
    public function indexmiss(Request $request){
        // foreach (ReceiveDet::select('id','purchasedetail_id')->get() as $key) {
        //     $checkPodet = PurchaseDetail::where('id',$key->purchasedetail_id)->count();

        //     if ($checkPodet == 0){
        //         echo "RP.".$key->id." PODET.".$key->purchasedetail_id."<br>";
        //     }
        // }
        Product::getGudang($request->prod_id);
    }
    // Check ReceiveDet Null
    public function indexSSSSS(){
        foreach (ReceiveDet::whereNull('purchasedetail_id')->get() as $key) {
            echo "RD.".$key->id." Product: ".$key->prod_id."<br>";
            // Get Purchase Detail
            $purchase_detail = PurchaseDetail::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->first();
            echo "PD.".$purchase_detail->id."<br>";
            $key->purchasedetail_id = $purchase_detail->id;
            $key->update();
        }
    }
    // Check sisa Toko Online Belom DO
    public function indexToko(){
        foreach (Sales::where('method','!=','0')->where('approve',1)->get() as $key) {
            $checkDO = DeliveryOrder::where('sales_id',$key->id)->count();

            if ($checkDO == 0){
                echo $key->jurnal_id."<br>";
                DeliveryOrder::autoDO($key->id,$key->trx_date,$key->creator);
            }
        }
    }
    // Missing DO
    public function indexMissingSODET(){
        foreach(DeliveryDetail::groupBy('product_id')->select('product_id')->get() as $product){
            foreach(DeliveryDetail::where('product_id',$product->product_id)->get() as $detail){
                $checkSO = SalesDet::where('trx_id',$detail->sales_id)->where('prod_id',$product->product_id)->count();

                if($checkSO == 0){
                    echo "DD.".$detail->id." Check SO: ".$checkSO." Product: ".$product->product_id."<br>";
                    $detail->delete();
                }
            }
        }
    }

    public function indexRP(){
            foreach(ReceiveDet::groupBy('prod_id')->select('prod_id')->get() as $product){
                    foreach(ReceiveDet::where('prod_id',$product->prod_id)->get() as $detail){
                        $checkPO = PurchaseDetail::where('prod_id',$detail->prod_id)->where('trx_id',$detail->trx_id)->count();
                $jurnal = Jurnal::where('id_jurnal', 'BP.%')->orWhere('id_jurnal', 'BB.%')->orWhere('id_jurnal', 'BT.%')->get();
                foreach($jurnal as $key){
                    $debet = Jurnal::where('id_jurnal', $key->id_jurnal)->where('AccPos', 'Debet')->sum('Amount');
                    $credit = Jurnal::where('id_jurnal', $key->id_jurnal)->where('AccPos', 'Credit')->sum('Amount');

                    if($debet != $credit){
                        echo $key->id_jurnal." - Debet:".$debet.", Credit:".$credit."<br>";
                    }
                }
            }
        }
    }

    public function indexCheckBonustoEmptyIDMember(){
        $rekening = BankMember::all();
        $no = 1;
        foreach($rekening as $rek){
            $count = BankMember::where('norek', $rek->norek)->count();
            if($count>1){
                $bonusa = BonusBayar::where('no_rek', $rek->norek)->get();
                foreach($bonusa as $a){
                    echo $no++.". ".$a->no_rek." - ".$a->id_jurnal." - ".Member::where('ktp', $rek->ktp)->first()->nama."<br>";
                }
                echo "<br>";

                $bonusb = TopUpBonus::where('no_rek', $rek->norek)->get();
                foreach($bonusb as $b){
                    echo $no++.". ".$b->no_rek." - ".$b->id_jurnal." - ".Member::where('ktp', $rek->ktp)->first()->nama."<br>";
                }
                echo "<br>";
            }
        }
        echo "<br>";

        $permem = PerusahaanMember::all();
        $no = 1;
        foreach($permem as $pm){
            $count = PerusahaanMember::where('noid', $pm->noid)->count();
            if($count>1){
                $bonus = Bonus::where('noid', $pm->noid)->get();
                foreach($bonus as $b){
                    echo $no++.". ".$b->noid." - ".$b->id_jurnal." - ".Member::where('ktp', $pm->ktp)->first()->nama."<br>";
                }
                echo "<br>";
            }
        }
        echo "<br>";
    }

    public function indexCheckDuplicateNorekNoid(){
        $rekening = BankMember::all();
        $no = 1;
        foreach($rekening as $rek){
            $count = BankMember::where('norek', $rek->norek)->count();
            if($count>1){
                echo $no++.". ".$rek->ktp." - ".$rek->norek." - ".Member::where('ktp', $rek->ktp)->first()->nama."<br>";
            }
        }
        echo "<br>";

        $permem = PerusahaanMember::all();
        $no = 1;
        foreach($permem as $pm){
            $count = PerusahaanMember::where('noid', $pm->noid)->count();
            if($count>1){
                echo $no++.". ".$pm->ktp." - ".$pm->noid." - ".Member::where('ktp', $pm->ktp)->first()->nama."<br>";
            }
        }
        echo "<br>";
    }

    // public function indexCheckPO(){
    //     foreach(Product::all() as $product){
    //         foreach(DeliveryDetail::where('product_id',$product->prod_id)->get() as $dd){
    //             $salesdet = SalesDet::where('prod_id',$dd->product_id)->where('trx_id',$dd->sales_id)->count();

    //             if($checkPO == 0){
    //                 echo "RD.".$detail->id." Check PO: ".$checkPO." Product: ".$product->prod_id."<br>";
    //             }
    //         }
    //     }
    // }

    public function indexcheckPOlama(){
        $produk = Product::all();
        foreach($produk as $prod){
            $no = 1;
            $receive = ReceiveDet::where('prod_id', $prod->prod_id)->get();
            foreach($receive as $key){
                $pd = PurchaseDetail::where('trx_id', $key->trx_id)->where('prod_id', $prod->prod_id)->sum('qty');
                $rd = ReceiveDet::where('trx_id', $key->trx_id)->where('prod_id', $prod->prod_id)->sum('qty');

                if($pd != $rd){
                    echo $no++.". ".$prod->prod_id.", PO ".$key->trx_id." qty:".$pd." - RD ".$key->id.", qty:".$rd."<br>";
                }
            }
            echo "<br>";
        }
    }

    public function indexCheckSO(){
        $produk = Product::all();
        foreach($produk as $prod){
            $no = 1;
            $do = DeliveryDetail::where('product_id', $prod->prod_id)->get();
            foreach($do as $key){
                $sd = SalesDet::where('trx_id', $key->sales_id)->where('prod_id', $prod->prod_id)->sum('qty');
                $dd = DeliveryDetail::where('sales_id', $key->sales_id)->where('product_id', $prod->prod_id)->sum('qty');

                if($sd != $dd){
                    echo $no++.". ".$prod->prod_id.", SO ".$key->sales_id." qty:".$sd." - DO: ".$key->do_id." - DD ".$key->id.", qty:".$dd."<br>";
                }
            }
            echo "<br>";
        }
    }

    public function indexCheckDOEmpty(){
        $sales = DeliveryOrder::all();
        foreach($sales as $s){
            $detail = DeliveryDetail::where('do_id', $s->id)->count();
            if($detail == 0){
                echo $s->id."-".$s->jurnal_id."<br>";
            }
        }
    }

    public function indexCheckSOEmpty(){
        $sales = Sales::all();
        foreach($sales as $s){
            $detail = SalesDet::where('trx_id', $s->id)->count();
            if($detail == 0){
                echo $s->id."<br>";
            }
        }
    }

    public function indexcheckrelevant(){
        $deliveries = DeliveryOrder::all();
        foreach($deliveries as $do){
            $saldet = SalesDet::where('trx_id', $do->sales_id)->count();
            if($saldet == 0){
                echo $do->sales_id." ".$do->id."<br>";
            }
        }
    }

    public function indexCekJurnalUnbalance(){
        $dateA = "2020-01-01";
        $dateB = "2020-07-31";
        $jurnal = Jurnal::whereBetween('date',[$dateA,$dateB])->groupBy('id_jurnal')->get();
        echo "Data Jurnal ".$dateA." - ".$dateB."<hr>";
        foreach($jurnal as $key){
            $debet = Jurnal::where('id_jurnal', $key->id_jurnal)->where('AccPos', 'Debet')->sum('Amount');
            $credit = Jurnal::where('id_jurnal', $key->id_jurnal)->where('AccPos', 'Credit')->sum('Amount');

            if($debet != $credit){
                echo "<hr>";
                echo $key->id_jurnal." - Debet : ".number_format($debet,2,",",".").", Credit : ".number_format($credit, 2, ",", ".")."<br>";
            }
        }
    }

    // public function indexinsertpodettoreceive(){
    //     foreach(ReceiveDet::select('id', 'prod_id', 'trx_id')->get() as $key){
    //         $purchasedet = PurchaseDetail::where('prod_id', $key->prod_id)->where('trx_id', $key->trx_id)->first();

    //         $key->purchasedetail_id = $purchasedet->id;
    //         $key->save();
    //     }

    //     $transaksi = Transaksi::where('id_user',$id_user)->first();

    //     // dd($transaksi->barang()->first()); // buat cek datanya ada atau enggak
    //     // dd($transaksi->barang->name); // buat get name nya, kalo emng datanya ada
    // }

    public function index_sodet(){
        echo "lelele";
        foreach (SalesDet::all() as $key) {
            $query = SalesDet::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->count();
            if($query > 1){
                echo "<hr>";
                echo "SO.".$key->trx_id." - ".$key->prod_id." : ".$query."<br>";
            }
        }
    }

    public function index_podet(){
        echo "lalala";
        foreach (PurchaseDetail::all() as $key) {
            $query = PurchaseDetail::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->count();
            if($query > 1){
                echo "<hr>";
                echo "PO.".$key->trx_id." - ".$key->prod_id." : ".$query."<br>";
            }
        }
    }
}
