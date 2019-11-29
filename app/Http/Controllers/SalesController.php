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
        $sales = Sales::whereBetween('trx_date',[$request->start,$request->end])->orderBy('trx_date','desc')->get();
        $page = MenuMapping::getMap(session('user_id'),"PSSL");
        $transaksi = Sales::getOrder($request->start,$request->end);
        if ($request->ajax()) {
            return response()->json(view('sales.indexsales',compact('sales','page','transaksi'))->render());
        }
    }

    public function destroySalesDetail(Request $request){
        try {
            $detail = SalesDet::where('id',$request->detail)->first();
            $sales = Sales::where('id',$detail->trx_id)->first();
            $sales->ttl_harga = $sales->ttl_harga - $detail->sub_ttl;
            $sales->save();

            $detail->delete();
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
            $id_jurnal = Jurnal::getJurnalID('SO');
            $sales = new Sales(array(
                'customer_id' => $request->customer,
                'trx_date' => $request->trx_date,
                'creator' => session('user_id'),
                'ttl_harga' => $request->raw_ttl_trx,
                'ongkir' => $request->ongkir,
                'approve' => 0,
                'jurnal_id' => $id_jurnal,
            ));
            // success
            try {
                $sales->save();
                $total_transaksi = $request->raw_ttl_trx+$request->ongkir;
                $jurnal_desc = "SO.".$sales->id;
                $modal = 0;
                for ($i=0; $i < $request->count ; $i++) {
                    $avcharga = PurchaseDetail::where('prod_id',$request->prod_id[$i])->avg('price');
                    $modal += ($request->qty[$i] * $avcharga);
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

                // Jurnal 1
                    //insert debet Piutang Konsumen Masukkan harga total - diskon
                    Jurnal::addJurnal($id_jurnal,$total_transaksi,$request->trx_date,$jurnal_desc,'1.1.3.1','Debet');
                    //insert credit pendapatan retail (SALES)
                    Jurnal::addJurnal($id_jurnal,$total_transaksi,$request->trx_date,$jurnal_desc,'4.1.1','Credit');
                // Jurnal 2
                    //insert debet COGS
                    Jurnal::addJurnal($id_jurnal,$modal,$request->trx_date,$jurnal_desc,'5.1','Debet');
                    //insert Credit Persediaan Barang milik customer
                    Jurnal::addJurnal($id_jurnal,$modal,$request->trx_date,$jurnal_desc,'1.1.4.1.2','Credit');

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
        $sales = Sales::where('id',$id)->first();
        $salesdet = SalesDet::where('trx_id',$id)->get();
        $products = PriceDet::where('customer_id',$sales->customer_id)->select('prod_id')->orderBy('prod_id','asc')->get();

        return view('sales.form_update', compact('salesdet','sales','products'));
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
            $sales = Sales::where('id',$id)->first();

            $sales->trx_date = $request->trx_date;
            $sales->creator = session('user_id');
            $sales->ttl_harga = $request->raw_ttl_trx;
            $sales->ongkir = $request->ongkir;
            // success
            try {
                $sales->update();
                $total_transaksi = $request->raw_ttl_trx+$request->ongkir;
                // Update Jurnal Piutang Customer dan Sales Update

                $jurnal_a = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','1.1.3.1')->first();
                $jurnal_a->Amount = $total_transaksi;
                $jurnal_a->date = $request->trx_date;
                $jurnal_a->update();

                $jurnal_b = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','4.1.1')->first();
                $jurnal_b->Amount = $total_transaksi;
                $jurnal_b->date = $request->trx_date;
                $jurnal_b->update();

                $modal = 0;
                for ($i=0; $i < $request->count ; $i++) {
                    if($request->detail[$i] == "baru"){
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
                    }else{
                        $salesdet = SalesDet::where('id',$request->detail[$i])->first();
                        $salesdet->qty = $request->qty[$i];
                        $salesdet->price = $request->price[$i];
                        $salesdet->sub_ttl = $request->sub_ttl_price[$i];
                        $salesdet->pv = $request->bv_unit[$i];
                        $salesdet->sub_ttl_pv = $request->sub_ttl_bv[$i];
                        $salesdet->update();
                    }
                }

                // Get Modal COGS
                $modal = 0;
                foreach (SalesDet::where('trx_id',$id)->get() as $key) {
                    $avcharga = PurchaseDetail::where('prod_id',$key->prod_id)->avg('price');
                    $modal += ($key->qty * $avcharga);
                }
                
                // Update Jurnal COGS 
                $jurnal_c = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','5.1')->first();
                $jurnal_c->Amount = $modal;
                $jurnal_c->date = $request->trx_date;
                $jurnal_c->update();

                $jurnal_d = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','1.1.4.1.2')->first();
                $jurnal_d->Amount = $modal;
                $jurnal_d->date = $request->trx_date;
                $jurnal_d->update();
                return redirect()->route('sales.index')->with('status', 'Data berhasil diubah');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function destroy($id)
    {
        $sales = Sales::where('id',$id)->first();
        Jurnal::where('id_jurnal',$sales->jurnal_id)->delete();
        Jurnal::where('id_jurnal',$sales->hpp_jurnal_id)->delete();
        try {
            $sales->delete();
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
