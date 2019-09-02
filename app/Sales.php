<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table ='tblproducttrx';
    protected $fillable = [
        'trx_date','creator','payment','ttl_harga','customer_id','jurnal_id','hpp_jurnal_id','ongkir','approve'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public static function getOrder($start,$end,$customer,$product){
        $data = collect();
        $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id')
        ->whereBetween('trx_date',[$start,$end]);
        if($customer <> "all"){
            $order->where('tblproducttrx.customer_id',$customer);
        }

        if($product <> "all"){
            $order->where('x.prod_id',$product);
        }
        $ttl_count = $order->sum('x.qty');
        $order->orderBy('tblproducttrx.id')->select('tblproducttrx.*');

        
        
        $ttl_pemasukan = $order->sum('x.sub_ttl');
        $ttl_total = $order->sum('x.sub_ttl_pv');
        $ttl_trx = $order->distinct()->count('tblproducttrx.id');

        $data->put('ttl_count',$ttl_count);
        $data->put('ttl_pemasukan',$ttl_pemasukan);
        $data->put('ttl_total',$ttl_total);
        $data->put('ttl_trx',$ttl_trx);
        $data->put('start',$start);
        $data->put('end',$end);
        $data->put('data',$order->get());
        return $data;
    }

    public static function getOrderPayment($start_trx,$end_trx,$start_pay,$end_pay,$customer){
        $payment = SalesPayment::whereBetween('payment_date',[$start_pay,$end_pay])->sum('payment_amount');
        $sales = Sales::whereBetween('trx_date',[$start_trx,$end_trx])->where('approve',1);
        $data = collect();

        if($customer <> "all"){
            $sales->where('customer_id',$customer);
        }
        
        $ttl_trx = $sales->count('id');

        $ttl_harga = $sales->sum('ttl_harga');
        $ttl_ongkir = $sales->sum('ongkir');
        $ttl_sales = $ttl_harga+$ttl_ongkir;

        $data->put('ttl_trx',$ttl_trx);
        $data->put('ttl_sales',$ttl_sales);
        $data->put('ttl_payment',$payment);
        $data->put('data',$sales->orderBy('id','desc')->get());

        return $data;
    }
}
