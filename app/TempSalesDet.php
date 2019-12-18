<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempSalesDet extends Model
{
    protected $table ='temp_tblproducttrxdet';

    protected $fillable = [
        'temp_id','prod_id','qty','unit','price','sub_ttl','creator','pv','sub_ttl_pv',
    ];
    
    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }
}
