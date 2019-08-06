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
}
