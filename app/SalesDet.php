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
}
