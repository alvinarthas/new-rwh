<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Excel;
use App\Exports\POExport;

use App\Purchase;
use App\PurchaseMap;
use App\PurchaseDetail;
use App\Perusahaan;
use App\PurchasePayment;
use App\ManageHarga;
use App\Jurnal;
use App\MenuMapping;
use App\ReceiveDet;
use App\Product;
use App\Log;
use App\TempPO;
use App\TempPODet;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = MenuMapping::getMap(session('user_id'),"PUPU");
        return view('purchase.index',compact('page'));
    }

    // HELPER

    public function ajxView(Request $request){
        $purchase = Purchase::where('month',$request->month)->where('year',$request->year)->get();

        if ($request->ajax()) {
            return response()->json(view('purchase.view',compact('purchase'))->render());
        }
    }

    public function showPurchase(Request $request){
        $supplier = Perusahaan::where('id',$request->supplier)->first();
        $month = $request->bulan;
        $year = $request->tahun;
        $products = Product::where('supplier',$request->supplier)->get();
        if ($request->ajax()) {
            return response()->json(view('purchase.showpurchase',compact('supplier','month','year','products'))->render());
        }
    }

    public function showIndexPurchaseOld(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if($request->param == "all"){
            $purchases = Purchase::orderBy('created_at','desc')->get();
        }else{
            $purchases = Purchase::where('month',$bulan)->where('year',$tahun)->orderBy('created_at','desc')->get();
        }

        $page = MenuMapping::getMap(session('user_id'),"PUPU");
        if ($request->ajax()) {
            return response()->json(view('purchase.indexpurchase',compact('purchases','page', 'bulan', 'tahun'))->render());
        }
    }

    public function showIndexPurchase(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $param = $request->param;

        if ($request->ajax()) {
            return response()->json(view('purchase.viewpurchase',compact('param', 'bulan', 'tahun'))->render());
        }
    }

    public function purchaseData(Request $request){
        if($request->ajax()){
            $datas = Purchase::purchaseData($request);
            echo json_encode($datas);
        }
    }

    public function addPurchase(Request $request){
        $product = $request->select_product;
        $qty = $request->qty;
        $unit = $request->unit;
        $count = $request->count+1;
        $month = $request->bulan;
        $year = $request->tahun;

        $manage = Product::where('prod_id',$request->select_product)->first();
        $sub_ttl_dist = $qty*$manage->harga_distributor;
        $sub_ttl_mod = $qty*$manage->harga_modal;

        $avcost = PurchaseDetail::avgCost($request->select_product);

        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td>'.$count.'</td>
        <input type="hidden" name="detail[]" id="detail'.$count.'" value="baru">
        <td><input type="hidden" name="prod_id[]" id="prod_id'.$count.'" value="'.$product.'">'.$product.'</td>
        <td><input type="hidden" name="prod_name[]" id="prod_name'.$count.'" value="'.$manage['name'].'">'.$manage['name'].'</td>
        <td><input type="number" name="qty[]" value="'.$qty.'" id="qty'.$count.'" onchange="changeTotal('.$count.')" onkeyup="changeTotal('.$count.')"></td>
        <td><input type="hidden" name="unit[]" value="'.$unit.'" id="unit'.$count.'">'.$unit.'</td>
        <td>Rp&nbsp;'.number_format($avcost,2,",",".").'</td>
        <td><input type="number" name="harga_dist[]" value="'.$manage['harga_distributor'].'" id="harga_dist'.$count.'" onkeyup="changeTotal('.$count.')"></td>
        <td><input type="number" name="harga_mod[]" value="'.$manage['harga_modal'].'" id="harga_mod'.$count.'" onkeyup="changeTotal('.$count.')"></td>
        <td><input type="number" readonly name="sub_ttl_dist[]" value="'.$sub_ttl_dist.'" id="sub_ttl_dist'.$count.'"></td>
        <td><input type="number" readonly name="sub_ttl_mod[]" value="'.$sub_ttl_mod.'" id="sub_ttl_mod'.$count.'"></td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
            'sub_ttl_dist' => $sub_ttl_dist,
            'sub_ttl_mod' => $sub_ttl_mod,
        );

        return response()->json($data);
    }

    public function destroyPurchaseDetail(Request $request){
        if($request->status == 1){
            $detail = TempPODet::where('id',$request->detail)->first();
            $purchase = TempPO::where('id',$detail->temp_id)->first();
            $purchase->total_harga_modal = $purchase->total_harga_modal - ($detail->price * $detail->qty);
            $purchase->total_harga_dist = $purchase->total_harga_dist - ($detail->qty * $detail->price_dist);
            $purchase->update();
            $detail->delete();
            Log::setLog('PUPUD','Delete  PO Temp Detail TEMP PO = .'.$purchase->id);
        }else{
            $detail = PurchaseDetail::where('id',$request->detail)->first();
            $purchase = Purchase::where('id',$detail->trx_id)->first();
            $purchase->total_harga_modal = $purchase->total_harga_modal - ($detail->price * $detail->qty);
            $purchase->total_harga_dist = $purchase->total_harga_dist - ($detail->qty * $detail->price_dist);
            $purchase->update();
            $detail->delete();

            if ($purchase->approve == 1) {
                // Recycle Purchase Jurnal
                Purchase::recylePurchase($purchase->id);

                // Recycle Receive Detail
                ReceiveDet::recycleRP($purchase->id,$request->detail);
            }
            Log::setLog('PUPUD','Delete  PO Detail PO = .'.$purchase->id);
        }

        return "true";
    }

    // END HELPER

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        if(session('role') == "Superadmin" || session('role') == "Direktur Utama"){
            $suppliers = Perusahaan::all();
        }else{
            $suppliers = PurchaseMap::where('employee_id',session('user_id'))->get();
        }

        return view('purchase.form', compact('jenis','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'bulanpost' => 'required',
            'tahunpost' => 'required',
            'supplierpost' => 'required',
            'count' => 'required',
            'po_date' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $purchase = new Purchase(array(
                // Informasi Pribadi
                'month' => $request->bulanpost,
                'year' => $request->tahunpost,
                'creator' => session('user_id'),
                'supplier' => $request->supplierpost,
                'notes' => $request->notes,
                'tgl' => $request->po_date,
                'approve' => 0,
                'total_harga_dist' => $request->ttl_harga_distributor,
                'total_harga_modal' => $request->ttl_harga_modal,
            ));
            // success
            try {
                $purchase->save();
                $date = date_format($purchase->created_at,"Y-m-d");
                $jurnal_desc = "PO.".$purchase->id;
                // insert Detail
                $total_modal=$request->ttl_harga_modal;
                $total_tertahan=0;
                $total_distributor=$request->ttl_harga_distributor;
                for ($i=0; $i < $request->count ; $i++) {
                    $selisih = $request->harga_mod[$i] - $request->harga_dist[$i];
                    $total_tertahan+=($selisih*$request->qty[$i]);

                    $purchasedet = new PurchaseDetail(array(
                        'trx_id' => $purchase->id,
                        'prod_id' => $request->prod_id[$i],
                        'qty' => $request->qty[$i],
                        'unit' => $request->unit[$i],
                        'creator' => session('user_id'),
                        'price' => $request->harga_mod[$i],
                        'price_dist' => $request->harga_dist[$i],
                    ));
                    $purchasedet->save();
                }

                // //insert debet Persediaan Barang Indent ( harga modal x qty )
                // Jurnal::addJurnal($id_jurnal,$total_modal,$request->po_date,$jurnal_desc,'1.1.4.1.1','Debet');
                // //insert debet Estimasi Bonus
                // Jurnal::addJurnal($id_jurnal,$total_tertahan,$request->po_date,$jurnal_desc,'1.1.3.4','Debet');
                // //insert credit hutang Dagang
                // Jurnal::addJurnal($id_jurnal,$total_distributor,$request->po_date,$jurnal_desc,'2.1.1','Credit');

                Log::setLog('PUPUC','Create PO.'.$purchase->id);
                return redirect()->route('purchase.index')->with('status', 'Data berhasil dibuat');

            } catch (\Exception $e) {
                // return response()->json($e);
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if ($request->ajax()) {
            $purchase = Purchase::where('id',$request->id)->first();
            $purchasedet = PurchaseDetail::where('trx_id',$request->id)->get();
            $purchasepay = PurchasePayment::where('trx_id',$request->id)->sum('payment_amount');

            $count = TempPO::where('purchase_id', $request->id)->count();

            if($count != 0){
                $temp_PO = TempPO::where('purchase_id', $request->id)->first();
                $temp_POdet = TempPODet::where('temp_id', $temp_PO->id)->get();
                return response()->json(view('purchase.modal',compact('purchase','purchasedet','purchasepay', 'temp_PO', 'temp_POdet'))->render());
            }else{
                return response()->json(view('purchase.modal',compact('purchase','purchasedet','purchasepay'))->render());
            }
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
        $count_temp = TempPO::where('purchase_id',$id)->count('purchase_id');
        $status_temp = TempPO::where('purchase_id',$id)->where('status',1)->count('purchase_id');
        $page = MenuMapping::getMap(session('user_id'),"PUPU");
        $supplier = Perusahaan::all();
        if($count_temp > 0 && $status_temp == 1){
            $status = 1;
            $purchase = TempPO::where('purchase_id',$id)->first();
            $details = TempPODet::where('temp_id',$purchase->id)->get();

            $ttl_harga_modal = 0;
            $ttl_harga_dist = 0;
            foreach ($details as $key) {
                $ttl_harga_modal += ($key->price*$key->qty);
                $ttl_harga_dist += ($key->price_dist*$key->qty);
            }

            $month = $purchase->month;
            $year = $purchase->year;
            $products = Product::where('supplier',$purchase->supplier()->first()->id)->get();
        }else{
            $status = 0;
            $purchase = Purchase::where('id',$id)->first();
            $details = PurchaseDetail::where('trx_id',$id)->get();

            $ttl_harga_modal = 0;
            $ttl_harga_dist = 0;
            foreach ($details as $key) {
                $ttl_harga_modal += ($key->price*$key->qty);
                $ttl_harga_dist += ($key->price_dist*$key->qty);
            }

            $month = $purchase->month;
            $year = $purchase->year;
            $products = Product::where('supplier',$purchase->supplier()->first()->id)->get();
        }


        return view('purchase.form_update', compact('details','purchase','products','ttl_harga_modal','ttl_harga_dist','status','page', 'supplier'));
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
        // dd($request->all());
        // Validate
        $validator = Validator::make($request->all(), [
            'bulanpost' => 'required',
            'tahunpost' => 'required',
            'supplierpost' => 'required',
            'count' => 'required',
            'po_date' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try {
                $check = TempPO::where('purchase_id',$id)->count();

                if($check > 0){
                    $temp_purchase = TempPO::where('purchase_id',$id)->first();
                    // Update and tranfer to Purchase Orginal
                    $temp_purchase->month = $request->bulanpost;
                    $temp_purchase->year = $request->tahunpost;
                    $temp_purchase->supplier = $request->supplierpost;
                    $temp_purchase->notes = $request->notes;
                    $temp_purchase->creator = session('user_id');
                    $temp_purchase->tgl = $request->po_date;
                    $temp_purchase->total_harga_modal = $request->ttl_harga_modal;
                    $temp_purchase->total_harga_dist = $request->ttl_harga_distributor;

                    $temp_purchase->update();
                    // Delete Temp Detail
                    $temp_purdet = TempPODet::where('temp_id',$temp_purchase->id)->delete();
                }else{
                    $temp_purchase = new TempPO(array(
                        // Informasi Pribadi
                        'purchase_id' => $id,
                        'month' => $request->bulanpost,
                        'year' => $request->tahunpost,
                        'creator' => session('user_id'),
                        'supplier' => $request->supplierpost,
                        'notes' => $request->notes,
                        'tgl' => $request->po_date,
                        'status' => 1,
                        'total_harga_dist' => $request->ttl_harga_distributor,
                        'total_harga_modal' => $request->ttl_harga_modal,
                    ));
                    // success

                    $temp_purchase->save();
                }
                for ($i=0; $i < $request->count ; $i++) {
                    $temp_po_det = new TempPODet(array(
                        'temp_id' => $temp_purchase->id,
                        'purchasedetail_id' => $request->detail[$i],
                        'prod_id' => $request->prod_id[$i],
                        'qty' => $request->qty[$i],
                        'unit' => $request->unit[$i],
                        'creator' => session('user_id'),
                        'price' => $request->harga_mod[$i],
                        'price_dist' => $request->harga_dist[$i],
                    ));
                    $temp_po_det->save();
                }
                Log::setLog('PUPUU','Update PO.'.$id);
                return redirect()->back()->with('status', 'Data berhasil diubah');
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
        $purchase = Purchase::where('id',$id)->first();
        $prodarray = collect();
        foreach (PurchaseDetail::where('trx_id',$id)->get() as $key) {
            $prodarray->push($key->prod_id);
        }
        // Receive Item
        foreach (ReceiveDet::where('trx_id',$id)->select('id_jurnal')->get() as $key) {
            Jurnal::where('id_jurnal',$key->id_jurnal)->delete();
        }

        // Purchase Payment
        foreach (PurchasePayment::where('trx_id',$id)->select('jurnal_id')->get() as $key) {
            Jurnal::where('id_jurnal',$key->jurnal_id)->delete();
        }
        Jurnal::where('id_jurnal',$purchase->jurnal_id)->delete();
        try {
            $purchase->delete();
            Jurnal::refreshCogs($prodarray);
            Log::setLog('PUPUD','Delete PO.'.$id);
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function export(Request $request)
    {
        ini_set('max_execution_time', 3000);

        $tgl = date('Y-m-d', strtotime(Carbon::today()));
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $monthname = date("F", mktime(0, 0, 0, $bulan, 10));


        if($bulan != "" && $tahun != ""){
            $filename = "Daftar Pemesanan bulan bonus ".$monthname." ".$tahun." (".$tgl.")";
            $purchase = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->join('tblperusahaan', 'tblpotrx.supplier', 'tblperusahaan.id')->where('tblpotrx.month',$bulan)->where('tblpotrx.year', $tahun)->select('tblpotrx.tgl', 'tblpotrx.id', 'tblperusahaan.nama', 'tblpotrx.month', 'tblpotrx.year', 'tblpotrxdet.prod_id', 'tblpotrxdet.price', 'tblpotrxdet.price_dist', 'tblpotrxdet.qty', 'tblpotrxdet.unit')->orderBy('tblpotrx.tgl','asc')->get();
        }else{
            $filename = "Daftar Pemesanan (".$tgl.")";
            $purchase = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->join('tblperusahaan', 'tblpotrx.supplier', 'tblperusahaan.id')->select('tblpotrx.tgl', 'tblpotrx.id', 'tblperusahaan.nama', 'tblpotrx.month', 'tblpotrx.year', 'tblpotrxdet.prod_id', 'tblpotrxdet.price', 'tblpotrxdet.price_dist', 'tblpotrxdet.qty', 'tblpotrxdet.unit')->orderBy('tblpotrx.tgl','asc')->get();
        }

        $data = array();
        $no = 0;

        foreach($purchase as $p){
            $trx_id = "PO".$p->id;
            $trx_date = $p->tgl;
            $supplier = $p->nama;
            $namabulan = date("F", mktime(0, 0, 0, $p->month, 10));
            $periode = $namabulan." ".$p->year;
            $prod_id = $p->product()->first()->prod_id;
            $prod_name = $p->product()->first()->name;
            $price = $p->price;
            $price_dist = $p->price_dist;
            $qty = $p->qty;
            $unit = $p->unit;
            $total_price = $price * $qty;
            $total_price_dist = $price_dist * $qty;
            $no++;

            $array = array(
                // Data Purchase
                'No' => $no,
                'Transaction ID' => $trx_id,
                'Transaction Date' => $trx_date,
                'Supplier' => $supplier,
                'Bulan Bonus' => $periode,
                'Product ID' => $prod_id,
                'Product Name' => $prod_name,
                'Price' => $price,
                'Price Dist' => $price_dist,
                'Qty' => $qty,
                'Unit' => $unit,
                'Sub Total Price' => $total_price,
                'Sub Total Price Dist' => $total_price_dist,
            );

            array_push($data, $array);
        }

        $export = new POExport($data);

        return Excel::download($export, $filename.'.xlsx');

    }
}
