<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Exports\PriceDetExport;
use App\Exports\PriceDetExport2;
use App\Customer;
use App\Product;
use App\PriceDet;
use App\MenuMapping;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = MenuMapping::getMap(session('user_id'),"CRCS");
        $jenis = "customer";
        $customers = Customer::select('id', 'apname', 'cid' ,'apphone', 'cicn' , 'ciphone')->get();
        return view('customer.index', compact('customers', 'jenis', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        return view('customer.form', compact('jenis'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string',
            'name' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $customer = new Customer(array(
                    'cid' => $request->customer_id,
                    'apname' => $request->name,
                    'apphone' => $request->phone,
                    'apfax' => $request->fax,
                    'apemail' => $request->email,
                    'apadd' => $request->address,
                    'cicn' => $request->cname,
                    'ciadd' => $request->cadd,
                    'cicty' => $request->ccity,
                    'cizip' => $request->czipcode,
                    'cipro' => $request->cprovince,
                    'ciweb' => $request->cwebsite,
                    'ciemail' => $request->cemail,
                    'ciphone' => $request->cphone,
                    'cifax' => $request->cfax,
                    'creator' => session('user_id'),
                ));
                $customer->save();
                return redirect()->route('customer.index')->with('status','Data berhasil disimpan');
            }catch(\Exception $e){
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
        $customer = Customer::where('id', $id)->first();
        $jenis = "edit";
        return view('customer.form', compact('jenis', 'customer'));
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
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string',
            'name' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $customer = Customer::where('id', $id)->first();
                $customer->cid = $request->customer_id;
                $customer->apname = $request->name;
                $customer->apphone = $request->phone;
                $customer->apfax = $request->fax;
                $customer->apemail = $request->email;
                $customer->apadd = $request->address;
                $customer->cicn = $request->cname;
                $customer->ciadd = $request->cadd;
                $customer->cicty = $request->ccity;
                $customer->cizip = $request->czipcode;
                $customer->cipro = $request->cprovince;
                $customer->ciweb = $request->cwebsite;
                $customer->ciemail = $request->cemail;
                $customer->ciphone = $request->cphone;
                $customer->cifax = $request->cfax;
                $customer->creator = session('user_id');

                $customer->save();
                return redirect()->route('customer.index')->with('status','Perubahan data berhasil disimpan');
            }catch(\Exception $e){
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
    public function destroy(Request $request)
    {
        try{
            $customer = Customer::where('id', $request->id)->first();
            $customer->delete();
            return response()->json();
            // return redirect()->route('customer.index')->with('status','Data berhasil dihapus');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function priceBV($id)
    {
        $customer = Customer::where('id', $id)->first();
        // $product = Product::join('tblperusahaan','tblproduct.supplier', 'tblperusahaan.id')->select('tblproduct.name','tblperusahaan.nama AS namasupplier', 'prod_id', 'category')->get();
        $product = PriceDet::join('tblproduct', 'tblpricedetail.prod_id', 'tblproduct.prod_id')->join('tblperusahaan','tblproduct.supplier', 'tblperusahaan.id')->where('customer_id', $id)->select('tblproduct.name','tblperusahaan.nama AS namasupplier', 'tblproduct.prod_id', 'category', 'tblpricedetail.price', 'tblpricedetail.pv', 'tblpricedetail.id AS pdid')->orderBy('tblproduct.name', 'asc')->get();
        $jenis = "customer";
        return view('customer.pricebv', compact('jenis', 'customer', 'product'));
    }

    public function updatePriceBV(Request $request, $id)
    {
        if($request->opsi==0){
            try{
                if(isset($request->prod_id)){
                    $ctr = count($request->prod_id);
                    for($i=0; $i<$ctr; $i++){
                        $prod_id = $request->prod_id[$i];
                        $prod_price = $request->prod_price[$i];
                        $prod_bv = $request->prod_bv[$i];

                        if(isset($request->prod_price_lama[$i]) || isset($request->prod_bv_lama[$i])){
                            $prod_price_lama = $request->prod_price_lama[$i];
                            $prod_bv_lama = $request->prod_bv_lama[$i];
                            if($prod_price_lama != $prod_price || $prod_bv_lama != $prod_bv){
                                $pricedet = PriceDet::where('prod_id', $prod_id)->where('customer_id', $id)->first();
                                $pricedet->price = $prod_price;
                                $pricedet->pv = $prod_bv;
                                $pricedet->update();
                            }
                        }else{
                            $pricedet = new PriceDet;
                            $pricedet->customer_id = $id;
                            $pricedet->prod_id = $prod_id;
                            $pricedet->price = $prod_price;
                            $pricedet->pv = $prod_bv;
                            $pricedet->save();
                        }
                    }
                    return redirect()->route('customer.index')->with('status','Price & BV berhasil diupdate!');
                }else{
                    return redirect()->route('customer.index')->with('warning','Price & BV gagal terupdate!');
                }
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
                // return response()->json($e);
            }
        }elseif($request->opsi==1){
            return redirect()->route('exportXlsProduct', ['id' => $id]);
        }
    }

    public function ajxGetProduct(Request $request){
        $keyword = strip_tags(trim($request->keyword));
        $key = $keyword.'%';
        $search = Product::join('tblperusahaan','tblproduct.supplier','=','tblperusahaan.id')->where('name','LIKE', $key)->orWhere('tblproduct.prod_id','LIKE', $key)->select('tblproduct.id AS id','tblproduct.name', 'tblproduct.prod_id', 'tblperusahaan.nama AS supplier')->orderBy('tblproduct.name')->limit(5)->get();
        // $s = BankMember::where('norek','LIKE', $norek.'%')->select('norek', 'id')->limit(5)->get();
        $data = array();
        $array = json_decode( json_encode($search), true);
        foreach ($array as $key) {
            $arrayName = array('id' =>$key['id'],'prod_id' => $key['prod_id'], 'nama' => $key['name'], 'supplier' => $key['supplier']);
            // $arrayName = array('id' => $key['id'],'text' => $key['norek']);
            array_push($data,$arrayName);
        }
        echo json_encode($data, JSON_FORCE_OBJECT);
    }

    public function ajxAddRowProduct(Request $request){
        $id_product = $request->prod_id;
        $customer_id = $request->cust_id;
        $count = $request->count+1;

        $product = Product::join('tblperusahaan','tblproduct.supplier', 'tblperusahaan.id')->where('tblproduct.id',$id_product)->select('tblproduct.name','tblperusahaan.nama AS namasupplier', 'prod_id', 'category')->first();

        $pricedet = PriceDet::where('prod_id', $id_product)->where('customer_id', $customer_id)->select('price', 'pv')->first();

        $append = '<tr style="width:100%" id="trow'.$count.'" class="trow">
        <td name="no" id="no'.$count.'">'.$count.'</td>
        <td><input type="hidden" name="supplier[]" id="supplier'.$count.'" value="'.$product['supplier'].'">'.$product['namasupplier'].'</td>
        <td><input type="hidden" name="prod_id[]" id="prod_id'.$count.'" value="'.$product['prod_id'].'">'.$product['prod_id'].'</td>
        <td><input type="hidden" name="prod_name[]" id="prod_name'.$count.'" value="'.$product['name'].'">'.$product['name'].'</td>
        <td><input type="hidden" name="prod_brand[]" id="prod_brand'.$count.'" value="'.$product['category'].'">'.$product['category'].'</td>
        <td><input type="text" class="form-control number" name="prod_price[]" id="prod_price'.$count.'" value="'.$pricedet['price'].'"></td>
        <td><input type="text" class="form-control number" name="prod_bv[]" id="prod_bv'.$count.'" value="'.$pricedet['pv'].'"></td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect waves-danger m-b-5" onclick="deleteItem('.$count.')" >x</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
        );

        return response()->json($data);
    }

    public function deletePriceDet(Request $request, $id)
    {
        try{
            $pricedet = PriceDet::where('id',$request->id)->first();
            $pricedet->delete();
            return response()->json();
        }catch(\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function priceByCustomer()
    {
        $page = MenuMapping::getMap(session('user_id'),"CRCS");
        $products = Product::join('tblperusahaan','tblproduct.supplier', 'tblperusahaan.id')->select('tblproduct.id AS pid', 'tblproduct.name','tblperusahaan.nama AS namasupplier', 'prod_id', 'category')->get();
        $jenis = "pricebycustomer";
        return view('customer.index', compact('jenis', 'products', 'page'));
    }

    public function managePriceByCustomer($id)
    {
        $product = Product::where('id', $id)->select('id', 'prod_id', 'name', 'category', 'supplier')->first();
        // $customer = PriceDet::join('tblcustomer', 'tblpricedetail.customer_id', 'tblcustomer.id')->where('tblpricedetail.prod_id', $product->prod_id)->select('tblcustomer.apname','tblcustomer.cid', 'tblcustomer.cicn', 'tblpricedetail.price', 'tblpricedetail.pv', 'tblpricedetail.id AS pdid')->orderBy('tblcustomer.apname', 'asc')->get();
        // $customer = Customer::join('tblpricedetail', 'tblcustomer.id', 'tblpricedetail.customer_id')->where('tblpricedetail.prod_id', $product->prod_id)->select('tblcustomer.apname','tblcustomer.cid', 'tblcustomer.cicn', 'tblpricedetail.price', 'tblpricedetail.pv', 'tblpricedetail.id AS pdid')->orderBy('tblcustomer.apname', 'asc')->get();
        $customer = Customer::select('apname', 'cid', 'cicn', 'id')->orderBy('apname', 'asc')->get();
        $jenis = "produk";
        return view('customer.pricebv', compact('jenis', 'customer', 'product'));
    }

    public function updateManagePriceBV(Request $request, $id)
    {
        if($request->opsi==0){
            try{
                if(isset($request->cust_id)){
                    $ctr = count($request->cust_id);
                    for($i=0; $i<$ctr; $i++){
                        $cust_id = $request->cust_id[$i];
                        $prod_price = $request->prod_price[$i];
                        $prod_bv = $request->prod_bv[$i];

                        if(empty(PriceDet::where('prod_id', $request->prod_id)->where('customer_id', $cust_id)->first()) == 0){
                            $prod_price_lama = $request->prod_price_lama[$i];
                            $prod_bv_lama = $request->prod_bv_lama[$i];
                            if($prod_price_lama != $prod_price || $prod_bv_lama != $prod_bv){
                                $pricedet = PriceDet::where('prod_id', $request->prod_id)->where('customer_id', $cust_id)->first();
                                $pricedet->price = $prod_price;
                                $pricedet->pv = $prod_bv;
                                $pricedet->update();
                            }
                        }else{
                            $pricedet = new PriceDet;
                            $pricedet->customer_id = $cust_id;
                            $pricedet->prod_id = $request->prod_id;
                            $pricedet->price = $prod_price;
                            $pricedet->pv = $prod_bv;
                            $pricedet->save();
                        }
                    }
                    return redirect()->route('pricebycustomer')->with('status','Price & BV berhasil diupdate!');
                }else{
                    return redirect()->route('pricebycustomer')->with('warning','Price & BV gagal terupdate!');
                }
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
                // return response()->json($e);
            }
        }elseif($request->opsi==1){
            return redirect()->route('exportXlsCustomer', ['id' => $id]);
        }
    }

    public function exportProduct($id){
        ini_set('max_execution_time', 3000);
        Carbon::setLocale('id');
        $getDate = Carbon::now()->setTimezone('Asia/Phnom_Penh');
        $dateFormat = date("d-m-Y", strtotime($getDate));
        $i = 0;
        $cid = $id;
        $cust_name = Customer::where('id', $cid)->first()->apname;
        $datas = PriceDet::join('tblproduct', 'tblpricedetail.prod_id', 'tblproduct.prod_id')->join('tblperusahaan','tblproduct.supplier', 'tblperusahaan.id')->where('customer_id', $cid)->select('tblproduct.name','tblperusahaan.nama AS namasupplier', 'tblproduct.prod_id', 'category', 'tblpricedetail.price', 'tblpricedetail.pv', 'tblpricedetail.id AS pdid')->orderBy('tblproduct.name', 'asc')->get();
        $data = array();
        // array_push($data, array("Pricelist $cust_name ($dateFormat)"));
        foreach($datas as $d){
            $i++;
            $array = array(
                // Data Pricedetail
                'NO' => $i,
                'PRODUCT NAME' => $d['name'],
                'PRODUCT BRAND' => $d['category'],
                'PRICE' => $d['price'],
                'BV' => $d['pv'],
                'SUPPLIER' => $d['namasupplier'],
            );
            array_push($data, $array);

        }
        $export = new PriceDetExport($data);
        return Excel::download($export, 'Pricelist '.$cust_name.' ('.$dateFormat.').xlsx');
        // return Excel::download($export, 'Price & BV.xlsx');
    }

    public function exportCustomer($id){
        ini_set('max_execution_time', 3000);
        Carbon::setLocale('id');
        $getDate = Carbon::now()->setTimezone('Asia/Phnom_Penh');
        $dateFormat = date("d-m-Y", strtotime($getDate));
        $i = 0;
        $pid = $id;
        $prod = Product::where('id', $pid)->select('name', 'prod_id')->first();
        $datas = Customer::select('apname', 'cid', 'cicn', 'id')->orderBy('apname', 'asc')->get();
        $data = array();
        foreach($datas as $d){
            $i++;
            $pd = PriceDet::where('customer_id', $d['id'])->where('prod_id', $prod['prod_id'])->select('price', 'pv')->first();
            $array = array(
                // Data Pricedetail
                'NO' => $i,
                'CUSTOMERS ID' => $d['cid'],
                'CUSTOMERS NAME' => $d['apname'],
                'COMPANYS NAME' => $d['cicn'],
                'PRICE' => $pd['price'],
                'BV' => $pd['pv'],
            );
            array_push($data, $array);

        }
        $export = new PriceDetExport2($data);
        return Excel::download($export, 'Pricelist '.$prod['name'].' by Customer('.$dateFormat.').xlsx');
        // return Excel::download($export, 'Price & BV.xlsx');
    }
}
