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

use Carbon\Carbon;

class TestController extends Controller
{
    // Check ReceiveDet Null
    public function index(){
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

    public function indexCheckPO(){
        foreach(Product::all() as $product){
            foreach(DeliveryDetail::where('product_id',$product->prod_id)->get() as $dd){
                $salesdet = SalesDet::where('prod_id',$dd->product_id)->where('trx_id',$dd->sales_id)->count();

                if($checkPO == 0){
                    echo "RD.".$detail->id." Check PO: ".$checkPO." Product: ".$product->prod_id."<br>";
                }
            }
        }
    }

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

    public function indexinsertpodettoreceive(){
        foreach(ReceiveDet::select('id', 'prod_id', 'trx_id')->get() as $key){
            $purchasedet = PurchaseDetail::where('prod_id', $key->prod_id)->where('trx_id', $key->trx_id)->first();

            $key->purchasedetail_id = $purchasedet->id;
            $key->save();
        }

        $transaksi = Transaksi::where('id_user',$id_user)->first();

        // dd($transaksi->barang()->first()); // buat cek datanya ada atau enggak
        // dd($transaksi->barang->name); // buat get name nya, kalo emng datanya ada
    }

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
