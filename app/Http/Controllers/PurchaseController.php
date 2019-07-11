<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Purchase;
use App\PurchaseDetail;
use App\Perusahaan;
use App\ManageHarga;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('purchase.index');
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
        if ($request->ajax()) {
            return response()->json(view('purchase.indexpurchase',compact('purchases'))->render());
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
        $suppliers = Perusahaan::all(); 
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
            ));
            // success
            try {
                $purchase->save();
                // insert Detail
                for ($i=0; $i < $request->count ; $i++) { 
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

                return redirect()->route('purchase.index')->with('status', 'Data berhasil dibuat');

            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
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
        //
    }
}
