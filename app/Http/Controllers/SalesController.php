<?php

namespace App\Http\Controllers;

// Library
use Excel;
use Carbon\Carbon;
use App\Exports\SOExport;
use App\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

// Models
use App\Log;
use App\Sales;
use App\Jurnal;
use App\Product;
use App\Customer;
use App\PriceDet;
use App\SalesDet;
use App\TempSales;
use App\Ecommerce;
use App\MenuMapping;
use App\TempSalesDet;
use App\SalesPayment;
use App\DeliveryOrder;
use App\PurchaseDetail;
use App\DeliveryDetail;


class SalesController extends Controller
{

    // GET DATA

    public function index(){
        $page = MenuMapping::getMap(session('user_id'),"PSSL");
        return view('sales.index',compact('page'));
    }

    public function showIndexSales(Request $request){
        $page = MenuMapping::getMap(session('user_id'),"PSSL");
        $method = $request->method;
        $param = $request->param;
        if($request->param == "all"){
            if(array_search("PSSLV",$page) && array_search("PSSLVO",$page)){
                $sales = Sales::orderBy('trx_date','desc')->get();
            }else if(array_search("PSSLV",$page)){
                $sales = Sales::where('method',0)->orderBy('trx_date','desc')->get();
            }else if(array_search("PSSLVO",$page)){
                $sales = Sales::where('method','NOT LIKE',0)->orderBy('trx_date','desc')->get();
            }


        }else{
            if ($request->method == "*"){
                $sales = Sales::whereBetween('trx_date',[$request->start,$request->end])->orderBy('trx_date','desc')->get();
            }elseif ($request->method == 0) {
                $sales = Sales::where('method',$request->method)->whereBetween('trx_date',[$request->start,$request->end])->orderBy('trx_date','desc')->get();
            }elseif ($request->method == 1){
                $sales = Sales::where('method','NOT LIKE',0)->whereBetween('trx_date',[$request->start,$request->end])->orderBy('trx_date','desc')->get();
            }
        }

        $transaksi = Sales::getOrder($request->start,$request->end,$request->param,$request->method,$page);

        if ($request->ajax()) {
            // return response()->json(view('sales.indexsales',compact('sales','page','transaksi'))->render());
            return response()->json(view('sales.viewsales',compact('sales','page','transaksi','method','param'))->render());
        }
    }

    public function showIndexSalesNew(Request $request){
        $page = MenuMapping::getMap(session('user_id'),"PSSL");
        $method = $request->method;
        $param = $request->param;

        $transaksi = Sales::getOrder($request->start,$request->end,$request->param,$request->method,$page);

        if ($request->ajax()) {
            // return response()->json(view('sales.indexsales',compact('sales','page','transaksi'))->render());
            return response()->json(view('sales.viewsales',compact('page','transaksi','method','param'))->render());
        }
    }

    public function salesData(Request $request){
        if($request->ajax()){
            $datas = Sales::salesData($request);
            echo json_encode($datas);
        }
    }

    public function customerSales(Request $request){
        if($request->method == '*'){
            $customers = Customer::select('id','apname')->get();
            $append = '<option value="#" disabled selected>Pilih Customer</option>';
        }elseif($request->method == 0){
            $customers = Customer::where('cust_type',0)->orwhere('cust_type',2)->select('id','apname')->get();
            $append = '<option value="#" disabled selected>Pilih Customer Offline</option>';
        }else{
            $customers = Customer::where('cust_type',1)->orwhere('cust_type',2)->select('id','apname')->get();
            $append = '<option value="#">Pilih Customer Online</option>';
        }

        foreach($customers as $key){
            $append.='<option value="'.$key->id.'">'.$key->apname.'</option>';
        }

        return response()->json($append);
    }

    public function show(Request $request,$id){
        if ($request->ajax()) {
            $sales = Sales::where('id',$request->id)->first();
            $salesdet = SalesDet::where('trx_id',$request->id)->get();
            $totalprice = SalesDet::where('trx_id', $request->id)->sum(DB::raw('price * qty'));
            $salespay = SalesPayment::where('trx_id',$request->id)->sum('payment_amount');

            $count = TempSales::where('trx_id', $request->id)->count();

            if($count != 0){
                $temp_sales = TempSales::where('trx_id', $request->id)->first();
                $temp_salesdet = TempSalesDet::where('temp_id', $temp_sales->id)->get();
                $temp_totalprice = TempSalesDet::where('temp_id', $temp_sales->id)->sum(DB::raw('price * qty'));
                $jenis = "double";
                return response()->json(view('sales.modal',compact('sales','salesdet','salespay', 'temp_sales', 'temp_salesdet', 'temp_totalprice', 'jenis'))->render());
            }else{
                $jenis = "double";
                return response()->json(view('sales.modal',compact('sales','salesdet','salespay', 'totalprice', 'jenis'))->render());
            }
        }
    }

    // CREATE DATA

    public function create(){
        $page = MenuMapping::getMap(session('user_id'),"PSSL");
        if(array_search("PSSLC",$page) && array_search("PSSLCO",$page)){
            $ecoms = Ecommerce::all();
            $customers = "";
        }else if(array_search("PSSLC",$page)){
            $ecoms = "";
            $customers = Customer::where('cust_type',0)->orwhere('cust_type',2)->select('id','apname')->get();
        }else if(array_search("PSSLCO",$page)){
            $ecoms = Ecommerce::all();
            $customers = Customer::where('cust_type',1)->orwhere('cust_type',2)->select('id','apname')->get();
        }

        return view('sales.form', compact('page','ecoms','customers'));
    }

    public function addSales(Request $request){
        $customer = $request->customer;
        $qty = $request->qty;
        $unit = $request->unit;
        $count = $request->count+1;
        $product_id = $request->select_product;

        $product = PriceDet::where('customer_id',$customer)->where('prod_id',$product_id)->first();
        $avcost = PurchaseDetail::avgCost($product_id);

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

    public function showSales(Request $request){
        // $products = PriceDet::where('customer_id',$request->customer)->select('prod_id')->orderBy('prod_id','asc')->get();
        $products = Product::select('prod_id','name')->get();
        $method = $request->method;
        $customer = Customer::where('id',$request->customer)->select('id','apname','apphone','cicn','ciphone')->first();
        if ($request->ajax()) {
            return response()->json(view('sales.showsales',compact('products','customer','method'))->render());
        }
    }

    public function store(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'customer' => 'required',
            'trx_date' => 'required|date',
            'count' => 'required',
            'raw_ttl_trx' => 'required|integer',
            'method' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            if($request->method <> 0){
                $count_trx = Sales::where('method',$request->method)->orderBy('online_id','desc')->first()->online_id;
                $online_id = $count_trx+1;
            }else{
                $online_id = 0;
            }

            $sales = new Sales(array(
                'customer_id' => $request->customer,
                'trx_date' => $request->trx_date,
                'creator' => session('user_id'),
                'ttl_harga' => $request->raw_ttl_trx,
                'ongkir' => $request->ongkir,
                'approve' => 0,
                'method' => $request->method,
                'online_id' => $online_id,
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

    // UPDATE DATA

    public function edit($id){
        $customer = Customer::all();
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

        if($sales->method == 0){
            $customer = Customer::where('cust_type',0)->get();
        }else{
            $customer = Customer::where('cust_type',1)->get();
        }
        return view('sales.form_update', compact('salesdet','sales','products','page','status', 'customer'));
    }

    public function update(Request $request, $id){
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
                        'method' => $request->method,
                        'online_id' => $request->online_id,
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

    // DELETE DATA

    public function destroySalesDetail(Request $request){
        try {
            if($request->status == 1){
                $detail = TempSalesDet::where('id',$request->detail)->first();
                $sales = TempSales::where('id',$detail->temp_id)->first();
                $sales->ttl_harga = $sales->ttl_harga - $detail->sub_ttl;
                $sales->save();

                $detail->delete();

                Log::setLog('PSSLD','Delete Temp Sales Detail SO.'.$sales->id);
            }else{
                $detail = SalesDet::where('id',$request->detail)->first();
                $sales = Sales::where('id',$detail->trx_id)->first();

                $sales->ttl_harga = $sales->ttl_harga - $detail->sub_ttl;

                $sales->save();
                // Check DO
                $dos = DeliveryDetail::where('product_id',$detail->prod_id)->where('sales_id',$sales->id)->get();
                $detail->delete();

                if ($sales->approve == 1) {
                    // Recycle Jurnal
                    Sales::recycleSales($sales->id);

                    // Recycle DO
                    foreach ($dos as $dodet) {
                        DeliveryOrder::recycleDO($dodet->do_id,$sales->trx_date,$dodet->id);
                    }
                }

                Log::setLog('PSSLD','Delete Sales Detail SO.'.$sales->id);
            }

            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function destroy($id){
        $sales = Sales::where('id',$id)->first();
        foreach (DeliveryOrder::where('sales_id',$id)->select('jurnal_id')->get() as $key) {
            Jurnal::where('id_jurnal',$key->jurnal_id)->delete();
        }

        foreach (SalesPayment::where('trx_id',$id)->select('jurnal_id')->get() as $key) {
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

    // EXPORT DATA

    public function export(Request $request){
        // echo "<pre>";
        // print_r($request->all());
        // die();
        ini_set('max_execution_time', 3000);

        $tgl = date('Y-m-d', strtotime(Carbon::today()));
        $start = $request->start;
        $end = $request->end;

        if($start != "" && $end != ""){
            $filename = "Daftar Penjualan ".$start." - ".$end."(".$tgl.")";
            $sales = SalesDet::join('tblproducttrx', 'tblproducttrxdet.trx_id', 'tblproducttrx.id')->join('tblcustomer', 'tblproducttrx.customer_id', 'tblcustomer.id')->whereBetween('tblproducttrx.trx_date',[$start,$end])->select('tblproducttrx.trx_date', 'tblproducttrx.id', 'tblcustomer.apname', 'tblproducttrxdet.prod_id', 'tblproducttrxdet.price', 'tblproducttrxdet.qty', 'tblproducttrxdet.unit', 'tblproducttrxdet.sub_ttl', 'tblproducttrxdet.pv', 'tblproducttrxdet.sub_ttl_pv')->orderBy('tblproducttrx.trx_date','desc')->get();
        }else{
            $filename = "Daftar Penjualan (".$tgl.")";
            $sales = SalesDet::join('tblproducttrx', 'tblproducttrxdet.trx_id', 'tblproducttrx.id')->join('tblcustomer', 'tblproducttrx.customer_id', 'tblcustomer.id')->select('tblproducttrx.trx_date', 'tblproducttrx.id', 'tblcustomer.apname', 'tblproducttrxdet.prod_id', 'tblproducttrxdet.price', 'tblproducttrxdet.qty', 'tblproducttrxdet.unit', 'tblproducttrxdet.sub_ttl', 'tblproducttrxdet.pv', 'tblproducttrxdet.sub_ttl_pv')->orderBy('tblproducttrx.trx_date','desc')->get();
        }

        $data = array();
        $no = 0;

        foreach($sales as $s){
            $trx_id = "SO".$s->id;
            $trx_date = $s->trx_date;
            $cust_name = $s->apname;
            $prod_id = $s->product()->first()->prod_id;
            $prod_name = $s->product()->first()->name;
            $price = $s->price;
            $qty = $s->qty;
            $unit = $s->unit;
            $sub_ttl = $s->sub_ttl;
            $pv = $s->pv;
            $sub_ttl_pv = $s->sub_ttl_pv;
            $no++;

            $array = array(
                // Data Member
                'No' => $no,
                'Transaction ID' => $trx_id,
                'Transaction Date' => $trx_date,
                'Customer Name' => $cust_name,
                'Product ID' => $prod_id,
                'Product Name' => $prod_name,
                'Price' => $price,
                'Qty' => $qty,
                'Unit' => $unit,
                'Sub Total' => $sub_ttl,
                'BV per Product' => $pv,
                'Total BV' => $sub_ttl_pv,
            );

            array_push($data, $array);
        }

        $export = new SOExport($data);

        return Excel::download($export, $filename.'.xlsx');
    }
}
