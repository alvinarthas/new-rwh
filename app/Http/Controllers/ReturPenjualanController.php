<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Jurnal;
use App\ReturPembelian;
use App\ReturPembelianDet;
use App\ReturPenjualan;
use App\ReturPenjualanDet;
use App\ReturPayment;
use App\Sales;
use App\SalesDet;
use App\Purchase;
use App\PurchaseDetail;
use App\Retur;
use App\ReturDetail;
use App\Perusahaan;
use App\Customer;
use Carbon\Carbon;
use App\MenuMapping;
use App\Coa;

class ReturPenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $sales = Sales::join('tblproducttrxdet', 'tblproducttrx.id', '=', 'tblproducttrxdet.trx_id')->orderBy('tblproducttrx.id', 'desc')->get();
        // $retur = ReturPenjualanDet::all();
        // $retur = ReturPenjualan::join('tblproducttrx', 'tblreturpj.trx_id', 'tblproducttrx.id')->select('tblreturpj.trx_id', 'tblreturpj.id_jurnal', 'tblreturpj.tgl', 'tblreturpj.customer', 'tblproducttrx.jurnal_id AS so_id')->get();
        $retur = Retur::where('status', '1')->get();
        $jenis = "report";
        $jenisretur = "penjualan";
        $page = MenuMapping::getMap(session('user_id'),"RJSO");
        return view('retur.nota.index', compact('retur', 'jenis', 'jenisretur','page'));
    }

    public function indexReturPayment()
    {
        $retur = Retur::getReturPay(1);
        $jenisretur = "penjualan";
        $page = MenuMapping::getMap(session('user_id'),"RJSP");
        return view('retur.payment.index', compact('retur', 'jenisretur','page'));
    }

    public function indexReturDelivery()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenisretur = "penjualan";
        $jenis = "create";
        $customer = Customer::all();
        return view('retur.nota.index', compact('jenisretur', 'jenis', 'customer'));
    }

    public function createReturPayment()
    {
        //
    }

    public function createReturDelivery()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
            $jenis = "PJ";

            // $retur = ReturPenjualan::join('tblproducttrx', 'tblreturpj.trx_id', 'tblproducttrx.id')->where('tblreturpj.trx_id',$request->id)->select('tblreturpj.trx_id', 'tblreturpj.id_jurnal', 'tblreturpj.tgl', 'tblreturpj.customer', 'tblproducttrx.jurnal_id AS so_id', 'tblreturpj.creator')->first();
            $retur = Retur::where('status', 1)->where('id', $request->id)->first();
            $so_trx = Sales::where('jurnal_id', $retur->source_id)->first()->id;
            // $returdet = ReturPenjualanDet::where('trx_id',$request->id)->get();
            $returdet = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.status', 1)->where('trx_id', $request->id)->get();

            return response()->json(view('retur.nota.modal',compact('retur','returdet', 'jenis', 'so_trx'))->render());
        }
    }

    public function showReturPayment($id, Request $request)
    {
        //
    }

    public function showReturDelivery($id, Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jenisretur = "penjualan";
        $sales = Sales::where('id', $id)->first();
        $salesdet = SalesDet::where('trx_id', $id)->get();
        $customer = Customer::where('id', $sales['customer_id'])->first();
        $trx_id = $id;
        return view('retur.nota.form', compact('salesdet', 'sales', 'customer', 'jenisretur', 'trx_id'));
    }

    public function editReturPayment($id)
    {
        $jenisretur = "penjualan";
        $retur = Retur::where('id', $id)->first();
        $details = ReturDetail::where('trx_id', $id)->get();
        $ttl_pay = ReturPayment::where('trx_id',$id)->sum('amount');
        $ttl_order = ReturDetail::where('trx_id',$id)->sum(DB::raw('qty * harga_dist'));
        $coas = Coa::where('StatusAccount','Detail')->where('AccNo','LIKE','1.1.1.2.%')->orWhere('AccNo','LIKE','1.1.1.1.%')->orWhere('AccNo','LIKE','2.5%')->orWhere('AccNo','LIKE','1.10.%')->orderBy('AccName','asc')->get();
        $payment = ReturPayment::where('trx_id', $id)->get();

        return view('retur.payment.form', compact('retur', 'details', 'ttl_pay', 'ttl_order', 'coas', 'jenisretur', 'payment'));
    }

    public function editReturDelivery($id)
    {
        //
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

                        if($qty!=0){
                            $sales_det = SalesDet::where('trx_id', $id)->where('prod_id', $prod_id)->first();

                            $returdet = new ReturDetail;
                            $returdet->trx_id = $retur->id;
                            $returdet->prod_id = $prod_id;
                            $returdet->qty = $qty;
                            $returdet->harga = $sales_det->price;
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

                                $total_transaksi += $sales_det->price * $qty;

                            }catch(\Exception $e) {
                                return redirect()->back()->withErrors($e->errorInfo);
                                // return response()->json($e);
                            }
                        }

                        // foreach (SalesDet::where('trx_id',$id)->get() as $key) {
                        //     $avcharga = PurchaseDetail::where('prod_id',$key->prod_id)->avg('price');
                        //     $modal += ($qty * $avcharga);
                        // }
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
        echo "<pre>";
        print_r($request->all());
        die();
    }

    public function updateReturDelivery(Request $request, $id)
    {
        //
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
            Jurnal::where('id_jurnal',$data->id_jurnal)->delete();
            ReturPayment::where('id', $request->id)->delete();

            return "true";
        }catch(\Exception $a){
            return response()->json($a);
        }
    }

    public function showReturPenjualan(Request $request)
    {
        $jenisretur = "penjualan";
        $customer = $request->customer;
        $retur = ReturPenjualanDet::all();
        $sales = Sales::join('tblproducttrxdet', 'tblproducttrx.id', '=', 'tblproducttrxdet.trx_id')->where('tblproducttrx.customer_id', $customer)->orderBy('tblproducttrx.id','desc')->get();
        return view('retur.nota.ajxShow', compact('jenisretur','sales', 'retur'));;
    }
}
