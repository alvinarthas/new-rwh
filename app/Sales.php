<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\SalesDet;
use App\DeliveryOrder;
use App\DeliveryDetail;

class Sales extends Model
{
    protected $table ='tblproducttrx';
    protected $fillable = [
        'trx_date','creator','payment','ttl_harga','customer_id','jurnal_id','hpp_jurnal_id','ongkir','approve','approve_by'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public static function getOrder($start,$end,$param){
        $data = collect();
        if($param == "all"){
            $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id');
        }else{
            $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id')
            ->whereBetween('trx_date',[$start,$end]);
        }
        
        $ttl_count = $order->sum('x.qty');
        $order->orderBy('tblproducttrx.id')->select('tblproducttrx.*');
        
        $ttl_pemasukan = $order->sum('x.sub_ttl');
        $ttl_total = $order->sum('x.sub_ttl_pv');
        if($param == "all"){
            $ttl_trx = Sales::count('id');
        }else{
            $ttl_trx = Sales::whereBetween('trx_date',[$start,$end])->count('id');
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

    public static function getOrderPayment($start_trx,$end_trx,$start_pay,$end_pay,$customer,$param){
        if($param == "all"){
            $payment = SalesPayment::sum('payment_amount');
            $sales = Sales::where('approve',1);
            $data = collect();
            
            $ttl_trx = $sales->count('id');
    
            $ttl_harga = $sales->sum('ttl_harga');
            $ttl_ongkir = $sales->sum('ongkir');
        }elseif($param == null){
            $payment = SalesPayment::whereBetween('payment_date',[$start_pay,$end_pay])->sum('payment_amount');
            $sales = Sales::whereBetween('trx_date',[$start_trx,$end_trx])->where('approve',1);
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

    public static function checkDO($start,$end){
        $sales = Sales::whereBetween('trx_date',[$start,$end])->where('status',1)->get();
        $data = collect();
        foreach ($sales as $sale) {
            $collect = collect();
            $salesdet = SalesDet::where('trx_id',$sale->id)->get();
            $detcount = $salesdet->count();
            $count = 0;
            foreach($salesdet as $key){
                $count_do_product = DeliveryDetail::where('sales_id',$sale->id)->where('product_id',$key->prod_id)->sum('qty');
                if($key->qty == $count_do_product){
                    $count++;
                }
            }

            if($detcount == $count){
                $status = 1;
            }else{
                $status = 0;
            }
            $collect->put('sales_id',$sale->id);
            $collect->put('customer',$sale->customer->apname);
            $collect->put('ttl',$sale->ttl_harga+$sale->ongkir);
            $collect->put('status_do',$status);
            $data->push($collect);
        }
        return $data;
    }

    public static function checkSent($product,$trx){
        return DeliveryDetail::where('product_id',$product)->where('sales_id',$trx)->sum('qty');
    }

    public static function getBV($bulan,$tahun){
        return Sales::join('tblproducttrxdet','tblproducttrx.id','=','tblproducttrxdet.trx_id')->whereMonth('trx_date',$bulan)->whereYear('trx_date',$tahun)->sum('tblproducttrxdet.sub_ttl_pv');
    }
}
