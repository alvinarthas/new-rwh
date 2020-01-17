<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Customer;
use App\Product;
use App\PriceDet;
use App\Sales;
use App\SalesDet;
use App\PurchaseDetail;
use App\Jurnal;
use App\MenuMapping;
use App\Log;
use App\TempSales;
use App\TempSalesDet;
use App\DeliveryOrder;

class SalesController extends Controller
{

    public function index()
    {
        $page = MenuMapping::getMap(session('user_id'),"PSSL");
        return view('sales.index',compact('page'));
    }

    public function showSales(Request $request){
        // $products = PriceDet::where('customer_id',$request->customer)->select('prod_id')->orderBy('prod_id','asc')->get();
        $products = Product::select('prod_id','name')->get();
        $customer = Customer::where('id',$request->customer)->select('id','apname','apphone','cicn','ciphone')->first();
        if ($request->ajax()) {
            return response()->json(view('sales.showsales',compact('products','customer'))->render());
        }
    }

    public function addSales(Request $request){
        $customer = $request->customer;
        $qty = $request->qty;
        $unit = $request->unit;
        $count = $request->count+1;
        $product_id = $request->select_product;

        $product = PriceDet::where('customer_id',$customer)->where('prod_id',$product_id)->first();
        $avcost = PurchaseDetail::where('prod_id',$product_id)->avg('price');

        if(isset($product)){
            $sub_ttl_price = $qty*$product->price;
            $sub_ttl_bv = $qty*$product->pv;
            $prod_name = $product->prod->name;
            $prod_id = $product->prod_id;
            $price = $product->price;
            $bv = $product->pv;
        }else{
            $prod = Product::where('prod_id',$product_id)->first();
            $sub_ttl_price = 0;
            $sub_ttl_bv = 0;
            $prod_name = $prod->name;
            $prod_id = $prod->prod_id;
            $price = 0;
            $bv = 0;
        }
    
        $append = '<tr style="width:100%" id="trow'.$count.'">
        <input type="hidden" name="detail[]" id="detail'.$count.'" value="baru">
        <td>'.$count.'</td>
        <td><input type="hidden" name="prod_id[]" id="prod_id'.$count.'" value="'.$prod_id.'">'.$prod_id.'</td>
        <td><input type="hidden" name="prod_name[]" id="prod_name'.$count.'" value="'.$prod_name.'">'.$prod_name.'</td>
        <td><input type="text" name="price[]" value="'.$price.'" id="price'.$count.'" onkeyup="changeTotal('.$count.')"></td>
        <td>Rp&nbsp;'.number_format($avcost,2,",",".").'</td>
        <td><input type="text" name="qty[]" value="'.$qty.'" id="qty'.$count.'" onkeyup="changeTotal('.$count.')"></td>
        <td><input type="hidden" name="unit[]" value="'.$unit.'" id="unit'.$count.'">'.$unit.'</td>
        <td><input type="text" name="sub_ttl_price[]" value="'.$sub_ttl_price.'" id="sub_ttl_price'.$count.'" readonly></td>
        <td><input type="text" name="bv_unit[]" value="'.$bv.'" id="bv_unit'.$count.'" onkeyup="changeTotal('.$count.',`bv`)"></td>
        <td><input type="text" name="sub_ttl_bv[]" value="'.$sub_ttl_bv.'" id="sub_ttl_bv'.$count.'" readonly></td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
            'sub_ttl_price' => $sub_ttl_price,
        );

        return response()->json($data);
    }

    public function showIndexSales(Request $request){
        if($request->param == "all"){
            $sales = Sales::orderBy('trx_date','desc')->get();
        }else{
            $sales = Sales::whereBetween('trx_date',[$request->start,$request->end])->orderBy('trx_date','desc')->get();
        }
        $page = MenuMapping::getMap(session('user_id'),"PSSL");
        $transaksi = Sales::getOrder($request->start,$request->end,$request->param);
        if ($request->ajax()) {
            return response()->json(view('sales.indexsales',compact('sales','page','transaksi'))->render());
        }
    }

    public function destroySalesDetail(Request $request){
        try {
            if($request->status == 1){
                $detail = TempSalesDet::where('id',$request->detail)->first();
                $sales = TempSales::where('id',$detail->temp_id)->first();
                $sales->ttl_harga = $sales->ttl_harga - $detail->sub_ttl;
                $sales->save();
    
                $detail->delete();
            }else{
                $detail = SalesDet::where('id',$request->detail)->first();
                $sales = Sales::where('id',$detail->trx_id)->first();
                $sales->ttl_harga = $sales->ttl_harga - $detail->sub_ttl;
                $sales->save();
    
                $detail->delete();
            }
            
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function create()
    {
        $customers = Customer::select('id','apname')->get();
        return view('sales.form', compact('customers'));
    }

    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'customer' => 'required',
            'trx_date' => 'required|date',
            'count' => 'required',
            'raw_ttl_trx' => 'required|integer',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $sales = new Sales(array(
                'customer_id' => $request->customer,
                'trx_date' => $request->trx_date,
                'creator' => session('user_id'),
                'ttl_harga' => $request->raw_ttl_trx,
                'ongkir' => $request->ongkir,
                'approve' => 0,
            ));
            // success
            try {
                $sales->save();

                for ($i=0; $i < $request->count ; $i++) {
                    $salesdet = new SalesDet(array(
                        'trx_id' => $sales->id,
                        'prod_id' => $request->prod_id[$i],
                        'qty' => $request->qty[$i],
                        'unit' => $request->unit[$i],
                        'creator' => session('user_id'),
                        'price' => $request->price[$i],
                        'pv' => $request->bv_unit[$i],
                        'sub_ttl' => $request->sub_ttl_price[$i],
                        'sub_ttl_pv' => $request->sub_ttl_bv[$i],
                    ));
                    $salesdet->save();
                }

                Log::setLog('PSSLC','Create SO.'.$sales->id);

                return redirect()->route('sales.index')->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }

    public function show(Request $request,$id)
    {
        if ($request->ajax()) {
            $sales = Sales::where('id',$request->id)->first();
            $salesdet = SalesDet::where('trx_id',$request->id)->get();
            return response()->json(view('sales.modal',compact('sales','salesdet'))->render());
        }
    }

    public function edit($id)
    {
        $count_temp = TempSales::where('trx_id',$id)->count('trx_id');
        $status_temp = TempSales::where('trx_id',$id)->where('status',1)->count('trx_id');
        $page = MenuMapping::getMap(session('user_id'),"PSSL");

        if($count_temp > 0 && $status_temp == 1){
            $status = 1;
            $sales = TempSales::where('trx_id',$id)->first();
            $salesdet = TempSalesDet::where('temp_id',$sales->id)->get();
            $products = Product::select('prod_id','name')->get();
        }else{
            $status = 0;
            $sales = Sales::where('id',$id)->first();
            $salesdet = SalesDet::where('trx_id',$id)->get();
            $products = Product::select('prod_id','name')->get();
        }
        return view('sales.form_update', compact('salesdet','sales','products','page','status'));
    }

    public function update(Request $request, $id)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'customer' => 'required',
            'trx_date' => 'required|date',
            'count' => 'required',
            'raw_ttl_trx' => 'required|integer',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            // success
            try {
                $check = TempSales::where('trx_id',$id)->count();

                if($check > 0){
                    $temp_sales = TempSales::where('trx_id',$id)->first();
                    // Update and tranfer to Sales Orginal
                    $temp_sales->trx_date = $request->trx_date;
                    $temp_sales->creator = session('user_id');
                    $temp_sales->ttl_harga = $request->raw_ttl_trx;
                    $temp_sales->ongkir = $request->ongkir;
                    $temp_sales->customer_id = $request->customer;
                    $temp_sales->status = 1;

                    $temp_sales->update();
                    // Delete Temp Detail
                    $temp_purdet = TempSalesDet::where('temp_id',$temp_sales->id)->delete();
                }else{
                    $temp_sales = new TempSales(array(
                        'trx_id' => $id,
                        'customer_id' => $request->customer,
                        'trx_date' => $request->trx_date,
                        'creator' => session('user_id'),
                        'ttl_harga' => $request->raw_ttl_trx,
                        'ongkir' => $request->ongkir,
                        'approve' => 0,
                        'status' => 1,
                    ));
                    $temp_sales->save();
                }
                
                for ($i=0; $i < $request->count ; $i++) {
                    $temp_salesdet = new TempSalesDet(array(
                        'temp_id' => $temp_sales->id,
                        'prod_id' => $request->prod_id[$i],
                        'qty' => $request->qty[$i],
                        'unit' => $request->unit[$i],
                        'creator' => session('user_id'),
                        'price' => $request->price[$i],
                        'pv' => $request->bv_unit[$i],
                        'sub_ttl' => $request->sub_ttl_price[$i],
                        'sub_ttl_pv' => $request->sub_ttl_bv[$i],
                    ));
                    $temp_salesdet->save();
                }
                Log::setLog('PSSLU','Update SO.'.$id);
                
                return redirect()->route('sales.index')->with('status', 'Data berhasil diubah');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function destroy($id)
    {
        $sales = Sales::where('id',$id)->first();
        foreach (DeliveryOrder::where('sales_id',$id)->select('jurnal_id')->get() as $key) {
            Jurnal::where('id_jurnal',$key->jurnal_id)->delete();
        }
        Jurnal::where('id_jurnal',$sales->jurnal_id)->delete();
        
        try {
            $sales->delete();
            Log::setLog('PSSLD','Delete SO.'.$id);
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
