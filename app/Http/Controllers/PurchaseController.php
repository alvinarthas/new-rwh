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
            'month' => 'required',
            'year' => 'required',
            'supplier' => 'required',
            'tgl' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $purchase = new Purchase(array(
                // Informasi Pribadi
                'month' => $request->month,
                'year' => $request->year,
                'creator' => session('user_id'),
                'supplier' => $request->supplier,
                'notes' => $request->notes,
                'tgl' => $request->tgl,
                'approve' => 0,
            ));
            // success
            try {
                $purchase->save();

            } catch (\Exception $e) {
                //throw $th;
            }
            // if(){
            //     return redirect()->route('purchase.index')->with('status', 'Data berhasil dibuat');
            // // fail
            // }else{
            //     return redirect()->back()->withErrors($e);
            // }
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
        $purchase = Purchase::where('trx_id',$id)->first();
        $jenis = "edit";

        return view('perusahaan.form', compact('jenis','purchase'));
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
            'month' => 'required',
            'year' => 'required',
            'supplier' => 'required',
            'tgl' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $purchase = new Purchase(array(
                // Informasi Pribadi
                'month' => $request->month,
                'year' => $request->year,
                'creator' => session('user_id'),
                'supplier' => $request->supplier,
                'notes' => $request->notes,
                'tgl' => $request->tgl,
                'approve' => 0,
            ));
            // success
            if($purchase->save()){
                return redirect()->route('purchase.index')->with('status', 'Data berhasil diubah');
            // fail
            }else{
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
