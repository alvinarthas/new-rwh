<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Product;
use App\Perusahaan;
use App\Company;
use App\MenuMapping;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        $page = MenuMapping::getMap(session('user_id'),"MDPD");
        return view('product.index', compact('products','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companys = Company::all();
        $perusahaans = Perusahaan::all();
        $jenis = "create";
        return view('product.form', compact('companys','perusahaans','jenis'));
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
            'company_id' => 'required',
            'prod_id' => 'required|string|unique:tblproduct',
            'name' => 'required|string',
            'category' => 'required|string',
            'supplier' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $product = new Product(array(
                // Informasi Pribadi
                'company_id' => $request->company_id,
                'name' => $request->name,
                'prod_id' => $request->prod_id,
                'category' => $request->category,
                'supplier' => $request->supplier,
                'stock' => 0
            ));
            // success
            if($product->save()){
                return redirect()->route('product.index')->with('status', 'Data berhasil dibuat');
            // fail
            }else{
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
        $product = Product::find($id);
        $companys = Company::all();
        $perusahaans = Perusahaan::all();
        $jenis = "edit";
        return view('product.form', compact('companys','perusahaans','jenis', 'product'));
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
            'company_id' => 'required',
            'prod_id' => 'required|string',
            'name' => 'required|string',
            'category' => 'required|string',
            'supplier' => 'required',
            'stock' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $product = Product::find($id);
            // Informasi Pribadi
            $product->company_id = $request->company_id;
            $product->name = $request->name;
            $product->prod_id = $request->prod_id;
            $product->category = $request->category;
            $product->supplier = $request->supplier;
            $product->stock = $request->stock;
            $product->prod_id_new = $request->prod_id_new;
            // success
            if($product->update()){
                return redirect()->route('product.index')->with('status', 'Data berhasil disimpan');
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
        $product = Product::find($id);
        if($product->delete()){
            return redirect()->route('product.index')->with('status', 'Data berhasil dihapus');
        // fail
        }else{
            return redirect()->back()->withErrors($e);
        }
    }

    public function manage()
    {
        $products = Product::all();

        return view('product.manageharga', compact('products'));
    }

    public function showProdAjx(Request $request)
    {
        $month = date("m", strtotime($request->date));
        $year = date('Y', strtotime($request->date));
        $prods = Product::all();
        $i = 0;

        return view('product.showProdAjxLog', compact('prods','month','year','i'));
    }
}
