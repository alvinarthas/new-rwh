<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryDetail extends Model
{
    protected $table ='delivery_detail';
    protected $fillable = [
        'do_id','qty','product_id','sales_id', 'gudang_id',
    ];

    public function sales(){
        return $this->belongsTo('App\Sales');
    }

    public function gudang(){
        return $this->belongsTo('App\Gudang','gudang_id','id');
    }

    public function product(){
        return $this->belongsTo('App\Product','product_id','prod_id');
    }
}
