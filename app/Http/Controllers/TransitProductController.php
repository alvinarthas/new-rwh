<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\TransitProduct;
use App\TransitProductDetail;
use App\Product;
use App\Gudang;
use App\MenuMapping;
use App\Log;

class TransitProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transits = TransitProduct::all();
        $page = MenuMapping::getMap(session('user_id'),"PRTS");
        return view('product.transit.index', compact('transits','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->ajax()){
            $productInit = $request->product_init;
            $gudangAwalInit = $request->gudang_asal;
            $gudangAkhirInit = $request->gudang_tujuan;
            $qty = $request->qty_init;
            $count = $request->count+1;

            $product = Product::where('prod_id',$productInit)->first();
            $gudangAwal = Gudang::where('id',$gudangAwalInit)->first();
            $gudangAkhir = Gudang::where('id',$gudangAkhirInit)->first();

            $append = '<tr style="width:100%" id="trow'.$count.'">
            <td>'.$count.'</td>
            <input type="hidden" name="detail[]" id="detail'.$count.'" value="baru">
            <td><input type="hidden" name="product[]" id="product'.$count.'" value="'.$productInit.'">'.$productInit.' - '.$product->name.'</td>
            <td><input type="number" name="qty[]" value="'.$qty.'" id="qty'.$count.'"></td>
            <td><input type="hidden" name="gudangStart[]" id="gudangStart'.$count.'" value="'.$gudangAwalInit.'">'.$gudangAwal->nama.'</td>
            <td style="align:center">=></td>
            <td><input type="hidden" name="gudangEnd[]" id="gudangEnd'.$count.'" value="'.$gudangAkhirInit.'">'.$gudangAkhir->nama.'</td>
            <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
            </tr>';

            $data = array(
                'append' => $append,
                'count' => $count,
            );

            return response()->json($data);

        }else{
            $products = Product::select('prod_id','name')->get();
            $gudangs = Gudang::select('id','nama')->get();
            $jenis = "create";
            return view('product.transit.form', compact('products','gudangs','jenis'));
        }
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
            'product' => 'required|array',
            'gudangStart' => 'required|array',
            'gudangEnd' => 'required|array',
            'qty' => 'required|array',
            'tanggal' => 'required',
            'count' => 'required',
            'keterangan' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $transit = new TransitProduct(array(
                // Informasi Pribadi
                'creator' => session('user_id'),
                'tgl' => $request->tanggal,
                'keterangan' => $request->keterangan,
            ));
            // success
            try {
                $transit->save();

                for ($i=0; $i < $request->count ; $i++) {
                    $transitDetail = new TransitProductDetail(array(
                        'transit_id' => $transit->id,
                        'product_id' => $request->product[$i],
                        'gudang_awal' => $request->gudangStart[$i],
                        'qty' => $request->qty[$i],
                        'gudang_akhir' => $request->gudangEnd[$i],
                    ));
                    $transitDetail->save();
                }

                Log::setLog('PRTSC','Create Transit Product: .'.$transit->id);
                return redirect()->route('transit.index')->with('status', 'Data Transit berhasil dibuat');

            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
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
        if($request->ajax()){
            $details = TransitProductDetail::where('transit_id',$request->id)->get();

            return response()->json(view('product.transit.modal',compact('details'))->render());
        }
    }

    public function getAvailableGudang(Request $request){
        $product = $request->product;
        $append = '<option value="#" disabled selected> Pilih Gudang Asal</option>';

        foreach(Gudang::select('id','nama')->get() as $key){
            $totalGudang = Product::getGudang($product,$key->id);

            if($totalGudang > 0){
                $append.='<option value="'.$key->id.'">'.$key->nama.'</option>';
            }
        }

        return response()->json($append);
    }

    public function getGudangTotal(Request $request){
        $product = $request->product;
        $gudang = $request->gudang;
        $totalGudang = Product::getGudang($product,$gudang);

        return response()->json($totalGudang);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $transit = TransitProduct::where('id',$id)->first();
        $products = Product::select('prod_id','name')->get();
        $gudangs = Gudang::select('id','nama')->get();
        $details = TransitProductDetail::where('transit_id',$id)->get();
        $jenis = "edit";

        return view('product.transit.form', compact('transit','products','details','gudangs','jenis'));
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
            'product' => 'required|array',
            'gudangStart' => 'required|array',
            'gudangEnd' => 'required|array',
            'qty' => 'required|array',
            'tanggal' => 'required',
            'count' => 'required',
            'keterangan' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            // success
            try {
                $transit = TransitProduct::where('id',$id)->first();

                $transit->keterangan = $request->keterangan;
                $transit->creator = session('user_id');
                $transit->tgl = $request->tanggal;
                $transit->update();

                for ($i=0; $i < $request->count ; $i++) {
                    if($request->detail[$i] == "baru"){
                        $transitDetail = new TransitProductDetail(array(
                            'transit_id' => $transit->id,
                            'product_id' => $request->product[$i],
                            'gudang_awal' => $request->gudangStart[$i],
                            'qty' => $request->qty[$i],
                            'gudang_akhir' => $request->gudangEnd[$i],
                        ));
                        $transitDetail->save();
                    }

                }

                Log::setLog('PRTSU','Update Transit Product: .'.$transit->id);
                return redirect()->route('transit.index')->with('status', 'Data Transit berhasil diubah');

            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
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
        try {
            TransitProduct::where('id',$id)->delete();
            Log::setLog('PRTSD','Delete Konversi: .'.$id);
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function destroyTransitDetail(Request $request){
        TransitProductDetail::where('id',$request->id)->delete();
        return "true";
    }
}
