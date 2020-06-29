<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Log;
use App\Product;
use App\Konversi;
use App\KonversiDetail;
use App\Perusahaan;
use App\MenuMapping;
use App\PurchaseMap;

class KonversiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $konvers = Konversi::all();
        $page = MenuMapping::getMap(session('user_id'),"PRKV");
        return view('product.konversi.index', compact('konvers','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->ajax()){
            $supplier = Perusahaan::where('id',$request->supplier)->first();
            $products = Product::where('supplier',$request->supplier)->get();
            return response()->json(view('product.konversi.showProduct',compact('supplier','products'))->render());
        }else{
            $jenis = "create";
            if(session('role') == "Superadmin" || session('role') == "Direktur Utama"){
                $suppliers = Perusahaan::all();
            }else{
                $suppliers = PurchaseMap::where('employee_id',session('user_id'))->get();
            }

            return view('product.konversi.form', compact('jenis','suppliers'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function addKonversi(Request $request){
        $parent = $request->product_parent;
        $child = $request->product_child;
        $qtyParent = $request->qty_parent;
        $qtyChild = $request->qty_child;
        $count = $request->count+1;

        $productParent = Product::where('prod_id',$parent)->first();
        $productChild = Product::where('prod_id',$child)->first();

        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td>'.$count.'</td>
        <input type="hidden" name="detail[]" id="detail'.$count.'" value="baru">
        <td><input type="hidden" name="productParent[]" id="productParent'.$count.'" value="'.$parent.'">'.$parent.' - '.$productParent->name.'</td>
        <td><input type="number" name="qtyParent[]" value="'.$qtyParent.'" id="qty'.$count.'"></td>
        <td><style="align:center">=></style></td>
        <td><input type="hidden" name="productChild[]" id="productChild'.$count.'" value="'.$child.'">'.$child.' - '.$productChild->name.'</td>
        <td><input type="number" name="qtyChild[]" value="'.$qtyChild.'" id="qty'.$count.'"></td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
        );

        return response()->json($data);
    }

    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'productParent' => 'required|array',
            'productChild' => 'required|array',
            'qtyParent' => 'required|array',
            'qtyChild' => 'required|array',
            'supplierKonversi' => 'required',
            'count' => 'required',
            'gudang' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $konversi = new Konversi(array(
                // Informasi Pribadi
                'creator' => session('user_id'),
                'supplier' => $request->supplierKonversi,
                'keterangan' => $request->keterangan,
            ));
            // success
            try {
                $konversi->save();

                for ($i=0; $i < $request->count ; $i++) {
                    $konversiDetailParent = new KonversiDetail(array(
                        'konversi_id' => $konversi->id,
                        'product_id' => $request->productParent[$i],
                        'status' => 0,
                        'qty' => $request->qtyParent[$i],
                        'gudang_id' => $request->gudang,
                    ));
                    $konversiDetailParent->save();

                    $konversiDetailChild = new KonversiDetail(array(
                        'konversi_id' => $konversi->id,
                        'product_id' => $request->productChild[$i],
                        'status' => 1,
                        'qty' => $request->qtyChild[$i],
                        'gudang_id' => $request->gudang,
                    ));
                    $konversiDetailChild->save();
                }

                Log::setLog('PRKVC','Create Konversi: .'.$konversi->id);
                return redirect()->route('konversi.index')->with('status', 'Data Konversi berhasil dibuat');

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
            $details = KonversiDetail::where('konversi_id',$request->id)->get();

            return response()->json(view('product.konversi.modal',compact('details'))->render());
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
        $konversi = Konversi::where('id',$id)->first();
        $products = Product::where('supplier',$konversi->supplier)->get();
        $details = KonversiDetail::where('konversi_id',$id)->get();

        return view('product.konversi.form_update', compact('konversi','products','details'));
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
            'productParent' => 'required|array',
            'productChild' => 'required|array',
            'qtyParent' => 'required|array',
            'qtyChild' => 'required|array',
            'supplierKonversi' => 'required',
            'count' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try {
                $konversi = Konversi::where('id',$id)->first();

                $konversi->keterangan = $request->keterangan;
                $konversi->creator = session('user_id');
                $konversi->update();

                for ($i=0; $i < $request->count ; $i++) {
                    if($request->detail[$i] == "baru"){
                        $konversiDetailParent = new KonversiDetail(array(
                            'konversi_id' => $konversi->id,
                            'product_id' => $request->productParent[$i],
                            'status' => 0,
                            'qty' => $request->qtyParent[$i],
                            'gudang_id' => $request->gudang,
                        ));
                        $konversiDetailParent->save();

                        $konversiDetailChild = new KonversiDetail(array(
                            'konversi_id' => $konversi->id,
                            'product_id' => $request->productChild[$i],
                            'status' => 0,
                            'qty' => $request->qtyChild[$i],
                            'gudang_id' => $request->gudang,
                        ));
                        $konversiDetailChild->save();
                    }
                }

                Log::setLog('PRKVU','Update Konversi: .'.$konversi->id);
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
        try {
            Konversi::where('id',$id)->delete();
            Log::setLog('PRKVD','Delete Konversi: .'.$id);
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function destroyKonversiDetail(Request $request){
        KonversiDetail::where('id',$request->parent)->delete();
        KonversiDetail::where('id',$request->child)->delete();
        return "true";
    }
}
