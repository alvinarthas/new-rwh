<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Jurnal;
use App\ReturPayment;
use App\Sales;
use App\SalesDet;
use App\Purchase;
use App\PurchaseDetail;
use App\Retur;
use App\ReturDetail;
use App\ReturStock;
use App\Perusahaan;
use App\Customer;
use Carbon\Carbon;
use App\MenuMapping;
use App\Coa;
use App\Log;
use App\Saldo;

class ReturPenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $retur = Retur::where('status', 1)->get();
        $page = MenuMapping::getMap(session('user_id'),"RJSO");
        return view('retur.penjualan.nota.index', compact('retur', 'page'));
    }

    public function indexReturPayment()
    {
        $retur = Retur::getReturPay(1);
        $page = MenuMapping::getMap(session('user_id'),"RJSP");
        return view('retur.penjualan.payment.index', compact('retur', 'page'));
    }

    public function indexReturDelivery()
    {
        $retur = Retur::getReturStock(1);
        $page = MenuMapping::getMap(session('user_id'),"RJDO");
        return view('retur.penjualan.stock.index', compact('retur', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customer = Customer::orderBy('apname', 'asc')->get();
        return view('retur.penjualan.nota.indexCreate', compact('customer'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if ($request->ajax()) {
            $retur = Retur::where('status', 1)->where('id', $request->id)->first();
            $so_trx = Sales::where('jurnal_id', $retur->source_id)->first()->id;
            $returdet = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.status', 1)->where('trx_id', $request->id)->get();

            return response()->json(view('retur.penjualan.nota.modal',compact('retur','returdet', 'so_trx'))->render());
        }
    }

    public function showReturPayment($id, Request $request)
    {
        //
    }

    public function showReturDelivery($id, Request $request)
    {
        if ($request->ajax()) {
            $jurnal_id = $request->ri_id;
            $sales = ReturStock::where('id_jurnal',$jurnal_id)->where('status', 1)->get();
            $sale = ReturStock::where('id_jurnal',$jurnal_id)->where('status', 1)->first();

            return response()->json(view('retur.penjualan.stock.modal',compact('sale', 'sales'))->render());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sales = Sales::where('id', $id)->first();
        $salesdet = SalesDet::where('trx_id', $id)->get();
        $retur = ReturDetail::getRetured(1, $sales->jurnal_id, $id);
        $customer = Customer::where('id', $sales->customer_id)->first();
        $trx_id = $id;

        return view('retur.penjualan.nota.form', compact('salesdet', 'sales', 'retur', 'trx_id', 'customer'));
    }

    public function editReturPayment($id)
    {
        $retur = Retur::where('id', $id)->first();
        $details = ReturDetail::where('trx_id', $id)->get();
        $ttl_pay = ReturPayment::where('trx_id',$id)->sum('amount');
        $ttl_order = ReturDetail::where('trx_id',$id)->sum(DB::raw('qty * harga'));
        $coas = Coa::where('StatusAccount','Detail')->where('AccNo','LIKE','1.1.1.2.%')->orWhere('AccNo','LIKE','1.1.1.1.%')->orWhere('AccNo','LIKE','2.5%')->orWhere('AccNo','LIKE','1.10.%')->orderBy('AccName','asc')->get();
        $payment = ReturPayment::where('trx_id', $id)->get();
        $page = MenuMapping::getMap(session('user_id'),"RJSP");

        return view('retur.penjualan.payment.form', compact('retur', 'details', 'ttl_pay', 'ttl_order', 'coas', 'payment','page'));
    }

    public function editReturDelivery($id)
    {
        $trx = Retur::where('id',$id)->first();
        $details = ReturStock::detailRetur($id, 1);
        $productretur = ReturDetail::where('trx_id',$id)->select('prod_id')->get();
        $page = MenuMapping::getMap(session('user_id'),"RJDO");
        $delivered = ReturStock::where('trx_id',$id)->groupBy('id_jurnal')->get();
        // echo "<pre>";
        // print_r($details);
        // die();

        return view('retur.penjualan.stock.form',compact('trx','details','productretur','delivered','page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Carbon::setLocale('id');
        $tgl = date('Y-m-d', strtotime(Carbon::today()));
        $validator = Validator::make($request->all(), [
            'prod_id' => 'required',
            'qtyretur' => 'required',
            'reason' => 'required',
            'customer' => 'required',
        ]);

        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $id_jurnal = Jurnal::getJurnalID('RJ');

                $sales = Sales::where('id',$id)->first();

                $retur = new Retur;
                $retur->tgl = $tgl;
                $retur->customer = $request->customer;
                $retur->id_jurnal = $id_jurnal;
                $retur->source_id = $sales->jurnal_id;
                $retur->status = 1;
                $retur->creator = session('user_id');
                $ctr = count($request->qtyretur);
                $modal = 0;
                $total_transaksi = 0;

                if($retur->save()){
                    for($i=0;$i<$ctr;$i++){
                        $qty = $request->qtyretur[$i];
                        $reason = $request->reason[$i];
                        $prod_id = $request->prod_id[$i];
                        $unit = $request->unit[$i];
                        $harga = $request->harga[$i];

                        if($qty!=0){
                            $returdet = new ReturDetail;
                            $returdet->trx_id = $retur->id;
                            $returdet->prod_id = $prod_id;
                            $returdet->qty = $qty;
                            $returdet->unit = $unit;
                            $returdet->harga = $harga;
                            $returdet->reason = $reason;
                            $returdet->creator = session('user_id');

                            try{
                                // success
                                $returdet->save();

                                $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price * qty'));

                                $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

                                if($sumprice <> 0 && $sumqty <> 0){
                                    $avcharga = $sumprice/$sumqty;
                                }else{
                                    $avcharga = 0;
                                }
                                $modal += ($qty * $avcharga);

                                $total_transaksi += $harga * $qty;

                            }catch(\Exception $e) {
                                return redirect()->back()->withErrors($e->errorInfo);
                                // return response()->json($e);
                            }
                        }
                    }
                    $jurnal_desc = "Retur ".$id_jurnal." dari ".$sales->jurnal_id;

                    // Jurnal 1
                    //insert credit Piutang Konsumen Masukkan harga total - diskon
                    Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'1.1.3.1','Credit',session('user_id'));
                    //insert debet pendapatan retail (SALES)
                    Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'4.1.1','Debet',session('user_id'));

                    // Jurnal 2
                    //insert credit COGS
                    Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'5.1','Credit',session('user_id'));
                    //insert debet Persediaan Barang milik customer
                    Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'2.1.3','Debet',session('user_id'));

                    return redirect()->route('returjual.index')->with('status', 'Data berhasil disimpan');
                }

            }catch(\Exception $a){
                return redirect()->back()->withErrors($a->getMessage());
                // return response()->json($e);
            }
        }
    }

    public function updateReturPayment(Request $request, $id)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required',
            'payment_amount' => 'required',
            'payment_method' => 'required',
            'date' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $id_jurnal = Jurnal::getJurnalID('RK');

            $rest = $request->paid - $request->payment_amount;
            $returJual = Retur::where('id',$request->trx_id)->first();

            // Jurnal
            $jurnal_desc = $returJual->id_jurnal;

            $payment = new ReturPayment(array(
                'trx_id' => $request->trx_id,
                'date' => $request->date,
                'amount' => $request->payment_amount,
                'status' => 0,
                'deduct_category' => $request->payment_deduction,
                'creator' => session('user_id'),
                'AccNo' => $request->payment_method,
                'description' => $request->payment_description,
                'deduct_amount' =>$request->deduct_amount,
                'id_jurnal' => $id_jurnal,
            ));

            $amount = $request->payment_amount - $request->deduct_amount;

            try {
                // Jurnal Debet kas/bank
                    Jurnal::addJurnal($id_jurnal,$amount,$request->date,$jurnal_desc,$request->payment_method,'Credit');

                if($request->payment_deduction != "No_Deduction"){
                    // Jurnal Biaya Transfer Bank
                    Jurnal::addJurnal($id_jurnal,$request->deduct_amount,$request->date,$jurnal_desc,$request->payment_deduction,'Debet');
                }

                // Jurnal Credit piutang konsumen
                    Jurnal::addJurnal($id_jurnal,$request->payment_amount,$request->date,$jurnal_desc,'1.1.3.1','Debet');

                // Payment
                $payment->save();

                if($rest == 0){
                    $returJual->status_bayar = 1;

                    $returJual->save();
                }

                if($request->payment_method == "2.1.2"){
                    $saldo = new Saldo(array(
                        'customer_id' => $returJual->customer,
                        'status' => 1,
                        'amount' => $request->payment_amount,
                        'keterangan' => "Retur Sales Payment: ".$returJual->id_jurnal,
                        'creator' => session('user_id'),
                        'tanggal' => $request->date,
                        'id_jurnal' => $id_jurnal,
                    ));
                    $saldo->save();
                }

                Log::setLog('RBPPC','Create Retur Sales Payment Jurnal ID: '.$id_jurnal);
                return redirect()->back()->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    public function updateReturDelivery(Request $request, $id)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required|integer',
            'count' => 'required',
            'prod_id' => 'required|array',
            'qty' => 'required|array',
            'delivery_date' => 'required|date',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $id_jurnal = Jurnal::getJurnalID('RL');
            $retur = Retur::where('id',$request->trx_id)->first();
            $sales = Sales::where('jurnal_id', $retur->source_id)->first();

            try {
                $price = 0;

                for ($i=0; $i < $request->count ; $i++) {
                    $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$request->prod_id[$i])->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

                    $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$request->prod_id[$i])->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

                    if($sumprice <> 0 && $sumqty <> 0){
                        $avcharga = $sumprice/$sumqty;
                    }else{
                        $avcharga = 0;
                    }

                    $price = $avcharga * $request->qty[$i];
                }

                $desc = "Retur Delivery Order dari ".$retur->source_id.", Jurnal ID:".$id_jurnal;
                // JURNAL
                //insert credit Persediaan Barang milik Customer
                Jurnal::addJurnal($id_jurnal,$price,$request->delivery_date,$desc,'2.1.3','Credit');
                //insert debet Persediaan Barang digudang
                Jurnal::addJurnal($id_jurnal,$price,$request->delivery_date,$desc,'1.1.4.1.2','Debet');

                for ($i=0; $i < $request->count ; $i++) {
                    $stock = new ReturStock(array(
                        'trx_id' => $request->trx_id,
                        'prod_id' => $request->prod_id[$i],
                        'qty' => $request->qty[$i],
                        'date' => $request->delivery_date,
                        'id_jurnal' => $id_jurnal,
                        'status' => 1,
                        'creator' => session('user_id'),
                        'gudang_id' => $request->gudang[$i],
                    ));

                    $stock->save();
                }
                Log::setLog('RJDOC','Create Retur Delivery Order dari '.$retur->source_id.' Jurnal ID: '.$id_jurnal);
                return redirect()->back()->with('status', 'Data DO berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = Retur::where('id', $id)->first();
            Jurnal::where('id_jurnal',$data->id_jurnal)->delete();
            Retur::where('id', $id)->delete();

            return "true";
        }catch(\Exception $a){
            return response()->json($a);
        }
    }

    public function destroyPayment(Request $request)
    {
        try{
            $data = ReturPayment::where('id', $request->id)->first();
            Log::setLog('RJSPD','Delete Retur Sales Payment Jurnal ID: '.$data->id_jurnal);
            Jurnal::where('id_jurnal',$data->id_jurnal)->delete();

            return "true";
        }catch(\Exception $a){
            return response()->json($a);
        }
    }

    public function destroyReturDelivery(Request $request){
        $id_jurnal = $request->jurnal_id;
        try {
            Jurnal::where('id_jurnal',$id_jurnal)->delete();
            ReturStock::where('id_jurnal', $id_jurnal)->delete();
            Log::setLog('RBRPD','Delete Retur Receive Product Jurnal ID: '.$id_jurnal);
            return "true";
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e);
        }
    }

    public function showReturPenjualan(Request $request)
    {
        $customer = $request->customer;
        $sales = Sales::join('tblproducttrxdet', 'tblproducttrx.id', '=', 'tblproducttrxdet.trx_id')->where('tblproducttrx.customer_id', $customer)->orderBy('tblproducttrx.id','desc')->get();

        return view('retur.penjualan.nota.ajxShow', compact('sales', 'customer'));
    }
}
