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
use App\MenuMapping;
use App\Log;

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
        $page = MenuMapping::getMap(session('user_id'),"PSSP");
        $sales = Sales::getOrderPayment($start_trx,$end_trx,$start_pay,$end_pay,$customer,$request->param);
        if ($request->ajax()) {
            return response()->json(view('payment.sales.view',compact('sales','page'))->render());
        }

    }

    public function salesCreate(Request $request, $id){
        $sales = Sales::where('id',$id)->first();
        $details = SalesDet::where('trx_id',$id)->get();
        $payment = SalesPayment::where('trx_id',$id)->get();
        $ttl_pay = SalesPayment::where('trx_id',$id)->sum('payment_amount');
        $coas = Coa::where('StatusAccount','Detail')->where('AccNo','LIKE','1.1.1.2%')->orWhere('AccNo','LIKE','1.10.%')->orderBy('AccName','asc')->get();
        $page = MenuMapping::getMap(session('user_id'),"PSSP");
        return view('payment.sales.form',compact('sales','payment','coas','details','ttl_pay','page'));
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
            $id_jurnal = Jurnal::getJurnalID('SP');

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
                    if($request->payment_method == "2.1.2"){
                        $saldo = new Saldo(array(
                            'customer_id' => $request->customer_info,
                            'status' => 0,
                            'amount' => $request->payment_amount,
                            'keterangan' => "Sales Order Payment SO.$request->trx_id",
                            'creator' => session('user_id'),
                            'tanggal' => $request->payment_date,
                            'id_jurnal' => $id_jurnal,
                        ));
                        $saldo->save();
                    }

                // Jurnal Debet kas/bank
                    Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->payment_date,$jurnal_desc,$request->payment_method,'Debet');
                // Jurnal Credit piutang konsumen
                    Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->payment_date,$jurnal_desc,'1.1.3.1','Credit');

                    Log::setLog('PSSPC','Create Sales Payment SO.'.$sales->id.' Jurnal ID: '.$id_jurnal);
                return redirect()->back()->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    public function salesPayDestroy(Request $request){
        $payment = SalesPayment::where('id',$request->id)->first();
        $jurnal = Jurnal::where('id_jurnal',$payment->jurnal_id);
        $sales = Sales::where('id',$payment->trx_id)->first();
        $saldo = Saldo::where('id_jurnal',$payment->jurnal_id);
        $sales->status = 0;
        $sales->save();

        $id_jurnal = $payment->jurnal_id;

        try {
            $payment->delete();
            $jurnal->delete();
            $saldo->delete();
            Log::setLog('PSSPD','Delete Sales Payment SO.'.$request->id.' Jurnal ID: '.$id_jurnal);
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
        $purchase = Purchase::getOrderPayment($request->bulan,$request->tahun,$request->param);
        $page = MenuMapping::getMap(session('user_id'),"PUPP");
        if ($request->ajax()) {
            return response()->json(view('payment.purchase.view',compact('purchase','page'))->render());
        }
    }

    public function purchaseCreate(Request $request,$id){
        $purchase = Purchase::where('id',$id)->first();
        $details = PurchaseDetail::where('trx_id',$id)->get();
        $payment = PurchasePayment::where('trx_id',$id)->get();
        $ttl_pay = PurchasePayment::where('trx_id',$id)->sum('payment_amount');
        $ttl_order = PurchaseDetail::where('trx_id',$id)->sum(DB::raw('qty * price_dist'));
        $coas = Coa::where('StatusAccount','Detail')->where('AccNo','LIKE','1.1.1.2%')->orWhere('AccNo','LIKE','2.5%')->orWhere('AccNo','LIKE','1.10.%')->orderBy('AccName','asc')->get();
        $page = MenuMapping::getMap(session('user_id'),"PUPP");

        return view('payment.purchase.form',compact('purchase','payment','coas','details','ttl_pay','ttl_order','page'));
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
            $id_jurnal = Jurnal::getJurnalID('PP');

            $payment = new PurchasePayment(array(
                'trx_id' => $request->trx_id,
                'payment_date' => $request->payment_date,
                'payment_amount' => $request->payment_amount,
                'deduct_category' => $request->payment_deduction,
                'payment_method' => $request->payment_method,
                'payment_desc' => $request->payment_description,
                'due_date' => $request->next_due_date,
                'deduct_amount' =>$request->deduct_amount,
                'jurnal_id' => $id_jurnal,
            ));

            try {
                // Jurnal Debet Hutang Dagang
                Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->payment_date,$jurnal_desc,'2.1.1','Debet');

                // Jurnal Credit Cash/Bank / Deposit Pembelian
                Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->payment_date,$jurnal_desc,$request->payment_method,'Credit');
                // Payment
                    $payment->save();
                if($rest == 0){
                    $purchase = Purchase::where('id',$request->trx_id)->first();
                    $purchase->status = 1;

                    $purchase->save();
                }

                Log::setLog('PUPPC','Create Purchase Payment PO.'.$request->trx_id.' Jurnal ID: '.$id_jurnal);
                return redirect()->back()->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    public function purchasePayDestroy(Request $request){
        $payment = PurchasePayment::where('id',$request->id)->first();
        $jurnal = Jurnal::where('id_jurnal',$payment->jurnal_id)->first();
        $purchase = Purchase::where('id',$payment->trx_id)->first();
        $purchase->status = 0;
        $purchase->save();

        $id_jurnal = $payment->jurnal_id;

        try {
            $payment->delete();
            $jurnal->delete();
            Log::setLog('PUPPD','Delete Purchase Payment PO.'.$request->id.' Jurnal ID: '.$id_jurnal);

            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
