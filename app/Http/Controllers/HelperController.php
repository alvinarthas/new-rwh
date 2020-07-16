<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;

use App\DataKota;
use App\Saldo;
use App\Sales;
use App\SalesDet;
use App\DeliveryOrder;
use App\DeliveryDetail;
use App\Coa;
use App\Deposit;
use App\Jurnal;
use App\Purchase;
use App\ReceiveDet;

class HelperController extends Controller
{
    public function getDataKota(Request $request){
        $kota = DataKota::where('kode_pusdatin_prov',$request->prov)->select('kode_pusdatin_kota','kab_kota')->get();

        $html = '<option value="#" disabled selected>Pilih Kab/Kota</>';
        foreach ($kota as $key) {
            $html.='<option value="'.$key->kode_pusdatin_kota.'">'.$key->kab_kota.'</option>';
        }
        echo $html;
    }

    public function checkSaldo(Request $request){
        $customer = $request->customer;

        $saldo_in = Saldo::where('customer_id',$customer)->where('status',1)->sum('amount');
        $saldo_out = Saldo::where('customer_id',$customer)->where('status',0)->sum('amount');

        $total = $saldo_in - $saldo_out;

        return $total;
    }

    public function checkDeposit(Request $request){
        $deposit  = Deposit::getSaldo($request->supplier);
        return $deposit;
    }

    public function ajxCoa(Request $request){
        $params = $request->params;
        $obat = Coa::where('AccName','LIKE',$params.'%')->orWhere('AccNo','LIKE',$params.'%')->limit(5)->get();
        $data = collect();
        foreach ($obat as $key) {
          $arrayName = array('id' =>$key->AccNo,'text' =>$key->AccNo." - ".$key->AccName);
          $data->push($arrayName);
        }

        return response()->json($data);
    }

    public function recycleSO(Request $request){
        // Loop All SO
        foreach (Sales::where('jurnal_id','!=','0')->select('id','trx_date')->get() as $key) {
            // Recyle Sales Jurnal
            Sales::recycleSales($key->id);

            // Get Delivery Order Data
            foreach (DeliveryOrder::where('sales_id')->select('id')->get() as $key2) {
                DeliveryOrder::recycleDO($key2->id,$key->trx_date);
            }
        }
    }

    public function recyclePO(Request $request){
        ini_set('max_execution_time', 900);
        // Loop All PO
        foreach (Purchase::where('jurnal_id','!=','0')->get() as $key) {
            // Recycle Purchase Jurnal
            Purchase::recylePurchase($key->id);

            // Recycle Receive Detail
            ReceiveDet::recycleRP($key->id);
        }
    }

    public function inBalanceJurnal(Request $request){
        $start = $request->start;
        $end = $request->end;

        foreach(Jurnal::whereBetween('date',[$start,$end])->select('id_jurnal')->get() as $jurnal){
            $debet = Jurnal::where('id_jurnal',$jurnal->id_jurnal)->where('AccPos','Debet')->sum('Amount');
            $credit = Jurnal::where('id_jurnal',$jurnal->id_jurnal)->where('AccPos','Credit')->sum('Amount');

            if ($debet != $credit){
                echo "JURNAL: ".$jurnal->id_jurnal."<br>";
            }
        }
    }

    public function inBalanceDO(Request $request){
        $product = $request->product;
        $type = $request->type;

        if ($type == "inbalance"){
            $sales = Sales::join('tblproducttrxdet','tblproducttrxdet.trx_id','=','tblproducttrx.id')->where('jurnal_id','!=','0')->where('tblproducttrxdet.prod_id',$product)->select('tblproducttrx.id')->groupBy('tblproducttrx.id')->get();

            foreach($sales as $sale){
                $deliveryDetail = DeliveryDetail::where('sales_id',$sale->id)->where('product_id',$product)->sum('qty');
                $salesDetail = SalesDet::where('trx_id',$sale->id)->where('prod_id',$product)->sum('qty');

                if ($deliveryDetail != $salesDetail){
                    echo "SO.".$sale->id." Total DO: ".$deliveryDetail." Total SO: ".$salesDetail."<br>";
                }
            }
        }else{
            foreach(DeliveryDetail::where('product_id',$product)->get() as $detail){
                $checkSO = SalesDet::where('trx_id',$detail->sales_id)->where('prod_id',$product)->count();

                if($checkSO == 0){
                    echo "DD.".$detail->id."<br>";
                }
            }
        }
    }
}
