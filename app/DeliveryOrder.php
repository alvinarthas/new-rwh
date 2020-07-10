<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\SalesDet;
use App\Sales;
use App\Purchase;

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

    public static function checkDO($start, $end){
        // $sales = Sales::join('tblproducttrxdet','tblproducttrx.id','=','tblproducttrxdet.trx_id');
        if($start <> NULL && $end<> NULL){
            $sales = Sales::whereBetween('trx_date',[$start,$end])->where('approve',1)->get();
        }else{
            $sales = Sales::where('approve',1)->get();
        }
        $data = collect();
        foreach($sales as $sal){
            $salesdet = SalesDet::where('trx_id', $sal->id)->groupBy('prod_id')->get();
            foreach($salesdet as $key){
                $key->qty = SalesDet::where('trx_id', $sal->id)->where('prod_id', $key->prod_id)->sum('qty');
                $detail = collect($key);

                $do_id = "";

                $do = DeliveryDetail::join('delivery_order', 'delivery_detail.do_id', 'delivery_order.id')->where('delivery_order.sales_id',$sal->id)->where('delivery_detail.product_id',$key->prod_id)->select('delivery_order.jurnal_id')->get();
                foreach($do as $d){
                    $do_id .= $d->jurnal_id." ";
                }

                $do_qty = DeliveryDetail::where('sales_id',$sal->id)->where('product_id',$key->prod_id)->sum('qty');
                $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

                $detail->put('so_id', $sal->jurnal_id);
                $detail->put('do_qty',$do_qty);
                $detail->put('do_id', $do_id);
                $detail->put('customer', $sal->customer()->first()->apname);
                $detail->put('product_name',$prodname);
                $data->push($detail);
            }
        }
        return $data;
    }

    // public static function checkDO($start,$end){
    //     $sales = SalesDet::join('tblproducttrx','tblproducttrxdet.trx_id','=','tblproducttrx.id');
    //     if($start <> NULL && $end <> NULL){
    //         $sales = $sales->whereBetween('tblproducttrx.trx_date',[$start,$end])->select('tblproducttrxdet.trx_id','tblproducttrx.customer_id','tblproducttrx.online_id','tblproducttrxdet.prod_id','tblproducttrxdet.qty')->get();
    //     }else{
    //         $sales = $sales->select('tblproducttrxdet.trx_id','tblproducttrx.customer_id','tblproducttrx.online_id','tblproducttrxdet.prod_id','tblproducttrxdet.qty')->get();
    //     }

    //     foreach ($sales as $key) {
    //         $do = DeliveryDetail::where('sales_id',$key->trx_id)->where('product_id',$key->prod_id)->sum('qty');
    //         $key->customer_name = $key->trx->customer->apname;
    //         $key->product_name = $key->product->name;
    //         $key->do_qty = $do;
    //     }
    //     return $sales;
    // }

    public static function autoDO($sales_id,$date){
        $id_jurnal = Jurnal::getJurnalID('DO');
        $sales = Sales::where('id',$sales_id)->first();

        $do = new DeliveryOrder(array(
            'sales_id' => $sales_id,
            'date' => $date,
            'petugas' => session('user_id'),
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
            Jurnal::addJurnal($id_jurnal,$price,$date,$desc,'2.1.3','Debet');
            //insert credit Persediaan Barang digudang
            Jurnal::addJurnal($id_jurnal,$price,$date,$desc,'1.1.4.1.2','Credit');

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
        Log::setLog('PSDOC','Create Delivery Order '.$sales->jurnal_id.' Jurnal ID: '.$id_jurnal);
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
}
