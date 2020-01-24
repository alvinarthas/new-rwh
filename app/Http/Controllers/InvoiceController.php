<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Customer;
use App\Product;
use App\PriceDet;
use App\Sales;
use App\SalesDet;

use PDF;

class InvoiceController extends Controller
{
    public function index(Request $request){
        $customers = Customer::select('id','apname')->get();
        $products = Product::orderBy('prod_id','asc')->get();
        return view('sales.invoice.index',compact('customers','products'));
    }

    public function view(Request $request){
        $invoice = Sales::getOrder($request->start,$request->end,$request->customer,$request->product);
        if ($request->ajax()) {
            return response()->json(view('sales.invoice.view',compact('invoice'))->render());
        }
    }

    public function print(Request $request){
        $trx_id = $request->trx_id;
        $jenis = $request->jenis;
        $transaksi = Sales::where('id',$trx_id)->first();
        $transaksidet = SalesDet::where('trx_id',$trx_id)->get();

        if ($request->ajax()) {
            return response()->json(view('sales.invoice.pdf2',compact('transaksi','jenis','transaksidet'))->render());
        }else{
            return view('sales.invoice.pdf2',compact('transaksi','jenis','transaksidet'));
        }
    }

}
