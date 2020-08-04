<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransitProductDetail extends Model
{
    protected $table ='tbl_transit_product_detail';
    protected $fillable = [
        'transit_id', 'product_id','qty','gudang_awal','gudang_akhir'
    ];

    public function product(){
        return $this->belongsTo('App\Product','product_id','prod_id');
    }

    public function gudang_awal(){
        return $this->belongsTo('App\Gudang','gudang_awal','id');
    }

    public function gudang_akhir(){
        return $this->belongsTo('App\Gudang','gudang_akhir','id');
    }

}
