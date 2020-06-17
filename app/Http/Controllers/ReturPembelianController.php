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
use App\Retur;
use App\ReturPayment;
use App\ReturDetail;
use App\Perusahaan;
use App\Customer;
use Carbon\Carbon;
use App\MenuMapping;
use App\Coa;

class ReturPembelianController extends Controller
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
        // $retur = ReturPembelian::join('tblpotrx', 'tblreturpb.trx_id', 'tblpotrx.id')->select('tblreturpb.trx_id','tblreturpb.id_jurnal', 'tblreturpb.tgl', 'tblreturpb.supplier', 'tblpotrx.jurnal_id AS po_id')->get();

        $retur = Retur::where('status', 0)->get();
        $jenis = "report";
        $jenisretur = "pembelian";
        $page = MenuMapping::getMap(session('user_id'),"RBPO");
        return view('retur.nota.index', compact('retur', 'jenis', 'jenisretur','page'));
    }

    public function indexReturPayment()
    {
        $retur = Retur::getReturPay(0);
        // echo "<pre>";
        // print_r($retur);
        // die();
        $jenisretur = "pembelian";
        $page = MenuMapping::getMap(session('user_id'),"RBPP");
        return view('retur.payment.index', compact('retur', 'jenisretur','page'));
    }

    public function indexReturReceive()
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
        $jenisretur = "pembelian";
        $jenis = "create";
        return view('retur.nota.index', compact('jenisretur', 'jenis'));
    }

    public function createReturPayment()
    {
        //
    }

    public function createReturReceive()
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
            $jenis = "PB";
            // $retur = ReturPembelian::join('tblpotrx', 'tblreturpb.trx_id', 'tblpotrx.id')->where('tblreturpb.trx_id',$request->id)->select('tblreturpb.trx_id', 'tblreturpb.id_jurnal', 'tblreturpb.tgl', 'tblreturpb.supplier', 'tblpotrx.jurnal_id AS po_id', 'tblreturpb.creator')->first();
            $retur = Retur::where('status', 0)->where('id', $request->id)->first();
            $po_trx = Purchase::where('jurnal_id', $retur->source_id)->first()->id;
            // $returdet = ReturPembelianDet::where('trx_id',$request->id)->get();
            $returdet = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.status', 0)->where('trx_id', $request->id)->get();

            return response()->json(view('retur.nota.modal',compact('retur','returdet', 'jenis', 'po_trx'))->render());
        }
    }

    public function showReturPayment($id, Request $request)
    {
        //
    }

    public function showReturReceive($id, Request $request)
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
        $jenisretur = "pembelian";
        $purchase = Purchase::where('id', $id)->first();
        $purchasedet = PurchaseDetail::where('trx_id', $id)->get();
        $perusahaans = Perusahaan::all();
        $trx_id = $id;
        return view('retur.nota.form', compact('purchasedet', 'purchase', 'perusahaans', 'jenisretur', 'trx_id'));
    }

    public function editReturPayment($id)
    {
        $jenisretur = "pembelian";
        $retur = Retur::where('id', $id)->first();
        $details = ReturDetail::where('trx_id', $id)->get();
        $ttl_pay = ReturPayment::where('trx_id',$id)->sum('amount');
        $ttl_order = ReturDetail::where('trx_id',$id)->sum(DB::raw('qty * harga_dist'));
        $coas = Coa::where('StatusAccount','Detail')->where('AccNo','LIKE','1.1.1.2.%')->orWhere('AccNo','LIKE','1.1.1.1.%')->orWhere('AccNo','LIKE','2.5%')->orWhere('AccNo','LIKE','1.10.%')->orderBy('AccName','asc')->get();
        $payment = ReturPayment::where('trx_id', $id)->get();

        return view('retur.payment.form', compact('retur', 'details', 'ttl_pay', 'ttl_order', 'coas', 'jenisretur', 'payment'));
    }

    public function editReturReceive($id)
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
        // echo "<pre>";
        // print_r($request->all());
        // die();

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

                $purchase = Purchase::where('id',$id)->first();

                // $retur = new ReturPembelian;
                $retur = new Retur;
                $retur->tgl = $tgl;
                $retur->supplier = $request->supplier;
                $retur->id_jurnal = $id_jurnal;
                $retur->source_id = $purchase->jurnal_id;
                $retur->status = 0;
                $retur->creator = session('user_id');
                // echo "<pre>";
                // print_r($retur);
                // die();
                $ctr = count($request->qtyretur);

                $total_modal = 0;
                $total_tertahan = 0;
                $total_distributor = 0;

                if($retur->save()){
                    for($i=0;$i<$ctr;$i++){
                        $qty = $request->qtyretur[$i];
                        $reason = $request->reason[$i];
                        $prod_id = $request->prod_id[$i];

                        if($qty!=0){
                            $pur_det = PurchaseDetail::where('trx_id', $id)->where('prod_id', $prod_id)->first();

                            $returdet = new ReturDetail;
                            $returdet->trx_id = $retur->id;
                            $returdet->prod_id = $prod_id;
                            $returdet->qty = $qty;
                            $returdet->harga = $pur_det->price;
                            $returdet->harga_dist = $pur_det->price_dist;
                            $returdet->reason = $reason;
                            $returdet->creator = session('user_id');

                            try{
                                // success
                                $returdet->save();
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

                    $jurnal_desc = "retur ".$id_jurnal." dari ".$purchase->jurnal_id;

                    //insert debet hutang Dagang
                    Jurnal::addJurnal($id_jurnal,$total_distributor,$purchase->tgl,$jurnal_desc,'2.1.1','Debet',session('user_id'));
                    //insert credit Persediaan Barang Indent ( harga modal x qty )
                    Jurnal::addJurnal($id_jurnal,$total_modal,$purchase->tgl,$jurnal_desc,'1.1.4.1.1','Credit',session('user_id'));
                    if($total_tertahan < 0){
                        //insert debet Estimasi Bonus
                        Jurnal::addJurnal($id_jurnal,$total_tertahan,$purchase->tgl,$jurnal_desc,'1.1.3.4','Debet',session('user_id'));
                    }else{
                        //insert credit Estimasi Bonus
                        Jurnal::addJurnal($id_jurnal,$total_tertahan,$purchase->tgl,$jurnal_desc,'1.1.3.4','Credit',session('user_id'));
                    }

                    return redirect()->route('returbeli.index')->with('status', 'Data berhasil disimpan');
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

    public function updateReturReceive(Request $request, $id)
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

    public function showReturPembelian(Request $request)
    {
        $jenisretur = "pembelian";
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $retur = ReturPembelianDet::all();
        $purchase = Purchase::join('tblpotrxdet', 'tblpotrx.id', '=', 'tblpotrxdet.trx_id')->where('tblpotrx.month', $bulan)->where('tblpotrx.year', $tahun)->where('tblpotrx.approve', 1)->orderBy('tblpotrx.id','desc')->get();
        return view('retur.nota.ajxShow', compact('jenisretur','purchase','tahun','bulan', 'retur'));;
    }
}
