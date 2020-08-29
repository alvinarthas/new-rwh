<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\SalesDet;
use App\DeliveryOrder;
use App\DeliveryDetail;
use App\PurchaseDetail;
use App\Jurnal;
use App\Log;
use App\TempSales;
use App\TempSalesDet;
use App\Customer;
use App\Purchase;
use App\Ecommerce;

class Sales extends Model
{
    protected $table ='tblproducttrx';
    protected $fillable = [
    'trx_date','creator','payment','ttl_harga','customer_id','jurnal_id','hpp_jurnal_id','ongkir','approve','approve_by','status','cogs','method','online_id'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public function online(){
        return $this->belongsTo('App\Ecommerce','method','id');
    }

    public static function getOrder($start,$end,$param,$method,$page){
        $data = collect();
        if($param == "all"){
            if(array_search("PSSLV",$page) && array_search("PSSLVO",$page)){
                $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id');
            }else if(array_search("PSSLV",$page)){
                $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id')
            ->where('method',0);
            }else if(array_search("PSSLVO",$page)){
                $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id')
                ->where('method','NOT LIKE', 0);
            }

        }else{
            if ($method == "*"){
                $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id')->whereBetween('trx_date',[$start,$end]);
            }elseif ($method == 0) {
                $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id')->where('method', 0)->whereBetween('trx_date',[$start,$end]);
            }elseif ($method == 1){
                $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id')->where('method','NOT LIKE', 0)->whereBetween('trx_date',[$start,$end]);
            }

        }
        $ttl_count = $order->sum('x.qty');
        $order->orderBy('tblproducttrx.id')->select('tblproducttrx.*');

        $ttl_pemasukan = $order->sum('x.sub_ttl');
        $ttl_total = $order->sum('x.sub_ttl_pv');
        if($param == "all"){
            if(array_search("PSSLV",$page) && array_search("PSSLVO",$page)){
                $ttl_trx = Sales::count('id');
            }else if(array_search("PSSLV",$page)){
                $ttl_trx = Sales::where('method',0)->whereBetween('trx_date',[$start,$end])->count('id');
            }else if(array_search("PSSLVO",$page)){
                $ttl_trx = Sales::where('method','NOT LIKE', 0)->whereBetween('trx_date',[$start,$end])->count('id');
            }

        }else{
            if ($method == "*"){
                $ttl_trx = Sales::whereBetween('trx_date',[$start,$end])->count('id');
            }elseif ($method == 0) {
                $ttl_trx = Sales::where('method',0)->whereBetween('trx_date',[$start,$end])->count('id');
            }elseif ($method == 1){
                $ttl_trx = Sales::where('method','NOT LIKE', 0)->whereBetween('trx_date',[$start,$end])->count('id');
            }

        }


        $data->put('ttl_count',$ttl_count);
        $data->put('ttl_pemasukan',$ttl_pemasukan);
        $data->put('ttl_total',$ttl_total);
        $data->put('ttl_trx',$ttl_trx);
        $data->put('start',$start);
        $data->put('end',$end);
        $data->put('data',$order->get());
        return $data;
    }

    public static function getOrderPayment($start_trx,$end_trx,$start_pay,$end_pay,$customer,$param,$method){
        if($param == "all"){
            $payment = SalesPayment::sum('payment_amount');
            $sales = Sales::where('approve',1);
            $data = collect();

            $ttl_trx = $sales->count('id');

            $ttl_harga = $sales->sum('ttl_harga');
            $ttl_ongkir = $sales->sum('ongkir');
        }elseif($param == null){
            if($method == '*'){
                $payment = SalesPayment::whereBetween('payment_date',[$start_pay,$end_pay])->sum('payment_amount');
                $sales = Sales::whereBetween('trx_date',[$start_trx,$end_trx])->where('approve',1);
            }elseif($method == 0){
                $payment = SalesPayment::join('tblproducttrx','tblproducttrx.id','=','tblsopayment.trx_id')->where('method',0)->whereBetween('payment_date',[$start_pay,$end_pay])->sum('payment_amount');
                $sales = Sales::where('method',0)->whereBetween('trx_date',[$start_trx,$end_trx])->where('approve',1);
            }else{
                $payment = SalesPayment::join('tblproducttrx','tblproducttrx.id','=','tblsopayment.trx_id')->where('method','NOT LIKE',0)->whereBetween('payment_date',[$start_pay,$end_pay])->sum('payment_amount');
                $sales = Sales::where('method','NOT LIKE',0)->whereBetween('trx_date',[$start_trx,$end_trx])->where('approve',1);
            }
            $data = collect();

            if($customer <> "all"){
                $sales->where('customer_id',$customer);
            }

            $ttl_trx = $sales->count('id');

            $ttl_harga = $sales->sum('ttl_harga');
            $ttl_ongkir = $sales->sum('ongkir');
        }

        $ttl_sales = $ttl_harga+$ttl_ongkir;

        $data->put('ttl_trx',$ttl_trx);
        $data->put('ttl_sales',$ttl_sales);
        $data->put('ttl_payment',$payment);
        $data->put('data',$sales->orderBy('id','desc')->get());

        return $data;
    }

    public static function checkDO(Request $request){
        $start = $request->start_date;
        $end = $request->end_date;
        $customer = $request->customer;
        $prod_id = $request->prod_id;
        $draw = $request->draw;
        $row = $request->start;
        $rowperpage = $request->length; // Rows display per page
        $columnIndex = $request['order'][0]['column']; // Column index
        $columnName = $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        $sales = Sales::join('tblproducttrxdet', 'tblproducttrxdet.trx_id', 'tblproducttrx.id')->join('tblcustomer', 'tblproducttrx.customer_id', 'tblcustomer.id')->select('tblproducttrx.id', 'tblcustomer.apname AS customer_name','tblproducttrx.jurnal_id', DB::Raw('tblproducttrx.ttl_harga + tblproducttrx.ongkir AS ttl_harga_ongkir'), 'tblproducttrx.ttl_harga', 'tblproducttrx.ongkir','tblproducttrx.trx_date')->where('tblproducttrx.approve', 1);

        if($start <> NULL && $end <> NULL){
            $sales->whereBetween('tblproducttrx.trx_date',[$start,$end]);
        }

        if($customer <> "#"){
            $sales->where('tblproducttrx.customer_id', $customer);
        }

        if($prod_id <> "#"){
            $sales->where('tblproducttrxdet.prod_id', $prod_id);
        }

        $sales->groupBy('tblproducttrxdet.trx_id');

        $totalRecords = 0;
        foreach($sales->get() as $count){
            $totalRecords++;
        }

        if($searchValue != ''){
            $sales = $sales->where('tblproducttrx.jurnal_id', 'LIKE', '%'.$searchValue.'%')->orWhere('tblproducttrx.trx_date', 'LIKE', '%'.$searchValue.'%')->orWhere('tblcustomer.apname', 'LIKE', '%'.$searchValue.'%')->orWhereRaw('(tblproducttrx.ttl_harga + tblproducttrx.ongkir) LIKE ?', '%'.$searchValue.'%');
        }

        $totalRecordwithFilter = 0;
        foreach($sales->get() as $count){
            $totalRecordwithFilter++;
        }

        if($columnName == "no"){
            $sales = $sales->orderBy('id', $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }elseif($columnName == "sales_id"){
            $sales = $sales->orderBy('jurnal_id', $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }elseif($columnName == "customer"){
            $sales = $sales->orderBy('customer_name', $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }elseif($columnName == "total_harga"){
            $sales = $sales->orderBy('ttl_harga_ongkir', $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }else{
            $sales = $sales->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }

        $data = collect();
        $nomor = 1;
        foreach($sales as $sale){
            $salesdet = SalesDet::where('trx_id',$sale->id)->select('*',DB::Raw('SUM(qty) as sum_qty'))->groupBy('prod_id')->get();
            $detcount = $salesdet->count();
            $count = 0;
            foreach($salesdet as $key){
                $count_do_product = DeliveryDetail::where('sales_id',$sale->id)->where('product_id',$key->prod_id)->sum('qty');
                if($key->sum_qty == $count_do_product){
                    $count++;
                }
            }
            if($detcount == $count){
                $status = '<a href="javascrip:;" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Sudah selesai melakukan Delivery</a>';
            }else{
                $status = '<a href="javascrip:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5">Belum selesai melakukan Delivery</a>';
            }

            $button = '<a href="do/show/'.$sale->id.'" class="btn btn-primary btn-rounded waves-effect w-md waves-danger m-b-5">Atur</a>';

            $detail = collect();
            $detail->put('no', $nomor++);
            $detail->put('sales_id', $sale->jurnal_id);
            $detail->put('trx_date', $sale->trx_date);
            $detail->put('customer', $sale->customer_name);
            $detail->put('total_harga', $sale->ttl_harga_ongkir);
            $detail->put('status_do', $status);
            $detail->put('option', $button);
            $data->push($detail);
        }

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data' => $data,
        );

        return $response;
    }

    public static function checkSent($product,$trx){
        return DeliveryDetail::where('product_id',$product)->where('sales_id',$trx)->sum('qty');
    }

    public static function getBV($bulan,$tahun){
        return Sales::join('tblproducttrxdet','tblproducttrx.id','=','tblproducttrxdet.trx_id')->whereMonth('trx_date',$bulan)->whereYear('trx_date',$tahun)->sum('tblproducttrxdet.sub_ttl_pv');
    }

    public static function setJurnal($id,$user_id){
        $sales = Sales::where('id',$id)->first();

        if($sales->method <> 0){
            $ecom = Ecommerce::where('id',$sales->method)->first();

            $id_jurnal = $ecom->kode_trx.'.'.$sales->online_id;
            $jurnal_desc = $ecom->kode_trx.'.'.$sales->online_id.' => '."SO.".$id;

            $sales->approve = 1;
            $sales->approve_by = $user_id;
            $sales->jurnal_id = $id_jurnal;
        }else{
            $id_jurnal = 'SO.'.$id;
            $jurnal_desc = "SO.".$id;

            $sales->approve = 1;
            $sales->approve_by = $user_id;
            $sales->jurnal_id = $id_jurnal;
        }

        $sales->update();

        $total_transaksi = $sales->ttl_harga + $sales->ongkir;

        $modal = 0;
        foreach (SalesDet::where('trx_id',$id)->get() as $key) {
            $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

            $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

            if($sumprice <> 0 && $sumqty <> 0){
                $avcharga = $sumprice/$sumqty;
            }else{
                $avcharga = 0;
            }

            $modal += ($key->qty * $avcharga);
        }

        // Jurnal 1
            //insert debet Piutang Konsumen Masukkan harga total - diskon
            Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'1.1.3.1','Debet',$user_id);
            //insert credit pendapatan retail (SALES)
            Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'4.1.1','Credit',$user_id);
        // Jurnal 2
            //insert debet COGS
            Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'5.1','Debet',$user_id);
            //insert Credit Persediaan Barang milik customer
            Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'2.1.3','Credit',$user_id);

        //  Auto DO
        if ($sales->method <> 0){
            DeliveryOrder::autoDO($sales->id,$sales->trx_date,$user_id);
        }
    }

    public static function recycleSales($id){
        $sales = Sales::where('id',$id)->first();
        // transfer new Detail

        $modal = 0;
        $total_harga = 0;
        foreach (SalesDet::where('trx_id',$id)->get() as $key) {
            $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

            $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

            if($sumprice <> 0 && $sumqty <> 0){
                $avcharga = $sumprice/$sumqty;
            }else{
                $avcharga = 0;
            }

            $modal += ($key->qty * $avcharga);
            $total_harga += ($key->price * $key->qty);
        }
        // Update Total Transaksi Sales
        $sales->ttl_harga = $total_harga;
        $sales->update();

        $total_transaksi = $total_harga + $sales->ongkir;

        // Update Jurnal
        $jurnal_a = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','1.1.3.1')->first();
        $jurnal_a->amount = $total_transaksi;
        $jurnal_a->update();

        $jurnal_b = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','4.1.1')->first();
        $jurnal_b->amount = $total_transaksi;
        $jurnal_b->update();

        $jurnal_c = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','5.1')->first();
        $jurnal_c->amount = $modal;
        $jurnal_c->update();

        $jurnal_d = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','2.1.3')->first();
        $jurnal_d->amount = $modal;
        $jurnal_d->update();
    }

    public static function updateSales($id,$user_id){
        $temp_sales = TempSales::where('trx_id',$id)->first();
        $temp_sales_det = TempSalesDet::where('temp_id',$temp_sales->id)->get();

        $sales = Sales::where('id',$id)->first();

        // Update and tranfer to Sales Orginal
        $sales->trx_date = $temp_sales->trx_date;
        $sales->creator = $user_id;
        $sales->ttl_harga = $temp_sales->ttl_harga;
        $sales->ongkir = $temp_sales->ongkir;
        $sales->customer_id = $temp_sales->customer_id;

        $sales->update();

        // Delete Old Original Detail
        $saldet = SalesDet::where('trx_id',$id)->delete();

        // transfer new Detail
        $modal = 0;
        foreach ($temp_sales_det as $key) {
            $salesdet = new SalesDet(array(
                'trx_id' => $sales->id,
                'prod_id' => $key->prod_id,
                'qty' => $key->qty,
                'unit' => $key->unit,
                'creator' => $user_id,
                'price' => $key->price,
                'pv' => $key->pv,
                'sub_ttl' => $key->sub_ttl,
                'sub_ttl_pv' => $key->sub_ttl_pv,
            ));

            $salesdet->save();

            $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

            $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

            if($sumprice <> 0 && $sumqty <> 0){
                $avcharga = $sumprice/$sumqty;
            }else{
                $avcharga = 0;
            }

            $modal += ($key->qty * $avcharga);
        }

        // Matikan status temp po
        $temp_sales->delete();

        // Update Jurnal
        $total_transaksi = $sales->ttl_harga+$sales->ongkir;
        if($sales->jurnal_id <> '0' || $sales->jurnal_id <> 0){
            $jurnal_a = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','1.1.3.1')->first();
            $jurnal_a->amount = $total_transaksi;
            $jurnal_a->date = $sales->trx_date;
            $jurnal_a->update();

            $jurnal_b = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','4.1.1')->first();
            $jurnal_b->amount = $total_transaksi;
            $jurnal_b->date = $sales->trx_date;
            $jurnal_b->update();

            $jurnal_c = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','5.1')->first();
            $jurnal_c->amount = $modal;
            $jurnal_c->date = $sales->trx_date;
            $jurnal_c->update();

            $jurnal_d = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','2.1.3')->first();
            $jurnal_d->amount = $modal;
            $jurnal_d->date = $sales->trx_date;
            $jurnal_d->update();
        }else{
            if($sales->method <> 0){
                $ecom = Ecommerce::where('id',$sales->method)->first();

                $id_jurnal = $ecom->kode_trx.'.'.$sales->online_id;
                $jurnal_desc = $ecom->kode_trx.'.'.$sales->online_id.' => '."SO.".$id;

            }else{
                $id_jurnal = 'SO.'.$id;
                $jurnal_desc = "SO.".$id;
            }

            $sales->jurnal_id = $id_jurnal;
            $sales->approve = 1;
            $sales->approve_by = $user_id;
            $sales->update();



            // Jurnal 1
                //insert debet Piutang Konsumen Masukkan harga total - diskon
                Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'1.1.3.1','Debet',$user_id);
                //insert credit pendapatan retail (SALES)
                Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'4.1.1','Credit',$user_id);
            // Jurnal 2
                //insert debet COGS
                Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'5.1','Debet',$user_id);
                //insert Credit Persediaan Barang milik customer
                Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'2.1.3','Credit',$user_id);
        }
    }

    public static function report($start,$end){
        $data = collect();

        foreach(Customer::all() as $key){
            $temp = collect();
            $detail = Sales::join('tblproducttrxdet','tblproducttrx.id','=','tblproducttrxdet.trx_id');

            if($start <> NULL && $end <> NULL){
                $detail->whereBetween('tblproducttrx.trx_date',[$start,$end]);
            }

            $bv = $detail->where('tblproducttrx.customer_id',$key->id)->sum('tblproducttrxdet.sub_ttl_pv');
            $price = $detail->where('tblproducttrx.customer_id',$key->id)->sum('tblproducttrxdet.sub_ttl');

            $temp->put('customer',$key->apname);
            $temp->put('price',$price);
            $temp->put('bv',$bv);

            $data->push($temp);
        }
        return $data;
    }

    public static function customerStock($customer){
        $data = collect();
        foreach(Sales::where('customer_id',$customer)->select('id','method','online_id')->get() as $key){
            $sales = collect();
            $data_sales = collect();
            $ttl_selisih = 0;
            foreach (SalesDet::where('trx_id',$key->id)->select('prod_id','qty')->get() as $key2) {
                $detail = collect();
                $do_det = DeliveryDetail::where('sales_id',$key->id)->where('product_id',$key2->prod_id)->sum('qty');

                $selisih = $key2->qty - $do_det;
                $ttl_selisih+=$selisih;
                if($selisih > 0){
                    $detail->put('product',$key2->product->name);
                    $detail->put('product_id',$key2->prod_id);
                    $detail->put('selisih',$selisih);
                    $data_sales->push($detail);
                }
            }
            if($ttl_selisih > 0){
                if($key->method == 0){
                    $sales->put('id','SO.'.$key->id);
                }else{
                    $sales->put('id',$key->online->kode_trx.'.'.$key->online_id);
                }
                $sales->put('data',$data_sales);
                $data->push($sales);
            }
        }
        return $data;
    }

    // Get Data for Serverless SO
    public static function salesData(Request $request){
        // Datatables Parameter POST
        $draw = $request->draw;
        $row = $request->start;
        $rowperpage = $request->length; // Rows display per page
        $columnIndex = $request['order'][0]['column']; // Column index
        $columnName = $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        // Custom Parameter Post
        $start = $request->start_date;
        $end = $request->end_date;
        $method = $request->trx_method;
        $param = $request->param;

        // Start Query
        $page = MenuMapping::getMap(session('user_id'),"PSSL");
        $ecom = Ecommerce::getKode();

        // Sales Initialization
        $sales = Sales::select('id','trx_date','creator','ttl_harga','customer_id','ongkir','approve','method','online_id', DB::raw('SUM(ttl_harga+ongkir) as ttl_trx'));

        // Query By Param
        if($param == "all"){
            if(array_search("PSSLV",$page) && array_search("PSSLVO",$page)){

            }else if(array_search("PSSLV",$page)){
                $sales = $sales->where('method',0);
            }else if(array_search("PSSLVO",$page)){
                $sales = $sales->where('method','NOT LIKE',0);
            }
        }else{
            // Query By Date Range & Method
            if ($method == "*"){
                $sales = $sales->whereBetween('trx_date',[$start,$end]);
            }elseif ($method == 0) {
                $sales = $sales->where('method',$method)->whereBetween('trx_date',[$start,$end]);
            }elseif ($method == 1){
                $sales = $sales->where('method','NOT LIKE',0)->whereBetween('trx_date',[$start,$end]);
            }
        }

        $totalRecords = $sales->count();

        // Search based on param
        if($searchValue != ''){
            // GET Char of TRX ID
            $charID = preg_replace("/[^a-zA-Z]/", "", $searchValue);
            $numID = preg_replace('/[^0-9]/', '', $searchValue);

            // Check Ecom Code
            $checkEcom = preg_filter('~' . strtoupper($charID) . '~', '$0', $ecom);
            if($checkEcom){
                $ecomId = array_key_first($checkEcom);

                if($param == "all"){
                    if(array_search("PSSLV",$page) && array_search("PSSLVO",$page)){
                        $sales = $sales->where('method',$ecomId);
                    }else if(array_search("PSSLV",$page)){
                        $sales = $sales->where('method',0);
                    }else if(array_search("PSSLVO",$page)){
                        $sales = $sales->where('method','NOT LIKE',0)->where('method',$ecomId);
                    }
                }else{
                    // Query By Date Range & Method
                    if ($method == "*"){
                        $sales = $sales->where('method',$ecomId);
                    }elseif ($method == 0) {
                        $sales = $sales->where('method',0);
                    }elseif ($method == 1){
                        $sales = $sales->where('method','NOT LIKE',0)->where('method',$ecomId);
                    }
                }

                if(is_numeric($numID)){
                    if($ecomId <> "0"){
                        $sales = $sales->where('online_id','like', $numID.'%');
                    }else{
                        $sales = $sales->where('id','like', $numID.'%');
                    }
                }

                // Search based on total_harga+ongkir
                $sales = $sales->orWhereRaw('(ttl_harga+ongkir) LIKE ?', $numID.'%');
            }

            // Search Based on Customer Name
            $sales = $sales->orWhereHas('customer', function ($query) use ($searchValue) {
                $query->where('apname', 'like', $searchValue.'%');
            });
            // or Search Based on Creator Name
            $sales = $sales->orWhereHas('creator', function ($query) use ($searchValue) {
                $query->where('name', 'like', $searchValue.'%');
            });

            // or Search Based on TRX DATE
            $sales = $sales->orWhere('trx_date','like',$searchValue.'%');
        }
        // dd($sales->toSql());

        $totalRecordwithFilter = $sales->count();

        // Sort and Pagination
        $sales = $sales->groupBy('id');
        if($columnName == "no" || $columnName == "trx_id"){
            $sales->orderBy('id', $columnSortOrder);
        }elseif($columnName == "trx_date"){
            $sales->orderBy('trx_date', $columnSortOrder);
        }elseif($columnName == "customer"){
            $sales->orderBy('customer_id', $columnSortOrder);
        }elseif($columnName == "creator"){
            $sales->orderBy('creator', $columnSortOrder);
        }elseif($columnName == "total"){
            $sales->orderBy('ttl_trx', $columnSortOrder);
        }else{
            $sales->orderBy($columnName, $columnSortOrder);
        }

        $sales = $sales->offset($row)->limit($rowperpage)->get();
        $data = collect();
        $i = 1;

        foreach($sales as $key){
            $detail = collect();
            $options = '';
            $trxModal = '';

                if ($key->method <> 0){
                    $trxModal .= '<td><a href="javascript:;" onclick="getDetail('.$key->id.')" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">'.$key->online()->first()->kode_trx.'.'.$key->online_id.'</a></td>';
                }else{
                    $trxModal .= '<td><a href="javascript:;" onclick="getDetail('.$key->id.')" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">SO.'.$key->id.'</a></td>';
                }

                if (array_search("PSSLU",$page)){
                    $options .= '<a href="'.route('sales.edit',['id'=>$key->id]).'" class="btn btn-purple btn-trans waves-effect w-md waves-danger m-b-5">Edit</a>';
                }

                if (array_search("PSSLD",$page)){
                    $options .= '<a href="javascript:;" class="btn btn-pink btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteSales('.$key->id.')">Delete</a>';
                }

                if ($key->approve == 0){
                    $url_register		= base64_encode(route('salesApprove',['user_id'=>session('user_id'),'trx_id'=>$key->id,'role'=>session('role')]));
                    if (array_search("PSSLA",$page)){
                        $options .= '<a href="finspot:FingerspotVer;'.$url_register.'" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Sales</a>';
                    }

                }else{
                    $count_temp = TempSales::where('trx_id',$key->id)->count('trx_id');
                    $status_temp = TempSales::where('trx_id',$key->id)->where('status',1)->count('trx_id');

                    if($count_temp > 0 && $status_temp == 1){
                        $url_register		= base64_encode(route('salesApprove',['user_id'=>session('user_id'),'trx_id'=>$key->id,'role'=>session('role')]));
                        if (array_search("PSSLA",$page)){
                            $options .= '<a href="finspot:FingerspotVer;'.$url_register.'" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Sales yang sudah diupdate</a>';
                        }
                    }else{
                        $options .= '<a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5">Sales sudah di approve</a>';
                    }
                }

                if (array_search("PSSLN",$page)){
                    $options .= '<a href="javascript:;" class="btn btn-info btn-trans waves-effect w-md waves-danger m-b-5" onclick="previewInvoice('.$key->id.')"><i class="fa fa-file-pdf-o"></i> Preview Invoice</a>';
                }

                if (array_search("PSSLP",$page)){
                    $jenis = "print";
                    $options .= '<input type="hidden" id="route'.$key->id.'" value="'.route('invoicePrint',['jenis' =>$jenis,'trx_id' => $key->id]).'">
                    <a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="printPdf('.$key->id.')"><i class="fa fa-file-pdf-o"></i> Print Invoice</a>';
                }
            $detail->put('no', $i++);
            $detail->put('trx_id', $trxModal);
            $detail->put('trx_date', $key->trx_date);
            $detail->put('customer', $key->customer->apname);
            $detail->put('creator', $key->creator()->first()->name);
            $detail->put('total',$key->ttl_trx);
            $detail->put('option', $options);
            $data->push($detail);
        }

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data' => $data,
        );

        return $response;
    }
}
