<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Purchase;
use App\PurchaseMap;
use App\PurchaseDetail;
use App\Perusahaan;
use App\ManageHarga;
use App\Jurnal;
use App\MenuMapping;

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
        $products = ManageHarga::showHarga($supplier->id,$month,$year);
        if ($request->ajax()) {
            return response()->json(view('purchase.showpurchase',compact('supplier','month','year','products'))->render());
        }
    }

    public function showIndexPurchase(Request $request){
        $purchases = Purchase::where('month',$request->bulan)->where('year',$request->tahun)->orderBy('created_at','desc')->get();
        $page = MenuMapping::getMap(session('user_id'),"PUPU");
        if ($request->ajax()) {
            return response()->json(view('purchase.indexpurchase',compact('purchases','page'))->render());
        }
    }

    public function addPurchase(Request $request){
        $product = $request->select_product;
        $qty = $request->qty;
        $unit = $request->unit;
        $count = $request->count+1;
        $month = $request->bulan;
        $year = $request->tahun;

        $manage = ManageHarga::showProduct($product,$month,$year);

        $sub_ttl_dist = $qty*$manage->harga_distributor;
        $sub_ttl_mod = $qty*$manage->harga_modal;

        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td>'.$count.'</td>
        <td><input type="hidden" name="prod_id[]" id="prod_id'.$count.'" value="'.$manage->prod_id.'">'.$manage->prod_id.'</td>
        <td><input type="hidden" name="prod_name[]" id="prod_name'.$count.'" value="'.$manage->name.'">'.$manage->name.'</td>
        <td><input type="hidden" name="qty[]" value="'.$qty.'" id="qty'.$count.'">'.$qty.'</td>
        <td><input type="hidden" name="unit[]" value="'.$unit.'" id="unit'.$count.'">'.$unit.'</td>
        <td><input type="hidden" name="harga_dist[]" value="'.$manage->harga_distributor.'" id="harga_dist'.$count.'">Rp. '.number_format($manage->harga_distributor).'</td>
        <td><input type="hidden" name="harga_mod[]" value="'.$manage->harga_modal.'" id="harga_mod'.$count.'">Rp. '.number_format($manage->harga_modal).'</td>
        <td><input type="hidden" name="sub_ttl_dist[]" value="'.$sub_ttl_dist.'" id="sub_ttl_dist'.$count.'">Rp. '.number_format($sub_ttl_dist).'</td>
        <td><input type="hidden" name="sub_ttl_mod[]" value="'.$sub_ttl_mod.'" id="sub_ttl_mod'.$count.'">Rp. '.number_format($sub_ttl_mod).'</td>
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
        $detail = PurchaseDetail::where('id',$request->detail)->first();
        $detail->delete();
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
            $id_jurnal = Jurnal::getJurnalID('PO');
            $purchase = new Purchase(array(
                // Informasi Pribadi
                'month' => $request->bulanpost,
                'year' => $request->tahunpost,
                'creator' => session('user_id'),
                'supplier' => $request->supplierpost,
                'notes' => $request->notes,
                'tgl' => $request->po_date,
                'approve' => 0,
                'jurnal_id' => $id_jurnal,
            ));
            // success
            try {
                $purchase->save();
                $date = date_format($purchase->created_at,"Y-m-d");
                $jurnal_desc = "PO.".$purchase->id;
                // insert Detail
                $total_modal=0;
                $total_tertahan=0;
                $total_distributor=0;
                for ($i=0; $i < $request->count ; $i++) {
                    $selisih = $request->harga_mod[$i] - $request->harga_dist[$i];
                    $total_modal += ($request->harga_mod[$i] * $request->qty[$i]);
                    $total_tertahan+=($selisih*$request->qty[$i]);
                    $total_distributor+=($request->harga_dist[$i]*$request->qty[$i]);

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

                //insert debet Persediaan Barang Indent ( harga modal x qty )
                Jurnal::addJurnal($id_jurnal,$total_modal,$request->po_date,$jurnal_desc,'1.1.4.1.1','Debet');
                //insert debet Estimasi Bonus
                Jurnal::addJurnal($id_jurnal,$total_tertahan,$request->po_date,$jurnal_desc,'1.1.3.4','Debet');
                //insert credit hutang Dagang
                Jurnal::addJurnal($id_jurnal,$total_distributor,$request->po_date,$jurnal_desc,'2.1.1','Credit');

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
    public function show($id)
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
        $products = ManageHarga::showHarga($purchase->supplier()->first()->id,$month,$year);

        return view('purchase.form_update', compact('details','purchase','products','ttl_harga_modal','ttl_harga_dist'));
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
        // Validate
        $validator = Validator::make($request->all(), [
            'count' => 'required',
            'po_date' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $purchase = Purchase::where('id',$id)->first();

            $purchase->creator = session('user_id');
            $purchase->notes = $request->notes;
            $purchase->tgl = $request->po_date;
            $purchase->approve = 0;

            // success
            try {

                $purchase->update();
                // insert Detail
                for ($i=0; $i < $request->count ; $i++) {
                    
                    if(isset($request->prod_id[$i])){
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
                }

                // Penjurnalan
                $total_modal=0;
                $total_tertahan=0;
                $total_distributor=0;
                foreach (PurchaseDetail::where('trx_id',$id)->get() as $key) {
                    $selisih = $key->price - $key->price_dist;
                    $total_modal += ($key->price * $key->qty);
                    $total_tertahan+=($selisih*$key->qty);
                    $total_distributor+=($key->price_dist*$key->qty);
                }
                //Update debet Persediaan Barang Indent ( harga modal x qty )
                    $jurnal1 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','1.1.4.1.1')->first();
                    $jurnal1->Amount = $jurnal1->Amount+$total_modal;
                    $jurnal1->date = $request->po_date;
                    $jurnal1->update();
                //Update debet Estimasi Bonus
                    $jurnal2 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','1.1.3.4')->first();
                    $jurnal2->Amount = $jurnal2->Amount+$total_tertahan;
                    $jurnal2->date = $request->po_date;
                    $jurnal2->update();
                //Update credit hutang Dagang
                    $jurnal3 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','2.1.1')->first();
                    $jurnal3->Amount = $jurnal2->Amount+$total_distributor;
                    $jurnal3->date = $request->po_date;
                    $jurnal3->update();
                
                
                return redirect()->route('purchase.index')->with('status', 'Data berhasil dibuat');

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
        Jurnal::where('id_jurnal',$purchase->jurnal_id)->delete();
        try {
            $purchase->delete();
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
