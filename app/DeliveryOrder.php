<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public static function checkDO($start,$end){
        $data = DeliveryOrder::join('delivery_detail','delivery_order.id','=','delivery_detail.do_id');
        if($start <> NULL && $end <> NULL){
            $data = $data->whereBetween('delivery_order.date',[$start,$end])->select('delivery_order.id','delivery_order.sales_id','date','jurnal_id','petugas','qty','product_id')->get();
        }else{
            $data = $data->select('delivery_order.id','delivery_order.sales_id','date','jurnal_id','petugas','qty','product_id')->get();
        }

        return $data;
    }
}
