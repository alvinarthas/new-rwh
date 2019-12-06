<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;

use App\DataKota;
use App\Saldo;
use App\Coa;
use App\Deposit;

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
}
