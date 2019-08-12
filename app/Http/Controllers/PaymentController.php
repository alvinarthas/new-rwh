<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Sales;
use App\PurchasePayment;
use App\Customer;

class PaymentController extends Controller
{
    // Sales
    public function salesIndex(){
        $customers = Customer::select('id','apname')->orderBy('apname','asc')->get();
        return view('payment.sales.index',compact('customers'));
    }

    public function salesView(Request $request){
        $start_trx = $request->start_trx;
        $end_trx = $request->end_trx;
        $start_pay = $request->start_pay;
        $end_pay = $request->end_pay;
        $customer = $request->customer;

        $sales = Sales::getOrderPayment($start_trx,$end_trx,$start_pay,$end_pay,$customer);
        if ($request->ajax()) {
            return response()->json(view('payment.sales.view',compact('sales'))->render());
        }

    }

    public function salesCreate(Request $request, $id){
        $sales = Sales::where('id',$id)->first();
        $payment = SalesPayment::where('trx_id',$id)->get();
    }

    public function salesStore(Request $request){

    }

    // Purchase
    public function purchaseIndex(){

    }

    public function purchaseView(Request $request){

    }

    public function purchaseCreate(Request $request){

    }

    public function purchaseStore(Request $request){

    }
}
