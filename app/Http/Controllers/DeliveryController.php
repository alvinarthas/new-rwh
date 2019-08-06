<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Storage;

use App\Customer;
use App\Product;
use App\PriceDet;
use App\Sales;
use App\SalesDet;

class DeliveryController extends Controller
{
    public function index(Request $request){
        $customers = Customer::select('id','apname')->get();
        $products = Product::orderBy('prod_id','asc')->get();
        return view('sales.do.index',compact('customers','products'));
    }

    public function view(Request $request){
        $invoice = SalesDet::getOrder($request->start,$request->end,$request->customer,$request->product);
        if ($request->ajax()) {
            return response()->json(view('sales.do.view',compact('invoice'))->render());
        }
    }

    public function print(Request $request){
        $trx_id = $request->trx_id;
        $transaksi = SalesDet::where('id',$trx_id)->first();

        $file =  'DO-'.$trx_id.'.txt';  # nama file temporary yang akan dicetak
        // $handle = fopen($file, 'w');
        $Data = "=========================\r\n";
        $Data .= "|       RWH HERBAL    |\r\n";
        $Data .= "|    DELIVERY ORDER   |\r\n";
        $Data .= "========================\r\n";
        $Data .= "TRXID : ".$transaksi->trx->trx_id."\r\n";
        $Data .= "DATE : ".$transaksi->trx->trx_date."\r\n";
        $Data .= "MARKETING : ".strtoupper($transaksi->trx->customer->apname)."\r\n";
        $Data .= "==========================\r\n";
        $Data .= $transaksi->product->name."\r\n";
        $Data .= "Qty : ".$transaksi->qty." ".$transaksi->unit."\r\n";
        $Data .= "\r\n";
        $Data .= "Approved By\r\nInventory Officer\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
        Storage::put($file, $Data);
        // file_put_contents($file, $Data);
        // fwrite($handle, $Data);
        // fclose($handle);
        // copy($file, "//localhost/POS-80C");
        // unlink($file);
    }
}
