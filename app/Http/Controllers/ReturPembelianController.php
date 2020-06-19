<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Jurnal;
use App\Purchase;
use App\PurchaseDetail;
use App\Retur;
use App\ReturPayment;
use App\ReturDetail;
use App\ReturStock;
use Carbon\Carbon;
use App\MenuMapping;
use App\Coa;
use App\Log;

class ReturPembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $retur = Retur::where('status', 0)->get();
        $page = MenuMapping::getMap(session('user_id'),"RBPO");
        return view('retur.pembelian.nota.index', compact('retur', 'page'));
    }

    public function indexReturPayment()
    {
        $retur = Retur::getReturPay(0);
        $page = MenuMapping::getMap(session('user_id'),"RBPP");
        return view('retur.pembelian.payment.index', compact('retur', 'page'));
    }

    public function indexReturReceive()
    {
        $retur = Retur::getReturStock(0);
        $page = MenuMapping::getMap(session('user_id'),"RBRP");
        return view('retur.pembelian.stock.index', compact('retur', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('retur.pembelian.nota.indexCreate');
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
            $retur = Retur::where('status', 0)->where('id', $request->id)->first();
            $po_trx = Purchase::where('jurnal_id', $retur->source_id)->first()->id;
            $returdet = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.status', 0)->where('trx_id', $request->id)->get();

            return response()->json(view('retur.pembelian.nota.modal',compact('retur','returdet', 'po_trx'))->render());
        }
    }

    public function showReturPayment($id, Request $request)
    {
        //
    }

    public function showReturReceive(Request $request)
    {
        if ($request->ajax()) {
            $jurnal_id = $request->ri_id;
            $receives = ReturStock::where('id_jurnal',$jurnal_id)->where('status', 0)->get();
            $receive = ReturStock::where('id_jurnal',$jurnal_id)->where('status', 0)->first();

            return response()->json(view('retur.pembelian.stock.modal',compact('receive', 'receives'))->render());
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
        $purchase = Purchase::where('id', $id)->first();
        $purchasedet = PurchaseDetail::where('trx_id', $id)->get();
        $retur = ReturDetail::getRetured(0, $purchase->jurnal_id, $id);
        $trx_id = $id;

        return view('retur.pembelian.nota.form', compact('purchasedet', 'purchase', 'retur', 'trx_id'));
    }

    public function editReturPayment($id)
    {
        $retur = Retur::where('id', $id)->first();
        $details = ReturDetail::where('trx_id', $id)->get();
        $ttl_pay = ReturPayment::where('trx_id',$id)->sum('amount');
        $ttl_order = ReturDetail::where('trx_id',$id)->sum(DB::raw('qty * harga_dist'));
        $coas = Coa::where('StatusAccount','Detail')->where('AccNo','LIKE','1.1.1.2.%')->orWhere('AccNo','LIKE','1.1.1.1.%')->orWhere('AccNo','LIKE','2.5%')->orWhere('AccNo','LIKE','1.10.%')->orderBy('AccName','asc')->get();
        $payment = ReturPayment::where('trx_id', $id)->get();

        return view('retur.pembelian.payment.form', compact('retur', 'details', 'ttl_pay', 'ttl_order', 'coas', 'payment'));
    }

    public function editReturReceive($id)
    {
        $trx = Retur::where('id',$id)->first();
        $details = ReturStock::detailRetur($id, 0);
        $productretur = ReturDetail::where('trx_id',$id)->select('prod_id')->get();
        $page = MenuMapping::getMap(session('user_id'),"RBRP");
        $receives = ReturStock::where('trx_id',$id)->groupBy('id_jurnal')->get();

        return view('retur.pembelian.stock.form',compact('trx','details','productretur','receives','page'));
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

                $purchase = Purchase::where('id',$id)->first();

                $retur = new Retur;
                $retur->tgl = $tgl;
                $retur->supplier = $request->supplier;
                $retur->id_jurnal = $id_jurnal;
                $retur->source_id = $purchase->jurnal_id;
                $retur->status = 0;
                $retur->creator = session('user_id');
                $ctr = count($request->qtyretur);

                $total_modal = 0;
                $total_tertahan = 0;
                $total_distributor = 0;

                if($retur->save()){
                    for($i=0;$i<$ctr;$i++){
                        $qty = $request->qtyretur[$i];
                        $reason = $request->reason[$i];
                        $prod_id = $request->prod_id[$i];
                        $price = $request->harga[$i];
                        $price_dist = $request->harga_dist[$i];
                        $unit = $request->unit[$i];

                        if($qty!=0){
                            $returdet = new ReturDetail;
                            $returdet->trx_id = $retur->id;
                            $returdet->prod_id = $prod_id;
                            $returdet->qty = $qty;
                            $returdet->unit = $unit;
                            $returdet->harga = $price;
                            $returdet->harga_dist = $price_dist;
                            $returdet->reason = $reason;
                            $returdet->creator = session('user_id');

                            try{
                                // success
                                $returdet->save();
                                $total_modal += $price * $qty;
                                $total_tertahan += PurchaseDetail::where('trx_id',$id)->where('prod_id', $prod_id)->sum(DB::Raw('(price_dist - price)*'.$qty));
                                $total_distributor += $price_dist * $qty;
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
        // Validate
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required',
            'prod_id' => 'required|array',
            'qty' => 'required|array',
            'receive_date' => 'required|date',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $id_jurnal = Jurnal::getJurnalID('RD');
                $price = 0;
                $count = count($request->prod_id);
                $retur = Retur::where('id', $request->trx_id)->first();

                for($i=0; $i < $count; $i++){
                    $pricedet = ReturDetail::where('trx_id',$request->trx_id)->where('prod_id',$request->prod_id[$i])->first()->harga;
                    $price += $pricedet * $request->qty[$i];
                }

                $desc = "Retur Receive Product dari ".$retur->source_id.", Retur ID : ".$retur->id_jurnal;

                // JURNAL
                // insert credit Persediaan Barang di Gudang
                Jurnal::addJurnal($id_jurnal,$price,$request->receive_date,$desc,'1.1.4.1.2','Credit');
                // insert debet Persediaan Barang Indent
                Jurnal::addJurnal($id_jurnal,$price,$request->receive_date,$desc,'1.1.4.1.1','Debet');

                for($i=0; $i < $count; $i++){
                    $receive = new ReturStock(array(
                        'trx_id' => $request->trx_id,
                        'prod_id' => $request->prod_id[$i],
                        'qty' => $request->qty[$i],
                        'date' => $request->receive_date,
                        'creator' => session('user_id'),
                        'status' => 0,
                        'id_jurnal' => $id_jurnal,
                    ));

                    $receive->save();
                }

                Log::setLog('RBRPC','Create Retur Receive Product dari '.$retur->source_id.' Jurnal ID: '.$id_jurnal);
                return redirect()->back()->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
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
            Jurnal::where('id_jurnal',$data->id_jurnal)->delete();
            ReturPayment::where('id', $request->id)->delete();

            return "true";
        }catch(\Exception $a){
            return response()->json($a);
        }
    }

    public function destroyReturReceive(Request $request){
        $id_jurnal = $request->jurnal_id;
        try {
            // Jurnal::where('id_jurnal',$id_jurnal)->delete();
            ReturStock::where('id_jurnal', $id_jurnal)->delete();
            Log::setLog('RBRPD','Delete Retur Receive Product Jurnal ID: '.$id_jurnal);
            return "true";
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e);
        }
    }

    public function showReturPembelian(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $purchase = Purchase::join('tblpotrxdet', 'tblpotrx.id', '=', 'tblpotrxdet.trx_id')->where('tblpotrx.month', $bulan)->where('tblpotrx.year', $tahun)->where('tblpotrx.approve', 1)->orderBy('tblpotrx.id','desc')->get();

        return view('retur.pembelian.nota.ajxShow', compact('purchase','tahun','bulan'));
    }
}
