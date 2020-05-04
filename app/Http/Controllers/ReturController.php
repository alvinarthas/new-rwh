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
use App\Sales;
use App\SalesDet;
use App\Purchase;
use App\PurchaseDetail;
use App\Perusahaan;
use App\Customer;
use Carbon\Carbon;
use App\MenuMapping;

class ReturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $purchase = Purchase::join('tblpotrxdet', 'tblpotrx.id', '=', 'tblpotrxdet.trx_id')->orderBy('tblpotrx.id', 'desc')->get();
        // echo $purchase;
        // die();
        // $retur = ReturPembelianDet::join('tblreturpb','tblreturpbdet.trx_id', 'tblreturpb.trx_id')->join('tblperusahaan','tblreturpb.supplier', 'tblperusahaan.id')->join('tblproduct', 'tblreturpbdet.prod_id', 'tblproduct.id')->select('tblreturpb.trx_id', 'tblperusahaan.nama', 'tblproduct.name', 'tblproduct.prod_id');
        $retur = ReturPembelian::join('tblpotrx', 'tblreturpb.trx_id', 'tblpotrx.id')->select('tblreturpb.trx_id','tblreturpb.id_jurnal', 'tblreturpb.tgl', 'tblreturpb.supplier', 'tblpotrx.jurnal_id AS po_id')->get();
        $jenis = "report";
        $jenisretur = "pembelian";
        $page = MenuMapping::getMap(session('user_id'),"REPB");
        return view('retur.index', compact('retur', 'jenis', 'jenisretur','page'));
    }

    public function indexpj()
    {
        // $sales = Sales::join('tblproducttrxdet', 'tblproducttrx.id', '=', 'tblproducttrxdet.trx_id')->orderBy('tblproducttrx.id', 'desc')->get();
        // $retur = ReturPenjualanDet::all();
        $retur = ReturPenjualan::join('tblproducttrx', 'tblreturpj.trx_id', 'tblproducttrx.id')->select('tblreturpj.trx_id', 'tblreturpj.id_jurnal', 'tblreturpj.tgl', 'tblreturpj.customer', 'tblproducttrx.jurnal_id AS so_id')->get();
        $jenis = "report";
        $jenisretur = "penjualan";
        $page = MenuMapping::getMap(session('user_id'),"REPJ");
        return view('retur.index', compact('retur', 'jenis', 'jenisretur','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenisretur = "pembelian";
        $jenis = "create";
        return view('retur.index', compact('jenisretur', 'jenis'));
    }

    public function createpj()
    {
        $jenisretur = "penjualan";
        $jenis = "create";
        $customer = Customer::orderBy('apname', 'asc')->get();
        return view('retur.index', compact('jenisretur', 'jenis', 'customer'));
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
    public function show($id)
    {
        //
    }

    public function showpb(Request $request, $id){
        if ($request->ajax()) {
            $jenis = "PB";
            $retur = ReturPembelian::join('tblpotrx', 'tblreturpb.trx_id', 'tblpotrx.id')->where('tblreturpb.trx_id',$request->id)->select('tblreturpb.trx_id', 'tblreturpb.id_jurnal', 'tblreturpb.tgl', 'tblreturpb.supplier', 'tblpotrx.jurnal_id AS po_id', 'tblreturpb.creator')->first();
            $returdet = ReturPembelianDet::where('trx_id',$request->id)->get();

            return response()->json(view('retur.modal',compact('retur','returdet', 'jenis'))->render());
        }
    }

    public function showpj(Request $request, $id){
        if ($request->ajax()) {
            $jenis = "PJ";
            $retur = ReturPenjualan::join('tblproducttrx', 'tblreturpj.trx_id', 'tblproducttrx.id')->where('tblreturpj.trx_id',$request->id)->select('tblreturpj.trx_id', 'tblreturpj.id_jurnal', 'tblreturpj.tgl', 'tblreturpj.customer', 'tblproducttrx.jurnal_id AS so_id', 'tblreturpj.creator')->first();
            $returdet = ReturPenjualanDet::where('trx_id',$request->id)->get();

            return response()->json(view('retur.modal',compact('retur','returdet', 'jenis'))->render());
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
        $jenisretur = "pembelian";
        $purchase = Purchase::where('id', $id)->first();
        $purchasedet = PurchaseDetail::where('trx_id', $id)->get();
        $perusahaans = Perusahaan::all();
        $trx_id = $id;
        return view('retur.form', compact('purchasedet', 'purchase', 'perusahaans', 'jenisretur', 'trx_id'));
    }

    public function editpj($id)
    {
        $jenisretur = "penjualan";
        $sales = Sales::where('id', $id)->first();
        $salesdet = SalesDet::where('trx_id', $id)->get();
        $customer = Customer::where('id', $sales['customer_id'])->first();
        $trx_id = $id;
        return view('retur.form', compact('salesdet', 'sales', 'customer', 'jenisretur', 'trx_id'));
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
            'supplier' => 'required',
        ]);

        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $id_jurnal = Jurnal::getJurnalID('RB');

                $retur = new ReturPembelian;
                $retur->trx_id = $id;
                $retur->tgl = $tgl;
                $retur->supplier = $request->supplier;
                $retur->id_jurnal = $id_jurnal;
                $retur->creator = session('user_id');
                $ctr = count($request->qtyretur);

                $total_modal = 0;
                $total_tertahan = 0;
                $total_distributor = 0;

                for($i=0;$i<$ctr;$i++){
                    $qty = $request->qtyretur[$i];
                    $reason = $request->reason[$i];
                    $prod_id = $request->prod_id[$i];

                    if($qty!=0){
                        $returdet = new ReturPembelianDet;
                        $returdet->trx_id = $id;
                        $returdet->prod_id = $prod_id;
                        $returdet->qty = $qty;
                        $returdet->reason = $reason;
                        $returdet->creator = session('user_id');
                        $returdet->tgl = $tgl;

                        try{
                            // success
                            $returdet->save();
                            $pur_det = PurchaseDetail::where('trx_id', $id)->where('prod_id', $prod_id)->first();
                            $total_modal += $pur_det->price * $qty;
                            $total_tertahan += PurchaseDetail::where('trx_id',$id)->where('prod_id', $prod_id)->sum(DB::Raw('(price_dist - price)*'.$qty));
                            $total_distributor += $pur_det->price_dist * $qty;
                        }catch(\Exception $e) {
                            // failed
                            return redirect()->back()->withErrors($e->errorInfo);
                            // return response()->json($e);
                        }
                    }
                }

                if($retur->save()){
                    $purchase = Purchase::where('id',$id)->first();

                    $jurnal_desc = "retur ".$id_jurnal." dari ".$purchase->jurnal_id;

                    //insert debet hutang Dagang
                    Jurnal::addJurnal($id_jurnal,$total_distributor,$purchase->tgl,$jurnal_desc,'2.1.1','Debet',session('$user_id'));
                    //insert credit Persediaan Barang Indent ( harga modal x qty )
                    Jurnal::addJurnal($id_jurnal,$total_modal,$purchase->tgl,$jurnal_desc,'1.1.4.1.1','Credit',session('user_id'));
                    if($total_tertahan < 0){
                        //insert debet Estimasi Bonus
                        Jurnal::addJurnal($id_jurnal,$total_tertahan,$purchase->tgl,$jurnal_desc,'1.1.3.4','Debet',session('user_id'));
                    }else{
                        //insert credit Estimasi Bonus
                        Jurnal::addJurnal($id_jurnal,$total_tertahan,$purchase->tgl,$jurnal_desc,'1.1.3.4','Credit',session('user_id'));
                    }

                    return redirect()->route('retur.index')->with('status', 'Data berhasil disimpan');
                }
            }catch(\Exception $a){
                return redirect()->back()->withErrors($a->errorInfo);
                // return response()->json($e);
            }
        }
    }

    public function updatepj(Request $request, $id)
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

                $retur = new ReturPenjualan;
                $retur->trx_id = $id;
                $retur->tgl = $tgl;
                $retur->customer = $request->customer;
                $retur->id_jurnal = $id_jurnal;
                $retur->creator = session('user_id');
                $ctr = count($request->qtyretur);
                $modal = 0;
                $total_transaksi = 0;

                $sales = Sales::where('id',$id)->first();

                for($i=0;$i<$ctr;$i++){
                    $qty = $request->qtyretur[$i];
                    $reason = $request->reason[$i];
                    $prod_id = $request->prod_id[$i];

                    if($qty!=0){
                        $returdet = new ReturPenjualanDet;
                        $returdet->trx_id = $id;
                        $returdet->prod_id = $prod_id;
                        $returdet->qty = $qty;
                        $returdet->reason = $reason;
                        $returdet->creator = session('user_id');
                        $returdet->tgl = $tgl;

                        // success
                        try{
                            $returdet->save();
                            $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price * qty'));

                            $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

                            if($sumprice <> 0 && $sumqty <> 0){
                                $avcharga = $sumprice/$sumqty;
                            }else{
                                $avcharga = 0;
                            }
                            $modal += ($qty * $avcharga);
                            $sales_det = SalesDet::where('trx_id', $id)->where('prod_id', $prod_id)->first();
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


                if($retur->save()){
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

                    return redirect()->route('retur.indexpj')->with('status', 'Data berhasil disimpan');
                }
            }catch(\Exception $a){
                return redirect()->back()->withErrors($a->errorInfo);
                // return response()->json($e);
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
        //
    }

    public function destroypb($id)
    {
        try{
            $data = ReturPembelian::where('trx_id', $id)->first();
            Jurnal::where('id_jurnal',$data->id_jurnal)->delete();
            ReturPembelian::where('trx_id', $id)->delete();

            return "true";
        }catch(\Exception $a){
            return response()->json($a);
        }
    }

    public function destroypj($id)
    {
        try{
            $data = ReturPenjualan::where('trx_id', $id)->first();
            Jurnal::where('id_jurnal',$data->id_jurnal)->delete();
            ReturPenjualan::where('trx_id', $id)->delete();

            return "true";
        }catch(\Exception $a){
            return response()->json($a);
        }
    }

    public function showReturPembelian(Request $request)
    {
        $jenisretur = "pembelian";
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $retur = ReturPembelianDet::all();
        $purchase = Purchase::join('tblpotrxdet', 'tblpotrx.id', '=', 'tblpotrxdet.trx_id')->where('tblpotrx.month', $bulan)->where('tblpotrx.year', $tahun)->orderBy('tblpotrx.id','desc')->get();
        return view('retur.ajxShow', compact('jenisretur','purchase','tahun','bulan', 'retur'));;
    }

    public function showReturPenjualan(Request $request)
    {
        $jenisretur = "penjualan";
        $customer = $request->customer;
        $retur = ReturPenjualanDet::all();
        $sales = Sales::join('tblproducttrxdet', 'tblproducttrx.id', '=', 'tblproducttrxdet.trx_id')->where('tblproducttrx.customer_id', $customer)->orderBy('tblproducttrx.id','desc')->get();
        return view('retur.ajxShow', compact('jenisretur','sales', 'retur'));;
    }
}
