<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;

use App\Sales;
use App\SalesDet;
use App\SalesPayment;
use App\Customer;
use App\Coa;
use App\Purchase;
use App\PurchasePayment;
use App\PurchaseDetail;
use App\Saldo;
use App\SaldoHistory;
use App\Jurnal;

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
        $details = SalesDet::where('trx_id',$id)->get();
        $payment = SalesPayment::where('trx_id',$id)->get();
        $ttl_pay = SalesPayment::where('trx_id',$id)->sum('payment_amount');
        $coas = Coa::where('grup_id',5)->where('StatusAccount','Detail')->orderBy('AccName','asc')->get();

        return view('payment.sales.form',compact('sales','payment','coas','details','ttl_pay'));
    }

    public function salesStore(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required',
            'payment_date' => 'required',
            'payment_method' => 'required',
            'payment_amount' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $rest = $request->paid - $request->payment_amount;
            // Jurnal 
            $jurnal_desc = "SO.".$request->trx_id;
            $id_jurnal = Jurnal::getJurnalID();

            $payment = new SalesPayment(array(
                'trx_id' => $request->trx_id,
                'payment_date' => $request->payment_date,
                'payment_amount' => $request->payment_amount,
                'deduct_category' => $request->payment_deduction,
                'payment_method' => $request->payment_method,
                'payment_description' => $request->payment_description,
                'due_date' => $request->next_due_date,
                'deduct_amount' =>$request->deduct_amount,
                'jurnal_id' => $id_jurnal,
            ));
            
            try {

                // Payment
                    $payment->save();
                
                // Status Sales
                if($rest == 0){
                    $sales = Sales::where('id',$request->trx_id)->first();
                    $sales->status = 1;
    
                    $sales->save();
                }

                // Saldo
                    if($request->payment_method == "saldo"){
                        $saldo = Saldo::where('customer_id',$request->customer_info)->first();
                        $saldodet = new SaldoHistory(array(
                            'saldo_id' => $saldo->id,
                            'customer_id' => $saldo->customer_id,
                            'jenis' => 'Credit',
                            'amount' => $request->payment_amount,
                            'keterangan' => "Sales Order Payment SO.$request->trx_id",
                            'creator' => session('user_id'),
                            'input_date' => $request->payment_date
                        ));
                        $saldodet->save();
        
                        $saldo_skrng = $saldo->saldo_skrng - $request->payment_amount;
                        $saldo->saldo_skrng = $saldo_skrng;
                        $saldo->save();
                    }

                // Jurnal Debet kas/bank
                    Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->payment_date,$jurnal_desc,$request->payment_method,'Debet');
                    if($request->payment_deduction == "Biaya_Transfer_Bank"){
                        $pay_method = "4-201001";

                        //insert debet potongan bank
                        Jurnal::addJurnal($id_jurnal,$request->deduct_amount,$request->payment_date,$jurnal_desc,$pay_method,'Debet');
                    }
                // Jurnal Credit piutang konsumen
                    Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->payment_date,$jurnal_desc,'1-103001','Credit');

                return redirect()->back()->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }

    public function salesPayDestroy(Request $request){
        $payment = SalesPayment::where('id',$request->id)->first();
        $jurnal = Jurnal::where('id_jurnal',$payment->jurnal_id)->first();
        $sales = Sales::where('id',$payment->trx_id)->first();
        $sales->status = 0;
        $sales->save();

        try {
            $payment->delete();
            $jurnal->delete();
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    // Purchase
    public function purchaseIndex(){
        return view('payment.purchase.index');
    }

    public function purchaseView(Request $request){
        $purchase = Purchase::getOrderPayment($request->bulan,$request->tahun);

        if ($request->ajax()) {
            return response()->json(view('payment.purchase.view',compact('purchase'))->render());
        }
    }

    public function purchaseCreate(Request $request,$id){
        $purchase = Purchase::where('id',$id)->first();
        $details = PurchaseDetail::where('trx_id',$id)->get();
        $payment = PurchasePayment::where('trx_id',$id)->get();
        $ttl_pay = PurchasePayment::where('trx_id',$id)->sum('payment_amount');
        $ttl_order = PurchaseDetail::where('trx_id',$id)->sum(DB::raw('qty * price'));
        $coas = Coa::where('grup',5)->orWhere('grup',2)->orWhere(DB::raw("AccNo like '2%' and AccName like '%CC%'"))->where('StatusAccount','Detail')->orderBy('AccName','asc')->get();

        return view('payment.purchase.form',compact('purchase','payment','coas','details','ttl_pay','ttl_order'));
    }

    public function purchaseStore(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required',
            'payment_date' => 'required',
            'payment_method' => 'required',
            'payment_amount' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $rest = $request->paid - $request->payment_amount;
            // Jurnal 
            $jurnal_desc = "PO.".$request->trx_id;
            $id_jurnal = Jurnal::getJurnalID();

            $payment = new PurchasePayment(array(
                'trx_id' => $request->trx_id,
                'payment_date' => $request->payment_date,
                'payment_amount' => $request->payment_amount,
                'payment_deduction' => $request->payment_deduction,
                'payment_method' => $request->payment_method,
                'payment_description' => $request->payment_description,
                'due_date' => $request->next_due_date,
                'deduct_amount' =>$request->deduct_amount,
                'jurnal_id' => $id_jurnal,
            ));
            
            try {
                // Payment
                    $payment->save();
                if($rest == 0){
                    $purchase = Purchase::where('id',$request->trx_id)->first();
                    $purchase->status = 1;
    
                    $purchase->save();
                }
                // Jurnal Debet kas/bank
                Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->payment_date,$jurnal_desc,'2-102002','Debet');
                    if($request->payment_deduction == "Biaya_Transfer_Bank"){
                        $pay_method = "4-201001";

                        //insert debet potongan bank
                        Jurnal::addJurnal($id_jurnal,$request->deduct_amount,$request->payment_date,$jurnal_desc,$pay_method,'Debet');
                    }
                // Jurnal Credit piutang konsumen
                    Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->payment_date,$jurnal_desc,$request->payment_method,'Credit');
                    

                return redirect()->back()->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }
    
    public function purchasePayDestroy(Request $request){
        $payment = PurchasePayment::where('id',$request->id)->first();
        $jurnal = Jurnal::where('id_jurnal',$payment->jurnal_id)->first();
        $purchase = Purchase::where('id',$payment->trx_id)->first();
        $purchase->status = 0;
        $purchase->save();

        try {
            $payment->delete();
            $jurnal = Jurnal::where('id_jurnal',$payment->jurnal_id)->first();
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
