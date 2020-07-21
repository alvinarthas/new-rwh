<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\SalesDet;
use App\Sales;
use App\Purchase;
use App\Jurnal;
use App\DeliveryDetail;

class DeliveryOrder extends Model
{
    protected $table ='delivery_order';
    protected $fillable = [
        'sales_id','date','petugas','jurnal_id'
    ];

    public function sales(){
        return $this->belongsTo('App\Sales');
    }

    public function petugas(){
        return $this->belongsTo('App\Employee','petugas','id');
    }

    public function product(){
        return $this->belongsTo('App\Product','product_id','prod_id');
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

        $sales = Sales::join('tblproducttrxdet','tblproducttrx.id','tblproducttrxdet.trx_id')->join('tblcustomer', 'tblproducttrx.customer_id', 'tblcustomer.id')->join('tblproduct', 'tblproducttrxdet.prod_id', 'tblproduct.prod_id')->select('tblproducttrx.id', 'tblproducttrx.trx_date', 'tblproducttrxdet.prod_id', 'tblproducttrx.jurnal_id', 'tblproducttrxdet.trx_id', 'tblproduct.name AS prodname', 'tblcustomer.apname AS customer_name', DB::raw('SUM(tblproducttrxdet.qty) as qtyso'))->where('tblproducttrx.approve', 1);

        if($start <> NULL && $end<> NULL){
            $sales->whereBetween('tblproducttrx.trx_date',[$start,$end]);
        }

        if($customer <> "#"){
            $sales->where('tblproducttrx.customer_id', $customer);
        }

        if($prod_id <> "#"){
            $sales->where('tblproducttrxdet.prod_id', $prod_id);
        }
        $sales->groupBy('tblproducttrxdet.trx_id','tblproducttrxdet.prod_id');

        $totalRecords = 0;
        foreach($sales->get() as $count){
            $totalRecords++;
        }

        if($searchValue != ''){
            $raw = DeliveryOrder::select('sales_id')->where('jurnal_id', 'LIKE', '%'.$searchValue)->get();
            // echo $raw;
            $sales->where('tblproducttrx.jurnal_id', 'LIKE', '%'.$searchValue.'%')->orWhere('tblcustomer.apname', 'LIKE', '%'.$searchValue.'%')->orWhere('tblproducttrxdet.prod_id', 'LIKE', '%'.$searchValue.'%')->orWhere('tblproduct.name', 'LIKE', '%'.$searchValue.'%')->orWhereIn('tblproducttrx.id', $raw);
        }

        $totalRecordwithFilter = 0;
        foreach($sales->get() as $count){
            $totalRecordwithFilter++;
        }

        if($columnName == "no"){
            $sales->orderBy('tblproducttrxdet.trx_id', $columnSortOrder);
        }elseif($columnName == "so_id"){
            $sales->orderBy('tblproducttrx.jurnal_id', $columnSortOrder);
        }elseif($columnName == "customer"){
            $sales->orderBy('customer_name', $columnSortOrder);
        }elseif($columnName == "prod_name"){
            $sales->orderBy('prodname', $columnSortOrder);
        }else{
            $sales->orderBy($columnName, $columnSortOrder);
        }

        $sales = $sales->offset($row)->limit($rowperpage)->get();

        $data = collect();
        $i = 1;

        foreach($sales as $key){
            $detail = collect();

            $do_id = "";

            $do = DeliveryDetail::join('delivery_order', 'delivery_detail.do_id', 'delivery_order.id')->where('delivery_order.sales_id',$key->trx_id)->where('delivery_detail.product_id',$key->prod_id)->select('delivery_order.jurnal_id')->get();
            foreach($do as $d){
                $do_id .= $d->jurnal_id." ";
            }

            $do_qty = DeliveryDetail::where('sales_id',$key->id)->where('product_id',$key->prod_id)->sum('qty');

            if($do_qty == 0){
                $qtydo = '<a href="javascrip:;" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$do_qty.'</a>';
            }elseif(($do_qty-$key->qtyso) == 0){
                $qtydo = '<a href="javascrip:;" class="btn btn-success btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$do_qty.'</a>';
            }elseif(($do_qty-$key->qtyso) < 0){
                $qtydo = '<a href="javascrip:;" class="btn btn-warning btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$do_qty.'</a>';
            }elseif(($do_qty-$key->qtyso) > 0){
                $qtydo = '<a href="javascrip:;" class="btn btn-custom btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$do_qty.'</a>';
            }else{
                $qtydo = '<a href="javascrip:;" class="btn btn-info btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$do_qty.'</a>';
            }

            $button = '<a href="do/show/'.$key->id.'" class="btn btn-primary btn-rounded waves-effect w-md waves-danger m-b-5">Atur</a>';

            $detail->put('no', $i++);
            $detail->put('so_id', $key->jurnal_id);
            $detail->put('do_id', $do_id);
            $detail->put('customer', $key->customer_name);
            $detail->put('prod_id', $key->prod_id);
            $detail->put('prod_name', $key->prodname);
            $detail->put('qtydo',$qtydo);
            $detail->put('option', $button);
            $data->push($detail);
        }

        // echo "<pre>";
        // print_r($data);
        // die();

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data' => $data,
        );

        return $response;
    }

    public static function autoDO($sales_id,$date,$user_id = null){
        if($user_id != null){
            $user = $user_id;
        }else{
            $user = session('user_id');
        }

        $id_jurnal = Jurnal::getJurnalID('DO');
        $sales = Sales::where('id',$sales_id)->first();

        $do = new DeliveryOrder(array(
            'sales_id' => $sales_id,
            'date' => $date,
            'petugas' => $user,
            'jurnal_id' => $id_jurnal,
        ));

        $details = SalesDet::where('trx_id',$sales_id)->get();
        $price = 0;
        foreach ($details as $key) {
            $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

            $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->prod_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

            if($sumprice <> 0 && $sumqty <> 0){
                $avcharga = $sumprice/$sumqty;
            }else{
                $avcharga = 0;
            }

            $price += $avcharga * $key->qty;
        }

        $desc = "Delivery Order ".$sales->jurnal_id;

        // JURNAL
            //insert debet Persediaan Barang milik Customer
            Jurnal::addJurnal($id_jurnal,$price,$date,$desc,'2.1.3','Debet',$user);
            //insert credit Persediaan Barang digudang
            Jurnal::addJurnal($id_jurnal,$price,$date,$desc,'1.1.4.1.2','Credit',$user);
        $do->save();



        $desc = "Delivery Order ID=".$do->id." ".$sales->jurnal_id;

        foreach(Jurnal::where('id_jurnal',$id_jurnal)->get() as $key){
            $key->description = $desc;
            $key->save();
        }

        foreach ($details as $key) {
            $dodet = new DeliveryDetail(array(
                'do_id' => $do->id,
                'sales_id' => $sales_id,
                'product_id' => $key->prod_id,
                'qty' => $key->qty,
            ));

            $dodet->save();
        }
        Log::setLog('PSDOC','Create Delivery Order '.$sales->jurnal_id.' Jurnal ID: '.$id_jurnal,$user);
    }

    public static function deleteDO($sales_id){
        $sales_jurnal = Sales::where('id',$sales_id)->select('jurnal_id')->first()->jurnal_id;
        $do = DeliveryOrder::where('sales_id',$sales_id)->select('jurnal_id','sales_id','id')->first();

        if($do <> NULL){
            $id_jurnal = $do->jurnal_id;
            try {
                $jurnal = Jurnal::where('id_jurnal',$do->jurnal_id)->delete();
                Log::setLog('PSDOD','Delete Delivery Order '.$sales_jurnal.' Jurnal ID: '.$id_jurnal);
                return "true";
            } catch (\Exception $e) {
                return response()->json($e);
            }
        }
    }

    public static function recycleDO($do_id,$date,$det_id = null){
        if ($det_id){
            DeliveryDetail::where('id',$det_id)->delete();
        }

        $do = DeliveryOrder::where('id',$do_id)->first();

        $price = 0;
        $count = 0;
        foreach(DeliveryDetail::where('do_id',$do_id)->get() as $key) {
            $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->product_id)->where('tblpotrx.tgl','<=',$date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

            $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->product_id)->where('tblpotrx.tgl','<=',$date)->sum('tblpotrxdet.qty');

            if($sumprice <> 0 && $sumqty <> 0){
                $avcharga = $sumprice/$sumqty;
            }else{
                $avcharga = 0;
            }

            $price += $avcharga * $key->qty;
            $count++;
        }

        if ($count > 0){
            $debet = Jurnal::where('id_jurnal',$do->jurnal_id)->where('AccPos', 'Debet')->first();
            $debet->amount = $price;
            $debet->update();

            $credit = Jurnal::where('id_jurnal',$do->jurnal_id)->where('AccPos', 'Credit')->first();
            $credit->amount = $price;
            $credit->update();
        }else{
            $jurnal = Jurnal::where('id_jurnal',$do->jurnal_id)->delete();
        }
    }
}
