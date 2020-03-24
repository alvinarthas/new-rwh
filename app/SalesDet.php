<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesDet extends Model
{
    protected $table ='tblproducttrxdet';

    protected $fillable = [
        'trx_id', 'prod_id','qty','unit','price','sub_ttl','creator','pv','sub_ttl_pv',
    ];
    
    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public function trx(){
        return $this->belongsTo('App\Sales');
    }

    public static function getOrder($start,$end,$customer,$product){
        $data = collect();
        $order = SalesDet::join('tblproducttrx as x','tblproducttrxdet.trx_id','=','x.id')
        ->whereBetween('x.trx_date',[$start,$end]);
        if($customer <> "all"){
            $order->where('x.customer_id',$customer);
        }

        if($product <> "all"){
            $order->where('x.prod_id',$product);
        }
        $ttl_count = $order->sum('tblproducttrxdet.qty');
        $order->orderBy('x.id')->select('tblproducttrxdet.*');

        
        
        $ttl_pemasukan = $order->sum('tblproducttrxdet.sub_ttl');
        $ttl_total = $order->sum('tblproducttrxdet.sub_ttl_pv');
        $ttl_trx = $order->distinct()->count('x.id');

        $data->put('ttl_count',$ttl_count);
        $data->put('ttl_pemasukan',$ttl_pemasukan);
        $data->put('ttl_total',$ttl_total);
        $data->put('ttl_trx',$ttl_trx);
        $data->put('start',$start);
        $data->put('end',$end);
        $data->put('data',$order->get());
        return $data;
    }

    public static function getProducts($trx){
        return SalesDet::join('tblproduct as p','tblproducttrxdet.prod_id','=','p.prod_id')->select('p.prod_id','p.name')->where('tblproducttrxdet.trx_id',$trx)->groupBy('tblproducttrxdet.prod_id')->get();
    }
}
