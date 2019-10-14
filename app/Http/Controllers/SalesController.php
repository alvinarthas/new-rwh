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
        $products = PriceDet::where('customer_id',$request->customer)->select('prod_id')->orderBy('prod_id','asc')->get();
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
        $product = $request->select_product;

        $product = PriceDet::where('customer_id',$customer)->where('prod_id',$product)->first();

        $sub_ttl_price = $qty*$product->price;
        $sub_ttl_bv = $qty*$product->pv;

        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td>'.$count.'</td>
        <td><input type="hidden" name="prod_id[]" id="prod_id'.$count.'" value="'.$product->prod_id.'">'.$product->prod_id.'</td>
        <td><input type="hidden" name="prod_name[]" id="prod_name'.$count.'" value="'.$product->prod->name.'">'.$product->prod->name.'</td>
        <td><input type="hidden" name="price[]" value="'.$product->price.'" id="price'.$count.'">Rp. '.number_format($product->price).'</td>
        <td><input type="hidden" name="qty[]" value="'.$qty.'" id="qty'.$count.'">'.$qty.'</td>
        <td><input type="hidden" name="unit[]" value="'.$unit.'" id="unit'.$count.'">'.$unit.'</td>
        <td><input type="hidden" name="sub_ttl_price[]" value="'.$sub_ttl_price.'" id="sub_ttl_price'.$count.'">Rp. '.number_format($sub_ttl_price).'</td>
        <td><input type="hidden" name="bv_unit[]" value="'.$product->pv.'" id="bv_unit'.$count.'">Rp. '.number_format($product->pv).'</td>
        <td><input type="hidden" name="sub_ttl_bv[]" value="'.$sub_ttl_bv.'" id="sub_ttl_dist'.$count.'">Rp. '.number_format($sub_ttl_bv).'</td>
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
        if ($request->ajax()) {
            return response()->json(view('sales.indexsales',compact('sales'))->render());
        }
    }

    public function destroySalesDetail(Request $request){
        $detail = SalesDet::where('id',$request->detail)->first();
        $detail->delete();
        return "true";
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
            $id_jurnal = Jurnal::getJurnalID();
            $id_jurnal2 = $id_jurnal+1;

            $sales = new Sales(array(
                'customer_id' => $request->customer,
                'trx_date' => $request->trx_date,
                'creator' => session('user_id'),
                'ttl_harga' => $request->raw_ttl_trx,
                'ongkir' => $request->ongkir,
                'approve' => 0,
                'jurnal_id' => $id_jurnal,
                'hpp_jurnal_id' => $id_jurnal2,
            ));
            // success
            try {
                $sales->save();
                $jurnal_desc = "SO.".$sales->id;
                $modal = 0;
                for ($i=0; $i < $request->count ; $i++) {
                    $avcharga = PurchaseDetail::where('prod_id',$key->id)->avg('price');
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
                    //insert debet Piutang Usaha Konsumen Masukkan harga total - diskon
                    Jurnal::addJurnal($id_jurnal,$request->raw_ttl_trx,$request->trx_date,$jurnal_desc,'1-103001','Debet');
                    //insert credit pendapatan retail
                    Jurnal::addJurnal($id_jurnal,$request->raw_ttl_trx,$request->trx_date,$jurnal_desc,'4-102000','Credit');
                // Jurnal 2
                    //insert debet Piutang Usaha Konsumen
                    Jurnal::addJurnal($id_jurnal2,$modal,$request->trx_date,$jurnal_desc,'5-100001','Debet');
                    //insert Credit Pendapatan Retail
                    Jurnal::addJurnal($id_jurnal2,$modal,$request->trx_date,$jurnal_desc,'1-105001','Credit');

                return redirect()->route('sales.index')->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }

    public function show($id)
    {
        //
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

                $jurnal = Jurnal::where('id_jurnal',$sales->jurnal_id)->first();
                $jurnal->Amount = $request->raw_ttl_trx;
                $jurnal->date = $request->trx_date;
                $jurnal->update();

                $modal = 0;
                for ($i=0; $i < $request->count ; $i++) {
                    if(isset($request->prod_id[$i])){
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
                }

                $hpp_jurnal = Jurnal::where('id_jurnal',$sales->hpp_jurnal_id)->first();
                $hpp_jurnal->Amount = $hpp_jurnal->Amount+$modal;
                $hpp_jurnal->date = $request->trx_date;
                $hpp_jurnal->update();
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
